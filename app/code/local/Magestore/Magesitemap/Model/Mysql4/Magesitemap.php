<?php

class Magestore_Magesitemap_Model_Mysql4_Magesitemap extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the magesitemap_id refers to the key field in your database table.
        $this->_init('magesitemap/magesitemap', 'magesitemap_id');
    }
}