<?php

class Zizio_Powershare_Adminhtml_ZizioRegisterController extends Zizio_Powershare_Model_Adminhtml_BaseController
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
		// mark registration as prompted
		Mage::getSingleton('admin/session')->setDataZizioPowershareRegisterPrompt(true);

		return $this->registerAction();
	}

	public function createAction()
	{
		try
		{
			$this->_initAction();

			$block = $this->getLayout()->createBlock('powershare/adminhtml_Register_Create');
			$content_block = $this->getLayout()->getBlock('content');
			$content_block->append($block);

			$this->renderLayout();
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}

	public function loginAction()
	{
		try
		{
			$this->_initAction();

			$block = $this->getLayout()->createBlock('powershare/adminhtml_Register_Login');
			$content_block = $this->getLayout()->getBlock('content');
			$content_block->append($block);

			$this->renderLayout();
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}

	public function registerAction()
	{
		try
		{
			$session = Mage::getSingleton('admin/session');
			$data = $this->getRequest()->getPost();
			if ($data && isset($data['username']) && isset($data['password']))
			{
				$session->setZizioUsername($data['username']);
				$session->setZizioPassword($data['password']);
			}

			if (!$session->hasZizioUsername() || !$session->hasZizioPassword())
				return $this->createAction();

			$this->_initAction();

			$block = $this->getLayout()->createBlock('powershare/adminhtml_Register_Register');
			$content_block = $this->getLayout()->getBlock('content');
			$content_block->append($block);

			$this->renderLayout();
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
			if ($data = $this->getRequest()->getPost())
			{
				$res = Zizio_Powershare_Helper_Data::Register($data);
				////SendUrl catch
				if (!$res) {
					return $this->registerAction();
				}

			}
			$this->_redirect("powershare/adminhtml_powershare");
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}
}
