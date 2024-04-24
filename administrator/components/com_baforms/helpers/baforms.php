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

abstract class baformsHelper 
{
    public static $countries;
    public static $fonts;

    public static function readFile($path)
    {
        $handle = fopen($path, "r");
        $content = fread($handle, filesize($path));
        fclose($handle);

        return $content;
    }

    public static function deleteFolder($dir)
    {
        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        self::deleteFolder($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function getSubmission($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
           ->select('*')
           ->from('#__baforms_submissions')
           ->where('id = '.$id);
        $db->setQuery($query);
        $result = $db->loadObject();
        if (strpos($result->message, '|-_-|') !== false) {
            $message = [];
            $array = explode('_-_', $result->message);
            foreach ($array as $value) {
                $data = explode('|-_-|', $value);
                $object = new stdClass();
                $object->title = $data[0];
                $object->message = $data[1];
                $object->type = $data[2];
                $message[] = $object;
            }
        } else {
            $message = json_decode($result->message);
            $pollResults = new stdClass();
            foreach ($message as $value) {
                if ($value->type == 'poll') {
                    $query = $db->getQuery(true)
                        ->select('data')
                        ->from('#__baforms_poll_statistic')
                        ->where('submission_id = '.$id)
                        ->where('field_id = '.$value->field_id);
                    $db->setQuery($query);
                    $data = $db->loadResult();
                    $pollResults->{$value->field_id} = json_decode($data);
                }
            }
            $result->pollResults = $pollResults;
        }
        
        $result->message = $message;

        return $result;
    }

    public static function getCustomPayments()
    {
        $path = JPATH_ROOT.'/components/com_baforms/libraries/custom-payment-gateway/custom-payment-gateway.xml';
        $str = self::readFile($path);
        $data = array();
        if (function_exists('simplexml_load_string')) {
            $xml = simplexml_load_string($str);
            foreach ($xml->payment as $payment) {
                $obj = new stdClass();
                foreach ($payment as $key => $value) {
                    $obj->{(string)$key} = trim((string)$value);
                }
                if ($obj->class == 'example') {
                    continue;
                }
                $data[] = $obj;
            }
        }

        return $data;
    }

    public static function prepareCustomPayments()
    {
        $dir = JPATH_ROOT.'/components/com_baforms/libraries/custom-payment-gateway';
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }
        if (!JFile::exists($dir.'/custom-payment-gateway.xml')) {
            $doc = new DOMDocument('1.0');
            $doc->formatOutput = true;
            $root = $doc->createElement('payments');
            $root = $doc->appendChild($root);
            $payment = $doc->createElement('payment');
            $payment = $root->appendChild($payment);
            self::xmlProperty($doc, $payment, 'label', 'Example Payment');
            self::xmlProperty($doc, $payment, 'path', 'media/example/example.php');
            self::xmlProperty($doc, $payment, 'class', 'example');
            $doc->save($dir.'/custom-payment-gateway.xml');
        }
    }

    public static function xmlProperty($doc, $item, $key, $value)
    {
        $title = $doc->createElement($key);
        $title = $item->appendChild($title);
        $data = $doc->createTextNode($value);
        $data = $title->appendChild($data);
    }

    public static function integrationExists($type)
    {
        switch ($type) {
            case 'redsys':
                $path = JPATH_ROOT.'/components/com_baforms/libraries/redsys';
                break;
            case 'google_sheets':
            case 'google_drive':
                $path = JPATH_ROOT.'/components/com_baforms/libraries/google-v4';
                break;
            case 'campaign_monitor':
                $path = JPATH_ROOT.'/components/com_baforms/libraries/campaign-monitor';
                break;
        }
        $flag = JFolder::exists($path);
        if ($type == 'google_drive' && !$flag) {
            $flag = JFolder::exists(JPATH_ROOT.'/components/com_baforms/libraries/google-drive');
        }

        return JFolder::exists($path);
    }

    public static function checkIntegration($type)
    {
        $exists = self::integrationExists($type);
        $className = !$exists ? ' require-library' : '';

        return $className;
    }

    public static function drawPages($pages, $formShortCodes = null)
    {
        $appItems = new stdClass();
        $html = '';
        $pagesCount = count($pages);
        foreach ($pages as $ind => $page) {
            include JPATH_COMPONENT.'/views/layout/elements/page.php';
            $pageStr = str_replace('ba-form-page-1', $page->key, $out);
            $pageStr = str_replace('data-id="0"', 'data-id="'.$page->id.'"', $pageStr);
            $pageStr = str_replace('data-title=""', 'data-title="'.htmlentities($page->title, ENT_COMPAT).'"', $pageStr);
            $rowsStr = '';
            $columnsStr = '';
            $columns_order = json_decode($page->columns_order, true);
            $columnWidth = 0;
            foreach ($columns_order as $i => $key) {
                if (!isset($page->columns)) {
                    $column = self::getFormColumns($page->form_id, $key);
                } else {
                    $column = $page->columns[$i];
                }
                if (!$column) {
                    continue;
                }
                $span = str_replace('span', '', $column->width);
                include JPATH_COMPONENT.'/views/layout/elements/column.php';
                $columnStr = $out;
                $columnStr = str_replace('span12', $column->width, $columnStr);
                $columnStr = str_replace('data-span="12"', 'data-span="'.$span.'"', $columnStr);
                $columnStr = str_replace('data-id="0"', 'data-id="'.$column->id.'"', $columnStr);
                $columnStr = str_replace('bacolumn-1', $column->key, $columnStr);
                $columnStr = str_replace('Span 12', 'Span '.$span, $columnStr);
                $fieldsStr = '';
                if (!isset($column->fields)) {
                    $fields = self::getFormItems($page->form_id, $column->key);
                } else {
                    $fields = $column->fields;
                }
                foreach ($fields as $field) {
                    $field->options = json_decode($field->options);
                    if ($field->type == 'submit' && $field->options->typography->{'font-family'} != 'inherit') {
                        $fonts[] = $field->options->typography->{'font-family'};
                    }
                    $appItems->{$field->key} = $field->options;
                    include JPATH_COMPONENT.'/views/layout/elements/'.$field->type.'.php';
                    $str = $out;
                    $str = str_replace('id=""', 'id="'.$field->key.'"', $str);
                    $str = str_replace('data-id="0"', 'data-id="'.$field->id.'"', $str);
                    $fieldsStr .= $str;
                }
                $columnStr = str_replace('[ba-forms-fields]', $fieldsStr, $columnStr);
                if ($columnWidth != 0) {
                    include JPATH_COMPONENT.'/views/layout/elements/resizer.php';
                    $columnsStr .= $out;
                }
                $columnsStr .= $columnStr;
                $columnWidth += intval($span);
                if ($columnWidth == 12) {
                    include JPATH_COMPONENT.'/views/layout/elements/row.php';
                    $rowStr = $out;
                    $rowsStr .= str_replace('[ba-columns]', $columnsStr, $rowStr);
                    $columnWidth = 0;
                    $columnsStr = '';
                }
            }
            $pageStr = str_replace('[ba-rows]', $rowsStr, $pageStr);
            $html .= $pageStr;
        }

        return array($html, $appItems);
    }

    public static function renderPrice($value, $thousand, $separator, $decimals)
    {
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

        return $value;
    }

    public static function addIntegration($service, $key)
    {
        $db = JFactory::getDbo();
        $obj = new stdClass();
        $obj->service = $service;
        $obj->key = $key;
        $db->insertObject('#__baforms_api', $obj);
        $obj->id = $db->insertid();

        return $obj;
    }

    public static function getIntegrations($id)
    {
        $db = JFactory::getDbo();
        $array = array('google_drive','google_maps', 'google_sheets', 'paypal', 'twocheckout', 'mailchimp', 'mollie',
            'stripe', 'activecampaign', 'authorize', 'liqpay', 'payupl', 'cloudpayments', 'campaign_monitor', 
            'getresponse', 'zoho_crm', 'zoho_auth', 'robokassa', 'payu_latam', 'yandex_kassa', 'redsys',
            'payfast', 'paypal_sdk', 'hcaptcha');
        $where = array();
        foreach ($array as $value) {
            $where[] = 'service = '.$db->quote($value);
        }
        $wheres = implode(' OR ', $where);
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where($wheres);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        $data = new stdClass();
        foreach ($items as $value) {
            $data->{$value->service} = $value;
        }
        if (empty($data->mollie)) {
            $data->mollie = self::addIntegration('mollie', '{"api_key":"","return_url":""}');
        }
        if (empty($data->google_drive)) {
            $data->google_drive = self::addIntegration('google_drive', '{"client_id":"", "client_secret":"", "code":"", "accessToken":""}');
        }
        if (empty($data->liqpay)) {
            $data->liqpay = self::addIntegration('liqpay', '{"public_key":"","private_key":"","return_url":""}');
        }
        if (empty($data->robokassa)) {
            $data->robokassa = self::addIntegration('robokassa', '{"shop_id":"","password":""}');
        }
        if (empty($data->payupl)) {
            $data->payupl = self::addIntegration('payupl', '{"pos_id":"","second_key":"","environment":"","return_url":""}');
        }
        if (empty($data->cloudpayments)) {
            $data->cloudpayments = self::addIntegration('cloudpayments', '{"public_id":"","return_url":""}');
        }
        if (empty($data->campaign_monitor)) {
            $data->campaign_monitor = self::addIntegration('campaign_monitor', '{"api_key":"","client_id":""}');
        }
        if (empty($data->getresponse)) {
            $data->getresponse = self::addIntegration('getresponse', '{"api_key":"","custom_fields":false}');
        }
        if (empty($data->zoho_crm)) {
            $data->zoho_crm = self::addIntegration('zoho_crm', '{"client_id":"","client_secret":""}');
        } else if (strpos($data->zoho_crm->key, 'grant_token')) {
            $data->zoho_crm->key = '{"client_id":"","client_secret":""}';
            $db->updateObject('#__baforms_api', $data->zoho_crm, 'id');
        }
        if (empty($data->zoho_auth)) {
            $data->zoho_auth = self::addIntegration('zoho_auth', '{}');
        }
        if (strpos($data->yandex_kassa->key, 'environment')) {
            $data->yandex_kassa->key = '{"shop_id":"","secret_key":"","return_url":""}';
            $db->updateObject('#__baforms_api', $data->yandex_kassa, 'id');
        }
        $query = $db->getQuery(true)
            ->select('id, telegram_token, acym_lists, acym_fields_map, mailchimp_list_id, mailchimp_fields_map,
                google_drive, google_sheets, pdf_submissions, campaign_monitor_fields, getresponse_fields,
                zoho_crm_fields, activecampaign_fields')
            ->from('#__baforms_forms')
            ->where('id = '.$id);
        $db->setQuery($query);
        $obj = $db->loadObject();
        if (empty($obj->campaign_monitor_fields)) {
            $obj->campaign_monitor_fields = '{}';
        }
        $object = json_decode($obj->campaign_monitor_fields);
        $campaign_monitor = json_decode($data->campaign_monitor->key);
        $object->api_key = $campaign_monitor->api_key;
        $object->client_id = $campaign_monitor->client_id;
        $str = json_encode($object);
        $data->campaign_monitor->key = $str;
        if (empty($obj->getresponse_fields)) {
            $obj->getresponse_fields = '{}';
        }
        $object = json_decode($obj->getresponse_fields);
        $getresponse = json_decode($data->getresponse->key);
        $object->api_key = $getresponse->api_key;
        $object->custom_fields = $getresponse->custom_fields;
        $str = json_encode($object);
        $data->getresponse->key = $str;
        if (empty($obj->zoho_crm_fields)) {
            $obj->zoho_crm_fields = '{}';
        }
        $object = json_decode($obj->zoho_crm_fields);
        $zoho_crm = json_decode($data->zoho_crm->key);
        foreach ($zoho_crm as $key => $value) {
            $object->{$key} = $value;
        }
        $str = json_encode($object);
        $data->zoho_crm->key = $str;
        if (strpos($data->google_sheets->key, 'client_id') === false) {
            $data->google_sheets->key = '{"client_id":"", "client_secret":"", "code":"", "accessToken": ""}';
            $obj->google_sheets = '{}';
        }
        if (!empty($obj->google_sheets) && $obj->google_sheets != '{}') {
            $key = json_decode($data->google_sheets->key);
            $google_sheets = json_decode($obj->google_sheets);
            $key->spreadsheet = $google_sheets->spreadsheet;
            $key->worksheet = $google_sheets->worksheet;
            foreach ($google_sheets->columns as $ind => $value) {
                $key->{$ind} = $value;
            }
            $data->google_sheets->key = json_encode($key);
        }
        if (strpos($data->google_drive->key, 'client_id') === false) {
            $data->google_drive->key = '{"client_id":"", "client_secret":"", "code":"", "accessToken":""}';
            $obj->google_drive = '{"folder":"","pdf":false,"files":false}';
        }
        if (empty($obj->google_drive)) {
            $obj->google_drive = '{"folder":"","pdf":false,"files":false}';
        }
        $object = json_decode($obj->google_drive);
        $google_drive = json_decode($data->google_drive->key);
        foreach ($object as $ind => $value) {
            $google_drive->{$ind} = $value;
        }
        $str = json_encode($google_drive);
        $data->google_drive->key = $str;
        if (empty($obj->activecampaign_fields)) {
            $obj->activecampaign_fields = '{"email":"","firstName":"","lastName":"","phone":"","list":""}';
        }
        $object = json_decode($obj->activecampaign_fields);
        $activecampaign = json_decode($data->activecampaign->key);
        foreach ($object as $ind => $value) {
            $activecampaign->{$ind} = $value;
        }
        $str = json_encode($activecampaign);
        $data->activecampaign->key = $str;
        if (empty($obj->pdf_submissions)) {
            $obj->pdf_submissions = '{"enable":false,"title":false,"empty":false,"size":"A4","orientation":"Portrait"}';
        }
        if (empty($obj->acym_fields_map)) {
            $obj->acym_fields_map = '{"name":"","email":"","list":""}';
        }
        if (!empty($obj->acym_lists)) {
            $acym = json_decode($obj->acym_fields_map);
            $lists = json_decode($obj->acym_lists);
            $acym->list = $lists->array[0];
            $obj->acym_lists = '';
            $obj->acym_fields_map = json_encode($acym);
            $db->updateObject('#__baforms_forms', $obj, 'id');
        }
        if (empty($obj->mailchimp_fields_map)) {
            $obj->mailchimp_fields_map = '{}';
        }
        $data->pdf_submissions = self::getIntegrationsObject('pdf_submissions', $obj->pdf_submissions);
        $data->telegram = self::getIntegrationsObject('telegram', $obj->telegram_token);
        $data->acymailing = self::getIntegrationsObject('acymailing', $obj->acym_fields_map);
        $object = json_decode($obj->mailchimp_fields_map);
        $object->list = $obj->mailchimp_list_id;
        $object->api_key = $data->mailchimp->key;
        $str = json_encode($object);
        $data->mailchimp->key = $str;
        
        return $data;
    }

    public static function getIntegrationsObject($service, $key)
    {
        $obj = new stdClass();
        $obj->service = $service;
        $obj->key = $key;

        return $obj;
    }

    public static function setDesignCssVariable($group, $subgroup, $option, $obj, $subname = '')
    {
        if (!empty($subgroup)) {
            $value = $obj->{$group}->{$subgroup}->{$option};
            $property = '--'.$group.'-'.$subgroup.'-'.$option;
        } else if (!empty($group)) {
            $value = $obj->{$group}->{$option};
            $property = '--'.($subname ? $subname.'-' : '').$group.'-'.$option;
        } else {
            $value = $obj->{$option};
            $property = '--'.($subname ? $subname.'-' : '').$option;
        }
        if (!empty($group) && isset($obj->{$group}->units) && isset($obj->{$group}->units->{$subgroup.'-'.$option})) {
            $value .= $obj->{$group}->units->{$subgroup.'-'.$option};
        } else if (!empty($group) && isset($obj->{$group}->units) && isset($obj->{$group}->units->{$option})) {
            $value .= $obj->{$group}->units->{$option};
        } else if (isset($obj->units) && isset($obj->units->{$option})) {
            $value .= $obj->units->{$option};
        } else if (!empty($group) && isset($obj->units) && isset($obj->units->{$group})) {
            $value .= $obj->units->{$group};
        } else if (!empty($group) && isset($obj->units) && isset($obj->units->{$group.'-'.$option})) {
            $value .= $obj->units->{$group.'-'.$option};
        } else if ($option == 'fullwidth') {
            $value = $value ? '100%' : 'auto';
        } else if ($subgroup == 'padding' || $subgroup == 'margin') {
            $value .= $obj->{$group}->units->{$subgroup};
        } else if (is_bool($value)) {
            $value = (int)$value;
        } else if ($option == 'font-family') {
            $value = str_replace('+', ' ', $value);
        }

        return $property.': '.$value;
    }

    public static function setDesignCssVariables($design)
    {
        $value = "";
        foreach ($design as $group => $groupValue) {
            if ($group == 'theme') {
                $value .= self::setDesignCssVariable('theme', '', 'color', $design).";\n\t";
                continue;
            } else if ($group == 'lightbox') {
                $value .= self::setDesignCssVariable('lightbox', '', 'color', $design).";\n\t";
                continue;
            }
            foreach ($groupValue as $subgroup => $subgroupValue) {
                if ($subgroup != 'units') {
                    foreach ($subgroupValue as $option => $optionValue) {
                        if ($option == 'link') {
                            continue;
                        }
                        $value .= self::setDesignCssVariable($group, $subgroup, $option, $design).";\n\t";
                    }
                }
            }
        }

        return $value;
    }

    public static function getFormsSettings($id, $formOptions)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_forms_settings')
            ->where('form_id = '.$id);
        $db->setQuery($query);
        $setings = $db->loadObject();
        if (!isset($setings->design)) {
            $dir = JPATH_COMPONENT.'/assets/json/';
            $obj = new stdClass();
            $obj->form_id = $id;
            $obj->design = json_encode($formOptions->design);
            $obj->navigation = json_encode($formOptions->navigation);
            $str = '[{"title":"New Rule","publish":true,"operation":"AND",'.
                '"when":[{"field":"","state":"","value":""}],"do":[{"field":"","action":""}]}]';
            $obj->condition_logic = $str;
            $obj->js = $obj->css = '';
            $db->insertObject('#__baforms_forms_settings', $obj);
            $setings = $obj;
        }

        return $setings;
    }

    public static function renderDefaultValue($value, $shortCodes)
    {
        foreach ($shortCodes as $ind => $shortCode) {
            $value = str_replace($ind, $shortCode, $value);
        }

        return $value;
    }

    public static function getTemplates($options)
    {
        $path = JPATH_ROOT.'/components/com_baforms/libraries/countries/countries.json';
        $str = self::readFile($path);
        self::$countries = json_decode($str);
        $obj = new stdClass();
        $dir = JPATH_COMPONENT.'/views/layout/elements';
        $files = JFolder::files($dir);
        foreach ($files as $file) {
            $key = str_replace('.php', '', $file);
            if (isset($options->{$key})) {
                $field = new stdClass();
                $field->options = $options->{$key};
                $field->id = 0;
                $field->key = '';
            }
            include($dir.'/'.$file);
            $obj->{$key} = $out;
        }

        return $obj;
    }

    public static function getFormItems($id, $key)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_items')
            ->where('form_id = '.$id)
            ->where('`parent` = '.$db->quote($key))
            ->order('`column_id` ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public static function getFormColumns($id, $key)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_columns')
            ->where('form_id = '.$id)
            ->where('`key` = '.$db->quote($key));
        $db->setQuery($query);
        $column = $db->loadObject();
        
        return $column;
    }

