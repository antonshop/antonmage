<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('fbreferral')};
CREATE TABLE {$this->getTable('fbreferral')} (
  `fbreferral_id` int(11) unsigned NOT NULL auto_increment,
  `fbuser` varchar(255) NOT NULL default '',
  `content` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`fbreferral_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 