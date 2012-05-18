<?php
class Anton_Fbreferral_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
	public function responseAction(){
		
		/* Getting the cart products */
		$checkoutsession = Mage::getSingleton('checkout/session');
		
		$productsid = array();
		foreach($checkoutsession->getQuote()->getAllItems() as $item){
			$productsid[] = $item;
		}
		$r = new ReflectionClass($checkoutsession);
		print_r($r->getMethods());
		exit;
	}
}