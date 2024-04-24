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

class gridboxControllerIntegrations extends JControllerAdmin
{
    public function getModel($name = 'integrations', $prefix = 'gridboxModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function update()
    {
        gridboxHelper::checkUserEditLevel();
        $post = $this->input->post->getArray([]);
        $data = (object)$post;
        $model = $this->getModel();
        $model->update($data);
        $obj = new stdClass();
        $obj->message = JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
        $str = json_encode($obj);
        echo $str;
        exit;
    }

    public function getOptions()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->getOptions($id);
        $str = json_encode($obj);
        echo $str;
        exit;
    }
}