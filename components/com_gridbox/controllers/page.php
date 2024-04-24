<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');


class gridboxControllerPage extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

    public function getItemsFilterCount()
    {
        $input = JFactory::getApplication()->input;
        $app = $input->get('app', 0, 'int');
        $data = $input->get('data', '', 'raw');
        $object = json_decode($data);
        $count = gridboxHelper::getItemsFilterCount($app, $object);
        print_r($count);
        exit;
    }

	public function getRecentPosts()
    {
        $input = JFactory::getApplication()->input;
        $input->set('view', 'page');
        $id = $input->get('id', 0, 'int');
        $sorting = $input->get('sorting', '', 'string');
        $limit = $input->get('limit', 0, 'int');
        $maximum = $input->get('maximum', 0, 'int');
        $category = $input->get('category', '', 'string');
        $tags = $input->get('tags', '', 'string');
        $type = $input->get('type', '', 'string');
        $featured = $input->get('featured', false, 'bool');
        $pagination = $input->get('pagination', '', 'string');
        $item = $input->get('item', '{}', 'raw');
        $start = $input->get('page', 1, 'int');
        $not = $input->get('not', '', 'string');
        $start--;
        gridboxHelper::$editItem = json_decode($item);
        $obj = new stdClass();
		$obj->pagination = gridboxHelper::getRecentPostsPagination($id, $limit, $category, $featured, $start, $pagination, $type, $tags);
        $start *= $limit;
        $obj->posts = gridboxHelper::getRecentPosts($id, $sorting, $limit, $maximum, $category, $featured, $start, $not, $type, $tags);
        $str = json_encode($obj);
        echo $str;exit;
    }
}