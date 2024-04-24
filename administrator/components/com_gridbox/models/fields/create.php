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

class JFormFieldCreate extends JFormFieldList
{
    protected $type = 'create';
    
    protected function getInput()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $doc = JFactory::getDocument();
        if ($this->value === 0 || $this->value === '0') {
            $title = JText::_('PAGES');
        } else {
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__gridbox_app'))
                ->where($db->quoteName('id').' = '.(int)$this->value);
            $db->setQuery($query);
            $title = $db->loadResult();
        }
        if (!empty($title)) {
            $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        }
        $iframe = 'index.php?option=com_gridbox&view=pages&layout=apps&edit_type=create&tmpl=component';
        include JPATH_ROOT.'/administrator/components/com_gridbox/views/layouts/submit-new-item.php';
        $html = $out;
        $html .= gridboxHelper::renderBootstrapModal('gridbox-app-modal', 'APP', $iframe);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        
        return $html;
    }
}