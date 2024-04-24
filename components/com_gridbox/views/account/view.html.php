<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxViewAccount extends JViewLegacy
{
    public $orders;
    public $statuses;
    public $wishlist;
    public $user;
    public $digital;
    public $subscriptions;
    public $customer;
    public $item;
    public $submitted;
    public $author;

    public function display($tpl = null)
    {

        $this->user = JFactory::getUser();
        if ($this->user->guest) {
            return gridboxHelper::raiseError(404, JText::_('NOT_FOUND'));
        }
        $input = JFactory::getApplication()->input;
        $layout = $input->get('layout', '', 'string');
        if ($layout == 'print' || $layout == 'pdf') {
            $this->item = $this->get('Item');
            foreach ($this->item->products as $product) {
                if (isset($product->extra_options->price)) {
                    $product->price -= $product->extra_options->price * $product->quantity;
                }
                if ($product->sale_price && isset($product->extra_options->price)) {
                    $product->sale_price -= $product->extra_options->price * $product->quantity;
                }
            }
        } else {
            $data = $this->get('data');
            $this->orders = $data->orders;
            $this->digital = $data->digital;
            $this->statuses = $this->get('statuses');
            $this->customer = $this->get('customerInfo');
            $this->subscriptions = $this->get('subscriptions');
            $this->submitted = $this->get('SubmittedItems');
            $this->author = $this->get('Author');
            foreach ($this->subscriptions->digital as $digital) {
                if (!empty($digital->license->expires)) {
                    $this->digital->expires++;
                }
                $this->digital->products[] = $digital;
            }
            if (gridboxHelper::$store->wishlist->login) {
                $wishlist_id = gridboxHelper::getWishlistId();
                $this->wishlist = gridboxHelper::getStoreWishlist($wishlist_id, true);
                $this->wishlist->empty = count($this->wishlist->products) == 0;
            }
            $this->prepareDocument();
        }

        parent::display($tpl);
    }

    public function prepareDocument()
    {
        $doc = JFactory::getDocument();
        $version = gridboxHelper::getVersion();
        $time = $this->get('time');
        $doc->addStyleSheet(JUri::root().'components/com_gridbox/assets/css/storage/account.css?'.$time);
        $doc->addStyleSheet(JURI::root().'components/com_gridbox/assets/css/account.css?'.$version);
        $doc->addScript(JURI::root().'components/com_gridbox/assets/js/ba-account.js?'.$version);
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $menu = $menus->getActive();
        if (isset($menu) && $menu->query['option'] == 'com_gridbox' && $menu->query['view'] == 'account') {
            $params  = $menus->getParams($menu->id);
            $title = $params->get('page_title');
            $title = !empty($title) ? $title : $menu->title;
            $desc = $params->get('menu-meta_description');
            $keywords = $params->get('menu-meta_keywords');
            $robots = $params->get('robots');
        } else {
            $title = '';
            $desc = '';
            $keywords = '';
            $robots = '';
        }
        if ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } else if ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }
        $doc->setTitle($title);
        $doc->setDescription($desc);
        $doc->setMetaData('keywords', $keywords);
        if (empty($robots)) {
            $config = JFactory::getConfig();
            $robots = $config->get('robots');
        }
        if ($robots) {
            $doc->setMetadata('robots', $robots);
        }
    }

    public function preparePrice($price, $symbol = null, $position = null)
    {
        if ($symbol == null) {
            $symbol = gridboxHelper::$store->currency->symbol;
            $position = gridboxHelper::$store->currency->position;
        }
        $decimals = gridboxHelper::$store->currency->decimals;
        $separator = gridboxHelper::$store->currency->separator;
        $thousand = gridboxHelper::$store->currency->thousand;
        $price = round($price * 1, $decimals);
        $price = number_format($price, $decimals, $separator, $thousand);
        if ($position == '') {
            $value = $symbol.' '.$price;
        } else {
            $value = $price.' '.$symbol;
        }

        return $value;
    }
}