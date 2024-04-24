<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_baforms')) {
    throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}
JLoader::register('baformsHelper', JPATH_ROOT.'/administrator/components/com_baforms/helpers/baforms.php');
JLoader::register('compatibleCheck', JPATH_ROOT.'/components/com_baforms/helpers/compatibleCheck.php');
JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_baforms/helpers/html');
baformsHelper::prepareCustomPayments();
$params = JComponentHelper::getParams('com_media');
define('IMAGE_PATH', $params->get('file_path', 'images'));
$params = JComponentHelper::getParams('com_baforms');
define('UPLOADS_STORAGE', $params->get('uploads_storage', 'images/baforms/uploads'));
define('PDF_STORAGE', $params->get('pdf_storage', 'images/baforms/pdf'));
$controller = JControllerLegacy::getInstance('baforms');
$controller->execute(JFactory::getApplication()->input->getCmd('task', 'display'));
$controller->redirect();