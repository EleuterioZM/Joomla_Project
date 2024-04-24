<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class pdf
{
    public $settings;
    public $fields;
    public $margin;
    public $width;
    public $left;
    public $right;
    
    public function __construct($fields = null, $settings = null, $margin = 20)
    {
        $this->settings = $settings;
        $this->fields = $fields;
        $this->margin = $margin;
    }

    public function setCoordinates($pdf, $x, $y)
    {
        $pdf->SetLeftMargin($x);
        $pdf->setX($x);
        $pdf->setY($y);
    }

    public function saveSubmission($submission)
    {
        $pdf = $this->getTfpdf();
        $date = date('Y-m-d', strtotime($submission->date_time));
        $pdf->SetFont('Roboto', 'Bold', 14);
        $pdf->MultiCell(0, 10, ' ');
        $y = $pdf->GetY();
        $pdf->MultiCell(0, 12, $submission->title, 0, 'L');
        $this->setCoordinates($pdf, $this->right, $y);
        $pdf->SetFont('Roboto', 'Regular', 10);
        $pdf->SetTextColor(149, 149, 149);
        $pdf->MultiCell($this->width, 10, $date, 0, 'R', false);
        $y = $pdf->getY();
        $this->setCoordinates($pdf, $this->left, $y);
        $pdf->MultiCell(0, 7, ' ');
        foreach ($submission->message as $obj) {
            if (empty($obj->message) || $obj->type == 'upload') {
                continue;
            }
            $pdf->SetFont('Roboto', 'Bold', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(0, 5, $obj->title, 0, 'L');
            $pdf->SetTextColor(149, 149, 149);
            $pdf->SetFont('Roboto', 'Regular', 10);
            if ($obj->type == 'total') {
                $object = json_decode($obj->message);
                $thousand = $object->options->thousand;
                $separator = $object->options->separator;
                $decimals = $object->options->decimals;
                $position = $object->options->position;
                $symbol = $object->options->symbol;
                $total = $object->total * 1;
                if ($object->options->cart) {
                    foreach ($object->products as $products) {
                        foreach ($products as $product) {
                            $price = $this->renderPrice($product->total, $thousand, $separator, $decimals, $position, $symbol);
                            $str = $product->title.' x '.$product->quantity.': '.$price;
                            $pdf->MultiCell(0, 5, $str, 0, 'L');
                        }
                    }
                }
                $shipping = 0;
                if (isset($object->shipping) || isset($object->promo) || $object->options->tax->enable) {
                    $price = $this->renderPrice($object->total, $thousand, $separator, $decimals, $position, $symbol);
                    $str = JText::_('SUBTOTAL').': '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                if (isset($object->shipping)) {
                    $shipping = $object->shipping->price * 1;
                    $price = $this->renderPrice($object->shipping->price, $thousand, $separator, $decimals, $position, $symbol);
                    $str = JText::_('SHIPPING').': '.$object->shipping->title.' '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                if (isset($object->promo) && $object->promo == $object->options->promo->code) {
                    $discount = $object->options->promo->discount * 1;
                    if ($object->options->promo->unit == '%') {
                        $discount = $total * $discount / 100;
                    }
                    $total -= $discount;
                    $price = $this->renderPrice($discount, $thousand, $separator, $decimals, $position, $symbol);
                    $str = JText::_('DISCOUNT').': '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                if ($object->options->tax->enable) {
                    $tax = $total * $object->options->tax->value / 100;
                    $total += $tax;
                    $price = $this->renderPrice($tax, $thousand, $separator, $decimals, $position, $symbol);
                    $str = $object->options->tax->title.': '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                $total += $shipping;
                $price = $this->renderPrice($total, $thousand, $separator, $decimals, $position, $symbol);
                $str = JText::_('TOTAL').': '.$price;
                $pdf->MultiCell(0, 5, $str, 0, 'L');
            } else if ($obj->type == 'signature') {
                $this->addImage($pdf, JPATH_ROOT.'/'.$obj->message, 80);
            } else if ($obj->type == 'poll' || $obj->type == 'checkbox' || $obj->type == 'selectMultiple') {
                $array = explode('<br>', $obj->message);
                foreach ($array as $value) {
                    $value = str_replace(';', '', $value);
                    $value = strip_tags($value);
                    $pdf->MultiCell(0, 5, $value, 0, 'L');
                }
            } else {
                $text = strip_tags($obj->message);
                $pdf->MultiCell(0, 5, $text, 0, 'L');
            }
            $pdf->MultiCell(0, 3, ' ');
        }
        $pdf->Output('D', 'baforms.pdf');
    }

    public static function imageCreate($path) {
        $info = getimagesize($path);
        $ext = image_type_to_extension($info[2], false);
        switch ($ext) {
            case 'png':
                $imageCreate = 'imagecreatefrompng';
                break;
            case 'gif':
                $imageCreate = 'imagecreatefromgif';
                break;
            case 'webp':
                $imageCreate = 'imagecreatefromwebp';
                break;
            default:
                $imageCreate = 'imagecreatefromjpeg';
        }
        return $imageCreate;
    }

    public function addImage($pdf, $path, $w = null)
    {
        if (!is_file($path)) {
            return;
        }
        if (!$w) {
            $w = $pdf->GetPageWidth() - $this->margin * 2;
        }
        $y = $pdf->getY();
        $imageCreate = $this->imageCreate($path);
        $img = $imageCreate($path);
        $width = imagesx($img);
        $height = imagesy($img);
        $ratio = $width / $height;
        $h = $w / $ratio;
        if ($y + $h > $pdf->GetPageHeight() - $this->margin) {
            $pdf->AddPage();
            $y = $pdf->getY();
        }
        $pdf->Image($path, $this->left, $y, $w, $h);
        $pdf->setY($y + $h);
    }

    public function renderPrice($price, $thousand, $separator, $decimals, $position, $symbol)
    {
        $price = baformsHelper::renderPrice((string)$price, $thousand, $separator, $decimals);
        if (empty($position)) {
            $price = $symbol.' '.$price;
        } else {
            $price .= ' '.$symbol;
        }

        return $price;
    }

    public function getTfpdf($orientation = 'Portrait', $size = 'A4', $unit = 'mm')
    {
        $fonts = '/administrator/components/com_baforms/assets/fonts/';
        include JPATH_ROOT.'/components/com_baforms/libraries/pdf-submissions/tfpdf.php';
        $pdf = new tFPDF($orientation, $unit, $size);
        $pdf->SetAutoPageBreak(true);
        $pdf->AddPage();
        $pdf->AddFont('Roboto', 'Regular', $fonts.'Roboto-Regular.ttf', 'Roboto-Regular', true);
        $pdf->AddFont('Roboto', 'Bold', $fonts.'Roboto-Bold.ttf', 'Roboto-Bold', true);
        $pw = $pdf->GetPageWidth() - $this->margin * 2;
        $pdf->setMargins($this->margin, $this->margin, $this->margin);
        $this->width = $pw * 0.45;
        $this->left = $this->margin;
        $this->right = $this->margin + $this->width + $pw * 0.1;

        return $pdf;
    }

    public function create($fileName)
    {
        $dir = JPATH_ROOT.'/'.PDF_STORAGE;
        if (PDF_STORAGE == 'images/baforms/pdf' && !JFolder::exists($dir)) {
            JFolder::create(JPATH_ROOT.'/images/baforms');
            JFolder::create($dir);
        }
        if (!JFolder::exists($dir)) {
            return;
        }
        $pdf = $this->getTfpdf($this->settings->orientation, $this->settings->size);
        if ($this->settings->title) {
            $pdf->SetFont('Roboto', 'Bold', 24);
            $pdf->MultiCell(0, 10, ' ');
            $pdf->MultiCell(0, 12, baformsHelper::$shortCodes->{'[Form Title]'}, 0, 'C');
            $pdf->MultiCell(0, 7, ' ');
        }
        foreach ($this->fields as $field) {
            if ((!$this->settings->empty && $field->value == '') || $field->type == 'upload') {
                continue;
            }
            $pdf->SetFont('Roboto', 'Bold', 10);
            $pdf->MultiCell(0, 10, $field->title, 0, 'L');
            $pdf->SetFont('Roboto', 'Regular', 10);
            if ($field->type == 'total') {
                $thousand = $field->options->thousand;
                $separator = $field->options->separator;
                $decimals = $field->options->decimals;
                $position = $field->options->position;
                $symbol = $field->options->symbol;
                $str = '';
                $object = $field->object;
                $total = $object->total * 1;
                if ($field->options->cart) {
                    foreach ($object->products as $products) {
                        foreach ($products as $product) {
                            $price = $this->renderPrice($product->total, $thousand, $separator, $decimals, $position, $symbol);
                            $str = $product->title.' x '.$product->quantity.': '.$price;
                            $pdf->MultiCell(0, 5, $str, 0, 'L');
                        }
                    }
                }
                if (isset($object->shipping) || isset($object->promo) || $field->options->tax->enable) {
                    $price = $this->renderPrice($object->total, $thousand, $separator, $decimals, $position, $symbol);
                    $str = JText::_('SUBTOTAL').': '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                if (isset($object->shipping)) {
                    $price = $this->renderPrice($object->shipping->price, $thousand, $separator, $decimals, $position, $symbol);
                    $str = JText::_('SHIPPING').': '.$object->shipping->title.' '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                if (isset($object->promo) && $object->promo == $field->options->promo->code) {
                    $discount = $field->options->promo->discount * 1;
                    if ($field->options->promo->unit == '%') {
                        $discount = $total * $discount / 100;
                    }
                    $price = $this->renderPrice($discount, $thousand, $separator, $decimals, $position, $symbol);
                    $str = JText::_('DISCOUNT').': '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                if ($field->options->tax->enable) {
                    $tax = $total * $field->options->tax->value / 100;
                    $total += $tax;
                    $price = $this->renderPrice($tax, $thousand, $separator, $decimals, $position, $symbol);
                    $str = $field->options->tax->title.': '.$price;
                    $pdf->MultiCell(0, 5, $str, 0, 'L');
                }
                $price = $this->renderPrice($field->value, $thousand, $separator, $decimals, $position, $symbol);
                $str = JText::_('TOTAL').': '.$price;
                $pdf->MultiCell(0, 5, $str, 0, 'L');
            } else if ($field->type == 'signature') {
                $this->addImage($pdf, JPATH_ROOT.'/'.$field->value, 80);
            } else if ($field->type == 'poll' || $field->type == 'checkbox' || $field->type == 'selectMultiple') {
                $array = explode('<br>', $field->value);
                foreach ($array as $value) {
                    $value = str_replace(';', '', $value);
                    $value = strip_tags($value);
                    $pdf->MultiCell(0, 5, $value, 0, 'L');
                }
            } else {
                $text = strip_tags($field->value);
                $pdf->MultiCell(0, 5, $text, 0, 'L');
            }
            $pdf->MultiCell(0, 3, ' ');
        }
        $pdf->Ln();
        $fileName = JFile::makeSafe($fileName);
        $i = 1;
        $name = $fileName;
        $dir .= '/';
        while (JFile::exists($dir.$name.'.pdf')) {
            $name = $fileName.'-'.($i++);
        }
        $file = $dir.$name.'.pdf';
        $pdf->Output('F', $file);

        return $file;
    }
}