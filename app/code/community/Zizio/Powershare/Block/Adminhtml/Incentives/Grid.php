<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('PowershareGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('desc');
		$this->setSaveParametersInSession(true);
		$this->setFilterVisibility(false);
	}
	
	protected function _prepareCollection()
	{
		try
		{
			$collection = new Zizio_Powershare_Model_RemoteData_Incentives_Collection();
			$this->setCollection($collection);
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'	=>	Mage::helper('powershare')->__('ID'),
			'align'		=>	'right',
			'width'		=>	'50px',
			'index'		=>	'id',
		));

		$this->addColumn('name', array(
			'header'	=>	Mage::helper('powershare')->__('Name'),
			'align'		=>	'left',
			'index'		=>	'name',
		));

		$this->addColumn('coupon_code', array(
			'header'	=>	Mage::helper('powershare')->__('Coupon Code'),
			'align'		=>	'left',
			'index'		=>	'coupon_code',
		));

		$this->addColumn('start_date', array(
			'header'	=>	Mage::helper('powershare')->__('Start'),
			'align'		=>	'left',
			'index'		=>	'start_date',
		));

		$this->addColumn('end_date', array(
			'header'	=>	Mage::helper('powershare')->__('End'),
			'align'		=>	'left',
			'index'		=>	'end_date',
		));

		$this->addColumn('status', array(
			'header'	=>	Mage::helper('powershare')->__('Status'),
			'align'		=>	'left',
			'width'		=>	'80px',
			'index'		=>	'status',
			'type'		=>	'options',
			'options'	=>	array(
				1	=>	'Enabled',
				2	=>	'Disabled',
			),
		));

		$this->addColumn('action', array(
			'header'	=>	Mage::helper('powershare')->__('Action'),
			'width'		=>	'100',
			'type'		=>	'action',
			'getter'	=>	'getId',
			'actions'	=>	array(
				array(
					'caption'	=>	Mage::helper('powershare')->__('Edit'),
					'url'		=>	array('base'=> '*/*/edit'),
					'field'		=>	'id'
				),
				array(
					'caption'	=>	Mage::helper('powershare')->__('Delete'),
					'url'		=>	array('base'=> '*/*/delete'),
					'field'		=>	'id',
					'confirm'	=> 'Incentive will be deleted, are you sure?'
				)
			),
			'filter'	=> false,
			'sortable'	=> false,
			'index'		=> 'action',
			'is_system'	=> true
		));
		
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		return $this;
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id'	=>	$row->getId()));
	}
}
