<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
if (!empty($this->edit_type)) {
?>
<script>
    themeData.edit_type = '<?php echo $this->edit_type; ?>';
<?php
    if ($this->edit_type == 'post-layout') {
?>
        themeData.app_type = '<?php echo $this->item->type; ?>';
<?php
    }
?>
</script>
<?php
}
?>

<?php
if (isset($this->item->app_type) && $this->item->app_type == 'blog') {
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.0/ckeditor.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/link.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/unlink.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/textColor.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/gridboxImage.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/gridboxVideo.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/gridboxPlugins.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/anchor.js"></script>
<script src="<?php echo JURI::root(); ?>components/com_gridbox/libraries/ckeditor/js/justifyLeft.js"></script>
<script>
top.document.body.classList.add('blog-post-editor-parent');
function createInlineCKE()
{
    $g('.content-text a[data-link]').removeAttr('data-cke-saved-href').each(function(){
        this.href = this.dataset.link;
    });
    $g('.content-text').each(function(){
        var a = CKEDITOR.dom.element.get(this);
        if (!a.getEditor()) {
            CKEDITOR.inline(this);
        }
    });
}
CKEDITOR.config.forcePasteAsPlainText = true;
CKEDITOR.disableAutoInline = true;
CKEDITOR.config.removePlugins = 'liststyle,tabletools,contextmenu';
CKEDITOR.config.uiColor = '#fafafa';
CKEDITOR.config.allowedContent = true;
CKEDITOR.config.removePlugins = 'image,magicline,link';
CKEDITOR.dtd.$removeEmpty.span = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;
CKEDITOR.config.toolbar_Basic = [
    {name: 'styles', items: ['Format']},
    {name: 'basicstyles', items: ['myTextColor', 'Bold', 'Italic', 'Underline']},
    {name: 'align', items: ['myJustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
    {name: 'margin', items: ['Outdent', 'Indent']},
    {name: 'links', items: ['myLink', 'myUnlink', 'myAnchor']},
    {name: 'lists', items: ['NumberedList', 'BulletedList']},
    {name: 'plugins', items: ['gridboxImage', 'gridboxVideo', 'gridboxPlugins']},
];
CKEDITOR.config.toolbar = 'Basic';
CKEDITOR.config.contentsCss = [
        JUri+'components/com_gridbox/libraries/ckeditor/css/ckeditor.css',
        'https://fonts.googleapis.com/css?family=Roboto'
    ];
document.addEventListener("DOMContentLoaded", function(){
    CKEDITOR.inline('editor1');
    createInlineCKE();
});
</script>
<div class="blog-post-editor-header-panel">
    <div class="post-editor-right-icons">
        <label class="gridbox-toggle-element">
            <input type="checkbox" class="advanced-blog-editor-toggle">
            <span></span>
            <span class="ba-tooltip ba-bottom"><?php echo JText::_('ADVANCED_EDITOR'); ?></span>
        </label>
    </div>
</div>
<script>
    if (localStorage.getItem('advanced-blog-editor') == 'true') {
        document.body.classList.add('advanced-blog-editor');
        document.querySelector('input.advanced-blog-editor-toggle').checked = true;
    }
</script>
<?php
}
?>
<script src="<?php echo JURI::root(). 'components/com_gridbox/libraries/sortable/sortable.js';?>"></script>
<script src="<?php echo JURI::root(). 'components/com_gridbox/libraries/columnResizer/columnResizer.js';?>"></script>
<script src="<?php echo JURI::root(). 'components/com_gridbox/assets/js/ba-grid.js'; ?>"></script>
<div id="global-css-sheets">
    <style></style>
</div>
<div id="custom-css-editor" data-enabled="<?php echo isset(gridboxHelper::$systemApps->{'code-editor'}); ?>">
    <style></style>
    <div class="custom-css-editor-code" style="display: none !important;"><?php echo $this->custom->code; ?></div>
</div>
<textarea id="code-css-value" style="display:none;"><?php echo $this->custom->code; ?></textarea>
<textarea id="code-js-value" style="display:none;"><?php echo $this->custom->js; ?></textarea>
<div class="notification-backdrop">
    <div class="ba-notification-message">
        <h4><?php echo JText::_('SELECT_END_POINT'); ?></h4>
        <p><?php echo JText::_('CLICK_TO_SELECT_END_POINT'); ?></p>
    </div>
    <div class="notification-placeholder"></div>
</div>
<div id="library-backdrop"></div>
<div id="library-placeholder" style="display: none;"><div></div></div>
<form method="post" id="ba-grid-form" enctype="multipart/form-data" data-emailprotector="{emailprotector=off}"
      action="<?php echo JUri::base() ?>index.php?option=com_gridbox&task=gridbox.save">
    <input type="hidden" name="grid_id" id="grid_id" value="<?php echo $this->item->id ?>">
    <input type="hidden" name="page_theme" id="page_theme" value="<?php echo $this->item->theme; ?>">
</form>
<div class="row-fluid">
    <div class="ba-edit-section row-fluid" id="ba-edit-section">
<?php
        echo $this->item->params;
?>
    </div>
    <div class="ba-add-section">
        <div><i class="zmdi zmdi-plus"></i></div>
        <span class="ba-tooltip add-section-tooltip">
            <?php echo JText::_('NEW_SECTION'); ?>
        </span>
    </div>
</div>
<div id="editor1" style="display: none !important;">
    
</div>