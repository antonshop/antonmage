<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('sharesuit')};
CREATE TABLE {$this->getTable('sharesuit')} (
  `sharesuit_id` int(11) unsigned NOT NULL auto_increment,
  `fbuser` varchar(255) NOT NULL default '',
  `content` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`sharesuit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE {$this->getTable('sharesuit_customer')} (
  `customer_id` int(11) unsigned NOT NULL auto_increment,
  `user` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `type` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 