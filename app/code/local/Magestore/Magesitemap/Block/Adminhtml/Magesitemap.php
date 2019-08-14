<?php
class Magestore_Magesitemap_Block_Adminhtml_Magesitemap extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_magesitemap';
    $this->_blockGroup = 'magesitemap';
    $this->_headerText = Mage::helper('magesitemap')->__('XmlTag Manager');
    $this->_addButtonLabel = Mage::helper('magesitemap')->__('Add XmlTag');
    parent::__construct();
  }
}