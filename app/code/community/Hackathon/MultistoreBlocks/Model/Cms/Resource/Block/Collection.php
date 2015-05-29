<?php
/**
 * Rework to correct count by identifier
 */

/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Model_Cms_Resource_Block_Collection
    extends Mage_Cms_Model_Resource_Block_Collection {

    /**
     * Get Helper
     *
     * @return Hackathon_MultistoreBlocks_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('hackathon_multistoreblocks');
    }

    /**
     * Get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();

        if (!Mage::app()->getStore()->isAdmin() || !$this->_getHelper()->isEnabled()) {
            return $select;
        }

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('count(distinct identifier)');

        return $select;
    }

}