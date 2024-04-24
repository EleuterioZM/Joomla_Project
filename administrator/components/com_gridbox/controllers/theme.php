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

class gridboxControllerTheme extends JControllerForm
{
    public function getModel($name = 'theme', $prefix = 'gridboxModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
    
    public function updateParams()
    {
        gridboxHelper::checkUserEditLevel();
        $model = $this->getModel();
        $model->updateParams();
    }
}