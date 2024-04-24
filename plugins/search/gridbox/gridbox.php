<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


defined('_JEXEC') or die;

/**
 * Content search plugin.
 *
 * @since  1.6
 */
class PlgSearchGridbox extends JPlugin
{
    public function onContentSearchAreas()
    {
        static $areas = array(
            'gridbox' => 'Gridbox Pages'
        );

        return $areas;
    }

    protected function getHref($id)
    {
        $url = 'index.php?option=com_gridbox&view=page&id='.$id;
        $app = JFactory::getApplication();
        $menus = $app->getMenu('site');
        $component = JComponentHelper::getComponent('com_gridbox');
        $attributes = array('component_id');
        $values = array($component->id);
        $items = $menus->getItems($attributes, $values);
        $itemId = null;
        foreach ($items as $item) {
            if (isset($item->query) && isset($item->query['view'])) {
                if ($item->query['view'] == 'page' && $item->query['id'] == $id) {
                    $itemId = '&Itemid=' . $item->id;
                    break;
                }
            }
        }
        if (!$itemId) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.id, a.type, p.page_category')
                ->from('#__gridbox_pages AS p')
                ->where('p.id = '.$id)
                ->leftJoin('`#__gridbox_app` AS a ON p.app_id = a.id');
            $db->setQuery($query);
            $app = $db->loadObject();
            if (!empty($app->type) && $app->type != 'single') {
                $url = 'index.php?option=com_gridbox&view=page&blog='.$app->id;
                $url .= '&category='.$app->page_category.'&id='.$id;
                foreach ($items as $value) {
                    if (isset($value->query) && isset($value->query['id']) && isset($value->query['app'])
                        && $value->query['view'] == 'blog' && $value->query['app'] == $app->id
                        && $value->query['id'] == $app->page_category) {
                        $itemId = '&Itemid='.$value->id;
                        break;
                    }
                }
                if (empty($itemId)) {
                    foreach ($items as $value) {
                        if (isset($value->query) && isset($value->query['id']) && isset($value->query['app'])
                            && $value->query['view'] == 'blog' && $value->query['app'] == $app->id
                            && $value->query['id'] == 0) {
                            $itemId = '&Itemid='.$value->id;
                            break;
                        }
                    }
                }
                if (empty($itemId)) {
                    foreach ($items as $value) {
                        if (isset($value->query) && isset($value->query['id']) && isset($value->query['app']) &&
                            $value->query['view'] == 'blog' && $value->query['app'] == $app->id) {
                            $itemId = '&Itemid='.$value->id;
                            break;
                        }
                    }
                }
            }
        }
        if ($itemId) {
            foreach ($items as $item) {
                if ($item->home == 1) {
                    $itemId = '&Itemid='.$item->id;
                    break;
                }
            }
        }
        $url .= $itemId;

