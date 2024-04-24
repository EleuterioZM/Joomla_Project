<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$isEnabled = JPluginHelper::isEnabled('system', 'gridbox');
if (!$isEnabled) {
	echo JText::_('ENABLE_GRIDBOX_SYSTEM_PLUGIN');exit();
}
$app = JFactory::getApplication();
$view = $app->input->getCmd('view', 'page');
$app->input->set('view', $view);
JLoader::register('gridboxHelper', dirname(__FILE__) . '/helpers/gridbox.php');
include_once JPATH_ROOT.'/components/com_gridbox/helpers/assets.php';
gridboxHelper::setBreakpoints();
$controller = JControllerLegacy::getInstance('gridbox');
$controller->execute($app->input->getCmd('task', 'display'));
$controller->redirect();