<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');


class gridboxControllerSelectLink extends JControllerForm
{
    public function getModel($name = 'SelectLink', $prefix = 'gridboxModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
    
    public function getString()
    {
        gridboxHelper::checkUserEditLevel();
		$model = $this->getModel();
    	$menus = $model->getMenus();
    	$gridbox = $model->getGridbox();
    	$str = '<ul><li><span><i class="zmdi zmdi-folder"></i>Gridbox';
    	$str .= '</span><i class="zmdi zmdi-chevron-right"></i><ul>';
    	$str .= $this->getGridboxHtml($gridbox);
    	$str .= '</ul></li><li><span><i class="zmdi zmdi-folder"></i>'.JText::_('MENU');
    	$str .= '</span><i class="zmdi zmdi-chevron-right"></i><ul>';
    	$str .= $this->getMenusHtml($menus).'</ul></li></ul>';
    	echo $str;
    	exit;
    }

    public function getGridboxHtml($obj)
    {
        gridboxHelper::checkUserEditLevel();
        $str = '';
        foreach ($obj as $value) {
            $str .= '<li';
            if (isset($value->link)) {
                $str .= ' data-url="'.$value->link.'"';
            }
            $str .= '><span><i class="zmdi zmdi-folder"></i>'.$value->title.'</span>';
            if (!empty($value->childs) || !empty($value->pages)) {
                $str .= '<i class="zmdi zmdi-chevron-right"></i><ul>';
                if (!empty($value->childs)) {
                    $str .= $this->getCategoriesHtml($value->childs);
                }
                if (isset($value->pages)) {
                    $str .= $this->getPagesHtml($value->pages);
                }
                $str .= '</ul>';
            }
            $str .= '</li>';
        }

        return $str;
    }

    public function getPagesHtml($obj)
    {
        gridboxHelper::checkUserEditLevel();
        $str = '';
        foreach ($obj as $value) {
            $str .= '<li';
            $str .= ' data-url="'.$value->link.'"';
            $str .= '><span><i class="zmdi zmdi-file"></i>'.$value->title.'</span>';
            $str .= '</li>';
        }

        return $str;
    }

    public function getCategoriesHtml($obj)
    {
        gridboxHelper::checkUserEditLevel();
        $str = '';
        foreach ($obj as $value) {
            $str .= '<li';
            $str .= ' data-url="'.$value->link.'"';
            $str .= '><span><i class="zmdi zmdi-folder"></i>'.$value->title.'</span>';
            if (!empty($value->childs) || !empty($value->pages)) {
                $str .= '<i class="zmdi zmdi-chevron-right"></i><ul>';
                $str .= $this->getCategoriesHtml($value->childs);
                $str .= $this->getPagesHtml($value->pages);
                $str .= '</ul>';
            }
            $str .= '</li>';
        }

        return $str;
    }

    public function getMenusHtml($obj)
    {
        gridboxHelper::checkUserEditLevel();
        $str = '';
        foreach ($obj as $value) {
            $str .= '<li';
            if (isset($value->id) && !empty($value->link)) {
                $str .= ' data-url="'.$value->link.'&Itemid='.$value->id.'"';
            }
            $str .= '><span><i class="zmdi zmdi-';
            if (!empty($value->childs)) {
                $str .= 'folder';
            } else {
                $str .= 'file';
            }
            $str .= '"></i>'.$value->title.'</span>';
            if (!empty($value->childs)) {
                $str .= '<i class="zmdi zmdi-chevron-right"></i><ul>';
                $str .= $this->getMenusHtml($value->childs);
                $str .= '</ul>';
            }
            $str .= '</li>';
        }

        return $str;
    }
}