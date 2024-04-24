<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

JLoader::register('baformsHelper', JPATH_ROOT.'/components/com_baforms/helpers/baforms.php');
baformshelper::prepareHelper();
$params = JComponentHelper::getParams('com_baforms');
if (!defined('UPLOADS_STORAGE')) {
    define('UPLOADS_STORAGE', $params->get('uploads_storage', 'images/baforms/uploads'));
}
if (!defined('PDF_STORAGE')) {
    define('PDF_STORAGE', $params->get('pdf_storage', 'images/baforms/pdf'));
}
if (!defined('SIGNATURE_STORAGE')) {
    define('SIGNATURE_STORAGE', 'images/baforms/signatures');
}
$controller = JControllerLegacy::getInstance('baforms');
$controller->execute(JFactory::getApplication()->input->getCmd('task', 'display'));
$controller->redirect();