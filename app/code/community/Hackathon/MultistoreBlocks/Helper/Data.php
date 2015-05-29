<?php
/**
 * Multistoreview helper
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */


/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Tell if the store is in single mode
     *
     * @return bool
     */
    public function isEnabled() {

        if(Mage::app()->isSingleStoreMode()) {
            return false;
        }

        return true;
    }

}
