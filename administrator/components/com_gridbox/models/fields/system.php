<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
JLoader::register('gridboxHelper', JPATH_ROOT.'/administrator/components/com_gridbox/helpers/gridbox.php');

class JFormFieldSystem extends JFormFieldList
{
    protected $type = 'system';
    
    protected function getInput()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $doc = JFactory::getDocument();
        $query = $db->getQuery(true)
            ->select($db->quoteName('title'))
            ->from($db->quoteName('#__gridbox_app'))
            ->where($db->quoteName('id').' = '.(int)$this->value);
        $db->setQuery($query);
        $title = $db->loadResult() ?? '';
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $iframe = 'index.php?option=com_gridbox&view=system&layout=modal&tmpl=component';
        include JPATH_ROOT.'/administrator/components/com_gridbox/views/layouts/submission-form-menu.php';
        $html = $out;
        $html .= gridboxHelper::renderBootstrapModal('gridbox-app-modal', 'PAGE', $iframe);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        
        return $html;
    }
}