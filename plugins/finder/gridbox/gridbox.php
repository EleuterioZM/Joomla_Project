<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


defined('_JEXEC') or die;

use Joomla\Registry\Registry;
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

class PlgFinderGridbox extends FinderIndexerAdapter
{
    protected $context = 'Gridbox';
    protected $extension = 'com_gridbox';
    protected $layout = 'page';
    protected $type_title = 'Page';
    protected $table = '#__gridbox_pages';
    protected $autoloadLanguage = true;
    protected $dateFormat = null;

    protected function setup()
    {
        return true;
    }

    public function onGidboxPagesAfterDelete($cid)
    {
        foreach ($cid as $id) {
            $this->remove($id);
        }
    }

    public function onGidboxPageAfterSave($id)
    {
        $this->reindex($id);
    }

    public function onGidboxPagesAfterPublish($cid)
    {
        foreach ($cid as $id) {
            $this->reindex($id);
        }
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

    protected function getPageFields($id)
    {
        $db = JFactory::getDbo();
        
        $data = [];
        $query = $db->getQuery(true)
            ->select('app_id')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $app_id = $db->loadResult();
        if (!empty($app_id)) {
            $query = $db->getQuery(true)
                ->select('f.*')
                ->from('#__gridbox_fields as f')
                ->where('f.app_id = '.$app_id)
                ->select('pf.value')
                ->where('pf.page_id = '.$id)
                ->leftJoin('`#__gridbox_page_fields` AS pf ON pf.field_id = f.id')
                ->where('pf.field_type <> '.$db->quote('field-google-maps'))
                ->where('pf.field_type <> '.$db->quote('field-simple-gallery'))
                ->where('pf.field_type <> '.$db->quote('product-gallery'))
                ->where('pf.field_type <> '.$db->quote('field-slideshow'))
                ->where('pf.field_type <> '.$db->quote('product-slideshow'))
                ->where('pf.field_type <> '.$db->quote('field-video'))
                ->where('pf.field_type <> '.$db->quote('image-field'))
                ->where('pf.field_type <> '.$db->quote('file'))
                ->where('pf.field_type <> '.$db->quote('field-button'))
                ->where('pf.field_type <> '.$db->quote('tag'));
            $db->setQuery($query);
            $data = $db->loadObjectList();
        }

        return $data;
    }

    protected function index(FinderIndexerResult $item, $format = 'html')
    {
        $item->setLanguage();
        if (JComponentHelper::isEnabled($this->extension) == false) {
            return;
        }
        $registry = new Registry;
        if (!empty($item->metadata)) {
            $registry->loadString($item->metadata);
        }
        $item->metadata = $registry;
        $item->publish_start_date = $item->start_date;
        $item->summary = strip_tags($item->body);
        $item->body = FinderIndexerHelper::prepareContent($item->body, '');
        $fields = $this->getPageFields($item->id);
        foreach ($fields as $field) {
            if (empty($field->value) || $field->value == '[]') {
                continue;
            }
            $str = '';
            if ($field->field_type == 'radio' || $field->field_type == 'select') {
                $fieldOptions = json_decode($field->options);
                foreach ($fieldOptions->items as $fieldOption) {
                    if ($fieldOption->key == $field->value) {
                        $str = $fieldOption->title;
                        break;
                    }
                }
            } else if ($field->field_type == 'checkbox') {
                $fieldOptions = json_decode($field->options);
                $valueOptions = json_decode($field->value);
                foreach ($valueOptions as $valueOption) {
                    foreach ($fieldOptions->items as $fieldOption) {
                        if ($fieldOption->key == $valueOption) {
                            $str .= '<span>'.$fieldOption->title.'</span>';
                        }
                    }
                }
            } else if ($field->field_type == 'url') {
                $valueOptions = json_decode($field->value);
                $str = $valueOptions->label;
            } else if ($field->field_type == 'time') {
                $valueOptions = json_decode($field->value);
                $str = $valueOptions->hours.':'.$valueOptions->minutes.' '.$valueOptions->format;
            } else if ($field->field_type == 'date' || $field->field_type == 'event-date') {
                $str = $this->getPostDate($field->value);
            } else if ($field->field_type == 'price') {
                $fieldOptions = json_decode($field->options);
                $thousand = $fieldOptions->thousand;
                $separator = $fieldOptions->separator;
                $decimals = $fieldOptions->decimals;
                $price = $this->preparePrice($field->value, $thousand, $separator, $decimals);
                $str = $price;
            } else {
                $str = htmlspecialchars($field->value);
            }
            $item->body .= ' '.$str;
        }
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        include_once JPATH_ROOT.'/components/com_gridbox/libraries/php/phpQuery/phpQuery.php';
        $item->body = preg_replace('/\[main_menu=+(.*?)\]/i', '', $item->body);
        $doc = phpQuery::newDocument($item->body);
        $search = '.ba-edit-item, .ba-box-model, .empty-item, .column-info, .ba-column-resizer,';
        $search .= ' .ba-edit-wrapper, .empty-list, .ba-item-main-menu > .ba-menu-wrapper > .main-menu > .add-new-item';
        pq($search)->remove();
        $item->body = $doc->htmlOuter();
        $item->summary = strip_tags($item->body);
        $item->url = $this->getUrl($item->id, $this->extension, $this->layout);
        $item->route = $this->getHref($item->id);
        if (JVERSION < '4.0.0') {
            //$item->path = FinderIndexerHelper::getContentPath($item->route);
        }
        $title = $this->getItemMenuTitle($item->url);
        if (!empty($title) && $this->params->get('use_menu_title', true)) {
            $item->title = $title;
        }
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'meta_title');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
        $item->state = $this->translateState($item->state, $item->cat_state);
        FinderIndexerHelper::getContentExtras($item);
        $this->indexer->index($item);
    }

    public function preparePrice($price, $thousand, $separator, $decimals)
    {
        $price = round($price * 1, $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);

        return $price;
    }

    public function setDateFormat()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_website')
            ->where('1');
        $db->setQuery($query);
        $website = $db->loadObject();
        $this->dateFormat = $website->date_format;
    }

    public function getDateFormat()
    {
        if (!$this->dateFormat) {
            $this->setDateFormat();
        }

        return $this->dateFormat;
    }

    public function getPostDate($created)
    {
        $dateFormat = $this->getDateFormat();
        $date = JHtml::date($created, $dateFormat, null);

        return $date;
    }

    protected function getListQuery($query = null)
    {
        $db = JFactory::getDbo();
        $query = $query instanceof JDatabaseQuery ? $query : $db->getQuery(true)
            ->select('a.id, a.title, a.page_alias AS alias, a.params AS body')
            ->select('a.published AS state, a.created AS start_date')
            ->select('a.meta_title, a.meta_description AS metadesc, a.meta_keywords AS metakey, a.page_access AS access')
            ->where('a.page_category <> '.$db->quote('trashed'))
            ->from('#__gridbox_pages AS a');

        return $query;
    }
}
