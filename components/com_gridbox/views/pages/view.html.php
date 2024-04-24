<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewPages extends JViewLegacy
{
    public $apps;
    public $pages;
    public $count;

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
        $this->apps = $this->get('Apps');
        $this->pages = $this->get('Pages');
        $this->count = $this->get('PageCount');
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('//fonts.googleapis.com/css?family=Roboto:300,400,500,700');
        $doc->setTitle('Gridbox Editor');
        $doc->addScript(JURI::root() . 'components/com_gridbox/assets/js/ba-pages.js');
        parent::display($tpl);
    }

    public function drawCategoryList($items, $id)
    {
        $str = '<ul>';
        $href = "index.php?option=com_gridbox&view=pages&tmpl=component&app=".$id."&category=";
        foreach ($items as $key => $item) {
            $str.= '<li><a href="'.$href.$item->id.'"><i class="zmdi zmdi-folder"></i>'.$item->title.'</a>';
            if (count($item->child) > 0) {
                $str .= '<i class="zmdi zmdi-chevron-right ba-icon-md"></i>';
                $str .= $this->drawCategoryList($item->child, $id);
            }
            $str .= '</li>';
        }
        $str .= '</ul>';

        return $str;
    }
}