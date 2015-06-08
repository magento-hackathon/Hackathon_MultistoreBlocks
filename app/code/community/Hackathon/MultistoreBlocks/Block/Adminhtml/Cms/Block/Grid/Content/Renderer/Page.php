<?php

class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Content_Renderer_Page extends Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Content_Renderer_Abstract
{

    public function __construct() {
        parent::__construct();
        $session = Mage::getSingleton('adminhtml/session');
        $filter = $session->getData('cmsPageGridfilter');
        $this->filter = $filter;
    }

}