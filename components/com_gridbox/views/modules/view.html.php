<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewModules extends JViewLegacy
{
    public $items;
    public $positions;
    public $types;

    public function display ($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
            gridboxHelper::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
        }
        if (count($errors = $this->get('Errors'))) {
            gridboxHelper::raiseError(500, implode('<br />', $errors));
            return false;
        }
        $this->items = $this->get('Items');
        $this->positions = array();
        $this->types = array();
        $this->positions[''] = JText::_('SELECT_POSITION');
        $this->types[''] = JText::_('SELECT_TYPE');
        $filters = $this->get('Filters');
        foreach ($filters as $item) {
            if (!empty($item->position) && !in_array($item->position, $this->positions)) {
                $this->positions[$item->position] = $item->position;
            }
            $module = str_replace('mod_custom', 'Custom HTML', $item->module);
            if (!in_array($module, $this->types)) {
                $this->types[$item->module] = $module;
            }
        }
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('//fonts.googleapis.com/css?family=Roboto:300,400,500,700');
        $doc->setTitle('Gridbox Editor');
        $doc->addScript(JURI::root() . 'components/com_gridbox/assets/js/ba-modules.js');
        parent::display($tpl);
    }
}