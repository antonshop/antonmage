<?php
	class Nofrills_Booklayout_ReferenceController extends Mage_Core_Controller_Front_Action
	{
	
		/**
		* Use to set the base page structure
		*/	
		protected function _initLayout()
		{
			$path_page = Mage::getModuleDir('', 'Nofrills_Booklayout') . DS . 
			'page-layouts' . DS . 'page.xml';					
			$xml = file_get_contents($path_page); 	
			$layout = Mage::getSingleton('core/layout')
			->getUpdate()
			->addUpdate($xml);			
		}

		/**
		* Use to send output 
		*/		
		protected function _sendOutput()
		{
			$layout = Mage::getSingleton('core/layout');
			
			$layout->generateXml()
			->generateBlocks();
			
			echo $layout->setDirectOutput(false)->getOutput();
		}
		
		
		public function indexAction()
		{
			$this->_initLayout();
			Mage::getSingleton('core/layout')
			->getUpdate()
			->addUpdate('<reference name="content">
				<block type="core/text" name="our_message">
					<action method="setText"><text>Here we go!</text></action>
				</block>
			</reference>');
			$this->_sendOutput();
		}
		
		
		protected function _loadUpdateFile($file)
		{
			$path_update = Mage::getModuleDir('', 'Nofrills_Booklayout') . DS . 
			'content-updates' . DS . $file;			
			
			$layout = Mage::getSingleton('core/layout')
			->getUpdate()
			->addUpdate(file_get_contents($path_update));					
		}
		
		protected function _loadUpdateFileFromRequest()
		{		
			$path_update = Mage::getModuleDir('', 'Nofrills_Booklayout') . DS . 
			'content-updates' . DS . $this->getFullActionName() . '.xml';		
			
			$layout = Mage::getSingleton('core/layout')
			->getUpdate()
			->addUpdate(file_get_contents($path_update));				
		}
		
		#URL: http://magento.example.com/nofrills_booklayout/reference/fox
		public function foxAction()
		{
			$this->_initLayout();
			$this->_loadUpdateFileFromRequest();
			$this->_sendOutput();
		}

		#URL: http://magento.example.com/nofrills_booklayout/reference/fox
		public function dogAction()
		{
			$this->_initLayout();		
			$this->_loadUpdateFile('dog.xml');
			$this->_sendOutput();
		}
		
		#URL: http://magento.example.com/nofrills_booklayout/reference/fox
		public function ceaserAction()
		{	
			$this->_initLayout();
			$this->_loadUpdateFile('ceaser.xml');
			$this->_sendOutput();
		}

		#URL: http://magento.example.com/nofrills_booklayout/reference/handle
		public function handleAction()
		{
			$this->loadLayout();
			$handles = Mage::getSingleton('core/layout')->getUpdate()->getHandles();
			var_dump($handles);
			exit;
		}
		
		#URL: http://magento.example.com/nofrills_booklayout/reference/layoutfiles
		public function layoutfilesAction()
		{
			$updatesRoot = Mage::app()->getConfig()->getNode('frontend/layout/updates');
			$updateFiles = array();
			foreach ($updatesRoot->children() as $updateNode) {
				if ($updateNode->file) {
					$module = $updateNode->getAttribute('module');
					if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module)) {
						continue;
					}
					$updateFiles[] = (string)$updateNode->file;
				}
			}
			// custom local layout updates file - load always last
			$updateFiles[] = 'local.xml';
			
			var_dump($updateFiles);
		}
		
	}