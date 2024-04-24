<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewAssociations extends JViewLegacy
{
    public $apps;
    public $pages;
    public $count;
    public $about;
    public $params;
    public $link;

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
        $this->about = gridboxHelper::aboutUs();
        $this->apps = $this->get('Apps');
        $this->pages = $this->get('Pages');
        $this->count = $this->get('PageCount');
        $this->params = $this->get('Params');
        $this->link = "index.php?option=com_gridbox&view=associations&associate=".
            $this->params->language."&type=".$this->params->type."&tmpl=component";
        $doc = JFactory::getDocument();
        $doc->addScript('components/com_gridbox/assets/js/ba-associations.js');
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-style-editor.css?'.$this->about->version);
        parent::display($tpl);
    }

    public function drawCategoryList($items, $id)
    {
        $str = '<ul>';
        $href = $this->link."&app=".$id."&category=";
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