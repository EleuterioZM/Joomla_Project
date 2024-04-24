<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/


class fieldsGenerator
{
    public $jce;
    public $jceIndex;
    public $form;
    public $pageTags;
    public $tags;
    public $desktopFiles;
    public $type;
    public $name;
    protected $bookingSettings;

    public function __construct($jce, $form, $pageTags, $tags, $type, $name = 'editor')
    {
        $this->jce = !empty($jce) && $jce * 1 === 1;
        $this->jceIndex = 1;
        $this->form = $form;
        $this->pageTags = $pageTags;
        $this->tags = $tags;
        $this->desktopFiles = gridboxHelper::getDesktopFieldFiles();
        $this->type = $type;
        $this->name = $name;
    }

    public function getGroupsHTML($fields, $groups, $fields_data, $productData)
    {
        $object = json_decode($groups);
        $array = [];
        $lastKey = '';
        $html = '';
        $data = new stdClass();
        $productsGroups = ['ba-group-product-pricing', 'ba-group-product-variations',
            'ba-group-related-product', 'ba-group-product-booking',
            'ba-group-digital-product', 'ba-group-subscription-product',
            'ba-group-subscription-renewal'
        ];
        
        if ($this->type == 'booking') {
            $object->{'ba-group-product-booking'} = new stdClass();
            $object->{'ba-group-product-booking'}->title = 'Booking Settings';
            $object->{'ba-group-product-booking'}->fields = [];
        }
        
        if ($this->type == 'products' && !isset($object->{'ba-group-product-pricing'})) {
            $object->{'ba-group-product-pricing'} = new stdClass();
            $object->{'ba-group-product-pricing'}->title = 'Pricing and Inventory';
            $object->{'ba-group-product-pricing'}->fields = [];
            $object->{'ba-group-product-variations'} = new stdClass();
            $object->{'ba-group-product-variations'}->title = 'Options and Variations';
            $object->{'ba-group-product-variations'}->fields = [];
        } else if ($this->type == 'booking' && !isset($object->{'ba-group-product-pricing'})) {
            $object->{'ba-group-product-pricing'} = new stdClass();
            $object->{'ba-group-product-pricing'}->title = 'Pricing';
            $object->{'ba-group-product-pricing'}->fields = [];
        } else if ($this->type == 'booking' && !isset($object->{'ba-group-product-variations'})) {
            $object->{'ba-group-product-variations'} = new stdClass();
            $object->{'ba-group-product-variations'}->title = 'Options';
            $object->{'ba-group-product-variations'}->fields = [];
        }
        if ($this->type == 'products' && !isset($object->{'ba-group-related-product'})) {
            $object->{'ba-group-related-product'} = new stdClass();
            $object->{'ba-group-related-product'}->title = 'Related Products';
            $object->{'ba-group-related-product'}->fields = [];
        }
        if ($this->type == 'products' && !isset($object->{'ba-group-digital-product'})) {
            $object->{'ba-group-digital-product'} = new stdClass();
            $object->{'ba-group-digital-product'}->title = 'Add Digital Product';
            $object->{'ba-group-digital-product'}->fields = [];
        }
        if ($this->type == 'products' && !isset($object->{'ba-group-subscription-product'})) {
            $object->{'ba-group-subscription-product'} = new stdClass();
            $object->{'ba-group-subscription-product'}->title = 'Subscription Settings';
            $object->{'ba-group-subscription-product'}->fields = [];
        }
        if ($this->type == 'products' && !isset($object->{'ba-group-subscription-renewal'})) {
            $object->{'ba-group-subscription-renewal'} = new stdClass();
            $object->{'ba-group-subscription-renewal'}->title = 'Renewal and Upgrage';
            $object->{'ba-group-subscription-renewal'}->fields = [];
        }
        if ($productData && !$productData->relatedFlag) {
            unset($object->{'ba-group-related-product'});
        }
        foreach ($fields as $value) {
            $data->{$value->field_key} = $value;
        }
        foreach ($object as $key => $group) {
            if (!in_array($key, $productsGroups)) {
                $lastKey = $key;
            }
            $group->str = '';
            if ($key == 'ba-group-product-booking') {
                $group->str = $this->getProductBooking($productData);
            } else if ($key == 'ba-group-product-pricing') {
                $group->str = $this->getProductPricingFields($productData);
            } else if ($key == 'ba-group-product-variations') {
                $group->str = $this->getProductVariations($productData);
            } else if ($key == 'ba-group-related-product') {
                $group->str = $this->getProductRelated($productData);
            } else if ($key == 'ba-group-digital-product') {
                $group->str = $this->getGigitalProduct($productData);
            } else if ($key == 'ba-group-subscription-product') {
                $group->str = $this->getSubscriptionProduct($productData);
            } else if ($key == 'ba-group-subscription-renewal') {
                $group->str = $this->getSubscriptionRenewal($productData);
            }
            foreach ($group->fields as $id) {
                if (!isset($data->{$id})) {
                    continue;
                }
                $value = $data->{$id};
                $group->str .= $this->getFieldHTML($fields_data, $value);
                if ($value->field_type == 'textarea') {
                    $options = json_decode($value->options);
                    if ($options->texteditor) {
                        $group->texteditor = true;
                    }
                }
                $array[] = $value->id;
            }
        }

        foreach ($fields as $value) {
            if (!in_array($value->id, $array)) {
                if ($value->field_type == 'textarea') {
                    $options = json_decode($value->options);
                    if ($options->texteditor) {
                        $object->{$lastKey}->texteditor = true;
                    }
                }
                $object->{$lastKey}->str .= $this->getFieldHTML($fields_data, $value);
            }
        }
        foreach ($object as $key => $group) {
            if (empty($group->str)) {
                continue;
            }
            $html .= '<div class="ba-fields-group-wrapper';
            if (($key == 'ba-group-product-variations' || $key == 'ba-group-product-pricing')
                && $productData->data->product_type == 'digital') {
                $html .= ' digital-product-type';
            } else if (($key == 'ba-group-product-variations' || $key == 'ba-group-product-pricing')
                && $productData->data->product_type == 'subscription') {
                $html .= ' subscription-product-type';
            } else if ($key == 'ba-group-digital-product' && $productData->data->product_type != 'digital') {
                $html .= ' physical-product-type';
            } else if (($key == 'ba-group-subscription-product' || $key == 'ba-group-subscription-renewal')
                && $productData->data->product_type != 'subscription') {
                $html .= ' physical-product-type';
            }
            $html .= '" id="'.$key;
            $html .= '"><div class="ba-fields-group-title"><input type="text" placeholder="';
            $html .= JText::_('NEW_GROUP').'" value="'.$group->title;
            $html .= '"><div class="ba-fields-group-icons">';
            if (!in_array($key, $productsGroups)) {
                $html .= '<i class="zmdi zmdi-delete"></i>';
            }
            if (!isset($group->texteditor)) {
                $html .= '<i class="zmdi zmdi-apps"></i>';
            }
            $html .= '</div></div><div class="ba-fields-group"';
            if ($key == 'ba-group-product-pricing' || $key == 'ba-group-product-variations') {
                $html .= ' data-disable-sorting="disable"';
            }
            $html .= '>';
            $html .= $group->str;
            $html .= '</div></div>';
        }

        return $html;
    }

    protected function getSubscriptionPage($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('title, intro_image AS image')
            ->from('#__gridbox_pages')
            ->where('id = '.$id);
        $db->setQuery($query);
        $page = $db->loadObject();

        return $page;
    }

