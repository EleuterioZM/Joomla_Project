<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.folder');
 
class plgSystemBaforms extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        if ($app->isClient('site')) {
            $path = JPATH_ROOT.'/components/com_baforms/helpers/baforms.php';
            JLoader::register('baformsHelper', $path);
        }
    }
    
    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        if ($app->isClient('site') && $doc->getType() == 'html') {
            $this->setForms();
        } else if ($app->isClient('administrator') && $doc->getType() == 'html' && JVERSION >= '4.0.0') {
            $html = $app->getBody();
            $html = str_replace('<body', "<body data-joomla-version='4'", $html);
            $app->setBody($html);
        }
    }

    public function onBeforeRenderGridbox()
    {
        $this->setForms();
    }

    public function setForms()
    {
        $app = JFactory::getApplication();
        $a_id = $app->input->get('a_id');
        $option = $app->input->get('option', '', 'string');
        if (empty($a_id) && $option != 'com_config') {
            $loaded = JLoader::getClassList();
            if (isset($loaded['baformshelper'])) {
                baformshelper::prepareHelper();
                $html = $app->getBody();
                $pos = strpos($html, '</head>');
                $head = substr($html, 0, $pos);
                $body = substr($html, $pos);
                include JPATH_ROOT.'/components/com_baforms/views/form/tmpl/click-trigger.min.php';
                $body = str_replace('</body>', $out.'</body>', $body);
                $content = $this->getContent($body);
                $html = $head.$content;
                $app->setBody($html);
            }
        }
    }
    
    public function getContent($body)
    {
        if (empty(baformshelper::$about)) {
            baformshelper::prepareHelper();
        }
        $body = baformsHelper::renderFormHTML($body);

        return $body;
    }
}