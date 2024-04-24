<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewUploader extends JViewLegacy
{
    protected $_limit;
    protected $about;
    protected $version;
    protected $uploader;
    
    public function display($tpl = null)
    {
        if (count($errors = $this->get('Errors'))) {
            gridboxHelper::raiseError(500, implode('<br />', $errors));
            return false;
        }
        $this->uploader = $this->get('Uploader');
        $this->about = gridboxHelper::aboutUs();
        $this->version = $this->about->version;
        $this->_limit = $this->uploader->limit;
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('//fonts.googleapis.com/css?family=Roboto:300,400,500,700');
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
        } else {
            $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
        }
        if ($doc->getDirection() == 'rtl') {
            $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/rtl-ba-style.css?'.$this->version);
        }
        $doc->setTitle('Gridbox Editor');
        
        parent::display($tpl);
    }
}