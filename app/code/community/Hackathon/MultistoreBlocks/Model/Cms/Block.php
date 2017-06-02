<?php
/**
 * Cms block rewrite (add event prefix)
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */

/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Model_Cms_Block extends Mage_Cms_Model_Block
{

    protected $_eventPrefix = 'cms_block';

    /**
     * @return array|string
     */
    public function getStoreNames()
    {
        $resource = Mage::getModel('core/resource');
        $db = $resource->getConnection('core_write');

        if  (!$this->getId()) {
            return [];
        }

        $storeNames = $db->fetchCol($db->select()->from($resource->getTableName('core_store'), 'name')->joinInner($resource->getTableName('cms_block_store'), $resource->getTableName('cms_block_store') . '.store_id = ' . $resource->getTableName('core_store') . '.store_id')->where('block_id = ?', $this->getId()));

        $storeNames = array_map(function ($input) {
            if ($input == Mage::helper('cms')->__('Admin')) {
                return Mage::helper('cms')->__('All');
            } else {
                return $input;
            }
        }, $storeNames);

        return implode(', ', $storeNames);
    }

}