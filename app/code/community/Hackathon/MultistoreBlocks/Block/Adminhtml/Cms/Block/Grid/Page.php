<?php

class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Page extends Mage_Adminhtml_Block_Cms_Page_Grid {

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
