<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class gridboxRouter extends JComponentRouterView
{
    public function build(&$query)
    {
        $segments = [];
        $menu = null;
        if (!isset($query['Itemid']) || empty($query['Itemid'])) {
            $id = gridboxHelper::getDefaultMenuItem();
            if (!empty($id)) {
                $query['Itemid'] = $id;
            }
        }
        if (isset($query['Itemid']) && !empty($query['Itemid'])) {
            $menus = JFactory::getApplication()->getMenu('site');
            $menu = $menus->getItem($query['Itemid']);
            $viewFlag = $menu->query['option'] == 'com_gridbox' && isset($query['view']) &&
                ($query['view'] == 'page' || $query['view'] == 'blog' || $query['view'] == 'system');
            $idFlag = isset($query['id']) && isset($menu->query['id']) && $query['id'] == $menu->query['id']
                && !isset($query['tag']) && !isset($menu->query['tag']);
            $tagFlag = isset($query['tag']) && isset($menu->query['tag']) && $query['tag'] == $menu->query['tag'];
            if ($viewFlag && $menu->query['view'] == $query['view'] && ($idFlag || $tagFlag) && !isset($query['author'])) {
                unset($query['view']);
                if (isset($query['blog'])) {
                    unset($query['blog']);
                }
                if (isset($query['category'])) {
                    unset($query['category']);
                }
                if (isset($query['app'])) {
                    unset($query['app']);
                }
                if (isset($query['id'])) {
                    unset($query['id']);
                }
                if ($tagFlag && isset($query['tag'])) {
                    unset($query['tag']);
                }
            } else if ($viewFlag && $menu->query['view'] == 'blog' && (isset($query['tag']) || isset($query['author']))
                && $menu->query['app'] == $query['app']) {
                if (isset($query['app'])) {
                    unset($query['app']);
                }
                if (isset($query['id'])) {
                    unset($query['id']);
                }
            }
        }
        if (isset($query['view']) && ($query['view'] == 'page' || $query['view'] == 'blog')) {
            if (isset($query['view'])) {
                unset($query['view']);
            }
            if (isset($query['tag']) && isset($query['app'])) {
                $db = JFactory::getDbo();
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_app')
                    ->where('type <> '.$db->quote('system_apps'))
                    ->where('id = '.$query['app']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                $segments[] = $alias;
                unset($query['app']);
                unset($query['id']);
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_tags')
                    ->where('`id` = '.$query['tag']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                $segments[] = 'tag';
                $segments[] = $alias;
                unset($query['tag']);
            }
            if (isset($query['author']) && isset($query['app'])) {
                $db = JFactory::getDbo();
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_app')
                    ->where('type <> '.$db->quote('system_apps'))
                    ->where('id = '.$query['app']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                $segments[] = $alias;
                unset($query['app']);
                unset($query['id']);
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_authors')
                    ->where('`id` = '.$query['author']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                $segments[] = 'author';
                $segments[] = $alias;
                unset($query['author']);
            }
            if (isset($query['app']) && !empty($query['id'])) {
                unset($query['app']);
                $array = gridboxHelper::getCategoryPath($query['id']);
                $path = array_reverse($array);
                foreach ($path as $key => $value) {
                    $segments[] = $value;
                }
                unset($query['id']);
            } else if (isset($query['app']) && empty($query['id'])) {
                $db = JFactory::getDbo();
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_app')
                    ->where('type <> '.$db->quote('system_apps'))
                    ->where('`id` = '.$query['app']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                unset($query['app']);
                unset($query['id']);
                $segments[] = $alias;
            }
            if (isset($query['blog'])) {
                unset($query['blog']);
            }
            $hasCategory = false;
            if (isset($query['category'])) {
                $id = $query['category'];
                $array = gridboxHelper::getCategoryPath($id);
                $path = array_reverse($array);
                foreach ($path as $key => $value) {
                    $segments[] = $value;
                }
                $hasCategory = true;
                unset($query['category']);
            }
            if (isset($query['id'])) {
                $id = $query['id'];
                if (!empty($id)) {
                    $db = JFactory::getDbo();
                    $q = $db->getQuery(true)
                        ->select('page_alias, page_category')
                        ->from('#__gridbox_pages')
                        ->where('`id` = '.$id);
                    $db->setQuery($q);
                    $obj = $db->loadObject();
                    if (!empty($obj->page_category) && !$hasCategory && !$obj->page_category != 'trashed') {
                        $array = gridboxHelper::getCategoryPath($obj->page_category);
                        $path = array_reverse($array);
                        foreach ($path as $key => $value) {
                            $segments[] = $value;
                        }
                    }
                    $query['id'] = $obj->page_alias;
                }
                $segments[] = $query['id'];
                unset($query['id']);
            }
            if (isset($query['tag'])) {
                $db = JFactory::getDbo();
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_tags')
                    ->where('`id` = '.$query['tag']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                $segments[] = 'tag';
                $segments[] = $alias;
                unset($query['tag']);
            }
            if (isset($query['author'])) {
                $db = JFactory::getDbo();
                $q = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__gridbox_authors')
                    ->where('`id` = '.$query['author']);
                $db->setQuery($q);
                $alias = $db->loadResult();
                $segments[] = 'author';
                $segments[] = $alias;
                unset($query['author']);
            }
        } else if (isset($query['view']) && $query['view'] == 'system') {
            $system = gridboxHelper::getSystemParams($query['id'], false);
            unset($query['view']);
            unset($query['id']);
            if ($system) {
                $segments[] = $system->alias;
            }
        } else if (isset($query['view']) && $query['view'] == 'account') {
            unset($query['view']);
        } else if (isset($query['view']) && $query['view'] == 'create') {
            unset($query['view']);
            unset($query['id']);
        } else if (isset($query['layout'])) {
            unset($query['layout']);
        }
        
        return $segments;
    }

    public function parse(&$segments)
    {
        if ($segments[0] == 'index.php') {
            return [];
        }
        $tag = '';
        JLoader::register('gridboxHelper', dirname(__FILE__).'/helpers/gridbox.php');
        gridboxHelper::checkURI();
        foreach ($segments as $value) {
            if ($value == 'tag' || $value == 'author') {
                $tag = $value;
                break;
            }
        }
        $vars = [];
        $db = JFactory::getDbo();
        $alias = end($segments);
        if (!empty($tag)) {
            if ($segments[0] != $tag) {
                $blog = $segments[0];
            } else {
                $blog = null;
            }
            $vars = getTagsVars($alias, $blog, $tag);
            if (!empty($vars)) {
                $segments = [];

                return $vars;
            }
        }
        $system  = gridboxHelper::getSystemPageByAlias($alias);
        if (!empty($system)) {
            $vars['view'] = 'system';
            $vars['id'] = $system;
            $segments = [];

            return $vars;
        }
        $q = $db->getQuery(true)
            ->select('id')
            ->from('#__gridbox_pages')
            ->where('`page_alias` = '.$db->quote($alias));
        $db->setQuery($q);
        $id = $db->loadResult();
        if (!empty($id)) {
            $q = $db->getQuery(true)
                ->select('page_category')
                ->from('#__gridbox_pages')
                ->where('`page_alias` = '.$db->quote($alias));
            $db->setQuery($q);
            $cat = $db->loadResult();
            if ($cat == 'trashed') {
                return raiseError();
            } else if (!empty($cat)) {
                $array = gridboxHelper::getCategoryPath($cat);
            } else {
                $array = [];
            }
            $array = array_reverse($array);
            $array[] = $alias;
            foreach ($array as $key => $value) {
                if (!isset($segments[$key]) || $segments[$key] != $value) {
                    return raiseError();
                }
            }
        }
        if (empty($id)) {
            $q = $db->getQuery(true)
                ->select('id, app_id')
                ->from('#__gridbox_categories')
                ->where('`alias` = '.$db->quote($alias));
            $db->setQuery($q);
            $obj = $db->loadObject();
            $vars['view'] = 'blog';
            if (isset($obj->id)) {
                $id = $obj->id;
                $array = gridboxHelper::getCategoryPath($id);
                $array = array_reverse($array);
                foreach ($array as $key => $value) {
                    if (!isset($segments[$key]) || $segments[$key] != $value) {
                        return raiseError();
                    }
                }
                $vars['app'] = $obj->app_id;
            } else {
                $vars = getTagsVars($alias);
                if (!empty($vars)) {
                    $segments = [];

                    return $vars;
                }
            }
            if (empty($vars)) {
                $q = $db->getQuery(true)
                    ->select('id')
                    ->from('#__gridbox_app')
                    ->where('type <> '.$db->quote('system_apps'))
                    ->where('`alias` = '.$db->quote($alias));
                $db->setQuery($q);
                $obj = $db->loadObject();
                if (isset($obj->id)) {
                    $vars['view'] = 'blog';
                    $vars['app'] = $obj->id;
                    $id = 0;
                }
            }
        } else {
            $vars['view'] = 'page';
        }
        if (empty($id) && $id !== 0) {
            return raiseError();
        }
        $vars['id'] = $id;
        $segments = [];

        return $vars;
    }
}

function raiseError()
{
    $language = JFactory::getLanguage();
    $language->load('com_gridbox');

    return gridboxHelper::raiseError(404, $language->_('NOT_FOUND'));
}

function getTagsVars($alias, $blog = null, $tableName = 'tag')
{
    $db = JFactory::getDbo();
    $q = $db->getQuery(true)
        ->select('id')
        ->from('#__gridbox_'.$tableName.'s')
        ->where('`alias` = '.$db->quote($alias));
    $db->setQuery($q);
    $id = $db->loadResult();
    $vars = [];
    if (!empty($id)) {
        if ($blog) {
            $q =$db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_app')
                ->where('type <> '.$db->quote('system_apps'))
                ->where('alias = '.$db->quote($blog));
            $db->setQuery($q);
            $app = $db->loadResult();
            $vars['view'] = 'blog';
            $vars['app'] = $app;
            $vars['id'] = 0;
            $vars[$tableName] = $id;

            return $vars;
        }
        $app = JFactory::getApplication();
        $menu = $app->getMenu('site');
        $active = $menu->getActive();
        if (empty($active)) {
            $q = $db->getQuery(true)
                ->select('id')
                ->from('#__gridbox_app')
                ->where('type <> '.$db->quote('system_apps'))
                ->where('type <> '.$db->quote('single'));
            $db->setQuery($q);
            $app_id = $db->loadResult();
            if (empty($app_id)) {
                return [];
            } else {
                $active = new stdClass();
                $active->query = array('view' => 'blog', 'app' => $app_id, 'id' => 0);
            }
        }
        $query = $active->query;
        if ($query['view'] == 'blog') {
            $vars['view'] = 'blog';
            $vars['app'] = $query['app'];
            $vars['id'] = $query['id'];
            $vars[$tableName] = $id;

            return $vars;
        } else if ($query['view'] == 'page') {
            $q = $db->getQuery(true)
                ->select('app_id')
                ->from('#__gridbox_pages')
                ->where('`id` = '.$db->quote($query['id']));
            $db->setQuery($q);
            $app_id = $db->loadResult();
            $vars['view'] = 'blog';
            $vars['app'] = $app_id;
            $vars['id'] = 0;
            $vars[$tableName] = $id;

            return $vars;
        }
    }

    return [];
}

function gridboxBuildRoute(&$query)
{
    $app = JFactory::getApplication();
    $router = new gridboxRouter($app, $app->getMenu('site'));

    return $router->build($query);
}

function gridboxParseRoute($segments)
{
    $app = JFactory::getApplication();
    $router = new gridboxRouter($app, $app->getMenu('site'));

    return $router->parse($segments);
}