    protected function getSubscriptionProduct($product)
    {
        $subscription = !empty($product->data->subscription) ? json_decode($product->data->subscription) : new stdClass();
        $expires = array('h' => JText::_('HOURS'), 'd' => JText::_('DAYS'),
            'm' => JText::_('MONTHS'), 'y' => JText::_('YEARS'));
        $value = isset($subscription->length) ? $subscription->length->value : '';
        $format = isset($subscription->length) ? $subscription->length->format : 'h';
        $html = $this->getHTMLHeader('0', 'subscription-length', '0', false, JText::_('SUBSCRIPTION_LENGTH'), true);
        $html .= '<input type="number" value="'.$value.'">';
        $html .= '<select>';
        foreach ($expires as $key => $value) {
            $html .= '<option value="'.$key.'"'.($format == $key ? ' selected' : '').'>'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '</div></div>';
        $action = isset($subscription->action) ? $subscription->action : 'products';
        $array = array('products' => JText::_('ACCESS_TO_DIGITAL_PRODUCTS'),
            'groups' => JText::_('ACCESS_TO_JOOMLA_USER_GROUPS'),
            'full' => JText::_('ACCESS_TO_DIGITAL_PRODUCTS_USER_GROUPS'));
        $html .= $this->getHTMLHeader('0', 'subscription-action', '0', false, JText::_('SUBSCRIPTION_ACTION'), true);
        $html .= '<select>';
        foreach ($array as $key => $value) {
            $html .= '<option value="'.$key.'"'.($action == $key ? ' selected' : '').'>'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '</div></div>';
        $html .= $this->getHTMLHeader('0', 'subscription-products', '0', false, JText::_('PRODUCTS'), true);
        $html .= '<div class="field-sorting-wrapper subscription-products"><div class="sorting-container">';
        $html .= '<div class="sorting-item"><div class="subscription-products-title-wrapper">';
        $html .= '<i class="zmdi zmdi-plus"></i></div>';
        $html .= '<div class="selected-items-wrapper" style="--placeholder-text:\''.JText::_('ADD_NEW_ITEM').'\';">';
        $products = isset($subscription->action) ? $subscription->products : [];
        foreach ($products as $id) {
            $page = $this->getSubscriptionPage($id);
            if (!$page) {
                continue;
            }
            $img = $page->image;
            $html .= '<span class="selected-items" data-id="'.$id.'">';
            $html .= '<span class="ba-item-thumbnail"'.(!empty($img) ? ' style="background-image:url('.$img.')"' : '').'>';
            if (empty($img)) {
                $html .= '<i class="zmdi zmdi-label"></i>';
            }
            $html .= '</span><span class="selected-items-name">'.$page->title;
            $html .= '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
            $html .= '<span class="grid-sorting-handle"></span></span>';
        }
        $html .= '</div></div>';
        $html .= '</div></div></div></div>';
        $checked = isset($subscription->remove) ? $subscription->remove : false;
        $html .= $this->getHTMLHeader('0', 'subscription-remove', '0', false, '', true);
        $html .= '<div class="ba-checkbox-wrapper"><span>'.JText::_('REMOVE_PRODUCTS_FROM_STOREFRONT');
        $html .= '</span><label class="ba-checkbox">';
        $html .= '<input type="checkbox"'.($checked ? ' checked' : '').'>';
        $html .= '<span></span></label></div>';
        $html .= '</div></div>';
        $html .= $this->getHTMLHeader('0', 'subscription-groups', '0', false, JText::_('USER_GROUPS'), true);
        $html .= '<div class="field-sorting-wrapper subscription-groups"><div class="sorting-container">';
        $html .= '<div class="sorting-item"><div class="subscription-groups-title-wrapper">';
        $html .= '<i class="zmdi zmdi-plus"></i></div>';
        $html .= '<div class="selected-items-wrapper" style="--placeholder-text:\''.JText::_('ADD_NEW_ITEM').'\';">';
        $groups = isset($subscription->action) ? $subscription->groups : [];
        foreach ($groups as $id) {
            $group = gridboxHelper::getUserGroups($id);
            if (!$group) {
                continue;
            }
            $html .= '<span class="selected-items" data-id="'.$id.'">';
            $html .= '<span class="ba-item-thumbnail">';
            $html .= '<i class="zmdi zmdi-account-circle"></i>';
            $html .= '</span><span class="selected-items-name">'.$group->title;
            $html .= '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
            $html .= '<span class="grid-sorting-handle"></span></span>';
        }
        $html .= '</div></div>';
        $html .= '</div></div></div></div>';

        return $html;
    }

    protected function getRenewalPlan($expires, $key = '', $value = '', $format = 'm', $price = '')
    {
        $html = '<div class="renewal-plan" data-key="'.$key.'">';
        $html .= '<div class="renewal-plan-length"><input type="number" value="'.$value;
        $html .= '" placeholder="'.JText::_('RENEWAL_LENGTH').'">';
        $html .= '<select>';
        foreach ($expires as $ind => $value) {
            $html .= '<option value="'.$ind.'"'.($ind == $format ? ' selected' : '').'>'.$value.'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="renewal-plan-price" data-field-type="price">';
        $html .= '<div class="field-editor-price-wrapper '.gridboxHelper::$store->currency->position;
        $html .= '"><span class="field-editor-price-currency">'.gridboxHelper::$store->currency->symbol;
        $html .= '</span><input type="text" value="'.$price.'" data-decimals="10"></div>';
        $html .= '</div>';
        $html .= '<div class="renewal-plan-icons"><span class="sorting-handle"><i class="zmdi zmdi-apps"></i></span>';
        $html .='<span class="delete-renewal-plan"><i class="zmdi zmdi-delete"></i></span></div>';
        $html .= '</div>';

        return $html;
    }

    protected function getSubscriptionRenewal($product)
    {
        $subscription = !empty($product->data->subscription) ? json_decode($product->data->subscription) : new stdClass();
        $plans = isset($subscription->renew) ? $subscription->renew->plans : new stdClass();
        $expires = array('h' => JText::_('HOURS'), 'd' => JText::_('DAYS'),
            'm' => JText::_('MONTHS'), 'y' => JText::_('YEARS'));
        $html = $this->getHTMLHeader('0', 'subscription-renewal-plans', '0', false, JText::_('RENEWAL_PLANS'), true);
        $html .= '<div class="field-sorting-wrapper renewal-plans-wrapper"><div class="sorting-container">';
        $html .= '<div class="renewal-plans">';
        foreach ($plans as $key => $plan) {
            $html .= $this->getRenewalPlan($expires, $key, $plan->length->value, $plan->length->format, $plan->price);
        }
        $html .= '</div>';
        $html .= '<template class="renewal-plan-template">';
        $html .= $this->getRenewalPlan($expires);
        $html .= '</template>';
        $html .= '<div class="sorting-item add-new-renewal-plan"><div class="subscription-renewal-plans-title-wrapper">';
        $html .= '<i class="zmdi zmdi-plus"></i></div>';
        $html .= '<div class="selected-items-wrapper" style="--placeholder-text:\''.JText::_('ADD_NEW_ITEM').'\';">';
        $html .= '</div></div>';
        $html .= '</div></div></div></div>';
        $value = isset($subscription->renew) ? $subscription->renew->remind->value : '';
        $format = isset($subscription->renew) ? $subscription->renew->remind->format : 'h';
        $html .= $this->getHTMLHeader('0', 'subscription-renewal-remind', '0', false, JText::_('SEND_RENEWAL_REMINDER'),true);
        $html .= '<input type="number" value="'.$value.'">';
        $html .= '<select>';
        foreach ($expires as $key => $value) {
            $html .= '<option value="'.$key.'"'.($format == $key ? ' selected' : '').'>'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '</div></div>';

        $html .= $this->getHTMLHeader('0', 'upgrade-plans', '0', false, JText::_('UPGRADE_PLANS'), true);
        $html .= '<div class="field-sorting-wrapper upgrade-plans"><div class="sorting-container">';
        $html .= '<div class="sorting-item"><div class="upgrade-plans-title-wrapper">';
        $html .= '<i class="zmdi zmdi-plus"></i></div>';
        $html .= '<div class="selected-items-wrapper" style="--placeholder-text:\''.JText::_('ADD_NEW_ITEM').'\';">';
        $products = isset($subscription->upgrade) ? $subscription->upgrade : [];
        foreach ($products as $id) {
            $page = $this->getSubscriptionPage($id);
            if (!$page) {
                continue;
            }
            $img = $page->image;
            $html .= '<span class="selected-items" data-id="'.$id.'">';
            $html .= '<span class="ba-item-thumbnail"'.(!empty($img) ? ' style="background-image:url('.$img.')"' : '').'>';
            if (empty($img)) {
                $html .= '<i class="zmdi zmdi-label"></i>';
            }
            $html .= '</span><span class="selected-items-name">'.$page->title;
            $html .= '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
            $html .= '<span class="grid-sorting-handle"></span></span>';
        }
        $html .= '</div></div>';
        $html .= '</div></div></div></div>';

        return $html;
    }

    protected function getGigitalProduct($product)
    {
        $digital = !empty($product->data->digital_file) ? json_decode($product->data->digital_file) : new stdClass();
        $filename = isset($digital->file) ? $digital->file->filename : '';
        $name = isset($digital->file) ? $digital->file->name : '';
        $value = isset($digital->file) ? $digital->expires->value : '';
        $format = isset($digital->file) ? $digital->expires->format : '';
        $max = isset($digital->file) ? $digital->max : '';
        $required = $product->data->product_type == 'digital';
        if (isset($digital->file) && isset($digital->file->type)) {
            $type = $digital->file->type;
        } else if (isset($digital->file) && !empty($filename)) {
            $type = 'upload';
        } else {
            $type = 'link';
        }
        $readonly = $type == 'upload' ? 'readonly="" onfocus="this.blur()"' : '';
        $html = $this->getHTMLHeader('0', 'digital-product-file', '0', $required, JText::_('PRODUCT_FILE'), true);
        $html .= '<div><input type="text" '.$readonly.' data-value="'.$filename.'" value="'.$name;
        $html .= '" data-type="'.$type.'" placeholder="'.JText::_('LINK');
        $html .= '"><span class="trigger-upload-digital-file"><i class="zmdi zmdi-attachment-alt"></i>';
        $html .= '<span class="ba-tooltip">'.JText::_('UPLOAD_PRODUCT_FILE').'</span></span>';
        $html .= '<div class="reset disabled-reset reset-digital-file"><i class="zmdi zmdi-close"></i></div></div>';
        $html .= '</div></div>';
        $expires = array('h' => JText::_('HOURS'), 'd' => JText::_('DAYS'),
            'm' => JText::_('MONTHS'), 'y' => JText::_('YEARS'));
        $html .= $this->getHTMLHeader('0', 'digital-link-expires', '0', false, JText::_('DOWNLOAD_LINK_EXPIRES'), true);
        $html .= '<input type="number" value="'.$value.'">';
        $html .= '<select>';
        foreach ($expires as $key => $value) {
            $html .= '<option value="'.$key.'"'.($format == $key ? ' selected' : '').'>'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '</div></div>';
        $html .= $this->getHTMLHeader('0', 'digital-max-downloads', '0', false, JText::_('MAXIMUM_DOWNLOADS'), true);
        $html .= '<input type="number" value="'.$max.'">';
        $html .= '</div></div>';

        return $html;
    }

    protected function getProductRelated($product)
    {
        $html = $this->getHTMLHeader('0', 'related-product', '0', false, JText::_('PRODUCTS'), true);
        $html .= '<div class="field-sorting-wrapper related-product"><div class="sorting-container">';
        $html .= '<div class="sorting-item"><div class="related-product-title-wrapper"><i class="zmdi zmdi-plus"></i></div>';
        $html .= '<div class="selected-items-wrapper" style="--placeholder-text:\''.JText::_('ADD_NEW_ITEM').'\';">';
        foreach ($product->related as $related) {
            $img = $related->image;
            $html .= '<span class="selected-items" data-id="'.$related->id.'">';
            $html .= '<span class="ba-item-thumbnail"'.(!empty($img) ? ' style="background-image:url('.$img.')"' : '').'>';
            if (empty($img)) {
                $html .= '<i class="zmdi zmdi-label"></i>';
            }
            $html .= '</span><span class="selected-items-name">'.$related->title;
            $html .= '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
            $html .= '<span class="grid-sorting-handle"></span></span>';
        }
        $html .= '</div></div>';
        $html .= '</div></div></div></div>';

        return $html;
    }

    protected function getProductVariations($product)
    {
        $weight = isset($product->data->dimensions->weight) ? $product->data->dimensions->weight : '';
        $html = '';
        if ($this->type == 'products') {
            $html .= $this->getHTMLHeader('0', 'product-options', '0', false, JText::_('OPTIONS'), true);
            $html .= '<div class="field-sorting-wrapper product-options"><div class="sorting-container">';
            foreach ($product->fields as $key => $field) {
                $html .= '<div class="sorting-item" data-id="'.$key.'" data-type="'.$field->type.'">';
                $html .= '<div class="product-options-title-wrapper">';
                $html .= $field->title.'</div><div class="selected-items-wrapper">';
                usort($field->map, function($a, $b){
                    return ($a->order_list < $b->order_list) ? -1 : 1;
                });
                foreach ($field->map as $option) {
                    $images = json_decode($option->images);
                    $count = is_array($images) ? count($images) : 0;
                    $html .= '<span class="selected-items" data-key="'.$option->option_key.'" data-id="'.$option->id;
                    $html .= '"><span class="ba-item-thumbnail" data-image-count="'.$count.'"';
                    if ($count > 0) {
                        $image = !gridboxHelper::isExternal($images[0]) ? JUri::root().$images[0] : $images[0];
                        $html .= ' style="background-image: url('.$image.');"';
                        foreach ($images as $key => $image) {
                            $html .= ' data-image-'.$key.'="'.$image.'"';
                        }
                    }
                    $html .= '><i class="zmdi zmdi-camera"></i></span>';
                    $html .= '<span class="selected-items-name">'.$product->fields_data->{$option->option_key};
                    $html .= '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
                    $html .= '<span class="grid-sorting-handle"></span></span>';
                }
                $html .= '</div><div class="product-options-icons-wrapper"><span class="add-new-product-options-value">';
                $html .= '<i class="zmdi zmdi-plus"></i></span><span class="sorting-handle"><i class="zmdi zmdi-apps"></i>';
                $html .= '</span><span class=""><i class="zmdi zmdi-delete"></i></span></div></div>';
            }
            $html .= '</div><div class="add-new-item"><span><input type="text" value="'.JText::_('ADD_NEW_ITEM');
            $html .= '" readonly="" onfocus="this.blur()"><i class="zmdi zmdi-plus"></i></span></div></div>';
            $html .= '</div></div>';
            $html .= $this->getHTMLHeader('1', 'product-variations', '1', false, JText::_('VARIATIONS'), true);
            $html .= '<div class="product-variations-table">';
            $html .= '<div class="variations-table-header"><div class="variations-table-row">';
            $html .= '<div class="variations-table-cell variation-cell"></div>';
            $html .= '<div class="variations-table-cell price-cell">'.JText::_('PRICE').'</div>';
            $html .= '<div class="variations-table-cell sele-price-cell">'.JText::_('SALE_PRICE').'</div>';
            $html .= '<div class="variations-table-cell sku-cell">'.JText::_('SKU').'</div>';
            $html .= '<div class="variations-table-cell stock-cell">'.JText::_('IN_STOCK').'</div>';
            $html .= '<div class="variations-table-cell weight-cell">'.JText::_('WEIGHT').'</div>';
            $html .= '<div class="variations-table-cell default-cell">'.JText::_('DEFAULT').'</div>';
            $html .= '</div></div>';
            $html .= '<div class="variations-table-body">';
            foreach ($product->data->variations as $key => $obj) {
                $array = explode('+', $key);
                $flag = true;
                foreach ($array as $value) {
                    if (!isset($product->fields_data->{$value})) {
                        $flag = false;
                        break;
                    }
                }
                if (!$flag) {
                    break;
                }
                $obj->weight = isset($obj->weight) ? $obj->weight : $weight;
                $html .= '<div class="variations-table-row" data-key="'.$key.'">';
                $html .= '<div class="variations-table-cell variation-cell">';
                foreach ($array as $value) {
                    $html .= '<span>'.$product->fields_data->{$value}.'</span>';
                }
                $html .= '</div>';
                $html .= '<div class="variations-table-cell price-cell" data-field-type="price">';
                $html .= '<input type="text" data-key="price" data-decimals="10';
                $html .= '" value="'.$obj->price.'"></div>';
                $html .= '<div class="variations-table-cell sale-price-cell" data-field-type="price">';
                $html .= '<input type="text" data-key="sale_price" data-decimals="10';
                $html .= '" value="'.$obj->sale_price.'"></div>';
                $html .= '<div class="variations-table-cell sku-cell"><input type="text" data-key="sku" value="'.$obj->sku;
                $html .= '"></div><div class="variations-table-cell stock-cell" data-field-type="price">';
                $html .= '<input type="text" data-key="stock" value="'.$obj->stock.'"></div>';
                $html .= '<div class="variations-table-cell weight-cell" data-field-type="price">';
                $html .= '<input type="text" data-key="weight" data-decimals="2" value="'.$obj->weight.'"></div>';
                $html .= '<div class="variations-table-cell default-cell" data-default="';
                $html .= (isset($obj->default) && $obj->default ? 1 : 0).'"><i class="zmdi zmdi-star"></i></div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div></div></div>';
        }
        

        $html .= $this->getHTMLHeader('0', 'product-extra-options', '0', false, JText::_('EXTRA_OPTIONS'), true);
        $html .= '<div class="field-sorting-wrapper product-extra-options"><div class="sorting-container">';
        foreach ($product->data->extra_options as $id => $obj) {
            $isFile = $obj->type == 'file' || $obj->type == 'textarea' || $obj->type == 'textinput';
            $html .= '<div class="sorting-item" data-id="'.$id.'" data-option-type="'.$obj->type.'">';
            $html .= '<div class="extra-product-options-table"><div class="extra-product-options-thead">';
            $html .= '<div class="extra-product-options-row"><div class="extra-product-option-title">'.$obj->title.'</div>';
            $html .= '<div class="extra-product-option-price">'.($isFile ? '' :  JText::_('PRICE')).'</div>';
            $html .= '<div class="extra-product-option-weight">'.($isFile ? '' :  JText::_('WEIGHT')).'</div>';
            $html .= '<div class="extra-product-option-default">'.($isFile ? '' :  JText::_('DEFAULT')).'</div>';
            $html .= '<div class="extra-product-option-icons">';
            if (!$isFile) {
                $html .= '<span class="add-new-extra-product-options"><i class="zmdi zmdi-plus"></i></span>';
            }
            $html .= '<span class="sorting-handle"><i class="zmdi zmdi-apps"></i></span>';
            $html .= '<span><i class="zmdi zmdi-delete"></i></span></div></div></div>';
            $html .= '<div class="extra-product-options-tbody">';
            if (!$isFile) {
                foreach ($obj->items as $key => $item) {
                    $item->weight = isset($item->weight) ? $item->weight : '';
                    $html .= '<div class="extra-product-options-row" data-key="'.$key.'">';
                    $html .= '<div class="extra-product-option-title">'.$item->title.'</div>';
                    $html .= '<div class="extra-product-option-price" data-field-type="price">';
                    $html .= '<input type="text" data-decimals="10" value="'.$item->price.'"></div>';
                    $html .= '<div class="extra-product-option-weight" data-field-type="price">';
                    $html .= '<input type="text" data-decimals="2" value="'.$item->weight.'"></div>';
                    $html .= '<div class="extra-product-option-default"><i class="zmdi zmdi-star" data-default="';
                    $html .= ((int)$item->default).'"></i></div><div class="extra-product-option-icons">';
                    $html .= '<span class="delete-extra-product-option"><i class="zmdi zmdi-delete"></i></span></div></div>';
                }
            } else {
                foreach ($obj->items as $key => $item) {
                    $html .= '<div class="extra-product-options-row" data-key="0">';
                    $html .= '<div class="extra-product-option-title">'.JText::_('FIELD_PRICE').'</div>';
                    $html .= '<div class="extra-product-option-price field-editor-price-wrapper ';
                    $html .= gridboxHelper::$store->currency->position.'" data-field-type="price">';
                    $html .= '<span class="field-editor-price-currency">'.gridboxHelper::$store->currency->symbol.'</span>';
                    $html .= '<input type="text" data-decimals="10" value="'.$item->price.'"></div>';
                    $html .= '</div>';
                }
            }            
            $html .= '</div></div></div>';
        }
        $html .= '</div><div class="add-new-item"><span><input type="text" value="'.JText::_('ADD_NEW_ITEM');
        $html .= '" readonly="" onfocus="this.blur()"><i class="zmdi zmdi-plus"></i></span></div>';
        $html .= '<template class="file-row-template">';
        $html .= '<div class="extra-product-options-row" data-key="0">';
        $html .= '<div class="extra-product-option-title">'.JText::_('FIELD_PRICE').'</div>';
        $html .= '<div class="extra-product-option-price field-editor-price-wrapper ';
        $html .= gridboxHelper::$store->currency->position.'" data-field-type="price">';
        $html .= '<span class="field-editor-price-currency">'.gridboxHelper::$store->currency->symbol.'</span>';
        $html .= '<input type="text" data-decimals="10" value=""></div>';
        $html .= '</div>';
        $html .= '</template>';
        $html .= '</div></div></div>';

        return $html;
    }

    protected function getBookingData($product)
    {
        $booking = gridboxHelper::getBooking();
        $this->bookingSettings = $booking->getSettings();
        $data = isset($product->data->booking->type) ? $product->data->booking : $booking->decodeSettingsFile('booking-product.json');

        return $data;
    }

    protected function getProductBooking($product)
    {
        $data = $this->getBookingData($product);
        $hours = isset($data->single->hours) ? $data->single->hours : $this->bookingSettings->default;
        
        $keys = [
            'booking_type' => [
                'type' => 'select',
                'required' => false,
                'title' => JText::_('BOOKING_TYPE'),
                'items' => [
                    (object)[
                        'key' => 'single',
                        'title' => JText::_('SINGLE_DAY')
                    ],
                    (object)[
                        'key' => 'multiple',
                        'title' => JText::_('MULTIPLE_NIGHTS')
                    ]
                ],
                'value' => $data->type,
                'class' => ''
            ],
            'min' => [
                'type' => 'text',
                'required' => false,
                'title' => JText::_('MIN_NIGHTS'),
                'value' => $data->multiple->min,
                'class' => 'one-fifty-width'.($data->type == 'single' ? ' ba-hide-element' : '')
            ],
            'max' => [
                'type' => 'text',
                'required' => false,
                'title' => JText::_('MAX_NIGHTS'),
                'value' => $data->multiple->max,
                'class' => 'one-fifty-width'.($data->type == 'single' ? ' ba-hide-element' : '')
            ],
            'type' => [
                'type' => 'select',
                'required' => false,
                'title' => JText::_('TYPE'),
                'items' => [
                    (object)[
                        'key' => 'private',
                        'title' => JText::_('PERSONAL_SESSION')
                    ],
                    (object)[
                        'key' => 'group-session',
                        'title' => JText::_('GROUP_SESSION')
                    ],
                    (object)[
                        'key' => 'group',
                        'title' => JText::_('PRIVATE_GROUP_SESSION')
                    ]
                ],
                'value' => $data->single->type,
                'class' => ($data->single->type != 'private' ? 'one-fifty-width' : '').($data->type != 'single' ? ' ba-hide-element' : '')
            ],
            'participants' => [
                'type' => 'text',
                'required' => false,
                'title' => JText::_('MAX_PARTICIPANTS'),
                'value' => $data->single->participants,
                'class' => 'one-fifty-width'.($data->type != 'single' || $data->single->type == 'private' ? ' ba-hide-element' : '')
            ],
            'time' => [
                'type' => 'select',
                'required' => false,
                'title' => JText::_('APPOINTMENT_TIME_SLOTS'),
                'items' => [
                    (object)[
                        'key' => 'yes',
                        'title' => JText::_('GRIDBOX_YES')
                    ],
                    (object)[
                        'key' => 'no',
                        'title' => JText::_('GRIDBOX_NO')
                    ]
                ],
                'value' => $data->single->time,
                'class' => ($data->type != 'single' ? ' ba-hide-element' : '')
            ],
            'duration' => [
                'type' => 'select',
                'required' => false,
                'title' => JText::_('APPOINTMENT_DURATION'),
                'items' => [
                    (object)[
                        'key' => '15',
                        'title' => '15 minutes'
                    ],
                    (object)[
                        'key' => '30',
                        'title' => '30 minutes'
                    ],
                    (object)[
                        'key' => '45',
                        'title' => '45 minutes'
                    ],
                    (object)[
                        'key' => '60',
                        'title' => '1 hour'
                    ],
                    (object)[
                        'key' => '90',
                        'title' => '1 hour 30 minutes'
                    ],
                    (object)[
                        'key' => '120',
                        'title' => '2 hours'
                    ],
                    (object)[
                        'key' => '150',
                        'title' => '2 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '180',
                        'title' => '3 hours'
                    ],
                    (object)[
                        'key' => '210',
                        'title' => '3 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '240',
                        'title' => '4 hours'
                    ],
                    (object)[
                        'key' => '270',
                        'title' => '4 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '300',
                        'title' => '5 hours'
                    ],
                    (object)[
                        'key' => '330',
                        'title' => '5 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '360',
                        'title' => '6 hours'
                    ],
                    (object)[
                        'key' => '390',
                        'title' => '6 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '420',
                        'title' => '7 hours'
                    ],
                    (object)[
                        'key' => '450',
                        'title' => '7 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '480',
                        'title' => '8 hours'
                    ],
                    (object)[
                        'key' => '510',
                        'title' => '8 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '540',
                        'title' => '9 hours'
                    ],
                    (object)[
                        'key' => '570',
                        'title' => '9 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '600',
                        'title' => '10 hours'
                    ],
                    (object)[
                        'key' => '630',
                        'title' => '10 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '660',
                        'title' => '11 hours'
                    ],
                    (object)[
                        'key' => '690',
                        'title' => '11 hours 30 minutes'
                    ],
                    (object)[
                        'key' => '720',
                        'title' => '12 hours'
                    ]
                ],
                'value' => $data->single->duration,
                'class' => 'one-fifty-width'.($data->type != 'single' || $data->single->time == 'no' ? ' ba-hide-element' : '')
            ],
            'availability' => [
                'type' => 'select',
                'required' => false,
                'title' => JText::_('AVAILABILITY'),
                'items' => [
                    (object)[
                        'key' => 'default',
                        'title' => JText::_('DEFAULT')
                    ],
                    (object)[
                        'key' => 'custom',
                        'title' => JText::_('CUSTOM')
                    ]
                ],
                'value' => $data->single->availability,
                'class' => 'one-fifty-width'.($data->type != 'single' || $data->single->time == 'no' ? ' ba-hide-element' : '')
            ]
        ];
        $html = '';
        $options = new stdClass();
        $obj = new stdClass();
        $obj->id = !empty($product->data) ? $product->data->id : 0;
        foreach ($keys as $key => $object) {
            $options->type = $object['type'];
            $className = $object['class'];
            $html .= $this->getHTMLHeader($key, $object['type'], $obj->id,
                                            $object['required'], $object['title'],
                                            true, $className);
            if ($object['type'] == 'select') {
                $options->items = $object['items'];
                $html .= $this->renderSelect($obj, $options, $object['value']);
            } else {
                $html .= $this->renderText($obj, $options, $object['value']);
            }
            $html .= '</div></div>';
        }

        $className = $data->type != 'single' || $data->single->time == 'no' || $data->single->availability == 'default' ? 'ba-hide-element' : '';
        $html .= $this->getHTMLHeader('booking-hours', 'booking-hours', $obj->id, false, JText::_('WORKING_HOURS'), true, $className);
        include JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/booking/fields-working-hours.php';
        $html .= $out;
        $html .= '</div></div>';


        return $html;
    }

    protected function getProductPricingFields($product)
    {
        $keys = [
            'price' => [
                'type' => 'price',
                'required' => true,
                'title' => JText::_('REGULAR_PRICE'),
                'value' => !empty($product->data) ? $product->data->price : '',
                'class' => $this->type == 'booking' ? '' : 'trigger-variations-table one-third-width'
            ],
            'sale_price' => [
                'type' => 'price',
                'required' => false,
                'title' => JText::_('SALE_PRICE'),
                'value' => !empty($product->data) ? $product->data->sale_price : '',
                'class' => $this->type == 'booking' ? '' : 'trigger-variations-table one-third-width'
            ],
            'stock' => [
                'type' => 'text',
                'required' => false,
                'title' => JText::_('IN_STOCK'),
                'value' => !empty($product->data) ? $product->data->stock : '',
                'class' => 'one-third-width'
            ],
            'sku' => [
                'type' => 'text',
                'required' => false,
                'title' => JText::_('SKU'),
                'value' => !empty($product->data) ? $product->data->sku : '',
                'class' => 'one-third-width'
            ]
        ];
        if ($this->type == 'booking') {
            unset($keys['stock']);
            unset($keys['sku']);
        }
        $options = new stdClass();
        $options->symbol = gridboxHelper::$store->currency->symbol;
        $options->decimals = 10;
        $options->position = gridboxHelper::$store->currency->position;
        $obj = new stdClass();
        $obj->id = !empty($product->data) ? $product->data->id : 0;
        $html = '';
        foreach ($keys as $key => $data) {
            $options->type = $data['type'];
            $className = $data['class'].' product-data';
            $html .= $this->getHTMLHeader($key, $data['type'], $obj->id, $data['required'], $data['title'], true, $className);
            if ($data['type'] == 'price') {
                $html .= $this->renderPrice($obj, $options, $data['value']);
            } else {
                $html .= $this->renderText($obj, $options, $data['value']);
            }
            $html .= '</div></div>';
        }
        if ($this->type != 'booking') {
            $className = 'trigger-variations-table one-third-width';
            $html .= $this->getHTMLHeader('dimensions', 'price', 'dimensions', false, JText::_('WEIGHT'), true, $className);
            $value = isset($product->data->dimensions->weight) ? $product->data->dimensions->weight : '';
            $html .= '<div class="field-editor-price-wrapper right-currency-position';
            $html .= '"><span class="field-editor-price-currency">'.gridboxHelper::$store->units->weight;
            $html .= '</span><input type="text" name="weight"';
            $html .= ' value="'.$value.'" data-decimals="2"></div>';
            $html .= '</div></div>';


            $className = ' one-third-width product-data';
            $label = JText::_('MIN_QTY');
            $help = JText::_('SET_PRODUCT_MINIMUM_QUANTITY');
            $html .= $this->getHTMLHeader('min', 'price', $obj->id, false, $label, true, $className, $help);
            $value = isset($product->data) ? $product->data->min : '';
            $html .= '<input type="text" name="min" value="'.$value.'" data-decimals="0">';
            $html .= '</div></div>';
        } else {
            $data = $this->getBookingData($product);
            $options->type = 'select';
            $options->items = [
                (object)[
                    'key' => 'complete',
                    'title' => JText::_('NO_NE')
                ],
                (object)[
                    'key' => 'partial',
                    'title' => JText::_('ONLINE_PARTIAL_PREPAYMENT')
                ]
            ];

            
            
            $value = $data->payment->type ?? 'complete';
            $className = $value == 'complete' ? '' : 'booking-partial-payment';
            $html .= $this->getHTMLHeader('booking_payment', 'booking-payment', 'booking_payment', false, JText::_('PREPAYMENT'), true, $className);
            $obj->id = 'type';
            $html .= $this->renderSelect($obj, $options, $value);

            $options->attributes = [
                'data-symbol' => $options->symbol
            ];
            $options->items = [
                (object)[
                    'key' => '$',
                    'title' => JText::_('AMOUNT')
                ],
                (object)[
                    'key' => '%',
                    'title' => JText::_('PERCENTAGE')
                ]
            ];
            $value = $data->payment->unit ?? '%';
            $obj->id = 'unit';
            $html .= $this->renderSelect($obj, $options, $value);

            $options->symbol = $value == '%' ? '%' : gridboxHelper::$store->currency->symbol;
            unset($options->attributes);
            $value = $data->payment->value ?? '';
            $obj->id = 'value';

            $html .= $this->renderPrice($obj, $options, $value);




            $html .= '</div></div>';



            



        }
        

        $html .= $this->getHTMLHeader('badges', 'product-badges', 'badges', false, JText::_('PRODUCT_BADGES'), true);
        $html .= '<div class="field-sorting-wrapper product-badges"><div class="sorting-container">';
        $html .= '<div class="sorting-item"><div class="product-badges-title-wrapper"><i class="zmdi zmdi-plus"></i></div>';
        $html .= '<div class="selected-items-wrapper" style="--placeholder-text:\''.JText::_('ADD_NEW_ITEM').'\';">';
        foreach ($product->badges as $badge) {
            $html .= '<span class="selected-items" data-id="'.$badge->id;
            $html .= '"><span class="selected-items-color" style="--badge-color: '.$badge->color;
            $html .= ';"></span><span class="selected-items-name">'.$badge->title;
            $html .= '</span><i class="zmdi zmdi-close remove-selected-items"></i>';
            $html .= '<span class="grid-sorting-handle"></span></span>';
        }
        $html .= '</div></div>';
        $html .= '</div></div></div></div>';

        return $html;
    }

    protected function getFieldHTML($fields_data, $value)
    {
        if (isset($fields_data->{$value->id})) {
            $fieldValue = $fields_data->{$value->id}->value;
        } else {
            $fieldValue = '';
        }
        $str = $this->getHTML($value, $fieldValue);

        return $str;
    }

    public function getHTMLHeader($key, $type, $id, $required, $label, $texteditor = false, $className = '', $help = '')
    {
        $html = '<div class="blog-post-'.$this->name.'-options-group '.$className.'" data-field-key="'.$key;
        $html .= '" data-field-type="'.$type.'"';
        $html .= ' data-id="'.$id.'" '.($required ? 'data-required' : '').'>';
        $html .= '<div class="blog-post-'.$this->name.'-group-element">';
        if (!$texteditor && $this->name == 'editor') {
            $html .= '<div class="sorting-handle"><i class="zmdi zmdi-apps"></i></div>';
        }
        $html .= '<label class="ba-field-'.$this->name.'-label">';
        $html .= $label.($required ? '<span class="required-fields-star">*</span>' : '').'</label>';
        if (!empty($help)) {
            $html .= '<label class="ba-help-icon"><i class="zmdi zmdi-help"></i>';
            $html .= '<span class="ba-tooltip ba-top ba-help ba-hide-element">'.$help.'</span></label>';
        }

        return $html;
    }

    public function getHTML($field, $value = '')
    {
        $options = json_decode($field->options);
        $label = isset($options->label) && !empty($options->label) ? $options->label : $field->label;
        $texteditor = $options->type == 'textarea' ? $options->texteditor : false;
        $html = $this->getHTMLHeader($field->field_key, $options->type, $field->id, $field->required, $label, $texteditor);
        if (isset($options->description) && !empty($options->description)) {
            $html .= '<span class="ba-field-'.$this->name.'-admin-description">'.$options->description.'</span>';
        }
        switch ($options->type) {
            case 'group-headline':
                $html .= $this->renderGroupHeadline($field, $options, $value);
                break;
            case 'text':
            case 'email':
            case 'number':
                $html .= $this->renderText($field, $options, $value);
                break;
            case 'price':
                $html .= $this->renderPrice($field, $options, $value);
                break;
            case 'textarea':
                $html .= $this->renderTextarea($field, $options, $value);
                break;
            case 'category':
                $html .= $this->renderCategory($field, $options, $value);
                break;
            case 'select':
                $html .= $this->renderSelect($field, $options, $value);
                break;
            case 'checkbox':
                $html .= $this->renderCheckbox($field, $options, $value);
                break;
            case 'radio':
                $html .= $this->renderRadio($field, $options, $value);
                break;
            case 'range':
                $html .= $this->renderRange($field, $options, $value);
                break;
            case 'url':
            case 'field-button':
                $html .= $this->renderUrl($field, $options, $value);
                break;
            case 'file':
                $html .= $this->renderFile($field, $options, $value);
                break;
            case 'date':
            case 'event-date':
                $html .= $this->renderDate($field, $options, $value);
                break;
            case 'tag':
                $html .= $this->renderTags($field, $options, $value);
                break;
            case 'image-field':
                $html .= $this->renderImage($field, $options, $value);
                break;
            case 'field-simple-gallery':
            case 'product-gallery':
            case 'field-slideshow':
            case 'product-slideshow':
                if ($this->name == 'editor') {
                    $html .= $this->renderGallery($field, $options, $value);
                } else {
                    $html .= $this->rederSubmissionGallery($field, $options, $value);
                }
                break;
            case 'field-google-maps':
                $html .= $this->renderGoogleMaps($field, $options, $value);
                break;
            case 'field-video':
                $html .= $this->renderVideo($field, $options, $value);
                break;
            case 'time':
                $html .= $this->renderTime($field, $options, $value);
                break;
        }
        $html .= '</div></div>';

        return $html;
    }

    protected function renderGroupHeadline($field, $options, $value)
    {
        return '';
    }

    protected function renderText($field, $options, $value)
    {
        return '<input type="'.$options->type.'" name="'.$field->id.'" value="'.$value.'">';
    }

    protected function renderPrice($field, $options, $value)
    {
        $str = '<div class="field-'.$this->name.'-price-wrapper '.$options->position;
        $str .= '"><span class="field-'.$this->name.'-price-currency">'.$options->symbol.'</span><input type="text" name="';
        $str .= $field->id.'" value="'.$value.'" data-decimals="'.$options->decimals.'"></div>';

        return $str;
    }

    protected function renderTextarea($field, $options, $value)
    {
        $str = '';
        if ($options->texteditor && $this->jce) {
            $str .= '<div class="ba-editor-wrapper jce-editor-enabled">';
            $str .= $this->form->getInput('editor'.$this->jceIndex);
            $str .= '</div>';
        }
        $str .= '<textarea name="'.$field->id.'" style="'.($options->texteditor && $this->jce ? 'display: none;' : '').'"';
        $str .= ($options->texteditor ? ' data-texteditor="texteditor"' : '');
        $str .= ($options->texteditor && $this->jce ? ' data-jce="'.($this->jceIndex++).'"' : '');
        $str .= '>'.$value.'</textarea>';

        return $str;
    }

    protected function renderCategory($field, $options, $value)
    {
        $li = $text = '';
        foreach ($options->items as $item) {
            $content = '';
            for ($i = 0; $i < $item->level; $i++) {
                $content .= '- ';
            }
            if ($item->level != 0) {
                $content .= '-';
            }
            if ($item->id == $value) {
                $text = $item->title;
            }
            $li .= '<li data-value="'.$item->id.'" style="--content: \''.$content.'\';" class="'.($item->id == $value ? 'selected' : '').'">';
            $li .= ($item->id == $value ? '<i class="zmdi zmdi-check"></i>' : '').$item->title.'</li>';
        }
        $str = '<div class="ba-custom-select '.$this->name.'-category-select"><input class="reset-input-margin" readonly onfocus="this.blur()"';
        $str .= 'placeholder="'.JText::_('CATEGORY').'" type="text" value="'.$text.'"><input type="hidden" name="'.$field->id.'" value="'.$value.'"><ul>';
        $str .= $li;
        $str .= '</ul><i class="zmdi zmdi-caret-down"></i></div>';

        return $str;
    }

    protected function renderSelect($field, $options, $value)
    {
        $str = '<select name="'.$field->id.'" value="'.$value.'"';
        if (isset($options->attributes)) {
            foreach ($options->attributes as $key => $attribute) {
                $str .= ' '.$key.'="'.$attribute.'"';
            }
        }
        $str .= '>';
        foreach ($options->items as $item) {
            if ($value == $item->key) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            $str .= '<option value="'.$item->key.'"'.$selected.'>'.$item->title.'</option>';
        }
        $str .= '</select>';

        return $str;
    }

    protected function renderCheckbox($field, $options, $value)
    {
        if (!empty($value)) {
            $data = json_decode($value);
        } else {
            $data = [];
        }
        $str = '';
        foreach ($options->items as $item) {
            $checked = in_array($item->key, $data);
            $str .= '<div class="ba-checkbox-wrapper"><span>'.$item->title.'</span><label class="ba-checkbox">';
            $str .= '<input type="checkbox" name="'.$field->id.'"'.($checked ? ' checked' : '').' value="'.$item->key.'">';
            $str .= '<span></span></label></div>';
        }

        return $str;
    }

    protected function renderRadio($field, $options, $value)
    {
        $str = '';
        foreach ($options->items as $item) {
            $checked = $value == $item->key;
            $str .= '<div class="ba-checkbox-wrapper"><span>'.$item->title.'</span><label class="ba-radio">';
            $str .= '<input type="radio" name="'.$field->id.'"'.($checked ? ' checked' : '').' value="'.$item->key.'">';
            $str .= '<span></span></label></div>';
        }

        return $str;
    }

    protected function renderRange($field, $options, $value)
    {
        $rangeValue = !empty($value) ? $value : 0;
        $str = '<div class="ba-range-wrapper"><span class="ba-range-liner"></span>';
        $str .= '<input type="range" class="ba-range" name="'.$field->id.'" min="'.$options->min;
        $str .= '" max="'.$options->max.'" value="'.$rangeValue.'"><input type="number" ';
        $str .= 'data-callback="emptyCallback" value="'.$rangeValue.'"></div>';

        return $str;
    }

    protected function renderUrl($field, $options, $value)
    {
        if (!empty($value)) {
            $data = json_decode($value);
        } else {
            $data = new stdClass();
            $data->label = $data->link = '';
        }
        $str = '<div'.(empty($options->label_type) ? '' : ' class="contstant-lanel-type"').'>';
        if (empty($options->label_type)) {
            $str .= '<div class="ba-url-field-label-wrapper">';
            $str .= ($this->name == 'editor' ? '<label>'.JText::_('LABEL').'</label>' : '');
            $str .= '<input type="text" name="'.$field->id.'" data-name="label" value="'.$data->label.'"';
            $str .= ($this->name == 'editor' ? '' : ' placeholder="'.JText::_('LABEL').'"').'></div>';
        }
        $str .= '<div class="link-picker-container">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('LINK').'</label>';
        }
        $str .= '<input type="text" name="'.$field->id.'" data-name="link" value="'.$data->link.'"';
        $str .= ($this->name == 'editor' ? '' : ' placeholder="'.JText::_('LINK').'"').'>';
        if ($this->name == 'editor') {
            $str .= '<div class="select-link"><i class="zmdi zmdi-attachment-alt"></i><span class="ba-tooltip">';
            $str .= JText::_('LINK_PICKER').'</span></div><div class="select-file"><i class="zmdi zmdi-file"></i>';
            $str .= '<span class="ba-tooltip">'.JText::_('FILE_PICKER').'</span></div>';
        }
        $str .= '</div></div>';

        return $str;
    }

    protected function renderFile($field, $options, $value)
    {
        if (is_numeric($value) && isset($this->desktopFiles->{$value})) {
            $desktopFile = $this->desktopFiles->{$value};
            $filename = $desktopFile->name;
        } else if (is_numeric($value)) {
            $value = $filename = '';
        } else {
            $filename = basename($value);
        }
        $str = '<div><input type="text" readonly="" onfocus="this.blur()" class="trigger-attachment-file-field"';
        if ($options->source == 'desktop' || $this->name != 'editor') {
            $str .= ' data-source="desktop"';
        }
        $str .= ' data-value="'.$value.'" value="'.$filename.'" placeholder="'.JText::_('SELECT');
        $str .= '" name="'.$field->id.'" data-size="'.$options->size;
        $str .= '" data-types="'.$options->types.'"><i class="zmdi zmdi-attachment-alt"></i>';
        $str .= '<div class="reset disabled-reset reset-attachment-file-field"><i class="zmdi zmdi-close"></i></div></div>';

        return $str;
    }

    protected function renderDate($field, $options, $value)
    {
        $str = '<div class="container-icon">';
        if ($this->name != 'editor') {
            $str .= '<div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
            $str .= '<div class="reset disabled-reset reset-date-field"><i class="zmdi zmdi-close"></i></div>';
        }
        $str .= '<input type="text" class="open-calendar-dialog" name="';
        $str .= $field->id.'" data-format="Y-m-d" readonly value="'.$value.'">';
        if ($this->name == 'editor') {
            $str .= '<div class="icons-cell"><i class="zmdi zmdi-calendar-alt"></i></div>';
            $str .= '<div class="reset disabled-reset reset-date-field"><i class="zmdi zmdi-close"></i></div>';
        }
        $str .= '</div>';

        return $str;
    }

    protected function renderTags($field, $options, $value)
    {
        $str = '<div class="meta-tags" data-name="'.$field->id.'">';
        $str .= '<select style="display: none;" name="meta_tags[]" class="meta_tags" multiple>';
        foreach ($this->pageTags as $tag) {
            $str .= '<option value="'.$tag->id.'" selected>'.$tag->title.'</option>';
        }
        $str .= '</select><ul class="picked-tags">';
        $html = '';
        foreach ($this->pageTags as $tag) {
            $html .= '<li class="tags-chosen"><span>';
            $html .= $tag->title.'</span><i class="zmdi zmdi-close" data-remove="'.$tag->id.'"></i></li>';
        }
        if ($this->name == 'editor') {
            $str.= $html;
        }
        $str .= '<li class="search-tag">';
        $str .= '<input type="text" placeholder="'.JText::_($this->name == 'editor' ? 'TAGS' : 'TYPE_PRESS_ENTER_TO_CREATE_TAG').'">';
        $str .= '</li>';
        if ($this->name != 'editor') {
            $str.= $html;
        }
        $str .= '</ul>';
        $str .= '<div class="select-post-tags input-action-icon"><i class="zmdi zmdi-playlist-plus"></i>';
        $str .= '<span class="ba-tooltip ba-top ba-hide-element">'.JText::_('TAGS').'</span></div>';
        $str .= '</div>';

        return $str;
    }

    protected function renderImage($field, $options, $value)
    {
        if (!empty($value)) {
            $data = json_decode($value);
        } else {
            $data = new stdClass();
            $data->src = $data->alt = '';
        }
        if (!isset($data->src)) {
            $data->src = $data->alt = '';
        }
        $filename = $path = '';
        if (is_numeric($data->src) && isset($this->desktopFiles->{$data->src})) {
            $desktopFile = $this->desktopFiles->{$data->src};
            $filename = $desktopFile->name;
            $path = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
        } else if (is_numeric($data->src)) {
            $data->src = '';
        } else if (!empty($data->src)) {
            $path = $data->src;
            $filename = basename($data->src);
        }
        $src = !gridboxHelper::isExternal($data->src) ? JUri::root().$path : $path;
        $img = !empty($data->src) ? 'background-image:url('.$src.');' : '';
        $str = '<div class="select-image-field-wrapper"><div class="ba-image-field-label-wrapper">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('SELECT').'</label>';
        }
        $str .= '<div><input type="text" name="'.$field->id.'" data-name="src" placeholder="'.JText::_('SELECT');
        $str .= '" value="'.($this->name == 'editor' ? $filename : '').'" readonly onfocus="this.blur()" class="select-image-field"';
        if ($options->source == 'desktop' || $this->name != 'editor') {
            $str .= ' data-source="desktop" data-size="'.$options->size.'"';
        }
        $str .= ' data-value="'.$data->src.'"><i class="zmdi zmdi-camera"></i>';
        if ($this->name == 'editor') {
            $str .= '<div class="image-field-tooltip" style="'.$img.'"></div>';
            $str .= '<div class="reset disabled-reset reset-image-field"><i class="zmdi zmdi-close"></i></div>';
        }
        $str .= '</div></div>';
        if ($this->name == 'editor') {
            $str .= '<div class="link-picker-container"><label>'.JText::_('IMAGE_ALT').'</label>';
            $str .= '<input type="text" name="'.$field->id.'"  data-name="alt" value="'.$data->alt.'"></div>';
        } else {
            $str .= '<div class="ba-uploaded-images-list">';
            if (!empty($data->src)) {
                $str .= '<div class="ba-uploaded-image-row" data-id="'.$data->src.'" data-alt="'.$data->alt.'">';
                $str .= '<span class="ba-uploaded-image" style="'.$img.'"></span>';
                $str .= '<span class="ba-uploaded-image-title">'.$filename.'</span><i class="ba-icons ba-icon-trash"></i>';
                $str .= '</div>';
            }
            $str .= '</div>';
        }
        $str .= '</div>';

        return $str;
    }

    protected function rederSubmissionGallery($field, $options, $value)
    {
        if (!empty($value)) {
            $data = json_decode($value);
        } else {
            $data = [];
        }
        $str = '<div class="select-image-field-wrapper"><div class="ba-image-field-label-wrapper">';
        $str .= '<div><input type="text" name="'.$field->id.'" data-name="src" placeholder="'.JText::_('SELECT');
        $str .= '" value="" readonly onfocus="this.blur()" class="select-image-field"';
        $str .= ' data-source="desktop" data-size="'.$options->size.'"';
        $str .= ' data-value=""><i class="zmdi zmdi-camera"></i>';
        $str .= '</div></div>';
        $str .= '<div class="ba-uploaded-images-list">';
        foreach ($data as $key => $obj) {
            if (is_numeric($obj->img) && isset($this->desktopFiles->{$obj->img})) {
                $desktopFile = $this->desktopFiles->{$obj->img};
                $path = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                $filename = $desktopFile->name;
            } else {
                $path = $obj->img;
                $filename = basename($obj->img);
            }
            $img = !gridboxHelper::isExternal($path) ? JUri::root().$path : $path;
            $unpublish = isset($obj->unpublish) && $obj->unpublish ? '1' : '0';
            $str .= '<div class="ba-uploaded-image-row" data-id="'.$obj->img.'" data-alt="'.$obj->alt.'">';
            $str .= '<span class="ba-uploaded-image" style="background-image: url('.$img.');"></span>';
            $str .= '<span class="ba-uploaded-image-title">'.$filename.'</span><i class="ba-icons ba-icon-trash"></i>';
            $str .= '</div>';
        }
        $str .= '</div>';
        $str .= '</div>';

        return $str;
    }

    protected function renderGallery($field, $options, $value)
    {
        if (!empty($value)) {
            $data = json_decode($value);
        } else {
            $data = [];
        }
        $str = '<div class="field-sorting-wrapper"';
        if ($options->source == 'desktop' || $this->name != 'editor') {
            $str .= ' data-source="desktop" data-size="'.$options->size.'"';
        }
        $str .= '><div class="sorting-container">';
        $i = 0;
        foreach ($data as $key => $obj) {
            if (is_numeric($obj->img) && isset($this->desktopFiles->{$obj->img})) {
                $desktopFile = $this->desktopFiles->{$obj->img};
                $path = 'components/com_gridbox/assets/uploads/app-'.$desktopFile->app_id.'/'.$desktopFile->filename;
                $filename = $desktopFile->name;
            } else {
                $path = $obj->img;
                $filename = basename($obj->img);
            }
            $img = !gridboxHelper::isExternal($path) ? JUri::root().$path : $path;
            $unpublish = isset($obj->unpublish) && $obj->unpublish ? '1' : '0';
            $str .= '<div class="sorting-item" data-img="'.$obj->img;
            $str .= '" data-unpublish="'.$unpublish.'" data-path="'.$path.'" data-alt="'.$obj->alt.'">';
            $str .= '<div class="sorting-checkbox"><label><input type="checkbox"><span></span></label></div>';
            $str .= '<div class="sorting-image sorting-handle"><img src="'.$img;
            $str .= '"></div><div class="sorting-title sorting-handle">';
            $str .= $filename.'</div><div class="sorting-icons"><span><i class="zmdi zmdi-edit"></i></span>';
            $str .= '<span><i class="zmdi zmdi-copy"></i></span>';
            $str .= '<span><i class="zmdi zmdi-eye-off unpublish-sorting-item"></i></span>';
            $str .= '<span><i class="zmdi zmdi-delete"></i></span></div></div>';
            $i++;
        }
        $str .= '</div>';
        $str .= '<div class="sorting-toolbar">';
        $str .= '<span class="sorting-toolbar-action" data-action="add"><i class="zmdi zmdi-plus"></i>';
        $str .= '<span class="ba-tooltip ba-bottom">'.JText::_('ADD_NEW_ITEM').'</span></span>';
        $disabled = $i == 0 ? ' disabled' : '';
        $str .= '<span class="sorting-toolbar-action'.$disabled.'" data-action="check"><i class="zmdi zmdi-check-circle"></i>';
        $str .= '<span class="ba-tooltip ba-bottom">'.JText::_('CHECK_ALL').'</span></span>';
        $str .= '<span class="sorting-toolbar-action disabled" data-action="copy"><i class="zmdi zmdi-copy"></i>';
        $str .= '<span class="ba-tooltip ba-bottom">'.JText::_('COPY_ITEM').'</span></span>';
        $str .= '<span class="sorting-toolbar-action disabled" data-action="delete"><i class="zmdi zmdi-delete"></i>';
        $str .= '<span class="ba-tooltip ba-bottom">'.JText::_('DELETE_ITEM').'</span></span>';
        $str .= '</div>';
        $str .= '</div>';

        return $str;
    }

    protected function renderGoogleMaps($field, $options, $value)
    {
        if (!empty($value)) {
            $obj = json_decode($value);
        } else {
            $value = '{}';
            $obj = new stdClass();
        }
        $str = '<div class="field-sorting-wrapper"><input type="text" name="'.$field->id;
        $str .= '" data-autocomplete="" placeholder="'.JText::_($this->name == 'editor' ? 'ENTER_LOCATION' : 'SEARCH_LOCATION_ON_MAP');
        $str .= '" value="'.(isset($obj->marker) ? $obj->marker->place : '').'">';
        $str .= '<div style="display: none !important;">'.$value.'</div>';
        $str .= '<div class="field-google-map-wrapper" data-id="'.$field->id.'"></div></div>';

        return $str;
    }

    protected function renderVideo($field, $options, $value)
    {
        if (!empty($value)) {
            $obj = json_decode($value);
        } else {
            $obj = new stdClass();
            $obj->type = 'source';
            $obj->id = $obj->file = '';
        }
        if ($obj->type == 'source' && is_numeric($obj->file) && isset($this->desktopFiles->{$obj->file})) {
            $desktopFile = $this->desktopFiles->{$obj->file};
            $filename = $desktopFile->name;
        } else {
            $filename = basename($obj->file);
        }
        $str = '<div class="field-sorting-wrapper"><div class="ba-field-video-source-wrapper">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('VIDEO_SOURCE').'</label>';
        }
        $str .= '<select class="select-field-video-type" name="'.$field->id.'" data-name="type" value="'.$obj->type.'">';
        $str .= '<option value="" style="display: none;">'.JText::_('SELECT').'</option>';
        $youtube = !isset($options->youtube) || $options->youtube;
        $vimeo = !isset($options->vimeo) || $options->vimeo;
        $file = !isset($options->file) || $options->file;
        if ($youtube) {
            $str .= '<option value="youtube"'.($obj->type == 'youtube' ? ' selected' : '').'>Youtube</option>';
        }
        if ($vimeo) {
            $str .= '<option value="vimeo"'.($obj->type == 'vimeo' ? ' selected' : '').'>Vimeo</option>';
        }
        if ($file) {
            $str .= '<option value="source"'.($obj->type == 'source' ? ' selected' : '').'>'.JText::_('SOURCE_FILE').'</option>';
        }
        $str .= '</select>';
        $str .= '</div><div class="field-video-id" style="';
        $str .= ($obj->type != 'source' && ($youtube || $vimeo) ? '' : 'display: none;').'">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('VIDEO_ID').'</label>';
        }
        $str .= '<input type="text" name="'.$field->id;
        $str .= '" data-name="id" value="'.$obj->id.'" placeholder="'.JText::_('VIDEO_ID').'"></div>';
        $str .= '<div class="field-video-file" style="'.($obj->type == 'source' && $file ? '' : 'display: none;').'">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('SOURCE_FILE').'</label>';
        }
        $str .= '<div><input type="text" class="select-input';
        $str .= ' disable-webkit-placeholder" readonly onfocus="this.blur()" name="'.$field->id;
        $str .= '" data-name="file" data-types="mp4" data-value="'.$obj->file.'" value="'.$filename.'"';
        if ($options->source == 'desktop' || $this->name != 'editor') {
            $str .= ' data-source="desktop" data-size="'.$options->size.'"';
        }
        $str .= ' placeholder="'.JText::_('SELECT');
        $str .= '"><i class="zmdi zmdi-attachment-alt"></i>';
        $str .= '<div class="reset disabled-reset"><i class="zmdi zmdi-close"></i></div></div></div></div>';

        return $str;
    }

    protected function renderTime($field, $options, $value)
    {
        if (!empty($value)) {
            $obj = json_decode($value);
        } else {
            $obj = new stdClass();
            $obj->format = '';
            $obj->hours = $obj->minutes = '';
        }
        $str = '<div class="field-sorting-wrapper"><div class="ba-select-secondary">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('HOURS').'</label>';
        }
        $str .= '<select name="'.$field->id.'" data-name="hours" value="'.$obj->hours.'">';
        if ($this->name != 'editor') {
            $str .= '<option value="">'.JText::_('HOURS').'</option>';
        }
        for ($i = 0; $i < 24; $i++) {
            $j = $i < 10 ? '0'.$i : $i;
            $str .=  '<option value="'.$j.'"'.($j ==  $obj->hours ? ' selected' : '').'>'.$j.'</option>';
        }
        $str .= '</select></div><span>:</span><div class="ba-select-secondary">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('MINUTES').'</label>';
        }
        $str .= '<select name="'.$field->id.'" data-name="minutes" value="'.$obj->minutes.'">';
        if ($this->name != 'editor') {
            $str .= '<option value="">'.JText::_('MINUTES').'</option>';
        }
        for ($i = 0; $i < 60; $i++) {
            $j = $i < 10 ? '0'.$i : $i;
            $str .= '<option value="'.$j.'"'.($j ==  $obj->minutes ? ' selected' : '').'>'.$j.'</option>';
        }
        $str .= '</select></div><div class="ba-select-secondary">';
        if ($this->name == 'editor') {
            $str .= '<label>'.JText::_('FORMAT').'</label>';
        }
        $str .= '<select name="'.$field->id.'" data-name="format" value="'.$obj->format.'">';
        if ($this->name != 'editor') {
            $str .= '<option value="">'.JText::_('FORMAT').'</option>';
        } else {
            $str .= '<option value=""'.($obj->format == '' ? ' selected' : '').'>-</option>';
        }
        $str .= '<option value="AM"'.($obj->format == 'AM' ? ' selected' : '').'>AM</option>';
        $str .= '<option value="PM"'.($obj->format == 'PM' ? ' selected' : '').'>PM</option></select></div></div>';

        return $str;
    }
}