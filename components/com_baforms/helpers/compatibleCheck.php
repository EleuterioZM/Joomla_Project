<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

abstract class compatibleCheck 
{
    private static $formOptions;
    private static $db;
    private static $pageInd;
    private static $submitField;
    private static $rules;

    public static function checkForm($id, $formOptions)
    {
        self::$db = JFactory::getDbo();
        $query = self::$db->getQuery(true)
            ->select('f.*, fs.id AS settings_id')
            ->from('#__baforms_forms AS f')
            ->where('f.id = '.$id)
            ->leftJoin('#__baforms_forms_settings AS fs ON f.id = fs.form_id');
        self::$db->setQuery($query);
        $form = self::$db->loadObject();
        if (empty($form->settings_id) && !empty($form->form_settings)) {
            self::$formOptions = $formOptions;
            self::getFormPages($form);
            self::getFormSettings($form);
        }
    }

    private static function getFormSettings($form)
    {
        $settings = explode('/', $form->form_settings);
        $style = explode(';', $settings[9]);
        $obj = new stdClass();
        $obj->form_id = $form->id;
        $obj->js = $obj->css = '';
        $design = self::getOptions('design');
        $navigation = self::getOptions('navigation');
        $design->theme->suffix = $settings[0];
        $design->theme->color = $form->theme_color;
        $design->label->typography->{'font-size'} = str_replace('px', '', $settings[1]);
        $design->label->typography->color = $settings[2];
        $design->label->typography->{'font-weight'} = $settings[10];
        $design->field->typography->{'font-size'} = str_replace('px', '', $settings[4]);
        $design->field->typography->color = $settings[5];
        $design->field->background->color = $settings[6];
        $design->field->border->color = str_replace('border: 1px solid ', '', $settings[7]);
        $design->field->border->top = true;
        $design->field->border->right = true;
        $design->field->border->bottom = true;
        $design->field->border->left = true;
        $design->field->border->radius = str_replace('px', '', $settings[8]);
        $design->field->icon->size = str_replace('px', '', $settings[11]);
        $design->field->icon->color = $settings[12];
        foreach ($style as $value) {
            $array = explode(':', $value);
            $key = trim($array[0]);
            $keyValue = trim($array[1]);
            if ($key == 'width') {
                $design->form->width->value = str_replace('%', '', $keyValue);
            } else if ($key == 'background-color') {
                $design->form->background->color = $keyValue;
            } else if ($key == 'border') {
                $design->form->border->color = str_replace('border: 1px solid ', '', $keyValue);
                $design->form->border->top = true;
                $design->form->border->right = true;
                $design->form->border->bottom = true;
                $design->form->border->left = true;
            } else if ($key == 'border-radius') {
                $design->form->border->radius = str_replace('px', '', $keyValue);
            }
        }
        $design->form->padding->top = 20;
        $design->form->padding->right = 20;
        $design->form->padding->bottom = 20;
        $design->form->padding->left = 20;
        $navigation->style = 'hidden-navigation-style';
        $navigation->progress = $form->save_continue == 1;
        $obj->design = json_encode($design);
        $obj->navigation = json_encode($navigation);
        $obj->condition_logic = json_encode(self::$rules);
        self::$db->insertObject('#__baforms_forms_settings', $obj);
        if (!empty($form->acym_fields_map)) {
            $acym_fields_map = json_decode($form->acym_fields_map);
            if (!empty($acym_lists)) {
                $acym_lists = json_decode($form->acym_lists);
                $acym_fields_map->list = $acym_lists->array[0];
            }
            $acymStr = json_encode($acym_fields_map);
            $query = self::$db->getQuery(true)
                ->update('#__baforms_forms')
                ->set('acym_fields_map = '.self::$db->quote($acymStr))
                ->where('id = '.$form->id);
            self::$db->setQuery($query)
                ->execute();
        }
        if (!empty($form->mailchimp_api_key)) {
            $mailchimp = self::getService('mailchimp');
            $mailchimp->key = $form->mailchimp_api_key;
            self::$db->updateObject('#__baforms_api', $mailchimp, 'id');
        }
        if (!empty($form->paypal_email)) {
            $paypal = self::getService('paypal');
            $data = json_decode($paypal->key);
            $data->email = $form->paypal_email;
            $data->environment = $form->payment_environment;
            $data->return_url = $form->return_url;
            $paypal->key = json_encode($data);
            self::$db->updateObject('#__baforms_api', $paypal, 'id');
        }
        if (!empty($form->seller_id)) {
            $twocheckout = self::getService('twocheckout');
            $data = json_decode($twocheckout->key);
            $data->account = $form->seller_id;
            $data->environment = $form->payment_environment;
            $data->return_url = $form->return_url;
            $twocheckout->key = json_encode($data);
            self::$db->updateObject('#__baforms_api', $twocheckout, 'id');
        }
        if (!empty($form->stripe_api_key)) {
            $stripe = self::getService('stripe');
            $data = json_decode($stripe->key);
            $data->api_key = $form->stripe_api_key;
            $data->secret_key = $form->stripe_secret_key;
            $data->return_url = $form->return_url;
            $stripe->key = json_encode($data);
            self::$db->updateObject('#__baforms_api', $stripe, 'id');
        }
    }

