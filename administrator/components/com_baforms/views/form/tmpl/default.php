<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$formShortCodes = $this->get('FormShortCodes');
$formShortCodes->{'[Page Title]'} = $doc->title;
$formShortCodes->{'[Page URL]'} = JUri::root().'administrator/index.php?option=com_baforms&view=form&id=';
$formShortCodes->{'[Page ID]'} = $this->item->id;
$formShortCodes->{'[Form Title]'} = $this->item->title;
$formShortCodes->{'[Form ID]'} = $this->item->id;
$formsStateStr = baformsHelper::checkFormsActivation();
$formsState = json_decode($formsStateStr);
$design = json_decode($this->formSettings->design);
$navigation = json_decode($this->formSettings->navigation);
$designStr = baformsHelper::setDesignCssVariables($design);
$className2 = '';
if ($design->theme->layout == 'lightbox') {
    $className2 .= ' ba-forms-lightbox-enabled';
}
$className2 .= ' lightbox-position-'.$design->lightbox->position;
$className = 'fields-icons-'.$design->field->icon->{'text-align'};
$className .= !empty($design->theme->suffix) ? ' '.$design->theme->suffix : '';
$className .= !empty($design->lightbox->suffix) ? ' '.$design->lightbox->suffix : '';
if (count($this->pages) > 1) {
    $className .= ' visible-page-break';
}
if (!empty($navigation->style)) {
    $className .= ' '.$navigation->style;
}
if ($navigation->progress) {
    $className .= ' visible-save-progress';
}
$fonts = array();
if ($design->field->typography->{'font-family'} !='inherit') {
    $fonts[] = $design->field->typography->{'font-family'};
}
if ($design->label->typography->{'font-family'} !='inherit' && !in_array($design->label->typography->{'font-family'}, $fonts)) {
    $fonts[] = $design->label->typography->{'font-family'};
}
baformsHelper::$fonts = $fonts;
?>
<link rel="stylesheet" type="text/css" href="components/com_baforms/assets/css/ba-admin.css?<?php echo $this->about->version; ?>">
<link rel="stylesheet" type="text/css" href="components/com_baforms/assets/libraries/minicolors/css/minicolors.css">
<link rel="stylesheet" type="text/css" href="<?php echo JUri::root().'components/com_baforms/assets/icons/material/material.css'; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo JUri::root().'components/com_baforms/assets/icons/fontawesome/fontawesome.css'; ?>">

<style type="text/css">
body {
    <?php echo $designStr; ?>
}
</style>
<script type="text/javascript">
<?php
echo baformsHelper::getFormsLanguage();
?>
var integrations = <?php echo json_encode($this->integrations); ?>
</script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/bootstrap/bootstrap.js"></script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/minicolors/js/minicolors.js"></script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/sortable/sortable.js"></script>
<script src="//cdn.ckeditor.com/4.12.1/full/ckeditor.js"></script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/ckeditor/link.js"></script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/ckeditor/unlink.js"></script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/ckeditor/textColor.js"></script>
<script type="text/javascript" src="components/com_baforms/assets/libraries/ckeditor/dataTags.js"></script>
<script type="text/javascript" src="index.php?option=com_baforms&task=form.getFormOptions"></script>
<script type="text/javascript" src="index.php?option=com_baforms&task=form.getFormShortCodes"></script>
<script type="text/javascript" src="index.php?option=com_baforms&task=form.getRecaptchaData"></script>
<script type="text/javascript">
var $f = null,
    JUri = '<?php echo JUri::root(); ?>',
    googleFonts = <?php echo $this->googleFont; ?>;
