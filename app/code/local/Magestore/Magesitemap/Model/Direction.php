<?php

class Magestore_Magesitemap_Model_Direction extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function toOptionArray()
    {
		$option = array(
			1	=>'Left to right',
			2	=>'Right to left',
		);
        return $option;
    }
}