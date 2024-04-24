<?php
/**
* @package   Gridbox template
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$doc = JFactory::getDocument();
$doc->setGenerator('Powered by Website Builder Gridbox');
$this->language = $doc->language;
$this->direction = $doc->direction;
JLoader::register('gridboxHelper', JPATH_ROOT . '/components/com_gridbox/helpers/gridbox.php');
$aboutUs = gridboxHelper::aboutUs();
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/gridbox.css?'.$aboutUs->version);
$doc->addScriptDeclaration("var gridboxVersion = '".$aboutUs->version."';");
$file = JPATH_ROOT. '/templates/gridbox/css/custom.css';
if (is_file($file) && filesize($file) != 0) {
    $doc->addStyleSheet(JUri::root().'templates/gridbox/css/custom.css');
}
$doc->addStyleSheet('//fonts.googleapis.com/css?family=Roboto:300,400,500,700');
if (JVERSION >= '4.0.0') {
    $doc->addScript(JUri::root().'media/vendor/jquery/js/jquery.min.js');
} else {
    $doc->addScript(JUri::root().'media/jui/js/jquery.min.js');
}
$doc->addScript(JUri::root().'components/com_gridbox/libraries/bootstrap/bootstrap.js?'.$aboutUs->version);
JHtmlBootstrap::loadCss($includeMaincss = false, $this->direction);
$favicon = gridboxHelper::getFavicon();
$sidebar = $app->input->cookie->get('sidebar-settings', '', 'string');
$custom = $sidebar == 'visible' ? ' sidebar-settings' : '';
$style = '';
$position = '';
if ($view == 'editor') {
    $panel = gridboxHelper::getModalSettings('page-structure-panel');
    $doc->addScriptDeclaration("window.pagestructure = ".$panel.";");
    $obj = json_decode($panel);
    $custom .= !empty($obj->position) && isset($obj->visible) && $obj->visible ? ' gridbox-page-structure-left' : '';
    $cp = gridboxHelper::getModalSettings();
    $doc->addScriptDeclaration("window.cp = ".$cp.";");
    $obj = json_decode($cp);
    foreach ($obj as $key => $value) {
        if ($key == 'position') {
            $position = $value;
        } else if ($key != 'visible') {
            $style .= '--modal-cp-'.$key.': '.($value < 0 ? 0 : $value).'px; ';
        }
    }
    $custom .= !empty($obj->position) && isset($obj->visible) && $obj->visible ? ' gridbox-cp-panel-right' : '';
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <jdoc:include type="head" />
<?php
    echo $favicon;
?>
    <style type="text/css">[gridbox-plugins-css]</style>
<?php
    if ($this->direction == 'rtl') {
?>
        <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/gridbox/css/gridbox-rtl.css" type="text/css" />
<?php
    }
?>
</head>
<body class="contentpane modal <?php echo $option.' '.$view.$custom; ?>" data-cp-position="<?php echo $position ?>" style="<?php echo $style; ?>">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
