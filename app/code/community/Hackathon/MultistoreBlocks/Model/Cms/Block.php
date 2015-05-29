<?php
/**
 * Cms block rewrite (add event prefix)
 *
 * @author Jeroen Boersma <jeroen@srcode.nl>
 * @author Willem Wigman <info@willemwigman.nl>
 * @author Peter Jaap Blaakmeer <peterjaap@elgentos.nl>
 */

/**
 * @package Hackathon_MultistoreBlocks
 * @category Hackathon
 */
class Hackathon_MultistoreBlocks_Model_Cms_Block extends Mage_Cms_Model_Block
{

    protected $_eventPrefix = 'cms_block';

}