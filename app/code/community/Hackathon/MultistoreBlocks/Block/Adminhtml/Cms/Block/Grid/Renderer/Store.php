<?php
/**
 * Multistoreview store renderer
 * Adds active and default store view in grid view
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */


/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Renderer_Store
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
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
     * Render row store views
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if (!$this->_getHelper()->isEnabled()) {
            return parent::_prepareCollection();
        }

        $out = '';
        $skipEmptyStoresLabel = $this->_getShowEmptyStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());

        // Get active store data
        $activeStores = $row->getActiveStores();

        if (is_null($origStores) && $row->getStoreName()) {
            $scopes = array();
            foreach (explode("\n", $row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat('&nbsp;', $k * 3) . $label;
            }
            $out .= implode('<br/>', $scopes) . $this->__(' [deleted]');
            return $out;
        }

        if (empty($origStores) && !$skipEmptyStoresLabel) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }

        if (empty($origStores)) {
            return '';
        }
        elseif (in_array(0, $origStores)) {
            $out .= Mage::helper('adminhtml')->__('All Store Views') . ' (' .
                ($activeStores[0] ? Mage::helper('adminhtml')->__('Active') :
                    Mage::helper('adminhtml')->__('Inactive')) . ')<br />';
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $out .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
                foreach ($group['children'] as $storeId => $store) {
                    $out .= str_repeat('&nbsp;', 6) . $store['label'] . ' (' .
                        ($activeStores[$storeId] ? Mage::helper('adminhtml')->__('Active') :
                            Mage::helper('adminhtml')->__('Inactive')) . ')<br/>';
                }
            }
        }

        return $out;
    }

}