        return $url;
    }

    public function getPagesFieldsWeare($search)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('distinct option_key')
            ->from('#__gridbox_fields_data')
            ->where('value LIKE '.$db->quote('%'.$db->escape($search, true).'%'));
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $array = array();
        foreach ($result as $value) {
            $array[] = $value->option_key;
        }
        $where = '(pf.value LIKE '.$db->quote('%'.$db->escape($search, true).'%');
        if (!empty($array)) {
            $subStr = implode(', ', $array);
            $where .= ' OR pf.value in ('.$subStr.')';
        }
        if ($time = strtotime($search)) {
            $dateStr = date('Y-m-d', $time);
            $where .= ' OR pf.value LIKE '.$db->quote('%'.$db->escape($dateStr, true).'%', false);
        }
        $where .= ')';
        $query = $db->getQuery(true)
            ->select('distinct p.id')
            ->from('`#__gridbox_pages` AS p')
            ->leftJoin('`#__gridbox_page_fields` AS pf ON pf.page_id = p.id')
            ->where($where)
            ->where('pf.field_type <> '.$db->quote('field-google-maps'))
            ->where('pf.field_type <> '.$db->quote('field-simple-gallery'))
            ->where('pf.field_type <> '.$db->quote('product-gallery'))
            ->where('pf.field_type <> '.$db->quote('field-slideshow'))
            ->where('pf.field_type <> '.$db->quote('product-slideshow'))
            ->where('pf.field_type <> '.$db->quote('field-video'))
            ->where('pf.field_type <> '.$db->quote('image-field'))
            ->where('pf.field_type <> '.$db->quote('file'));
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $array = array();
        foreach ($result as $value) {
            $array[] = $value->id;
        }
        $subStr = implode(', ', $array);
        $result = !empty($subStr) ? 'id in ('.$subStr.')' : '';

        return $result;
    }

    public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        JLoader::register('gridboxHelper', JPATH_ROOT.'/components/com_gridbox/helpers/gridbox.php');
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $tag = JFactory::getLanguage()->getTag();
        $searchText = $text;
        if (is_array($areas)) {
            if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
                return array();
            }
        }
        $limit = $this->params->def('search_limit', 50);
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $text = trim($text);
        if ($text == '') {
            return array();
        }
        switch ($phrase) {
            case 'exact':
                $search = $db->quote('%'.$db->escape($text, true).'%', false);
                $fields = $this->getPagesFieldsWeare($text);
                $where = array();
                $where[] = 'title LIKE '.$search;
                $where[] = 'params LIKE '.$search;
                if (!empty($fields)) {
                    $where[] = $fields;
                }
                $where = '('.implode(') OR (', $where).')';
                break;
            case 'all':
            case 'any':
            default:
                $words = explode(' ', $text);
                $wheres = array();
                foreach ($words as $word) {
                    $search = $db->quote('%' . $db->escape($word, true) . '%', false);
                    $fields = $this->getPagesFieldsWeare($word);
                    $where = array();
                    $where[] = 'LOWER(title) LIKE LOWER('.$search.')';
                    $where[] = 'LOWER(params) LIKE LOWER('.$search.')';
                    if (!empty($fields)) {
                        $where[] = $fields;
                    }
                    $wheres[] = implode(' OR ', $where);
                }
                $where = '('.implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
                break;
        }
        switch ($ordering) {
            case 'oldest':
                $order = 'p.created ASC';
                break;
            case 'popular':
                $order = 'p.hits DESC';
                break;
            case 'alpha':
                $order = 'p.title ASC';
                break;
            case 'category':
                $order = 'p.created DESC';
                break;
            case 'newest':
            default:
                $order = 'p.created DESC';
                break;
        }
        $rows = array();
        $query = $db->getQuery(true);
        if ($limit > 0) {
            $query->select('p.id, p.title, p.params AS text, p.created, \'2\' AS browsernav, p.app_id')
                ->from('#__gridbox_pages AS p')
                ->where('('.$where.')')
                ->where('p.page_category <> '.$db->quote('trashed'))
                ->where('p.published = 1')
                ->where('p.created <= '.$db->quote($date))
                ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
                ->where('p.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')')
                ->where('p.page_access in ('.$groups.')')
                ->order($order);
            $db->setQuery($query, 0, $limit);
            try {
                $list = $db->loadObjectList();
            } catch (RuntimeException $e) {
                $list = array();
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
            }
            $limit -= count($list);
            if (isset($list)) {
                error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
                include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
                foreach ($list as $key => $item) {
                    $item->text = gridboxHelper::checkMainMenu($item->text);
                    $doc = phpQuery::newDocument($item->text);
                    $search = '.ba-edit-item, .ba-box-model, .empty-item, .column-info, .ba-column-resizer,';
                    $search .= ' .ba-edit-wrapper, .empty-list, .ba-item-main-menu > .ba-menu-wrapper > .main-menu > .add-new-item';
                    pq($search)->remove();
                    $text = $doc->htmlOuter();
                    $text = strip_tags($text);
                    if (trim($text) == 'Click Here And Start Typing') {
                        $text = '';
                    }
                    $list[$key]->text = $text;
                    
                    $list[$key]->href = $this->getHref($item->id);
                    $list[$key]->section = '';
                }
            }
            $rows[] = $list;
        }
        $results = array();
        if (count($rows)) {
            foreach ($rows as $row) {
                $new_row = array();
                foreach ($row as $value) {
                    $new_row[] = $value;
                }
                $results = array_merge($results, (array) $new_row);
            }
        }

        return $results;
    }
}
