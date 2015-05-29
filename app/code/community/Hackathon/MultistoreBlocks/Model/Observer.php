<?php

class Hackathon_MultistoreBlocks_Model_Observer
{

    public function beforeSaveCmsBlock($observer)
    {
        Mage::log('test',null,'events.log',true);
        Zend_Debug::dump(array_keys($observer->getData()));exit;
    }

}