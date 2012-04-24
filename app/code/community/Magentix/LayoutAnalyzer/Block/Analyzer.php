<?php

class Magentix_LayoutAnalyzer_Block_Analyzer extends Mage_Core_Block_Text {

	public function getLayoutListe($nodes,$t=0,$s=0) {
		$colors = array('#900','#009','#060','#c60','#909','#c06');
		$html = '<ul class="layout-liste">';
		foreach ($nodes as $k => $v) {
			if(is_string($k)) {
				$html .= '<li style="color:'.(isset($colors[$t]) ? $colors[$t] : '#000').'">';
				$type = (explode(' ',$k));
				preg_match('#(name)="(.*)"#sU',$k,$name);
				if(is_array($v) && count($v)) {
					if($s == 1 && $t == 1 && isset($name[2])) {
						$html .= '<span'.(!$t ? ' class="first"' : '').'><a href="javascript:void(0);" style="color:'.$colors[$t].'" onclick="javascript:layoutSearch(\''.$name[2].'|'.$type[0].'\',this)">'.$k.'</a></span>'.$this->getLayoutListe($v,$t+1);
					} else {
						$html .= '<span'.(!$t ? ' class="first"' : '').'>'.$k.'</span>'.$this->getLayoutListe($v,$t+1);
					}
				} else {
					if($s == 1 && $t == 1 && isset($name[2])) {
						$html .= '<a href="javascript:void(0);" style="color:'.$colors[$t].'" onclick="javascript:layoutSearch(\''.$name[2].'|'.$type[0].'\',this)">'.$k.(is_string($v) ? ' '.$v : '').'</a>';
					} else {
						$html .= $k.(is_string($v) ? ' '.$v : '');
					}
				}
				$html .= '</li>';
			} else {
				$html .= '<li class="layout-liste-box">'.$this->getLayoutListe($v,$t,1).'</li>';
			}
		}
		return $html .= '</ul>';
	}

