<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxModelTextEditor extends JModelItem
{
    public function getTable($type = 'pages', $prefix = 'gridboxTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        
    }

    public function getForm()
    {
        $form = JForm::getInstance('gridbox', JPATH_COMPONENT.'/models/forms/gridbox.xml');
        
        return $form;
    }

    public function getJce()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('enabled')
            ->from('`#__extensions`')
            ->where('`element` = '.$db->quote('jce'))
            ->where('`folder` = '.$db->quote('editors'));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }
}