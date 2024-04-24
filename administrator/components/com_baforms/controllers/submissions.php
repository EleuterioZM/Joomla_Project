<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');

class BaformsControllerSubmissions extends JControllerAdmin
{
    public function getModel($name = 'submission', $prefix = 'baformsModel', $config = array()) 
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function removeTmpAttachment()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $submission = $input->get('submission', 0, 'int');
        $filename = $input->get('filename', '', 'string');
        $model = $this->getModel();
        $model->removeTmpAttachment($id, $filename, $submission);
        exit();
    }

    public function contextDelete()
    {
        $id = $this->input->get('id', 0, 'int');
        $model = $this->getModel();
        $model->contextDelete($id);
        exit;
    }

    public function delete()
    {
        $cid = $this->input->get('cid', [], 'array');
        $model = $this->getModel();
        $model->deleteFiles($cid);
        parent::delete();
        echo JText::_('COM_BAFORMS_N_ITEMS_DELETED');
        exit;
    }

    public function readAll()
    {
        $model = $this->getModel();
        $model->readAll();
        exit;
    }

    public function unread()
    {
        $cid = $this->input->get('cid', [], 'array');
        $model = $this->getModel();
        foreach ($cid as $id) {
            $model->setReadStatus($id, 1);
        }
        exit;
    }
    
    public function setReadStatus()
    {
        $id = $this->input->get('id', 0, 'int');
        $state = $this->input->get('state', 0, 'int');
        $model = $this->getModel();
        $model->setReadStatus($id, $state);
        exit;
    }

    public function getTotal($str)
    {
        $object = json_decode($str);
        $thousand = $object->options->thousand;
        $separator = $object->options->separator;
        $decimals = $object->options->decimals;
        $total = $object->total * 1;
        if ($object->options->tax->enable) {
            $tax = $total * $object->options->tax->value / 100;
            $total += $tax;
        }
        if (isset($object->promo)) {
            $discount = $object->options->promo->discount * 1;
            if ($object->options->promo->unit == '%') {
                $discount = $total * $discount / 100;
            }
            $total -= $discount;
        }
        if (isset($object->shipping)) {
            $shipping = $object->shipping->price * 1;
        }
        $price = $this->renderPrice((string)$total, $thousand, $separator, $decimals);
        if (!empty($object->options->position)) {
            $price .= ' '.$object->options->symbol;
        } else {
            $price = $object->options->symbol.' '.$price;
        }

        return $price;
    }

    public function renderPrice($value, $thousand, $separator, $decimals)
    {
        $delta = $value < 0 ? '-' : '';
        $value = str_replace('-', '', $value);
        $priceArray = explode('.', $value);
        $priceThousand = $priceArray[0];
        $priceDecimal = isset($priceArray[1]) ? $priceArray[1] : '';
        $value = '';
        if (($pricestrlen = strlen($priceThousand)) > 3 && $thousand != '') {
            for ($i = 0; $i < $pricestrlen; $i++) {
                if ($i % 3 == 0 && $i != 0) {
                    $value .= $thousand;
                }
                $value .= $priceThousand[$pricestrlen - 1 - $i];
            }
            $value = strrev($value);
        } else {
            $value .= $priceThousand;
        }
        if ($decimals != 0) {
            $value .= $separator;
            for ($i = 0; $i < $decimals; $i++) {
                $value .= isset($priceDecimal[$i]) ? $priceDecimal[$i] : '0';
            }
        }

        return $delta.$value;
    }
    
    public function exportXML()
    {
        $data = explode(',', $_POST['data']);
        $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $root = $doc->createElement('submissions');
        $root = $doc->appendChild($root);
        $model = $this->getModel();
        foreach($data as $id) {
            $obj = $model->getMessage($id);
            $files = $model->getFiles($id);
            $postroot = $doc->createElement('submission');
            $postroot = $root->appendChild($postroot);
            $title = $doc->createElement('title');
            $title = $postroot->appendChild($title);
            $text = $doc->createTextNode($obj->title);
            $text = $title->appendChild($text);
            $title = $doc->createElement('date');
            $title = $postroot->appendChild($title);
            $text = $doc->createTextNode($obj->date_time);
            $text = $title->appendChild($text);
            foreach($obj->message as $message) {
                $patern = array('~', '`', '!', '@', '"', '#', '№', '$', ';', '%', '^', '&', '?', '*',
                    '(', ')', '-', '+', '=', '/', '|', '.', "'", ',', '\\', '€');
                $replace = ' ';
                $title = str_replace($patern, $replace, $message->title);
                $title = preg_replace('/\s+/', ' ', $title);
                $title = trim($title);
                $title = str_replace(' ', '-', $title);
                if (empty($title)) {
                    $title = 'Label';
                }
                $title = $doc->createElement($title);
                $title = $postroot->appendChild($title);
                if ($message->type == 'total') {
                    $total = $this->getTotal($message->message);
                    $text = $total;
                } else if ($message->type == 'upload' && isset($files->{$message->field_id})) {
                    $pathes = [];
                    foreach ($files->{$message->field_id}->files as $file) {
                        $pathes[] = JUri::root().UPLOADS_STORAGE.'/form-'.$file->form_id.'/'.$file->filename;
                    }
                    $text = implode(', ', $pathes);
                } else if ($message->type == 'poll') {
                    $results = $obj->pollResults->{$message->field_id};
                    $text = $message->message;
                    foreach ($results as $result) {
                        $text .= ' '.$result->title.': '.$result->votes.' '.JText::_('VOTES').', '.$result->percent.'%;';
                    }
                } else {
                    $text = $message->message;
                }
                $text = str_replace('<br>', ' ', $text);
                $text = $doc->createTextNode($text);
                $text = $title->appendChild($text);
            }
        }
        $file = '/tmp/baform-'.time().'.xml';
        $bytes = $doc->save(JPATH_ROOT.$file); 
        if ($bytes) {
            echo new JResponseJson(true, JPATH_ROOT.$file);
        } else {
            echo new JResponseJson(false, '', true);
        }
        jexit();
    }

    public function showSubmission()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', 0, 'int');
        $model = $this->getModel();
        $obj = $model->getMessage($id);
        $obj->time = date('H:i', strtotime($obj->date_time));
        $obj->files = $model->getFiles($id);
        $msg = json_encode($obj);
        echo $msg;exit();
    }
    
    public function exportCSV()
    {
        $str = $this->input->get('data', '', 'string');
        $data = explode(',', $str);
        $list = [];
        $model = $this->getModel();
        $csv = new stdClass();
        $i = 2;
        $array = [JText::_('DATE'), JText::_('ID')];
        foreach ($data as $id) {
            $obj = $model->getMessage($id);
            if (!isset($csv->{$obj->title})) {
                $i = 2;
                $object = new stdClass();
                $object->keys = [];
                $object->data = [];
                $csv->{$obj->title} = $object;
            } else {
                $object = $csv->{$obj->title};
            }
            foreach ($obj->message as $msg) {
                if (!isset($object->keys[$msg->title])) {
                    $object->keys[$msg->title] = $i++;
                }
                $msg->ind = $object->keys[$msg->title];
            }
            $object->data[] = $obj;
        }
        foreach ($csv as $title => $obj) {
            $list[] = [$title];
            $header = array_merge($array, []);
            foreach ($obj->keys as $key => $value) {
                $header[] = $key;
            }
            $list[] = $header;
            foreach ($obj->data as $object) {
                $info = [];
                $files = $model->getFiles($object->id);
                foreach ($header as $i => $value) {
                    $info[] = '';
                }
                $info[0] = $object->date_time;
                $info[1] = $object->id;
                foreach ($object->message as $value) {
                    if ($value->type == 'total') {
                        $info[$value->ind] = $this->getTotal($value->message);
                    } else if ($value->type == 'upload' && isset($files->{$value->field_id})) {
                        $pathes = [];
                        foreach ($files->{$value->field_id}->files as $file) {
                            $pathes[] = JUri::root().UPLOADS_STORAGE.'/form-'.$file->form_id.'/'.$file->filename;
                        }
                        $info[$value->ind] = implode(', ', $pathes);
                    } else if ($value->type == 'poll') {
                        $results = $object->pollResults->{$value->field_id};
                        $info[$value->ind] = strip_tags($value->message);
                        foreach ($results as $result) {
                            $info[$value->ind] .= ' '.$result->title.': '.$result->votes.' '.JText::_('VOTES').', '.$result->percent.'%;';
                        }
                    } else {
                        $info[$value->ind] = strip_tags($value->message);
                    }
                }
                $list[] = $info;
            }
        }
        $file =  '/tmp/baform-'.time().'.csv';
        $fp = fopen(JPATH_ROOT.$file, 'w');
        fwrite ($fp, b"\xEF\xBB\xBF");
        foreach ($list as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        echo new JResponseJson(true, JPATH_ROOT.$file);
        jexit();
    }
    
    public function download()
    {
        $file = $_GET['file'];
        if (file_exists($file)) {
            $ext = JFile::getExt($file);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=baform.'.$ext);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            if (readfile($file)) {
                unlink($file);
            }
            exit;
        }
    }
}