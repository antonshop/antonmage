<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class TBT_Enhancedgrid_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $isenhanced = true;
    private $columnSettings = array();
    private $isenabled = true;

    public function __construct()
    {
        parent::__construct();
        $this->isenabled = Mage::getStoreConfig('enhancedgrid/general/isenabled');
        
        $this->setId('productGrid');

        $this->prepareDefaults();
        
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
        
        $this->prepareColumnSettings();
        $this->setTemplate('tbt/enhancedgrid/catalog/product/grid.phtml');

    }
    
    private function prepareDefaults() {
        $this->setDefaultLimit(Mage::getStoreConfig('enhancedgrid/defaults/limit'));
        $this->setDefaultPage(Mage::getStoreConfig('enhancedgrid/defaults/page'));
        $this->setDefaultSort(Mage::getStoreConfig('enhancedgrid/defaults/sort'));
        $this->setDefaultDir(Mage::getStoreConfig('enhancedgrid/defaults/dir'));

    }
    
    private function prepareColumnSettings() {
        $storeSettings = Mage::getStoreConfig('enhancedgrid/columns/showcolumns');
        
        $tempArr = explode(',', $storeSettings);
        
        foreach($tempArr as $showCol) {
            $this->columnSettings[trim($showCol)] = true;
        }
    }
    
    public function colIsVisible($code) {
        return isset($this->columnSettings[$code]);
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('export_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Export'),
                    'onclick'   => $this->getJsObjectName().'.doExport()',
                    'class'   => 'task'
                ))
        );
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'task'
                ))
        );
        return parent::_prepareLayout();
    }
    public function getQueryStr() {
        return urldecode($this->getParam('q'));
    }
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        if($queryString = $this->getQueryStr()) {
            $collection = Mage::Helper('enhancedgrid')
                ->getSearchCollection($queryString, $this->getRequest());
        }
        if(!$collection) {
            $collection = Mage::getModel('catalog/product')->getCollection();
        }
        $store = $this->_getStore();
        $collection 
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        $collection->addAttributeToSelect('sku');
        
        //$collection->addAttributeToSelect('attribute_set_id');
        //$collection->addAttributeToSelect('type_id');
        //$collection->addAttributeToSelect('image'); // TODO remove this
    
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
        // EG: Select all needed columns.
        //id,name,type,attribute_set,sku,price,qty,visibility,status,websites,image
        foreach($this->columnSettings as $col => $true) {
            if($col == 'qty' || $col == 'websites' || $col=='id') continue;
            $collection->addAttributeToSelect($col);
        }
        
        $this->setCollection($collection);
        
        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));
        
        parent::_prepareCollection();
        $collection->addWebsiteNamesToResult();
        
        return $this;
    }


    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }


    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        
        $store = $this->_getStore();
        if($this->colIsVisible('id')) {
            $this->addColumn('id',
                array(
                    'header'=> Mage::helper('catalog')->__('ID'),
                    'width' => '50px',
                    'type'  => 'number',
                    'index' => 'entity_id',
            ));
        }
        
        $imgWidth = Mage::getStoreConfig('enhancedgrid/images/width') + "px";
        
        if($this->colIsVisible('thumbnail')) {
            $this->addColumn('thumbnail',
                array(
                    'header'=> Mage::helper('catalog')->__('Thumbnail'),
                    'type'  => 'image',
                    'width' => $imgWidth,
                    'index' => 'thumbnail',
            ));
        }
        if($this->colIsVisible('small_image')) {
            $this->addColumn('small_image',
                array(
                    'header'=> Mage::helper('catalog')->__('Small Img'),
                    'type'  => 'image',
                    'width' => $imgWidth,
                    'index' => 'small_image',
            ));
        }
        if($this->colIsVisible('image')) {
            $this->addColumn('image',
                array(
                    'header'=> Mage::helper('catalog')->__('Image'),
                    'type'  => 'image',
                    'width' => $imgWidth,
                    'index' => 'image',
            ));
        }
        
        if($this->colIsVisible('name')) {
            $this->addColumn('name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name'),
                    'index' => 'name',
                    'width' => '150px'
            ));
        }
        if($this->colIsVisible('name')) {
            if ($store->getId()) {
                $this->addColumn('custom_name',
                    array(
                        'header'=> Mage::helper('catalog')->__('Name In %s', $store->getName()),
                        'index' => 'custom_name',
                        'width' => '150px'
                ));
            }
        }

        if($this->colIsVisible('type_id')) {
            $this->addColumn('type',
                array(
                    'header'=> Mage::helper('catalog')->__('Type'),
                    'width' => '60px',
                    'index' => 'type_id',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));
        }

        
        if($this->colIsVisible('attribute_set_id')) {
            $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
    
            $this->addColumn('set_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                    'width' => '100px',
                    'index' => 'attribute_set_id',
                    'type'  => 'options',
                    'options' => $sets,
            ));
        }
        
        if($this->colIsVisible('sku')) {
            $this->addColumn('sku',
                array(
                    'header'=> Mage::helper('catalog')->__('SKU'),
                    'width' => '80px',
                    'index' => 'sku',
            ));
        }


        if($this->colIsVisible('price')) {
            $this->addColumn('price',
                array(
                    'header'=> Mage::helper('catalog')->__('Price'),
                    'type'  => 'price',
                    'currency_code' => $store->getBaseCurrency()->getCode(),
                    'index' => 'price',
            ));
        }


        if($this->colIsVisible('qty')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }


        if($this->colIsVisible('visibility')) {
            $this->addColumn('visibility',
                array(
                    'header'=> Mage::helper('catalog')->__('Visibility'),
                    'width' => '70px',
                    'index' => 'visibility',
                    'type'  => 'options',
                    'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ));
        }


        if($this->colIsVisible('status')) {
            $this->addColumn('status',
                array(
                    'header'=> Mage::helper('catalog')->__('Status'),
                    'width' => '70px',
                    'index' => 'status',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));
        }


        if($this->colIsVisible('websites')) {
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addColumn('websites',
                    array(
                        'header'=> Mage::helper('catalog')->__('Websites'),
                        'width' => '100px',
                        'sortable'  => false,
                        'index'     => 'websites',
                        'type'      => 'options',
                        'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
            }
        }

        // EG: Show all (other) needed columns.
        $ignoreCols = array('id'=>true, 'websites'=>true,'status'=>true,'visibility'=>true,'qty'=>true,
                'price'=>true,'sku'=>true,'attribute_set_id'=>true, 'type_id'=>true,'name'=>true, 
                'image'=>true, 'thumbnail' => true, 'small_image'=>true);
        $currency = $store->getBaseCurrency()->getCode();
        $truncate =  Mage::getStoreConfig('enhancedgrid/general/truncatelongtextafter');
        $defaults = array(
            'cost'  => array('type'=>'price', 'width'=>'30px', 'header'=> Mage::helper('catalog')->__('Cost'), 'currency_code' => $currency),
            'weight'  => array('type'=>'number', 'width'=>'30px', 'header'=> Mage::helper('catalog')->__('Weight')),
            'url_key'  => array('type'=>'text', 'width'=>'100px', 'header'=> Mage::helper('catalog')->__('Url Key')),
            'tier_price'  => array('type'=>'price', 'width'=>'100px', 'header'=> Mage::helper('catalog')->__('Tier Price'), 'currency_code' => $currency),
            'tax_class_id'  => array('type'=>'text', 'width'=>'100px', 'header'=> Mage::helper('catalog')->__('Tax Class ID')),
            'special_to_date'  => array('type'=>'date', 'width'=>'100px', 'header'=> Mage::helper('catalog')->__('Spshl TO Date')),
            'special_price'  => array('type'=>'price', 'width'=>'30px', 'header'=> Mage::helper('catalog')->__('Special Price'), 'currency_code' => $currency),
            'special_from_date'  => array('type'=>'date', 'width'=>'100px', 'header'=> Mage::helper('catalog')->__('Spshl FROM Date')),
            'color'  => array('type'=>'text', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Spshl TO Date')),
            'size'  => array('type'=>'text', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Size')),
            'brand'  => array('type'=>'text', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Brand')),
            'custom_design'  => array('type'=>'text', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Custom Design')),
            'custom_design_from'  => array('type'=>'date', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Custom Design FRM')),
            'custom_design_to'  => array('type'=>'date', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Custom Design TO')),
            'default_category_id'  => array('type'=>'text', 'width'=>'70px', 'header'=> Mage::helper('catalog')->__('Default Categry ID')),
            'dimension'  => array('type'=>'text', 'width'=>'75px', 'header'=> Mage::helper('catalog')->__('Dimensions')),
            'manufacturer'  => array('type'=>'text', 'width'=>'75px', 'header'=> Mage::helper('catalog')->__('Manufacturer')),
            'meta_keyword'  => array('type'=>'text', 'width'=>'200px', 'header'=> Mage::helper('catalog')->__('Meta Keywds')),
            'meta_description'  => array('type'=>'text', 'width'=>'200px', 'header'=> Mage::helper('catalog')->__('Meta Descr')),
            'meta_title'  => array('type'=>'text', 'width'=>'100px', 'header'=> Mage::helper('catalog')->__('Meta Title')),
            'short_description'  => array('type'=>'text', 'width'=>'150px', 'header'=> Mage::helper('catalog')->__('Short Description'), 'string_limit'=>$truncate),
            'description'  => array('type'=>'text', 'width'=>'200px', 'header'=> Mage::helper('catalog')->__('Description'), 'string_limit'=>$truncate)
        );
        //id,name,type,attribute_set,sku,price,qty,visibility,status,websites,image
        foreach($this->columnSettings as $col => $true) {
            if(isset($ignoreCols[$col])) continue;
            if(isset($defaults[$col])) {
                $innerSettings = $defaults[$col];
            } else {
                $innerSettings = array(
                    'header'=> Mage::helper('catalog')->__($col),
                    'width' => '100px',
                    'type'  => 'text',
                );
            }
            $innerSettings['index'] = $col;
            $this->addColumn($col, $innerSettings);
        }

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'id' => "editlink",
                        'url'     => array(
                            'base'=>'adminhtml/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Update attributes'),
            'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
        ));

        
        // Divider
        $this->getMassactionBlock()->addItem('imagesDivider', $this->getMADivider("Images"));
        
        // Show images...
        $imgWidth = Mage::getStoreConfig('enhancedgrid/images/width') ;
        $imgHeight = Mage::getStoreConfig('enhancedgrid/images/height');
        $this->getMassactionBlock()->addItem('showImages', array(
            'label' => $this->__('Show Selected Images'),
            'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
            'callback' => 'showSelectedImages(productGrid_massactionJsObject, '
                .'{checkedValues}, \'<img src=\\\'{imgurl}\\\' width='.$imgWidth
                .' height='.$imgHeight.' border=0 />\')'
            
        ));
        // Hide Images
        $this->getMassactionBlock()->addItem('hideImages', array(
            'label' => $this->__('Hide Selected Images'),
            'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
            'callback' => 'hideSelectedImages(productGrid_massactionJsObject, {checkedValues})'
            
        ));
        
        // Divider 3
        $this->getMassactionBlock()->addItem('otherDivider', $this->getMADivider("Other"));
        
        // Opens all products

        // Refresh...
        $this->getMassactionBlock()->addItem('refreshProducts', array(
            'label' => $this->__('Refresh Products'),
            'url'   => $this->getUrl('*/*/massRefreshProducts', array('_current'=>true))
        ));
        
       // $this->getMassactionBlock()->addItem('saveEditables', array(
       //     'label' => $this->__('SAVE EDITABLES'),
       //     'url'   => $this->getUrl('*/*/saveEditables', array('_current'=>true)),
       //     'fields' => array('short_description2', '')
       // ));
        
        
        return $this;
    }
    
    
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    
    
    protected function getMADivider($dividerHeading="-------") {
        $dividerTemplate = array(
          'label' => '--------'.$this->__($dividerHeading).'--------',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }
    
}