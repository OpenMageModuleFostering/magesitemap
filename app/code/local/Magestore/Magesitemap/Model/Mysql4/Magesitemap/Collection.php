<?php

class Magestore_Magesitemap_Model_Mysql4_Magesitemap_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magesitemap/magesitemap');
    }
}