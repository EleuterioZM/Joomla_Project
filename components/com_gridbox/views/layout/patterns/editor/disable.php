<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div class="ba-settings-group disable-settings-group">
    <div class="settings-group-title">
        <i class="zmdi zmdi-eye"></i>
        <span><?php echo JText::_('DISABLE_ON'); ?></span>
    </div>
    <div class="ba-settings-toolbar">
        <label data-option="disable" data-group="desktop" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-desktop-windows"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('DESKTOP'); ?>
            </span>
        </label>
        <label data-option="disable" data-group="laptop" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-laptop-mac"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('LAPTOP'); ?>
            </span>
        </label>
        <label data-option="disable" data-group="tablet" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-tablet"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('TABLET_LANDSCAPE'); ?>
            </span>
        </label>
        <label data-option="disable" data-group="tablet-portrait" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-tablet-mac"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('TABLET_PORTRAIT'); ?>
            </span>
        </label>
        <label data-option="disable" data-group="phone" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-smartphone-landscape"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('PHONE_LANDSCAPE'); ?>
            </span>
        </label>
        <label data-option="disable" data-group="phone-portrait" data-value="1" data-callback="sectionRules">
            <i class="zmdi zmdi-smartphone-android"></i>
            <span class="ba-tooltip">
                <?php echo JText::_('PHONE_PORTRAIT'); ?>
            </span>
        </label>
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();