<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;

class gridboxModelStoreSettings extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'title', 'published', 'state', 'order_list'
            );
        }
        parent::__construct($config);
    }

    public function getIntegrations()
    {
        $db = JFactory::getDbo();
        $array = [$db->quote('exchangerates_data'), $db->quote('exchangerates'), $db->quote('google_login'), $db->quote('facebook_login')];
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_api')
            ->where('service IN ('.implode(',', $array).')');
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $integrations = new stdClass();
        foreach ($array as $obj) {
            $integrations->{$obj->service} = $obj;
        }

        return $integrations;
    }

    public function getForm()
    {
        $form = JForm::getInstance('gridbox', JPATH_COMPONENT.'/models/forms/gridbox.xml');
        
        return $form;
    }

    public function addCountry()
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = 'Country';
        $db->insertObject('#__gridbox_countries', $obj);
        $obj->id = $db->insertid();
        $obj->states = new stdClass();

        return $obj;
    }

    public function getCountries()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_countries');
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach ($list as $obj) {
            $query = $db->getQuery(true)
                ->select('*')
                ->where('country_id = '.$obj->id)
                ->from('#__gridbox_country_states');
            $db->setQuery($query);
            $array = $db->loadObjectList();
            $obj->states = new stdClass();
            foreach ($array as $state) {
                $obj->states->{$state->id} = $state;
            }
        }

        return $list;
    }

    public function updateCountry($id, $title)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = $title;
        $obj->id = $id;
        $db->updateObject('#__gridbox_countries', $obj, 'id');
    }

    public function deleteCountry($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_countries')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_country_states')
            ->where('country_id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function addState($id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = 'State';
        $obj->country_id = $id;
        $db->insertObject('#__gridbox_country_states', $obj);
        $obj->id = $db->insertid();

        return $obj;
    }

    public function updateState($id, $title)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->title = $title;
        $obj->id = $id;
        $db->updateObject('#__gridbox_country_states', $obj, 'id');
    }

    public function deleteState($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__gridbox_country_states')
            ->where('id = '.$id);
        $db->setQuery($query)
            ->execute();
    }

    public function updateSettings($data)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->id = $data->id;
        unset($data->id);
        $obj->key = json_encode($data);
        $db->updateObject('#__gridbox_api', $obj, 'id');
        foreach ($data->notifications as $notification) {
            if (isset($notification->delay) && isset($notification->key) && !$notification->delay->enabled) {
                $query = $db->getQuery(true)
                    ->delete('#__gridbox_email_delay')
                    ->where('notification = '.$db->quote($notification->key));
                $db->setQuery($query)
                    ->execute();
            }
        }
    }

    public function setGridboxFilters()
    {
        $app = JFactory::getApplication();
        $ordering = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', null);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', null);
        gridboxHelper::setGridboxFilters($ordering, $direction, $this->context);
    }

    public function getGridboxFilters()
    {
        $array = gridboxHelper::getGridboxFilters($this->context);
        if (!empty($array)) {
            foreach ($array as $obj) {
                $name = str_replace($this->context.'.', '', $obj->name);
                $this->setState($name, $obj->value);
            }
        }
    }

    public function getCustomerInfo()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_customer_info')
            ->order('order_list ASC');
        $db->setQuery($query);
        $info = $db->loadObjectList();

        return $info;
    }

    public function setFilters()
    {
        $this->setGridboxFilters();
        $this::populateState();
    }

    public function getLanguages()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('lang_code, title')
            ->from('#__languages')
            ->where('published >= 0')
            ->order('title');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $languages = [];
        $languages['*'] = JText::_('JALL');
        foreach ($items as $key => $value) {
            $languages[$value->lang_code] = $value->title;
        }

        return $languages;
    }
    
    protected function getListQuery()
    {
        $this->getGridboxFilters();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_api')
            ->where('`service` = '.$db->quote('store'));
        
        return $query;
    }
    
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        return parent::getStoreId($id);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);
        parent::populateState('id', 'desc');
    }
}