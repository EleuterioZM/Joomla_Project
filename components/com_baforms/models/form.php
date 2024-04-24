<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/ 

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
use Joomla\Registry\Registry;
 
class baformsModelForm extends JModelItem
{
    public $db;
    public $paymentData;
    public $integrationsFields;
    public $pdf;
    public $drivePdf;
    public $files;

    public function getItem($pk = null)
    {
        
    }

    public function getServiceData($service)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote($service));
        $db->setQuery($query);
        $data = $db->loadObject();
        $service = json_decode($data->key);
        if (!$service) {
            $service = $data->key;
        }

        return $service;
    }

    public function checkCoupon($id, $coupon)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_items')
            ->where('id = '.$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        $options = json_decode($item->options);
        $response = '';
        $expired = !empty($options->promo->expires) ? strtotime('now') > strtotime($options->promo->expires) : false;
        $expires = strtotime($options->promo->expires);
        $now = strtotime('now');
        if ($options->promo->enable && $options->promo->code == $coupon && !$expired) {
            $response = json_encode($options->promo);
        }

        return $response;
    }

    public function getRecaptchaData()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('params, enabled, element')
            ->from('#__extensions')
            ->where('element = '.$db->quote('recaptcha').' OR element = '.$db->quote('recaptcha_invisible'))
            ->where('folder = '.$db->quote('captcha'))
            ->where('type = '.$db->quote('plugin'));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $data = new stdClass();
        $data->data = new stdClass();
        foreach ($list as $value) {
            if ($value->enabled == 1) {
                $obj = new Registry();
                $obj->loadString($value->params);
                $object = new stdClass();
                $object->public_key = $obj->get('public_key', '');
                $object->private_key = $obj->get('private_key', '');
                $object->theme = $obj->get('theme2', '');
                $object->size = $obj->get('size', '');
                $object->badge = $obj->get('badge', '');
            } else {
                $object = null;
            }
            $data->{$value->element} = $object;
        }
        $hcaptcha = $this->getServiceData('hcaptcha');
        if (empty($hcaptcha->site_key) || empty($hcaptcha->secret_key)) {
            $hcaptcha = null;
        }
        $data->hcaptcha = $hcaptcha;
        $str = json_encode($data);

        return $str;
    }

    public function replace($str)
    {
        $str = mb_strtolower($str, 'utf-8');
        $search = array('?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '***91;',
            '***93;', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=',
            '|', '"', '\'', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ъ',
            'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я');
        $replace = array('-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
            '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'j', 'i', 'e', '-', 'zh', 'ts',
            'ch', 'sh', 'shch', '', 'yu', 'ya');
        $str = str_replace($search, $replace, $str);
        $str = trim($str);
        $str = preg_replace("/_{2,}/", "-", $str);

        return $str;
    }

    public function saveSignature($data, $id)
    {
        $obj = json_decode($data);
        $array = explode(',', $obj->image);
        $method = $obj->method;
        $str = $method($array[1]);
        $dir = JPATH_ROOT.'/'.SIGNATURE_STORAGE;
        if (SIGNATURE_STORAGE == 'images/baforms/signatures' && !JFolder::exists($dir)) {
            JFolder::create(JPATH_ROOT.'/images/baforms');
            JFolder::create($dir);
        }
        if (!JFolder::exists($dir)) {
            return '';
        }
        $dir .= '/form-'.$id.'/';
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }
        $name = $fileName = 'signature';
        $i = 2;
        $name = $fileName;
        while (JFile::exists($dir.$name.'.jpg')) {
            $name = $fileName.'-'.($i++);
        }
        $fileName = $name.'.jpg';
        JFile::write($dir.$fileName, $str);

        return SIGNATURE_STORAGE.'/form-'.$id.'/'.$fileName; 
    }

    public function uploadAttachmentFile($file, $id, $field_id)
    {
        $obj = new stdClass();
        if (isset($file['error']) && $file['error'] == 0) {
            $ext = strtolower(JFile::getExt($file['name']));
            $dir = JPATH_ROOT.'/'.UPLOADS_STORAGE;
            if (UPLOADS_STORAGE == 'images/baforms/uploads' && !JFolder::exists($dir)) {
                JFolder::create(JPATH_ROOT.'/images/baforms');
                JFolder::create($dir);
            }
            if (!JFolder::exists($dir)) {
                return $obj;
            }
            $dir .= '/form-'.$id.'/';
            if (!JFolder::exists($dir)) {
                JFolder::create($dir);
            }
            $name = str_replace('.'.$ext, '', $file['name']);
            $fileName = $this->replace($name);
            $fileName = JFile::makeSafe($fileName);
            $name = str_replace('-', '', $fileName);
            $name = str_replace('.', '', $name);
            if ($name == '') {
                $fileName = date("Y-m-d-H-i-s").'.'.$ext;
            }
            $i = 2;
            $name = $fileName;
            while (JFile::exists($dir.$name.'.'.$ext)) {
                $name = $fileName.'-'.($i++);
            }
            $fileName = $name.'.'.$ext;
            JFile::upload($file['tmp_name'], $dir.$fileName);
            $obj = $this->addAttachmentFile($file['name'], $fileName, $id, $field_id);
        }

        return $obj;
    }

    public function addAttachmentFile($name, $filename, $id, $field_id)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->submission_id = 0;
        $obj->form_id = $id;
        $obj->field_id = $field_id;
        $obj->name = $name;
        $obj->filename = $filename;
        $obj->date = date("Y-m-d-H-i-s");
        $db->insertObject('#__baforms_submissions_attachments', $obj);
        $obj->id = $db->insertid();

        return $obj;
    }

    public function removeTmpAttachment($id)
    {
        if (!empty($id)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__baforms_submissions_attachments')
                ->where('id = '.$id);
            $db->setQuery($query);
            $obj = $db->loadObject();
            $dir = JPATH_ROOT.'/'.UPLOADS_STORAGE.'/form-'.$obj->form_id.'/';
            $file = $dir.$obj->filename;
            if (JFile::exists($file)) {
                JFile::delete($file);
            }
            $query = $db->getQuery(true)
                ->delete('#__baforms_submissions_attachments')
                ->where('id = '.$id);
            $db->setQuery($query)
                ->execute();
        }
    }

    public function updateShortcodes($data, $fields)
    {
        baformsHelper::$shortCodes->{'[Page Title]'} = $data['page-title'];
        baformsHelper::$shortCodes->{'[Page URL]'} = $data['page-url'];
        baformsHelper::$shortCodes->{'[Page ID]'} = $data['page-id'];
        $allFields = '';
        foreach ($fields as $key => $field) {
            preg_match('/\d+/', $key, $match);
            if ($field->type == 'total') {
                $object = json_decode($field->value);
                include JPATH_ROOT.'/components/com_baforms/views/form/tmpl/submission/total-email-pattern.php';
                baformsHelper::$shortCodes->{'[Field ID='.$match[0].']'} = $out;
                $allFields .= $out;
                $field->value = $object->resultTotal;
            } else {
                $value = str_replace(';', '', $field->value);
                baformsHelper::$shortCodes->{'[Field ID='.$match[0].']'} = $value;
                include JPATH_ROOT.'/components/com_baforms/views/form/tmpl/submission/field-email-pattern.php';
                $allFields .= $out;
            }
            if ($field->type == 'signature') {
                baformsHelper::$shortCodes->{'[Field ID='.$match[0].']'} = '<img src="'.JUri::root().$field->value.'">';
            }
        }
        baformsHelper::$shortCodes->{'[All Fields]'} = $allFields;
    }

    public function preparePaymentData($id, $userEmail, $object, $field)
    {
        $str = json_encode($object);
        $object = json_decode($str);
        $this->paymentData = new stdClass();
        $this->paymentData->id = $id;
        $this->paymentData->userEmail = $userEmail;
        $this->paymentData->total = $object->total;
        $this->paymentData->products = [];
        $this->paymentData->title = $field->options->title;
        $this->paymentData->decimals = $field->options->decimals;
        $this->paymentData->position = $field->options->position;
        $this->paymentData->separator = $field->options->separator;
        $this->paymentData->symbol = $field->options->symbol;
        $this->paymentData->code = $field->options->code;
        $this->paymentData->thousand = $field->options->thousand;
        $tax = 0;
        foreach ($object->products as $products) {
            foreach ($products as $product) {
                $product->total = $product->price * $product->quantity;
                $this->paymentData->products[] = $product;
            }
        }
        if (isset($object->promo) && $field->options->promo->enable && $object->promo == $field->options->promo->code) {
            $discount = $field->options->promo->discount * 1;
            if ($field->options->promo->unit == '%') {
                $discount = $this->paymentData->total * $discount / 100;
            }
            $this->paymentData->discount = $discount;
            $this->paymentData->total = $this->paymentData->total - $discount;
        }
        if ($field->options->tax->enable) {
            $tax = $field->options->tax->value * 1;
            $this->paymentData->total = $this->paymentData->total * 1 + $this->paymentData->total * $tax / 100;
        }
        if (isset($object->shipping)) {
            $this->paymentData->shipping = $object->shipping;
            $product = new stdClass();
            $product->quantity = 1;
            $product->title = $object->shipping->title;
            $product->price = $object->shipping->price;
            $product->total = $product->price;
            $this->paymentData->products[] = $product;
            $this->paymentData->total = $this->paymentData->total + $this->paymentData->shipping->price;
        }
    }

    public function executePHP($code)
    {
        try {
            eval($code);
        } catch (Throwable $t) {
            
        }
    }

    protected function updatePoll($field, $value)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $values = [];
        $allow = true;
        if (!$field->options->again) {
            $allow = baformsHelper::checkUserPoll($field);
        } else {
            $ip = '';
        }
        foreach ($field->options->items as $item) {
            $exists = in_array($item->key, $value);
            if ($exists && $allow) {
                $obj = new stdClass();
                $obj->form_id = $field->form_id;
                $obj->field_id = $field->id;
                $obj->value = $item->key;
                $obj->ip = $ip;
                $this->db->insertObject('#__baforms_poll_results', $obj);
            }
            if ($exists) {
                $values[] = $item->title;
            }
        }

        return $values;
    }

    public function checkRecaptchaResponse($type, $post)
    {
        $str = $this->getRecaptchaData();
        $data = json_decode($str);
        if (empty($type) || !($recaptcha = $data->{$type})) {
            return true;
        }
        $url = 'https://'.($type == 'hcaptcha' ? 'hcaptcha.com' : 'www.google.com/recaptcha/api').'/siteverify';
        $array = [
            'secret' => ($type == 'hcaptcha' ? $recaptcha->secret_key : $recaptcha->private_key),
            'response' => ($type == 'hcaptcha' ? $post['h-captcha-response'] : $post['g-recaptcha-response'])
        ];
        $headers = ['application/x-www-form-urlencoded'];
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = 'secret='.$array['secret'].'&response='.$array['response'];
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $response = json_decode($body);

        return $response->success;
    }

    public function sendMessage($data, $btn, $id)
    {
        baformsHelper::prepareHelper();
        $this->db = JFactory::getDbo();
        $this->integrationsFields = new stdClass();
        $this->files = [];
        JFactory::getLanguage()->load('com_baforms', JPATH_ADMINISTRATOR);
        $submit = $this->getFormField($btn, $id);
        if (!$this->checkRecaptchaResponse($submit->options->recaptcha, $data)) {
            return;
        }
        baformsHelper::getFormShortCodes($id);
        $userEmail = '';
        $fields = new stdClass();
        $attachmentFiles = [];
        $files = [];
        $messageArray = [];
        foreach ($data as $key => $value) {
            if (is_numeric($key) && $value != '') {
                $field = $this->getFormField($key * 1, $id);
                $fields->{$field->key} = new stdClass();
                if ($field->type == 'input' && $field->options->type == 'email') {
                    $userEmail = $value;
                } else if ($field->type == 'poll') {
                    $value = self::updatePoll($field, $value);
                    $fields->{$field->key}->results = baformsHelper::getPollResults($field->id, $field->options->items);
                } else if ($field->type == 'signature') {
                    $value = $this->saveSignature($value, $id);
                }
                $this->integrationsFields->{str_replace('baform-', '', $field->key)} = $value;
                $fields->{$field->key}->id = $field->id;
                $fields->{$field->key}->value = '';
                $fields->{$field->key}->title = $field->options->title;
                $fields->{$field->key}->type = $field->type;
                if (empty($fields->{$field->key}->title) && isset($field->options->placeholder)) {
                    $fields->{$field->key}->title = $field->options->placeholder;
                }
                $message = '';
                $rating = array('VERY_UNSATISFIED', 'UNSATISFIED', 'NEUTRAL', 'SATISFIED', 'VERY_SATISFIED');
                switch ($field->type) {
                    case 'checkbox':
                    case 'selectMultiple':
                    case 'poll':
                        foreach ($value as $text) {
                            $fields->{$field->key}->value .= $text.';<br>';
                        }
                        break;
                    case 'upload':
                        $filesData = json_decode($value);
                        $integration = [];
                        foreach ($filesData as $file) {
                            if (!is_numeric($file->id)) {
                                continue;
                            }
                            $attachmentFiles[] = $file->id;
                            $filePath = UPLOADS_STORAGE.'/form-'.$id.'/'.$file->filename;
                            $obj = new stdClass();
                            $obj->url = JUri::root().$filePath;
                            $obj->path = JPATH_ROOT.'/'.$filePath;
                            $this->files[] = $obj;
                            $files[] = JPATH_ROOT.'/'.$filePath;
                            $integration[] = $obj->url;
                            $fields->{$field->key}->value .= '<a href="'.$obj->url.'">'.$file->name.'</a>;<br>';
                        }
                        $this->integrationsFields->{str_replace('baform-', '', $field->key)} = implode('; ', $integration);
                        break;
                    case 'calculation':
                        $thousand = $field->options->thousand;
                        $separator = $field->options->separator;
                        $decimals = $field->options->decimals;
                        $price = baformsHelper::renderPrice($value, $thousand, $separator, $decimals);
                        if (empty($field->options->position)) {
                            $price = $field->options->symbol.' '.$price;
                        } else {
                            $price .= ' '.$field->options->symbol;
                        }
                        $fields->{$field->key}->value = $price;
                        break;
                    case 'slider':
                        $fields->{$field->key}->value = str_replace(' ', ' - ', $value);
                        break;
                    case 'phone':
                        $fields->{$field->key}->value = '<a href="tel:'.$value.'">'.$value.'</a>';
                        break;
                    case 'rating':
                        $fields->{$field->key}->value = JText::_($rating[$value * 1 - 1]);
                        break;
                    default:
                        $fields->{$field->key}->value = $value;
                        break;
                }
                if ($field->type == 'total') {
                    $fields->{$field->key}->options = $field->options;
                    $object = json_decode($fields->{$field->key}->value);
                    $object->options = $field->options;
                    $fields->{$field->key}->object = $object;
                    $message = json_encode($object);
                    $this->preparePaymentData($id, $userEmail, $object, $field);
                    if (isset($data['payment_id'])) {
                        $object = new stdClass();
                        $object->title = 'Payment Id';
                        $object->message = $data['payment_id'];
                        $object->type = 'input';
                        $messageArray[] = $object;
                    } else if (isset($data['transId'])) {
                        $object = new stdClass();
                        $object->title = 'Transaction Id';
                        $object->message = $data['transId'];
                        $object->type = 'input';
                        $messageArray[] = $object;
                    } else if (isset($data['invoiceId'])) {
                        $object = new stdClass();
                        $object->title = 'Invoice Id';
                        $object->message = $data['invoiceId'];
                        $object->type = 'input';
                        $messageArray[] = $object;
                    }
                    $thousand = $field->options->thousand;
                    $separator = $field->options->separator;
                    $decimals = $field->options->decimals;
                    $price = baformsHelper::renderPrice((string)$this->paymentData->total, $thousand, $separator, $decimals);
                    if (empty($field->options->position)) {
                        $price = $field->options->symbol.' '.$price;
                    } else {
                        $price .= ' '.$field->options->symbol;
                    }
                    $this->integrationsFields->{str_replace('baform-', '', $field->key)} = $price;
                } else if ($field->type != 'upload') {
                    $message = $fields->{$field->key}->value;
                    $this->integrationsFields->{str_replace('baform-', '', $field->key)} = strip_tags($fields->{$field->key}->value);
                }
                $object = new stdClass();
                $object->title = $fields->{$field->key}->title;
                $object->message = $message;
                $object->type = $field->type;
                $object->field_id = $field->id;
                $messageArray[] = $object;
            }
        }
        $this->updateShortcodes($data, $fields);
        if ($submit->options->database) {
            $submission = new stdClass();
            $submission->title = baformsHelper::$shortCodes->{'[Form Title]'};
            $submission->message = json_encode($messageArray);
            $config = JFactory::getConfig();
            date_default_timezone_set($config->get('offset'));
            $submission->date_time = date("Y-m-d H:i:s");
            $this->db->insertObject('#__baforms_submissions', $submission);
            $submissionId = $this->db->insertid();
            baformsHelper::$shortCodes->{'[Submission ID]'} = $submissionId;
            foreach ($fields as $field) {
                if ($field->type == 'poll') {
                    $object = new stdClass();
                    $object->submission_id = $submissionId;
                    $object->data = json_encode($field->results);
                    $object->field_id = $field->id;
                    $this->db->insertObject('#__baforms_poll_statistic', $object);
                }
            }
            if (!empty($attachmentFiles)) {
                $attachmentStr = implode(', ', $attachmentFiles);
                $query = $this->db->getQuery(true)
                    ->update('#__baforms_submissions_attachments')
                    ->set('submission_id = '.$submissionId)
                    ->where('id IN ('.$attachmentStr.')');
                $this->db->setQuery($query)
                    ->execute();
            }
        }
        if (!empty($submit->options->php)) {
            $code = baformsHelper::renderDefaultValue($submit->options->php, true);
            $this->executePHP($code);
        }
        if (baformsHelper::$about->tag == 'pro' && isset(baformsHelper::$state->data)) {
            $this->checkIntegration($id, $fields, $files);
        }
        if ($submit->options->notifications->enable) {
            try {
                $mailer = JFactory::getMailer();
                $config = JFactory::getConfig();
                $recipients = [];
                $sender = [$config->get('mailfrom'), $config->get('fromname')];
                $notifications = $submit->options->notifications;
                if ($notifications->email == 'customer-email' && !empty($userEmail)) {
                    $sender = [$userEmail, ''];
                } else if ($notifications->email == 'custom' && !empty($notifications->{'custom-email'})) {
                    $sender = [$notifications->{'custom-email'}];
                    $sender[] = isset($notifications->{'custom-name'}) ? $notifications->{'custom-name'} : '';
                }
                if (!isset($notifications->cc)) {
                    $notifications->cc = $notifications->bcc = new stdClass();
                }
                foreach ($notifications->admin as $email => $value) {
                    $recipients[] = $email;
                }
                if (empty($recipients)) {
                    $recipients[] = $config->get('mailfrom');
                }
                $reply = empty($userEmail) ? null : $userEmail;
                $mailFiles = [];
                if ($submit->options->notifications->attach) {
                    $mailFiles = array_merge([], $files);
                }
                if (isset($notifications->attach_pdf) && $notifications->attach_pdf && !empty($this->pdf)) {
                    $mailFiles[] = $this->pdf;
                }
                $subject = baformsHelper::renderDefaultValue($notifications->subject);
                $body = baformsHelper::renderDefaultValue($notifications->body);
                $cc = $bcc = null;
                $array = [];
                foreach ($notifications->cc as $email => $value) {
                   $array[] = $email;
                }
                if (!empty($array)) {
                    $cc = $array;
                }
                $array = [];
                foreach ($notifications->bcc as $email => $value) {
                   $array[] = $email;
                }
                if (!empty($array)) {
                    $bcc = $array;
                }
                $mailer->sendMail($sender[0], $sender[1], $recipients, $subject, $body, true, $cc, $bcc, $mailFiles, $reply);
            } catch (Exception $e) {
                
            }
        }
        if ($submit->options->reply->enable && !empty($userEmail)) {
            try {
                $mailer = JFactory::getMailer();
                $config = JFactory::getConfig();
                $recipients = [$userEmail];
                $sender = [$config->get('mailfrom'), $config->get('fromname')];
                $reply = $submit->options->reply;
                if (!empty($reply->email) && !empty($reply->{'custom-email'})) {
                    $sender = [$reply->{'custom-email'}];
                    $sender[] = isset($reply->{'custom-name'}) ? $reply->{'custom-name'} : '';
                }
                $mailFiles = [];
                if ($submit->options->reply->attach) {
                    $mailFiles = array_merge([], $files);
                }
                if (isset($reply->attach_pdf) && $reply->attach_pdf && !empty($this->pdf)) {
                    $mailFiles[] = $this->pdf;
                }
                $subject = baformsHelper::renderDefaultValue($reply->subject);
                $body = baformsHelper::renderDefaultValue($reply->body);
                $mailer->sendMail($sender[0], $sender[1], $recipients, $subject, $body, true, null, null, $mailFiles, $sender[0]);
            } catch (Exception $e) {
                
            }
        }
        if ($submit->options->onclick == 'payment' && (baformsHelper::$about->tag == 'pro' && isset(baformsHelper::$state->data))) {
            $this->executePayment($submit);
        } else if ($submit->options->onclick == 'redirect') {
            $link = baformsHelper::renderDefaultValue($submit->options->link);
            echo strip_tags($link);
        } else if ($submit->options->onclick == 'message' && isset($submit->options->{'message-type'})
            && $submit->options->{'message-type'} == 'advanced') {
            $str = baformsHelper::renderDefaultValue($submit->options->{'advanced-message'});
            echo $str;
        }
    }

    public function checkIntegration($id, $fields, $files)
    {
        $query = $this->db->getQuery(true)
            ->select('acym_fields_map, telegram_token, mailchimp_fields_map, mailchimp_list_id,
                google_sheets, activecampaign_fields, pdf_submissions, campaign_monitor_fields,
                getresponse_fields, zoho_crm_fields, google_drive')
            ->from('#__baforms_forms')
            ->where('id = '.$id);
        $this->db->setQuery($query);
        $object = $this->db->loadObject();
        $mailchimp = $this->getServiceData('mailchimp');
        if (!empty($mailchimp)) {
            $mailchimp_fields = json_decode($object->mailchimp_fields_map);
            $this->addMailchimpSubscribe($mailchimp, $object->mailchimp_list_id, $mailchimp_fields);
        }
        $campaign_monitor = $this->getServiceData('campaign_monitor');
        $this->addCampaignMonitorSubscribe($campaign_monitor, $object->campaign_monitor_fields);
        $getresponse = $this->getServiceData('getresponse');
        $this->addGetResponseSubscribe($getresponse, $object->getresponse_fields);
        $activecampaign = $this->getServiceData('activecampaign');
        $this->addActivecampaignContact($activecampaign, $object->activecampaign_fields);
        if (!empty($object->acym_fields_map)) {
            $acymailing = json_decode($object->acym_fields_map);
            $this->addAcymailingSubscriber($acymailing);
        }
        if (!empty($object->telegram_token)) {
            $this->telegramAction($object->telegram_token, $fields, $files);
        }
        $google_drive = $this->getServiceData('google_drive');
        if (!empty($object->google_drive)) {
            $obj = json_decode($object->google_drive);
            foreach ($obj as $ind => $value) {
                $google_drive->{$ind} = $value;
            }
        }
        if (empty($object->pdf_submissions)) {
            $object->pdf_submissions = '{"enable":false,"title":false,"empty":false,"size":"A4","orientation":"Portrait"}';
        }
        $this->createPdf($fields, $object->pdf_submissions, $google_drive);
        $this->googleDriveIntegration($google_drive, $files);
        if (!empty($object->google_sheets)) {
            $this->addGoogleSheets($object->google_sheets);
        }
        $zoho_crm = $this->getServiceData('zoho_auth');
        if (!empty($zoho_crm->client_id) && !empty($zoho_crm->client_secret)) {
            $zoho_crm_fields = json_decode($object->zoho_crm_fields);
            $this->addZohoCRMContact($zoho_crm, $zoho_crm_fields);
        }
    }

    public function googleDriveIntegration($obj, $files)
    {
        $path = JPATH_ROOT.'/components/com_baforms/libraries/google-v4';
        $path2 = JPATH_ROOT.'/components/com_baforms/libraries/google-drive';
        if (empty($obj->client_id) || empty($obj->client_secret) || empty($obj->accessToken)
            || empty($obj->folder) || !($obj->pdf || $obj->files) || !(JFolder::exists($path) || JFolder::exists($path2))) {
            return;
        }
        include JPATH_ROOT.'/components/com_baforms/libraries/wrappers/drive.php';
        $drive = new drive($obj->client_id, $obj->client_secret);
        $data = [];
        if ($obj->files) {
            foreach ($files as $file) {
                $object = new stdClass();
                $object->name = basename($file);
                $object->path = $file;
                $data[] = $object;
            }
        }
        if ($obj->pdf) {
            $pdf = !empty($this->pdf) ? $this->pdf : $this->drivePdf;
            $object = new stdClass();
            $object->name = basename($pdf);
            $object->path = $pdf;
            $data[] = $object;
        }
        $drive->uploadFiles($obj->accessToken, $data, $obj->folder);
    }

    public function createPdf($fields, $settings, $google_drive)
    {
        $obj = json_decode($settings);
        $drive = !empty($google_drive->accessToken) && !empty($google_drive->folder) && $google_drive->pdf;
        $path = JPATH_ROOT.'/components/com_baforms/libraries/pdf-submissions/pdf.php';
        if (!($obj->enable || $drive) || !JFile::exists($path)) {
            return;
        }
        include $path;
        $pdf = new pdf($fields, $obj);
        $fileName = $this->replace(baformsHelper::$shortCodes->{'[Form Title]'});
        $file = $pdf->create($fileName);
        if ($obj->enable) {
            $this->pdf = $file;
        } else if ($drive) {
            $this->drivePdf = $file;
        }
    }

    public function addZohoCRMContact($zoho_crm, $zoho_crm_fields)
    {
        include JPATH_ROOT.'/components/com_baforms/libraries/wrappers/zoho.php';
        $zoho = new zoho_crm($zoho_crm->client_id, $zoho_crm->client_secret);
        $zoho->setAuth($zoho_crm);
        $fields = new stdClass();
        $empty = true;
        foreach ($zoho_crm_fields as $key => $value) {
            if (empty($value) || !isset($this->integrationsFields->{$value})) {
                continue;
            }
            $empty = false;
            $fields->{$key} = strip_tags($this->integrationsFields->{$value});
        }
        if (!$empty) {
            $zoho->insertContact($fields);
        }
    }

    public function addGoogleSheets($google_sheets)
    {
        $obj = $this->getServiceData('google_sheets');
        $dir = JPATH_ROOT.'/components/com_baforms/libraries/google-v4';
        if (!empty($obj->client_id) && !empty($obj->client_secret) && !empty($obj->code) && JFolder::exists($dir)) {
            $data = json_decode($google_sheets);
            if (!empty($data->spreadsheet) && $data->worksheet != '') {
                $row = [];
                foreach ($data->columns as $key => $value) {
                    if (empty($value) || !isset($this->integrationsFields->{$value})) {
                        continue;
                    }
                    $str = strip_tags($this->integrationsFields->{$value});
                    $str = str_replace('+', '', $str);
                    $row[$key] = $str;
                }
                if (!empty($row)) {
                    require_once JPATH_ROOT.'/components/com_baforms/libraries/wrappers/sheets.php';
                    $sheets = new sheets($obj->client_id, $obj->client_secret);
                    $sheets->insert($obj->accessToken, $row, $data->spreadsheet, $data->worksheet);
                }
            }
        }
    }

    public function addActivecampaignContact($obj, $activecampaign_fields)
    {
        $fields = json_decode($activecampaign_fields);
        if (!empty($obj->api_key) && !empty($obj->account) && !empty($fields->list) && !empty($fields->email)
            && isset($this->integrationsFields->{$fields->email})) {
            require_once JPATH_ROOT.'/components/com_baforms/libraries/activecampaign/activecampaign.php';
            $activecampaign = new activecampaign($obj->account, $obj->api_key);
            $contact = new stdClass();
            foreach ($fields as $key => $field) {
                if ($key == 'list') {
                    continue;
                } else if (!empty($field) && isset($this->integrationsFields->{$field})) {
                    $contact->{$key} = $this->integrationsFields->{$field};
                }
            }
            $activecampaign->addContact($contact, $fields->list);
        }
    }

    public function addGetResponseSubscribe($obj, $getresponse_fields)
    {
        $fields = json_decode($getresponse_fields);
        if (!empty($obj->api_key) && !empty($fields->email) && isset($this->integrationsFields->{$fields->email})
            && !empty($fields->name) && isset($this->integrationsFields->{$fields->name})) {
            require_once JPATH_ROOT.'/components/com_baforms/libraries/getresponse/getresponse.php';
            $getresponse = new getresponse($obj->api_key, $fields->list_id);
            $custom = [];
            if ($obj->custom_fields) {
                foreach ($fields as $key => $field) {
                    if ($key == 'name' || $key == 'email' || $key == 'list_id' || !isset($this->integrationsFields->{$field})) {
                        continue;
                    }
                    $custom[] = array(
                        'customFieldId' => $key,
                        'value' => array($this->integrationsFields->{$field})
                    );
                }
            }
            $getresponse->addSubscriber($this->integrationsFields->{$fields->name}, $this->integrationsFields->{$fields->email}, $custom);
        }
    }

    public function addCampaignMonitorSubscribe($obj, $campaign_monitor_fields)
    {
        $fields = json_decode($campaign_monitor_fields);
        $dir = JPATH_ROOT.'/components/com_baforms/libraries/campaign-monitor/campaign.php';
        if (JFile::exists($dir) && !empty($obj->api_key) && !empty($obj->client_id)
            && !empty($fields->EmailAddress) && isset($this->integrationsFields->{$fields->EmailAddress})
            && !empty($fields->Name) && isset($this->integrationsFields->{$fields->Name})) {
            require_once $dir;
            $campaign = new campaign($obj->api_key, $obj->client_id, $fields->list_id);
            $custom = [];
            foreach ($fields as $key => $field) {
                if ($key == 'Name' || $key == 'EmailAddress' || $key == 'list_id' || !isset($this->integrationsFields->{$field})) {
                    continue;
                }
                $custom[] = array(
                    'Key' => $key,
                    'Value' => $this->integrationsFields->{$field}
                );
            }
            $campaign->addSubscriber($this->integrationsFields->{$fields->Name}, $this->integrationsFields->{$fields->EmailAddress}, $custom);
        }
    }
    public function addMailchimpSubscribe($api_key, $listid, $fields)
    {
        if (!empty($listid) && !empty($fields->EMAIL) && isset($this->integrationsFields->{$fields->EMAIL})) {
            $email = $this->integrationsFields->{$fields->EMAIL};
            $memberId = md5(strtolower($email));
            $dataCenter = substr($api_key,strpos($api_key,'-') + 1);
            $url = 'https://'.$dataCenter.'.api.mailchimp.com/3.0/lists/'.$listid.'/members/'.$memberId;
            $merge_fields = [];
            foreach ($fields as $key => $value) {
                if ($key != 'EMAIL' && isset($this->integrationsFields->{$value})) {
                    $merge_fields[$key] = $this->integrationsFields->{$value};
                }
            }
            $array = array('email_address' => $email, 'status' => 'subscribed');
            if (!empty($merge_fields)) {
                $array['merge_fields'] = $merge_fields;
            }
            $json = json_encode($array);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_USERPWD, 'user:'.$api_key);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            curl_exec($curl);
            curl_close($curl);
        }
    }

    public function getContentsCurl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }

    public function checkTelegramExt($file)
    {
        switch (JFile::getExt($file)) {
            case 'jpg':
            case 'png':
            case 'gif':
            case 'jpeg':
                return array('sendPhoto', 'photo');
            case 'mp3':
                return array('sendAudio', 'audio');
            case 'mp4':
                return array('sendVideo', 'video');
            default:
                return array('sendDocument', 'document');
        }
    }

    public function telegramAction($token, $fields, $files)
    {
        $message = '';
        if (function_exists('curl_init')) {
            $url = 'https://api.telegram.org/bot'.$token;
            $data = $this->getContentsCurl($url.'/getUpdates');
            $data = json_decode($data);
            if (!empty($data->result)) {
                $chats = [];
                foreach ($fields as $field) {
                    if ($field->type != 'upload') {
                        $text = str_replace('<br>', '', $field->value);
                        $text = str_replace('<br/>', '', $text);
                        if (!empty($field->title)) {
                            $message .= '<b>'.$field->title. '</b> : ';
                        }
                        $message .= $text.'                                                                                            ';
                        if (empty($field->title)) {
                            $message .= '                 ';
                        }
                    }
                }
                foreach ($data->result as $key => $value) {
                    $result = $value;
                    $chat_id = $result->message->chat->id;
                    if (!in_array($chat_id, $chats)) {
                        $chats[] = $chat_id;
                        $uri = $url.'/sendMessage?chat_id='.$chat_id.'&parse_mode=HTML&text='.urlencode($message);
                        $this->getContentsCurl($uri);
                        foreach ($this->files as $file) {
                            $method = $this->checkTelegramExt($file->path);
                            $uri = $url.'/'.$method[0].'?chat_id='.$chat_id.'&'.$method[1].'='.$file->url;
                            $this->getContentsCurl($uri);
                        }
                    }
                }
            }
        }
    }

    public function addAcymailingSubscriber($acymailing)
    {
        $checkAcymailing = $this->checkAcymailing();
        if (!empty($checkAcymailing)) {
            if (!empty($acymailing->name) && !empty($acymailing->email)) {
                $app = JFactory::getApplication();
                $config = JFactory::getConfig();
                date_default_timezone_set($config->get('offset'));
                $created = date('Y-m-d H:i:s');
                $obj = new stdClass();
                if (isset($this->integrationsFields->{$acymailing->name})
                    && isset($this->integrationsFields->{$acymailing->email})) {
                    $obj->name = $this->integrationsFields->{$acymailing->name};
                    $obj->email = $this->integrationsFields->{$acymailing->email};
                    $checkAcymailingEmail = $this->checkAcymailingEmail($obj->email);
                    if (!empty($checkAcymailingEmail)) {
                        return;
                    }
                    try {
                        $obj->creation_date = $created;
                        $obj->confirmed = $obj->active = 1;
                        $this->db->insertObject('#__acym_user', $obj);
                        $id = $this->db->insertid();
                        if (!empty($acymailing->list)) {
                            $obj = new stdClass();
                            $obj->list_id = $acymailing->list;
                            $obj->user_id = $id;
                            $obj->subscription_date = $created;
                            $obj->status = 1;
                            $this->db->insertObject('#__acym_user_has_list', $obj);
                        }
                        foreach ($acymailing as $key => $value) {
                            if ($key == 'email' || $key == 'name' || !isset($this->integrationsFields->{$value})) {
                                continue;
                            }
                            $obj = new stdClass();
                            $obj->field_id = $key;
                            $obj->user_id = $id;
                            $obj->value = $this->integrationsFields->{$value};
                            $this->db->insertObject('#__acym_user_has_field', $obj);
                        }
                    } catch (Throwable $t) {
                        
                    }
                }
            }
        }
    }

    public function checkAcymailingEmail($email)
    {
        $query = $this->db->getQuery(true)
            ->select('id')
            ->from('#__acym_user')
            ->where('email = '.$this->db->quote($this->db->escape($email, true)));
        $this->db->setQuery($query);
        $id = $this->db->loadResult();

        return $id;
    }

    public function checkAcymailing()
    {
        $query = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('element = '.$this->db->quote('com_acym'));
        $this->db->setQuery($query);
        $id = $this->db->loadResult();

        return $id;
    }

    public function executeCustomPayment($className)
    {
        $file = JPATH_ROOT.'/components/com_baforms/libraries/custom-payment-gateway/custom-payment-gateway.xml';
        if (function_exists('simplexml_load_string') && JFile::exists($file)) {
            $str = baformsHelper::readFile($file);
            $xml = simplexml_load_string($str);
            foreach ($xml->payment as $payment) {
                $obj = new stdClass();
                foreach ($payment as $key => $value) {
                    $obj->{(string)$key} = trim((string)$value);
                }
                if ($obj->class == $className) {
                    include JPATH_ROOT.'/'.$obj->path;
                    $payment = new $obj->class;
                    $payment->executePayment($this->paymentData);
                    break;
                }
            }
        }
    }

    public function executePayment($submit)
    {
        if (strpos($submit->options->payment, 'custom-payment-') !== false) {
            $key = str_replace('custom-payment-', '', $submit->options->payment);
            $this->executeCustomPayment($key);            
        } else {
            switch ($submit->options->payment) {
                case 'paypal':
                    $this->paypal();
                    break;
                case 'payfast':
                    $this->payfast();
                    break;
                case 'twocheckout':
                    $this->twocheckout();
                    break;
                case 'liqpay':
                    $this->liqpay();
                    break;
                case 'payupl':
                    $this->payupl();
                    break;
                case 'payu_latam':
                    $this->payulatam();
                    break;
                case 'yandex_kassa':
                    $this->yandexkassa();
                    break;
                case 'redsys':
                    $this->redsys();
                    break;
                case 'robokassa':
                    $this->robokassa();
                    break;
                case 'mollie':
                    $this->mollie();
                    break;
            }
        }
    }

    public function getFormField($id, $form_id)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from('#__baforms_items')
            ->where('id = '.$id)
            ->where('form_id = '.$form_id);
        $this->db->setQuery($query);
        $item = $this->db->loadObject();
        $item->options = json_decode($item->options);

        return $item;
    }

    public function mollie()
    {
        $mollie = $this->getServiceData('mollie');
        $price = baformsHelper::renderPrice((string)$this->paymentData->total, '', '.', '2');
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $name = implode(', ', $title);
        $array = array(
            "amount" => array("currency" => $this->paymentData->code, "value" => $price),
            "description" => $name,
            "redirectUrl" => $mollie->return_url,
            "metadata" => array("order_id" => time())
        );
        $headers = array('Authorization: Bearer '.$mollie->api_key, 'Content-Type: application/json');
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = json_encode($array);
        $options[CURLOPT_URL] = 'https://api.mollie.com/v2/payments';
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $response = json_decode($body);
        if (isset($response->_links) && isset($response->_links->checkout)) {
            header('Location: '.$response->_links->checkout->href, true, 303);
        } else {
            print_r($response->detail);exit;
        }
    }

    public function redsys()
    {
        $data = $this->getServiceData('redsys');
        include JPATH_ROOT.'/components/com_baforms/libraries/redsys/redsys.php';
        $redsys = new redsys($data);
        $redsys->executePayment($this->paymentData);
        exit;
    }

    public function robokassa()
    {
        $robokassa = $this->getServiceData('robokassa');
        $title = array(JText::_('SUBMISSION_ID').': '.baformsHelper::$shortCodes->{'[Submission ID]'});
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        $inv_id = baformsHelper::$shortCodes->{'[Submission ID]'};
        if (empty($inv_id)) {
            $inv_id = time();
        }
        $allowedCurrency = array('USD', 'EUR', 'KZT');
        $OutSumCurrency = in_array($this->paymentData->code, $allowedCurrency);
        $receiptData = new stdClass();
        $receiptData->items = [];
        $tax = !empty($robokassa->tax) ? $robokassa->tax : 'none';
        foreach ($this->paymentData->products as $product) {
            $item = new stdClass();
            $item->name = str_replace('"', '', $product->title);
            $item->quantity = $product->quantity;
            $item->sum = $product->total;
            $item->tax = $tax;
            $receiptData->items[] = $item;
        }
        $receipt = urlencode(json_encode($receiptData));
        $cache = $robokassa->shop_id.":".$this->paymentData->total.":".$inv_id.":".$receipt.":";
        if ($OutSumCurrency) {
            $cache .= $this->paymentData->code.":";
        }
        $cache .= $robokassa->password;
        $signature = md5($cache);
?>
        <form action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST" id="payment-form">
            <input type=hidden name=MerchantLogin value="<?php echo $robokassa->shop_id; ?>">
            <input type=hidden name=OutSum value="<?php echo $this->paymentData->total; ?>">
            <input type=hidden name=InvId value="<?php echo $inv_id; ?>">
            <input type=hidden name=Description value="<?php echo $name; ?>">
            <input type=hidden name=SignatureValue value="<?php echo $signature; ?>">
            <input type=hidden name=Receipt value="<?php echo $receipt; ?>">
<?php
        if ($OutSumCurrency) {
?>
            <input type=hidden name=OutSumCurrency value="<?php echo $this->paymentData->code; ?>">
<?php
        }
        if (isset($robokassa->environment) && $robokassa->environment == 'sandbox') {
?>
            <input type=hidden name=IsTest value="1">
<?php
        }
?>
        </form>
        <script type="text/javascript">
            document.getElementById('payment-form').submit();
        </script>
<?php
    }

    public function payuLatam()
    {
        $payu = $this->getServiceData('payu_latam');
        if ($payu->environment == 'sandbox') {
            $url = 'https://sandbox.gateway.payulatam.com/ppp-web-gateway';
        } else {
            $url = 'https://gateway.payulatam.com/ppp-web-gateway/';
        }
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $description = implode(', ', $title);
        $ref = time();
        $sig = $payu->api_key. "~".$payu->merchant_id."~".$ref."~".$this->paymentData->total."~".$this->paymentData->code;
        $signature = md5($sig);
?>
        <form id="payment-form" action="<?php echo $url; ?>" method="post">
            <input name="merchantId" type="hidden" value="<?php echo $payu->merchant_id; ?>">
            <input name="accountId" type="hidden" value="<?php echo $payu->account_id; ?>">
            <input name="description" type="hidden" value="<?php echo $description; ?>">
            <input name="referenceCode" type="hidden" value="<?php echo $ref; ?>">
            <input name="amount" type="hidden" value="<?php echo $this->paymentData->total; ?>">
            <input name="tax" type="hidden" value="0">
            <input name="taxReturnBase" type="hidden" value="0">
            <input name="currency" type="hidden" value="<?php echo $this->paymentData->code; ?>">
            <input name="signature" type="hidden" value="<?php echo $signature ?>">
<?php
        if (!empty($this->paymentData->userEmail)) {
?>
            <input name="buyerEmail" type="hidden" value="<?php echo $this->paymentData->userEmail; ?>">
<?php
        }
?>
        </form>
        <script type="text/javascript">
            document.getElementById('payment-form').submit();
        </script>
        <?php 
        exit;
    }

    public function yandexKassa()
    {
        $yandex = $this->getServiceData('yandex_kassa');
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $name = implode(', ', $title);
        $orderId = uniqid('', true);
        $price = round($this->paymentData->total, 2);
        $array = array(
            'amount' => array(
                'value' => $price,
                'currency' => $this->paymentData->code,
            ),
            'confirmation' => array(
                'type' => 'redirect',
                'return_url' => $yandex->return_url,
            ),
            'capture' => true,
            'description' => $name,
        );
        $headers = array('Idempotence-Key: '.$orderId, 'Content-Type: application/json');
        $curl = curl_init('https://api.yookassa.ru/v3/payments');
        curl_setopt($curl, CURLOPT_USERPWD, $yandex->shop_id.':'.$yandex->secret_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($curl);
        $response = json_decode($body);
        if (isset($response->confirmation)) {
            header('Location: '.$response->confirmation->confirmation_url);
        } else {
            echo $response->description;
        }
        exit;
    }

    public function payupl()
    {
        $payupl = $this->getServiceData('payupl');
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        if ($payupl->environment == 'sandbox') {
            $url = 'https://secure.snd.payu.com/api/v2_1/orders';
        } else {
            $url = 'https://secure.payu.com/api/v2_1/orders';
        }
        $price = baformsHelper::renderPrice((string)$this->paymentData->total, '', '.', '2');
        $fields = ["customerIp" => $_SERVER['REMOTE_ADDR'], "merchantPosId" => $payupl->pos_id,
            "description" => $name, "totalAmount" => $price * 100, "currencyCode" => $this->paymentData->code,
            "notifyUrl" => $payupl->return_url, "continueUrl" => $payupl->return_url,
            "extOrderId" => baformsHelper::$shortCodes->{'[Submission ID]'}
        ];
        $fields['products[0].name'] = $name;
        $fields['products[0].unitPrice'] = $price * 100;
        $fields['products[0].quantity'] = 1;
        ksort($fields);
        $str = '';
        foreach ($fields as $value) {
            $str .= $value;
        }
        $str .= $payupl->second_key;
        $hash = hash('md5', $str);
        $signature = 'sender='.$payupl->pos_id.';algorithm=MD5;signature='.$hash;
        ?>
        <form id="payment-form" action="<?php echo $url; ?>" method="post">
<?php
        foreach ($fields as $key => $value) {
?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
<?php
        }
?>
            <input type="hidden" name="OpenPayu-Signature" value="<?php echo $signature; ?>">
        </form>
        <script type="text/javascript">
            document.getElementById('payment-form').submit();
        </script>
<?php
        exit;
    }

    public function payfast()
    {
        $payfast = $this->getServiceData('payfast');
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $name = implode(', ', $title);
        $name = str_replace('"', '', $name);
        if ($payfast->environment == 'sandbox') {
            $url = 'https://sandbox.payfast.co.za/eng/process';
        } else {
            $url = 'https://www.payfast.co.za/eng/process';
        }
        $price = baformsHelper::renderPrice((string)$this->paymentData->total, '', '.', '2');
        $m_payment_id = time();
        ?>
        <form id="payment-form" action="<?php echo $url; ?>" method="post">
            <input type="hidden" name="merchant_id" value="<?php echo $payfast->merchant_id; ?>">
            <input type="hidden" name="merchant_key" value="<?php echo $payfast->merchant_key; ?>">
            <input type="hidden" name="return_url" value="<?php echo $payfast->return_url; ?>">
            <input type="hidden" name="m_payment_id" value="<?php echo $m_payment_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $price; ?>">
            <input type="hidden" name="item_name" value="<?php echo $name; ?>">
        </form>
        <script type="text/javascript">
            document.getElementById('payment-form').submit();
        </script>
<?php
        exit;
    }

    public function liqpay()
    {
        $liqpay = $this->getServiceData('liqpay');
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        include JPATH_ROOT.'/components/com_baforms/libraries/liqpay/LiqPay.php';
        $LiqPay = new LiqPay($liqpay->public_key, $liqpay->private_key);
        $html = $LiqPay->cnb_form(array(
            'action' => 'pay',
            'amount' => $this->paymentData->total,
            'currency' => $this->paymentData->code,
            'description' => implode(', ', $title),
            'order_id' => time(),
            'version' => '3',
            'result_url' => $liqpay->return_url
        ));
        echo $html;exit;
    }

    public function twoCheckout()
    {
        $checkout = $this->getServiceData('twocheckout');
        if ($checkout->environment == 'sandbox') {
            $url = 'https://sandbox.2checkout.com/checkout/purchase';
        } else {
            $url = 'https://www.2checkout.com/checkout/purchase';
        }
        $price = baformsHelper::renderPrice((string)$this->paymentData->total, '', '.', 2);
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
?>
        <form id="payment-form" action="<?php echo $url; ?>" method="post">
            <input type="hidden" name="sid" value="<?php echo $checkout->account; ?>">
            <input type="hidden" name="mode" value="2CO">
            <input type="hidden" name="pay_method" value="PPI">
            <input type="hidden" name="x_receipt_link_url" value="<?php echo $checkout->return_url; ?>">
            <input type="hidden" name="li_1_name" value="<?php echo implode(', ', $title); ?>">
            <input type="hidden" name="li_1_price" value="<?php echo $price; ?>">
            <input type="hidden" name="li_1_type" value="product">
            <input type="hidden" name="li_1_quantity" value="1">

        </form>
        <script type="text/javascript">
            document.getElementById('payment-form').submit();
        </script>
<?php 
        exit;
    }
    
    public function paypal()
    {
        $paypal = $this->getServiceData('paypal');
        if ($paypal->environment == 'sandbox') {
            $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $url = 'https://www.paypal.com/cgi-bin/webscr';
        }


        $price = baformsHelper::renderPrice((string)$this->paymentData->total, '', '.', 2);
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
?>
        <form id="payment-form" action="<?php echo $url; ?>" method="post">
            <input type="hidden" name="cmd" value="_ext-enter">
            <input type="hidden" name="redirect_cmd" value="_cart">
            <input type="hidden" name="upload" value="1">
            <input type="hidden" name="business" value="<?php echo $paypal->email; ?>">
            <input type="hidden" name="receiver_email" value="<?php echo $paypal->email; ?>">
            <input type="hidden" name="currency_code" value="<?php echo $this->paymentData->code; ?>">
            <input type="hidden" name="return" value="<?php echo $paypal->return_url; ?>">
            <input type="hidden" name="cancel_return" value="<?php echo $paypal->return_url; ?>">
            <input type="hidden" name="rm" value="2">
            <input type="hidden" name="shipping" value="0">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="charset" value="utf-8">
            <input type="hidden" name="item_name_1" value="<?php echo implode(', ', $title); ?>">
            <input type="hidden" name="amount_1" value="<?php echo $price; ?>">
            <input type="hidden" name="quantity_1" value="1">  
        </form>
        <script type="text/javascript">
            document.getElementById('payment-form').submit();
        </script>
<?php
        exit;
    }

    public function stripeCharges($id, $name, $object)
    {
        $stripe = $this->getServiceData('stripe');
        $this->db = JFactory::getDbo();
        $field = $this->getFormField($name, $id);
        $this->preparePaymentData($id, '', $object, $field);
        $array = array(
            'payment_method_types' => array('card'/*, 'ideal', 'fpx', 'bacs_debit', 'bancontact', 'giropay', 'p24', 'eps'*/),
            'line_items' => [],
            'mode' => 'payment',
            'success_url' => $stripe->return_url,
            'cancel_url' => $stripe->return_url
            );
        $title = [];
        foreach ($this->paymentData->products as $product) {
            $title[] = $product->title;
        }
        $price = baformsHelper::renderPrice((string)$this->paymentData->total, '', '.', '2');
        $line_item = [
            'price_data' => [
                'currency' => $this->paymentData->code,
                'product_data' => [
                    'name' => implode(', ', $title),
                ],
                'unit_amount' => $price * 100,
            ],
            'quantity' => 1
        ];
        $array['line_items'][] = $line_item;
        $ua = array('bindings_version' => '7.17.0', 'lang' => 'php',
            'lang_version' => phpversion(), 'publisher' => 'stripe', 'uname' => php_uname());
        $headers = array('X-Stripe-Client-User-Agent: '.json_encode($ua),
            'User-Agent: Stripe/v1 PhpBindings/7.17.0',
            'Authorization: Bearer '.$stripe->secret_key);
        $curl = curl_init();
        $options = [];
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = $this->encode($array);
        $options[CURLOPT_URL] = 'https://api.stripe.com/v1/checkout/sessions';
        $options[CURLOPT_CONNECTTIMEOUT] = 30;
        $options[CURLOPT_TIMEOUT] = 80;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        print_r($body);exit;
    }

    public function encode($arr, $prefix = null)
    {
        if (!is_array($arr))
            return $arr;
        $r = [];
        foreach ($arr as $k => $v) {
            if (is_null($v))
                continue;
            if ($prefix && $k && !is_int($k))
                $k = $prefix."[".$k."]";
            else if ($prefix)
                $k = $prefix."[]";
            if (is_array($v)) {
                $r[] = $this->encode($v, $k, true);
            } else {
                $r[] = urlencode($k)."=".urlencode($v);
            }
        }

        return implode("&", $r);
    }

    public function payAuthorize($id, $total, $cardNumber, $expirationDate, $cardCode)
    {
        $authorize = $this->getServiceData('authorize');
        $obj = new stdClass();
        $obj->createTransactionRequest = new stdClass();
        $obj->createTransactionRequest->merchantAuthentication = new stdClass();
        $obj->createTransactionRequest->merchantAuthentication->name = $authorize->login_id;
        $obj->createTransactionRequest->merchantAuthentication->transactionKey = $authorize->transaction_key;
        $obj->createTransactionRequest->clientId = 'sdk-php-2.0.0-ALPHA';
        $obj->createTransactionRequest->refId = 'ref'.time();
        $obj->createTransactionRequest->transactionRequest = new stdClass();
        $obj->createTransactionRequest->transactionRequest->transactionType = 'authCaptureTransaction';
        $obj->createTransactionRequest->transactionRequest->amount = $total;
        $obj->createTransactionRequest->transactionRequest->payment = new stdClass();
        $obj->createTransactionRequest->transactionRequest->payment->creditCard = new stdClass();
        $obj->createTransactionRequest->transactionRequest->payment->creditCard->cardNumber = $cardNumber;
        $obj->createTransactionRequest->transactionRequest->payment->creditCard->expirationDate = $expirationDate;
        $obj->createTransactionRequest->transactionRequest->payment->creditCard->cardCode = $cardCode;
        $xmlRequest = json_encode($obj);
        $url =  ($authorize->environment == 'sandbox' ? 'https://apitest' : 'https://api2').'.authorize.net/xml/v1/request.api';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 45);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: text/json"));
        $text = curl_exec($curl);
        curl_close($curl);
        $response = json_decode(substr($text, 3), true);
        $response['return_url'] = $authorize->return_url;
        $str = json_encode($response);
        print_r($str);exit;
    }

    public function getForm($data = [], $loadData = true)
    {
        
    }
    
    public function save($data)
    {
        
    }
    
}