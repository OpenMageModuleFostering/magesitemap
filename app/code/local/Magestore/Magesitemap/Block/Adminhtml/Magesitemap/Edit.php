<?php

class Magestore_Magesitemap_Block_Adminhtml_Magesitemap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'magesitemap';
        $this->_controller = 'adminhtml_magesitemap';
        
        $this->_updateButton('save', 'label', Mage::helper('magesitemap')->__('Save Tag'));
        $this->_updateButton('delete', 'label', Mage::helper('magesitemap')->__('Delete Tag'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		$dataObject = Mage::registry('magesitemap_data');
		$is_static = '0';
		if(is_object($dataObject)){
			if($dataObject->getData('static_value')){
				$is_static ='1';
			}
		}
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('magesitemap_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'magesitemap_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'magesitemap_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			function choooseType(){
				if($('type_attribute').value !='attribute_product'){
					$('attribute_code').hide();
					$('attribute_code').removeClassName('required-entry');
					$('static_value').hide();
					$('static_value').removeClassName('required-entry');					
					if($('type_attribute').value == 'static_value'){
						$('static_value').show();
						$('static_value').addClassName('required-entry');
					}
				}else{
					$('attribute_code').show();
					$('attribute_code').addClassName('required-entry');
					$('static_value').hide();
					$('static_value').removeClassName('required-entry');					
				}
			}
			function checkIsStatic(is_static){
				if(is_static ==1){
					$('type_attribute').value = 'static_value';
					$('attribute_code').hide();
					$('static_value').show();
				}
			}
			window.onload = checkIsStatic('".$is_static."');
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('magesitemap_data') && Mage::registry('magesitemap_data')->getId() ) {
            return Mage::helper('magesitemap')->__("Edit Tag '%s'", $this->htmlEscape(Mage::registry('magesitemap_data')->getData('alias_name')));
        } else {
            return Mage::helper('magesitemap')->__('Add Tag');
        }
    }
}