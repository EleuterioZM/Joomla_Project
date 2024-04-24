<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
include_once 'tfpdf.php';

class pdf extends tFPDF
{
	public $store;
    public $tags;

    public function prepareTags($general, $infos)
    {
        $tags = new stdClass();
        $tags->from = new stdClass();
        $tags->billed = new stdClass();
        $address = [$general->business_name];
        $array = [];
        if (!empty($general->country)) {
            $array[] = $general->country;
        }
        if (!empty($general->region)) {
            $array[] = $general->region;
        }
        $address[] = implode(', ', $array);
        $array = [];
        if (!empty($general->city)) {
            $array[] = $general->city;
        }
        if (!empty($general->street)) {
            $array[] = $general->street;
        }
        if (!empty($general->zip_code)) {
            $array[] = $general->zip_code;
        }
        $address[] = implode(', ', $array);
        $address[] = $general->email;
        $address[] = $general->phone;
        $tags->from->{'[All Fields]'} = implode("\n", $address);
        $tags->from->{'[Store Name]'} = $general->store_name;
        $tags->from->{'[Store Legal Business Name]'} = $general->business_name;
        $tags->from->{'[Store Phone]'} = $general->phone;
        $tags->from->{'[Store Email]'} = $general->email;
        $address = [];
        if (!empty($general->country)) {
            $address[] = $general->country;
        }
        if (!empty($general->region)) {
            $address[] = $general->region;
        }
        if (!empty($general->city)) {
            $address[] = $general->city;
        }
        if (!empty($general->street)) {
            $address[] = $general->street;
        }
        if (!empty($general->zip_code)) {
            $address[] = $general->zip_code;
        }
        $tags->from->{'[Store Address]'} = implode(', ', $address);
        $array = [];
        foreach ($infos as $info) {
            if ($info->type == 'headline' || $info->type == 'acceptance' || empty($info->value)) {
                continue;
            }
            $value = $info->value;
            if ($info->type == 'country') {
                $object = json_decode($value);
                $values = [];
                if (!empty($object->region)) {
                    $values[] = $object->region;
                }
                if (!empty($object->country)) {
                    $values[] = $object->country;
                }
                $value = implode(', ', $values);
            }
            $value = str_replace('; ', ', ', $value);
            $array[] = $value;
            $tags->billed->{'[Customer ID='.$info->customer_id.']'} = $value;
        }
        $tags->billed->{'[All Fields]'} = implode("\n", $array);

        $this->tags = $tags;
    }

    public function replaceTags($text, $key)
    {
        foreach ($this->tags->{$key} as $tag => $value) {
            $text = str_replace($tag, $value, $text);
        }
        $text = preg_replace('/\[Customer ID=\d+\]/', '', $text);

        return $text;
    }

	public function preparePrice($price, $symbol, $position)
    {
        $decimals = $this->store->currency->decimals;
        $separator = $this->store->currency->separator;
        $thousand = $this->store->currency->thousand;
        $price = round($price * 1, $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);
        if ($position == '') {
            $value = $symbol.' '.$price;
        } else {
            $value = $price.' '.$symbol;
        }

        return $value;
    }

    public function setCoordinates($l, $x, $y)
    {
        $this->SetLeftMargin($l);
        $this->setX($x);
        $this->setY($y);
    }

    public function setTextCell($l, $w, $h, $y, $text, $a, $f = false)
    {
        $this->setCoordinates($l, $l, $y);
        $this->MultiCell($w, $h, $text, 0, $a, $f);
        $y = $this->getY();

        return $y;
    }

    public function setCartTotal($l, $w, $r, $w1, $y, $title, $value, $item, $promo = false)
    {
        if ($value == JText::_('FREE')) {
            $price = $value;
        } else {
            $price = $this->preparePrice($value, $item->currency_symbol, $item->currency_position);
        }
        if ($promo) {
            $price = '-'.$price;
        }
        $y0 = $this->setTextCell($l, $w, 8, $y, $title, 'L');
        $y1 = $this->setTextCell($r, $w1, 8, $y, $price, 'L');
        $y = max($y0, $y1);

        return $y;
    }

