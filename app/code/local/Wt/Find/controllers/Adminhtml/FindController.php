<?php

class Wt_Find_Adminhtml_FindController extends Mage_Adminhtml_Controller_action
{

  protected function _initAction()
  {
      $this->loadLayout()
          ->_setActiveMenu('find/items')
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
      $findId     = $this->getRequest()->getParam('id');
      $findModel  = Mage::getModel('find/find')->load($findId);

      if ($findModel->getId() || $findId == 0) {

          Mage::register('find_data', $findModel);

          $this->loadLayout();
          $this->_setActiveMenu('find/items');
         
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
         
          $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
         
          $this->_addContent($this->getLayout()->createBlock('find/adminhtml_find_edit'))
               ->_addLeft($this->getLayout()->createBlock('find/adminhtml_find_edit_tabs'));
             
          $this->renderLayout();
      } else {
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('find')->__('Item does not exist'));
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
              $findModel = Mage::getModel('find/find');
             
              $findModel->setId($this->getRequest()->getParam('id'))
                  ->setTitle($postData['title'])
                  ->setContent($postData['content'])
                  ->setStatus($postData['status'])
                  ->save();
             
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
              Mage::getSingleton('adminhtml/session')->setFindData(false);

              $this->_redirect('*/*/');
              return;
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
              Mage::getSingleton('adminhtml/session')->setFindData($this->getRequest()->getPost());
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
              $findModel = Mage::getModel('find/find');
             
              $findModel->setId($this->getRequest()->getParam('id'))
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