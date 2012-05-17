<?php

class Zizio_Powershare_Adminhtml_PowershareController extends Zizio_Powershare_Model_Adminhtml_BaseController
{
	protected function _initAction()
	{
		try
		{
			$this->loadLayout()
				->_setActiveMenu('promo/powershare')
				->_addBreadcrumb(Mage::helper('adminhtml')->__('Zizio Power Share Configuration'),
					Mage::helper('adminhtml')->__('Zizio Power Share Configuration'));
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
		return $this;
	}

	public function indexAction()
	{
		try
		{
			// check if we alrady run the registration process, if not do it now
			if (!Zizio_Powershare_Helper_Data::IsZizioPowershareRegistered())
			{
				$this->_forward('index', 'adminhtml_zizioRegister');
			}
			else
			{
				//$this->_redirect('adminhtml/system_config/edit', array('section' => "powershare"));
				$this->_initAction();
				$block = $this->getLayout()->createBlock('powershare/adminhtml_incentives');
				$content_block = $this->getLayout()->getBlock('content');
				$content_block->append($block);
				$this->renderLayout();
			}
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}
	
	public function editAction()
	{
		try
		{
			$id = $this->getRequest()->getParam('id');
			$model = new Zizio_Powershare_Model_Powershare();
			$model->load($id);

			if ($model->getId() || $id == 0)
			{
				$session = Mage::getSingleton('adminhtml/session');
				
				$incentives = $session->getData('zizio_incentives');
				if (!$incentives)
					$incentives = $incentives = array();
				
				$data = $session->getFormData(true);
				if (!empty($data))
					$model->setData($data);
				
				Mage::register('powershare_incentive', $model);
				$incentives[$model->getId()] = $model->getData();
				$session->setData('zizio_incentives', $incentives);
				
				$this->loadLayout();
				$this->_setActiveMenu('promo/powershare');

				$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

				$this->_addContent(
					$this->getLayout()->createBlock('powershare/adminhtml_incentives_edit'))->_addLeft(
						$this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_tabs'));

				$this->renderLayout();
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('powershare')->__('Item does not exist'));
				$this->_redirect('*/*/');
			}
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}

	public function newAction()
	{
		try
		{
			$this->_forward('edit');
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}

	public function saveAction()
	{
		try
		{
			if (($this->getRequest() == null) || ($this->getRequest()->getPost() == null))
			{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('powershare')->__('Unable to find item to save'));
					$this->_redirect('*/*/');
					return;
			}

			$data = $this->getRequest()->getPost();
			if (isset($data['product_categories']))
			{
				$data['category_ids'] = $data['product_categories'];
				unset($data['product_categories']);
			}
			
			$model = new Zizio_Powershare_Model_Powershare();
						
			// Fill model with POST data
			$model->setData($data);
			$model->setData('_id', $this->getRequest()->getParam('id'));

			// Save incentive in Zizio widgets site:
			if (!$model->save())
				throw new Exception("Failed to save PowerShare incentive in Zizio server - ".$model->getErrorMsg());

			// Add notifications
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('powershare')->__('Item was successfully saved'));
			Mage::getSingleton('adminhtml/session')->setFormData(false);

			if ($this->getRequest()->getParam('back'))
			{
				$this->_redirect('*/*/edit', array('id' => $model->getId()));
				return;
			}
			$this->_redirect('*/*/');
			return;
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);

			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
		}

		$this->_redirect('*/*/');
	}

	public function deleteAction()
	{
		try
		{
			if ($this->getRequest()->getParam('id'))
			{
				$model = new Zizio_Powershare_Model_Powershare();
				$model->setData('id', $this->getRequest()->getParam('id'));
				if ($model->delete())
					Mage::getSingleton('adminhtml/session')->
						addSuccess('Item was successfully deleted');
				else 
					Mage::getSingleton('adminhtml/session')->
						addError('Failed to delete Item - '.$model->getErrorMsg());
						
				$this->_redirect('*/*/');
			}
			$this->_redirect('*/*/');
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
		}
	}

    /**
     * Get categories fieldset block
     */
	
	private function _getSessionIncentive()
	{
        $id = $this->getRequest()->getParam('id');
	    $model = new Zizio_Powershare_Model_Powershare();
	    $session = Mage::getSingleton('adminhtml/session');
        
		$incentives = $session->getData('zizio_incentives');
		if (!$incentives)
			$incentives = array();
    	if (!isset($incentives[$id]))
    	{    	
			$model->load($id);
	    	$incentives[$id] = $model->getData();
			$session->setData('zizio_incentives', $incentives);
    	}
    	
    	return $incentives[$id];
	}
	
    public function categoriesAction()
    {
    	try
		{
			$incentive = $this->_getSessionIncentive();
	        $this->getResponse()->setBody(
	            $this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_categories_tree', "", $incentive)
	            	->toHtml()
	        );
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
    }
    
    public function categoriesJsonAction()
    {
    	try
		{
			$incentive = $this->_getSessionIncentive();
	        $this->getResponse()->setBody(
	            $this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_categories_tree', "", $incentive)
	                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
	        );
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
    }
    
    /**
     * Product selection grid (via ajax call)
     */

	public function gridProductAction()
	{
		try
		{
			$incentive = $this->_getSessionIncentive();
			$product_ids = $this->getRequest()->getParam('product_ids');
			if ($product_ids)
				$incentive['product_ids'] = explode(",", $product_ids);
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_product_grid', "", $incentive)
					->getHtml()
			);
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}
	
}
