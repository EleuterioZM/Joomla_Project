<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

abstract class gridboxHelperAssociation
{
    public static function getEditorAssociations($id, $edit_type, $languages)
    {
        $url = 'index.php?option=com_gridbox&view=editor&tmpl=component';
        if ($edit_type == '') {
            $type = 'page';
        } else if ($edit_type == 'blog' || $edit_type == 'post-layout') {
            $type = 'app';
            $url .= '&edit_type='.$edit_type;
        } else if ($edit_type == 'system') {
            $type = 'system';
            $url .= '&edit_type=system';
        }
        if (empty($type)) {
            return [];
        }
        $items = self::getItems($id, $type);
        $array = [];
        $data = [];
        $url .= '&id=';
        foreach ($items as $item) {
            $array[$item->language] = $url.$item->id;
        }
        foreach ($languages as $key => $language) {
            if (isset($array[$key])) {
                $obj = new stdClass();
                $obj->link = $array[$key];
                $obj->title = $language;
                $obj->language = $key;
                $data[] = $obj;
            }
        }

        return $data;
    }

    protected static function getItems($id, $type)
    {
        $db = JFactory::getDbo();
        if ($type == 'page') {
            $table = '#__gridbox_pages';
        } else if ($type == 'app') {
            $table = '#__gridbox_app';
        } else if ($type == 'category') {
            $table = '#__gridbox_categories';
        } else if ($type == 'tag') {
            $table = '#__gridbox_tags';
        } else if ($type == 'system') {
            $table = '#__gridbox_system_pages';
        }
        $query = $db->getQuery(true)
            ->select('hash')
            ->from('#__gridbox_associations')
            ->where('item_id = '.$id)
            ->where('item_type = '.$db->quote($type));
        $db->setQuery($query);
        $hash = $db->loadResult();
        $query = $db->getQuery(true)
            ->select('a.item_id AS id, p.title, p.language')
            ->from('#__gridbox_associations AS a')
            ->where('a.hash = '.$db->quote($hash))
            ->where('a.item_type = '.$db->quote($type))
            ->where('a.item_id <> '.$id)
            ->leftJoin($table.' AS p ON p.id = a.item_id')
            ->where('p.language <> '.$db->quote('*'));
        if ($type == 'category') {
            $query->select('p.app_id');
        } else if ($type == 'page') {
            $query->select('p.app_id, p.page_category');
        }
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }

    public static function getPageAppType($id)
    {
        if ($id == 0) {
            return 'single';
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('type')
            ->from('#__gridbox_app')
            ->where('id = '.$id);
        $db->setQuery($query);
        $type = $db->loadResult();

        return $type;
    }

    public static function getAssociations()
    {
        $input = JFactory::getApplication()->input;
        $view = $input->get('view', '', 'string');
        $id = $input->getInt('id', 0, 'int');
        $app_id = $input->getInt('app', 0, 'int');
        $tag_id = $input->getInt('tag', 0, 'int');
        $author_id = $input->getInt('author', 0, 'int');
        $return = [];
        $link = 'index.php?option=com_gridbox&view=';
        if ($view == 'page' && !empty($id)) {
            $items = self::getItems($id, 'page');
            foreach ($items as $item) {
                $type = self::getPageAppType($item->app_id);
                $category = $item->page_category;
                $return[$item->language] = gridboxHelper::getGridboxPageLinks($item->id, $type, $item->app_id, $category)
                    .'&lang='.$item->language;
            }
        } else if ($view == 'blog' && $id == 0 && !empty($app_id) && empty($tag_id) && empty($author_id)) {
            $items = self::getItems($app_id, 'app');
            foreach ($items as $item) {
                $return[$item->language] = $link.'blog&app='.$item->id.'&id=0&lang='.$item->language;
            }
        } else if ($view == 'blog' && !empty($id) && !empty($app_id) && empty($tag_id) && empty($author_id)) {
            $items = self::getItems($id, 'category');
            foreach ($items as $item) {
                $return[$item->language] = gridboxHelper::getGridboxCategoryLinks($item->id, $item->app_id)
                    .'&lang='.$item->language;
            }
        } else if ($view == 'blog' && !empty($app_id) && !empty($tag_id)) {
            $items = self::getItems($tag_id, 'tag');
            $apps = self::getItems($app_id, 'app');
            $apps_list = [];
            foreach ($apps as $item) {
                $apps_list[$item->language] = $item->id;
            }
            foreach ($items as $item) {
                if (empty($apps_list[$item->language])) {
                    continue;
                }
                $app_id = $apps_list[$item->language];
                $return[$item->language] = gridboxHelper::getGridboxTagLinks($item->id, $app_id).'&lang='.$item->language;
            }
        } else if ($view == 'system' && !empty($id)) {
            $items = self::getItems($id, 'system');
            foreach ($items as $item) {
                $return[$item->language] = $link.'system&id='.$item->id.'&lang='.$item->language;
            }
        }

        return $return;
    }
}
