<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$configured = !empty($options->key);
$icon = $configured ? 'zmdi zmdi-check-circle' : 'zmdi zmdi-alert-octagon';
$text = $configured ? JText::_('CONFIGURED') : JText::_('NOT_CONFIGURED');
$className = $options->service == 'facebook_login' || $options->service == 'google_login' ? 'ba-tooltip ba-bottom' : '';
?>
<div class="integrations-configuration-wrapper" data-configured="<?php echo intval($configured); ?>">
<?php
if (!empty($className)) {
?>
    <span class="ba-integrations-configuration-icon">
<?php
}
?>
    <i class="<?php echo $icon; ?>"></i>
    <span class="<?php echo $className; ?>"><?php echo $text; ?></span>
<?php
if (!empty($className)) {
?>
    </span>
<?php
}
?>
    <a class="default-action" target="_blank"
        href="<?php echo JUri::root(); ?>administrator/index.php?option=com_gridbox&view=integrations">
        <i class="zmdi zmdi-settings"></i>
        <span class="ba-tooltip ba-bottom"><?php echo JText::_('MANAGE_INTEGRATIONS'); ?></span> 
    </a>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();