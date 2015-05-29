<?php
/**
 * Rework to group by identifier
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */

/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid extends Mage_Adminhtml_Block_Cms_Block_Grid
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
     * Prepare collection and group by
     * @return $this
     */
    protected function _prepareCollection()
    {
        if (!$this->_getHelper()->isEnabled()) {
            return parent::_prepareCollection();
        }

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
        if (!$this->_getHelper()->isEnabled()) {
            return parent::_afterLoadCollection();
        }

        $collection = $this->getCollection();
        /** @var $collection Mage_Cms_Model_Resource_Block_Collection */

        $connection = $collection->getConnection();

        // Add store ids
        foreach ($collection as $cmsBlock) {
            /** @var $cmsBlock Hackathon_MultistoreBlocks_Model_Block */

            $select = $connection->select();

            $select
                ->from(array('cb' => $collection->getTable('cms/block')), array('is_active'))
                ->join(array('cbs' => $collection->getTable('cms/block_store')), 'cb.block_id = cbs.block_id', array('store_id'))
                ->where('cb.identifier = ?', $cmsBlock->getData('identifier'))
                ->distinct();

            $storeIdData = $connection->fetchAll($select);

            $storeIds = array();
            $activeStores = array();
            foreach ($storeIdData as $storeIdRow) {
                $storeIds[] = $storeIdRow['store_id'];
                $activeStores[$storeIdRow['store_id']] = $storeIdRow['is_active'];
            }

            // Be sure to have only one record per store
            $storeIds = array_unique($storeIds);

            $cmsBlock->setStoreId($storeIds)
                ->setStores($storeIds)
                ->setActiveStores($activeStores);
        }

        return $this;
    }

    /**
     * Remove status column
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        if (!$this->_getHelper()->isEnabled()) {
            return parent::_prepareColumns();
        }

        parent::_prepareColumns();

        // Removed is_active
        $this->removeColumn('is_active')
            ->removeColumn('store_id');

        // Readd stores
        $this->addColumnAfter('store_id', array(
            'header'        => Mage::helper('cms')->__('Store View'),
            'index'         => 'store_id',
            'type'          => 'store',
            'store_all'     => true,
            'store_view'    => true,
            'sortable'      => false,
            'renderer'      => 'Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Renderer_Store',
            'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
        ), 'identifier');

        // Fix order
        $this->sortColumnsByOrder();

        return $this;
    }

}
