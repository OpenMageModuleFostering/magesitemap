<?php 
$installer = $this;

$installer->startSetup();
$conn = $installer->_conn;
$conn->addColumn($this->getTable('magesitemap'), 'static_value', 'varchar(255) NOT NULL');
$installer->endSetup(); 