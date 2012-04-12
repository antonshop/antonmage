<?php

class Wt_Find_Block_Adminhtml_Find_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('findGrid');
      // This is the primary key of the database
      $this->setDefaultSort('find_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('find/find')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('find_id', array(
          'header'    => Mage::helper('find')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'find_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('find')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('content', array(
          'header'    => Mage::helper('find')->__('Item Content'),
          'width'     => '150px',
          'index'     => 'content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('find')->__('Status'),
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