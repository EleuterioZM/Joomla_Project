<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

abstract class baformsHelper 
{
    public static $state;
    public static $about;
    public static $shortCodes;
    public static $design;
    public static $fonts = [];
    public static $fontawesome;
    public static $material;
    public static $conditionLogic;
    public static $countries;

    public static function readFile($path)
    {
        $handle = fopen($path, "r");
        $content = fread($handle, filesize($path));
        fclose($handle);

        return $content;
    }

    public static function checkUserPoll($field)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__baforms_poll_results')
            ->where('form_id = '.$field->form_id)
            ->where('field_id = '.$field->id)
            ->where('ip = '.$db->quote($ip));
        $db->setQuery($query);
        $count = $db->loadResult();
        $allow = $count == 0;

        return $allow;
    }

    public static function getPollResults($id, $items)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_poll_results')
            ->where('field_id = '.$id);
        $db->setQuery($query);
        $array = $db->loadObjectList();
        $count = count($array);
        $results = new stdClass();
        $order = [];
        foreach ($array as $value) {
            if (!isset($results->{$value->value})) {
                $results->{$value->value} = new stdClass();
                $results->{$value->value}->votes = 0;
            }
            $results->{$value->value}->votes++;
        }
        $percent = 0;
        foreach ($results as $key => $value) {
            $value->percent = round($value->votes * 100 / $count, 1);
            $percent += $value->percent;
            $order[(string)$key] = $value->percent;
        }
        arsort($order);
        $i = 0;
        foreach ($order as $key => $value) {
            $results->{$key}->order = $i;
            $i++;
        }
        foreach ($items as $key => $item) {
            if (!isset($results->{$item->key})) {
                $obj = new stdClass();
                $obj->votes = $obj->percent = 0;
                $obj->order = $i;
                $results->{$item->key} = $obj;
            }
            $results->{$item->key}->title = $item->title;
        }

        return $results;
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
            } else if ($group == 'css' || $group == 'js') {
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

    public static function getFormShortCodes($id)
    {
        $user = JFactory::getUser();
        $doc = JFactory::getDocument();
        $input = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title')
            ->from('#__baforms_forms')
            ->where('id = '.$id);
        $db->setQuery($query);
        $formTitle = $db->loadResult();
        $task = $input->get('task', '', 'string');
        $page_id = $input->get('id', 0, 'int');
        if (is_array($page_id) || $task == 'loadAjaxForm') {
            $page_id = 0;
        }
        $time = time();
        self::$shortCodes = new stdClass();
        self::$shortCodes->{'[Username]'} = $user->id != 0 ? $user->username : '';
        self::$shortCodes->{'[User Name]'} = $user->id != 0 ? $user->name : '';
        self::$shortCodes->{'[User Email]'} = $user->id != 0 ? $user->email : '';
        self::$shortCodes->{'[User ID]'} = strval($user->id);
        self::$shortCodes->{'[User IP Address]'} = $_SERVER['REMOTE_ADDR'];
        self::$shortCodes->{'[Date]'} = JHtml::date($time, 'j F Y');
        self::$shortCodes->{'[Time]'} = JHtml::date($time, 'H:i:s');
        self::$shortCodes->{'[Time AM / PM]'} = JHtml::date($time, 'h:i:s A');
        self::$shortCodes->{'[Page Title]'} = $task == 'loadAjaxForm' ? '[Page Title]' : $doc->title;
        self::$shortCodes->{'[Page URL]'} = $task == 'loadAjaxForm' ? '[Page URL]' : $_SERVER['REQUEST_URI'];
        self::$shortCodes->{'[Page ID]'} = strval($page_id);
        self::$shortCodes->{'[Form Title]'} = $formTitle;
        self::$shortCodes->{'[Form ID]'} = strval($id);
        self::$shortCodes->{'[Submission ID]'} = '';
    }

    public static function renderDefaultValue($value, $slash = false)
    {
        foreach (self::$shortCodes as $ind => $shortCode) {
            if ($slash) {
                $shortCode = addcslashes($shortCode, '\'');
            }
            $value = str_replace($ind, $shortCode, $value);
        }
        $value = preg_replace('/\[Field ID=\d+\]/', '', $value);
        preg_match_all('/\[URL parameter = (.*?)\]/', $value, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            $input = JFactory::getApplication()->input;
            foreach ($matches as $match) {
                $result = $input->get->get($match[1], '', 'string');
                $value = str_replace($match[0], $result, $value);
            }
        }
        preg_match_all('/\[SQL query = (.*?)\]/', $value, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            $db = JFactory::getDbo();
            foreach ($matches as $match) {
                try {
                    $query = $match[1];
                    $db->setQuery($query);
                    $result = $db->loadResult();
                    $value = str_replace($match[0], $result, $value);
                } catch (Exception $e) {
                    $value = str_replace($match[0], '', $value);
                }
            }
        }

        return $value;
    }

    public static function getMapsKey()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('`key`')
            ->from('`#__baforms_api`')
            ->where('`service` = '.$db->quote('google_maps'));
        $db->setQuery($query);
        $key = $db->loadResult();
        return $key;
    }

    public static function loadJQuery($id)
    {
        $params = JComponentHelper::getParams('com_baforms');
        $jquery = $params->get('load_jquery', 1);
        
        return $jquery;
    }

    public static function getFormOptions()
    {
        $obj = new stdClass();
        $dir = JPATH_ROOT.'/administrator/components/com_baforms/assets/json/';
        $files = JFolder::files($dir);
        foreach ($files as $value) {
            $str = self::readFile($dir.$value);
            $key = str_replace('.json', '', $value);
            $obj->{$key} = json_decode($str);
        }

        return $obj;
    }

    public static function renderFormHTML($body)
    {
        JLoader::register('compatibleCheck', JPATH_ROOT.'/components/com_baforms/helpers/compatibleCheck.php');
        $app = JFactory::getApplication();
        $input = $app->input;
        $option = $input->get('option', '', 'string');
        $view = $input->get('view', '', 'string');
        if ($option == 'com_sppagebuilder' && $view == 'form') {
            return $body;
        }
        $regex = '/\[forms ID=+(.*?)\]/i';
        $array = [];
        preg_match_all($regex, $body, $matches, PREG_SET_ORDER);
        $formOptions = null;
        foreach ($matches as $index => $match) {
            $id = $match[1] * 1;
            if (!empty($id) && is_numeric($id) && self::checkForm($id)) {
                if (!isset($array[$id])) {
                    if (empty($formOptions)) {
                        $formOptions = self::getFormOptions();
                        $str = self::readFile(JPATH_ROOT.'/components/com_baforms/libraries/countries/countries.json');
                        self::$countries = json_decode($str);
                    }
                    compatibleCheck::checkForm($id, $formOptions);
                    $array[$id] = new stdClass();
                    $array[$id]->html = self::drawHTMLPage($id);
                    $array[$id]->design = self::$design;
                    $array[$id]->conditionLogic = self::$conditionLogic;
                }
                $html = $array[$id]->html;
                $body = @preg_replace("|\[forms ID=".$id."\]|", addcslashes($html, '\\$'), $body, 1);
            }
        }
        if (!empty($array)) {
            $body = self::drawScripts($array).$body;
        }

        return $body;
    }
    
    public static function drawScripts($cid)
    {
        $doc = JFactory::getDocument();
        $scripts = $doc->_scripts;
        $array = [];
        $loadFormsMap = new stdClass();
        $loadFormsMap->load = false;
        foreach ($scripts as $key => $script) {
            if (strpos($key, 'maps.googleapis.com/maps/api/js?libraries=places')) {
                $loadFormsMap->load = false;
            }
            $key = explode('/', $key);
            $array[] = end($key);
        }
        foreach ($cid as $id => $value) {
            $signatures = self::getScriptItemsCount($id, 'signature');
            if (!$loadFormsMap->load && (self::getScriptItemsCount($id, 'map') || self::getScriptItemsCount($id, 'address'))) {
                $loadFormsMap->load = true;
                $loadFormsMap->api_key = self::getMapsKey();
            }
        }
        $loadMapJSON = json_encode($loadFormsMap);
        $params = JComponentHelper::getParams('com_baforms');
        if (!defined('UPLOADS_STORAGE')) {
            define('UPLOADS_STORAGE', $params->get('uploads_storage', 'images/baforms/uploads'));
        }
        if (!defined('PDF_STORAGE')) {
            define('PDF_STORAGE', $params->get('pdf_storage', 'images/baforms/pdf'));
        }
        $html = '';
        include JPATH_ROOT.'/components/com_baforms/views/form/tmpl/style.php';
        $html .= $out;
        $src = JUri::root().'components/com_baforms/assets/css/ba-style.css?'.self::$about->version;
        $link = "\n\t<link href=\"%s\" rel=\"stylesheet\" type=\"text/css\">";
        $html .= sprintf($link, $src);
        if (self::$fontawesome) {
            $src = JURI::root().'components/com_baforms/assets/icons/fontawesome/fontawesome.css';
            $html .= sprintf($link, $src);
        }
        if (self::$material) {
            $src = JURI::root().'components/com_baforms/assets/icons/material/material.css';
            $html .= sprintf($link, $src);
        }
        if (!empty(self::$fonts)) {
            $fontsStr = '';
            foreach (self::$fonts as $font) {
                if (!empty($fontsStr)) {
                    $fontsStr .= '%7C';
                }
                $fontsStr .= $font;
            }
            $src = '//fonts.googleapis.com/css?family='.$fontsStr;
            $html .= sprintf($link, $src);
        }
        if (self::loadJQuery($id) == 0) {
            
        } else if (!in_array('jquery.min.js', $array) && !in_array('jquery.js', $array)) {
            if (JVERSION >= '4.0.0') {
                $src = JUri::root(true).'/media/vendor/jquery/js/jquery.min.js';
            } else {
                $src = JUri::root(true).'/media/jui/js/jquery.min.js';
            }
            $html .= '<script src="'.$src.'"></script>';
        }
        $src = JUri::root().'components/com_baforms/assets/js/ba-form.js?'.self::$about->version;
        $html .= '<script src="'.$src.'"></script>';
        
        return $html;
    }

    public static function getScriptItemsCount($id, $type)
    {
    	$db = JFactory::getDbo();
    	$query = $db->getQuery(true)
    		->select('COUNT(id)')
    		->from('#__baforms_items')
            ->where('form_id = '.$id)
    		->where('type = '.$db->quote($type));
		$db->setQuery($query);
		$count = $db->loadResult();

		return $count > 0;
    }

    public static function setAppLicense()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = '{}';
        $db->updateObject('#__baforms_api', $balbooa, 'id');
    }

    public static function setAppLicenseActivation()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_api')
            ->where('service = '.$db->quote('balbooa_activation'));
        $db->setQuery($query);
        $balbooa = $db->loadObject();
        $balbooa->key = '{}';
        $db->updateObject('#__baforms_api', $balbooa, 'id');
    }

    public static function renderPrice($value, $thousand, $separator, $decimals)
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

    public static function renderPollResults($field)
    {
        self::prepareCondition('[]', 0);
        JFactory::getLanguage()->load('com_baforms', JPATH_ADMINISTRATOR);
        $renderResults = true;
        include JPATH_ROOT.'/components/com_baforms/views/form/tmpl/poll.php';

        return $out;
    }
    
    public static function drawHTMLPage($id)
    {
        self::getFormShortCodes($id);
        $pages = self::getPages($id);
        $pageCount = count($pages);
        $settings = self::getFormSettings($id);
        $navigation = json_decode($settings->navigation);
        $closedPoll = false;
        self::prepareCondition($settings->condition_logic, $id);
        self::$design = json_decode($settings->design);
        self::$design->css = $settings->css;
        self::$design->js = $settings->js;
        if (self::$design->field->typography->{'font-family'} !='inherit'
            && !in_array(self::$design->field->typography->{'font-family'}, self::$fonts)) {
            self::$fonts[] = self::$design->field->typography->{'font-family'};
        }
        if (self::$design->label->typography->{'font-family'} !='inherit'
            && !in_array(self::$design->label->typography->{'font-family'}, self::$fonts)) {
            self::$fonts[] = self::$design->label->typography->{'font-family'};
        }
        $path = JPATH_ROOT.'/components/com_baforms/views/form/tmpl/';
        JFactory::getLanguage()->load('com_baforms', JPATH_ADMINISTRATOR);
        $url = JUri::root().'index.php?option=com_baforms';
        /*$url = JRoute::_('index.php?option=com_baforms');
        if (substr($url, -1) === '/') {
            $url = substr($url, 0, -1);
        }*/
        include $path.'form.php';
        $input = JFactory::getApplication()->input;
        $option = $input->get('option', '', 'string');
        if (self::$fontawesome !== true && $option != 'com_gridbox') {
            $array = ['fa fa-', 'fab fa-', 'fal fa-', 'far fa-', 'fas fa-'];
            foreach ($array as $value) {
                if (strpos($out, $value)) {
                    self::$fontawesome = true;
                    break;
                }
            }
        }
        if (self::$material !== true && $option != 'com_gridbox') {
            self::$material = strpos($out, 'zmdi zmdi-') !== false;
        }
        
        return $out;
    }

    public static function prepareCondition($condition_logic, $id)
    {
        self::$conditionLogic = new stdClass();
        self::$conditionLogic->hidden = [];
        self::$conditionLogic->conditions = [];
        $conditions = json_decode($condition_logic);
        $types = ['radio', 'select', 'checkbox', 'selectMultiple'];
        foreach ($conditions as $condition) {
            if (!$condition->publish) {
                continue;
            }
            $flags = [];
            foreach ($condition->when as $when) {
                $field = self::getFormItem($id, $when->field);
                if (empty($field)) {
                    continue;
                }
                $options = json_decode($field->options);
                $value = '';
                if (in_array($field->type, $types)) {
                    $replaced = false;
                    foreach ($options->items as $item) {
                        if (!$replaced && $when->value == $item->key) {
                            $replaced = true;
                            $when->value = strip_tags($item->title);
                        }
                        if ($item->default) {
                            $value .= strip_tags($item->title);
                        }
                    }
                } else if ($field->type == 'input') {
                    $value = self::renderDefaultValue($options->default);
                    $value = preg_replace('/\[Field ID=+(.*?)\]/i', '', $value);
                } else if ($field->type == 'calendar') {
                    $value = $options->default == 'today' ? JHtml::date(time(), 'j F Y') : '';
                }
                switch ($when->state) {
                    case 'equal':
                        $flags[] = $value == $when->value;
                        break;
                    case 'not-equal':
                        $flags[] = $value != $when->value;
                        break;
                    case 'not-empty':
                        $flags[] = $value != '';
                        break;
                    case 'empty':
                        $flags[] = $value == '';
                        break;
                    case 'greater':
                        $flags[] = (is_numeric($value) ? $value * 1 : $value) > (is_numeric($when->value) ? $when->value * 1 : $when->value);
                        break;
                    case 'less':
                        $flags[] = (is_numeric($value) ? $value * 1 : $value) < (is_numeric($when->value) ? $when->value * 1 : $when->value);
                        break;
                    case 'contain':
                        $flags[] = (strpos($value, $when->value) !== false);
                        break;
                    case 'not-contain':
                        $flags[] = (strpos($value, $when->value) === false);
                        break;
                }
            }
            if (empty($flags)) {
                continue;
            }
            $flag = ($condition->operation == 'AND' && !in_array(false, $flags))||($condition->operation == 'OR' && in_array(true, $flags));
            foreach ($condition->do as $do) {
                if (($do->action == 'show' && !$flag) || ($do->action == 'hide' && $flag)) {
                    self::$conditionLogic->hidden[] = 'baform-'.$do->field;
                }
            }
            self::$conditionLogic->conditions[] = $condition;
        }
    }

    public static function prepareHelper()
    {
        baformshelper::$about = baformshelper::aboutUs();
        baformshelper::$state = baformshelper::checkFormsActivation();
    }

    public static function getFormItem($id, $key)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_items')
            ->where('form_id = '.$id)
            ->where('`key` = '.$db->quote('baform-'.$key))
            ->order('`column_id` ASC');
        $db->setQuery($query);
        $items = $db->loadObject();
        
        return $items;
    }

    public static function getFormColumns($key, $id)
    {
    	$db = JFactory::getDbo();
    	$query = $db->getQuery(true)
    		->select('*')
    		->from('#__baforms_columns')
    		->where('form_id = '.$id)
    		->where('`key` = '.$db->quote($key));
		$db->setQuery($query);
		$obj = $db->loadObject();

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
        $array = [$db->quote('input'), $db->quote('submit'), $db->quote('text'), $db->quote('image'), $db->quote('html')];
        if (self::$about->tag != 'pro' || !isset(self::$state->data)) {
            $query->where('type in ('.implode(',', $array).')');
        }
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        return $items;
    }

    public static function getFormSettings($id)
    {
    	$db = JFactory::getDbo();
    	$query = $db->getQuery(true)
    		->select('*')
    		->from('#__baforms_forms_settings')
    		->where('form_id = '.$id);
		$db->setQuery($query);
		$obj = $db->loadObject();

		return $obj;
    }

    public static function getPages($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__baforms_pages')
            ->where('form_id = '.$id)
            ->order('`order_index` ASC');
        $db->setQuery($query);
        $item = $db->loadObjectList();
        
        return $item;
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
        $state = json_decode($balbooa);

        return $state;
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
        $about = $db->loadResult();
        $about = json_decode($about);
        $xml = simplexml_load_file(JPATH_ROOT.'/administrator/components/com_baforms/baforms.xml');
        $about->tag = (string)$xml->tag;

        return $about;
    }
    
    public static function getFormsFooter($id)
    {
        $copyright = self::$about->tag != 'pro';
        include JPATH_ROOT.'/components/com_baforms/views/form/tmpl/footer.php';

        return $out;
    }
    
    public static function checkForm($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(id)')
            ->from('#__baforms_forms')
            ->where('id='.$id)
            ->where('published = 1');
        $db->setQuery($query);
        $count = $db->loadResult();

        return $count > 0;
    }
}