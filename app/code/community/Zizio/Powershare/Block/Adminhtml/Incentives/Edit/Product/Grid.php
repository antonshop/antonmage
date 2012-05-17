<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Edit_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_form;
	protected $_product_ids;
	protected $_id = 'product_selection';
	
	public function __construct()
	{
        parent::__construct();
        
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }
        $data = $args[0];
        
        if (isset($data['product_ids'])) { // get product ids from explicit constrcution data (used for ajax actions) 
        	$this->setProductIds($data['product_ids']);
        } else {                           // get product ids from current request bound incentive
        	$incentive = Mage::registry('powershare_incentive');
	    	if ($incentive)
	    		$this->setProductIds($incentive->getProductIds());
        	else
	    		$this->setProductIds(array());
        }

		$this->setId($this->_id);
		$this->setDefaultSort('id');
		$this->setUseAjax(true);
		$this->setVarNameFilter('product_filter');
		$this->setRowClickCallback("Z_ProductSelectGridRow");
		$this->setCheckboxCheckCallback("Z_ProductCheckGridCell");
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
	
	protected function _getStore()
	{
		return Mage::app()->getStore($this->getRequest()->getParam('store'));
	}
	
	protected function _beforeToHtml()
	{
		$this->setId($this->_id);
		$this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
		$this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
    	return parent::_beforeToHtml();
	}

	protected function _addColumnFilterToCollection($column)
	{
		if ($this->getCollection() && ($column->getId() == 'websites')) {
			$this->getCollection()->joinField(
				'websites',
				'catalog/product_website',
				'website_id',
				'product_id=entity_id',
				null,
				'left'
			);
		}

		return parent::_addColumnFilterToCollection($column);
	}

	protected function _prepareCollection()
	{
		try
		{
			$store = $this->_getStore();
			
			$collection = Mage::getModel('catalog/product')->getCollection()
				->setStore($store)
				->addAttributeToSelect('name')
				->addAttributeToSelect('small_image')
				->addAttributeToSelect('meta_description')
				->addAttributeToSelect('description')
				->addAttributeToSelect('category_ids')
				->addAttributeToSelect('sku')
				->addAttributeToSelect('price')
				->addAttributeToSelect('price_type')
				->addAttributeToSelect('url_key')
				->addAttributeToSelect('type_id')
				->addAttributeToSelect('attribute_set_id')
				->addFieldToFilter('visibility', array('gt'=>'1'));
			
			if ($store->getId()) {
				//$collection->setStoreId($store->getId());
				$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
				$collection->addStoreFilter($store);
				$collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
				$collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
				$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
				$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
				$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
			}
			else
			{
				$collection->addAttributeToSelect('price');
				$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
				$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
			}

			$this->setCollection($collection);
			parent::_prepareCollection();
			$this->getCollection()->addWebsiteNamesToResult();
		}
		catch (Exception $ex)
		{
			Mage::helper('powershare')->LogError($ex);
		}
		return $this;
	}
	
    protected function getProductIds()
    {
        return (array) $this->_productIds;
    }

	protected function setProductIds($productIds)
	{
		$this->_productIds = $productIds;
	}
    
	protected function _prepareColumns()
	{
		try
		{
			$this->addColumn('in_products', array(
	            'header_css_class' => 'a-center',
	            'type'      => 'checkbox',
	            'name'      => 'in_products',
	            'values'    => $this->getProductIds(),
	            'align'     => 'center',
	            'index'     => 'entity_id',
	            'use_index' => true,
	        ));
			
			$this->addColumn('prd_entity_id', array(
				'header'    => Mage::helper('sales')->__('ID'),
				'sortable'  => true,
				'width'     => '60px',
				'index'     => 'entity_id'
			));

			$this->addColumn('prd_name', array(
				'header'    => Mage::helper('sales')->__('Product Name'),
				'index'     => 'name',
				'column_css_class'=> 'name'
			));

			$this->addColumn('prd_sku', array(
				'header'    => Mage::helper('sales')->__('SKU'),
				'width'     => '80px',
				'index'     => 'sku',
				'column_css_class'=> 'sku'
			));

			$this->addColumn('prd_price', array(
				'header'    => Mage::helper('sales')->__('Price'),
				'align'     => 'center',
				'type'      => 'currency',
				'currency_code' => $this->_getStore()->getCurrentCurrencyCode() ." ",
				'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
				'index'     => 'price'
			));

			$this->addColumn('prd_qty', array(
				'header'=> Mage::helper('catalog')->__('Qty'),
				'width' => '100px',
				'type'  => 'number',
				'index' => 'qty',
			));

			$this->addColumn('prd_type', array(
				'header'=> Mage::helper('catalog')->__('Type'),
				'width' => '90px',
				'index' => 'type_id',
				'type'  => 'options',
				'options' => Mage::getModel('catalog/product_type')->getOptionArray(),
			));

			$this->addColumn('prd_visibility', array(
				'header'=> Mage::helper('catalog')->__('Visibility'),
				'width' => '70px',
				'index' => 'visibility',
				'type'  => 'options',
				'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
			));

			$this->addColumn('prd_status', array(
				'header'=> Mage::helper('catalog')->__('Status'),
				'width' => '70px',
				'index' => 'status',
				'type'  => 'options',
				'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
			));

			if (!Mage::app()->isSingleStoreMode()) {
				$this->addColumn('websites', array(
					'header'=> Mage::helper('catalog')->__('Websites'),
					'width' => '100px',
					'sortable'  => false,
					'index'     => 'websites',
					'type'      => 'options',
					'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
				));
			}
		}
		catch (Exception $ex)
		{
			Mage::helper('powershare')->LogError($ex);
		}
		return parent::_prepareColumns();
	}

	//protected function _prepareMassaction()
	//{
	//	$this->setMassactionIdField('product_id');
	//	$this->getMassactionBlock()->setFormFieldName('product');
	//
	//	$this->getMassactionBlock()->addItem('add', array(
	//		'label' => $this->__('Show Incentive on'),
	//		'url'   => $this->getUrl('*/*/massAdd', array('_current'=>true))
	//	));
	//	return $this;
	//}

	public function getGridUrl()
	{
		// This will invoke a call to the controller object PowershareController.php to
		// function name gridProductAction
		$ret = $this->getUrl('*/*/gridProduct', array(
			'index'    => $this->getIndex(),
			'_current' => true
		));
		return $ret;
	}
	
	public function getField($elementId, $type, $config=array())
	{
		return Mage::helper('powershare')->getFieldElement($elementId, $type, $config, $this->getForm());
	}
	
	protected function _getMoreHtml()
	{
		$prd = $this->getField("all_products", "zizio/checkbox", array(
			'name'     => 'all_products',
			'label'    => Mage::helper('powershare')->__('Enabled for all Products'),
			'title'	   => Mage::helper('powershare')->__('Enabled for all Products'),
			'style'    => "height: 14px;",
			'checked'  => $this->getProductIds() ? false : true
        ));
        
        $hr = $this->getField("products-hr", "zizio/hr");
        
        $prd_ids = $this->getField("product_ids", "hidden", array(
			'name'     => 'product_ids',
			'value'    => implode(",", $this->getProductIds())
        ));
   
        $js = '
<script type="text/javascript">
	window.Z_ProductSelectGridRow = (function (grid, event) {
        var trElement = Event.findElement(event, "tr");
        var isInput = Event.element(event).tagName == "INPUT";
        if (trElement) {
            var checkbox = Element.select(trElement, "input");
            if (checkbox[0]) {
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                grid.setCheckboxChecked(checkbox[0], checked);
            }
        }
    });
    
    window.product_ids = $A($F("product_ids").split(","));
    
    window.Z_ProductCheckGridCell = (function (grid, element, checked) {
	    if (checked) {
            if (!element.up("th")) {
            	product_ids.push(element.value);
            }
        } else {
            product_ids = $A(product_ids).without(element.value);
        }
        var joint_product_ids = product_ids.filter(function(n){ return n; }).join(",")
        $("product_ids").value = joint_product_ids;
        product_selectionJsObject.addVarToUrl("product_ids", joint_product_ids);
    });
        		   
    window.Z_DisableProducts = (function() {
		$("product_ids").value = "";
        if (Prototype.Browser.IE) {
        	$$("#products-grid, #products-hr").invoke("hide");
        } else {
		   	$("products-grid").disabled = true;
	       	$("products-grid").setOpacity(0.2);
	       	var mask = $("z-products-grid-mask");
	       	if (mask)
	       	    mask.remove();
	    	mask = new Element("div", {"id": "z-products-grid-mask"});
	     	$("products-grid").insert({"before": mask});
	    	mask.absolutize();
	       	mask.setStyle({"z-index": 10});
	       	var timeout = (function() {
            	if (!$("products-grid").disabled)
                	return;
            	mask.clonePosition($("products-grid"));
            	window.z_products_grid_mask_timeout = window.setTimeout(arguments.callee, 500);
        	});
		    timeout();
        }
	});
	   
	window.Z_EnableProducts = (function() {
        $("product_ids").value = window.product_ids || "";
        if (Prototype.Browser.IE) {
            $$("#products-grid, #products-hr").invoke("show");
        } else {
		    $("products-grid").disabled = false;
	        $("products-grid").setOpacity(1);
	        var mask = $("z-products-grid-mask");
            if (mask)
                mask.remove();
	        if (window.z_products_grid_mask_timeout)
	            window.z_products_grid_mask_timeout = window.clearTimeout(window.z_products_grid_mask_timeout);
    	}
	});
    
    (function() {
        var callback = (function() {
            this.checked ? Z_DisableProducts() : Z_EnableProducts();
        });
        $("all_products").observe("change", callback);
        $("all_products").observe("click", callback);
        callback.call($("all_products"));
	})();
</script>';
        
        $html  = $prd->toHtml();
        $html .= $hr->toHtml();
        $html .= '<div id="products-grid">';
        $html .= $prd_ids->toHtml();
        $html .= $js;
        
		return $html;
	}
	
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $find = Mage::helper('powershare')->FindHtmlElement($html, array(
			'tag_name' => "div",
			'attributes' => array(
        		'id' => "/^product_selection$/"
        	)
        ));
        $more_html = $this->_getMoreHtml();
        if ($find) {
        	$html = substr_replace($html, $more_html, $find['end_offset'] + 1, 0);
        } else {
        	$html = $more_html . $html;
        }
        $html .= "</div>";
        
    	return $html;
    }

}
