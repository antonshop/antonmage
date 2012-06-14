<?php

class Appmagento_Tags_Block_Adminhtml_Tags_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('tagsGrid');
      // This is the primary key of the database
      $this->setDefaultSort('tags_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('tags/tags')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('tags_id', array(
          'header'    => Mage::helper('tags')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'tags_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('tags')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('content', array(
          'header'    => Mage::helper('tags')->__('Item Content'),
          'width'     => '150px',
          'index'     => 'content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('tags')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Active',
              0 => 'Inactive',
          ),
      ));

      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}