<?php
class Magestore_Magesitemap_Block_Magesitemap extends Mage_Core_Block_Template
{
	protected $_categoryId;
	
	public function setCategory($categoryId){
		$this->_categoryId = $categoryId;
	}
	
	public function getCategory(){
		$categoryModel = Mage::getModel("catalog/category")
						->load($this->_categoryId);
		if($categoryModel->getId()){
			return $categoryModel;
		}else{
			return NULL;
		}

	}
	
	public function getCurrentStore(){
		$storeCode = $this->getRequest()->getParam('store','');
		
		if($storeCode !=''){
			//die($storeCode);
			$store = Mage::app()->getStore()->load($storeCode);
			return $store;
		}
		return Mage::app()->getStore();
		
	}
	
	public function getProductCollection(){
	
		$collection = Mage::getModel("catalog/product")->getCollection()
					->addAttributeToSelect("*")		
					->setOrder('updated_at','DESC')
					->addCategoryFilter($this->getCategory())
					->setStore($this->getCurrentStore())
					;		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		return $collection;
	}
	
	public function addNodeXML($tag_name,$tag_value,$cdata = false){
		$value = $cdata ? '<![CDATA['.trim($tag_value).']]>' : trim($tag_value);
		$name = trim($tag_name);
		return "<{$name}>{$value}</{$name}>";
	}
	
    public function setHeader(){
        Header("Cache-Control: no-cache, must-revalidate");
        Header("Pragma: no-cache");
        Header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        Header("Content-type: text/xml; charset=utf-8");
    }

    public function encode($htmlInput){
		
        if( defined('_ENCODING') && strtolower(_ENCODING) != 'utf-8' ){
            $htmlInput = iconv( _ENCODING, 'utf-8', $htmlInput );
        }
        print $htmlInput;
    }
	
	public function getXmlTags(){
		$collection = Mage::getModel("magesitemap/magesitemap")
						->getCollection()
						->addFieldToFilter('status',1);
		return $collection;
	}
	public function getTaxPercent($product){
	
        $percent = $product->getTaxPercent();
        $taxClassId = $product->getTaxClassId();
         if (is_null($percent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, null);
                $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
            }
        }
        $product_tax = $percent;	
	}
	
	public function exportXml(){
	
		$storeName 	= $this->getCurrentStore()->getName();
		$xml_data[] = '<?xml version="1.0" encoding="utf-8" ?>';
		$xml_data[]	= '<store name ="'.$storeName.'" date="'.date('d/m/Y').'" time ="'.date('H:i:s').'" >';
		$xml_data[] = '<products>';
		$productCollection = $this->getProductCollection();

		foreach($productCollection as $_product){
			$xml_data[] = '<product>';
			$xmltags = $this->getXmlTags();
			if($xmltags->getSize()>0){
				foreach($xmltags as $_tag){
					$_tagName 	= $this->preProcessTagData($_tag->getAliasName());
					$_tagValue 	= $this->preProcessTagData($_tag->getAttributeCode());
					if($_tagValue == 'image'){
						$_tagValue = Mage::helper('catalog/image')->init($_product, 'image')->resize(400);
						$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
					}elseif($_tagValue =='price'){
						$price =  $_product->getFinalPrice();
						$_tagValue = $this->getCurrentStore()->convertPrice($price, false, false);
						$currency_code = $this->getCurrentStore()->getCurrentCurrency()->getCurrencyCode();
						$xml_data[] = $this->addNodeXML('currency_code',$currency_code,true);
						$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
					}elseif($_tagValue =='product_link'){
						$_tagValue = $_product->getProductUrl();
						$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
						
					}elseif($_tagValue =='shipping_cost'){
						$default_shipping = Mage::getStoreConfig('carriers/flatrate/price');
						$_tagValue 	= $default_shipping;
						$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
										
					}elseif($_tagValue == 'tax_rate'){
						$_tagValue 	= $this->getTaxPercent($_product);
						$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
					}elseif($_tagValue == 'static_value'){
						$_tagValue 	= $_tag->getStaticValue();
						$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
					}else{
						$attrModel = $_product->getResource()->getAttribute($_tagValue);
						if(is_object($attrModel)){
							if($attrModel->getData("frontend_input") =="select" || $attrModel->getData("frontend_input") == "multiselect"){
								$_tagValue 	= $_product->getAttributeText($_tagValue);
								$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
								
							}else{
								$_tagValue 	= $_product->getData($_tagValue);
								$xml_data[] = $this->addNodeXML($_tagName,$_tagValue,true);
							}
						}
						
					}

					
				}
			}
			$xml_data[] = '</product>';
			
		}
		$xml_data[] = '</products>';
		$xml_data[] = '</store>';
		$xml_output = join("\n",$xml_data);
		$this->setHeader();
		$this->encode($xml_output);
		
	}
	
	public function preProcessTagData($data){
		$outputData = str_replace(' ','',strtolower($data));	
		return $outputData;
	}
	
	public function getPrice($product){
		
		$finalPrice =  $product->getFinalPrice();
		$price = $this->getCurrentStore()->convertPrice($finalPrice, false, false);  
		return $price;
	}
	
	public function getCurrencyCode(){
	
		$currencyCode = $this->getCurrentStore()->getData("current_currency")->getCurrencyCode();
		return $currencyCode;
	}
}