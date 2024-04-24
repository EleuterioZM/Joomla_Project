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

class gridboxControllerStoreSettings extends JControllerAdmin
{
    public function getModel($name = 'storesettings', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function getExchangerates()
    {
        $base = $this->input->get('base', '', 'string');
        $array = $this->input->get('symbols', [], 'array');
        $exchangerates = gridboxHelper::$storeHelper->getService('exchangerates');
        $json = gridboxHelper::$storeHelper->getAutoExchangerates($base, $array, new stdClass(), $exchangerates->key);
        $str = json_encode($json);
        echo $str;exit;
    }

    public function addCountry()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $obj = $model->addCountry();
        $str = json_encode($obj);
        echo $str;
        exit();
    }

    public function getCountries()
    {
        $model = $this->getModel();
        $array = $model->getCountries();
        $str = json_encode($array);
        echo $str;
        exit();
    }

    public function updateCountry()
    {
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'title');
        $model = $this->getModel();
        $model->updateCountry($id, $title);
        echo "{}";exit;
    }

    public function deleteCountry()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->deleteCountry($id);
        echo "{}";exit;
    }

    public function addState()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->addState($id);
        $str = json_encode($obj);
        echo $str;exit;
    }

    public function updateState()
    {
        $id = $this->input->get('id', 0, 'int');
        $title = $this->input->get('title', '', 'title');
        $model = $this->getModel();
        $model->updateState($id, $title);
        echo "{}";exit;
    }

    public function deleteState()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->deleteState($id);
        echo "{}";exit;
    }

    public function updateSettings()
    {
        gridboxHelper::checkUserEditLevel();
        $post = $this->input->post->getArray([]);
        $data = new stdClass();
        foreach ($post as $key => $value) {
            $data->{$key} = $this->input->get($key, '', 'raw');
        }
        foreach ($data as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $data->{$key} = json_decode($value);
        }
        $model = $this->getModel();
        $model->updateSettings($data);
        $obj = new stdClass();
        $obj->message = JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
        $str = json_encode($obj);
        echo $str;
        exit;
    }
}