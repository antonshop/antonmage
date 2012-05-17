<?php

class Anton_Fbreferral_Adminhtml_FbreferralController extends Mage_Adminhtml_Controller_action
{

  protected function _initAction()
  {
      $this->loadLayout()
          ->_setActiveMenu('fbreferral/items')
          ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      return $this;
  }   
 
  public function indexAction()
  {
      $this->_initAction()
           ->renderLayout();
  }

  public function editAction()
  {
      $fbreferralId     = $this->getRequest()->getParam('id');
      $fbreferralModel  = Mage::getModel('fbreferral/fbreferral')->load($fbreferralId);

      if ($fbreferralModel->getId() || $fbreferralId == 0) {

          Mage::register('fbreferral_data', $fbreferralModel);

          $this->loadLayout();
          $this->_setActiveMenu('fbreferral/items');
         
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
         
          $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
         
          $this->_addContent($this->getLayout()->createBlock('fbreferral/adminhtml_fbreferral_edit'))
               ->_addLeft($this->getLayout()->createBlock('fbreferral/adminhtml_fbreferral_edit_tabs'));
             
          $this->renderLayout();
      } else {
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fbreferral')->__('Item does not exist'));
          $this->_redirect('*/*/');
      }
  }
 
  public function newAction()
  {
      $this->_forward('edit');
  }
 
  public function saveAction()
  {
      if ( $this->getRequest()->getPost() ) {
          try {
              $postData = $this->getRequest()->getPost();
              $fbreferralModel = Mage::getModel('fbreferral/fbreferral');
             
              $fbreferralModel->setId($this->getRequest()->getParam('id'))
                  ->setTitle($postData['title'])
                  ->setContent($postData['content'])
                  ->setStatus($postData['status'])
                  ->save();
             
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
              Mage::getSingleton('adminhtml/session')->setFbreferralData(false);

              $this->_redirect('*/*/');
              return;
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
              Mage::getSingleton('adminhtml/session')->setFbreferralData($this->getRequest()->getPost());
              $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
              return;
          }
      }
      $this->_redirect('*/*/');
  }
 
  public function deleteAction()
  {
      if( $this->getRequest()->getParam('id') > 0 ) {
          try {
              $fbreferralModel = Mage::getModel('fbreferral/fbreferral');
             
              $fbreferralModel->setId($this->getRequest()->getParam('id'))
                  ->delete();
                 
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
              $this->_redirect('*/*/');
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
              $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
          }
      }
      $this->_redirect('*/*/');
  }
}