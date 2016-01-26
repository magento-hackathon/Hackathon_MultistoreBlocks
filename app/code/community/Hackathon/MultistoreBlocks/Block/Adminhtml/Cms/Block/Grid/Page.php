<?php

if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitpermissions')) {
    class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Page_Abstract extends Aitoc_Aitpermissions_Block_Rewrite_AdminCmsPageGrid {};
} else {
    class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Page_Abstract extends Mage_Adminhtml_Block_Cms_Page_Grid {};
}

class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Page extends Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Page_Abstract {

    protected function _prepareColumns()
    {
        $this->addColumnAfter('content',array(
                'header' => Mage::helper('cms')->__('Content'),
                'align' => 'left',
                'index' => 'content',
                'renderer' => 'Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Content_Renderer_Page'
            )
            ,'identifier'
        );
        parent::_prepareColumns();
    }

}
