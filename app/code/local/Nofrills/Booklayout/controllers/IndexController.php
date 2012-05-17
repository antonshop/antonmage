<?php
class Nofrills_Booklayout_IndexController  extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
// 		START
// 		$block = new Mage_Core_Block_Text();
// 		$block->setText("Hello World");
// 		echo $block->toHtml();	
//		END

//		START
// 		$block = new Mage_Core_Block_Template();
// 		$block->setTemplate('helloworld.phtml');
// 		var_dump($block->getTemplateFile());
// 		echo $block->toHtml();			
//		END

//		START
// 		$paragraph_block = new Mage_Core_Block_Text();
// 		$paragraph_block->setText('One paragraph to rule them all.');
// 		
// 		
// 		$main_block = new Mage_Core_Block_Template();
// 		$main_block->setTemplate('helloworld.phtml');
// 		
// 		$main_block->setChild('the_first',$paragraph_block);
// 		echo $main_block->toHtml();	
//		END

		$block_1 = new Mage_Core_Block_Text();
		$block_1->setText('Original Text');

		$block_2 = new Mage_Core_Block_Text();
		$block_2->setText('The second sentance.');		
		
		$main_block = new Mage_Core_Block_Template();
		$main_block->setTemplate('helloworld.phtml');
		
		$main_block->setChild('the_first'	,$block_1);
		$main_block->setChild('the_second'	,$block_2);
		
		$block_1->setText('Wait, I want this text instead.');
		echo $main_block->toHtml();	

	}
	
	public function helloblockAction()
	{
// 		$block_1 = new Mage_Core_Block_Text();
// 		$block_1->setText('The first sentance. ');
// 
// 		$block_2 = new Mage_Core_Block_Text();
// 		$block_2->setText('The second sentance. ');		
// 	
// 		$main_block = new Nofrills_Booklayout_Block_Helloworld();
// 		// $main_block->setTemplate('helloworld.phtml');			
// 		
// 		$main_block->setChild('the_first',$block_1);
// 		$main_block->setChild('the_second',$block_2);
// 		
// 		echo $main_block->toHtml();

		$main_block = new Nofrills_Booklayout_Block_Helloworld();			
		echo $main_block->toHtml();
	}	
	
	#http://magento.example.com/nofrills_booklayout/index/layout
	public function layoutAction()
	{
// 		$layout = Mage::getSingleton('core/layout');				
// 		$block = $layout->createBlock('core/template','root');
// 		$block->setTemplate('helloworld-2.phtml');
// 		echo $block->toHtml();

// 		$layout = Mage::getSingleton('core/layout');				
// 		$block = $layout->createBlock('nofrills_booklayout/helloworld','root');			
// 		echo $block->toHtml();
		
		//START
// 		$layout = Mage::getSingleton('core/layout');				
// 		$block = $layout->createBlock('nofrills_booklayout/helloworld','root');			
// 
// 		$layout->addOutputBlock('root');
// 		$layout->setDirectOutput(true);
// 		$layout->getOutput();		
		//END
		
		$layout = Mage::getSingleton('core/layout');				
		$block = $layout->createBlock('nofrills_booklayout/helloworld','root');			
		echo $layout->addOutputBlock('root')->setDirectOutput(false)->getOutput();		
	}	
}