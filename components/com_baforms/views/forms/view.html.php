<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla view library
jimport('joomla.application.component.view');
 

class baformsViewForms extends JViewLegacy
{
	protected $items;
    protected $pagination;
    protected $state;
    
    public function display($tpl = null) 
	{
        $this->items = $this->get('ModalItems');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}
        parent::display($tpl);
	}
}