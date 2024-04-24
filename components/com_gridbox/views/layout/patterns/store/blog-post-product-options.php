<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
if (isset($data->default)) {
    $vars = explode('+', $data->default->variation);
} else {
    $vars = array();
}
$varsMap = new stdClass();
foreach ($variations_map as $variation) {
    if ($variation->field_type != 'color' && $variation->field_type != 'image') {
        continue;
    }
    if (!isset($varsMap->{$variation->order_group})) {
        $varsMap->{$variation->order_group} = new stdClass();
        $varsMap->{$variation->order_group}->key = $variation->field_key;
        $varsMap->{$variation->order_group}->type = $variation->field_type;
        $varsMap->{$variation->order_group}->variations = array();
    }
    $varsMap->{$variation->order_group}->variations[] = $variation;
}
?>
<div class="ba-blog-post-product-options-wrapper">
<?php
foreach ($varsMap as $varMap) {
    if ($desktop && (!isset($desktop->store->{$varMap->key}) || !$desktop->store->{$varMap->key})) {
        continue;
    }
?>
    <div class="ba-blog-post-product-options" data-key="<?php echo $varMap->key; ?>" data-type="<?php echo $varMap->type; ?>">
<?php
    foreach ($varMap->variations as $variation) {
        $images = json_decode($variation->images);
        if ($variation->field_type == 'image') {
            if (!empty($images)) {
                $variation->image = $images[0];
            }
            $variation->image = !gridboxHelper::isExternal($variation->image) ? JUri::root().$variation->image : $variation->image;
            $value = 'url('.$variation->image.')';
        } else {
            $value = $variation->color;
        }
        $varClass = in_array($variation->option_key, $vars) ? ' active' : '';
        $img = !empty($productImages) ? $productImages[0]->img : $page->intro_image;
?>
        <span class="ba-blog-post-product-option<?php echo $varClass; ?>" data-image="<?php echo !empty($images) ? $images[0] : $img; ?>">
            <span style="--variation-value: <?php echo $value ?>;"></span>
            <span class="ba-tooltip ba-top"><?php echo $variation->value; ?></span>
        </span>
<?php
    }
?>
    </div>
<?php
}
?>
</div>
<?php
$productOptions = ob_get_contents();
ob_end_clean();