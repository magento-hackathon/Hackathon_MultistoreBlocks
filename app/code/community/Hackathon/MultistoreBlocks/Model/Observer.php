<?php

class Hackathon_MultistoreBlocks_Model_Observer
{

    public function beforeSaveCmsBlock($observer)
    {
        // Prevent function from calling itself
        if(Mage::registry('before_save_cms_block_prevent_loop')) return;
        Mage::register('before_save_cms_block_prevent_loop', true);

        /*
         * array (size=9)
          0 => string 'key' (length=3)
          1 => string 'back' (length=4)
          2 => string 'form_key' (length=8)
          3 => string 'block_id' (length=8)
          4 => string 'title' (length=5)
          5 => string 'identifier' (length=10)
          6 => string 'stores' (length=6)
          7 => string 'is_active' (length=9)
          8 => string 'content' (length=7)
         */

        $block = $observer->getEvent()->getDataObject();
        //Zend_Debug::dump($block);exit;
        /*foreach($block->getContent() as $storeId=>$content)
        {
            $status = $block->getStatus()[$storeId]; // upgrade to 5.5 instead of changing this, lazy bastard
            $stores = $block->getStores();
        }*/
    }

    public function loadAfterCmsBlock($observer)
    {
        $block = $observer->getEvent()->getDataObject();
        if(
            Mage::app()->getRequest()->getControllerName() == 'cms_block'
            && Mage::app()->getRequest()->getActionName() == 'edit'
        ) {
            
        }
    }

}