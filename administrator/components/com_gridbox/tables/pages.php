<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class gridboxTablePages extends JTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__gridbox_pages', 'id', $db);
    }

    public function check()
    {
        jimport('joomla.filter.output');
        if (empty($this->page_alias)) {
            $this->page_alias = $this->title;
        }
        $pk = $this->id;
        if (empty($pk)) {
            $pk = 0;
        }
        $originAlias = $this->page_alias;
        $this->page_alias = gridboxHelper::stringURLSafe(trim($this->page_alias));
        if (empty($this->page_alias)) {
            $this->page_alias = $originAlias;
            $this->page_alias = gridboxHelper::replace($this->page_alias);
            $this->page_alias = JFilterOutput::stringURLSafe($this->page_alias);
        }
        if (empty($this->page_alias)) {
            $this->page_alias = date('Y-m-d-H-i-s');
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__gridbox_pages')
            ->where('`page_alias` = ' .$db->Quote($this->page_alias))
            ->where('`id` <> '.$pk);
        $db->setQuery($query);
        $id = $db->loadResult();
        if (!empty($id)) {
            return false;
        }
        return true;
    }
}
