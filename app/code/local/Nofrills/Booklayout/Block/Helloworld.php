<?php
class Nofrills_Booklayout_Block_Helloworld extends Mage_Core_Block_Template
{
	public function _construct()
	{
		$this->setTemplate('helloworld.phtml');
		return parent::_construct();
	}
	
	public function _beforeToHtml()
	{
		$block_1 = new Mage_Core_Block_Text();
		$block_1->setText('The first sentance. ');
		$this->setChild('the_first', $block_1);		

		$block_2 = new Mage_Core_Block_Text();
		$block_2->setText('The second sentance. ');		
		$this->setChild('the_second', $block_2);		
	}
		public function fetchTitle()
		{
			return 'Hello Fancy World';
		}	
}	