CKEDITOR.disableAutoInline = true;
CKEDITOR.config.removePlugins = 'tableselection,liststyle,tabletools,contextmenu,magicline,image,link';
CKEDITOR.config.uiColor = '#fafafa';
CKEDITOR.config.allowedContent = true;
CKEDITOR.dtd.$removeEmpty.span = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;
CKEDITOR.config.toolbar_Basic = [
    {name: 'styles', items: ['Format']},
    {name: 'align', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight']},
    {name: 'color', items: ['myTextColor']},
    {name: 'basicstyles', items: ['Bold', 'Italic']},
    {name: 'links', items: ['myLink', 'myUnlink']},
    {name: 'lists', items: ['NumberedList', 'BulletedList']},
    {name: 'document', items: ['Source']}
];
CKEDITOR.config.toolbar = 'Basic';
CKEDITOR.config.contentsCss = [
    JUri+'administrator/components/com_baforms/assets/css/ba-admin.css'
];
<?php
if (!isset($formsState->data)) {
?>
document.body.classList.add('disabled-licence');
<?php
}
?>
</script>
<script type="text/javascript" src="<?php echo 'components/com_baforms/assets/js/ba-admin.js?'.$this->about->version ?>"></script>
<div class="preloader ba-preloader-slide">
    <div class="preloader-left-section"></div>
</div>
<div id="ba-notification">
    <i class="zmdi zmdi-close"></i>
    <h4><?php echo JText::_('ERROR'); ?></h4>
    <p></p>
</div>
<div class="ba-toolbar">
    <div class="ba-toolbar-group">
        <div class="ba-toolbar-element forms-save">
            <i class="zmdi zmdi-check"></i>
            <span class="ba-toolbar-label">
                <?php echo JText::_('JAPPLY'); ?>
            </span>
        </div>
    </div>
    <div class="ba-toolbar-group">
        <div class="ba-toolbar-element forms-close">
            <i class="zmdi zmdi-close"></i>
            <span class="ba-toolbar-label">
                <?php echo JText::_('CLOSE'); ?>
            </span>
        </div>
    </div>
    <div class="ba-toolbar-group">
        <div class="ba-toolbar-element">
            <input type="text" value="<?php echo $this->item->title; ?>" class="ba-toolbar-label ba-form-title"
                placeholder="<?php echo JText::_('ENTER_FORM_TITLE'); ?>">
            <span class="ba-alert-tooltip"><?php echo JText::_('THIS_FIELD_REQUIRED'); ?></span>
        </div>
    </div>
    <div class="ba-toolbar-group">
        <div class="ba-toolbar-element ba-design-editor close-all-modals">
            <i class="zmdi zmdi-format-color-fill"></i>
            <span class="ba-toolbar-label">
                <?php echo JText::_('DESIGN'); ?>
            </span>
        </div>
    </div>
</div>
<div class="ba-sidebar">
    <div class="top-icons">
        <span class="show-bootstrap-modal" data-modal="templates-modal">
            <a href="#">
                <span class="zmdi zmdi-assignment"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element">
                <?php echo JText::_('TEMPLATES'); ?>
            </span>
        </span>
        <span class="show-bootstrap-modal" data-modal="integration-modal">
            <a href="#">
                <span class="zmdi zmdi-cloud-done"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element">
                <?php echo JText::_('INTEGRATIONS'); ?>
            </span>
        </span>
        <span class="show-bootstrap-modal" data-modal="condition-logic-modal">
            <a href="#">
                <span class="zmdi zmdi-compass"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element">
                <?php echo JText::_('CONDITIONAL_LOGIC'); ?>
            </span>
        </span>
    </div>
    <div>
        <span class="show-media-manager">
            <a href="#">
                <span class="zmdi zmdi-folder"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element">
                <?php echo JText::_('MEDIA_MANAGER'); ?>
            </span>
        </span>
        <span class="ba-code-editor close-all-modals">
            <a href="#">
                <span class="zmdi zmdi-code-setting"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element">
                <?php echo JText::_('CODE_EDITOR'); ?>
            </span>
        </span>
    </div>
    <div class="bottom-icons">
        <span class="joomla-admin">
            <a href="<?php echo JUri::root().'administrator/index.php?option=com_baforms'; ?>" class="default-action" target="_blank">
                <span class="zmdi zmdi-home"></span>
            </a>
            <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('DASHBOARD'); ?></span>
        </span>
    </div>
</div>
<div class="ba-form-fields-list">
    <div class="ba-form-field" data-key="input">
        <i class="zmdi zmdi-crop-7-5"></i>
        <span><?php echo JText::_('INPUT'); ?></span>
    </div>
    <div class="ba-form-field" data-key="checkbox">
        <i class="zmdi zmdi-check"></i>
        <span><?php echo JText::_('CHECKBOX'); ?></span>
    </div>
    <div class="ba-form-field" data-key="radio">
        <i class="zmdi zmdi-dot-circle"></i>
        <span><?php echo JText::_('RADIO'); ?></span>
    </div>
    <div class="ba-form-field" data-key="select">
        <i class="zmdi zmdi-card"></i>
        <span><?php echo JText::_('DROPDOWN'); ?></span>
    </div>
    <div class="ba-form-field" data-key="selectMultiple">
        <i class="zmdi zmdi-storage"></i>
        <span><?php echo JText::_('SELECT_MULTIPLE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="upload">
        <i class="zmdi zmdi-cloud-upload"></i>
        <span><?php echo JText::_('UPLOAD_FILE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="slider">
        <i class="zmdi zmdi-tune"></i>
        <span><?php echo JText::_('SLIDER'); ?></span>
    </div>
    <div class="ba-form-field" data-key="calendar">
        <i class="zmdi zmdi-calendar-alt"></i>
        <span><?php echo JText::_('CALENDAR'); ?></span>
    </div>
    <div class="ba-form-field" data-key="phone">
        <i class="zmdi zmdi-phone-in-talk"></i>
        <span><?php echo JText::_('PHONE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="submit">
        <i class="zmdi zmdi-mail-send"></i>
        <span><?php echo JText::_('SUBMIT_BUTTON'); ?></span>
    </div>
    <div class="ba-form-field" data-key="rating">
        <i class="zmdi zmdi-star-half"></i>
        <span><?php echo JText::_('RATING'); ?></span>
    </div>
    <div class="ba-form-field" data-key="acceptance">
        <i class="zmdi zmdi-check-square"></i>
        <span><?php echo JText::_('ACCEPTANCE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="total">
        <i class="zmdi zmdi-shopping-basket"></i>
        <span><?php echo JText::_('CART'); ?></span>
    </div>
    <div class="ba-form-field" data-key="calculation">
        <i class="zmdi zmdi-functions"></i>
        <span><?php echo JText::_('CALCULATION'); ?></span>
    </div>
    <div class="ba-form-field" data-key="poll">
        <i class="zmdi zmdi-equalizer"></i>
        <span><?php echo JText::_('POLL'); ?></span>
    </div>
    <div class="ba-form-field<?php echo !empty($this->integrations->google_maps->key) ? '' : ' disabled-field-drag'; ?>" data-key="map">
        <i class="zmdi zmdi-map"></i>
        <span><?php echo JText::_('GOOGLE_MAPS'); ?></span>
        <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('INTEGRATE_GOOGLE_MAPS_APP'); ?></span>
    </div>
    <div class="ba-form-field<?php echo !empty($this->integrations->google_maps->key) ? '' : ' disabled-field-drag'; ?>" data-key="address">
        <i class="zmdi zmdi-pin"></i>
        <span><?php echo JText::_('AUTOCOMPLETE_ADDRESS'); ?></span>
        <span class="ba-tooltip ba-right ba-hide-element"><?php echo JText::_('INTEGRATE_GOOGLE_MAPS_APP'); ?></span>
    </div>
    <div class="ba-form-field" data-key="headline">
        <i class="zmdi zmdi-format-size"></i>
        <span><?php echo JText::_('HEADLINE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="text">
        <i class="zmdi zmdi-format-subject"></i>
        <span><?php echo JText::_('TEXT'); ?></span>
    </div>
    <div class="ba-form-field" data-key="image">
        <i class="zmdi zmdi-image-o"></i>
        <span><?php echo JText::_('IMAGE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="signature">
        <i class="zmdi zmdi-gesture"></i>
        <span><?php echo JText::_('SIGNATURE'); ?></span>
    </div>
    <div class="ba-form-field" data-key="html">
        <i class="zmdi zmdi-code-setting"></i>
        <span>HTML</span>
    </div>
</div>
<div class="ba-forms-workspace<?php echo $className2; ?>">
    <div id="library-backdrop"></div>
    <div class="sortable-backdrop"><div></div></div>
    <div class="ba-forms-workspace-lightbox-edit-icons">
        <i class="zmdi zmdi-invert-colors edit-lightbox-color close-all-modals" data-rgba="<?php echo $design->lightbox->color; ?>"></i>
        <i class="zmdi zmdi-settings edit-lightbox close-all-modals"></i>
    </div>
    <div class="ba-forms-workspace-body <?php echo $className; ?>">
<?php
    list($html, $appItems) = baformsHelper::drawPages($this->pages, $formShortCodes);
    echo $html;
?>
    </div>
    <div class="ba-forms-workspace-footer">
        <span class="add-new-page">
            <i class="zmdi zmdi-file"></i>
            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_NEW_PAGE'); ?></span>
        </span>
    </div>
</div>
<div id="integration-element-options-modal" class="ba-modal-cp modal hide">
    <div class="modal-header">
        <h3 class="ba-modal-title"></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="integration-options" data-group="google_maps">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="key">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="google_drive">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client ID</span>
                    <input type="text" class="get-google-drive-auth-url" data-key="client_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client Secret</span>
                    <input type="text" class="get-google-drive-auth-url" data-key="client_secret">
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-button-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('AUTHENTICATE'); ?></span>
                        <a href="#" target="_blank" class="default-action auth-drive-btn">
                            <?php echo JText::_('AUTHENTICATE_GOOGLE_ACCOUNT'); ?>
                        </a>
                    </div>
                    <div class="ba-settings-item ba-settings-input-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('AUTHENTICATION_CODE'); ?></span>
                        <input type="text" class="authenticate-google-drive" data-key="code">
                    </div>
                </div>
                <div class="ba-subgroup-element google-drive-folders">
                    <div class="ba-settings-item ba-settings-font-type">
                        <span class="ba-settings-item-title">
                            <?php echo JText::_('SELECT_FOLDER'); ?>
                        </span>
                        <div class="trigger-picker-modal" data-modal="drive-folders-dialog">
                            <input placeholder="<?php echo JText::_('SELECT_FOLDER'); ?>" type="text" readonly onfocus="this.blur()">
                            <input type="hidden" data-key="folder">
                        </div>
                    </div>
                </div>
                <div class="ba-subgroup-element google-drive-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('SEND_TO_GOOGLE_DRIVE'); ?></span>
                    <div class="ba-settings-item ba-settings-checkbox-type upload-pdf-to-drive">
                        <span class="ba-settings-item-title">
                            <?php echo JText::_('PDF_SUBMISSIONS'); ?>
                        </span>
                        <label class="ba-form-toggle">
                            <input type="checkbox" data-key="pdf" value="1">
                            <span></span>
                        </label>
                    </div>
                    <div class="ba-settings-item ba-settings-checkbox-type">
                        <span class="ba-settings-item-title">
                            <?php echo JText::_('ATTACHMENT_FILES'); ?>
                        </span>
                        <label class="ba-form-toggle">
                            <input type="checkbox" data-key="files" value="1">
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="google_sheets">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client ID</span>
                    <input type="text" class="get-google-sheets-auth-url" data-key="client_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client Secret</span>
                    <input type="text" class="get-google-sheets-auth-url" data-key="client_secret">
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-button-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('AUTHENTICATE'); ?></span>
                        <a href="#" target="_blank" class="default-action auth-sheets-btn">
                            <?php echo JText::_('AUTHENTICATE_GOOGLE_ACCOUNT'); ?>
                        </a>
                    </div>
                    <div class="ba-settings-item ba-settings-input-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('AUTHENTICATION_CODE'); ?></span>
                        <input type="text" class="authenticate-google-sheets" data-key="code">
                    </div>
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('SELECT_SPREADSHEET'); ?></span>
                        <select class="google-sheet-spreadsheets" data-key="spreadsheet">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('SELECT_WORKSHEET'); ?></span>
                        <select class="google-sheet-worksheets" data-key="worksheet">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
                <div class="ba-subgroup-element google-sheets-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="telegram">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Bot Token</span>
                    <input type="text" data-key="key">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="mollie">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="api_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="paypal">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Email</span>
                    <input type="text" data-key="email">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="paypal_sdk">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client ID</span>
                    <input type="text" data-key="client_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="payfast">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Merchant ID</span>
                    <input type="text" data-key="merchant_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Merchant Key</span>
                    <input type="text" data-key="merchant_key">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="pdf_submissions">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-checkbox-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('ACTIVATE'); ?>
                    </span>
                    <label class="ba-form-toggle">
                        <input type="checkbox" data-key="enable" value="1">
                        <span></span>
                    </label>
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-checkbox-type">
                        <span class="ba-settings-item-title">
                            <?php echo JText::_('INCLUDE_FORM_TITLE'); ?>
                        </span>
                        <label class="ba-form-toggle">
                            <input type="checkbox" data-key="title" value="1">
                            <span></span>
                        </label>
                    </div>
                    <div class="ba-settings-item ba-settings-checkbox-type">
                        <span class="ba-settings-item-title">
                            <?php echo JText::_('INCLUDE_EMPTY_FIELDS'); ?>
                        </span>
                        <label class="ba-form-toggle">
                            <input type="checkbox" data-key="empty" value="1">
                            <span></span>
                        </label>
                    </div>
                    <span class="ba-settings-group-title"><?php echo JText::_('PAGE_SETTINGS'); ?></span>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('ORIENTATION'); ?></span>
                        <select data-key="orientation">
                            <option value="" hidden></option>
                            <option value="Portrait"><?php echo JText::_('PORTRAIT'); ?></option>
                            <option value="Landscape"><?php echo JText::_('LANDSCAPE'); ?></option>
                        </select>
                    </div>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('SIZE'); ?></span>
                        <select data-key="size">
                            <option value="" hidden></option>
                            <option value="A3">A3</option>
                            <option value="A4">A4</option>
                            <option value="A5">A5</option>
                            <option value="Letter">Letter</option>
                            <option value="Legal">Legal</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="twocheckout">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Account Number</span>
                    <input type="text" data-key="account">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="authorize">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Login ID</span>
                    <input type="text" data-key="login_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Transaction Key</span>
                    <input type="text" data-key="transaction_key">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="cloudpayments">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Public ID</span>
                    <input type="text" data-key="public_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="stripe">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="api_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Secret Key</span>
                    <input type="text" data-key="secret_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="redsys">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Commerce Number (FUC)</span>
                    <input type="text" data-key="merchant">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Transaction Type</span>
                    <input type="text" data-key="transaction">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Terminal Number</span>
                    <input type="text" data-key="terminal">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Encryption Key (SHA-256)</span>
                    <input type="text" data-key="signature">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="robokassa">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Shop identifier</span>
                    <input type="text" data-key="shop_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Password #1</span>
                    <input type="text" data-key="password">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Tax</span>
                    <select data-key="tax">
                        <option value="" hidden></option>
                        <option value="none">Without VAT</option>
                        <option value="vat0">VAT at the rate of 0%</option>
                        <option value="vat10">VAT of the check at the rate of 10%</option>
                        <option value="vat110">VAT of the check at the estimated rate of 10/110</option>
                        <option value="vat20">VAT of the check at the rate of 20%</option>
                        <option value="vat120">VAT of the check at the estimated rate of 20/120</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="hcaptcha">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Site Key</span>
                    <input type="text" data-key="site_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Secret Key</span>
                    <input type="text" data-key="secret_key">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Theme</span>
                    <select data-key="theme">
                        <option value="" hidden></option>
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-checkbox-type">
                        <span class="ba-settings-item-title">
                            Invisible Captcha
                        </span>
                        <label class="ba-form-toggle">
                            <input type="checkbox" data-key="invisible" value="1">
                            <span></span>
                        </label>
                    </div>
            </div>
        </div>
        <div class="integration-options" data-group="liqpay">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Public Key</span>
                    <input type="text" data-key="public_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Private Key</span>
                    <input type="text" data-key="private_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="payupl">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">POS ID</span>
                    <input type="text" data-key="pos_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Second key</span>
                    <input type="text" data-key="second_key">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="payu_latam">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="api_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Merchant ID</span>
                    <input type="text" data-key="merchant_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Account ID</span>
                    <input type="text" data-key="account_id">
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Environment</span>
                    <select data-key="environment">
                        <option value="" hidden></option>
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="yandex_kassa">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Shop ID</span>
                    <input type="text" data-key="shop_id">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Secret Key</span>
                    <input type="text" data-key="secret_key">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Return URL</span>
                    <input type="text" data-key="return_url">
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="activecampaign">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API URL</span>
                    <input type="text" data-key="account" class="connect-activecampaign">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="api_key" class="connect-activecampaign">
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Select a List</span>
                        <select class="activecampaign-list" data-key="list">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
                <div class="ba-subgroup-element activecampaign-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Email</span>
                        <select class="forms-fields-list" data-key="email">
                            <option value="" hidden></option>
                        </select>
                    </div>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">First Name</span>
                        <select class="forms-fields-list" data-key="firstName">
                            <option value="" hidden></option>
                        </select>
                    </div>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Last Name</span>
                        <select class="forms-fields-list" data-key="lastName">
                            <option value="" hidden></option>
                        </select>
                    </div>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Phone Number</span>
                        <select class="forms-fields-list" data-key="phone">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="campaign_monitor">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="api_key" class="connect-campaign-monitor">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client ID</span>
                    <input type="text" data-key="client_id" class="connect-campaign-monitor">
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Select a List</span>
                        <select class="campaign-monitor-list connect-campaign-monitor" data-key="list_id">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
                <div class="ba-subgroup-element campaign-monitor-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="getresponse">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" data-key="api_key" class="connect-getresponse">
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Select a List</span>
                        <select class="getresponse-list connect-getresponse" data-key="list_id">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
                <div class="ba-subgroup-element getresponse-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                    <div class="getresponse-required-fields">
                        
                    </div>
                    <div class="ba-settings-item ba-settings-checkbox-type">
                        <span class="ba-settings-item-title">
                            <?php echo JText::_('ENABLE_CUSTOM_FIELDS'); ?>
                        </span>
                        <label class="ba-form-toggle">
                            <input type="checkbox" class="connect-getresponse" data-key="custom_fields" value="1">
                            <span></span>
                        </label>
                    </div>
                    <div class="ba-subgroup-element getresponse-custom-fields">
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="zoho_crm">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client Id</span>
                    <input type="text" data-key="client_id" class="connect-zoho-crm">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Client Secret</span>
                    <input type="text" data-key="client_secret" class="connect-zoho-crm">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Redirect URL</span>
                    <input type="text" disabled onfocus="this.blur()" value="<?php echo $this->get('ZohoRedirect'); ?>"
                        class="select-input field-id-input zoho-redirect-uri">
                    <div class="copy-to-clipboard input-action-icon">
                        <i class="zmdi zmdi-copy"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                    </div>
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-button-type">
                        <span class="ba-settings-item-title"><?php echo JText::_('AUTHENTICATE'); ?></span>
                        <a href="#" target="_blank" class="default-action auth-zoho-crm-btn">
                            <?php echo JText::_('AUTHENTICATE'); ?>
                        </a>
                    </div>
                </div>
                <div class="ba-subgroup-element zoho-crm-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="mailchimp">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">API Key</span>
                    <input type="text" class="mailchimp-api-key" data-key="api_key">
                </div>
                <div class="ba-subgroup-element">
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Select a List</span>
                        <select class="mailchimp-list" data-key="list">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
                <div class="ba-subgroup-element mailchimp-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                </div>
            </div>
        </div>
        <div class="integration-options" data-group="acymailing">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">Select a List</span>
                    <select data-key="list" class="set-group-display">
                        <option value="" hidden></option>
                        <?php echo baformsHelper::getAcymailingLists(); ?>
                    </select>
                </div>
                <div class="ba-subgroup-element acymailing-fields">
                    <span class="ba-settings-group-title"><?php echo JText::_('MATCH_YOUR_FORM_FIELDS'); ?></span>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Name</span>
                        <select class="forms-fields-list" data-key="name">
                            <option value="" hidden></option>
                        </select>
                    </div>
                    <div class="ba-settings-item ba-settings-select-type">
                        <span class="ba-settings-item-title">Email</span>
                        <select class="forms-fields-list" data-key="email">
                            <option value="" hidden></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL'); ?>
        </a>        
        <a href="#" class="ba-btn deactivate-integration-element">
            <?php echo JText::_('DEACTIVATE'); ?>
        </a>
        <a href="#" class="ba-btn apply-integration-element">
            <?php echo JText::_('JAPPLY'); ?>
        </a>
    </div>
</div>
<div id="design-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="basic-design-settings active">
        <div class="modal-header">
            <h3 class="ba-modal-title"><?php echo JText::_('FORM_DESIGN'); ?></h3>
            <div class="modal-header-icon">
                <i class="zmdi zmdi-close close-cp-modal"></i>
            </div>
        </div>
        <div class="modal-body">
            <div class="ba-settings-group">
                <span class="ba-settings-group-title">
                    <?php echo JText::_('COLOR_SCHEME'); ?>
                </span>
                <div class="ba-settings-item ba-settings-color-scheme-type">
                    <div class="color-scheme-item" data-scheme="scheme-1">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="scheme-2">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="scheme-3">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="scheme-4">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="scheme-5">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="scheme-6">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="scheme-7">
                        <div></div>
                        <div></div>
                    </div>
                    <div class="color-scheme-item" data-scheme="custom">
                        <i class="zmdi zmdi-palette"></i>
                    </div>
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('STYLE'); ?>
                    </span>
                    <select class="ba-form-style-select" data-group="theme" data-option="style">
                        <option value="default">Default</option>
                        <option value="rounded">Rounded</option>
                    </select>
                </div>
            </div>
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-range-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('WIDTH'); ?>
                    </span>
                    <div class="ba-range-wrapper">
                        <span class="ba-range-liner"></span>
                        <input type="range" class="ba-range" min="0" max="100">
                        <input type="number" data-group="form" data-subgroup="width" data-option="value">
                        <select class="ba-units-select" data-group="form" data-subgroup="units" data-option="width-value">
                            <option value="px">px</option>
                            <option value="%">%</option>
                        </select>
                    </div>
                </div>
                <div class="ba-settings-item ba-settings-select-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('LAYOUT'); ?>
                    </span>
                    <select class="ba-form-layout-select" data-group="theme" data-option="layout">
                        <option value=""><?php echo JText::_('DEFAULT'); ?></option>
                        <option value="lightbox"><?php echo JText::_('LIGHTBOX'); ?></option>
                    </select>
                </div>
            </div>
            <div class="ba-settings-group">
                <span class="ba-settings-group-title">
                    <?php echo JText::_('FONT'); ?>
                </span>
                <div class="ba-settings-item ba-settings-font-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('FONT'); ?>
                    </span>
                    <div class="trigger-picker-modal fonts-select" data-modal="google-fonts-dialog">
                        <input placeholder="<?php echo JText::_('FONT'); ?>" type="text" readonly onfocus="this.blur()">
                        <input type="hidden" data-group="theme" data-subgroup="typography" data-option="font-family">
                    </div>
                </div>
                <div class="ba-settings-item ba-settings-range-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('SIZE'); ?>
                    </span>
                    <div class="ba-range-wrapper">
                        <span class="ba-range-liner"></span>
                        <input type="range" class="ba-range" min="0" max="100">
                        <input type="number" data-group="theme" data-subgroup="typography" data-option="font-size">
                        <select class="ba-units-select" data-group="theme" data-subgroup="units" data-option="font-size">
                            <option value="px">px</option>
                            <option value="em">em</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-range-type">
                    <span class="ba-settings-item-title">
                        <?php echo JText::_('SPACING'); ?>
                    </span>
                    <div class="ba-range-wrapper">
                        <span class="ba-range-liner"></span>
                        <input type="range" class="ba-range" min="0" max="100">
                        <input type="number" data-group="theme" data-option="margin">
                        <span class="ba-units-value">px</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span class="design-settings-switcher" data-action="add">
                <i class="zmdi zmdi-settings"></i>
                <span><?php echo JText::_('ADVANCED_SETTINGS') ?></span>
            </span>
            <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
        </div>
    </div>
    <div class="advanced-design-settings">
        <div class="modal-header">
            <h3 class="ba-modal-title"><?php echo JText::_('ADVANCED_SETTINGS'); ?></h3>
            <div class="modal-header-icon">
                <i class="zmdi zmdi-close hide-design-settings" data-action="remove"></i>
            </div>
        </div>
        <div class="modal-body">
            <div class="general-tabs">
                <ul class="nav nav-tabs code-nav">
                    <li class="active">
                        <a href="#advanced-form-settings" data-toggle="tab">
                            <?php echo JText::_('FORM'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#advanced-label-settings" data-toggle="tab">
                            <?php echo JText::_('LABEL'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#advanced-field-settings" data-toggle="tab">
                            <?php echo JText::_('FIELD'); ?>
                        </a>
                    </li>
                </ul>
                <div class="tabs-underline"></div>
                <div class="tab-content">
                    <div id="advanced-form-settings" class="row-fluid tab-pane active">
                        <div class="accordion">
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('WIDTH'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>

                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('WIDTH'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="form" data-subgroup="width" data-option="value">
                                                <select class="ba-units-select" data-group="form" data-subgroup="units"
                                                    data-option="width-value">
                                                    <option value="px">px</option>
                                                    <option value="%">%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('FULLWIDTH'); ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="form" data-subgroup="width" data-option="fullwidth">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('BACKGROUND'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="form" data-subgroup="background"
                                                data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('SPACING'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-padding-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('PADDING'); ?>
                                            </span>
                                            <i class="zmdi zmdi-link spacing-link" data-group="form" data-subgroup="padding"></i>
                                            <div class="ba-settings-toolbar">
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('TOP'); ?>
                                                    </span>
                                                    <input type="number" data-group="form" data-subgroup="padding" data-option="top">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('RIGHT'); ?>
                                                    </span>
                                                    <input type="number" data-group="form" data-subgroup="padding" data-option="right">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('BOTTOM'); ?>
                                                    </span>
                                                    <input type="number" data-group="form" data-subgroup="padding" data-option="bottom">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('LEFT'); ?>
                                                    </span>
                                                    <input type="number" data-group="form" data-subgroup="padding" data-option="left">
                                                </div>
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('BORDER'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-inline-checkbox">
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('TOP'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="form" data-subgroup="border" data-option="top">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('RIGHT'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="form" data-subgroup="border" data-option="right">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('BOTTOM'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="form" data-subgroup="border" data-option="bottom">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('LEFT'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="form" data-subgroup="border" data-option="left">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('RADIUS'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="form" data-subgroup="border" data-option="radius">
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="form" data-subgroup="border"
                                                data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('WIDTH'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="form" data-subgroup="border" data-option="width">
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('SHADOW'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('VALUE'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="6">
                                                <input type="number" data-group="form" data-subgroup="shadow" data-option="value">
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="form" data-subgroup="shadow"
                                                data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('ADVANCED'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-input-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                            <input type="text" class="modify-item-suffix">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="advanced-label-settings" class="row-fluid tab-pane">
                        <div class="ba-settings-item ba-settings-font-type">
                            <span class="ba-settings-item-title">
                                <?php echo JText::_('FONT'); ?>
                            </span>
                            <div class="trigger-picker-modal fonts-select" data-modal="google-fonts-dialog">
                                <input placeholder="<?php echo JText::_('FONT'); ?>" type="text" readonly onfocus="this.blur()">
                                <input type="hidden" data-group="label" data-subgroup="typography" data-option="font-family">
                            </div>
                        </div>
                        <div class="ba-settings-item ba-settings-range-type">
                            <span class="ba-settings-item-title">
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="100">
                                <input type="number" data-group="label" data-subgroup="typography" data-option="font-size">
                                <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="font-size">
                                    <option value="px">px</option>
                                    <option value="em">em</option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-item ba-settings-range-type">
                            <span class="ba-settings-item-title">
                                <?php echo JText::_('LETTER_SPACING'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner letter-spacing"></span>
                                <input type="range" class="ba-range" min="-10" max="10">
                                <input type="number" data-group="label" data-subgroup="typography" data-option="letter-spacing">
                                <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="letter-spacing">
                                    <option value="px">px</option>
                                    <option value="em">em</option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-item ba-settings-range-type">
                            <span class="ba-settings-item-title">
                                <?php echo JText::_('LINE_HEIGHT'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="200">
                                <input type="number"  data-group="label" data-subgroup="typography" data-option="line-height">
                                <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="line-height">
                                    <option value="px">px</option>
                                    <option value="em">em</option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-item ba-settings-color-type">
                            <span class="ba-settings-item-title">
                                <?php echo JText::_('COLOR'); ?>
                            </span>
                            <input type="text" data-type="color" data-group="label" data-subgroup="typography" data-option="color">
                            <span class="minicolors-opacity-wrapper">
                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                            </span>
                        </div>
                        <div class="ba-settings-item ba-settings-typography-toolbar-type">
                            <div class="ba-settings-toolbar">
                                <div>
                                    <label data-option="font-weight" data-value="bold" data-default="normal" data-group="label"
                                        data-subgroup="typography" data-option="font-weight">
                                        <i class="zmdi zmdi-format-bold"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('BOLD'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-transform" data-value="uppercase" data-default="none" data-group="label"
                                        data-subgroup="typography" data-option="text-transform">
                                        <i class="zmdi zmdi-format-size"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('UPPERCASE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="font-style" data-value="italic" data-default="normal" data-group="label"
                                        data-subgroup="typography" data-option="font-style">
                                        <i class="zmdi zmdi-format-italic"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('ITALIC'); ?>
                                        </span>
                                    </label>
                                </div>
                                <div>
                                    <label data-value="left" data-group="label" data-subgroup="typography" data-option="text-align">
                                        <i class="zmdi zmdi-format-align-left"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('LEFT'); ?>
                                        </span>
                                    </label>
                                    <label data-value="center" data-group="label" data-subgroup="typography" data-option="text-align">
                                        <i class="zmdi zmdi-format-align-center"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('CENTER'); ?>
                                        </span>
                                    </label>
                                    <label data-value="right" data-group="label" data-subgroup="typography" data-option="text-align">
                                        <i class="zmdi zmdi-format-align-right"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('RIGHT'); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="advanced-field-settings" class="row-fluid tab-pane">
                        <div class="accordion">
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('BACKGROUND'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="field" data-subgroup="background"
                                                data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('SPACING'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-padding-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('PADDING'); ?>
                                            </span>
                                            <i class="zmdi zmdi-link spacing-link" data-group="field" data-subgroup="padding"></i>
                                            <div class="ba-settings-toolbar">
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('TOP'); ?>
                                                    </span>
                                                    <input type="number" data-group="field" data-subgroup="padding" data-option="top">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('RIGHT'); ?>
                                                    </span>
                                                    <input type="number" data-group="field" data-subgroup="padding" data-option="right">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('BOTTOM'); ?>
                                                    </span>
                                                    <input type="number" data-group="field" data-subgroup="padding" data-option="bottom">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('LEFT'); ?>
                                                    </span>
                                                    <input type="number" data-group="field" data-subgroup="padding" data-option="left">
                                                </div>
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-margin-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('MARGIN'); ?>
                                            </span>
                                            <i class="zmdi zmdi-link spacing-link" data-group="field" data-subgroup="margin"></i>
                                            <div class="ba-settings-toolbar">
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('TOP'); ?>
                                                    </span>
                                                    <input type="number" data-group="field" data-subgroup="margin" data-option="top">
                                                </div>
                                                <div>
                                                    <span>
                                                        <?php echo JText::_('BOTTOM'); ?>
                                                    </span>
                                                    <input type="number" data-group="field" data-subgroup="margin" data-option="bottom">
                                                </div>
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('BORDER'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-inline-checkbox">
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('TOP'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="field" data-subgroup="border" data-option="top">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('RIGHT'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="field" data-subgroup="border" data-option="right">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('BOTTOM'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="field" data-subgroup="border" data-option="bottom">
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div>
                                                <span class="ba-settings-item-title">
                                                    <?php echo JText::_('LEFT'); ?>
                                                </span>
                                                <label class="ba-form-toggle">
                                                    <input type="checkbox" data-group="field" data-subgroup="border" data-option="left">
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('RADIUS'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="field" data-subgroup="border" data-option="radius">
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="field" data-subgroup="border"
                                                data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('WIDTH'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="field" data-subgroup="border" data-option="width">
                                                <span class="ba-units-value">px</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('FONT'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-font-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('FONT'); ?>
                                            </span>
                                            <div class="trigger-picker-modal fonts-select" data-modal="google-fonts-dialog">
                                                <input placeholder="<?php echo JText::_('FONT'); ?>" type="text"
                                                    readonly onfocus="this.blur()">
                                                <input type="hidden" data-group="field" data-subgroup="typography" data-option="font-family">
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('SIZE'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="field" data-subgroup="typography" data-option="font-size">
                                                <select class="ba-units-select" data-group="field" data-subgroup="units"
                                                    data-option="font-size">
                                                    <option value="px">px</option>
                                                    <option value="em">em</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('LETTER_SPACING'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner letter-spacing"></span>
                                                <input type="range" class="ba-range" min="-10" max="10">
                                                <input type="number" data-group="field" data-subgroup="typography"
                                                    data-option="letter-spacing">
                                                <select class="ba-units-select" data-group="field" data-subgroup="units"
                                                    data-option="letter-spacing">
                                                    <option value="px">px</option>
                                                    <option value="em">em</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('LINE_HEIGHT'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="200">
                                                <input type="number" data-group="field" data-subgroup="typography"
                                                    data-option="line-height">
                                                <select class="ba-units-select" data-group="field" data-subgroup="units"
                                                    data-option="line-height">
                                                    <option value="px">px</option>
                                                    <option value="em">em</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="field" data-subgroup="typography"
                                                data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                        <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                            <div class="ba-settings-toolbar">
                                                <div>
                                                    <label data-option="font-weight" data-value="bold" data-default="normal"
                                                        data-group="field" data-subgroup="typography" data-option="font-weight">
                                                        <i class="zmdi zmdi-format-bold"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('BOLD'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-option="text-transform" data-value="uppercase" data-default="none"
                                                        data-group="field" data-subgroup="typography" data-option="text-transform">
                                                        <i class="zmdi zmdi-format-size"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('UPPERCASE'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-option="font-style" data-value="italic" data-default="normal"
                                                        data-group="field" data-subgroup="typography" data-option="font-style">
                                                        <i class="zmdi zmdi-format-italic"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('ITALIC'); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div>
                                                    <label data-value="left" data-group="field"
                                                        data-subgroup="typography" data-option="text-align">
                                                        <i class="zmdi zmdi-format-align-left"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('LEFT'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-value="center" data-group="field"
                                                        data-subgroup="typography" data-option="text-align">
                                                        <i class="zmdi zmdi-format-align-center"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('CENTER'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-value="right" data-group="field"
                                                        data-subgroup="typography" data-option="text-align">
                                                        <i class="zmdi zmdi-format-align-right"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('RIGHT'); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse">
                                        <?php echo JText::_('ICON'); ?>
                                        <i class="zmdi zmdi-caret-right"></i>
                                    </a>
                                </div>
                                <div class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="ba-settings-item ba-settings-range-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('SIZE'); ?>
                                            </span>
                                            <div class="ba-range-wrapper">
                                                <span class="ba-range-liner"></span>
                                                <input type="range" class="ba-range" min="0" max="100">
                                                <input type="number" data-group="field" data-subgroup="icon" data-option="size">
                                                <select class="ba-units-select" data-group="field" data-subgroup="units"
                                                    data-option="size">
                                                    <option value="px">px</option>
                                                    <option value="em">em</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-color-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('COLOR'); ?>
                                            </span>
                                            <input type="text" data-type="color" data-group="field" data-subgroup="icon" data-option="color">
                                            <span class="minicolors-opacity-wrapper">
                                                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                            </span>
                                        </div>
                                        <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                            <div class="ba-settings-toolbar">
                                                <div>
                                                    <label data-value="flex-start" data-group="field"
                                                        data-subgroup="icon" data-option="text-align">
                                                        <i class="zmdi zmdi-format-align-left"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('LEFT'); ?>
                                                        </span>
                                                    </label>
                                                    <label data-value="flex-end" data-group="field"
                                                        data-subgroup="icon" data-option="text-align">
                                                        <i class="zmdi zmdi-format-align-right"></i>
                                                        <span class="ba-tooltip ba-top ba-hide-element">
                                                            <?php echo JText::_('RIGHT'); ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span class="design-settings-switcher" data-action="remove">
                <i class="zmdi zmdi-settings"></i>
                <span><?php echo JText::_('BASIC_SETTINGS') ?></span>
            </span>
            <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
        </div>
    </div>
</div>
<div id="bulk-adding-dialog" class="ba-modal-cp modal hide hidden-modal-backdrop picker-modal-arrow">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('BULK_ADDING'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-check apply-bulk-items"></i>
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="ba-settings-group">
            <div class="ba-settings-item ba-settings-textarea-type">
                <textarea placeholder="<?php echo JText::_('ENTER_ONE_OPTION_PER_LINE'); ?>"></textarea>
            </div>
        </div>
    </div>
</div>
<div id="default-value-dialog" class="ba-modal-cp modal hide hidden-modal-backdrop picker-modal-arrow">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('DATA_TAGS'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="default-value-searchbar">
            <div class="ba-settings-group">
                <div class="ba-settings-item ba-settings-select-type">
                    <select class="select-data-tags-type">
                        <option value=""><?php echo JText::_('All'); ?></option>
                        <option value="page"><?php echo JText::_('PAGE'); ?></option>
                        <option value="form"><?php echo JText::_('FORM'); ?></option>
                        <option value="fields"><?php echo JText::_('FIELDS'); ?></option>
                        <option value="user"><?php echo JText::_('USER'); ?></option>
                        <option value="other"><?php echo JText::_('OTHER'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="">
            <div class="ba-settings-group page-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('PAGE_TITLE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page Title]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('PAGE_ID'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page ID]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('PAGE_URL'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Page URL]">
                </div>
            </div>
            <div class="ba-settings-group form-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('FORM_TITLE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Form Title]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('FORM_ID'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Form ID]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('SUBMISSION_ID'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Submission ID]">
                </div>
            </div>
            <div class="ba-settings-group fields-data-tags">
                <div class="ba-settings-item ba-settings-input-type all-fields-tag">
                    <span class="ba-settings-item-title"><?php echo JText::_('ALL_FIELDS'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[All Fields]">
                </div>
            </div>
            <div class="ba-settings-group user-data-tags">
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Joomla Username</span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Username]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Joomla <?php echo JText::_('USER_NAME'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[User Name]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Joomla <?php echo JText::_('USER_EMAIL'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[User Email]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">Joomla User ID</span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[User ID]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('USER_IP_ADDRESS'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[User IP Address]">
                </div>
            </div>
            <div class="ba-settings-group other-data-tags">
            	<div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('TIME'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Time]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('TIME_AM_PM'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Time AM / PM]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('DATE'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[Date]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title"><?php echo JText::_('SQL_QUERY'); ?></span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[SQL query = SELECT]">
                </div>
                <div class="ba-settings-item ba-settings-input-type">
                    <span class="ba-settings-item-title">URL parameters</span>
                    <input type="text" readonly onfocus="this.blur()" class="select-input" value="[URL parameter = Key]">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="select-icon-dialog" class="ba-modal-cp modal hide hidden-modal-backdrop picker-modal-arrow">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('ICONS'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <iframe src="index.php?option=com_baforms&view=icons&tmpl=component"></iframe>
    </div>
</div>
<div id="code-editor-dialog" class="ba-modal-lg modal hide hidden-modal-backdrop code-editor-dialog" style="display: none;">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('CODE_EDITOR'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close" data-dismiss="modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs code-nav">
                <li class="active">
                    <a href="#code-edit-css" data-toggle="tab">
                        css
                    </a>
                </li>
                <li>
                    <a href="#code-edit-javascript" data-toggle="tab">
                        javascript
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="code-edit-css" class="row-fluid tab-pane active">
                    <textarea id="code-editor-css"></textarea>
                    <span></span>
                </div>
                <div id="code-edit-javascript" class="row-fluid tab-pane">
                    <textarea id="code-editor-javascript"></textarea>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
    <i class="zmdi zmdi-format-valign-center resizable-handle-right resizable-handle" data-direction="right"></i>
</div>
<div id="html-editor-dialog" class="ba-modal-lg modal hide hidden-modal-backdrop code-editor-dialog" style="display: none;">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('CODE_EDITOR'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <div class="tab-content">
                <div id="edit-custom-html" class="row-fluid tab-pane active">
                    <textarea id="custom-html-editor"></textarea>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
    <i class="zmdi zmdi-format-valign-center resizable-handle-right resizable-handle" data-direction="right"></i>
</div>
<div id="image-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('IMAGE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('SOURCE'); ?></span>
                                <input type="text" readonly onfocus="this.blur()" class="select-input select-image" data-option="src"
                                    data-callback="imageFieldAction">
                                <div class="input-click-trigger input-action-icon">
                                    <i class="zmdi zmdi-camera"></i>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('ALT'); ?></span>
                                <input type="text" data-option="alt" data-callback="imageFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('WIDTH'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="100">
                                    <input type="number" data-option="width" data-callback="imageFieldAction">
                                    <select class="ba-units-select" data-group="units" data-option="width" data-callback="imageFieldAction">
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-value="left" data-option="align" data-callback="imageFieldAction">
                                            <i class="zmdi zmdi-format-align-left"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('LEFT'); ?>
                                            </span>
                                        </label>
                                        <label data-value="center" data-option="align" data-callback="imageFieldAction">
                                            <i class="zmdi zmdi-format-align-center"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('CENTER'); ?>
                                            </span>
                                        </label>
                                        <label data-value="right" data-option="align" data-callback="imageFieldAction">
                                            <i class="zmdi zmdi-format-align-right"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('RIGHT'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('ADMIN_LABEL'); ?></span>
                                <input type="text" data-option="admin-label" data-callback="imageFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="calendar-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('CALENDAR'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="calendarFieldAction">
                                    <option value=""><?php echo JText::_('REGULAR'); ?></option>
                                    <option value="range"><?php echo JText::_('RANGE'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="calendarFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('PLACEHOLDER'); ?></span>
                                <input type="text" data-option="placeholder" data-callback="calendarFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                <select data-option="default" data-callback="calendarFieldAction">
                                    <option value=""><?php echo JText::_('NO_NE'); ?></option>
                                    <option value="today"><?php echo JText::_('TODAY'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="calendarFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-inline-checkbox">
                                <div>
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('REQUIRED'); ?>
                                    </span>
                                    <label class="ba-form-toggle">
                                        <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                        <span></span>
                                    </label>
                                </div>
                                <div>
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('READ_ONLY'); ?>
                                    </span>
                                    <label class="ba-form-toggle">
                                        <input type="checkbox" data-option="readonly" data-callback="calendarFieldAction">
                                        <span></span>
                                    </label>
                                </div>
                                <div>
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('HIDDEN'); ?>
                                    </span>
                                    <label class="ba-form-toggle">
                                        <input type="checkbox" data-option="hidden" data-callback="inputFieldAction">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIRST_DAY_OF_WEEK'); ?></span>
                                <select data-option="start" data-callback="calendarFieldAction">
                                    <option value="0"><?php echo JText::_('SUNDAY'); ?></option>
                                    <option value="1"><?php echo JText::_('MONDAY'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DISABLE_PAST_DATES'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-group="disable" data-option="previous" data-callback="calendarFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DISABLE_SPECIFIC_WEEK_DAYS'); ?>
                                </span>
                                <div class="selected-dates-wrapper">
                                    <div class="selected-input-wrapper">
                                        <input type="text" readonly onfocus="this.blur()" class="select-input open-disable-days-dialog"
                                            data-callback="setDisableDates">
                                        <div class="input-action-icon input-click-trigger">
                                            <i class="zmdi zmdi-playlist-plus"></i>
                                        </div>
                                    </div>
                                    <div class="selected-dates-tags" data-group="disable" data-option="days"></div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DISABLE_SPECIFIC_DATES'); ?>
                                </span>
                                <div class="selected-dates-wrapper">
                                    <div class="selected-input-wrapper">
                                        <input type="text" readonly onfocus="this.blur()" class="select-input open-calendar-dialog"
                                            data-callback="setDisableDates">
                                        <div class="input-action-icon input-click-trigger">
                                            <i class="zmdi zmdi-calendar-alt"></i>
                                        </div>
                                    </div>
                                    <div class="selected-dates-tags" data-group="disable" data-option="dates"></div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DISABLE_RANGE_DATES'); ?>
                                </span>
                                <div class="selected-dates-wrapper disable-range-dates">
                                    <div class="selected-input-wrapper">
                                        <input type="text" readonly onfocus="this.blur()" class="select-input open-calendar-dialog"
                                            data-callback="setDisableDatesRange" data-index="0">
                                        <div class="input-action-icon input-click-trigger">
                                            <i class="zmdi zmdi-calendar-alt"></i>
                                        </div>
                                    </div>
                                    <span class="number-delimiter">-</span>
                                    <div class="selected-input-wrapper">
                                        <input type="text" readonly onfocus="this.blur()" class="select-input open-calendar-dialog"
                                            data-callback="setDisableDatesRange" data-index="1">
                                        <div class="input-action-icon input-click-trigger">
                                            <i class="zmdi zmdi-calendar-alt"></i>
                                        </div>
                                    </div>
                                    <div class="selected-dates-tags" data-group="disable" data-option="range-dates"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="signature-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('SIGNATURE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="calendarFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="calendarFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type email-type-options" style="">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="lightbox-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('LIGHTBOX'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('TRIGGER'); ?>
                                </span>
                                <select data-group="trigger" data-option="type" data-callback="lightboxAction">
                                    <option value=""><?php echo JText::_('CLICK'); ?></option>
                                    <option value="time-delay"><?php echo JText::_('TIME_DELAY'); ?></option>
                                    <option value="scrolling"><?php echo JText::_('SCROLLING'); ?></option>
                                    <option value="bottom-of-page"><?php echo JText::_('BOTTOM_OF_PAGE'); ?></option>
                                    <option value="exit-intent"><?php echo JText::_('EXIT_INTENT'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type lightbox-trigger-options" data-trigger="">
                                <span class="ba-settings-item-title"><?php echo JText::_('EMBED_CODE'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input"
                                    value="ba-click-lightbox-form-<?php echo $this->item->id; ?>">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-number-type lightbox-trigger-options" data-trigger="time-delay">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('TIME_DELAY'); ?>, ms
                                </span>
                                <input type="number" data-group="trigger" data-option="time" data-callback="lightboxAction">
                            </div>
                            <div class="ba-settings-item ba-settings-number-type lightbox-trigger-options" data-trigger="scrolling">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('PERCENTAGE'); ?>, %
                                </span>
                                <input type="number" data-group="trigger" data-option="scroll" data-callback="lightboxAction">
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type lightbox-trigger-options lightbox-session-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('SHOW_ONCE_PER_SESSION'); ?></span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-group="session" data-option="enable" data-callback="lightboxAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-number-type lightbox-session-duration">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('SESSION_DURATION'); ?>
                                </span>
                                <input type="number" data-group="session" data-option="duration" data-callback="lightboxAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('DISPLAY'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-lightbox-position-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('POSITION'); ?>
                                </span>
                                <div class="lightbox-position-wrapper">
                                    <div data-value="top-left"></div>
                                    <div data-value="top-center"></div>
                                    <div data-value="top-right"></div>
                                    <div data-value="center-left"></div>
                                    <div data-value="center"></div>
                                    <div data-value="center-right"></div>
                                    <div data-value="bottom-left"></div>
                                    <div data-value="bottom-center"></div>
                                    <div data-value="bottom-right"></div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('ANIMATION'); ?>
                                </span>
                                <select data-option="animation" data-callback="lightboxAction">
                                    <option value="ba-forms-lightbox-effect-1">Fade In</option>
                                    <option value="ba-forms-lightbox-effect-8">Fall In</option>
                                    <option value="ba-forms-lightbox-effect-9">Flip Horizontal</option>
                                    <option value="ba-forms-lightbox-effect-10">Flip Vertical</option>
                                    <option value="ba-forms-lightbox-effect-2">Scale In</option>
                                    <option value="ba-forms-lightbox-effect-6">Slide In Bottom</option>
                                    <option value="ba-forms-lightbox-effect-3">Slide In Left</option>
                                    <option value="ba-forms-lightbox-effect-4">Slide In Right</option>
                                    <option value="ba-forms-lightbox-effect-5">Slide In Top</option>
                                    <option value="ba-forms-lightbox-effect-7">Spinner</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="upload-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('UPLOAD_FILE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="uploadFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="uploadFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DRAG_AND_DROP'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="drag" data-callback="uploadFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FILE_TYPES'); ?></span>
                                <input type="text" data-option="types" data-callback="uploadFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-number-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('MAX_UPLOAD_FILE_SIZE'); ?>
                                </span>
                                <input type="number" data-option="filesize" data-callback="uploadFieldAction">
                                <span class="ba-units-value">MB.</span>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('ALLOW_MULTIPLE_FILES'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="multiple" data-callback="uploadFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-number-type multiple-upload-options">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('MAX_NUMBER_OF_FILES'); ?>
                                </span>
                                <input type="number" data-option="count" data-callback="uploadFieldAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="slider-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('SLIDER'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="sliderFieldAction">
                                    <option value="range"><?php echo JText::_('SLIDER'); ?></option>
                                    <option value="slider"><?php echo JText::_('RANGE'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-number-type ">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('RANGE'); ?>
                                </span>
                                <input type="number" data-option="min" data-callback="sliderFieldAction">
                                <span class="number-delimiter">-</span>
                                <input type="number" data-option="max" data-callback="sliderFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-number-type ">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('STEP'); ?>
                                </span>
                                <input type="number" data-option="step" data-callback="sliderFieldAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="select-multiple-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('SELECT_MULTIPLE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ITEMS'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="default">
                                            <i class="zmdi zmdi-star"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DEFAULT_VALUE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                                <span>
                                    <i class="zmdi zmdi-playlist-plus bulk-adding-items"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('BULK_ADDING'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('REGULAR'); ?></option>
                                    <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="google-maps-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('GOOGLE_MAPS'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('ADDRESS'); ?></span>
                                <input type="text" data-option="place" data-callback="mapFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-map-type">
                                <div class="ba-address-map-wrapper"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('INFOBOX'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('UPLOAD_MARKER'); ?></span>
                                <input type="text" readonly onfocus="this.blur()" class="select-input select-image" data-option="icon"
                                    data-group="marker" data-callback="mapFieldAction">
                                <div class="reset input-action-icon">
                                    <i class="zmdi zmdi-close"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DISPLAY_INFOBOX'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="infobox" data-group="marker" data-callback="mapFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-text-editor-type">
                                <textarea id="marker-description-editor" class="text-editor-textarea" data-option="description"
                                    data-group="marker" data-callback="mapFieldAction"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('HEIGHT'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="1440">
                                    <input type="number" data-option="height" data-callback="mapFieldAction">
                                    <span class="ba-units-value">px</span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title">
                                <?php echo JText::_('THEME'); ?>
                                </span>
                                <select data-option="styleType" data-callback="mapFieldAction">
                                    <option value="standart">Standard</option>
                                    <option value="silver">Silver</option>
                                    <option value="retro">Retro</option>
                                    <option value="dark">Dark</option>
                                    <option value="night">Night</option>
                                    <option value="aubergine">Aubergine</option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('CONTROLS'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="controls" data-callback="mapFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('SCROLL_ZOOMING'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="scrollwheel" data-group="map" data-callback="mapFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DRAGGABLE_MAP'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="draggable" data-group="map" data-callback="mapFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="dropdown-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('DROPDOWN'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ITEMS'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="default">
                                            <i class="zmdi zmdi-star"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DEFAULT_VALUE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                                <span>
                                    <i class="zmdi zmdi-playlist-plus bulk-adding-items"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('BULK_ADDING'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('REGULAR'); ?></option>
                                    <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('PLACEHOLDER'); ?></span>
                                <input type="text" data-option="placeholder" data-callback="selectFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="navigation-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('NAVIGATION'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ITEMS'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('TYPE'); ?>
                                </span>
                                <select data-option="style" data-callback="navigationAction">
                                    <option value="hidden-navigation-style"><?php echo JText::_('NO_NE'); ?></option>
                                    <option value="dots-navigation-style"><?php echo JText::_('DOTS'); ?></option>
                                    <option value="step-navigation-style"><?php echo JText::_('STEPS'); ?></option>
                                    <option value="progress-navigation-style"><?php echo JText::_('PROGRESS_BAR'); ?></option>
                                    <option value=""><?php echo JText::_('TRIANGLES'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('AUTOMATIC_PAGE_NAVIGATION'); ?></span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="auto" data-callback="navigationAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('SAVE_PROGRESS'); ?></span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="progress" data-callback="navigationAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="checkbox-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('CHECKBOX'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ITEMS'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="default">
                                            <i class="zmdi zmdi-star"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DEFAULT_VALUE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="image">
                                            <i class="zmdi zmdi-image-o"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('IMAGE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                                <span>
                                    <i class="zmdi zmdi-playlist-plus bulk-adding-items"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('BULK_ADDING'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('REGULAR'); ?></option>
                                    <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-number-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('NUMBER_OF_COLUMNS'); ?>
                                </span>
                                <input type="number" data-option="count" data-callback="checkboxFieldAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="poll-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('POLL'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ITEMS'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="default">
                                            <i class="zmdi zmdi-star"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DEFAULT_VALUE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="image">
                                            <i class="zmdi zmdi-image-o"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('IMAGE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                                <span>
                                    <i class="zmdi zmdi-playlist-plus bulk-adding-items"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('BULK_ADDING'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('ALLOW_MULTIPLE_VOTES'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="multiple" data-callback="pollFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('ALLOW_VOTING_AGAIN'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="again" data-callback="pollFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('SHOW_VOTE_COUNT'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="vote-count" data-callback="pollFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('CLOSE_POLL'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="close" data-callback="pollFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('SET_END_DATE'); ?>
                                </span>
                                <div class="selected-dates-wrapper">
                                    <div class="selected-input-wrapper">
                                        <input type="text" readonly onfocus="this.blur()"
                                            class="select-input open-calendar-dialog"
                                            data-callback="setDateValue" data-option="end">
                                        <div class="input-action-icon input-click-trigger">
                                            <i class="zmdi zmdi-playlist-plus"></i>
                                        </div>
                                        <div class="reset input-action-icon">
                                            <i class="zmdi zmdi-close"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('RESET'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-number-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('NUMBER_OF_COLUMNS'); ?>
                                </span>
                                <input type="number" data-option="count" data-callback="checkboxFieldAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="radio-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('RADIO'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ITEMS'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="default">
                                            <i class="zmdi zmdi-star"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DEFAULT_VALUE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="image">
                                            <i class="zmdi zmdi-image-o"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('IMAGE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                                <span>
                                    <i class="zmdi zmdi-playlist-plus bulk-adding-items"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('BULK_ADDING'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('REGULAR'); ?></option>
                                    <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-number-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('NUMBER_OF_COLUMNS'); ?>
                                </span>
                                <input type="number" data-option="count" data-callback="checkboxFieldAction">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="submit-button-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('SUBMIT_BUTTON'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs code-nav">
                <li class="active">
                    <a href="#submit-button-general-settings" data-toggle="tab">
                        <?php echo JText::_('SETTINGS'); ?>
                    </a>
                </li>
                <li>
                    <a href="#submit-button-design-settings" data-toggle="tab">
                        <?php echo JText::_('DESIGN'); ?>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="submit-button-general-settings" class="row-fluid tab-pane active">
                    <div class="accordion">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('GENERAL'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse in">
                                <div class="accordion-inner">
                                    <div class="ba-settings-group">
                                        <div class="ba-settings-item ba-settings-input-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                            <input type="text" data-option="label" data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('ICON'); ?></span>
                                            <input type="text" readonly onfocus="this.blur()" class="select-input select-icon"
                                                data-option="submit-icon" data-callback="submitFieldAction">
                                            <div class="reset input-action-icon">
                                                <i class="zmdi zmdi-close"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element">
                                                    <?php echo JText::_('RESET'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-select-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('ON_CLICK'); ?></span>
                                            <select data-option="onclick" data-callback="submitFieldAction">
                                                <option value="message"><?php echo JText::_('THANK_YOU_MESSAGE'); ?></option>
                                                <option value="redirect"><?php echo JText::_('REDIRECT'); ?></option>
                                                <option value="payment"><?php echo JText::_('PAYMENT_GATEWAY'); ?></option>
                                            </select>
                                        </div>
                                        <div class="ba-settings-item ba-settings-select-type message-options">
                                            <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                            <select data-option="message-type" data-callback="submitFieldAction">
                                                <option value=""><?php echo JText::_('DEFAULT'); ?></option>
                                                <option value="advanced"><?php echo JText::_('ADVANCED'); ?></option>
                                            </select>
                                        </div>
                                        <div class="ba-settings-item ba-settings-text-editor-type message-options advanced-message-options">
                                            <textarea id="advanced-message-editor" class="text-editor-textarea"
                                                data-option="advanced-message" data-callback="submitFieldAction"></textarea>
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type message-options default-message-options">
                                            <span class="ba-settings-item-title"><?php echo JText::_('MESSAGE'); ?></span>
                                            <input type="text"  data-option="message" data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type redirect-options">
                                            <span class="ba-settings-item-title"><?php echo JText::_('LINK'); ?></span>
                                            <input type="text" data-option="link" data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-select-type payment-options">
                                            <span class="ba-settings-item-title"><?php echo JText::_('PAYMENT_GATEWAY'); ?></span>
                                            <select data-option="payment" data-callback="submitFieldAction">
                                                <option><?php echo JText::_('NO_NE'); ?></option>
<?php
                                            $customPayments = baformsHelper::getCustomPayments();
                                            foreach ($customPayments as $customPayment) {
?>
                                                <option value="custom-payment-<?php echo $customPayment->class; ?>">
                                                    <?php echo $customPayment->label; ?>
                                                </option>
<?php
                                            }
?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('ADMIN_EMAIL_NOTIFICATIONS'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-group">
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('SEND_TO_ADMIN') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="notifications" data-option="enable"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                            <span class="ba-settings-item-title"><?php echo JText::_('ADMIN_EMAIL'); ?></span>
                                            <div class="selected-dates-wrapper">
                                                <div class="selected-input-wrapper">
                                                    <input type="text" class="add-notifications-admin-emails"
                                                        placeholder="<?php echo JText::_('ADD_EMAIL_PRESS_ENTER') ?>">
                                                </div>
                                                <div class="selected-dates-tags" data-group="notifications" data-option="admin"></div>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                            <span class="ba-settings-item-title">Cc</span>
                                            <div class="selected-dates-wrapper">
                                                <div class="selected-input-wrapper">
                                                    <input type="text" class="add-notifications-admin-emails"
                                                        placeholder="<?php echo JText::_('ADD_EMAIL_PRESS_ENTER') ?>">
                                                </div>
                                                <div class="selected-dates-tags" data-group="notifications" data-option="cc"></div>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type ba-settings-selected-dates-wrapper">
                                            <span class="ba-settings-item-title">Bcc</span>
                                            <div class="selected-dates-wrapper">
                                                <div class="selected-input-wrapper">
                                                    <input type="text" class="add-notifications-admin-emails"
                                                        placeholder="<?php echo JText::_('ADD_EMAIL_PRESS_ENTER') ?>">
                                                </div>
                                                <div class="selected-dates-tags" data-group="notifications" data-option="bcc"></div>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-select-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('FROM_EMAIL'); ?></span>
                                            <select data-group="notifications" data-option="email" data-callback="submitFieldAction">
                                                <option value=""><?php echo JText::_('DEFAULT'); ?></option>
                                                <option value="customer-email"><?php echo JText::_('CUSTOMER_EMAIL'); ?></option>
                                                <option value="custom"><?php echo JText::_('CUSTOM'); ?></option>
                                            </select>
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type notifications-custom-email">
                                            <span class="ba-settings-item-title"><?php echo JText::_('NAME'); ?></span>
                                            <input type="text" data-group="notifications" data-option="custom-name"
                                                data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type notifications-custom-email">
                                            <span class="ba-settings-item-title"><?php echo JText::_('EMAIL'); ?></span>
                                            <input type="text" data-group="notifications" data-option="custom-email"
                                                data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('SUBJECT'); ?></span>
                                            <input type="text" data-group="notifications" data-option="subject"
                                                data-callback="submitFieldAction">
                                            <div class="select-default-value input-action-icon">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-text-editor-type">
                                            <textarea id="notification-email-body-editor" class="text-editor-textarea"
                                                data-group="notifications" data-option="body" data-callback="submitFieldAction"></textarea>
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type pdf-attachment-option">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('PDF_ATTACHMENT') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="notifications" data-option="attach_pdf"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('ATTACH_UPLOADED_FILES') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="notifications" data-option="attach"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('CUSTOMER_EMAIL_NOTIFICATIONS'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-group">
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('SEND_TO_CUSTOMER') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="reply" data-option="enable"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-settings-item ba-settings-select-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('FROM_EMAIL'); ?></span>
                                            <select data-group="reply" data-option="email" data-callback="submitFieldAction">
                                                <option value=""><?php echo JText::_('DEFAULT'); ?></option>
                                                <option value="custom"><?php echo JText::_('CUSTOM'); ?></option>
                                            </select>
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type reply-custom-email">
                                            <span class="ba-settings-item-title"><?php echo JText::_('NAME'); ?></span>
                                            <input type="text" data-group="reply" data-option="custom-name"
                                                data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type reply-custom-email">
                                            <span class="ba-settings-item-title"><?php echo JText::_('EMAIL'); ?></span>
                                            <input type="text" data-group="reply" data-option="custom-email"
                                                data-callback="submitFieldAction">
                                        </div>
                                        <div class="ba-settings-item ba-settings-input-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('SUBJECT'); ?></span>
                                            <input type="text" data-group="reply" data-option="subject"
                                                data-callback="submitFieldAction">
                                            <div class="select-default-value input-action-icon">
                                                <i class="zmdi zmdi-playlist-plus"></i>
                                                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-text-editor-type">
                                            <textarea id="reply-email-body-editor" class="text-editor-textarea"
                                                data-group="reply" data-option="body" data-callback="submitFieldAction"></textarea>
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type pdf-attachment-option">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('PDF_ATTACHMENT') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="reply" data-option="attach_pdf"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('ATTACH_UPLOADED_FILES') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="reply" data-option="attach"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('SPAM_PROTECTION'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-group">
                                        <div class="ba-settings-item ba-settings-select-type">
                                            <span class="ba-settings-item-title">reCAPTCHA</span>
                                            <select data-option="recaptcha" data-callback="submitFieldAction">
                                                <option value=""><?php echo JText::_('NO_NE'); ?></option>
                                            </select>
                                            <div style="display: none !important;">
<?php
                                                echo $this->form->getInput('recaptcha');
?>
                                            </div>
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('HONEYPOT') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-option="honeypot" data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('PHP_SCRIPTS'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-group">
                                        <div class="ba-settings-item ba-settings-textarea-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('AFTER_DATA_SUBMISSION'); ?></span>
                                            <textarea data-option="php" data-callback="submitFieldAction"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('ADVANCED'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-group">
                                        <div class="ba-settings-item ba-settings-input-type">
                                            <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                            <input type="text" class="modify-item-suffix">
                                        </div>
                                        <div class="ba-settings-item ba-settings-checkbox-type">
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('SAVE_DATA_TO_DATABASE') ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-option="database" data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="submit-button-design-settings" class="row-fluid tab-pane">
                    <div class="accordion">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('BACKGROUND'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="background"
                                            data-option="color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('HOVER'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="background"
                                            data-option="hover" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-select-type">
                                        <span class="ba-settings-item-title"><?php echo JText::_('ANIMATION'); ?></span>
                                        <select data-option="animation" data-callback="submitFieldAction">
                                            <option value=""><?php echo JText::_('NO_NE'); ?></option>
                                            <option value="ba-form-tada-animation">Tada</option>
                                            <option value="ba-form-slide-out-diagonal-animation">Slide Out Diagonal</option>
                                            <option value="ba-form-slide-in-diagonal-animation">Slide In Diagonal</option>
                                            <option value="ba-form-slide-in-horizontal-animation">Slide In Horizontal</option>
                                            <option value="ba-form-slide-out-horizontal-animation">Slide Out Horizontal</option>
                                            <option value="ba-form-icon-horizontal-slide-in-animation">Icon Horizontal Slide In</option>
                                            <option value="ba-form-icon-vertical-slide-in-animation">Icon Vertical Slide In</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('SPACING'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-settings-padding-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('PADDING'); ?>
                                        </span>
                                        <i class="zmdi zmdi-link spacing-link" data-group="padding"
                                            data-callback="submitFieldAction"></i>
                                        <div class="ba-settings-toolbar">
                                            <div>
                                                <span>
                                                    <?php echo JText::_('TOP'); ?>
                                                </span>
                                                <input type="number" data-group="padding" data-option="top"
                                                    data-callback="submitFieldAction">
                                            </div>
                                            <div>
                                                <span>
                                                    <?php echo JText::_('RIGHT'); ?>
                                                </span>
                                                <input type="number" data-group="padding" data-option="right"
                                                    data-callback="submitFieldAction">
                                            </div>
                                            <div>
                                                <span>
                                                    <?php echo JText::_('BOTTOM'); ?>
                                                </span>
                                                <input type="number" data-group="padding" data-option="bottom"
                                                    data-callback="submitFieldAction">
                                            </div>
                                            <div>
                                                <span>
                                                    <?php echo JText::_('LEFT'); ?>
                                                </span>
                                                <input type="number" data-group="padding" data-option="left"
                                                    data-callback="submitFieldAction">
                                            </div>
                                            <span class="ba-units-value">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('BORDER'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-inline-checkbox">
                                        <div>
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('TOP'); ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="border" data-option="top"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div>
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('RIGHT'); ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="border" data-option="right"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div>
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('BOTTOM'); ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="border" data-option="bottom"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div>
                                            <span class="ba-settings-item-title">
                                                <?php echo JText::_('LEFT'); ?>
                                            </span>
                                            <label class="ba-form-toggle">
                                                <input type="checkbox" data-group="border" data-option="left"
                                                    data-callback="submitFieldAction">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('RADIUS'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="100">
                                            <input type="number" data-group="border" data-option="radius"
                                                data-callback="submitFieldAction">
                                            <span class="ba-units-value">px</span>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="border"
                                            data-option="color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('HOVER'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="border"
                                            data-option="hover" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('WIDTH'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="100">
                                            <input type="number" data-group="border" data-option="width"
                                                data-callback="submitFieldAction">
                                            <span class="ba-units-value">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('FONT'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-settings-font-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('FONT'); ?>
                                        </span>
                                        <div class="trigger-picker-modal fonts-select" data-modal="google-fonts-dialog">
                                            <input placeholder="<?php echo JText::_('FONT'); ?>" type="text" readonly onfocus="this.blur()">
                                            <input type="hidden" data-group="typography" data-option="font-family"
                                                data-callback="submitFieldAction">
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('SIZE'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="100">
                                            <input type="number" data-group="typography"
                                                data-option="font-size" data-callback="submitFieldAction">
                                            <select class="ba-units-select" data-group="units"
                                                data-option="font-size" data-callback="submitFieldAction">
                                                <option value="px">px</option>
                                                <option value="em">em</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('LETTER_SPACING'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner letter-spacing"></span>
                                            <input type="range" class="ba-range" min="-10" max="10">
                                            <input type="number" data-group="typography"
                                                data-option="letter-spacing" data-callback="submitFieldAction">
                                            <select class="ba-units-select" data-group="units"
                                                data-option="letter-spacing" data-callback="submitFieldAction">
                                                <option value="px">px</option>
                                                <option value="em">em</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('LINE_HEIGHT'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="200">
                                            <input type="number" data-group="typography"
                                                data-option="line-height" data-callback="submitFieldAction">
                                            <select class="ba-units-select" data-group="units"
                                                data-option="line-height" data-callback="submitFieldAction">
                                                <option value="px">px</option>
                                                <option value="em">em</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="typography"
                                            data-option="color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('HOVER'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="typography"
                                            data-option="hover" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                        <div class="ba-settings-toolbar">
                                            <div>
                                                <label data-option="font-weight" data-value="bold" data-default="normal"
                                                    data-group="typography" data-option="font-weight"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-bold"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('BOLD'); ?>
                                                    </span>
                                                </label>
                                                <label data-option="text-transform" data-value="uppercase" data-default="none"
                                                    data-group="typography" data-option="text-transform"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-size"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('UPPERCASE'); ?>
                                                    </span>
                                                </label>
                                                <label data-option="font-style" data-value="italic" data-default="normal"
                                                    data-group="typography" data-option="font-style"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-italic"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('ITALIC'); ?>
                                                    </span>
                                                </label>
                                            </div>
                                            <div>
                                                <label data-value="flex-start" data-group="typography" data-option="text-align"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-align-left"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('LEFT'); ?>
                                                    </span>
                                                </label>
                                                <label data-value="center" data-group="typography" data-option="text-align"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-align-center"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('CENTER'); ?>
                                                    </span>
                                                </label>
                                                <label data-value="flex-end" data-group="typography" data-option="text-align"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-align-right"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('RIGHT'); ?>
                                                    </span>
                                                </label>
                                                <label data-value="1" data-group="typography" data-option="text-align"
                                                    data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-align-justify"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('JUSTIFY'); ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('SHADOW'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('VALUE'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="6">
                                            <input type="number" data-group="shadow" data-option="value"
                                                data-callback="submitFieldAction">
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="shadow" data-option="color"
                                            data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('HOVER'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="shadow" data-option="hover"
                                            data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('ICON'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('SIZE'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="100">
                                            <input type="number" data-group="icon" data-option="size"
                                                data-callback="submitFieldAction">
                                            <select class="ba-units-select" data-group="units"
                                                data-option="size" data-callback="submitFieldAction">
                                                <option value="px">px</option>
                                                <option value="em">em</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="icon"
                                            data-option="color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('HOVER'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="icon"
                                            data-option="hover" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                        <div class="ba-settings-toolbar">
                                            <div>
                                                <label data-value="row" data-group="icon"
                                                    data-option="text-align" data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-align-left"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('LEFT'); ?>
                                                    </span>
                                                </label>
                                                <label data-value="row-reverse" data-group="icon"
                                                    data-option="text-align" data-callback="submitFieldAction">
                                                    <i class="zmdi zmdi-format-align-right"></i>
                                                    <span class="ba-tooltip ba-top ba-hide-element">
                                                        <?php echo JText::_('RIGHT'); ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group advanced-message-options">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse">
                                    <?php echo JText::_('SUBMISSION_POPUP'); ?>
                                    <i class="zmdi zmdi-caret-right"></i>
                                </a>
                            </div>
                            <div class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('WIDTH'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="100">
                                            <input type="number" data-group="popup" data-option="width" data-callback="submitFieldAction">
                                            <select class="ba-units-select" data-group="units" data-option="popup-width">
                                                <option value="px">px</option>
                                                <option value="%">%</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-range-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('BORDER_RADIUS'); ?>
                                        </span>
                                        <div class="ba-range-wrapper">
                                            <span class="ba-range-liner"></span>
                                            <input type="range" class="ba-range" min="0" max="100">
                                            <input type="number" data-group="popup" data-option="radius"
                                                data-callback="submitFieldAction">
                                            <span class="ba-units-value">px</span>
                                        </div>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('BACKGROUND_COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="popup"
                                            data-option="background-color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('BACKDROP_COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="popup"
                                            data-option="backdrop-color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-color-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('ICON_CLOSE_COLOR'); ?>
                                        </span>
                                        <input type="text" data-type="color" data-group="popup"
                                            data-option="icon-color" data-callback="submitFieldAction">
                                        <span class="minicolors-opacity-wrapper">
                                            <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                                data-callback="submitFieldAction">
                                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-settings-item ba-settings-select-type">
                                        <span class="ba-settings-item-title">
                                            <?php echo JText::_('ANIMATION'); ?>
                                        </span>
                                        <select data-option="popup-animation" data-callback="submitFieldAction">
                                            <option value="ba-forms-lightbox-effect-1">Fade In</option>
                                            <option value="ba-forms-lightbox-effect-8">Fall In</option>
                                            <option value="ba-forms-lightbox-effect-9">Flip Horizontal</option>
                                            <option value="ba-forms-lightbox-effect-10">Flip Vertical</option>
                                            <option value="ba-forms-lightbox-effect-2">Scale In</option>
                                            <option value="ba-forms-lightbox-effect-6">Slide In Bottom</option>
                                            <option value="ba-forms-lightbox-effect-3">Slide In Left</option>
                                            <option value="ba-forms-lightbox-effect-4">Slide In Right</option>
                                            <option value="ba-forms-lightbox-effect-5">Slide In Top</option>
                                            <option value="ba-forms-lightbox-effect-7">Spinner</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="acceptance-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('ACCEPTANCE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-text-editor-type dialog-options">
                                <textarea id="acceptance-editor" class="text-editor-textarea" data-option="html"
                                    data-callback="acceptanceFieldAction"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="phone-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('PHONE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-font-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('DEFAULT_VALUE'); ?>
                                </span>
                                <div class="trigger-picker-modal default-country-select" data-modal="default-country-dialog">
                                    <input placeholder="<?php echo JText::_('SEARCH'); ?>" type="text" readonly onfocus="this.blur()">
                                    <input type="hidden" data-option="default" data-callback="phoneAction">
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="headline-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('HEADLINE'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HTML_TAG'); ?></span>
                                <select data-option="tag" data-callback="headlineAction">
                                    <option value="h1">H1</option>
                                    <option value="h2">H2</option>
                                    <option value="h3">H3</option>
                                    <option value="h4">H4</option>
                                    <option value="h5">H5</option>
                                    <option value="h6">H6</option>
                                    <option value="p">Paragraph</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('DESIGN'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-font-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('FONT'); ?>
                                </span>
                                <div class="trigger-picker-modal fonts-select" data-modal="google-fonts-dialog">
                                    <input placeholder="<?php echo JText::_('FONT'); ?>" type="text" readonly onfocus="this.blur()">
                                    <input type="hidden" data-group="label" data-subgroup="typography" data-option="font-family"
                                        data-callback="headlineAction">
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('SIZE'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="100">
                                    <input type="number" data-group="label" data-subgroup="typography" data-option="font-size"
                                        data-callback="headlineAction">
                                    <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="font-size"
                                        data-callback="headlineAction">
                                        <option value="px">px</option>
                                        <option value="em">em</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('LETTER_SPACING'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner letter-spacing"></span>
                                    <input type="range" class="ba-range" min="-10" max="10">
                                    <input type="number" data-group="label" data-subgroup="typography" data-option="letter-spacing"
                                        data-callback="headlineAction">
                                    <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="letter-spacing"
                                        data-callback="headlineAction">
                                        <option value="px">px</option>
                                        <option value="em">em</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('LINE_HEIGHT'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="200">
                                    <input type="number"  data-group="label" data-subgroup="typography" data-option="line-height"
                                        data-callback="headlineAction">
                                    <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="line-height"
                                        data-callback="headlineAction">
                                        <option value="px">px</option>
                                        <option value="em">em</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-color-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('COLOR'); ?>
                                </span>
                                <input type="text" data-type="color" data-group="label" data-subgroup="typography" data-option="color"
                                    data-callback="headlineAction">
                                <span class="minicolors-opacity-wrapper">
                                    <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                </span>
                            </div>
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-option="font-weight" data-value="bold" data-default="normal" data-group="label"
                                            data-subgroup="typography" data-option="font-weight" data-callback="headlineAction">
                                            <i class="zmdi zmdi-format-bold"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('BOLD'); ?>
                                            </span>
                                        </label>
                                        <label data-option="text-transform" data-value="uppercase" data-default="none" data-group="label"
                                            data-subgroup="typography" data-option="text-transform" data-callback="headlineAction">
                                            <i class="zmdi zmdi-format-size"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('UPPERCASE'); ?>
                                            </span>
                                        </label>
                                        <label data-option="font-style" data-value="italic" data-default="normal" data-group="label"
                                            data-subgroup="typography" data-option="font-style" data-callback="headlineAction">
                                            <i class="zmdi zmdi-format-italic"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('ITALIC'); ?>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <label data-value="left" data-group="label" data-subgroup="typography" data-option="text-align"
                                            data-callback="headlineAction">
                                            <i class="zmdi zmdi-format-align-left"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('LEFT'); ?>
                                            </span>
                                        </label>
                                        <label data-value="center" data-group="label" data-subgroup="typography" data-option="text-align"
                                            data-callback="headlineAction">
                                            <i class="zmdi zmdi-format-align-center"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('CENTER'); ?>
                                            </span>
                                        </label>
                                        <label data-value="right" data-group="label" data-subgroup="typography" data-option="text-align"
                                            data-callback="headlineAction">
                                            <i class="zmdi zmdi-format-align-right"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('RIGHT'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="calculation-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('CALCULATION'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('REGULAR'); ?></option>
                                    <option value="product"><?php echo JText::_('PRODUCT'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('HIDDEN'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="hidden" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('CALCULATION'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-form-calculator">
                            <div class="ba-form-calculator-header">
                                <textarea class="ba-form-calculator-screen" data-option="formula"
                                    data-callback="inputFieldAction"></textarea>
                            </div>
                            <div class="ba-form-calculator-body">
                                <div class="ba-form-calculator-row">
                                    <span class="ba-form-calculator-btn" data-fields>
                                        <i class="zmdi zmdi-playlist-plus"></i>
                                    </span>
                                    <span class="ba-form-calculator-btn" data-code="(">(</span>
                                    <span class="ba-form-calculator-btn" data-code=")">)</span>
                                    <span class="ba-form-calculator-btn" data-code=" / ">/</span>
                                </div>
                                <div class="ba-form-calculator-row">
                                    <span class="ba-form-calculator-btn" data-code="7">7</span>
                                    <span class="ba-form-calculator-btn" data-code="8">8</span>
                                    <span class="ba-form-calculator-btn" data-code="9">9</span>
                                    <span class="ba-form-calculator-btn" data-code=" * ">*</span>
                                </div>
                                <div class="ba-form-calculator-row">
                                    <span class="ba-form-calculator-btn" data-code="4">4</span>
                                    <span class="ba-form-calculator-btn" data-code="5">5</span>
                                    <span class="ba-form-calculator-btn" data-code="6">6</span>
                                    <span class="ba-form-calculator-btn" data-code=" - ">-</span>
                                </div>
                                <div class="ba-form-calculator-row">
                                    <span class="ba-form-calculator-btn" data-code="1">1</span>
                                    <span class="ba-form-calculator-btn" data-code="2">2</span>
                                    <span class="ba-form-calculator-btn" data-code="3">3</span>
                                    <span class="ba-form-calculator-btn" data-code=" + ">+</span>
                                </div>
                                <div class="ba-form-calculator-row">
                                    <span class="ba-form-calculator-btn" data-code="0">0</span>
                                    <span class="ba-form-calculator-btn" data-code=".">.</span>
                                    <span class="ba-form-calculator-btn" data-code="">AC</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('VALUE'); ?></span>
                                <input type="text" data-option="symbol" data-callback="calculationAction">
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('POSITION'); ?></span>
                                <select data-option="position" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('LEFT'); ?></option>
                                    <option value="right-currency-position"><?php echo JText::_('RIGHT'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('THOUSAND_SEPARATOR'); ?></span>
                                <select data-option="thousand" data-callback="calculationAction">
                                    <option value=","><?php echo JText::_('COMMA'); ?></option>
                                    <option value="."><?php echo JText::_('DOT'); ?></option>
                                    <option value=" "><?php echo JText::_('SPACE'); ?></option>
                                    <option value=""><?php echo JText::_('None'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('DECIMAL_SEPARATOR'); ?></span>
                                <select data-option="separator" data-callback="calculationAction">
                                    <option value=","><?php echo JText::_('COMMA'); ?></option>
                                    <option value="."><?php echo JText::_('DOT'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('NUMBER_OF_DECIMALS') ?></span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="10">
                                    <input type="number" data-option="decimals" data-callback="calculationAction">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group calculation-field-design-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('DESIGN'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('INHERIT_GLOBAL_STYLES'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="design" data-callback="calculationAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="ba-settings-group field-design-options">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select class="select-design-font-group">
                                    <option value="label"><?php echo JText::_('LABEL'); ?></option>
                                    <option value="field"><?php echo JText::_('VALUE'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-font-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('FONT'); ?>
                                </span>
                                <div class="trigger-picker-modal fonts-select" data-modal="google-fonts-dialog">
                                    <input placeholder="<?php echo JText::_('FONT'); ?>" type="text" readonly onfocus="this.blur()">
                                    <input type="hidden" data-group="label" data-subgroup="typography" data-option="font-family"
                                        data-callback="calculationAction">
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('SIZE'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="100">
                                    <input type="number" data-group="label" data-subgroup="typography" data-option="font-size"
                                        data-callback="calculationAction">
                                    <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="font-size"
                                        data-callback="calculationAction">
                                        <option value="px">px</option>
                                        <option value="em">em</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('LETTER_SPACING'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner letter-spacing"></span>
                                    <input type="range" class="ba-range" min="-10" max="10">
                                    <input type="number" data-group="label" data-subgroup="typography" data-option="letter-spacing"
                                        data-callback="calculationAction">
                                    <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="letter-spacing"
                                        data-callback="calculationAction">
                                        <option value="px">px</option>
                                        <option value="em">em</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('LINE_HEIGHT'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="200">
                                    <input type="number"  data-group="label" data-subgroup="typography" data-option="line-height"
                                        data-callback="calculationAction">
                                    <select class="ba-units-select" data-group="label" data-subgroup="units" data-option="line-height"
                                        data-callback="calculationAction">
                                        <option value="px">px</option>
                                        <option value="em">em</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-color-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('COLOR'); ?>
                                </span>
                                <input type="text" data-type="color" data-group="label" data-subgroup="typography" data-option="color"
                                    data-callback="calculationAction">
                                <span class="minicolors-opacity-wrapper">
                                    <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                </span>
                            </div>
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-option="font-weight" data-value="bold" data-default="normal" data-group="label"
                                            data-subgroup="typography" data-option="font-weight" data-callback="calculationAction">
                                            <i class="zmdi zmdi-format-bold"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('BOLD'); ?>
                                            </span>
                                        </label>
                                        <label data-option="text-transform" data-value="uppercase" data-default="none" data-group="label"
                                            data-subgroup="typography" data-option="text-transform" data-callback="calculationAction">
                                            <i class="zmdi zmdi-format-size"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('UPPERCASE'); ?>
                                            </span>
                                        </label>
                                        <label data-option="font-style" data-value="italic" data-default="normal" data-group="label"
                                            data-subgroup="typography" data-option="font-style" data-callback="calculationAction">
                                            <i class="zmdi zmdi-format-italic"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('ITALIC'); ?>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <label data-value="left" data-group="label" data-subgroup="typography" data-option="text-align"
                                            data-callback="calculationAction">
                                            <i class="zmdi zmdi-format-align-left"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('LEFT'); ?>
                                            </span>
                                        </label>
                                        <label data-value="center" data-group="label" data-subgroup="typography" data-option="text-align"
                                            data-callback="calculationAction">
                                            <i class="zmdi zmdi-format-align-center"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('CENTER'); ?>
                                            </span>
                                        </label>
                                        <label data-value="right" data-group="label" data-subgroup="typography" data-option="text-align"
                                            data-callback="calculationAction">
                                            <i class="zmdi zmdi-format-align-right"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('RIGHT'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ba-settings-group field-design-options">
                            <div class="ba-settings-item ba-settings-color-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('BACKGROUND'); ?>
                                </span>
                                <input type="text" data-type="color" data-group="background"
                                    data-option="color" data-callback="calculationAction">
                                <span class="minicolors-opacity-wrapper">
                                    <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01"
                                        data-callback="calculationAction">
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="ba-settings-group field-design-options">
                            <div class="ba-settings-item ba-settings-padding-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('PADDING'); ?>
                                </span>
                                <i class="zmdi zmdi-link spacing-link" data-group="padding"
                                    data-callback="calculationAction"></i>
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <span>
                                            <?php echo JText::_('TOP'); ?>
                                        </span>
                                        <input type="number" data-group="padding" data-option="top"
                                            data-callback="calculationAction">
                                    </div>
                                    <div>
                                        <span>
                                            <?php echo JText::_('RIGHT'); ?>
                                        </span>
                                        <input type="number" data-group="padding" data-option="right"
                                            data-callback="calculationAction">
                                    </div>
                                    <div>
                                        <span>
                                            <?php echo JText::_('BOTTOM'); ?>
                                        </span>
                                        <input type="number" data-group="padding" data-option="bottom"
                                            data-callback="calculationAction">
                                    </div>
                                    <div>
                                        <span>
                                            <?php echo JText::_('LEFT'); ?>
                                        </span>
                                        <input type="number" data-group="padding" data-option="left"
                                            data-callback="calculationAction">
                                    </div>
                                    <span class="ba-units-value">px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="total-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('CART'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('PRODUCTS'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="cart" data-callback="calculationAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('CURRENCY'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('SYMBOL'); ?></span>
                                <input type="text" data-option="symbol" data-callback="calculationAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CURRENCY_CODE'); ?></span>
                                <input type="text" data-option="code" data-callback="calculationAction">
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CURRENCY_POSITION'); ?></span>
                                <select data-option="position" data-callback="calculationAction">
                                    <option value=""><?php echo JText::_('LEFT'); ?></option>
                                    <option value="right-currency-position"><?php echo JText::_('RIGHT'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('THOUSAND_SEPARATOR'); ?></span>
                                <select data-option="thousand" data-callback="calculationAction">
                                    <option value=","><?php echo JText::_('COMMA'); ?></option>
                                    <option value="."><?php echo JText::_('DOT'); ?></option>
                                    <option value=" "><?php echo JText::_('SPACE'); ?></option>
                                    <option value=""><?php echo JText::_('None'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('DECIMAL_SEPARATOR'); ?></span>
                                <select data-option="separator" data-callback="calculationAction">
                                    <option value=","><?php echo JText::_('COMMA'); ?></option>
                                    <option value="."><?php echo JText::_('DOT'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-range-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('NUMBER_OF_DECIMALS') ?></span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="10">
                                    <input type="number" data-option="decimals" data-callback="calculationAction">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('SHIPPING'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group sorting-group-wrapper">
                            <div class="ba-settings-item ba-settings-typography-toolbar-type">
                                <div class="ba-settings-toolbar">
                                    <div>
                                        <label data-action="default">
                                            <i class="zmdi zmdi-star"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DEFAULT_VALUE'); ?>
                                            </span>
                                        </label>
                                        <label data-action="copy">
                                            <i class="zmdi zmdi-copy"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('COPY_ITEM'); ?>
                                            </span>
                                        </label>
                                        <label data-action="delete">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-top ba-hide-element">
                                                <?php echo JText::_('DELETE_ITEM'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-sortable-type">
                                <div class="sorting-container">
                                    
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                                <span>
                                    <i class="zmdi zmdi-plus-circle add-new-select-item"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element">
                                        <?php echo JText::_('ADD_NEW_ITEM'); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROMO_CODE'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('PROMO_CODE'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-group="promo" data-option="enable" data-callback="calculationAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="promo-code-options">
                                <div class="ba-settings-item ba-settings-input-type">
                                    <span class="ba-settings-item-title"><?php echo JText::_('CODE'); ?></span>
                                    <input type="text" data-group="promo" data-option="code" data-callback="calculationAction">
                                </div>
                                <div class="ba-settings-item ba-settings-number-type">
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('DISCOUNT'); ?>
                                    </span>
                                    <input type="number" data-group="promo" data-option="discount" data-callback="calculationAction">
                                    <select data-group="promo" data-option="unit" data-callback="calculationAction">
                                        <option value=""><?php echo JText::_('AMOUNT'); ?></option>
                                        <option value="%"><?php echo JText::_('PERCENTAGE'); ?></option>
                                    </select>
                                </div>
                                <div class="ba-settings-item ba-settings-input-type">
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('EXPIRES'); ?>
                                    </span>
                                    <input type="text" data-group="promo" data-option="expires" readonly onfocus="this.blur()"
                                        class="select-input open-calendar-dialog" data-callback="setCartExpire">
                                    <div class="reset input-action-icon">
                                        <i class="zmdi zmdi-close"></i>
                                        <span class="ba-tooltip ba-top ba-hide-element">
                                            <?php echo JText::_('RESET'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('TAX'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('TAX'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-group="tax" data-option="enable" data-callback="calculationAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="tax-options">
                                <div class="ba-settings-item ba-settings-input-type">
                                    <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                    <input type="text" data-group="tax" data-option="title" data-callback="calculationAction">
                                </div>
                                <div class="ba-settings-item ba-settings-number-type">
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('RATE'); ?> %
                                    </span>
                                    <input type="number" data-group="tax" data-option="value" data-callback="calculationAction">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="input-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('INPUT'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="type" data-callback="inputFieldAction">
                                    <option value="text"><?php echo JText::_('TEXT_INPUT'); ?></option>
                                    <option value="textarea"><?php echo JText::_('TEXT_AREA'); ?></option>
                                    <option value="email"><?php echo JText::_('EMAIL'); ?></option>
                                    <option value="password"><?php echo JText::_('PASSWORD'); ?></option>
                                    <option value="phone"><?php echo JText::_('PHONE'); ?></option>
                                    <option value="zip"><?php echo JText::_('ZIP_CODE'); ?></option>
                                    <option value="card"><?php echo JText::_('CREDIT_CARD_NUMBER'); ?></option>
                                    <option value="date"><?php echo JText::_('DATE'); ?></option>
                                    <option value="time"><?php echo JText::_('TIME'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type not-mask-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('PLACEHOLDER'); ?></span>
                                <input type="text" data-option="placeholder" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type not-mask-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                <input type="text" data-option="default" data-callback="inputFieldAction">
                                <div class="select-default-value input-action-icon">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DATA_TAGS'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('ICON'); ?></span>
                                <input type="text" readonly onfocus="this.blur()" class="select-input select-icon"
                                    data-option="icon" data-callback="inputFieldAction">
                                <div class="reset input-action-icon">
                                    <i class="zmdi zmdi-close"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-inline-checkbox">
                                <div>
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('REQUIRED'); ?>
                                    </span>
                                    <label class="ba-form-toggle">
                                        <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                        <span></span>
                                    </label>
                                </div>
                                <div>
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('READ_ONLY'); ?>
                                    </span>
                                    <label class="ba-form-toggle">
                                        <input type="checkbox" data-option="readonly" data-callback="inputFieldAction">
                                        <span></span>
                                    </label>
                                </div>
                                <div>
                                    <span class="ba-settings-item-title">
                                        <?php echo JText::_('HIDDEN'); ?>
                                    </span>
                                    <label class="ba-form-toggle">
                                        <input type="checkbox" data-option="hidden" data-callback="inputFieldAction">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('PROPERTIES'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type not-mask-type not-password-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('VALIDATION'); ?></span>
                                <select data-option="validation" data-callback="inputFieldAction">
                                    <option value=""><?php echo JText::_('NO_NE'); ?></option>
                                    <option value="numbers"><?php echo JText::_('NUMBERS'); ?></option>
                                    <option value="email"><?php echo JText::_('EMAIL'); ?></option>
                                    <option value="url"><?php echo JText::_('URL'); ?></option>
                                </select>
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type email-type-options">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('CONFIRM_EMAIL'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-group="confirm" data-option="enable" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type email-type-options email-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-group="confirm" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type email-type-options email-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('PLACEHOLDER'); ?></span>
                                <input type="text" data-group="confirm" data-option="placeholder" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type email-type-options email-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                <input type="text" data-group="confirm" data-option="default" data-callback="inputFieldAction">
                                <div class="select-default-value input-action-icon">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type email-type-options email-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('ICON'); ?></span>
                                <input type="text" readonly onfocus="this.blur()" class="select-input select-icon"
                                    data-group="confirm" data-option="icon" data-callback="inputFieldAction">
                                <div class="reset input-action-icon">
                                    <i class="zmdi zmdi-close"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type email-type-options email-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-group="confirm" data-option="description" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-checkbox-type password-type-options">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('CONFIRM_PASSWORD'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-group="confirm-password" data-option="enable" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type password-type-options password-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-group="confirm-password" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type password-type-options password-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('PLACEHOLDER'); ?></span>
                                <input type="text" data-group="confirm-password" data-option="placeholder" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type password-type-options password-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                <input type="text" data-group="confirm-password" data-option="default" data-callback="inputFieldAction">
                                <div class="select-default-value input-action-icon">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type password-type-options password-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('ICON'); ?></span>
                                <input type="text" readonly onfocus="this.blur()" class="select-input select-icon"
                                    data-group="confirm-password" data-option="icon" data-callback="inputFieldAction">
                                <div class="reset input-action-icon">
                                    <i class="zmdi zmdi-close"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type password-type-options password-confirm-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-group="confirm-password" data-option="description" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type mask-type-options">
                                <span class="ba-settings-item-title"><?php echo JText::_('MASK'); ?></span>
                                <input type="text" data-option="mask" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group not-email-type-options not-mask-type not-password-options">
                            <div class="ba-settings-item ba-settings-number-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('CHARACTERS'); ?>
                                </span>
                                <input type="number" data-group="characters" data-option="length" data-callback="inputFieldAction">
                                <select data-group="characters" data-option="key" data-callback="inputFieldAction">
                                    <option value="min"><?php echo JText::_('MIN'); ?></option>
                                    <option value="max"><?php echo JText::_('MAX'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="address-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('AUTOCOMPLETE_ADDRESS'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('PLACEHOLDER'); ?></span>
                                <input type="text" data-option="placeholder" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                <input type="text" data-option="default" data-callback="inputFieldAction">
                                <div class="select-default-value input-action-icon">
                                    <i class="zmdi zmdi-playlist-plus"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DEFAULT_VALUE'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('ICON'); ?></span>
                                <input type="text" readonly onfocus="this.blur()" class="select-input select-icon" data-option="icon"
                                    data-callback="inputFieldAction">
                                <div class="reset input-action-icon">
                                    <i class="zmdi zmdi-close"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('RESET'); ?></span>
                                </div>
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="rating-field-settings-dialog" class="ba-modal-cp fields-editor-panel draggable-modal-cp modal hide hidden-modal-backdrop">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('RATING'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('GENERAL'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body in collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-select-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('TYPE'); ?></span>
                                <select data-option="layout" data-callback="ratingFieldAction">
                                    <option value="smiles"><?php echo JText::_('SMILES'); ?></option>
                                    <option value="stars"><?php echo JText::_('STARS'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('LABEL'); ?></span>
                                <input type="text" data-option="title" data-callback="inputFieldAction">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('HELP_TEXT'); ?></span>
                                <input type="text" data-option="description" data-callback="inputFieldAction">
                            </div>
                        </div>
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-checkbox-type">
                                <span class="ba-settings-item-title">
                                    <?php echo JText::_('REQUIRED'); ?>
                                </span>
                                <label class="ba-form-toggle">
                                    <input type="checkbox" data-option="required" data-callback="inputFieldAction">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse">
                        <?php echo JText::_('ADVANCED'); ?>
                        <i class="zmdi zmdi-caret-right"></i>
                    </a>
                </div>
                <div class="accordion-body collapse">
                    <div class="accordion-inner">
                        <div class="ba-settings-group">
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('CLASS_SUFFIX'); ?></span>
                                <input type="text" class="modify-item-suffix">
                            </div>
                            <div class="ba-settings-item ba-settings-input-type">
                                <span class="ba-settings-item-title"><?php echo JText::_('FIELD_ID'); ?></span>
                                <input type="text" disabled onfocus="this.blur()" class="select-input field-id-input">
                                <div class="copy-to-clipboard input-action-icon">
                                    <i class="zmdi zmdi-copy"></i>
                                    <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_TO_CLIPBOARD'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <i class="zmdi zmdi-more resize-handle-bottom resizable-handle" data-direction="bottom"></i>
    </div>
</div>
<div id="text-editor-dialog" class="ba-modal-lg modal hide hidden-modal-backdrop" style="display: none;">
    <div class="modal-header">
        <h3 class="ba-modal-title"><?php echo JText::_('TEXT'); ?></h3>
        <div class="modal-header-icon">
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="ba-settings-group">
            <div class="ba-settings-item ba-settings-input-type">
                <input type="text" data-option="admin-label" data-callback="imageFieldAction"
                    placeholder="<?php echo JText::_('ADMIN_LABEL'); ?>">
            </div>
        </div>
        <textarea id="editor"></textarea>
    </div>
    <i class="zmdi zmdi-format-valign-center resizable-handle-right resizable-handle" data-direction="right"></i>
</div>
<div id="edit-post-link-dialog" class="ba-modal-sm modal hide">
    <div class="modal-body">
        <h3 class="ba-modal-title">
            <?php echo JText::_('LINK') ?>
        </h3>
        <div class="ba-input-lg link-picker-container">
            <div class="post-link-input-wrapper">
                <input type="text" class="reset-input-margin post-link-input" placeholder="<?php echo JText::_('LINK'); ?>">
                <span class="focus-underline"></span>
            </div>
        </div>
        <div class="ba-custom-select cke-link-target-select">
            <input readonly="" onfocus="this.blur()" type="text">
            <input type="hidden" data-property="target">
            <i class="zmdi zmdi-caret-down"></i>
            <ul>
                <li data-value="_blank"><?php echo JText::_('NEW_WINDOW'); ?></li>
                <li data-value="_self"><?php echo JText::_('SAME_WINDOW'); ?></li>
            </ul>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL'); ?>
        </a>
        <a href="#" class="ba-btn-primary disable-button" id="post-link-apply">
            <?php echo JText::_('JAPPLY'); ?>
        </a>
    </div>
</div>
<div id="color-variables-dialog" class="modal hide ba-modal-picker picker-modal-arrow" style="display: none;">
    <div class="modal-header">
        <i class="zmdi zmdi-eyedropper"></i>
    </div>
    <div class="modal-body">
        <div id="color-picker-cell">
            <input type="hidden" data-dismiss="modal">
            <input type="text" class="variables-color-picker">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01">
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY'); ?></span>
            </span>
        </div>
    </div>
</div>
<div id="add-columns-dialog" class="modal hide ba-modal-picker picker-modal-arrow hidden-modal-backdrop" style="display: none;">
    <div class="modal-body">
        <div class="ba-settings-item ba-settings-range-type">
            <span class="ba-settings-item-title">
                <?php echo JText::_('COLUMNS'); ?>
            </span>
            <div class="ba-range-wrapper">
                <span class="ba-range-liner"></span>
                <input type="range" class="ba-range" min="1" max="4">
                <input type="number" id="modify-column-number" data-callback="modifyColumns">
            </div>
        </div>
    </div>
</div>
<div id="disable-week-days-dialog" class="modal hide ba-modal-picker picker-modal-arrow hidden-modal-backdrop" style="display: none;">
    <div class="modal-header">
        <i class="zmdi zmdi-playlist-plus"></i>
    </div>
    <div class="modal-body">
        <div class="week-days-wrapper">
            <div data-date="0"><?php echo JText::_('SUNDAY'); ?></div>
            <div data-date="1"><?php echo JText::_('MONDAY'); ?></div>
            <div data-date="2"><?php echo JText::_('TUESDAY'); ?></div>
            <div data-date="3"><?php echo JText::_('WEDNESDAY'); ?></div>
            <div data-date="4"><?php echo JText::_('THURSDAY'); ?></div>
            <div data-date="5"><?php echo JText::_('FRIDAY'); ?></div>
            <div data-date="6"><?php echo JText::_('SATURDAY'); ?></div>
        </div>
    </div>
</div>
<div id="calendar-dialog" class="modal hide ba-modal-picker picker-modal-arrow hidden-modal-backdrop" style="display: none;">
    <div class="modal-header">
        <i class="zmdi zmdi-calendar-alt"></i>
    </div>
    <div class="modal-body">
        <div class="ba-calendar-wrapper" data-year="<?php echo date('Y'); ?>" data-month="<?php echo date('n') - 1; ?>">
            <div class="ba-calendar-title-wrapper">
                <i class="zmdi zmdi-chevron-left" data-action="prev"></i>
                <span class="ba-calendar-title"><?php echo JHtml::date(time(), 'F Y'); ?></span>
                <i class="zmdi zmdi-chevron-right" data-action="next"></i>
            </div>
            <div class="ba-calendar-header">
                <div class="ba-calendar-day-name" data-day="0"><?php echo JText::_('SUN'); ?></div>
                <div class="ba-calendar-day-name" data-day="1"><?php echo JText::_('MON'); ?></div>
                <div class="ba-calendar-day-name" data-day="2"><?php echo JText::_('TUE'); ?></div>
                <div class="ba-calendar-day-name" data-day="3"><?php echo JText::_('WED'); ?></div>
                <div class="ba-calendar-day-name" data-day="4"><?php echo JText::_('THU'); ?></div>
                <div class="ba-calendar-day-name" data-day="5"><?php echo JText::_('FRI'); ?></div>
                <div class="ba-calendar-day-name" data-day="6"><?php echo JText::_('SAT'); ?></div>
            </div>
            <div class="ba-calendar-body"></div>
            <div class="ba-calendar-footer">
                <span class="ba-forms-today-btn" data-year="<?php echo date('Y'); ?>"
                    data-month="<?php echo date('n') - 1; ?>"><?php echo JText::_('TODAY'); ?></span>
            </div>
         </div>
    </div>
</div>
<div id="custom-color-scheme-dialog" class="modal hide ba-modal-picker picker-modal-arrow hidden-modal-backdrop" style="display: none;">
    <div class="modal-header">
        <i class="zmdi zmdi-palette"></i>
    </div>
    <div class="modal-body">
        <div class="ba-settings-item ba-settings-color-type">
            <span class="ba-settings-item-title">
                <?php echo JText::_('THEME'); ?>
            </span>
            <input type="text" data-type="color" data-group="theme" data-option="color" data-key="theme">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
        <div class="ba-settings-item ba-settings-color-type">
            <span class="ba-settings-item-title">
                <?php echo JText::_('FONT'); ?>
            </span>
            <input type="text" data-type="color" data-group="theme" data-subgroup="typography" data-option="color" data-key="font">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
        <div class="ba-settings-item ba-settings-color-type">
            <span class="ba-settings-item-title">
                <?php echo JText::_('FIELD'); ?>
            </span>
            <input type="text" data-type="color" data-group="field" data-subgroup="background" data-option="color" data-key="field">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
        <div class="ba-settings-item ba-settings-color-type">
            <span class="ba-settings-item-title">
                <?php echo JText::_('BACKGROUND'); ?>
            </span>
            <input type="text" data-type="color" data-group="form" data-subgroup="background" data-option="color" data-key="background">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01" data-callback>
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY') ?></span>
            </span>
        </div>
    </div>
</div>
<div id="drive-folders-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker" style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-input-type">
            <input type="text" placeholder="<?php echo JText::_('SEARCH'); ?>" class="font-search">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-settings-item ba-settings-list-type">
            <ul></ul>
        </div>
    </div>
</div>
<div id="google-fonts-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker" style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-input-type">
            <input type="text" placeholder="<?php echo JText::_('SEARCH'); ?>" class="font-search">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-settings-item ba-settings-list-type">
            <ul></ul>
        </div>
    </div>
</div>
<div id="default-country-dialog" class="modal hide ba-modal-picker picker-modal-arrow ba-modal-list-picker" style="display: none;">
    <div class="modal-body modal-list-type-wrapper">
        <div class="ba-settings-item ba-settings-input-type">
            <input type="text" placeholder="<?php echo JText::_('SEARCH'); ?>" class="default-country-search">
            <i class="zmdi zmdi-search"></i>
        </div>
        <div class="ba-settings-item ba-settings-list-type">
            <ul>
<?php
            foreach (baformsHelper::$countries as $country) {
            
?>
                <li data-title="<?php echo $country->title; ?>" data-value="<?php echo $country->flag; ?>">
                    <span class="ba-phone-flag ba-phone-flag-<?php echo $country->flag; ?>"></span>
                    <span class="ba-phone-country-title"><?php echo $country->title; ?></span>
                    <span class="ba-phone-country-prefix">+<?php echo $country->prefix; ?></span>
                </li>
<?php
            }
?>
            </ul>
        </div>
    </div>
</div>
<div id="uploader-modal" class="ba-modal-lg modal ba-modal-dialog hide" style="display:none" data-check="single">
    <div class="modal-body">
        <iframe src="javascript:''" name="uploader-iframe"></iframe>
        <input type="hidden" data-dismiss="modal">
    </div>
</div>
<div id="templates-modal" class="ba-modal-lg modal ba-modal-dialog hide left-filter-modal" style="display:none">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('TEMPLATES'); ?></span>
        <i class="zmdi zmdi-fullscreen media-fullscrean"></i>
        <i class="close-media zmdi zmdi-close" data-dismiss="modal"></i>
    </div>
    <div class="modal-body">
        <div class="ba-folder-tree">
            <ul>
                <li class="active integrations-filter" data-group="*">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('ALL'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="contact">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('CONTACT'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="application">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('APPLICATION'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="booking">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('BOOKING'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="order">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('ORDER'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="callback">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('CALLBACK'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="multi-page">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('MULTI_PAGE'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="event-registration">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('EVENT_REGISTRATION'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="newsletter">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('NEWSLETTER'); ?>
                    </a>
                </li>
                
            </ul>
        </div>
        <div class="ba-work-area">
            <div class="ba-integrations-group-wrapper">
                <div class="integrations-group">
<?php
                foreach ($this->formTemplates as $formTemplate) {
                    $formTemplateImg = JUri::root().'administrator/components/com_baforms/assets/images/templates/'.$formTemplate->image;
?>
                    <div class="templates-element ba-work-area-element" data-group="<?php echo $formTemplate->group; ?>"
                        data-key="<?php echo $formTemplate->key; ?>" data-id="<?php echo $formTemplate->id; ?>">
                        <div class="templates-element-image"
                            style="background-image: url(<?php echo $formTemplateImg; ?>);"></div>
                        <span><?php echo $formTemplate->title; ?></span>
                    </div>
<?php
                }
?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($this->about->tag == 'pro') {
?>
<div id="login-modal" class="ba-modal-sm modal hide" style="display: none;">
    <div class="modal-body">
        
    </div>
</div>
<?php
}
?>
<div id="condition-logic-modal" class="ba-modal-lg modal ba-modal-dialog hide left-filter-modal" style="display:none">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('CONDITIONAL_LOGIC'); ?></span>
        <i class="zmdi zmdi-fullscreen media-fullscrean"></i>
        <i class="close-media zmdi zmdi-close" data-dismiss="modal"></i>
    </div>
    <div class="modal-body">
        <div class="ba-folder-tree">
            <div class="ba-settings-toolbar">
                <div>
                    <label data-action="copy">
                        <i class="zmdi zmdi-copy"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('COPY_ITEM'); ?></span>
                    </label>
                    <label data-action="publish">
                        <i class="zmdi zmdi-eye-off"></i>
                        <span data-action="unpublish">
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('JLIB_HTML_UNPUBLISH_ITEM'); ?></span>
                        </span>
                        <span data-action="publish" style="display: none;">
                            <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('JLIB_HTML_PUBLISH_ITEM'); ?></span>
                        </span>
                    </label>
                    <label data-action="delete">
                        <i class="zmdi zmdi-delete"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('DELETE_ITEM'); ?></span>
                    </label>
                </div>
            </div>
            <ul>
                
            </ul>
            <div class="ba-settings-item ba-settings-button-type">
                <a href="#" class="add-new-condition-logic"><?php echo JText::_('ADD_NEW_RULE'); ?></a>
            </div>
        </div>
        <div class="ba-work-area">
            <div class="ba-settings-group condition-logic-when-group" style="display: none;">
                <span class="ba-settings-group-title"><?php echo JText::_('CONDITIONS'); ?></span>
                <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                    <span>
                        <i class="zmdi zmdi-plus-circle add-new-when-condition"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_NEW_CONDITION'); ?></span>
                    </span>
                </div>
                <p class="conditions-matches-wrapper">
                    <span><?php echo JText::_('IF'); ?></span>
                    <select class="conditions-matches-operation">
                        <option value="AND"><?php echo JText::_('ALL'); ?></option>
                        <option value="OR"><?php echo JText::_('ANY'); ?></option>
                    </select>
                    <span><?php echo JText::_('CONDITIONS_MATCHED_THEN'); ?></span>
                </p>
            </div>
            <div class="ba-settings-group condition-logic-do-group" style="display: none;">
                <span class="ba-settings-group-title"><?php echo JText::_('ACTIONS'); ?></span>
                <div class="ba-settings-item ba-settings-input-type add-new-select-item-wrapper">
                    <span>
                        <i class="zmdi zmdi-plus-circle add-new-do-condition"></i>
                        <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('ADD_NEW_ACTION'); ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="integration-modal" class="ba-modal-lg modal ba-modal-dialog hide left-filter-modal" style="display:none">
    <div class="modal-header">
        <span class="ba-dialog-title"><?php echo JText::_('INTEGRATIONS'); ?></span>
        <i class="zmdi zmdi-fullscreen media-fullscrean"></i>
        <i class="close-media zmdi zmdi-close" data-dismiss="modal"></i>
    </div>
    <div class="modal-body">
        <div class="ba-folder-tree">
            <ul>
                <li class="active integrations-filter" data-group="*">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('ALL'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="data-storage">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('DATA_STORAGE'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="emailling">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('EMAILLING'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="mapping">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('MAPPING'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="other">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('OTHER'); ?>
                    </a>
                </li>
                <li class="integrations-filter" data-group="payment">
                    <a href="#">
                        <i class="zmdi zmdi-folder"></i>
                        <?php echo JText::_('PAYMENT_PROCESSING'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="ba-work-area">
            <div class="ba-integrations-search-wrapper">
                <input type="text" class="ba-integrations-search">
                <i class="zmdi zmdi-search"></i>
            </div>
            <div class="ba-integrations-group-wrapper">
<?php
                $imgPath = JUri::root().'administrator/components/com_baforms/assets/images/integrations/';
?>
                <div class="integrations-group">
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="twocheckout">
                        <img src="<?php echo $imgPath.'2co.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>2Checkout</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="emailling" data-type="activecampaign">
                        <img src="<?php echo $imgPath.'activecampaign.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>ActiveCampaign</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="emailling" data-type="acymailing">
                        <img src="<?php echo $imgPath.'acymailing.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>AcyMailing</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="authorize">
                        <img src="<?php echo $imgPath.'authorize-net.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Authorize.Net</span>
                    </div>
                    <div class="integrations-element ba-work-area-element<?php echo baformsHelper::checkIntegration('campaign_monitor'); ?>"
                        data-group="emailling" data-type="campaign_monitor">
                        <img src="<?php echo $imgPath.'campaignmonitor.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Campaign Monitor</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="cloudpayments">
                        <img src="<?php echo $imgPath.'cloudpayments.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Cloudpayments</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="emailling" data-type="getresponse">
                        <img src="<?php echo $imgPath.'getresponse.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>GetResponse</span>
                    </div>
                    <div class="integrations-element ba-work-area-element<?php echo baformsHelper::checkIntegration('google_drive'); ?>"
                        data-group="data-storage" data-type="google_drive">
                        <img src="<?php echo $imgPath.'google-drive.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Google Drive</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="mapping" data-type="google_maps">
                        <img src="<?php echo $imgPath.'google-maps.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Google Maps</span>
                    </div>
                    <div class="integrations-element ba-work-area-element<?php echo baformsHelper::checkIntegration('google_sheets'); ?>"
                        data-group="data-storage" data-type="google_sheets">
                        <img src="<?php echo $imgPath.'google-sheets.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Google Sheets</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="other" data-type="hcaptcha">
                        <img src="<?php echo $imgPath.'hcaptcha.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>hCaptcha</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="liqpay">
                        <img src="<?php echo $imgPath.'liqpay.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>LiqPay</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="emailling" data-type="mailchimp">
                        <img src="<?php echo $imgPath.'mailchimp.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Mailchimp</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="mollie">
                        <img src="<?php echo $imgPath.'mollie.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Mollie</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="payfast">
                        <img src="<?php echo $imgPath.'payfast.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>PayFast</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="paypal_sdk">
                        <img src="<?php echo $imgPath.'paypal.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>PayPal</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="payu_latam">
                        <img src="<?php echo $imgPath.'payu.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>PayU Latam</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="payupl">
                        <img src="<?php echo $imgPath.'payu.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>PayU Polska</span>
                    </div>
                    <div class="integrations-element ba-work-area-element"
                        data-group="data-storage" data-type="pdf_submissions">
                        <img src="<?php echo $imgPath.'pdf-submissions.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>PDF Submissions</span>
                    </div>
                    <div class="integrations-element ba-work-area-element<?php echo baformsHelper::checkIntegration('redsys'); ?>"
                        data-group="payment" data-type="redsys">
                        <img src="<?php echo $imgPath.'redsys.png'; ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Redsys</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="robokassa">
                        <img src="<?php echo $imgPath.'robokassa.png'; ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Robokassa</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="stripe">
                        <img src="<?php echo $imgPath.'stripe.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Stripe</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="other" data-type="telegram">
                        <img src="<?php echo $imgPath.'telegram.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Telegram</span>
                    </div>
                    <div class="integrations-element ba-work-area-element" data-group="payment" data-type="yandex_kassa">
                        <img src="<?php echo $imgPath.'yookassa.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>YooKassa</span>
                    </div>
                    <div class="integrations-element ba-work-area-element"
                        data-group="data-storage" data-type="zoho_crm">
                        <img src="<?php echo $imgPath.'zoho.png' ?>">
                        <i class="zmdi zmdi-check-circle"></i>
                        <span>Zoho CRM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="delete-dialog" class="ba-modal-sm modal hide" style="display:none">
    <div class="modal-body">
        <h3><?php echo JText::_('DELETE_ITEM'); ?></h3>
        <p class="modal-text can-delete"><?php echo JText::_('DELETE_QUESTION') ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="ba-btn" data-dismiss="modal">
            <?php echo JText::_('CANCEL') ?>
        </a>
        <a href="#" class="ba-btn-primary red-btn" id="apply-delete">
            <?php echo JText::_('DELETE') ?>
        </a>
    </div>
</div>
<div class="library-item-handle" id="library-item-handle" style="display: none;">
    <i class="zmdi zmdi-apps"></i>
</div>
<div id="library-placeholder" style="display: none;"><div></div></div>
<div id="custom-css-editor" data-enabled="1">
    <style type="text/css"></style>
    <div class="custom-css-editor-code" style="display: none !important;"><?php echo $this->formSettings->css; ?></div>
</div>
<textarea id="code-css-value" style="display:none;"><?php echo $this->formSettings->css; ?></textarea>
<textarea id="code-js-value" style="display:none;"><?php echo $this->formSettings->js; ?></textarea>
<input type="hidden" id="form-id" value="<?php echo $this->item->id; ?>">
<?php
if (!empty(baformsHelper::$fonts)) {
    $href = '//fonts.googleapis.com/css?family=';
    foreach (baformsHelper::$fonts as $i => $font) {
        $href .= $font.(isset(baformsHelper::$fonts[$i + 1]) ? '%7C' : '');
    }
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $href; ?>">
<?php
}
include(JPATH_COMPONENT.'/views/layout/templates.php');
$appItemsStr = json_encode($appItems);
?>
<script type="text/javascript">
    app.items = <?php echo $appItemsStr; ?>;
    app.design = <?php echo $this->formSettings->design; ?>;
    app.items.navigation = <?php echo $this->formSettings->navigation; ?>;
    app.conditionLogic = <?php echo $this->formSettings->condition_logic; ?>;
    app.setDesignCssVariables();
    if (app.items.navigation.style) {
        document.querySelector('.ba-forms-workspace-body').classList.add(app.items.navigation.style);
    }
    if (app.items.navigation.suffix) {
        document.querySelectorAll('.ba-form-page-navigation-wrapper, .ba-form-page-break').forEach((el) => {
            el.classList.add(app.items.navigation.suffix);
        });
    }
</script>