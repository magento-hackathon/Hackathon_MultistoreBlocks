<?php
/**
 * Rework to group by identifier
 */

/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid extends Mage_Adminhtml_Block_Cms_Block_Grid
{

    /**
     * Prepare collection and group by
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cms/block')->getCollection();
        /** @var $collection Mage_Cms_Model_Resource_Block_Collection */

        $this->setCollection($collection);
        // Group by identifier
        $collection->getSelect()->group('identifier');

        // Skip parent to force grouping
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /**
     * Add all store ids
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();
        /** @var $collection Mage_Cms_Model_Resource_Block_Collection */

        $connection = $collection->getConnection();

        // Add store ids
        foreach ($collection as $cmsBlock) {
            /** @var $cmsBlock Hackathon_MultistoreBlocks_Model_Block */

            $select = $connection->select();

            $select
                ->from(array('cb' => $collection->getTable('cms/block')), array())
                ->join(array('cbs' => $collection->getTable('cms/block_store')), 'cb.block_id = cbs.block_id', array('store_id'))
                ->where('cb.identifier = ?', $cmsBlock->getData('identifier'))
                ->distinct();

            $storeIds = $connection->fetchCol($select);

            $cmsBlock->setStoreId($storeIds)
                ->setStores($storeIds);
        }

        return $this;
    }

}
