<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
JLoader::register('gridboxHelper', JPATH_ROOT.'/administrator/components/com_gridbox/helpers/gridbox.php');

class JFormFieldGridboxTag extends JFormFieldList
{
    protected $type = 'gridboxtag';
    
    protected function getInput()
    {
        $app = JFactory::getApplication();
        $id = $app->input->get('id');
        $db = JFactory::getDbo();
        $link = '';
        if (!empty($id)) {
            $query = $db->getQuery(true)
                ->select('link')
                ->from('#__menu')
                ->where('id = '.$id);
            $db->setQuery($query);
            $link = $db->loadResult();
        }
        $appTitle = '';
        $appId = '';
        $hide = 'jQuery("#'.$this->id.'_id").closest(".control-group").hide();';
        $iframe = '';
        if (!empty($link)) {
            $array = array();
            parse_str($link, $array);
            if (isset($array['app']) && !empty($array['app'])) {
                $appId = $array['app'];
                $query = $db->getQuery(true)
                    ->select('title')
                    ->from('#__gridbox_app')
                    ->where('id = '.$array['app']);
                $db->setQuery($query);
                $appTitle = $db->loadResult();
            }
            if (isset($array['tag'])) {
                $this->value = $array['tag'];
            }
        }
        if (!empty($appTitle)) {
            $hide = '';
            $iframe = 'index.php?option=com_gridbox&view=tags&layout=modal&tmpl=component&id='.$appId;
        }
        $doc = JFactory::getDocument();
        $query = $db->getQuery(true)
            ->select($db->quoteName('title'))
            ->from($db->quoteName('#__gridbox_tags'))
            ->where($db->quoteName('id') . ' = ' . (int) $this->value);
        $db->setQuery($query);
        $title = $db->loadResult();
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        include JPATH_ROOT.'/administrator/components/com_gridbox/views/layouts/select-tag.php';
        $html = $out;
        $html .= gridboxHelper::renderBootstrapModal('gridbox-category-modal', 'TAG', $iframe);
        $script = 'jQuery(document).ready(function(){
            jQuery("#gridbox-category-modal").on("shown.bs.modal", function(){
                if (this.dataset.url && this.querySelector("iframe") && this.querySelector("iframe").src != this.dataset.url) {
                    this.querySelector("iframe").src = this.dataset.url;
                }
            });
            jQuery("#jform_request_tag_id").closest(".control-group").before(jQuery("#select-app"));
            jQuery("#select-app").css("display", "");
            '.$hide.'
        });';
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }
        $doc->addScriptDeclaration($script);
        
        return $html;
    }
}