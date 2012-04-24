<?php

class Magentix_LayoutAnalyzer_Model_Observer {

	public function addBlock($observer) {
		if(!Mage::getStoreConfigFlag('dev/layoutanalyzer/active')) return $this;

		$layout = $observer->getLayout();

		$bodyEnd = $layout->getXpath("//block[@name='before_body_end']");

		if (!$bodyEnd) return $this;

		$block = $bodyEnd[0]->addChild('block');
		$block->addAttribute('type', 'layoutanalyzer/analyzer');
		$block->addAttribute('name', 'layout_analyzer');
		$block->addAttribute('as', 'layout_analyzer');
	}

	public function setAnalyzer($observer) {
		if(!Mage::getStoreConfigFlag('dev/layoutanalyzer/active')) return $this;

		$layout = $observer->getLayout();
		$update = $layout->getUpdate();

		$xml = $update->asString();
		$handles = $update->getHandles();
		$nodes = Mage::getModel('layoutanalyzer/analyzer')->xmlToArray(simplexml_load_string('<layout>'.$xml.'</layout>'));

		$analyzerBlock = $layout->getBlock('layout_analyzer');

		if($xml && $nodes && $analyzerBlock) {
			$analyzerBlock->setXml($xml);
			$analyzerBlock->setNodesArray($nodes);
			$analyzerBlock->setHandles($handles);
		}
	}

}