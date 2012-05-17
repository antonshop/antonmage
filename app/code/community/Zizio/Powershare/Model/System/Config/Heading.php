<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 **
 *
 * NOTE BY ZIZIO
 * 
 * This code is copied (almost) as-is from magento 1.4.2.
 * It is here only for future-compatibility for older Magento versions 
 *
 */


/**
 * Renderer for sub-heading in fieldset
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zizio_Powershare_Model_System_Config_Heading
    extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $useContainerId = $element->getData('use_container_id');
        return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s" style="border-bottom: 1px solid #CCCCCC;">%s</h4></td></tr>',
            $element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
        );
    }
}
