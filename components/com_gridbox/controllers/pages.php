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


class gridboxControllerPages extends JControllerForm
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function setCookie()
	{
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $key = $input->get('key', '', 'string');
		$value = $input->get('value', '', 'string');
		setcookie($key, $value, time()+7200);
		exit;
	}

	public function getGalleryCategories()
    {
        gridboxHelper::checkUserEditLevel();
        $input = JFactory::getApplication()->input;
        $id = $input->get('gallery', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, settings')
            ->from('#__bagallery_category')
            ->where('`form_id` = '.$id)
            ->order('orders ASC');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        print_r(json_encode($result));
        exit;
    }
}