	public function getScript() {
		return '<script type="text/javascript" src="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.js"></script>
				<script type="text/javascript">
				//<![CDATA[
					function layoutSearch(blockname,el) {
						var links = $(\'layout-list-content\').select(\'a\');
						for(i=0;i<links.length;i++) { links[i].style.background = \'none\'; }
						
						var parameters = { name: blockname };
						var files_data = $(\'layout-files-data\');
						if(files_data) { files_data.update(\'Loading...\'); }
						new Ajax.Request(\''.Mage::getUrl('layoutanalyzer/index/search/').'\', {
							method: \'post\',
							onSuccess: function(transport)	{
								if(transport.status == 200)	{	
									var data = transport.responseText;
									if(files_data) {
										files_data.update(data);
										getAllFiles(data,blockname);
									}
									if(el) { el.style.background = \'#ff0\'; }
								}
							},
							parameters: parameters
						});
						void(0);
					}
					
					function layoutGetFile(dfile,highlight,type) {
						var parameters = { file: dfile };
						if(highlight) { parameters[\'highlight\'] = highlight; }
						if(type) { parameters[\'type\'] = type; }
						var file_content = $(\'layout-file-content\');
						if(file_content) { file_content.update(\'Loading...\'); }
						
						new Ajax.Request(\''.Mage::getUrl('layoutanalyzer/index/openfile/').'\', {
							method: \'post\',
							onSuccess: function(transport){
								if(transport.status == 200)	{	
									var data = transport.responseText;
									if(file_content) {
										file_content.update(data);
									}
								}
							},
							parameters: parameters
						});
						void(0);
					}
					
					function getAllFiles(data,blockname) {
						if(!data) { data = \'\'; }
						if(!blockname) { blockname = \'\'; }
						var parameters = { files: data };
						parameters[\'name\'] = blockname;
						
						var file_content = $(\'layout-file-content\');
						if(file_content) { file_content.update(\'Loading...\'); }
						
						new Ajax.Request(\''.Mage::getUrl('layoutanalyzer/index/getallfiles/').'\', {
							method: \'post\',
							onSuccess: function(transport){
								if(transport.status == 200)	{	
									var data = transport.responseText;
									if(file_content) {
										file_content.update(data);
									}
								}
							},
							parameters: parameters
						});
						void(0);
					}
					
					function layoutAction() {
						var layout_content = $(\'layout-content\');
						var layout_objects = $$(\'object\');
						if(layout_content) {
							if(layout_content.style.display == \'none\') {
								layout_content.show();
								if(layout_objects) {
									for(i=0;i<layout_objects.length;i++) { layout_objects[i].hide(); }
								}
							} else {
								layout_content.hide();
								if(layout_objects) {
									for(i=0;i<layout_objects.length;i++) { layout_objects[i].show(); }
								}
							}
						}
						void(0);
					}
					
					function colorsyntaxe() {
						prettyPrint();
						var pre = $$(\'pre.prettyprint\')[0];
						pre.className = \'window\';
					}
				//]]>
				</script>
		';
	}

	public function getStyle() {
		return '<style type="text/css">
				.layout-wrapper{position:absolute;top:0;left:0;right:0;text-align:left;font:11px Arial, Helvetica, sans-serif;border-bottom:3px solid #ccc;border-top:3px solid #ccc;background:#496778;z-index:1000;min-width:900px}
				.layout-wrapper a{text-decoration:none}
				.layout-wrapper .handles a{color:#1979A3}
				.layout-wrapper .layout-liste li a{text-decoration:underline}
				.layout-wrapper .layout-get-file{margin-left:5px;text-decoration:underline}
				.layout-liste{margin:0;padding:0}
				.layout-liste li{padding:3px 0 3px 15px;margin:0;list-style-type:none}
				.layout-liste li.first{border:0}
				.layout-liste li span.first{font-weight:bold}
				.layout-liste li.layout-liste-box{background:#fff;border:1px solid #ccc;margin:2px 0 2px 0}
				.layout-liste li.layout-liste-box .layout-liste-box{border:0;margin:0;padding:0}
				.layout-box{width:50%;float:left}
				.layout-box .window{overflow:auto;margin:0 10px;border:1px solid #ccc;height:350px;background:#efefef;padding:5px}
				.layout-box .window-full{margin:0 10px 0;border:1px solid #ccc;background:#efefef;padding:5px}
				.layout-box .module-author{margin:10px;border:1px solid #ccc;padding:5px;text-align:right;border:0;color:#efefef}
				.layout-box .module-author a{color:#fff;font-size:11px}
				.layout-action{padding:3px;color:#fff;display:block;background:#405C6C}
				.layout-action:hover{background:#000}
				.layout-title{margin:20px 10px 0 10px;background:#618499;color:#fff;font-size:16px;padding:3px 10px;font-weight:bold}
				.layout-title a{color:#C7E8FF}
				.layout-title a.clean{font-size:11px;margin-left:10px;font-weight:normal;float:right}
				.layout-highlight{background:#FFFF00}
				.layout-file-path{background:#ccc;margin-bottom:5px;padding:2px}
				#layout-files-data{margin:0 10px;font-size:12px;color:#C7E8FF;background:#618499;padding:0 5px}
				#layout-file-content a{color:#000}
				#layout-file-content a.use{color:#900}
				#layout-file-content strong{color:#900;display:block;margin-top:3px}
				.str{color:#080}.kwd{color:#008}.com{color:#800}.typ{color:#606}.lit{color:#066}.pun{color:#660}
				.pln{color:#000}.tag{color:#008}.atn{color:#606}.atv{color:#080}.dec{color:#606}
			</style>';
	}

	public function getAnalyzer() {
		if(!($this->getXml() && $this->getNodesArray())) return '';

		$_handles = '';
		$_files = '';

		$xml = $this->getXml();
		$nodes = $this->getNodesArray();
		$handles = $this->getHandles();
		$files = Mage::getModel('layoutanalyzer/analyzer')->getFileNames();
		sort($files);
		
		foreach($handles as $h) {
			$_handles .= '<strong>'.$h.'</strong><br />';
			$usefiles = Mage::getModel('layoutanalyzer/analyzer')->getUseFiles($files,array($h));
			foreach($usefiles as $f) {
				$_handles .= '<a href="javascript:void(0);" onclick="javascript:layoutGetFile(\''.addslashes($f).'\',\''.$h.'\',\'h\')">'.$f.'</a><br />';
			}
			$_handles .= '<br />';
		}
		
		foreach($files as $f) {
			$filename = substr($f,strrpos($f,DS)+1);
			$_files .= '<strong>'.$filename.'</strong> <a href="javascript:void(0);" onclick="javascript:layoutGetFile(\''.addslashes($f).'\')">'.$f.'</a><br />';
		}

		return '<div class="layout-wrapper"><a href="javascript:layoutAction()" class="layout-action">Layout Analyzer : '.Mage::helper('core/url')->getCurrentUrl().'</a><div id="layout-content" style="display:none">'.$this->getStyle().$this->getScript().
				'<div class="layout-box">
					<h2 class="layout-title">'.Mage::helper('layoutanalyzer')->__('Generated Layout').' <a href="'.Mage::getUrl('layoutanalyzer/index/cache/').'" class="clean">'.Mage::helper('layoutanalyzer')->__('Clean Cache and Refresh').'</a></h2>
					<pre class="prettyprint window">'.htmlentities($xml).'</pre>
					<script type="text/javascript">colorsyntaxe();</script>
				</div>
				<div class="layout-box">
					<h2 class="layout-title">'.Mage::helper('layoutanalyzer')->__('Organized Layout').' <span id="layout-files-data">'.Mage::helper('layoutanalyzer')->__('Select block for search').'</span></h2>
					<div class="window" id="layout-list-content">'.$this->getLayoutListe($nodes).'</div>
				</div>
				<div class="layout-box">
					<h2 class="layout-title">'.Mage::helper('layoutanalyzer')->__('Layout Files').' <a href="javascript:getAllFiles()" class="clean">'.Mage::helper('layoutanalyzer')->__('Files').'</a></h2>
					<div class="window" id="layout-file-content">'.$_files.'</div>
				</div>
				<div class="layout-box">
					<h2 class="layout-title">'.Mage::helper('layoutanalyzer')->__('Handles').'</h2>
					<div class="window handles">'.$_handles.'</div>
				</div>
				<div class="layout-box" style="width:100%"><div class="module-author">Layout Analyzer 0.1.2 - Magento '.Mage::getVersion().' - By Magentix - <a href="http://www.magentix.fr" target="_blank">http://www.magentix.fr</a> - <a href="http://www.magentix.fr/contact/" target="_blank">'.Mage::helper('layoutanalyzer')->__('Report All Bugs').'</a></div></div>
				</div>
				<div style="clear:both"></div>
			</div>';
	}

	protected function _toHtml() {
		$this->setCacheLifetime(null);
		$this->addText($this->getAnalyzer());
		return parent::_toHtml();
	}

}