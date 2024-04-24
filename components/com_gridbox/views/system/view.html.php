<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxViewSystem extends JViewLegacy
{
    protected $item;
    
    public function display($tpl = null)
    {
        $this->item = $this->get('Item');
        if (empty($this->item)) {
            gridboxHelper::raiseError(404, JText::_('NOT_FOUND'));
        }
        if ($this->item->type == 'submission-form') {
            $options = json_decode($this->item->page_options);
            $user = JFactory::getUser();
            $groups = $user->getAuthorisedViewLevels();
            if (!in_array($options->access, $groups)) {
                gridboxHelper::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
                return;
            }
        }
        $this->item->html = gridboxHelper::checkModules($this->item->html, $this->item->items);
        $this->prepareDocument();
        parent::display($tpl);
    }

    public function prepareDocument()
    {
        $doc = JFactory::getDocument();
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
        } else {
            $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
        }
        $doc->addScript(JUri::root().'/components/com_gridbox/libraries/bootstrap/bootstrap.js');
        if ($this->item->type == 'thank-you-page') {
            $session = JFactory::getSession();
            $json = $session->get('gridbox-store-layer', '');
            if (!empty($json)) {
                $script = 'window.gridboxOrder = '.$json.';';
                $doc->addScriptDeclaration($script);
                $session->clear('gridbox-store-layer');
            }
            if (!empty($json) && gridboxHelper::$website->google_analytics
                && !empty(gridboxHelper::$website->google_gtm_id) && gridboxHelper::$website->ecommerce_tracking) {
                $script = "window.dataLayer = window.dataLayer || []; window.dataLayer.push(".$json.");";
                $doc->addScriptDeclaration($script);
            }
        }
        $time = $this->item->saved_time;
        if (!empty($time)) {
            $time = '?'.$time;
        }
        $doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/system-page-'.$this->item->id.'.css'.$time);
        $doc->setTitle($this->item->title);
        gridboxHelper::checkMoreScripts($this->item->html, $time);
    }
}