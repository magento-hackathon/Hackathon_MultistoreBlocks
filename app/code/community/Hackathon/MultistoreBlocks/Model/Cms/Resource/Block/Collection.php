<?php
/**
 * Rework to correct count by identifier
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
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

    /**
     * To Option Array with store names
     * 
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField='id', $labelField='name', $additional=array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
                /* Addition to add store names in dropdowns in the backend */
                if ($field == $labelField) {
                    if ($item->getStoreNames()) {
                        $data[$code] .= ' (' . $item->getStoreNames() . ')';
                    }
                }
            }
            $res[] = $data;
        }
        return $res;
    }

}