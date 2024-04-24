<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$taxes = [];
$later = 0;
$prepaid = 0;
?>
<div style="border-bottom: 1px solid #f3f3f3;">
<?php
foreach ($products as $product) {
    if (!empty($product->extra_options)) {
        $extra_options = json_decode($product->extra_options);
    } else {
        $extra_options = new stdClass();
    }
    $extra_options->items = $extra_options->items ?? new stdClass();
    if (!empty($product->tax)) {
        $exist = false;
        foreach ($taxes as $tax) {
            if ($tax->title == $product->tax_title && $tax->rate == $product->tax_rate) {
                $tax->amount += $product->tax * 1;
                $exist = true;
                break;
            }
        }
        if (!$exist) {
            $tax = new stdClass();
            $tax->amount = $product->tax * 1;
            $tax->title = $product->tax_title;
            $tax->rate = $product->tax_rate;
            $taxes[] = $tax;
        }
    }
    if ($product->product_type == 'booking' && !empty($product->booking->later)) {
        $later += $product->booking->later;
        $prepaid += $product->booking->prepaid;
    }
    $price = $product->sale_price !== '' ? $product->sale_price : $product->price;
    $price = $this->preparePrice($price, $currency->thousand, $currency->separator, $currency->decimals);
    $image = (!empty($product->image) && !gridboxHelper::isExternal($product->image) ? JUri::root() : '').$product->image;
    $info = [];
    foreach ($product->variations as $variation) {
        $info[] = '<span>'.$variation->title.' '.$variation->value.'</span>';
    }
    $infoStr = implode('<span>/</span>', $info);
?>
    <div style="margin-top: 25px;margin-bottom: 25px; display: flex; overflow: hidden;">
<?php
    if (!empty($image)) {
?>
        <div style="margin-right: 25px;">
            <img src="<?php echo $image; ?>" style="width: 80px;">
        </div>
<?php
    }
?>
        <div style="width: 100%;">
            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                <div style="display: inline-block; flex-grow: 1;width: 100%;">
<?php
                if (!empty($product->sku)) {
?>
                    <span style="display: block;margin-bottom: 5px;font-size:10px;color:#777;"><?php echo JText::_('SKU').': '.$product->sku; ?></span>
<?php
                }
?>
                    <span style="font-weight: bold;display: block;margin-bottom: 5px;"><?php echo $product->title; ?></span>
<?php
            if ($product->product_type != 'digital' && $product->product_type != 'subscription') {
?>
                    <span style="color:#777;font-size: 12px;line-height: 2em;white-space: nowrap;"><?php echo $infoStr; ?></span>
<?php
            } else if ($product->product_type == 'digital' && $order->status == 'completed') {
                $link = JUri::root().'index.php?option=com_gridbox&task=store.downloadDigitalFile&file=';
                $link .= $product->product_token;
?>
                    <a href="<?php echo $link; ?>"><?php echo JText::_('DOWNLOAD_FILE'); ?></a>
<?php
            }
?>
                </div>
<?php
            if (empty($product->product_type) || $product->product_type == 'physical') {
?>
                <div style="display: inline-block;line-height: 1.5;margin: 0 10px;white-space: nowrap;">
                    x <?php echo $product->quantity; ?>
                </div>
<?php
            }
?>
                <div style="float: right;white-space: nowrap;">
<?php
                if (empty($currency->position)) {
?>
                    <span style="font-weight: bold;"><?php echo $currency->symbol; ?></span>
<?php
                }
?>
                    <span style="font-weight: bold;"><?php echo $price; ?></span>
<?php
                if (!empty($currency->position)) {
?>
                    <span style="font-weight: bold;"><?php echo $currency->symbol; ?></span>
<?php
                }
?>
                </div>
            </div>
        
<?php
        if ($product->product_type == 'booking') {
            $date = gridboxHelper::formatDate($product->booking->start_date);
            
            if (!empty($product->booking->end_date)) {
                $date .= ' - '.gridboxHelper::formatDate($product->booking->end_date);
            }
?>
            <div style="margin-bottom: 30px;">
                <div style="line-height: 2em;">
                    <span><?php echo JText::_('DATE'); ?>:</span>
                    <span><?php echo $date; ?></span>
                </div>
<?php
                if (!empty($product->booking->start_time)) {
?>
                    <div style="line-height: 2em;">
                        <span><?php echo JText::_('TIME'); ?>:</span>
                        <span><?php echo $product->booking->start_time; ?></span>
                    </div>
<?php
                }
                if (!empty($product->booking->guests)) {
?>
                    <div style="line-height: 2em;">
                        <span><?php echo JText::_('GUESTS'); ?>:</span>
                        <span><?php echo $product->booking->guests; ?></span>
                    </div>
<?php
                }
?>
            </div>
<?php
        }
        foreach ($extra_options->items as $extra) {
            if (isset($extra->attachments)) {
                continue;
            }
?>
            
            <div style="margin-bottom: 30px;">
                <span style="font-size: .8em;font-weight: bold;margin-bottom: 10px;display: block;"><?php echo $extra->title ?></span>
                <div style="font-size: .8em;line-height: normal;color: #777;">
<?php
                    foreach ($extra->values as $extra_value) {
?>
                    <div style="margin-bottom: 10px;">
                        <span><?php echo $extra_value->value; ?></span>
<?php
                        if ($extra_value->price != '') {
                $extra_price = $extra_value->price * $product->quantity;
                $extra_price = $this->preparePrice($extra_price, $currency->thousand, $currency->separator, $currency->decimals);
?>
                        <span style="float: right;">
<?php
                        if (empty($currency->position)) {
?>
                            <span><?php echo $currency->symbol; ?></span>
<?php
                        }
?>
                            <span><?php echo $extra_price; ?></span>
<?php
                        if (!empty($currency->position)) {
?>
                            <span><?php echo $currency->symbol; ?></span>
<?php
                        }
?>
                        </span>
<?php
                        }
?>
                    </div>
<?php
                    }
?>
                </div>
            </div>
<?php
        }
?>
        </div>
    </div>
<?php
}
?>
</div>
<?php
$taxCount = count($taxes);
if ($shipping) {
    $price = $this->preparePrice($shipping->price, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div style="margin-top: 25px;margin-bottom: 25px;">
    <div style="margin-bottom: 10px; font-weight: bold;"><?php echo JText::_('SHIPPING'); ?></div>
    <div>
        <span style="display: inline-block; text-align: left;"><?php echo $shipping->title; ?></span>
        <div style="float: right;">
<?php
    if (empty($currency->position)) {
?>
        <span style="font-weight: bold;"><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span style="font-weight: bold;"><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span style="font-weight: bold;"><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        </div>
    </div>
</div>
<?php
}
?>
<?php
if ($payment || $later != 0) {
?>
<div style="margin-top: 25px;margin-bottom: 25px;padding-bottom: 25px;border-bottom: 1px solid #f3f3f3;">
    <div style="margin-bottom: 10px; font-weight: bold;"><?php echo JText::_('PAYMENT'); ?></div>
<?php
    if ($later == 0) {
?>
        <div>
            <span><?php echo $payment->title; ?></span>
        </div>
<?php
    } else {
        $price = $this->preparePrice($prepaid, $currency->thousand, $currency->separator, $currency->decimals);
?>
        <div style="width: 50%;display: inline-block; line-height: 2em;">
            <span><?php echo JText::_('ALREADY_PAID'); ?></span>
        </div>
        <div style="float: right; line-height: 2em;">
<?php
            if (empty($currency->position)) {
?>
                <span><?php echo $currency->symbol; ?></span>
<?php
            }
?>
                <span><?php echo $price; ?></span>
<?php
            if (!empty($currency->position)) {
?>
                <span><?php echo $currency->symbol; ?></span>
<?php
            }
?>
        </div>
<?php
        $price = $this->preparePrice($later, $currency->thousand, $currency->separator, $currency->decimals);
?>
        <div style="width: 50%;display: inline-block; line-height: 2em;">
            <span><?php echo JText::_('LEFT_TO_PAY'); ?></span>
        </div>
        <div style="float: right;line-height: 2em;">
<?php
            if (empty($currency->position)) {
?>
                <span><?php echo $currency->symbol; ?></span>
<?php
            }
?>
                <span><?php echo $price; ?></span>
<?php
            if (!empty($currency->position)) {
?>
                <span><?php echo $currency->symbol; ?></span>
<?php
            }
?>
        </div>
<?php
    }
?>
</div>
<?php
}
?>
<?php
$price = $this->preparePrice($order->subtotal, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div style="margin-top: 25px;margin-bottom: 25px; font-weight: bold;">
    <div style="display: inline-block;"><?php echo JText::_('SUBTOTAL'); ?>
    </div>
    <div style="float: right;">
<?php
        if (empty($currency->position)) {
?>
            <span><?php echo $currency->symbol; ?></span>
<?php
        }
?>
            <span><?php echo $price; ?></span>
<?php
        if (!empty($currency->position)) {
?>
            <span><?php echo $currency->symbol; ?></span>
<?php
        }
?>
    </div>
</div>
<?php
if ($discount) {
    $price = $this->preparePrice($discount->value, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div style="font-weight: bold; margin-bottom: 25px;">
    <div style="display: inline-block;"><?php echo JText::_('DISCOUNT'); ?></div>
    <div style="float: right;">
        <span>-</span>
<?php
    if (empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
    </div>
</div>
<?php
}
?>
<?php
if ($shipping) {
    $price = $this->preparePrice($shipping->price, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div style="font-weight: bold;margin-bottom: 25px;">
    <div style="display: inline-block;"><?php echo JText::_('SHIPPING'); ?></div>
    <div style="float: right;">
<?php
    if (empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
    </div>
</div>
<?php
}
?>
<?php
if ($shipping && !empty($shipping->tax)) {
    $price = $this->preparePrice($shipping->tax, $currency->thousand, $currency->separator, $currency->decimals);
    if ($order->tax_mode == 'incl') {
        $title = JText::_('INCLUDES').' '.$shipping->tax_title;
        $order->tax = $order->tax * 1 + $shipping->tax * 1;
        if ($taxCount == 1) {
            foreach ($taxes as $tax) {
                if ($tax->title != $shipping->tax_title || $tax->rate != $shipping->tax_rate) {
                    $taxCount++;
                }
            }
        }
    } else {
        $title = JText::_('TAX_ON_SHIPPING');
    }
?>
<div style="font-weight: bold;margin-bottom: 25px; margin-top: -20px;">
    <div style="display: inline-block;font-size: 12px; line-height: normal;"><?php echo $title; ?></div>
    <div style="display: inline-block;font-size: 12px; line-height: normal;">
<?php
    if (empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
    </div>
</div>
<?php
}
?>
<?php
if (!empty($order->tax) && $order->tax_mode == 'excl' && $taxCount == 0) {
    $price = $this->preparePrice($order->tax, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div style="font-weight: bold;margin-bottom: 25px;">
    <div style="display: inline-block;"><?php echo JText::_('TAX'); ?></div>
    <div style="float: right;">
<?php
    if (empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
    </div>
</div>
<?php
} else if (!empty($order->tax) && $order->tax_mode == 'excl') {
    foreach ($taxes as $tax) {
        $price = $this->preparePrice($tax->amount, $currency->thousand, $currency->separator, $currency->decimals);
?>
       <div style="font-weight: bold;margin-bottom: 25px;">
            <div style="display: inline-block;"><?php echo $tax->title; ?></div>
            <div style="float: right;">
<?php
            if (empty($currency->position)) {
?>
                <span><?php echo $currency->symbol; ?></span>
<?php
            }
?>
                <span><?php echo $price; ?></span>
<?php
            if (!empty($currency->position)) {
?>
                <span><?php echo $currency->symbol; ?></span>
<?php
            }
?>
            </div>
        </div> 
<?php
    }
}
?>
<?php
$price = $this->preparePrice($order->total, $currency->thousand, $currency->separator, $currency->decimals);
?>
<div style="font-weight:bold; border-top: 1px solid #f3f3f3;padding-top: 25px;">
    <div style="display: inline-block;line-height: 2em;"><?php echo JText::_('TOTAL'); ?></div>
    <div style="float: right; font-size: 2em; line-height: 1em;">
<?php
    if (empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
    </div>
</div>

<?php
if (!empty($order->tax) && $order->tax_mode == 'incl') {
    $price = $this->preparePrice($order->tax, $currency->thousand, $currency->separator, $currency->decimals);
    $text = $taxCount == 1 ? (JText::_('INCLUDES').' '.$taxes[0]->rate.'% '.$taxes[0]->title) : JText::_('INCLUDING_TAXES');
?>

<div style="font-weight:bold;">
    <div style="display: inline-block;font-size: 12px;line-height: normal;"><?php echo $text; ?></div>
    <div style="display: inline-block; font-size: 12px; line-height: normal;">
<?php
    if (empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
        <span><?php echo $price; ?></span>
<?php
    if (!empty($currency->position)) {
?>
        <span><?php echo $currency->symbol; ?></span>
<?php
    }
?>
    </div>
</div>
<?php
}

$out = ob_get_contents();
ob_end_clean();