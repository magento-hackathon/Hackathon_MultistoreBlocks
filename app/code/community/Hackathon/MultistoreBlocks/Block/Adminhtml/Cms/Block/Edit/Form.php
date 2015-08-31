<?php
/**
 * Multistoreview admin edit form
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */


/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Edit_Form
    extends Mage_Adminhtml_Block_Cms_Block_Edit_Form
{

    /**
     * Get helper
     *
     * @return Hackathon_MultistoreBlocks_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('hackathon_multistoreblocks');
    }

    /**
     * Build form based on multiblockdata
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        if (!$this->_getHelper()->isEnabled()) {
            return parent::_prepareForm();
        }

        $model = Mage::registry('cms_block');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('block_');

        $baseFieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>Mage::helper('cms')->__('General Information'),
            'class' => 'fieldset-wide')
        );

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


        $jumps = array('Jump to:');

        $storeNames = $this->getStoreNames($model->getStoreId());
        $primaryFieldset = $form->addFieldset('tabbed_fieldset_0', array(
        	'legend'=>Mage::helper('cms')->__('Block Content for:').' ' . $storeNames,
        	'class' => 'fieldset-wide'
        ));
        $jumps[] = '<a href="javascript:$(\'block_tabbed_fieldset_' . $model->getId() . '\').scrollTo()">' . $storeNames .'</a>';

        $this->setTab($model, $primaryFieldset, $form);
		
		$siblingBlocks = $model->getSiblingBlocks();

        if(!is_object($siblingBlocks)) $siblingBlocks = array();
		foreach($siblingBlocks as $block){
            $this->setTab($block, null, $form);
            $storeNames = $this->getStoreNames($block->getStoreId());
            $jumps[] = '<a href="javascript:$(\'block_tabbed_fieldset_' . $block->getId() . '\').scrollTo();void(0)">' . $storeNames .'</a>';
		}

        if($model->getId()) {
            $baseFieldset->addField('jump', 'note', array(
                'text' => implode('<br />', $jumps)
            ));
        }
	
        $form->setUseContainer(true);
        $this->setForm($form);
    }

    protected function setTab($block, $fieldset, $form){

        if(!$fieldset){

            $fieldset = $form->addFieldset(
                'tabbed_fieldset_'.$block->getId(),
                array(
                    'legend'=>Mage::helper('cms')->__('Block Content for: ').' ' . $this->getStoreNames($block->getStoreId()),
                    'class' => 'fieldset-wide'
                )
            );
        }
    
        if (!$block->getId()) {
            $block->setData('is_active', '1');
            $block_id = 0;
        } else {
            $block_id = $block->getId();
        }
        
        $field =$fieldset->addField('multistore_store_id['.$block_id.']', 'multiselect', array(
            'name'      => 'multistore_stores['.$block_id.'][]',
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
    
        $fieldset->addField('multistore_is_active['.$block_id.']', 'select', array(
            'label'     => Mage::helper('cms')->__('Status'),
            'title'     => Mage::helper('cms')->__('Status'),
            'name'      => 'multistore_is_active['.$block_id.']',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '0' => Mage::helper('cms')->__('Disabled'),
            ),
            'value'     => $block->getData('is_active'),
        ));


        $fieldset->addField('multistore_content_'.$block_id.'', 'editor', array(
            'name'      => 'multistore_content['.$block_id.']',
            'label'     => Mage::helper('cms')->__('Content'),
            'title'     => Mage::helper('cms')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'value'     => $block->getContent(),
        ));
    }

    protected function getStoreNames($store_ids)
    {
        if(!is_array($store_ids)) $store_ids = array();
        $storeNames = array();
        foreach($store_ids as $store_id){
            if($store_id == 0){
                $storeNames[] = Mage::helper('cms')->__('All Store Views');
            } else {
                $storeNames[] = Mage::app()->getStore($store_id)->getName();
            }
        }

        $returnValue = implode(', ',$storeNames);

        return $returnValue;
    }

}
