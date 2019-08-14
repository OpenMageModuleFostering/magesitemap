<?php

class Magestore_Magesitemap_Block_Adminhtml_Magesitemap_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('magesitemap_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('magesitemap')->__('Tag Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('magesitemap')->__('Tag Information'),
          'title'     => Mage::helper('magesitemap')->__('Tag Information'),
          'content'   => $this->getLayout()->createBlock('magesitemap/adminhtml_magesitemap_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}