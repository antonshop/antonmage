<?php

class Appmagento_Tags_Adminhtml_TagsController extends Mage_Adminhtml_Controller_action
{

  protected function _initAction()
  {
      $this->loadLayout()
          ->_setActiveMenu('tags/items')
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
      $tagsId     = $this->getRequest()->getParam('id');
      $tagsModel  = Mage::getModel('tags/tags')->load($tagsId);

      if ($tagsModel->getId() || $tagsId == 0) {

          Mage::register('tags_data', $tagsModel);

          $this->loadLayout();
          $this->_setActiveMenu('tags/items');
         
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
          $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
         
          $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
         
          $this->_addContent($this->getLayout()->createBlock('tags/adminhtml_tags_edit'))
               ->_addLeft($this->getLayout()->createBlock('tags/adminhtml_tags_edit_tabs'));
             
          $this->renderLayout();
      } else {
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tags')->__('Item does not exist'));
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
              $tagsModel = Mage::getModel('tags/tags');
             
              $tagsModel->setId($this->getRequest()->getParam('id'))
                  ->setTitle($postData['title'])
                  ->setContent($postData['content'])
                  ->setStatus($postData['status'])
                  ->save();
             
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
              Mage::getSingleton('adminhtml/session')->setTagsData(false);

              $this->_redirect('*/*/');
              return;
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
              Mage::getSingleton('adminhtml/session')->setTagsData($this->getRequest()->getPost());
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
              $tagsModel = Mage::getModel('tags/tags');
             
              $tagsModel->setId($this->getRequest()->getParam('id'))
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