<?php
class Magestore_Magesitemap_Block_Tree extends Mage_Core_Block_Template
{
    public function buildTree(){
		$urlJsFile = $this->getJsUrl("magestore/tree.js");
		$urlJsCss =  $this->getJsUrl("magestore/tree.css");
		$direction 	= Mage::getStoreConfig('xmlsitemap/general/direction');
		$dir 		= '';
		if($direction ==2){
			$dir 	= 'dir ="rtl"';
		}else{
			$dir 	= 'dir ="ltr"';
		}		
        $_html = "<html {$dir} >";
        $_html .= '<head>'.
					'<link rel="stylesheet" href="'.$this->getJsUrl().'magestore/tree.css" type="text/css">'.
					'<script language="javascript" src="'.$this->getJsUrl().'magestore/tree.js"></script>';
        $_html .= 	'<title>Sitemap</title>';
        $_html .= '</head>';
        $_html .= '<body><div class="dtree">';
		$_html .= '<div class="block_store">';
		$_html .= $this->getStoreHtml();
		$_html .= '</div>';
        $_html .= '<script type="text/javascript"> <!--'."\n";
		$_html .= " var baseUrl = '".$this->getJsUrl('magestore/')."';";
        $_html .= " var d = new dTree('d');d.icon.setUrl('".$this->getJsUrl('magestore/')."');";
		$storeName = $this->getCurrentStore()->getName();
		$rootCategory = Mage::getModel("catalog/category")->load($this->getCurrentStore()->getRootCategoryId());
        $_html .= $this->addNode($rootCategory,true);
		$listCategory = Mage::getModel("catalog/category")->getCollection();
        foreach($listCategory as $item ){
			if($item->getId() == $rootCategory){
				continue;
			}
            $_category = Mage::getModel('catalog/category')->load($item->getId()); 
            if (!$_category->getId()) {
                continue;
            }

            if (!$_category->getIsActive()) {
                continue;
            }
            $_html .= $this->addNode($_category);
        } 
        $_html .= 'document.write(d); //--> </script>';
        $_html .= '</div></body></html>';
		print($_html);
    }
	public function getCurrentStore(){
		$storeCode = $this->getRequest()->getParam('store','');
		if($storeCode !=''){
			$store = Mage::app()->getStore()->load($storeCode);
			return $store;
		}
		return Mage::app()->getStore();
		
	}
    public function addNode($_category,$isRoot = false){
		if($isRoot){
			return "d.add({$_category->getId()},-1,'".addslashes($this->getCurrentStore()->getName())."','".$this->getUrl("*/*/index")."catID/{$_category->getId()}');\n";		
		}else{
			return "d.add({$_category->getId()},{$_category->getParentId()},'".addslashes($_category->getName())."','".$this->getUrl("*/*/index")."catID/{$_category->getId()}/store/{$this->getCurrentStore()->getId()}');\n";
		}
    }
	public function getStoreHtml(){
		$stores = Mage::app()->getStore()->getCollection();
		$currentStoreId = $this->getCurrentStore()->getId();
		$selected = '';
		$html = '<form id="store_form" method="get" action =""><select name="store" id="store_view" onchange="document.getElementById(\'store_form\').submit()">';
		foreach($stores as $_store){
			if($_store->getId() == $currentStoreId){
				$html .='<option value="'.$_store->getId().'" selected ="selected" >'.$_store->getName().'</option>';
			}else{
				$html .='<option value="'.$_store->getId().'" >'.$_store->getName().'</option>';			
			}
			 
		}
		$html .='</select></form>';
		return $html;
		
	}
}