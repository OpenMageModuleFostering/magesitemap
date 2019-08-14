<?php

class Magestore_Magesitemap_Model_Magesitemap extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magesitemap/magesitemap');
    }
}