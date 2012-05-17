<?php

class Anton_Fbreferral_Block_Adminhtml_Fbreferral_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('fbreferralGrid');
      // This is the primary key of the database
      $this->setDefaultSort('fbreferral_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('fbreferral/fbreferral')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('fbreferral_id', array(
          'header'    => Mage::helper('fbreferral')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'fbreferral_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('fbreferral')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('content', array(
          'header'    => Mage::helper('fbreferral')->__('Item Content'),
          'width'     => '150px',
          'index'     => 'content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('fbreferral')->__('Status'),
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