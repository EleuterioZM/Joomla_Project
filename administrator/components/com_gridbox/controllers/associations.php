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


class gridboxControllerAssociations extends JControllerForm
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
        $key = $this->input->get('key', '', 'string');
        $value = $this->input->get('value', '', 'string');
        setcookie($key, $value, time()+7200);
        exit;
    }

    public function getLinks()
    {
        $id = $this->input->get('id', 0, 'int');
        $type = $this->input->get('type', '', 'string');
        $model = $this->getModel();
        $data = $model->getLinks($id, $type);
        $str = json_encode($data);
        echo $str;
        exit;
    }

    public function saveLinks()
    {
        if (JLanguageAssociations::isEnabled()) {
            $id = $this->input->get('id', 0, 'int');
            $type = $this->input->get('type', '', 'string');
            $items = $this->input->get('items', [], 'array');
            $model = $this->getModel();
            $model->saveLinks($id, $type, $items);
        }
        exit;
    }
}