    public function create($item, $general, $dest = 'D', $path = '')
    {
        if (!empty($path) && !is_dir($path)) {
    		return '';
    	}
        $invoice = gridboxHelper::$store->invoice;
        $dir = '/administrator/components/com_gridbox/assets/fonts/';
        $this->SetAutoPageBreak(true);
        $this->AddPage();
        $this->AddFont('Roboto', 'Regular', $dir.'Roboto-Regular.ttf', 'Roboto-Regular', true);
        $this->AddFont('Roboto', 'Bold', $dir.'Roboto-Bold.ttf', 'Roboto-Bold', true);
        $this->SetDrawColor(243, 243, 243);
        $margin = 20;
        $pw = $this->GetPageWidth() - $margin * 2;
        $this->setMargins($margin, $margin, $margin);
        $width = $pw * 0.45;
        $left = $margin;
        $right = $margin + $width + $pw * 0.1;
        $this->SetFont('Roboto', 'Bold', 18);
        $y = $y1 = $this->GetY();
        $y = $this->setTextCell($margin, $pw, 9, $y, JText::_('INVOICE'), 'L');
        $this->SetFont('Roboto', 'Regular', 10);
        $this->SetTextColor(149, 149, 149);
        $y = $this->setTextCell($left, $width, 6, $y, 'No. '.$item->order_number, 'L');
        $date = JDate::getInstance($item->date)->format(gridboxHelper::$website->date_format);
        $y = $this->setTextCell($left, $width, 6, $y, $date, 'L');
        if (!empty($invoice->logo)) {
            $h = $y - $y1;
            $this->Image(JPATH_ROOT.'/'.$invoice->logo, $right, $y1, 0, $h, '', '', 'R');
        }
        $y = $this->setTextCell($left, $pw, 10, $y, ' ', 'L');
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Roboto', 'Bold', 10);
        $this->setTextCell($left, $width, 10, $y, JText::_('FROM'), 'L');
        $y = $y1 = $this->setTextCell($right, $width, 10, $y, JText::_('BILLED_TO'), 'L');
        $this->SetFont('Roboto', 'Regular', 10);
        $this->SetTextColor(119, 119, 119);
        $this->prepareTags($general, $item->info);
        $text = $this->replaceTags($invoice->from, 'from');
        $y = $this->setTextCell($left, $width, 8, $y, $text, 'L');
        $this->SetTextColor(119, 119, 119);
        $this->SetFont('Roboto', 'Regular', 10);
        $text = $this->replaceTags($invoice->billed, 'billed');
        $y1 = $this->setTextCell($right, $width, 8, $y1, $text, 'L');
        if ($y1 > $y) {
            $y = $y1;
        }
        $y = $this->setTextCell($left, $pw, 8, $y, ' ', 'L');
        $this->SetFillColor(239, 239, 239);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Roboto', 'Bold', 10);
        $hasSku = false;
        foreach($item->products as $product) {
            if (empty($product->sku)) {
                continue;
            }
            $hasSku = true;
            break;
        }
        $header = [];
        if ($hasSku) {
            $header[] = JText::_('SKU');
        }
        $header[] = JText::_('PRODUCT');
        $header[] = JText::_('QTY');
        if (!empty($item->tax) && $item->tax_mode == 'incl') {
            $header[] = JText::_('NET_PRICE');
        }
        $header[] = JText::_('PRICE');
        $header[] = JText::_('AMOUNT');
        $n = count($header);
        if ($hasSku && !empty($item->tax) && $item->tax_mode == 'incl') {
            $tw = [$pw * 0.2, $pw * 0.25, $pw * 0.1, $pw * 0.15, $pw * 0.15, $pw * 0.15];
        } else if ($hasSku) {
            $tw = [$pw * 0.2, $pw * 0.4, $pw * 0.1, $pw * 0.15, $pw * 0.15];
        } else if (!empty($item->tax) && $item->tax_mode == 'incl') {
            $tw = [$pw * 0.45, $pw * 0.1, $pw * 0.15, $pw * 0.15, $pw * 0.15];
        } else {
            $tw = [$pw * 0.6, $pw * 0.1, $pw * 0.15, $pw * 0.15];
        }
        $tl = [];
        foreach ($tw as $i => $value) {
            $tLeft = 0;
            for ($j = 0; $j < $i; $j++) {
                $tLeft += $tw[$j];
            }
            $tl[] = $tLeft;
        }
        $ys = [];
        for ($i = 0; $i < $n; $i++) {
            $ys[] = $this->setTextCell($tl[$i] + $margin, $tw[$i], 10, $y, $header[$i], 'L', true);
        }
        $max = max($ys);
        $min = min($ys);
        if ($min != $max) {
            for ($i = 0; $i < $n; $i++) {
                if ($ys[$i] != $max) {
                    $this->setTextCell($tl[$i] + $margin, $tw[$i], $max - $ys[$i], $ys[$i], ' ', 'L', true);
                }
            }
        }
        $y = $max;
        $this->SetTextColor(119, 119, 119);
        $taxes = [];
        foreach($item->products as $product) {
            $this->SetFont('Roboto', 'Regular', 10);
            $y = $this->setTextCell($left, $pw, 5, $y, ' ', 'L');
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
            $extraPrice = isset($product->extra_options->price) ? $product->extra_options->price : 0;
            $amount = ($product->sale_price !== '' ? $product->sale_price : $product->price) + $extraPrice * $product->quantity;
            $price = $amount / $product->quantity;
            $priceText = $this->preparePrice($price, $item->currency_symbol, $item->currency_position);
            $info = [];
            foreach ($product->variations as $variation) {
                $info[] = $variation->title.' '.$variation->value;
            }
            $infoStr = implode('/', $info);
            $amountText = $this->preparePrice($amount, $item->currency_symbol, $item->currency_position);
            $ys = [];
            $texts = [];
            if ($hasSku) {
                $texts[] = $product->sku;
            }
            $texts[] = $product->title;
            $texts[] = $product->quantity;
            if (!empty($product->tax_rate) && $item->tax_mode == 'incl') {
                $netPrice = $price - ($price - $price / ($product->tax_rate / 100 + 1));
                $netText = $this->preparePrice($netPrice, $item->currency_symbol, $item->currency_position);
                $texts[] = $netText;
            }
            $texts[] = $priceText;
            $texts[] = $amountText;
            foreach ($tw as $i => $value) {
                $ys[] = $this->setTextCell($tl[$i] + $margin, $tw[$i], 5, $y, $texts[$i], 'L');
            }
            $y = max($ys);
            $isBooking = $product->product_type == 'booking';
            if ($isBooking) {
                $this->SetFontSize(7);
                $str = JText::_('DATE') . ': ' . gridboxHelper::formatDate($product->booking->start_date) .
                    (!empty($product->booking->end_date) ? (' - ' . gridboxHelper::formatDate($product->booking->end_date)) : '');
                $y = $this->setTextCell($tl[0] + $margin, $tw[0], 5, $y, $str, 'L');
            }
            if ($isBooking && !empty($product->booking->start_time)) {
                $str = JText::_('TIME') . ': ' . $product->booking->start_time;
                $y = $this->setTextCell($tl[0] + $margin, $tw[0], 5, $y, $str, 'L');
            }
            if ($isBooking && !empty($product->booking->guests)) {
                $str = JText::_('GUESTS') . ': ' . $product->booking->guests;
                $y = $this->setTextCell($tl[0] + $margin, $tw[0], 5, $y, $str, 'L');
            }
            if (!empty($info)) {
                $this->SetFontSize(7);
                $y = $this->setTextCell($tl[0] + $margin, $tw[0], 5, $y, $infoStr, 'L');
            }
            if (isset($product->extra_options->items)) {
                foreach ($product->extra_options->items as $extra) {
                    if (isset($extra->attachments)) {
                        continue;
                    }
                    $y = $this->setTextCell($tl[0] + $margin, $tw[0], 2, $y, ' ', 'L');
                    $this->SetTextColor(119, 119, 119);
                    $this->SetFont('Roboto', 'Regular', 9);
                    $y = $this->setTextCell($tl[0] + $margin, $tw[0], 5, $y, $extra->title, 'L');
                    $this->SetFont('Roboto', 'Regular', 10);
                    $this->SetTextColor(119, 119, 119);
                    $count = 0;
                    foreach ($extra->values as $value) {
                        $ys = [];
                        $this->SetFontSize(7);
                        $ys[] = $this->setTextCell($tl[0] + $margin, $tw[0], 5, $y, $value->value, 'L');
                        $i = count($tw) - 1;
                        if ($value->price != '') {
                            $price = $value->price * $product->quantity;
                            $price = $this->preparePrice($price, $item->currency_symbol, $item->currency_position);
                        } else {
                            $price = '';
                        }
                        $this->SetFontSize(10);
                        $ys[] = $this->setTextCell($tl[$i] + $margin, $tw[$i], 5, $y, $price, 'L');
                        $y = max($ys);
                        $count++;
                    }
                }
            }
        }
        $this->SetFontSize(10);
        $taxCount = count($taxes);
        $y = $this->setTextCell($left, $pw, 5, $y, ' ', 'L');
        $this->Line($margin, $y, $pw + $margin, $y);
        $y = $this->setTextCell($left, $pw, 8, $y, ' ', 'L');
        $left = $pw * 0.6 + $margin;
        $w = $pw * 0.25;
        $w1 = $pw * 0.15;
        $right = $left + $w;
        $this->SetFont('Roboto', 'Bold', 10);
        $this->SetTextColor(0, 0, 0);
        $y = $this->setCartTotal($left, $w, $right, $w1, $y, JText::_('SUBTOTAL'), $item->subtotal, $item);
        $this->SetFont('Roboto', 'Regular', 10);
        $this->SetTextColor(119, 119, 119);
        if ($item->promo) {
            $y = $this->setCartTotal($left, $w, $right, $w1, $y, JText::_('DISCOUNT'), $item->promo->value, $item, true);
        }
        if ($item->shipping) {
            $price = $item->shipping->type != 'free' && $item->shipping->type != 'pickup' ? $item->shipping->price : JText::_('FREE');
            $y = $this->setCartTotal($left, $w, $right, $w1, $y, JText::_('SHIPPING'), $price, $item);
        }
        if ($item->shipping && $item->shipping->tax != '' && $item->tax_mode == 'incl') {
            $this->SetFontSize(7);
            $price = $this->preparePrice($item->shipping->tax, $item->currency_symbol, $item->currency_position);
            $y = $this->setTextCell($left, $w + $w1, 5, $y, JText::_('INCLUDES').' '.$item->shipping->tax_title.' '.$price, 'L');
            $item->tax = $item->tax * 1 + $item->shipping->tax;
            if ($taxCount == 1) {
                foreach ($taxes as $tax) {
                    if ($tax->title != $item->shipping->tax_title || $tax->rate != $item->shipping->tax_rate) {
                        $taxCount++;
                    }
                }
            }
            $this->SetFontSize(10);
        } else if ($item->shipping && $item->shipping->tax != '') {
            $y = $this->setCartTotal($left, $w, $right, $w1, $y, JText::_('TAX_ON_SHIPPING'), $item->shipping->tax, $item);
        }
        if (!empty($item->tax) && $item->tax_mode == 'excl' && $taxCount == 0) {
            $y = $this->setCartTotal($left, $w, $right, $w1, $y, JText::_('TAX'), $item->tax, $item);
        } else if (!empty($item->tax) && $item->tax_mode == 'excl') {
            foreach ($taxes as $tax) {
                $y = $this->setCartTotal($left, $w, $right, $w1, $y, $tax->title, $tax->amount, $item);
            }
        }
        $y = $this->setTextCell($left, $pw, 8, $y, ' ', 'L');
        $this->SetFont('Roboto', 'Bold', 10);
        $this->SetTextColor(0, 0, 0);
        $y = $this->setCartTotal($left, $w, $right, $w1, $y, JText::_('TOTAL'), $item->total, $item);
        if (!empty($item->tax) && $item->tax_mode == 'incl') {
            $this->SetFont('Roboto', 'Regular', 7);
            $this->SetTextColor(119, 119, 119);
            $text = $taxCount == 1 ? JText::_('INCLUDES').' '.$taxes[0]->rate.'% '.$taxes[0]->title : JText::_('INCLUDING_TAXES');
            $price = $this->preparePrice($item->tax, $item->currency_symbol, $item->currency_position);
            $y = $this->setTextCell($left, $w + $w1, 5, $y, $text.' '.$price, 'L');
        }
        $path .= 'order-'.str_replace('#', '', $item->order_number).'.pdf';
        $this->Output($dest, $path);

        return $path;
    }

    public function Footer()
    {
        if (empty(gridboxHelper::$store->invoice->footer)){
            return;
        }
        $margin = 20;
        $left = $margin;
        $pw = $this->GetPageWidth() - $margin * 2;
        $array = explode('\n', gridboxHelper::$store->invoice->footer);
        $this->SetY($margin * -1);
        $y = $this->GetY();
        $y -= count($array) * 10;
        $this->SetFont('Roboto', 'Regular', 10);
        $this->SetTextColor(119, 119, 119);
        $this->setTextCell($left, $pw, 6, $y, gridboxHelper::$store->invoice->footer, 'L');
    }
}