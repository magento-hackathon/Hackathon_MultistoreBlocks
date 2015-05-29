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

}
