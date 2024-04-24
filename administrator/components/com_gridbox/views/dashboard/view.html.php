<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class gridboxViewDashboard extends JViewLegacy
{
    protected $about;
    protected $apps;
    protected $pages;
    protected $comments;
    protected $reviews;
    protected $files;
    protected $_imagesExt;
    
    public function display($tpl = null) 
    {
        $app = JFactory::getApplication();
        $this->pages = $this->get('Pages');
        $this->comments = $this->get('Comments');
        $this->reviews = $this->get('Reviews');
        $this->get('Filetypes');
        $files = $this->get('Files');
        $this->_imagesExt = array('jpg', 'png', 'gif', 'jpeg', 'svg', 'ico', 'webp');
        usort($files, function($a, $b){
            if ($a->modify == $b->modify) {
                return 0;
            }
            return ($a->modify < $b->modify) ? 1 : -1;
        });
        $this->files = array_slice($files, 0, 10);
        $this->about = gridboxHelper::aboutUs();
        $this->apps = gridboxHelper::getApps();
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('components/com_gridbox/assets/css/ba-admin.css?1'.$this->about->version);
        if (JVERSION >= '4.0.0') {
            $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
        }

        parent::display($tpl);
    }

    public function preferences()
    {
        $url = 'index.php?option=com_config&amp;view=component&amp;component=com_gridbox&amp;path=';
        
        return $url;
    }
}