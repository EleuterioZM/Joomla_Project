<?php
/**
* @package   Grifbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

class gridboxModelBlog extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getCategory()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_categories')
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();

        return $item;
    }

    public function getItem($id = null)
    {
        $input = JFactory::getApplication()->input;
        $tag = $input->get('tag', 0, 'int');
        $author = $input->get('author', 0, 'int');
        if (!empty($tag)) {
            $table = $this->getTable('tags');
            $table->load($tag);
            $table->hit($tag);
        }
        if (!empty($author)) {
            $table = $this->getTable('authors');
            $table->load($author);
            $table->hit($author);
        }
        $db = $this->getDbo();
        $id = $input->get('app', 0, 'int');
        $category = $input->get('id', 0, 'int');
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__gridbox_app')
            ->where('language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')')
            ->where('published = 1')
            ->where('type <> '.$db->quote('system_apps'))
            ->where('id = ' .$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (!$item || $item->type == 'single') {
            return null;
        }
        if (empty($item->app_layout)) {
            $item->app_layout = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/app.html');
        }
        if (empty($item->app_items)) {
            $item->app_items = gridboxHelper::readFile(JPATH_ROOT.'/components/com_gridbox/views/layout/apps/'.$item->type.'/app.json');
        }
        if (!empty($tag) || $category != 0) {
            $query = $db->getQuery(true)
                ->select('access');
            if (!empty($tag)) {
                $query->from("#__gridbox_tags")
                    ->where('id = '.$tag);
            } else {
                $query->from("#__gridbox_categories")
                    ->where('id = '.$category);
            }
            $query->where('language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')')
                ->where('published = 1');
            $db->setQuery($query);
            $item->access = $db->loadResult();
            if (!$item->access) {
                $item = null;
            }
        }
        
        return $item;
    }

    public function getTag()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('tag', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_tags')
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();

        return $item;
    }

    public function getAuthor()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('author', 0, 'int');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_authors')
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();

        return $item;
    }
    
    public function getForm()
    {
        $form = JForm::getInstance('gridbox', JPATH_COMPONENT.'/models/forms/gridbox.xml');
        
        return $form;
    }
}
