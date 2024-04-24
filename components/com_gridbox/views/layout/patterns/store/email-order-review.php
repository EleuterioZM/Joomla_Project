<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$taxes = array();
?>
<div>
<?php
foreach ($products as $product) {
    if (!isset($product->link)) {
        continue;
    }
    $image = (!empty($product->image) && !gridboxHelper::isExternal($product->image) ? JUri::root() : '').$product->image;
?>
    <div style="margin-top: 25px;margin-bottom: 25px; display: flex; overflow: hidden;">
<?php
    if (!empty($image)) {
?>
        <div style="margin-right: 25px;">
            <img src="<?php echo $image; ?>" style="width: 80px;">
        </div>
<?php
    }
?>
        <div style="width: 100%;">
            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                <div style="display: inline-block; flex-grow: 1;width: 100%;">
                    <span style="font-weight: bold;display: block;margin-bottom: 5px;"><?php echo $product->title; ?></span>
                </div>
            </div>
            <img src="<?php echo JUri::root(); ?>components/com_gridbox/assets/images/stars.png">
            <a href="<?php echo $product->link; ?>" style="display: block; background-color: #5092ff; width: 140px; border-radius: 3px; color: #fff; font-size: 12px; font-weight: bold;text-align: center; line-height: 30px; text-decoration: none; margin-top: 15px;"><?php echo JText::_('WRITE_REVIEW'); ?></a>
        </div>
    </div>
<?php
}
?>
</div>
<?php

$out = ob_get_contents();
ob_end_clean();