<?php
	class Nofrills_Booklayout_PackageController extends Mage_Core_Controller_Front_Action
	{		
		public function loadLayout($handles=null, $generateBlocks=true, $generateXml=true)		
		{
			$original_results = parent::loadLayout($handles,$generateBlocks,$generateXml);
			$handles = Mage::getSingleton('core/layout')->getUpdate()->getHandles();
			echo "<strong>Handles Generated For This Request: <br/>\n",implode("<br/>\n",$handles),"</strong>";
			
			return $original_results;
		}
		
		public function indexAction()
		{
			$this->loadLayout();
			$this->renderLayout();			
		}

		public function secondAction()
		{
			$this->loadLayout();
			$this->renderLayout();			
		}		
	}