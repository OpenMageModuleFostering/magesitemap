<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('magesitemap')};
CREATE TABLE {$this->getTable('magesitemap')} (
  `magesitemap_id` int(11) unsigned NOT NULL auto_increment,
  `alias_name` varchar(255) NOT NULL default '',
  `attribute_code` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',		
  PRIMARY KEY (`magesitemap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 