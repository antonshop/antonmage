<?php

class Appmagento_Sharesuit_Adminhtml_SharesuitController extends Mage_Adminhtml_Controller_action
{

  protected function _initAction()
  {
      $this->loadLayout()
          ->_setActiveMenu('sharesuit/items')
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
      $sharesuitId     = $this->getRequest()->getParam('id');
      $sharesuitModel  = Mage::getModel('sharesuit/sharesuit')->load($sharesuitId);

      if ($sharesuitModel->getId() || $sharesuitId == 0) {

          Mage::register('sharesuit_data', $sharesuitModel);

          $this->loadLayout();
          $this->_setActiveMenu('sharesuit/items');
         
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
         
          $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
         
          $this->_addContent($this->getLayout()->createBlock('sharesuit/adminhtml_sharesuit_edit'))
               ->_addLeft($this->getLayout()->createBlock('sharesuit/adminhtml_sharesuit_edit_tabs'));
             
          $this->renderLayout();
      } else {
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sharesuit')->__('Item does not exist'));
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
              $sharesuitModel = Mage::getModel('sharesuit/sharesuit');
             
              $sharesuitModel->setId($this->getRequest()->getParam('id'))
                  ->setTitle($postData['title'])
                  ->setContent($postData['content'])
                  ->setStatus($postData['status'])
                  ->save();
             
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
              Mage::getSingleton('adminhtml/session')->setSharesuitData(false);

              $this->_redirect('*/*/');
              return;
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
              Mage::getSingleton('adminhtml/session')->setSharesuitData($this->getRequest()->getPost());
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
              $sharesuitModel = Mage::getModel('sharesuit/sharesuit');
             
              $sharesuitModel->setId($this->getRequest()->getParam('id'))
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