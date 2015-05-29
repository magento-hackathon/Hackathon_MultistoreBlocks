<?php

class Hackathon_MultistoreBlocks_Model_Observer
{
    public function __construct() {
        if(!Mage::helper('hackathon_multistoreblocks')->isEnabled()) {
            return;
        }
    }

    public function beforeSaveCmsBlock($observer)
    {
        // Prevent function from calling itself
        if(Mage::registry('before_save_cms_block_prevent_loop')) {
            return;
        }
        Mage::register('before_save_cms_block_prevent_loop', true);

        $block = $observer->getEvent()->getDataObject();
        return;

        foreach($block->getContent() as $key=>$content)
        {
            $isActive = $block->getIsActive()[$key]; // upgrade to 5.5 instead of changing this, lazy bastard
            $storeIds = $block->getStoreId()[$key];
            $existingId = $block->getExistingId[$key];

            foreach($storeIds as $storeId)
            {
                $_block = Mage::getModel('cms/block')->setStoreId($storeId)->load($block->getIdentifier());
                if($existingId)
                {

                }
                $_block->setTitle($block->getTitle());
                $_block->setIdentifier($block->getIdentifier());
                $_block->setIsActive($isActive);
                $_block->setStores($storeIds);
                $_block->setContent($content);
                $_block->save();
            }
        }
    }

    public function loadAfterCmsBlock($observer)
    {
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
                $siblingBlock->setStoreIds($storeIds);
            }

            $block->setSiblingBlocks($siblingBlocks);
        }
    }

}