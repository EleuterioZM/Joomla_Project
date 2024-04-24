<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class baformsModelSubmissions extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['id', 'title', 'date'];
        }
        parent::__construct($config);
    }

    public function getSubmissionForms()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('DISTINCT title')
            ->from('#__baforms_submissions');
        $db->setQuery($query);
        $data = $db->loadObjectList();

        return $data;
    }

    public function getSubmission()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $item = baformsHelper::getSubmission($id);

        return $item;
    }
    
    protected function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title, message, date_time, submission_state')
            ->from('#__baforms_submissions');
        $search = $this->getState('filter.search');
        $title = $this->getState('filter.title');
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        if (!empty($id)) {
            $query->where('id = '.$id);
        }
        if (!empty($search)) {
            $search = $db->quote('%'.$db->escape($search, true).'%', false);
            $query->where('(title LIKE '.$search.' OR message LIKE '.$search.')');
        }
        if (!empty($title) && $title != '*') {
            $query->where('title = '.$db->quote($title));
        }
        $orderCol = $this->state->get('list.ordering', 'title');
        $orderDirn = $this->state->get('list.direction', 'desc');
        if ($orderCol == 'ordering') {
            $orderCol = 'id';
        } else if ($orderCol == 'date') {
            $orderCol = 'date_time';
        }
        $query->order($db->quoteName($orderCol).' '.$orderDirn);
        
        return $query;
    }

    public function setFilters()
    {
        $this->populateState();
    }
    
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }
    
    public function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
        $this->setState('filter.state', $published);
        $title = $this->getUserStateFromRequest($this->context.'.filter.title', 'filter_title', '');
        $this->setState('filter.title', $title);
        
        parent::populateState('id', 'desc');
    }
   
}