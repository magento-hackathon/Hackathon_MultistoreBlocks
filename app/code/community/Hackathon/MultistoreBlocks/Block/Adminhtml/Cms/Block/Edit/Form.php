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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml cms block edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Edit_Form extends Mage_Adminhtml_Block_Cms_Block_Edit_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_form');
        $this->setTitle(Mage::helper('cms')->__('Block Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('cms_block');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('block_');

        $baseFieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('General Information'), 'class' => 'fieldset-wide'));

        $baseFieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('cms')->__('Block Title'),
            'title'     => Mage::helper('cms')->__('Block Title'),
            'required'  => true,
            'value'     => $model->getTitle(),
        ));

        $baseFieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => Mage::helper('cms')->__('Identifier'),
            'title'     => Mage::helper('cms')->__('Identifier'),
            'required'  => true,
            'class'     => 'validate-xml-identifier',
            'value'     => $model->getIdentifier(),
        ));

		$tabbedFieldset = $form->addFieldset('tabbed_fieldset', array('legend'=>Mage::helper('cms')->__('Block Content'), 'class' => 'fieldset-wide'));

            
		$this->setTab($model, $tabbedFieldset);
		
		$siblingBlocks = $model->getSiblingBlocks();

		foreach($siblingBlocks as $block){
            $this->setTab($block, $tabbedFieldset);
		}
	
        //$form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        //return parent::_prepareForm();
    }

    protected function setTab($block, $fieldset){
    
        if (!$block->getId()) {
            $block->setData('is_active', '1');
            $block_id = 0;
        } else {
            $block_id = $block->getId();
        }
        
        $field =$fieldset->addField('store_id['.$block_id.']', 'multiselect', array(
            'name'      => 'stores['.$block_id.'][]',
            'label'     => Mage::helper('cms')->__('Store View'),
            'title'     => Mage::helper('cms')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'value'     => $block->getStoreId(),
        ));
        if ($block->getBlockId()) {
            $fieldset->addField('multistore_block_id['.$block_id.']', 'hidden', array(
                'name' => 'multistore_block_id['.$block_id.']',
                'value'=> $block_id,
            ));
        }
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);
    
        $fieldset->addField('is_active['.$block_id.']', 'select', array(
            'label'     => Mage::helper('cms')->__('Status'),
            'title'     => Mage::helper('cms')->__('Status'),
            'name'      => 'is_active['.$block_id.']',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '0' => Mage::helper('cms')->__('Disabled'),
            ),
            'value'     => $block->getData('is_active'),
        ));


        $fieldset->addField('content['.$block_id.']', 'editor', array(
            'name'      => 'content['.$block_id.']',
            'label'     => Mage::helper('cms')->__('Content'),
            'title'     => Mage::helper('cms')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'value'     => $block->getContent(),
        ));
    }
}
