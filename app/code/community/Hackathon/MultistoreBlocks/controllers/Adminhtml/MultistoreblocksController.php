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

    /**
     * Delete action
     * @author     Wouter Steenmeijer wouter@elgentos.nl
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('block_id')) {
            try {
                $model = Mage::getModel('cms/block')->setId($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The block has been deleted.'));

                //@TODO: redirect to edit page if you delete the block id you edit but more with the same identifier still exists
                // go to edit form
                $this->_redirectReferer();

                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirectReferer();
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a block to delete.'));
        // go to grid
        $this->_redirect('*/cms_block');
    }

}