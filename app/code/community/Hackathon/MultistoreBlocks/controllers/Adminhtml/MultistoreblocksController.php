<?php
class Hackathon_MultistoreBlocks_Adminhtml_MultistoreblocksController extends Mage_Adminhtml_Controller_Action
{
    const DEFAULT_DELETE_ACTION_FAILED_MESSAGE = 'Unable to find a block to delete.';
    
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
        if (!  $this->getRequest()->has('block_id')) {
            // display error message & redirect back to grid listing blocks
            return $this->deleteActionFailed();
        }

        try {
            // Load the block with the bare minimum we need (block_id and its identifier)
            $cmsBlockCollection = Mage::getModel('cms/block')->getCollection()
                ->addFieldToFilter('block_id', $this->getRequest()->getParam('block_id'));
            $cmsBlockCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(['block_id', 'identifier']);

            /** @var Mage_Cms_Model_Block $model */
            $model = $cmsBlockCollection->getFirstItem();

            // Block doesn't even exist
            if (! $model->hasData('block_id')) {
                return $this->deleteActionFailed();
            }

            $oldBlockIdentifier = $model->getIdentifier();

            // Delete and push success message
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The block has been deleted.'));

            // Try to get any sibling block (only the block_id) with same identifier, so we can redirect back to edit page
            $cmsBlockCollection = Mage::getModel('cms/block')->getCollection()
                ->addFieldToFilter('identifier', $oldBlockIdentifier)
                ->setOrder('block_id', Varien_Data_Collection::SORT_ORDER_ASC)
                ->setPageSize(1);
            $cmsBlockCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(['block_id']);

            /** @var Mage_Cms_Model_Block $model */
            $model = $cmsBlockCollection->getFirstItem();

            if (! $model->hasData('block_id')) {
                // No sibling block found, return to grid listing
                return $this->_redirect('*/cms_block');
            }

            // Back to edit page if there is a block with same identifier
            return $this->_redirect('*/cms_block/edit', ['block_id' => $model->getData('block_id')]);
        } catch (Exception $e) {
            return $this->deleteActionFailed($e->getMessage());
        }
    }

    /**
     * Push error message and redirect back to grid listing blocks
     */
    protected function deleteActionFailed($deleteMessage = '')
    {
        if (empty($deleteMessage)) {
            $deleteMessage = self::DEFAULT_DELETE_ACTION_FAILED_MESSAGE;
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__($deleteMessage));
        $this->_redirect('*/cms_block');
    }
}
