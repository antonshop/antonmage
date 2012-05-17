<?php


class Zizio_Powershare_Block_Adminhtml_Incentives_Edit_Categories_Tree extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
	protected $_form;
	protected $_product;
	protected $_categoryIds;

    public function __construct()
    {
    	$args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }
        $data = $args[0];
        
        if (isset($data['category_ids']))
        	$this->setCategoryIds($data['category_ids']);
        else
        	$this->setCategoryIds(array());
        	
        parent::__construct();
    }
    
    public function getProduct()
    {
		if (!$this->_product)
    		$this->_product = Mage::getModel('catalog/product')->setStoreId($this->getRequest()->getParam('store', 0));
        return $this->_product;
	}
	
    public function isReadonly()
    {
        return false;
    }

	protected function setCategoryIds($categoryIds)
	{
		$this->_categoryIds = $categoryIds;
	}
	
    protected function getCategoryIds()
    {
        return (array) $this->_categoryIds;
    }
	
	protected function setForm($form)
	{
		$this->_form = $form;
	}
	
	protected function getForm()
	{
		if (!$this->_form)
			$this->_form = new Varien_Object(array(
	        	'html_id_prefix' => "",
	        	'html_id_suffix' => ""
	       	));
		return $this->_form;
	}
	
	public function getField($elementId, $type, $config=array())
	{
		return Mage::helper('powershare')->getFieldElement($elementId, $type, $config, $this->getForm());
	}
	
	protected function _getMoreHtml()
	{
		$cat = $this->getField("all_categories", "zizio/checkbox", array(
			'name'     => 'all_categories',
			'label'    => Mage::helper('powershare')->__('Enabled for all Categories'),
			'title'	   => Mage::helper('powershare')->__('Enabled for all Categories'),
			'checked'  => $this->getCategoryIds() ? false : true
        ));
        
        $hr = $this->getField("product-categories-hr", "zizio/hr");
        
        $js = '<script type="text/javascript">'
        	. '	   window.Z_DisableCategories = (function() {'
        	. '	       window.z_product_categories = $("product_categories").value || "";'
        	. '		   $("product_categories").value = "";'
        	. '        if (Prototype.Browser.IE) {'
        	. '        	   $$("#product-categories, #product-categories-hr").invoke("hide");'
        	. '        } else {'
        	. '		   	   $("product-categories").disabled = true;'
        	. '	       	   $("product-categories").setOpacity(0.2);'
        	. '	       	   var mask = $("z-product-categories-mask");'
        	. '	       	   if (mask)'
        	. '	       	       mask.remove();'
        	. '	    	   mask = new Element("div", {"id": "z-product-categories-mask"});'
        	. '	     	   $("product-categories").insert({"before": mask});'
        	. '	    	   mask.absolutize();'
        	. '	       	   mask.setStyle({"z-index": 10});'
        	. '	       	   var timeout = (function() {'
        	. '            	   if (!$("product-categories").disabled)'
        	. '                	   return;'
        	. '            	   mask.clonePosition($("product-categories"));'
        	. '            	   window.z_product_categories_mask_timeout = window.setTimeout(arguments.callee, 500);'
        	. '        	   });'
        	. '		       timeout();'
        	. '        }'
        	. '	   });'
        	. '	   '
        	. '	   window.Z_EnableCategories = (function() {'
        	. '        $("product_categories").value = window.z_product_categories || "";'
        	. '        if (Prototype.Browser.IE) {'
        	. '            $$("#product-categories, #product-categories-hr").invoke("show");'
        	. '        } else {'
        	. '		       $("product-categories").disabled = false;'
        	. '	           $("product-categories").setOpacity(1);'
        	. '	           var mask = $("z-product-categories-mask");'
        	. '	           if (mask)'
        	. '	               mask.remove();'
        	. '	           if (window.z_product_categories_mask_timeout)'
        	. '	           	   window.z_product_categories_mask_timeout = window.clearTimeout(window.z_product_categories_mask_timeout);'
        	. '    	   }'
        	. '	   });'
        	. '    '
        	. '    (function() {'
        	. '        var callback = (function() {'
        	. '            this.checked ? Z_DisableCategories() : Z_EnableCategories();'
        	. '        });'
        	. '        $("all_categories").observe("change", callback);'
        	. '        $("all_categories").observe("click", callback);'
        	. '        callback.call($("all_categories"));'
			. '	   })();'
        	. '</script>';
        
        $html  = $cat->toHtml();
        $html .= $hr->toHtml();
        $html .= $js;

		return $html;
	}
	
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $find = Mage::helper('powershare')->FindHtmlElement($html, array(
			'tag_name' => "input",
			'attributes' => array(
        		'name' => "/^category_ids$/"
        	)
        ));
        if ($find) {
        	$more_html = $this->_getMoreHtml();
        	$html = substr_replace($html, $more_html, $find['start_offset'], 0);
        }
        
    	return $html;
    }
}
