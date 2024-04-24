<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filter.output');

class gridboxControllerTrashed extends JControllerAdmin
{
    public function getModel($name = 'gridbox', $prefix = 'gridboxModel', $config = array()) 
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function getCategories()
    {
        gridboxHelper::checkUserEditLevel();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title, type')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->order($db->escape('id ASC'));
        $db->setQuery($query);
        $obj = new stdClass();
        $obj->id = 0;
        $obj->title = JText::_('PAGES');
        $obj->type = 'single';
        $items = array($obj);
        $apps = $db->loadObjectList();
        $items = array_merge($items, $apps);
        foreach ($items as $item) {
            $query = $db->getQuery(true)
                ->select('id, title')
                ->from('#__gridbox_categories')
                ->where('parent = 0')
                ->where('app_id = ' .$item->id);
            $db->setQuery($query);
            $item->categories = $db->loadObjectList();
            foreach ($item->categories as $value) {
                $value->child = $this->getAllChild($value, $item->id);
            }
        }
        $result = json_encode($items);
        echo $result;
        exit;
    }

    protected function getAllChild($parent, $id)
    {
        gridboxHelper::checkUserEditLevel();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_categories')
            ->where('`app_id` = '.$id)
            ->where('`parent` = '.$parent->id);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        foreach ($items as $key => $value) {
            $value->child = $this->getAllChild($value, $id);
        }

        return $items;
    }

    public function contextDelete()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $assets = new gridboxAssetsHelper($id, 'page');
        $flag = $assets->checkPermission('core.delete');
        if ($flag) {
            $array = [];
            $array[] = $id;
            $model = $this->getModel();
            $model->delete($array);
            gridboxHelper::afterDeleteAction($array);
            gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }

    public function delete()
    {
        $cid = JFactory::getApplication()->input->get('cid', [], 'array');
        $str = JFactory::getApplication()->input->get('types', '', 'string');
        $types = explode(', ', $str);
        $flag = true;
        $user = JFactory::getUser();
        $pages = [];
        $system = [];
        foreach ($cid as $key => $id) {
            $type = $types[$key];
            if ($type == 'system') {
                $system[] = $id;
                $flag = $user->authorise('core.delete', 'com_gridbox');
            } else {
                $pages[] = $id;
                $assets = new gridboxAssetsHelper($id, 'page');
                $flag = $assets->checkPermission('core.delete');
            }
            if (!$flag) {
                break;
            }
        }
        if ($flag) {
            $model = $this->getModel();
            $model->delete($pages);
            $model = $this->getModel('system');
            $model->delete($system);
            gridboxHelper::afterDeleteAction($pages);
            gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_DELETED');
        } else {
            gridboxHelper::ajaxReload('JERROR_CORE_DELETE_NOT_PERMITTED');
        }
    }

    public function restoreSingle()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $category = $input->get('category_id', '', 'string');
        $model = $this->getModel();
        $model->moveSingle($id, $category);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_RESTORED');
    }

    public function restoreBlog()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('context-item', 0, 'int');
        $category = $input->get('category_id', '', 'string');
        $obj = json_decode($category);
        $model = $this->getModel($name = 'category', $prefix = 'gridboxModel', $config = array());
        $model->pageMoveTo($obj->id, $id, $obj->app_id);
        gridboxHelper::ajaxReload('COM_GRIDBOX_N_ITEMS_RESTORED');
    }
}