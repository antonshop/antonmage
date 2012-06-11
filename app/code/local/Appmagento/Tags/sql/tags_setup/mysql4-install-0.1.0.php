<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('seotags')};
CREATE TABLE {$this->getTable('seotags')} (
  `tags_id` int(11) unsigned NOT NULL auto_increment,
  `tags_name` varchar(255) NOT NULL default '',
  `products_id` varchar(255) NOT NULL default '',
  `content` varchar(255) NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`tags_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 