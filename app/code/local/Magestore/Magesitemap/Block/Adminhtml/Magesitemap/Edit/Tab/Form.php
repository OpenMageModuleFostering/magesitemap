<?php

class Magestore_Magesitemap_Block_Adminhtml_Magesitemap_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('magesitemap_form', array('legend'=>Mage::helper('magesitemap')->__('Item information')));
     
      $fieldset->addField('alias_name', 'text', array(
          'label'     => Mage::helper('magesitemap')->__('Tag Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'alias_name',
      ));
	  
      $fieldset->addField('type_attribute', 'select', array(
          'label'     => Mage::helper('magesitemap')->__('Select Type'),
          'name'      => 'type_attribute',
		  'onchange'  => 'choooseType();',	
          'values'    => array(
              array(
                  'value'     => 'attribute_product',
                  'label'     => Mage::helper('magesitemap')->__('Product Attribute'),
              ),
              array(
                  'value'     => 'product_link',
                  'label'     => Mage::helper('magesitemap')->__('Product Link'),
              ),			  
              array(
                  'value'     => 'shipping_cost',
                  'label'     => Mage::helper('magesitemap')->__('Shipping cost'),
              ),
              array(
                  'value'     => 'tax_rate',
                  'label'     => Mage::helper('magesitemap')->__('Tax'),
              ),
			  array(
				'value'		  =>'static_value',
				'label'		  =>Mage::helper('magesitemap')->__("Static Tag"),
			  ),
          ),
      ));
     
	  
      $fieldset->addField('attribute_code', 'text', array(
          'label'     => Mage::helper('magesitemap')->__('Attribute Code'),
		 // 'style'	  =>'display:none;',
          'required'  => true,
          'name'      => 'attribute_code',
      ));	
      $fieldset->addField('static_value', 'text', array(
          'label'     => Mage::helper('magesitemap')->__('Static Value'),
          'required'  => false,
		  'style'	  =>'display:none;',
          'name'      => 'static_value',
      ));		  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('magesitemap')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('magesitemap')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('magesitemap')->__('Disabled'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getMagesitemapData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMagesitemapData());
          Mage::getSingleton('adminhtml/session')->setMagesitemapData(null);
      } elseif ( Mage::registry('magesitemap_data') ) {
          $form->setValues(Mage::registry('magesitemap_data')->getData());
      }
      return parent::_prepareForm();
  }
}