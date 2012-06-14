<?php
class Appmagento_Tags_IndexController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch(){
        parent::preDispatch();
		if(!Mage::helper('tags')->getEnabled())
            return $this->_redirectUrl(Mage::getBaseUrl());
	}
		
    public function indexAction()
    {
    	$this->loadLayout();
    	
    	$title = Mage::helper('tags')->getTitle();
    	//$titleUrl = Mage::helper('tags')->getTitleUrl();
    	
    	$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Home Page'), 'link'=>Mage::getBaseUrl()));
		$breadcrumbs->addCrumb($title, array('label'=>$title, 'title'=>$title, 'link'=>Mage::getBaseUrl().'tags'));
		$breadcrumbs->addCrumb('manufacturer', array('label'=>'State', 'title'=>'States'));
        
		$this->renderLayout();
    }
}