<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$thousand = $field->options->thousand;
$separator = $field->options->separator;
$decimals = $field->options->decimals;
$total = $object->total * 1;
if (isset($data['payment_id'])) {
?>
<div style="max-width: 100%;  width: 550px; display: flex;margin: 0 auto;">
    <span style="color:#999;line-height:54px;font-size:16px;white-space: nowrap;">Payment Id:</span>
    <span style="color: #333;font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;width: 100%;word-break: break-word;"><?php echo $data['payment_id']; ?></span>
</div>
<?php
} else if (isset($data['transId'])) {
?>
<div style="max-width: 100%;  width: 550px; display: flex;margin: 0 auto;">
    <span style="color:#999;line-height:54px;font-size:16px;white-space: nowrap;">Transaction Id:</span>
    <span style="color: #333;font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;width: 100%;word-break: break-word;"><?php echo $data['transId']; ?></span>
</div>
<?php
} else if (isset($data['invoiceId'])) {
?>
<div style="max-width: 100%;  width: 550px; display: flex;margin: 0 auto;">
    <span style="color:#999;line-height:54px;font-size:16px;white-space: nowrap;">Invoice Id:</span>
    <span style="color: #333;font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;width: 100%;word-break: break-word;"><?php echo $data['invoiceId']; ?></span>
</div>
<?php
}
?>
<div style="margin: 0 auto; max-width:100%;width:550px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:100%; border-collapse: collapse;">
    <tbody>
        <tr style="width:100%">
            <td style="font-weight: 500;line-height: 54px;font-size: 16px;">
                <?php echo $field->options->title; ?>
            </td>
        </tr>
<?php
if ($field->options->cart) {
    foreach ($object->products as $products) {
        foreach ($products as $product) {
            $price = baformsHelper::renderPrice((string)$product->total, $thousand, $separator, $decimals);
            if (empty($field->options->position)) {
                $price = $field->options->symbol.' '.$price;
            } else {
                $price .= ' '.$field->options->symbol;
            }
?>
        <tr style="border-bottom: 1px solid #f3f3f3;padding: 10px 0; width: 100%;">
            <td style="color: #999; line-height: 54px;font-size: 16px;">
                <?php echo $product->title; ?>
            </td>
            <td style="color: #999;line-height: 54px;font-size: 16px;">
                <?php echo $product->quantity; ?>
            </td>
            <td style="color: #333; font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;">
                <?php echo $price; ?>
            </td>
        </tr>
<?php
        }
    }
}
?>
    </tbody>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:100%; border-collapse: collapse;">
    <tbody>
<?php
    $shipping = 0;
    if (isset($object->shipping) || isset($object->promo) || $field->options->tax->enable) {
        $price = baformsHelper::renderPrice((string)$object->total, $thousand, $separator, $decimals);
        if (empty($field->options->position)) {
            $price = $field->options->symbol.' '.$price;
        } else {
            $price .= ' '.$field->options->symbol;
        }
?>
        <tr style="padding: 10px 0; width: 100%;">
            <td style="color: #333; font-weight: bold;line-height: 54px;font-size: 16px;">
                <?php echo JText::_('SUBTOTAL'); ?>
            </td>
            <td></td>
            <td style="color: #333; font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;">
                <?php echo $price; ?>
            </td>
        </tr>
<?php
    }
?>
<?php
    if (isset($object->shipping)) {
        $shipping = $object->shipping->price * 1;
        $price = baformsHelper::renderPrice((string)$object->shipping->price, $thousand, $separator, $decimals);
        if (empty($field->options->position)) {
            $price = $field->options->symbol.' '.$price;
        } else {
            $price .= ' '.$field->options->symbol;
        }
?>
        <tr style="padding: 10px 0;">
            <td style="color: #999;line-height: 54px;font-size: 16px;">
                <?php echo JText::_('SHIPPING'); ?>
            </td>
            <td style="color: #999;line-height: 54px;font-size: 16px;text-align: right;">
                <?php echo $object->shipping->title; ?>
            </td>
            <td style="color: #333; font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;">
                <?php echo $price; ?>
            </td>
        </tr>
<?php
    }
?>
<?php
    if (isset($object->promo) && $object->promo == $field->options->promo->code) {
        $discount = $field->options->promo->discount * 1;
        if ($field->options->promo->unit == '%') {
            $discount = $total * $discount / 100;
        }
        $total -= $discount;
        $price = baformsHelper::renderPrice((string)$discount, $thousand, $separator, $decimals);
        if (empty($field->options->position)) {
            $price = $field->options->symbol.' '.$price;
        } else {
            $price .= ' '.$field->options->symbol;
        }
?>
        <tr style="padding: 10px 0;">
            <td style="color: #999;line-height: 54px;font-size: 16px;">
                <?php echo JText::_('DISCOUNT'); ?>
            </td>
            <td></td>
            <td style="color: #333; font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;">
                <?php echo $price; ?>
            </td>
        </tr>
<?php
    }
?>
<?php
    if ($field->options->tax->enable) {
        $tax = $total * $field->options->tax->value / 100;
        $total += $tax;
        $price = baformsHelper::renderPrice((string)$tax, $thousand, $separator, $decimals);
        if (empty($field->options->position)) {
            $price = $field->options->symbol.' '.$price;
        } else {
            $price .= ' '.$field->options->symbol;
        }
?>
        <tr style="padding: 10px 0;">
            <td style="color: #999;line-height: 54px;font-size: 16px;">
                <?php echo $field->options->tax->title; ?>
            </td>
            <td></td>
            <td style="color: #333; font-weight: bold;text-align: right;line-height: 54px;font-size: 16px;">
                <?php echo $price; ?>
            </td>
        </tr>
<?php
    }
    $total += $shipping;
    $price = baformsHelper::renderPrice((string)$total, $thousand, $separator, $decimals);
    if (empty($field->options->position)) {
        $price = $field->options->symbol.' '.$price;
    } else {
        $price .= ' '.$field->options->symbol;
    }
?>
        <tr style="border-top: 1px solid #f3f3f3; padding: 10px 0;">
            <td style="color: #333; font-weight: bold;line-height: 54px;font-size: 16px;">
                <?php echo JText::_('TOTAL'); ?>
            </td>
            <td></td>
            <td style="color: #333; font-weight: bold;line-height: 54px;font-size: 16px;text-align: right;">
                <?php echo $price; ?>
            </td>
        </tr>
    </tbody>
</table>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();