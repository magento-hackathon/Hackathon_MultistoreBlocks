<?php

class Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Grid_Content_Renderer_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $value = strip_tags($value);
        $strLength = 100;
        if($this->filter) {
            parse_str(base64_decode($this->filter), $data);
        }
        if(isset($data['identifier'])) {
            $string = $data['identifier'];
            $end = strstr($value, $string);
            $start = strstr($value, $string, true);
            $combined = '&hellip;' . substr($start,-$strLength/2) . substr($end,0,$strLength/2) . '&hellip;';
            $combinedWithHighlighting = str_replace($string,'<span style="color:black;background-color:yellow">'.$string.'</span>',$combined);
            return $combinedWithHighlighting;
        } else {
            return substr($value, 0, $strLength) . '&hellip;';
        }

    }
}