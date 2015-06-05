<?php

class Hackathon_MultistoreBlocks_Adminhtml_MultistoreblocksController extends Mage_Adminhtml_Controller_Action
{

    public function duplicateAction() {
        $data = $this->getRequest()->getParams();
        $block = Mage::getModel('cms/block')->addData($data)->unsetData('block_id');
        try {
            $block->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('hackathon_multistoreblocks')->__('Block successfully duplicated.'));
            $this->_redirect('*/cms_block/edit/block_id/' . $block->getId());
        } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hackathon_multistoreblocks')->__('Could not save block; %s', $e->getMessage()));
            $this->_redirectReferer();
        }
    }

}