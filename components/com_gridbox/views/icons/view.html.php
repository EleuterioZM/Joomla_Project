<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxViewIcons extends JViewLegacy
{
    public $items;

    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
            gridboxHelper::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
        }
        $this->items = $this->get('Item');
        $app = JFactory::getApplication();
        $input = JFactory::getApplication()->input;
        $doc = JFactory::getDocument();
        $doc->setTitle('Gridbox Editor');
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
        } else {
            $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
        }
        $doc->addScript(JUri::root().'/components/com_gridbox/libraries/bootstrap/bootstrap.js');
        $doc->addScript(JURI::root() . 'components/com_gridbox/assets/js/ba-icons.js');
        $doc->addStyleSheet(JURI::root() . 'components/com_gridbox/assets/css/ba-style-editor.css');
        
        parent::display($tpl);
    }
}