<?php

class Appmagento_Sharesuit_Block_Adminhtml_Sharesuit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('sharesuitGrid');
      // This is the primary key of the database
      $this->setDefaultSort('sharesuit_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('sharesuit/sharesuit')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('sharesuit_id', array(
          'header'    => Mage::helper('sharesuit')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'sharesuit_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('sharesuit')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('content', array(
          'header'    => Mage::helper('sharesuit')->__('Item Content'),
          'width'     => '150px',
          'index'     => 'content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('sharesuit')->__('Status'),
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