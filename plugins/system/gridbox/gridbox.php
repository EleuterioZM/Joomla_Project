<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Profiler\Profiler;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
 
class plgSystemGridbox extends CMSPlugin
{
    public $cache;
    public $performance;
    public $dispatcher;

    public function checkUserSubscription()
    {
        $user = Factory::getUser();
        if (empty($user->id)) {
            return;
        }
        $db = Factory::getDbo();
        $date = $db->quote(date("Y-m-d H:i:s"));
        $nullDate = $db->quote($db->getNullDate());
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__gridbox_store_subscriptions')
            ->where('user_id = '.$user->id);
        $db->setQuery($query);
        $subscriptions = $db->loadObjectList();
        if (count($subscriptions) == 0) {
            return;
        }
        $addGroups = [];
        $removeGroups = [];
        foreach ($subscriptions as $subscription) {
            $user_groups = json_decode($subscription->user_groups);
            $status = empty($subscription->expires) || date('Y-m-d H:i:s') < $subscription->expires;
            $query = $db->getQuery(true)
                ->select('d.subscription')
                ->from('#__gridbox_pages AS p')
                ->leftJoin('#__gridbox_store_product_data AS d ON p.id = d.product_id')
                ->where('p.id = '.$subscription->product_id)
                ->where('p.page_category <> '.$db->quote('trashed'))
                ->where('p.published = 1')
                ->where('p.created <= '.$date)
                ->where('(p.end_publishing = '.$nullDate.' OR p.end_publishing >= '.$date.')');
            $db->setQuery($query);
            $page = $db->loadObject();
            if (!$page) {
                $status = false;
            } else {
                $obj = json_decode($page->subscription);
                $update = false;
                $remove = [];
                foreach ($user_groups as $group) {
                    if (!in_array($group, $obj->groups) ||
                        ($obj->action == 'products' && $subscription->action != 'products')) {
                        $update = true;
                        $remove[] = $group;
                    }
                }
                foreach ($obj->groups as $group) {
                    if (!in_array($group, $user_groups) && $obj->action != 'products') {
                        $update = true;
                        break;
                    }
                }
                foreach ($remove as $group) {
                    UserHelper::removeUserFromGroup($user->id, $group);
                }
                if ($update) {
                    $user_groups = $obj->groups;
                    $subscription->user_groups = json_encode($user_groups);
                    $subscription->action = $obj->action;
                    $db->updateObject('#__gridbox_store_subscriptions', $subscription, 'id');
                }
            }
            if ($subscription->action == 'products') {
                continue;
            }

            
            $removeGroups = [];
            foreach ($user_groups as $group) {
                if ($status) {
                    $addGroups[] = (int)$group;
                } else {
                    $removeGroups[] = (int)$group;
                }
            }
        }
        foreach ($addGroups as $group) {
            if (!isset($user->groups[$group])) {
                UserHelper::addUserToGroup($user->id, $group);
            }
        }
        foreach ($removeGroups as $group) {
            if (isset($user->groups[$group]) && !in_array($group, $addGroups)) {
                UserHelper::removeUserFromGroup($user->id, $group);
            }
        }
    }

    public function __construct(&$subject, $config)
    {
        $this->dispatcher = $subject;
        parent::__construct($subject, $config);
        $app = Factory::getApplication();
        if ($app->isClient('site')) {
            $this->checkUserSubscription();
            $path = JPATH_ROOT.'/components/com_gridbox/helpers/gridbox.php';
            JLoader::register('gridboxHelper', $path);
            $this->performance = gridboxHelper::getPerformance();
            if ($this->performance->page_cache == 1) {
                $options = [
                    'defaultgroup' => 'gridbox',
                    'browsercache' => $this->performance->browser_cache,
                    'caching' => false,
                ];
                $this->cache = Cache::getInstance('page', $options);
            }
        }
    }

    protected function getCacheKey()
    {
        static $key;

        if (!$key) {
            $parts[] = Uri::getInstance()->toString();
            $key = md5(serialize($parts));
        }

        return $key;
    }

