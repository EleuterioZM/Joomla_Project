<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = false) 
	{
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', 'dashboard'));

        parent::display($cachable);
    }
}