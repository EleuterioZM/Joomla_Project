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
$this->language = $doc->language;
$this->direction = $doc->direction;
JLoader::register('gridboxHelper', JPATH_ROOT.'/components/com_gridbox/helpers/gridbox.php');
$path = JPATH_ROOT.'/components/com_baforms/helpers/baforms.php';
if (file_exists($path)) {
    JLoader::register('baformsHelper', $path);
    baformshelper::prepareHelper();
}
gridboxHelper::$isError = true;
gridboxHelper::setBreakpoints();
gridboxHelper::checkResponsive();
gridboxHelper::checkGridboxLanguage();
$aboutUs = gridboxHelper::aboutUs();
$error = gridboxHelper::getSystemParamsByType('404');
if (!$error) {
    exit;
}
gridboxHelper::checkSystemTheme($error->id);
$id = gridboxHelper::getTheme($error->id, false, 'system');
$data = array('id' => $error->id, 'theme' => $id, 'edit_type' => 'system');
$page = new stdClass();
$page->option = 'com_gridbox';
$page->view = 'page';
$page->id = $error->id;
$data['page'] = $page;
$this->params = gridboxHelper::getThemeParams($id);
$params = $this->params->get('params');
gridboxHelper::prepareParentFonts($params);
gridboxHelper::checkSystemCss($error->id);
$error->html = gridboxHelper::checkModules($error->html, $error->items);
$code = $this->error->getCode();
$message = $this->error->getMessage();
$error->html = str_replace('{gridbox_error_code}', $code, $error->html);
$error->html = str_replace('{gridbox_error_message}', $message, $error->html);
$time = $this->params->get('time', '');
if (!empty($time)) {
    $time = '?'.$time;
}
$error->options = json_decode($error->page_options);
if ($error->options->enable_header == 1) {
    $footer = $this->params->get('footer');
    $header = $this->params->get('header');
    $layout = $this->params->get('layout');
    $fonts = $this->params->get('fonts');
} else {
    $fonts = '{}';
}
$fonts = gridboxHelper::prepareFonts($fonts, 'com_gridbox', $error->id, 'system');
$custom = gridboxHelper::checkCustom($id, 'page', $time);
$website = gridboxHelper::getWebsiteCode();
if ($error->options->enable_header == 1) {
    $footer->html = gridboxHelper::checkModules($footer->html, $footer->items);
    $header->html = gridboxHelper::checkModules($header->html, $header->items);
    gridboxHelper::checkMoreScripts($footer->html, $time);
    gridboxHelper::checkMoreScripts($header->html, $time);
}
if ($error->options->enable_header == 1 && file_exists($path)) {
    $footer->html = baformsHelper::renderFormHTML($footer->html);
    $header->html = baformsHelper::renderFormHTML($header->html);
}
if (JVERSION >= '4.0.0') {
    $doc->addScript(JUri::root().'/media/vendor/jquery/js/jquery.min.js');
} else {
    $doc->addScript(JUri::root().'/media/jui/js/jquery.min.js');
}
$doc->addScript(JUri::root().'/components/com_gridbox/libraries/bootstrap/bootstrap.js');
$doc->addScriptDeclaration("var JUri = '".JUri::root()."';");
$doc->addScript($this->baseurl . '/templates/gridbox/js/gridbox.js?'.$aboutUs->version);
$doc->addScriptDeclaration("var themeData = ".json_encode($data).";");
$doc->addStyleSheet($this->baseurl . '/templates/gridbox/css/gridbox.css?'.$aboutUs->version);
$doc->addStyleSheet($this->baseurl . '/templates/gridbox/css/storage/responsive.css'.$time);
$doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/style-'.$id.'.css'.$time);
$doc->addStyleSheet(JUri::root().'templates/gridbox/css/storage/system-page-'.$error->id.'.css'.$time);
if (!empty($fonts)) {
    $doc->addStyleSheet($fonts);
}
$breakpoints = json_encode(gridboxHelper::$breakpoints);
$disable_responsive = gridboxHelper::$website->disable_responsive == 1 ? 'true' : 'false';
$doc->addScriptDeclaration("var breakpoints = ".$breakpoints.";");
$doc->addScriptDeclaration("var menuBreakpoint = ".gridboxHelper::$menuBreakpoint.";");
$doc->addScriptDeclaration("var disableResponsive = ".$disable_responsive.", gridboxVersion = '".$aboutUs->version."';");
$getItemsUrl = 'index.php?option=com_gridbox&task=editor.getItems&id='.$data['id'].'&theme='.$data['theme'].'&edit_type=system';
$getItemsUrl .= '&view='.$data['page']->view.'&'.str_replace('?', '', $time);
$doc->addScript(JUri::root().$getItemsUrl);
$stylesheets = gridboxHelper::returnSystemStyle($doc);
$favicon = gridboxHelper::getFavicon();
ob_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>"
    dir="<?php echo $this->direction; ?>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
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
</head>
<body class="com_gridbox page">
    <div class="ba-overlay"></div>
<?php
if ($error->options->enable_header == 1) {
?>
    <header class="header <?php echo $layout; ?>">
        <?php echo $header->html; ?>
    </header>
<?php
}
?>
    <div class="body">
        <div class="row-fluid main-body">
            <div class="ba-col-12">
<?php
if (JFactory::getUser()->authorise('core.edit', 'com_gridbox')) {
?>
            <a class="edit-page-btn" target="_blank"
               href="<?php echo JUri::root().'index.php?option=com_gridbox&view=editor&edit_type=system&tmpl=component&id=1'; ?>">
               <i class="zmdi zmdi-settings"></i>
               <span class="ba-tooltip ba-top"><?php echo JText::_('EDIT_PAGE'); ?></span>
            </a>
<?php
}
            echo $error->html;
?>
            </div>
        </div>
    </div>
<?php
if ($error->options->enable_header == 1) {
?>
    <footer class="footer">
        <?php echo $footer->html; ?>
    </footer>
<?php
}
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
$out = ob_get_contents();
ob_end_clean();
$out = gridboxHelper::compressGridbox($out);
$css = gridboxHelper::loadUsedCSS($out);
$out = str_replace('[gridbox-plugins-css]', $css, $out);
$icons = gridboxHelper::checkIconsLibrary($out);
if (!empty($icons)) {
    $out = str_replace('</head>', $icons.'</head>', $out);
}
$str = gridboxHelper::initItems($out);
if (!empty($str)) {
    $out = str_replace('</head>', $str."</head>", $out);
}

echo $out;