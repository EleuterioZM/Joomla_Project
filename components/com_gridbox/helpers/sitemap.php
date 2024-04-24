<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxSitemapHelper
{
    public function __construct()
    {
        include_once JPATH_ROOT.'/components/com_gridbox/helpers/seo.php';
    }

    public function create()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('m.id, m.link, m.title, m.language')
            ->from('#__menu_types AS mt')
            ->leftJoin('`#__menu` AS m ON mt.menutype = m.menutype')
            ->where('mt.client_id = 0')
            ->where('published = 1')
            ->where('access = 1')
            ->where('m.type = '.$db->quote('component'));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $langObj = new stdClass();
        if (JLanguageMultilang::isEnabled()) {
            $languages  = JLanguageHelper::getLanguages();
            foreach ($languages as $language) {
                $langObj->{$language->lang_code} = $language->sef;
            }
        }
        $str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $str .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($items as $item) {
            $link = $item->link.'&Itemid='.$item->id;
            $link = 'index.php?Itemid='.$item->id;
            if (JLanguageMultilang::isEnabled() && $item->language != '*' && isset($langObj->{$item->language})) {
                $link .= '&lang='.($langObj->{$item->language});
            }
            $linkString = str_replace('index.php?', '', $item->link);
            parse_str($linkString, $array);
            if (isset($array['option']) && isset($array['id'])) {
                $data = $this->getMenuModify($array);
            } else {
                $data = new stdClass();
                $data->sitemap_include = 1;
                $data->lastmod = date('Y-m-d');
                $data->changefreq = 'monthly';
                $data->priority = '0.5';
            }
            $url = JRoute::_($link);
            $str .= $this->getUrl($url, $data);
        }
        gridboxHelper::getGridboxMenuItems();
        $itemId = gridboxHelper::getDefaultMenuItem();
        if (!empty($itemId)) {
            $default = '&Itemid='.$itemId;
        }
        $str .= $this->getPages(0, 'single', $default, $langObj);
        $query = $db->getQuery(true)
            ->select('id, type, language, saved_time, changefreq, priority, sitemap_include')
            ->from('#__gridbox_app')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('published = 1')
            ->where('access = 1');
        $db->setQuery($query);
        $apps = $db->loadObjectList();
        foreach ($apps as $app) {
            if ($app->type != 'single') {
                $itemId = gridboxHelper::getGridboxMenuItemidByApp($app->id);
                $app->lastmod = $this->setLastmod($app->saved_time);
                if (date('Y', strtotime($app->lastmod)) < 2000) {
                    $app->lastmod = date('Y-m-d');
                } else {
                    $app->lastmod = date('Y-m-d', strtotime($app->lastmod));
                }
                if (!$itemId) {
                    $link = 'index.php?option=com_gridbox&view=blog&app='.$app->id.'&id=0'.$default;
                    if (JLanguageMultilang::isEnabled() && $app->language != '*' && isset($langObj->{$app->language})) {
                        $link .= '&lang='.($langObj->{$app->language});
                    }
                    $url = JRoute::_($link);
                    $str .= $this->getUrl($url, $app);
                }
                $str .= $this->getCategories($app->id, $app->lastmod, $default, $langObj);
                $str .= $this->getTags($app->id, $app->lastmod, $default, $langObj);
                $str .= $this->getAuthors($app->id, $app->lastmod, $default, $langObj);
            }
            $str .= $this->getPages($app->id, $app->type, $default, $langObj);
        }
        $str .= '</urlset>';
        JFile::write(JPATH_ROOT.'/sitemap.xml', $str);
    }

    public function getMenuModify($array)
    {
        $db = JFactory::getDbo();
        $id = $array['id'];
        $table = $type = '';
        $column = 'saved_time, changefreq, priority, sitemap_include';
        if ($array['option'] == 'com_gridbox' && $array['view'] == 'page') {
            $table = '#__gridbox_pages';
            $column .= ', created, sitemap_override, app_id';
            $type = 'page';
        } else if ($array['option'] == 'com_gridbox' && $array['view'] == 'blog') {
            $table = '#__gridbox_app';
            $id = $array['app'];
            $type = 'app';
        }
        if (!empty($table)) {
            $data = $this->getComponentModify($table, $column, $id, $type);
        } else {
            $data = new stdClass();
            $data->sitemap_include = 1;
            $data->lastmod = date('Y-m-d');
            $data->changefreq = 'monthly';
            $data->priority = '0.5';
        }

        return $data;
    }

    public function getComponentModify($table, $column, $id, $type)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($column)
            ->from($table)
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (!$obj) {
            return null;
        }
        if ($type == 'page') {
            $query = $db->getQuery(true)
                ->select('type')
                ->from('#__gridbox_app')
                ->where('id = '.$obj->app_id);
            $db->setQuery($query);
            $obj->app_type = $db->loadResult();
        }
        if ($type != 'app' && $obj->sitemap_override == 0) {
            $seo = new gridboxSeoHelper($obj, $type);
            $global = $seo->getGlobal();
            $obj->sitemap_include = $global->sitemap_include;
            $obj->changefreq = $global->changefreq;
            $obj->priority = $global->priority;
        }
        $obj->lastmod = $this->setLastmod($obj->saved_time);
        $Y = date('Y', strtotime($obj->lastmod));
        if ($Y < 2000 && isset($obj->created)) {
            $obj->lastmod = $obj->created;
        }
        if ($Y < 2000 && !isset($obj->created)) {
            $obj->lastmod = date('Y-m-d');
        } else {
            $obj->lastmod = date('Y-m-d', strtotime($obj->lastmod));
        }

        return $obj;
    }

    public function getUrl($url, $data)
    {
        $l = strlen($url) - 1;
        if (gridboxHelper::$website->sitemap_slash && $url[$l] != '/') {
            $url .= '/';
        } else if (!gridboxHelper::$website->sitemap_slash && $url[$l] == '/') {
            $url = substr($url, 0, $l);
        }
        if ($data && $data->sitemap_include == 1) {
            $str = "\t<url>\n";
            $str .= "\t\t<loc>".gridboxHelper::$website->sitemap_domain.$url."</loc>\n";
            $str .= "\t\t<lastmod>".$data->lastmod."</lastmod>\n";
            $str .= "\t\t<changefreq>".$data->changefreq."</changefreq>\n";
            $str .= "\t\t<priority>".$data->priority."</priority>\n";
            $str .= "\t</url>\n";
        } else {
            $str = '';
        }

        return $str;
    }

    public function getPages($app_id, $type, $default, $langObj)
    {
        $str = '';
        $db = JFactory::getDbo();
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('id, page_category, language, saved_time, created, changefreq, priority, sitemap_include, sitemap_override, app_id')
            ->from('#__gridbox_pages')
            ->where('app_id = '.$app_id)
            ->where('page_category <> '.$db->quote('trashed'))
            ->where('published = 1')
            ->where('created <= '.$date)
            ->where('page_access = 1')
            ->where('(end_publishing = '.$nullDate.' OR end_publishing >= '.$date.')');
        $digital = gridboxHelper::getSubscriptionProducts();
        if (!empty($digital)) {
            $pks = implode(', ', $digital);
            $query->where('id NOT IN ('.$pks.')');
        }
        $db->setQuery($query);
        $pages = $db->loadObjectList();
        foreach ($pages as $page) {
            $itemId = gridboxHelper::getGridboxMenuItemidByPage($page->id);
            if (!empty($itemId)) {
                continue;
            }
            if ($page->sitemap_override == 0) {
                $seo = new gridboxSeoHelper($page, 'page');
                $global = $seo->getGlobal();
                $page->sitemap_include = $global->sitemap_include;
                $page->changefreq = $global->changefreq;
                $page->priority = $global->priority;
            }
            $link = gridboxHelper::getGridboxPageLinks($page->id, $type, $app_id, $page->page_category);
            if (strpos($link, '&Itemid=') === false) {
                $link .= $default;
            }
            $str .= $this->getPage($link, $page, $langObj);
        }

        return $str;
    }

    public function getPage($link, $page, $langObj)
    {
        if (JLanguageMultilang::isEnabled() && $page->language != '*' && isset($langObj->{$page->language})) {
            $link .= '&lang='.($langObj->{$page->language});
        }
        $page->lastmod = $this->setLastmod($page->saved_time);
        if (date('Y', strtotime($page->lastmod)) < 2000) {
            $page->lastmod = $page->created;
        }
        $page->lastmod = date('Y-m-d', strtotime($page->lastmod));
        $url = JRoute::_($link);
        $str = $this->getUrl($url, $page);

        return $str;
    }

    public function setLastmod($saved_time)
    {
        if (!empty($saved_time)) {
            $array = explode('-', $saved_time);
            if (count($array) == 6) {
                $saved_time = $array[0].'-'.$array[1].'-'.$array[2].' '.$array[3].':'.$array[4].':'.$array[5];
            }
        }

        return $saved_time;
    }

    public function getCategories($app_id, $lastmod, $default, $langObj)
    {
        $str = '';
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, language, changefreq, priority, sitemap_include, sitemap_override, app_id')
            ->from('#__gridbox_categories')
            ->where('app_id = '.$app_id)
            ->where('published = 1')
            ->where('access = 1');
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        foreach ($categories as $category) {
            $itemId = gridboxHelper::getGridboxMenuItemidByCategory($app_id, $category->id);
            if (!empty($itemId)) {
                continue;
            }
            if ($category->sitemap_override == 0) {
                $seo = new gridboxSeoHelper($category, 'category');
                $global = $seo->getGlobal();
                $category->sitemap_include = $global->sitemap_include;
                $category->changefreq = $global->changefreq;
                $category->priority = $global->priority;
            }
            $link = gridboxHelper::getGridboxCategoryLinks($category->id, $app_id);
            if (strpos($link, '&Itemid=') === false) {
                $link .= $default;
            }
            if (JLanguageMultilang::isEnabled() && $category->language != '*' && isset($langObj->{$category->language})) {
                $link .= '&lang='.($langObj->{$category->language});
            }
            $url = JRoute::_($link);
            $category->lastmod = $lastmod;
            $str .= $this->getUrl($url, $category);
        }

        return $str;
    }

    public function getTags($app_id, $lastmod, $default, $langObj)
    {
        $str = '';
        $db = JFactory::getDbo();
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('DISTINCT t.id, t.language, t.changefreq, t.priority, t.sitemap_include, t.sitemap_override')
            ->from('#__gridbox_pages AS p')
            ->where('p.app_id = '.$app_id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('p.page_access = 1')
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('t.published = 1')
            ->where('t.access = 1')
            ->leftJoin('`#__gridbox_tags_map` AS m ON p.id = m.page_id')
            ->leftJoin('`#__gridbox_tags` AS t ON t.id = m.tag_id');
        $db->setQuery($query);
        $tags = $db->loadObjectList();
        foreach ($tags as $tag) {
            $itemId = gridboxHelper::getGridboxMenuItemidByTag($tag->id, $app_id);
            if (!empty($itemId)) {
                continue;
            }
            if ($tag->sitemap_override == 0) {
                $seo = new gridboxSeoHelper($tag, 'tag');
                $global = $seo->getGlobal();
                $tag->sitemap_include = $global->sitemap_include;
                $tag->changefreq = $global->changefreq;
                $tag->priority = $global->priority;
            }
            $link = gridboxHelper::getGridboxTagLinks($tag->id, $app_id);
            if (strpos($link, '&Itemid=') === false) {
                $link .= $default;
            }
            if (JLanguageMultilang::isEnabled() && $tag->language != '*' && isset($langObj->{$tag->language})) {
                $link .= '&lang='.($langObj->{$tag->language});
            }
            $url = JRoute::_($link);
            $tag->lastmod = $lastmod;
            $str .= $this->getUrl($url, $tag);
        }

        return $str;
    }

    public function getAuthors($app_id, $lastmod, $default, $langObj)
    {
        $str = '';
        $db = JFactory::getDbo();
        $date = date("Y-m-d H:i:s");
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('DISTINCT t.id, t.changefreq, t.priority, t.sitemap_include, t.sitemap_override')
            ->from('#__gridbox_pages AS p')
            ->where('p.app_id = '.$app_id)
            ->where('p.page_category <> '.$db->quote('trashed'))
            ->where('p.published = 1')
            ->where('p.created <= '.$db->quote($date))
            ->where('p.page_access = 1')
            ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$db->quote($date).')')
            ->where('t.published = 1')
            ->leftJoin('`#__gridbox_authors_map` AS m ON p.id = m.page_id')
            ->leftJoin('`#__gridbox_authors` AS t ON t.id = m.author_id');
        $db->setQuery($query);
        $authors = $db->loadObjectList();
        foreach ($authors as $author) {
            if ($author->sitemap_override == 0) {
                $seo = new gridboxSeoHelper($author, 'author');
                $global = $seo->getGlobal();
                $author->sitemap_include = $global->sitemap_include;
                $author->changefreq = $global->changefreq;
                $author->priority = $global->priority;
            }
            $link = gridboxHelper::getGridboxAuthorLinks($author->id, $app_id);
            if (strpos($link, '&Itemid=') === false) {
                $link .= $default;
            }
            $url = JRoute::_($link);
            $author->lastmod = $lastmod;
            $str .= $this->getUrl($url, $author);
        }

        return $str;
    }
}