    private static function getService($service)
    {
        $query = self::$db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.self::$db->quote($service));
        self::$db->setQuery($query);
        $obj = self::$db->loadObject();

        return $obj;
    }

    private static function getFormPages($form)
    {
        $query = self::$db->getQuery(true)
            ->select('*')
            ->from('#__baforms_columns')
            ->where('form_id = '.$form->id)
            ->order('id ASC');
        self::$db->setQuery($query);
        $columns = self::$db->loadObjectList();
        $pages = array();
        self::$pageInd = 0;
        foreach ($columns as $column) {
            if (empty($pages)) {
                $pages[] = self::createPage($form);
            }
            $page = end($pages);
            $settings = explode(',', $column->settings);
            if (trim($settings[1]) != 'spank') {
                $column->parent = $page->key;
                $column->key = trim($settings[0]);
                $column->width = trim($settings[1]);
                $page->columns_order[] = $column->key;
                self::$db->updateObject('#__baforms_columns', $column, 'id');
            } else {
                $pages[] = self::createPage($form);
                $n = count($pages);
                $pages[$n - 1]->order_index = $n;
            }
        }
        self::getFormFields($form);
        if (!empty($pages) && ($form->display_total == 1 || $form->display_submit == 1)) {
            $page = end($pages);
            $page->columns_order[] = 'bacolumn-0';
            $column = new stdClass();
            $column->form_id = $form->id;
            $column->parent = $page->key;
            $column->key = 'bacolumn-0';
            $column->width = 'span12';
            $column->settings = '';
            self::$db->insertObject('#__baforms_columns', $column);
            if ($form->display_total == 1) {
                $total = self::getOptions('total');
                $total->title = '';
                $total->code = $form->currency_code;
                $total->symbol = $form->currency_symbol;
                $total->position = $form->currency_position == 'before' ? '' : 'right-currency-position';
                $key = str_replace('baform-', '', self::$submitField->key) * 1;
                $totalField = new stdClass();
                $totalField->options = json_encode($total);
                $totalField->parent = 'bacolumn-0';
                $totalField->type = 'total';
                $totalField->form_id = $form->id;
                $totalField->column_id = 1;
                $totalField->key = 'baform-'.(++$key);
                $totalField->settings = $totalField->custom = '';
                self::$db->insertObject('#__baforms_items', $totalField);
            }
        }
        foreach ($pages as $page) {
            $page->columns_order = json_encode($page->columns_order);
            self::$db->insertObject('#__baforms_pages', $page);
        }
    }

    private static function createPage($form)
    {
        $obj = new stdClass();
        $obj->form_id = $form->id;
        $obj->key = 'ba-form-page-'.(++self::$pageInd);
        $obj->title = 'Page '.self::$pageInd;
        $obj->order_index = 0;
        $obj->columns_order = array();

        return $obj;
    }

    private static function getFormFields($form)
    {
        $query = self::$db->getQuery(true)
            ->select('*')
            ->from('#__baforms_items')
            ->where('form_id = '.$form->id);
        self::$db->setQuery($query);
        $fields = self::$db->loadObjectList();
        $maxKey = 0;
        $fieldsList = new stdClass();
        $childs = array();
        self::$rules = array();
        foreach ($fields as $field) {
            if (!empty($field->settings)) {
                if (empty($field->options)) {
                    $field->options = '{}';
                }
                $options = json_decode($field->options);
                $settings = explode('_-_', $field->settings);
                if ($settings[0] == 'button') {
                    self::$submitField = $field;
                    $submit = self::getOptions('submit');
                    $array = explode(';', $settings[2]);
                    $style = new stdClass();
                    foreach ($array as $value) {
                        $value = trim($value);
                        if (!empty($value)) {
                            $subarray = explode(':', $value);
                            $key = trim($subarray[0]);
                            $keyValue = trim($subarray[1]);
                            $style->{$key} = $keyValue;
                        }
                    }
                    $submit->background->color = $style->{'background-color'};
                    $submit->background->hover = $style->{'background-color'};
                    $submit->typography->color = $style->color;
                    $submit->typography->hover = $style->color;
                    $submit->typography->{'font-size'} = str_replace('px', '', $style->{'font-size'});
                    $submit->typography->{'font-weight'} = $style->{'font-weight'};
                    $submit->border->radius = str_replace('px', '', $style->{'border-radius'});
                    $submit->label = $settings[1];
                    $submit->database = $form->save_submissions == 1;
                    if ($form->alow_captcha == 'recaptcha' || $form->alow_captcha == 'recaptcha_invisible') {
                        $submit->recaptcha = $form->alow_captcha;
                    }
                    if (!empty($form->redirect_url)) {
                        $submit->onclick = 'redirect';
                        $submit->link = $form->redirect_url;
                    }
                    $payment = array('paypal' => 'paypal', 'stripe' => 'stripe', '2checkout' => 'twocheckout');
                    if ($form->display_total == 1 && isset($payment[$form->payment_methods])) {
                        $submit->onclick = 'payment';
                        $submit->payment = $payment[$form->payment_methods];
                    }
                    $submit->notifications->enable = !empty($form->email_recipient);
                    if (!empty($form->email_recipient)) {
                        $emails = explode(',', $form->email_recipient);
                        foreach ($emails as $email) {
                            $submit->notifications->admin->{$email} = true;
                        }
                    }
                    $submit->notifications->subject = $form->email_subject;
                    $submit->notifications->attach = $form->attach_uploaded_files == 1;
                    $submit->reply->enable = !empty($form->sender_email);
                    $submit->reply->attach = false;
                    $submit->reply->subject = $form->reply_subject;
                    $submit->reply->body = $form->reply_body;
                    $field->options = json_encode($submit);
                    $field->parent = 'bacolumn-0';
                    $field->type = 'submit';
                    $field->key = 'baform-0';
                    $field->column_id = 2;
                } else if ($settings[2] == 'htmltext') {
                    $text = self::getOptions('text');
                    $text->html = $settings[3];
                    self::updateFieldData('text', $text, $field, $settings);
                } else if ($settings[2] == 'terms') {
                    $acceptance = self::getOptions('acceptance');
                    $acceptance->html = $settings[3];
                    self::updateFieldData('acceptance', $acceptance, $field, $settings, '');
                } else if ($settings[2] == 'map') {
                    $map = self::getOptions('map');
                    $style = explode(';', $settings[3]);
                    $map->height = $style[4];
                    $map->styleType = $style[10];
                    $map->controls = $style[6] == 1;
                    if (!empty($style[1])) {
                        $map->marker->position = json_decode($style[1]);
                    }
                    $map->marker->description = $style[2];
                    $map->marker->icon = $style[7];
                    $map->infobox = $style[5] == 1;
                    $map->map->scroll = $style[8] == 1;
                    $map->map->draggable = $style[9] == 1;
                    if (!empty($style[0])) {
                        $obj = json_decode($style[0]);
                        $map->map->center = $obj->center;
                    }
                    self::updateFieldData('map', $map, $field, $settings);
                } else if ($settings[2] == 'image') {
                    $image = self::getOptions('image');
                    $style = explode(';', $settings[3]);
                    $image->src = $style[0];
                    $image->align = $style[1];
                    $image->width = $style[2];
                    $image->alt = $style[3];
                    $image->units->width = '%';
                    self::updateFieldData('image', $image, $field, $settings);
                } else if ($settings[2] == 'date') {
                    $calendar = self::getOptions('calendar');
                    $style = explode(';', $settings[3]);
                    $calendar->required = isset($style[1]) ? $style[1] == 1 : false;
                    $calendar->disable->previous = isset($style[2]) ? $style[2] == 1 : false;
                    self::updateFieldData('calendar', $calendar, $field, $settings, $style[0]);
                } else if ($settings[2] == 'textInput' || $settings[2] == 'textarea') {
                    $input = self::getOptions('input');
                    $style = explode(';', $settings[3]);
                    $input->placeholder = $style[2];
                    $input->required = $style[3] == 1;
                    $input->icon = isset($style[5]) ? $style[5] : '';
                    if ($settings[2] == 'textarea') {
                        $input->type = 'textarea';
                    } else if ($style[4] == 'number'|| $style[4] == 'calculation') {
                        $input->type = 'number';
                    }
                    self::updateFieldData('input', $input, $field, $settings, $style[0], $style[1]);
                } else if ($settings[2] == 'email') {
                    $input = self::getOptions('input');
                    $style = explode(';', $settings[3]);
                    $input->placeholder = $style[2];
                    $input->icon = isset($style[3]) ? $style[3] : '';
                    $input->type = 'email';
                    if (isset($style[4])) {
                        $input->confirm->enable = true;
                        $input->confirm->title = $style[5];
                        $input->confirm->description = $style[6];
                        $input->confirm->placeholder = $style[7];
                        $input->confirm->icon = $style[8];
                    }
                    self::updateFieldData('input', $input, $field, $settings, $style[0], $style[1]);
                } else if ($settings[2] == 'upload') {
                    $upload = self::getOptions('upload');
                    $style = explode(';', $settings[3]);
                    $upload->filesize = $style[2];
                    $upload->types = $style[3];
                    $upload->required = isset($style[4]) ? $style[4] == 1 : false;
                    self::updateFieldData('upload', $upload, $field, $settings, $style[0], $style[1]);
                } else if ($settings[2] == 'slider') {
                    $slider = self::getOptions('slider');
                    $style = explode(';', $settings[3]);
                    $slider->type = 'slider';
                    $slider->min = $style[2];
                    $slider->max = $style[3];
                    $slider->step = $style[4];
                    self::updateFieldData('slider', $slider, $field, $settings, $style[0], $style[1]);
                } else if ($settings[2] == 'address') {
                    $address = self::getOptions('address');
                    $style = explode(';', $settings[3]);
                    $address->placeholder = $style[2];
                    $address->required = $style[3] == 1;
                    $address->icon = $style[4];
                    self::updateFieldData('slider', $slider, $field, $settings, $style[0], $style[1]);
                } else if ($settings[2] == 'chekInline' || $settings[2] == 'checkMultiple' || $settings[2] == 'radioInline'
                    || $settings[2] == 'radioMultiple' || $settings[2] == 'dropdown' || $settings[2] == 'selectMultiple') {
                    if ($settings[2] == 'radioInline' || $settings[2] == 'radioMultiple') {
                        $type = 'radio';
                    } else if ($settings[2] == 'dropdown') {
                        $type = 'select';
                    } else if ($settings[2] == 'selectMultiple') {
                        $type = 'selectMultiple';
                    } else {
                        $type = 'checkbox';
                    }
                    $checkbox = self::getOptions($type);
                    $style = explode(';', $settings[3]);
                    $checkbox->items = new stdClass();
                    $list = explode('\n', $style[2]);
                    $price = false;
                    $default = $settings[2] == 'dropdown' ? 5 : 4;
                    foreach ($list as $key => $element) {
                        $elementValue = explode('====', $element);
                        $obj = new stdClass();
                        $obj->title = $elementValue[0];
                        $obj->price = isset($elementValue[1]) ? $elementValue[1] : '';
                        $obj->default = isset($style[$default]) && $style[$default] == $key;
                        if (!$price && !empty($obj->price)) {
                            $price = true;
                        }
                        $obj->key = $key;
                        if (isset($options->imageMap) && isset($options->imageMap->{$key})) {
                            $obj->image = $options->imageMap->{$key};
                        }
                        $checkbox->items->{$key}  = $obj;
                    }
                    $checkbox->required = $style[3] == 1;
                    $checkbox->type = $price ? 'product' : '';
                    if ($settings[2] != 'dropdown' && $settings[2] != 'selectMultiple') {
                        if ($settings[2] == 'checkMultiple' || $settings[2] == 'radioMultiple') {
                            $options->width = 100;
                        } else if (!isset($options->width)) {
                            $options->width = 25;
                        }
                        $checkbox->count = floor(100 / $options->width);
                    }
                    self::updateFieldData($type, $checkbox, $field, $settings, $style[0], $style[1]);
                }
                $fieldKey = str_replace('baform-', '', $field->key) * 1;
                $maxKey = max($maxKey, $fieldKey);
                $fieldsList->{$field->key} = $field;
                if (strpos($field->parent, 'bacolumn') === false) {
                    self::getRule($field);
                    $childs[] = $field;
                }
                if ($settings[0] != 'button') {
                    self::$db->updateObject('#__baforms_items', $field, 'id');
                }
            }
        }
        foreach ($childs as $child) {
            $child->parent = self::getConditions($fields, $child->key);
            self::$db->updateObject('#__baforms_items', $child, 'id');
        }
        self::$submitField->key = 'baform-'.(++$maxKey);
        self::$db->updateObject('#__baforms_items', self::$submitField, 'id');
    }

    private static function getConditions($fields, $key)
    {
        $parent = '';
        foreach ($fields as $field) {
            if ($field->key == $key) {
                $parent = $field->parent;
                break;
            }
        }
        if (!empty($parent) && strpos($parent, 'bacolumn') === false) {
            $parent = self::getConditions($fields, $parent);
        }

        return $parent;
    }

    private static function getRule($field)
    {
        $settings = explode('_-_', $field->settings);
        $rule = new stdClass();
        $rule->title = 'New Rule';
        $rule->publish = true;
        $rule->operation = 'AND';
        $id = str_replace('baform-', '', $field->parent);
        $when = new stdClass();
        $when->field = $id;
        $when->state = 'equal';
        $when->value = $settings[4];
        $rule->when = array($when);
        $do = new stdClass();
        $id = str_replace('baform-', '', $field->key);
        $do->field = $id;
        $do->action = 'show';
        $rule->do = array($do);
        self::$rules[] = $rule;
    }

    private static function updateFieldData($type, $obj, $field, $settings, $title = null, $description = null)
    {
        if (isset($obj->suffix)) {
            $obj->suffix = $field->custom;
        }
        if (isset($title)) {
            $obj->title = $title;
        }
        if (isset($description)) {
            $obj->description = $description;
        }
        $field->options = json_encode($obj);
        $field->type = $type;
        $field->parent = $settings[0];
        $field->key = $settings[1];
    }

    private static function getOptions($key)
    {
        $str = json_encode(self::$formOptions->{$key});
        $obj = json_decode($str);

        return $obj;
    }
}