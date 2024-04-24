<?php
/**
* @package   Gridbox template
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$doc->setGenerator('Powered by Website Builder Gridbox');
$this->language = $doc->language;
$this->direction = $doc->direction;
$option = $app->input->get('option', '', 'string');
$view = $app->input->get('view', '', 'string');
if ($option == 'com_gridbox' && $view == 'editor') {
    $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/gridbox.css');
    if (JVERSION >= '4.0.0') {
        $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
    } else {
        $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
    }
    $doc->addScript(JUri::root().'/components/com_gridbox/libraries/bootstrap/bootstrap.js');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<jdoc:include type="head" />
</head>
<body class="contentpane modal <?php echo $option; ?>">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
<?php
} else {
JLoader::register('gridboxHelper', JPATH_ROOT . '/components/com_gridbox/helpers/gridbox.php');
$system = gridboxHelper::getSystemParamsByType('offline');
$system->options = json_decode($system->page_options);
$pageclass = !empty($system->options->suffix) ? $pageclass .= ' '.$system->options->suffix : '';
if (!$system) {
    exit;
}
gridboxHelper::checkSystemTheme($system->id);
gridboxHelper::setBreakpoints();
gridboxHelper::checkResponsive();
gridboxHelper::checkGridboxLanguage();
$aboutUs = gridboxHelper::aboutUs();
$id = gridboxHelper::getTheme($system->id, false, 'system');
$data = array('id' => $system->id, 'theme' => $id, 'edit_type' => 'system');
$page = new stdClass();
$page->option = 'com_gridbox';
$page->view = 'page';
$page->id = $system->id;
$data['page'] = $page;
$this->params = gridboxHelper::getThemeParams($id);
$params = $this->params->get('params');
$time = $this->params->get('time', '');
if (!empty($time)) {
    $time = '?'.$time;
}
$item = gridboxHelper::getSystemParams($system->id);
$item->html = gridboxHelper::checkModules($item->html, $item->items);
gridboxHelper::checkMoreScripts($item->html, $time);
gridboxHelper::prepareParentFonts($params);
gridboxHelper::checkSystemCss($system->id);
$fonts = '{}';
$fonts = gridboxHelper::prepareFonts($fonts, 'com_gridbox', $system->id, 'system');
$custom = gridboxHelper::checkCustom($id, 'page', $time);
$website = gridboxHelper::getWebsiteCode();
if (JVERSION >= '4.0.0') {
    $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
} else {
    $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
}
$doc->addScript(JUri::root().'/components/com_gridbox/libraries/bootstrap/bootstrap.js');
$doc->addScript($this->baseurl . '/templates/gridbox/js/gridbox.js?'.$aboutUs->version);
$doc->addStyleSheet($this->baseurl . '/templates/gridbox/css/gridbox.css?'.$aboutUs->version);
$doc->addStyleSheet($this->baseurl . '/templates/gridbox/css/storage/responsive.css'.$time);
$doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/style-'.$id.'.css'.$time);
$doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/system-page-'.$system->id.'.css'.$time);
if (!empty($fonts)) {
    $doc->addStyleSheet($fonts);
}
$breakpoints = json_encode(gridboxHelper::$breakpoints);
$disable_responsive = gridboxHelper::$website->disable_responsive == 1 ? 'true' : 'false';
$getItemsUrl = 'index.php?option=com_gridbox&task=editor.getItems&id='.$data['id'].'&theme='.$data['theme'].'&edit_type=system';
$getItemsUrl .= '&view='.$data['page']->view.'&'.str_replace('?', '', $time);
$doc->addScript(JUri::root().$getItemsUrl);
$stylesheets = gridboxHelper::returnSystemStyle($doc);
$favicon = gridboxHelper::getFavicon();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>"
    dir="<?php echo $this->direction; ?>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $item->title; ?></title>
<?php
if (!(bool)gridboxHelper::$website->disable_responsive) {
?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php
} else {
?>
    <meta name="viewport" content="width=device-width">
<?php
}
if ($this->direction == 'rtl') {
?>
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/media/jui/css/bootstrap-rtl.css" type="text/css" />
<?php
}
    echo $favicon;
?>
    <style type="text/css">[gridbox-plugins-css]</style>
<?php
    echo $stylesheets;
    echo "\n".$website->header_code;
    echo $custom;
?>
    <script>
        var JUri = '<?php echo JUri::root(); ?>',
            breakpoints = <?php echo $breakpoints; ?>,
            menuBreakpoint = '<?php echo gridboxHelper::$menuBreakpoint; ?>' * 1,
            disableResponsive = <?php echo $disable_responsive; ?>,
            gridboxVersion = '<?php echo $aboutUs->version; ?>',
            themeData = <?php echo json_encode($data); ?>;
    </script>
</head>
<body class="com_gridbox page<?php echo $pageclass; ?>">
    <div class="ba-overlay"></div>
    <div class="body">
        <div class="row-fluid main-body">
            <div class="ba-col-12">
<?php
if (JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
?>
            <a class="edit-page-btn" target="_blank"
               href="<?php echo JUri::root().'index.php?option=com_gridbox&view=editor&edit_type=system&tmpl=component&id='.$system->id; ?>">
               <i class="zmdi zmdi-settings"></i>
               <span class="ba-tooltip ba-top"><?php echo JText::_('EDIT_PAGE'); ?></span>
            </a>
<?php
}
            echo $item->html;
?>
            </div>
        </div>
    </div>
<?php
if ($params->desktop->background->type == 'video') {
?>
    <div class="ba-video-background global-video-bg"></div>
<?php
}
?>
<?php
echo $website->body_code."\n";
?>
</body>
</html>
<?php
}