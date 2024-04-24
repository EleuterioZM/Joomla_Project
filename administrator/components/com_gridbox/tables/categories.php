<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class gridboxTableCategories extends JTable
{
    public function __construct(&$db)
	{
		parent::__construct('#__gridbox_categories', 'id', $db);
	}

	public function check()
    {
        jimport('joomla.filter.output');
        if (empty($this->alias)) {
            $this->alias = $this->title;
        }
        $originAlias = $this->alias;
        $this->alias = $this->stringURLSafe(trim($this->alias));
        if (empty($this->alias)) {
            $this->alias = $originAlias;
            $this->alias = gridboxHelper::replace($this->alias);
            $this->alias = JFilterOutput::stringURLSafe($this->alias);
        }
        if (empty($this->alias)) {
            $this->alias = date('Y-m-d-H-i-s');
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_categories')
            ->where('`alias` = ' .$db->Quote($this->alias))
            ->where('`id` <> '.$this->id);
        $db->setQuery($query);
        $id = $db->loadResult();
        if (!empty($id)) {
            return false;
        }
        return true;
    }
}
