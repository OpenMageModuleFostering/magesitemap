<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('createsitemap')};
CREATE TABLE {$this->getTable('createsitemap')} (
  `createsitemap_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `tag_ids` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',		
  PRIMARY KEY (`createsitemap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 