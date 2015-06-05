<?php
/**
 * Multistoreblocks observer
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */

/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Model_Observer
{

    /**
     * Save multistore block content
     *
     * @param $observer
     */
    public function beforeSaveCmsBlock($observer)
    {
        if(!Mage::helper('hackathon_multistoreblocks')->isEnabled()) {
            // Not enabled
            return;
        }

        // Prevent function from calling itself
        if(Mage::registry('before_save_cms_block_prevent_loop')) {
            return;
        }
        Mage::register('before_save_cms_block_prevent_loop', true);

        $block = $observer->getEvent()->getDataObject();

        $multistoreContent = $block->getMultistoreContent();
        if (!$multistoreContent || !is_array($multistoreContent)) {
            // No multistore content
            return;
        }

        foreach($block->getMultistoreContent() as $key=>$content)
        {
            $isActive = $block->getMultistoreIsActive()[$key]; // upgrade to 5.5 instead of changing this, lazy bastard
            $stores = $block->getMultistoreStores()[$key];
            $existingId = $block->getMultistoreBlockId()[$key];

            $_block = Mage::getModel('cms/block');
            if($existingId) {
                $_block = $_block->load($existingId);
            } else {
                $existingId = null;
            }

            $_block->addData(array(
                'title' => $block->getTitle(),
                'identifier' => $block->getIdentifier(),
                'is_active' => $isActive,
                'stores' => $stores,
                'content' => $content
            ));

            try {
                if(!isset($firstBlock)) $firstBlock = $_block;
		        $_block->save();
		        //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('hackathon_multistoreblocks')->__('Block %s is saved.', $_block->getId()));
            } catch(Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        // Make sure the normal method also saves something instead of creating a new one
        $block->setData($firstBlock->getData());

    }

    public function loadAfterCmsBlock($observer)
    {
        if(!Mage::helper('hackathon_multistoreblocks')->isEnabled()) {
            return;
        }

        $block = $observer->getEvent()->getDataObject();
        if(
            $block->getId()
            && Mage::app()->getRequest()->getControllerName() == 'cms_block'
            && Mage::app()->getRequest()->getActionName() == 'edit'
        ) {
            $siblingBlocks = Mage::getModel('cms/block')->getCollection()
                ->addFieldToFilter('identifier', $block->getIdentifier())
                ->addFieldToFilter('block_id', array('neq' => $block->getId()));

            $resource = Mage::getModel('cms/resource_block');

            foreach($siblingBlocks as $siblingBlock) {
                $storeIds = $resource->lookupStoreIds($siblingBlock->getId());
                $siblingBlock->setStoreId($storeIds);
            }

            $block->setSiblingBlocks($siblingBlocks);
        }
    }

    public function cmsBlockDeleteCommitAfter($observer)
    {
        if(Mage::registry('hackathon_multistoreblocks_delete_blocks')) return;

        Mage::register('hackathon_multistoreblocks_delete_blocks', true);
        $block = $observer->getEvent()->getDataObject();

        $blocks = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('identifier',$block->getIdentifier())->addFieldToFilter('block_id', array('neq' => $block->getId()));
        foreach($blocks as $block) {
            $block->delete();
        }

    }

    public function adminhtmlWidgetContainerHtmlBefore($observer)
    {
        if(!Mage::app()->isSingleStoreMode())
        {
            return;
        }

        $editBlock = $observer->getEvent()->getBlock();

        $blockId = Mage::app()->getRequest()->getParam('block_id');
        if(!$blockId)
        {
            return;
        }

        if($editBlock instanceof Mage_Adminhtml_Block_Cms_Block_Edit)
        {
            $editBlock->addButton('duplicate', array(
                'label'     => Mage::helper('hackathon_multistoreblocks')->__('Duplicate block'),
                'onclick'   => "duplicateBlock()"
            ), 0, 15);
        }
    }

}
