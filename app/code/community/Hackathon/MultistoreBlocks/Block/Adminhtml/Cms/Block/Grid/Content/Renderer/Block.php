<?php

class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Content_Renderer_Block extends Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Content_Renderer_Abstract
{
    public function __construct() {
        parent::__construct();
        $session = Mage::getSingleton('adminhtml/session');
        $filter = $session->getData('cmsBlockGridfilter');
        $this->filter = $filter;
    }
}