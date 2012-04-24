<?php

class Magentix_LayoutAnalyzer_Model_Analyzer {

	public function xmlToArray($nodes) {
		$layout = array();
		foreach ($nodes->children() as $node) {
			$key = trim($node->getName().' : '.$this->extractAttributes($node));

			if(!array_key_exists($key, $layout)) {
				$layout[$key] = array();
			}

			if($child = $this->xmlToArray($node)) {
				if(is_array($layout[$key])) $layout[$key][] = $child;
			} else {
				if(strlen(trim((string)$node[0]))) {
					$layout[$key] = trim((string)$node[0]);
				}
			}

		}
		return $layout;
	}

	public function extractAttributes($node) {
		$attributes = '';
		foreach($node->attributes() as $a => $b) {
		    $attributes .= $a.'="'.$b.'" ';
		}
		return trim($attributes);
	}
	
	public function getFileNames() {
		$design = Mage::getSingleton('core/design_package');
		
		$updatesRoot = Mage::app()->getConfig()->getNode($design->getArea().'/layout/updates');
		
		$updateFiles = array();
		foreach ($updatesRoot->children() as $updateNode) {
			if ($updateNode->file) {
				$module = $updateNode->getAttribute('module');
				if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, Mage::app()->getStore()->getId())) {
					continue;
				}
				$updateFiles[] = (string)$updateNode->file;
			}
		}
		
		$filenames = array();
		
		foreach ($updateFiles as $file) {
			$filename = $design->getLayoutFilename($file, array(
				'_area'    => $design->getArea(),
				'_package' => $design->getPackageName(),
				'_theme'   => $design->getTheme('layout')
			));
			if (!is_readable($filename)) {
				continue;
			}

			$filenames[] = $filename;
		}
		return $filenames;
	}
	
	public function getUseFiles($files,$handles=array()) {
		$useFiles = array();
		foreach($files as $f) {
			$content = file_get_contents($f);
			foreach($handles as $h) {
				if(preg_match('/<'.$h.'(.*?)>/',$content)) {
					$useFiles[] = $f;
				}
			}
		}
		return $useFiles;
	}
	
	public function searchFilesByNode($files,$name,$node) {
		$_files = array();
		foreach($files as $f) {
			$content = file_get_contents($f);
			if(preg_match('/<'.$node.'(.*)name="'.$name.'"/',$content)) {
				$_files[] = $f;
			}
		}
		return $_files;
	}

}