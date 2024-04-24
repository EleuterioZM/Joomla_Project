<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$className = 'fields-icons-'.self::$design->field->icon->{'text-align'};
if (!empty($navigation->style)) {
    $className .= ' '.$navigation->style;
}
if (!empty(self::$design->theme->suffix)) {
    $className .= ' '.self::$design->theme->suffix;
}
if (self::$design->theme->layout == 'lightbox') {
    $trigger = json_encode(self::$design->lightbox->trigger);
    $trigger = htmlspecialchars($trigger, ENT_QUOTES);
    $session = json_encode(self::$design->lightbox->session);
    $session = htmlspecialchars($session, ENT_QUOTES);
    $lightboxClassName = 'ba-forms-modal-wrapper ba-form-lightbox-layout ba-form-'.$id.' '.self::$design->lightbox->animation;
    $lightboxClassName .= ' lightbox-position-'.self::$design->lightbox->position;
    if (!empty(self::$design->lightbox->suffix)) {
        $className .= ' '.self::$design->lightbox->suffix;
    }
?>
<div class="<?php echo $lightboxClassName; ?>" data-position="<?php echo self::$design->lightbox->position; ?>"
    data-id="<?php echo $id; ?>" data-trigger="<?php echo $trigger; ?>" data-session="<?php echo $session; ?>"
    style="opacity: 0; pointer-events: none;">
    <div class="ba-forms-modal-backdrop" data-dismiss="formsModal"></div>
    <div class="ba-forms-modal">
<?php
}
?>
<div class="com-baforms-wrapper">
    <form novalidate class="ba-form-<?php echo $id; ?> <?php echo $className; ?>" action="<?php echo $url; ?>"
        method="post" enctype="multipart/form-data" data-id="<?php echo $id; ?>">
<?php
    $pagesCount = count($pages);
    foreach ($pages as $key => $page) {
        include $path.'page.php';
        echo $out;
    }
    $footer = self::getFormsFooter($id);
?>
        <div class="ba-form-footer">
<?php
            echo $footer;
?>
        </div>
    </form>
</div>
<?php
if (self::$design->theme->layout == 'lightbox') {
?>        
    </div>
</div>
<?php
}
?>
<?php
$out = ob_get_contents();
ob_end_clean();