    public static function replace($str)
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
    
    public static function checkUserEditLevel()
    {
        if (!JFactory::getUser()->authorise('core.edit', 'com_baforms')) {
            exit;
        }
    }

    public static function getUnreadSubmissionsCount()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__baforms_submissions')
            ->where('submission_state = 1');
        $db->setQuery($query);
        $count  = $db->loadResult();

        return $count;
    }
    
    public static function checkActive($app)
    {
        $active = '';
        $input = JFactory::getApplication()->input;
        $view = $input->get('view', 'forms', 'string');
        if ($app == $view) {
            $active = 'active';
        }

        return $active;
    }

    public static function getAcymailingFields()
    {
        $checkAcymailing = self::checkAcymailing();
        $fields = array();
        if (!empty($checkAcymailing)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, name')
                ->from('#__acym_field')
                ->where('namekey <> '.$db->quote('acym_name'))
                ->where('namekey <> '.$db->quote('acym_email'))
                ->where('namekey <> '.$db->quote('acym_language'));
            $db->setQuery($query);
            $fields = $db->loadObjectList();
        }

        return $fields;
    }

    public static function getAcymailingLists()
    {
        $html = '';
        $checkAcymailing = self::checkAcymailing();
        if (!empty($checkAcymailing)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, name')
                ->from('#__acym_list');
            $db->setQuery($query);
            $list = $db->loadObjectList();
            foreach ($list as $value) {
                $html .= '<option value="'.$value->id.'">'.$value->name.'</option>';
            }
        }

        return $html;
    }

    public static function checkAcymailing()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('element = '.$db->quote('com_acym'));
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function checkFormsState()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();
        if (empty($balbooa)) {
            $obj = new stdClass();
            $obj->key = $balbooa = '{}';
            $obj->service = 'balbooa';
            $db->insertObject('#__baforms_api', $obj);
            $obj = new stdClass();
            $obj->key = $balbooa = '{}';
            $obj->service = 'balbooa_activation';
            $db->insertObject('#__baforms_api', $obj);
        }

        return $balbooa;
    }

    public static function checkFormsActivation()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('`key`')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadResult();

        return $balbooa;
    }

    public static function setAppLicense($data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = json_decode($balbooa->key);
        $balbooa->key->data = $data;
        $balbooa->key = json_encode($balbooa->key);
        $db->updateObject('#__baforms_api', $balbooa, 'id');
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = '{"data":"active"}';
        $db->updateObject('#__baforms_api', $balbooa, 'id');
    }

    public static function aboutUs()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("manifest_cache");
        $query->from("#__extensions");
        $query->where("type=" .$db->quote('component'))
            ->where('element=' .$db->quote('com_baforms'));
        $db->setQuery($query);
        $cache = $db->loadResult();
        $about = json_decode($cache);
        $xml = simplexml_load_file(JPATH_ROOT.'/administrator/components/com_baforms/baforms.xml');
        $about->tag = (string)$xml->tag;

        return $about;
    }

    public static function getFormsLanguage()
    {
        $result = [];
        $keys = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER',
            'OCTOBER', 'NOVEMBER', 'DECEMBER', 'SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];
        foreach ($keys as $value) {
            $result[$value] = JText::_($value);
        }
        $shortDate = ['JAN' => '2019-01-14', 'FEB' => '2019-02-14', 'MAR' => '2019-03-14', 'APR' => '2019-04-14',
            'MAY' => '2019-05-14', 'JUN' => '2019-06-14', 'JUL' => '2019-07-14', 'AUG' => '2019-08-14', 'SEP' => '2019-09-14',
            'OCT' => '2019-10-14', 'NOV' => '2019-11-14', 'DEC' => '2019-12-14'];
        foreach ($shortDate as $key => $value) {
            $result[$key] = JHtml::date(strtotime($value), 'M');
        }
        $path = JPATH_ROOT.'/administrator/components/com_baforms/language/en-GB/en-GB.com_baforms.ini';
        if (JFile::exists($path)) {
            $contents = self::readFile($path);
            $contents = str_replace('_QQ_', '"\""', $contents);
            $data = parse_ini_string($contents);
            foreach ($data as $ind => $value) {
                $result[$ind] = JText::_($ind);
            }
        }
        $data = 'var formsLanguage = '.json_encode($result).';';

        return $data;
    }
}