    public function onUserAfterLogin($options)
    {
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) {
            setcookie('gridbox_username', $options['user']->username, 0, '/');
        }
    }

    public function onAfterInitialise()
    {
        $app = Factory::getApplication();
        if ($app->isClient('site') && JVERSION < '4.0.0') {
            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->detach($this);
            $dispatcher->attach($this);
        }
    }

    public function onAfterRoute()
    {
        $app = Factory::getApplication();
        $doc = Factory::getDocument();
        $pageTitle = $doc->getTitle();
        $view = $app->input->getCmd('view', '');
        $option = $app->input->getCmd('option', '');
        $task = $app->input->getCmd('task', '');
        $id = $app->input->get('id', 0, 'int');
        $user = Factory::getUser();
        if ($app->isClient('site')) {
            gridboxHelper::checkURI();
            gridboxHelper::checkGridboxLoginData();
            if ($doc->getType() == 'html' && $option == 'com_gridbox' && $view == 'system') {
                $params = gridboxHelper::getSystemParams($id);
            }
            /*
            if ($doc->getType() == 'html' && $option == 'com_gridbox' && $view == 'system' && $params
                && $params->type == 'thank-you-page') {
                gridboxHelper::setOrder();
            }
            */
            if ($doc->getType() == 'html' && strpos($pageTitle, 'Gridbox Editor') === false && $view != 'gridbox'
                && $this->performance->page_cache == 1 && $user->get('guest')) {
                PluginHelper::importPlugin('pagecache');
                $results = $app->triggerEvent('onPageCacheSetCaching');
                $caching = !in_array(false, $results, true);
                if ($caching && $app->input->getMethod() == 'GET') {
                    $this->cache->setCaching(true);
                }
                $data = $this->cache->get($this->getCacheKey(), 'gridbox');
                if ($data !== false) {
                    $app->setBody($data);
                    echo $app->toString();
                    if (JDEBUG) {
                        Profiler::getInstance('Application')->mark('afterCache');
                    }
                    $app->close();
                }
            }
            if ($option == 'com_gridbox' && empty($task)) {
                $app->setTemplate('gridbox');
            }
        }
    }

    public function onBeforeRender()
    {
        $app = Factory::getApplication();
        if ($app->isClient('site') && JVERSION < '4.0.0') {
            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->detach($this);
            $dispatcher->attach($this);
        }
    }

    public function onAfterRespond()
    {
        $app = Factory::getApplication();
        $doc = Factory::getDocument();
        $pageTitle = $doc->getTitle();
        $view = $app->input->get('view');
        if ($app->isClient('site') && $doc->getType() == 'html'
            && strpos($pageTitle, 'Gridbox Editor') === false && $view != 'gridbox') {
            $user = Factory::getUser();
            $body = $app->getBody();
            if ($this->performance->page_cache == 1 && $user->get('guest')) {
                $data = $app->toString();
                $data = str_replace('<html ', '<html data-cached="true" ', $data);
                $this->cache->store($data, $this->getCacheKey(), 'gridbox');
            }
        }
    }

    public function onAfterRender()
    {
        $app = Factory::getApplication();
        $doc = Factory::getDocument();
        $view = $app->input->get('view');
        $option = $app->input->get('option');
        $pageTitle = $doc->getTitle();
        if ($app->isClient('administrator') && $doc->getType() == 'html' && $option == 'com_gridbox') {
            $sidebar = $app->input->cookie->get('gridbox-sidebar', '', 'string');
            if ($sidebar == 'visible') {
                $html = $app->getBody();
                $html = str_replace('<body', '<body data-sidebar="visible"', $html);
                $app->setBody($html);
            }
        }
        if ($app->isClient('site') && $doc->getType() == 'html') {
            $app->triggerEvent('onBeforeRenderGridbox');
            $html = $app->getBody();
            $str = gridboxHelper::checkMeta();
            $html = str_replace('</head>', $str.'</head>', $html);
            $app->setBody($html);
        } else if ($app->isClient('administrator') && $doc->getType() == 'html' && JVERSION >= '4.0.0') {
            $html = $app->getBody();
            $html = str_replace('<body', "<body data-joomla-version='4'", $html);
            $app->setBody($html);
        }
        if ($app->isClient('site') && $doc->getType() == 'html' && strpos($pageTitle, 'Gridbox Editor') === false
            && $view != 'gridbox' && $this->performance->email_encryption == 1 && $option != 'com_users') {
            $html = $app->getBody();
            $pos = strpos($html, '</head>');
            $head = substr($html, 0, $pos);
            $body = substr($html, $pos);
            if (strpos($body, '@') !== false) {
                $body = $this->EncryptEmails($body);
                $html = $head.$body;
                $app->setBody($html);
            }
        }
        if ($app->isClient('site') && $doc->getType() == 'html'
            && strpos($pageTitle, 'Gridbox Editor') === false
            && $option == 'com_gridbox' && $view == 'page') {
            $body = $app->getBody();
            if (strpos($body, 'ba-item-star-rating') || strpos($body, 'ba-item-reviews')) {
                $body = gridboxHelper::setMicrodata($body);
                $app->setBody($body);
            }
        } else if (JVERSION >= '4.0.0' && $app->isClient('site') && $doc->getType() == 'html'
            && $option == 'com_finder' && $view == 'search') {
            $body = $app->getBody();
            $body = gridboxHelper::fixSmartSearch($body);
            $app->setBody($body);
        }
        if ($app->isClient('site') && $doc->getType() == 'html'
            && strpos($pageTitle, 'Gridbox Editor') === false && $view != 'gridbox') {
            $body = $app->getBody();
            $icons = gridboxHelper::checkIconsLibrary($body);
            if (!empty($icons)) {
                $body = str_replace('</head>', $icons.'</head>', $body);
            }
            $str = gridboxHelper::initItems($body);
            if (!empty($str)) {
                $body = str_replace('</head>', $str."</head>", $body);
            }
            if (!empty($icons) || !empty($str)) {
                $app->setBody($body);
            }
        }
        if ($app->isClient('site') && $doc->getType() == 'html'
            && strpos($pageTitle, 'Gridbox Editor') === false && $view != 'gridbox') {
            if ($option == 'com_gridbox' && ($this->performance->compress_css == 1 || $this->performance->compress_js == 1
                    || $this->performance->compress_html == 1 || $this->performance->compress_images == 1
                    || $this->performance->adaptive_images == 1 || $this->performance->defer_loading == 1
                    || $this->performance->images_lazy_load == 1 || $this->performance->enable_canonical == 1)) {
                $body = $app->getBody();
                $body = gridboxHelper::compressGridbox($body);
                $app->setBody($body);
            }
        }
        if ($app->isClient('site') && $doc->getType() == 'html'
            && strpos($pageTitle, 'Gridbox Editor') === false && $view != 'gridbox') {
            $body = $app->getBody();
            $body = str_replace(' type="text/javascript"', '', $body);
            $app->setBody($body);
        }
        $app->triggerEvent('onAfterRenderGridbox');
        if ($app->isClient('site') && $doc->getType() == 'html') {
            $body = $app->getBody();
            $css = gridboxHelper::loadUsedCSS($body, $view, $option);
            $body = str_replace('[gridbox-plugins-css]', $css, $body);
            $app->setBody($body);
        }
    }

    public function getPattern($link, $html)
    {
        $pattern = '~(?:<a ([^>]*)href\s*=\s*"mailto:'.$link.'"([^>]*))>'.$html.'</a>~i';

        return $pattern;
    }

    public function addEmailAttributes($email, $before, $after)
    {
        if ($before !== '') {
            $before = str_replace("'", "\'", $before);
            $email = str_replace(".innerHTML += '<a '", ".innerHTML += '<a {$before}'", $email);
        }
        if ($after !== '') {
            $after = str_replace("'", "\'", $after);
            $email = str_replace("'\'>'", "'\'{$after}>'", $email);
        }

        return $email;
    }

    protected function convertEncoding($text)
    {
        $text = html_entity_decode($text);
        $text = str_replace('a', '&#97;', $text);
        $text = str_replace('e', '&#101;', $text);
        $text = str_replace('i', '&#105;', $text);
        $text = str_replace('o', '&#111;', $text);
        $text = str_replace('u', '&#117;', $text);
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8', false);

        return $text;
    }

    protected function cloak($mail, $mailto = true, $text = '', $email = true)
    {
        if ($mailto && (empty($text) || $email)) {
            $text = PunycodeHelper::emailToUTF8($text ?: $mail);
        } else if (!$mailto) {
            $mail = PunycodeHelper::emailToUTF8($mail);
        }
        $mail = $this->convertEncoding($mail);
        $rand = md5($mail . mt_rand(1, 100000));
        $mail = explode('@', $mail);
        $mail_parts = explode('.', $mail[1]);
        if ($mailto) {
            if ($text) {
                $text = $this->convertEncoding($text);
                if ($email) {
                    $text = explode('@', $text);
                    $text_parts = explode('.', $text[1]);
                    $tmpScript = "var addy_text".$rand." = '".$text[0]."' + '&#64;' + '"
                        .implode("' + '&#46;' + '", $text_parts). "';";
                } else {
                    $tmpScript = "var addy_text".$rand." = '".$text."';";
                }
                $tmpScript .= "document.getElementById('cloak".$rand
                    ."').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy".$rand." + '\'>'+addy_text".$rand."+'<\/a>';";
            } else {
                $tmpScript = "document.getElementById('cloak".$rand
                    ."').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy".$rand." + '\'>' +addy".$rand."+'<\/a>';";
            }
        } else {
            $tmpScript = "document.getElementById('cloak".$rand."').innerHTML += addy".$rand.";";
        }
        $script = "
                document.getElementById('cloak".$rand."').innerHTML = '';
                var prefix = '&#109;a' + 'i&#108;' + '&#116;o';
                var path = 'hr' + 'ef' + '=';
                var addy".$rand." = '".$mail[0]."' + '&#64;';
                addy".$rand." = addy".$rand." + '".implode("' + '&#46;' + '", $mail_parts)."';
                $tmpScript
        ";
        $inlineScript = "<script type='text/javascript'>".$script."</script>";

        return '<span id="cloak'.$rand.'">'.Text::_('JLIB_HTML_CLOAKING').'</span>'.$inlineScript;
    }

    public function EncryptEmails($html)
    {
        $regEmail = '([\w\.\'\-\+]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-zA-Z0-9\-]{2,10}))';
        $regEmailLink = $regEmail.'([?&][\x20-\x7f][^"<>]+)';
        $regText = '((?:[\x20-\x7f]|[\xA1-\xFF]|[\xC2-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF4][\x80-\xBF]{3})[^<>]+)';
        $regImage = '(<img[^>]+>)';
        $regTextSpan = '(<span[^>]+>|<span>|<strong>|<strong><span[^>]+>|<strong><span>)'.$regText.'(</span>|</strong>|</span></strong>)';
        $regEmailSpan = '(<span[^>]+>|<span>|<strong>|<strong><span[^>]+>|<strong><span>)'.$regEmail.'(</span>|</strong>|</span></strong>)';
        $pattern = $this->getPattern($regEmail, $regEmail);
        $pattern = str_replace('"mailto:', '"http://mce_host([\x20-\x7f][^<>]+/)', $pattern);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[3][0];
            $emailText = $matches[5][0];
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = $this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]);
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regText);
        $pattern = str_replace('"mailto:', '"http://mce_host([\x20-\x7f][^<>]+/)', $pattern);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[3][0];
            $emailText = $matches[5][0];
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = $this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]);
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regEmail);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = $matches[4][0];
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = $this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]);
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regEmailSpan);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = $matches[4][0] . $matches[5][0] . $matches[6][0];
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regTextSpan);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = $matches[4][0] . addslashes($matches[5][0]) . $matches[6][0];
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regText);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = addslashes($matches[4][0]);
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = $this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]);
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regImage);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = $matches[4][0];
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regImage.$regEmail);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = $matches[4][0] . $matches[5][0];
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmail, $regImage.$regText);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0];
            $emailText = $matches[4][0] . addslashes($matches[5][0]);
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[3][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regEmail);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0] . $matches[3][0];
            $emailText = $matches[5][0];
            $email = str_replace('&amp;', '&', $email);
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = $this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]);
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regText);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0] . $matches[3][0];
            $emailText = addslashes($matches[5][0]);
            $email = str_replace('&amp;', '&', $email);
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = $this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]);
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regEmailSpan);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0] . $matches[3][0];
            $emailText = $matches[5][0] . $matches[6][0] . $matches[7][0];
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regTextSpan);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[2][0] . $matches[3][0];
            $emailText = $matches[5][0] . addslashes($matches[6][0]) . $matches[7][0];
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regImage);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[1][0] . $matches[2][0] . $matches[3][0];
            $emailText = $matches[5][0];
            $email = str_replace('&amp;', '&', $email);
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regImage.$regEmail);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[1][0] . $matches[2][0] . $matches[3][0];
            $emailText = $matches[4][0] . $matches[5][0] . $matches[6][0];
            $email = str_replace('&amp;', '&', $email);
            $replacement = $this->cloak($email, true, $emailText);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = $this->getPattern($regEmailLink, $regImage . $regText);
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[1][0] . $matches[2][0] . $matches[3][0];
            $emailText = $matches[4][0] . $matches[5][0] . addslashes($matches[6][0]);
            $email = str_replace('&amp;', '&', $email);
            $replacement = $this->cloak($email, true, $emailText, 0);
            $replacement = html_entity_decode($this->addEmailAttributes($replacement, $matches[1][0], $matches[4][0]));
            $html = substr_replace($html, $replacement, $matches[0][1], strlen($matches[0][0]));
        }
        $pattern = '~(?![^<>]*>)'.$regEmail.'~i';
        while (preg_match($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            $email = $matches[1][0];
            $replacement = $this->cloak($email, false);
            $html = substr_replace($html, $replacement, $matches[1][1], strlen($email));
        }

        return $html;
    }
}