<?php

class Hackathon_MultistoreBlocks_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled() {
        if(Mage::app()->isSingleStoreMode()) false;
        return true;
    }
}