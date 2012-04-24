<?php

class Magentix_LayoutAnalyzer_IndexController extends Mage_Core_Controller_Front_Action {

	public function preDispatch() {
		parent::preDispatch();

		if(!Mage::getStoreConfigFlag('dev/layoutanalyzer/active')) {
			$this->norouteAction();
		}
	}
	
	public function cacheAction() {
		Mage::app()->cleanCache();
		$this->_redirectReferer();
	}
	
	public function searchAction() {
		$search = $this->getRequest()->getPost('name', false);
		
		$_files = Mage::helper('layoutanalyzer')->__('Appears in : ');
		
		if($search) {
			list($name,$node) = explode('|',$search);
			$analyzer = Mage::getModel('layoutanalyzer/analyzer');
			$files = $analyzer->getFileNames();
			$appearFiles = $analyzer->searchFilesByNode($files,$name,$node);
			
			if(count($appearFiles)) {
				foreach($appearFiles as $b) {
					$filename = substr($b,strrpos($b,DS)+1);
					$_files .= '<a href="javascript:layoutGetFile(\''.addslashes($b).'\',\''.$search.'\',\'b\')" class="layout-get-file">'.$filename.'</a> ';
				}
			} else {
				$_files = Mage::helper('layoutanalyzer')->__('Not found in files, updated from backend');
			}
		}
		
		echo $_files;
	}
	
	public function openfileAction() {
		$file = $this->getRequest()->getPost('file', false);
		$name = $this->getRequest()->getPost('highlight', false);
		$type = $this->getRequest()->getPost('type', false);
		
		$_content = '';
		
		if($file) {
			$_content = htmlentities(file_get_contents($file));
			if($name && $type) {
				switch($type) {
					case 'h':
						$_content = preg_replace('/&lt;(.?)'.$name.'&gt;/','<span class="layout-highlight">&lt;$1'.$name.'&gt;</span>',$_content);
					break;
					default:
						list($name,$node) = explode('|',$name);
						$_content = preg_replace('/&lt;'.$node.'(.*?)&quot;'.$name.'&quot;(.*?)&gt;/','<span class="layout-highlight">&lt;'.$node.'$1&quot;'.$name.'&quot;$2&gt;</span>',$_content);
					break;
				}
			}
		}
		
		echo '<div class="layout-file-path">'.$file.'</div><pre class="prettyprint">'.$_content.'</pre><script type="text/javascript">prettyPrint();</script>';
	}
	
	public function getallfilesAction() {
		$search = $this->getRequest()->getPost('files', false);
		$name = $this->getRequest()->getPost('name', false);
	
		$_files = '';
		$files = Mage::getModel('layoutanalyzer/analyzer')->getFileNames();
		
		foreach($files as $f) {
			$filename = substr($f,strrpos($f,DS)+1);
			$_files .= '<strong'.(preg_match('/'.$filename.'/',$search) ? ' class="layout-highlight"' : '').'>'.$filename.'</strong> <a href="javascript:void(0);" onclick="javascript:layoutGetFile(\''.addslashes($f).'\',\''.$name.'\',\'b\')">'.$f.'</a><br />';
		}
		
		echo $_files;
	}

}