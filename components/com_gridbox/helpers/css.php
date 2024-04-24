<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class cssHelper
{
    public $css;
    public $states;
    public $state;
    public $transitions;
    public $keys;
    public $transition;
    public $breakpoint;
    public $item;
    public $object;
    public $media;
    public $cascade;

    public function __construct()
    {
        $this->css = '';
        $this->states = new stdClass();
        $this->transitions = [];
        $this->keys = new stdClass();
        $this->keys->states = ['hover'];
        $this->keys->border = ['bottom', 'left', 'top', 'right'];
        $this->keys->shadow = ['horizontal', 'vertical', 'blur', 'spread'];
        $this->keys->fonts = ['body', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $this->transition = (object)['duration' => 0.3, 'x1' => 0.42, 'y1' => 0, 'x2' => 0.58, 'y2' => 1];
        $this->cascade = new stdClass();
    }

    public function prepareColor($obj)
    {
        $object = new stdClass();
        foreach ($obj as $ind => $value) {
            $key = $ind == 'background' ? 'background-color' : $ind;
            $object->{$key} = $obj->{$ind};
        }

        return $object;
    }

    public function prepareColors($obj)
    {
        if (!isset($obj->desktop) && !isset($obj->colors)) {
            $obj->colors = new stdClass();
            $obj->colors->default = $this->prepareColor($obj->normal);
            $obj->colors->hover = $this->prepareColor($obj->hover);
            $obj->colors->state = true;
            $obj->colors->transition = $this->transition;
        } else if (isset($obj->desktop) && !isset($obj->desktop->colors)) {
            $obj->desktop->colors = new stdClass();
            $obj->desktop->colors->default = $obj->desktop->normal;
            $obj->desktop->colors->hover = $obj->hover;
            $obj->desktop->colors->state = true;
            $obj->desktop->colors->transition = $this->transition;
        }
    }

    public function get($key, $obj, $state, $variable = null, $calc = null, $states = null)
    {
        if (!isset($obj->default) && (isset($obj->hover) || isset($obj->active))) {
            $css = '';
        } else {
            $this->state = $state;
            $object = isset($obj->{$state}) ? $obj->{$state} : $obj;
            $this->{$key}($object, $variable, $calc);
            $css = $this->css;
        }
        if (!$states) {
            $states = $this->keys->states;
        }
        foreach ($states as $ind) {
            $this->checkState($key, $obj, $ind, $variable, $calc);
        }

        return $css;
    }

    public function checkState($key, $obj, $state, $variable, $calc){
        if (!isset($this->states->{$state})) {
            $this->states->{$state} = [];
        }
        if (((isset($obj->state) && $obj->state) || !isset($obj->state)) && isset($obj->{$state})) {
            $this->state = $state;
            $this->{$key}($obj->{$state}, $variable, $calc);
            $this->states->{$state}[] = $this->css;
            $this->updateTransitions($obj, $key);
        }
    }

    public function getStateRule($selector, $state)
    {
        if (!empty($this->states->{$state})) {
            $css = implode(' ', $this->states->{$state});
            $this->getFullRule($selector, $css);
        } else {
            $this->css = '';
        }

        return $this->css;
    }

    public function getFullRule($selector, $css)
    {
        $this->css = $selector." {";
        $this->css .= $css;
        $this->css .= "}";
    }

    public function updateTransitions($obj, $key)
    {
        if (!isset($obj->transition)) {
            return;
        }
        $transition = $obj->transition;
        $property = $key == 'shadow' ? 'box-shadow' : $key;
        $easing = $transition->x1.', '.$transition->y1.', '.$transition->x2.', '.$transition->y2;
        if ($property == 'overlay') {
            $property = 'background';
        } else if ($property == 'backgroundColor') {
            $property = 'background-color';
        } else if ($property == 'colors') {
            $property = 'color';
        }
        $this->transitions [] = $property.' '.$transition->duration.'s cubic-bezier('.$easing.')';
        if ($key == 'border') {
            $this->updateTransitions($obj, 'border-radius');
        } else if ($key == 'colors') {
            $this->updateTransitions($obj, 'background-color');
        } else if ($key == 'overlay' || $key == 'background') {
            $this->updateTransitions($obj, 'backdrop-filter');
            $this->updateTransitions($obj, '-webkit-backdrop-filter');
        }
    }

    public function getTransitionRule($selector)
    {
        if (!empty($this->transitions)) {
            $this->getFullRule($selector, 'transition: '.implode(', ', $this->transitions).';');
        } else {
            $this->css = '';
        }
        $this->transitions = [];
        $this->states = new stdClass();

        return $this->css;
    }

    public function background($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        $image = $blur = 'none';
        $color = 'rgba(0, 0, 0, 0)';
        if ($obj->type == 'image' && empty($obj->image)) {
            return '';
        } else if ($obj->type == 'image') {
            $this->css .= "background-image: url(".$this->setBackgroundImage($obj->image).");";
            $this->css .= "background-color: ".$color.";";
            $this->css .= "backdrop-filter: ".$blur.";";
            $this->css .= "-webkit-backdrop-filter: ".$blur.";";
        } else if ($obj->type == 'color' && !isset($obj->color)) {
            return '';
        } else if ($obj->type == 'color' && $obj->color) {
            $this->css .= "background-image: ".$image.";";
            $this->css .= "background-color: ".$this->getCorrectColor($obj->color).";";
            $this->css .= "backdrop-filter: ".$blur.";";
            $this->css .= "-webkit-backdrop-filter: ".$blur.";";
        } else if ($obj->type == 'blur' && isset($obj->blur)) {
            $this->css .= "background-image: ".$image.";";
            $this->css .= "background-color: ".$color.";";
            $this->css .= "backdrop-filter: blur(".$obj->blur."px);";
            $this->css .= "-webkit-backdrop-filter: blur(".$obj->blur."px);";
        } else if ($obj->type == 'none' && $this->breakpoint != 'desktop') {
            $this->css .= "background-image: ".$image.";";
            $this->css .= "background-color: ".$color.";";
            $this->css .= "backdrop-filter: ".$blur.";";
            $this->css .= "-webkit-backdrop-filter: ".$blur.";";
        }
    }

    public function backgroundColor($obj, $variable = null, $calc = null)
    {
        $this->css = isset($obj->color) ? ($variable ? $variable : '')."background-color: ".$this->getCorrectColor($obj->color).";" : '';
    }

    public function backgroundImage($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        foreach ($obj as $key => $value) {
            $value = $key == 'image' ? 'url('.$this->setBackgroundImage($value).')' : $value;
            $this->css .= "background-".$key.": ".$value.";";
        }
    }

    public function backgroundBlur($blur, $variable = null, $calc = null)
    {
        $this->css = ($variable ? $variable : '')."backdrop-filter: blur(".$blur."px);";
        $this->css .= "-webkit-backdrop-filter: blur(".$blur."px);";
    }

    public function backgrounds($obj, $variable = null, $calc = null)
    {
        $this->css = "";
        $this->gradient($obj->gradient, $variable, $calc);
        $this->css .= "background-color: rgba(0, 0, 0, 0);";
        $this->css .= 'background-attachment: scroll;';
    }

    public function getColors($key, $obj, $state, $variable = null, $calc = null, $states = null)
    {
        if (isset($obj->colors->default) && $this->cascade->{'colors-bg'}) {
            $obj->colors->default->type = $this->cascade->{'colors-bg'}->type ?? '';
        }
        if (isset($obj->colors->default) && $this->cascade->{'colors-bg'} && $this->cascade->{'colors-bg'}->gradient) {
            $obj->colors->default->gradient = $this->cascade->{'colors-bg'}->gradient;
        }
        $css = $this->get($key, $obj->colors, $state, $variable, $calc, $states);

        return $css;
    }

    public function colors($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        foreach ($obj as $ind => $value) {
            if ($ind == 'type') {
                continue;
            }
            if ($ind == 'color' || ($ind == 'background-color' && empty($obj->type))) {
                $this->css .= ($variable ? $variable : '').$ind.': '.$this->getCorrectColor($value).';';
            } else if ($ind == 'gradient' && !empty($obj->type)) {
                $this->gradient($value, $variable, $calc);
            }
        }
    }

    public function overlay($obj, $variable = null, $calc = null)
    {
        $this->css = "";
        if ((!isset($obj->type) || $obj->type == 'color') && isset($obj->color)) {
            $this->css .= "background-color: ".$this->getCorrectColor($obj->color).";";
            $this->css .= 'background-image: none;';
            $this->css .= 'backdrop-filter: none;';
            $this->css .= '-webkit-backdrop-filter: none;';
        } else if (isset($obj->type) && $obj->type == 'none' && $this->breakpoint != 'desktop') {            
            $this->css .= 'background-color: rgba(0, 0, 0, 0);';
            $this->css .= 'background-image: none;';
            $this->css .= 'backdrop-filter: none;';
            $this->css .= '-webkit-backdrop-filter: none;';
        } else if (isset($obj->type) && $obj->type == 'blur' && isset($obj->blur)) {
            $this->backgroundBlur($obj->blur);
            $this->css .= 'background-color: rgba(0, 0, 0, 0);';
            $this->css .= 'background-image: none;';
        } else if (isset($obj->type) && $obj->type == 'gradient' && isset($obj->gradient)) {
            $this->css .= 'background-color: rgba(0, 0, 0, 0);';
            $this->gradient($obj->gradient, $variable, $calc);
            $this->css .= 'background-attachment: scroll;';
            $this->css .= 'backdrop-filter: none;';
            $this->css .= '-webkit-backdrop-filter: none;';
        }
    }

    public function gradient($obj, $variable = null, $calc = null)
    {
        $this->css .= 'background-image: '.$obj->effect.'-gradient(';
        $this->css .= $obj->effect == 'linear' ? $obj->angle.'deg' : 'circle';
        $this->css .= ', '.$this->getCorrectColor($obj->color1).' ';
        $this->css .= $obj->position1.'%, '.$this->getCorrectColor($obj->color2);
        $this->css .= ' '.$obj->position2.'%);';
    }

    public function prepareBorder($obj)
    {
        if (isset($obj) && (!isset($obj->top) || !isset($obj->right) || !isset($obj->bottom) || !isset($obj->left))) {
            foreach ($this->keys->border as $key) {
                $obj->{$key} = 1;
            }
        }
    }

    public function border($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        $properties = ['style', 'color', 'width', 'radius'];
        if (!$variable) {
            $variable = '--';
        }
        $flag = false;
        foreach ($this->keys->border as $key) {
            if ($flag) {
                break;
            }
            $flag = isset($obj->{$key}) && $obj->{$key} == 1;
        }
        if ($this->breakpoint != 'desktop' && !$flag && (isset($obj->style) || isset($obj->color) || isset($obj->width) || isset($obj->radius))) {
            $flag = true;
        }
        if (!$flag && (!isset($obj->radius) || (isset($obj->radius) && $obj->radius === ''))) {
            return;
        } else if (!$flag) {
            $this->css .= $variable."border-radius: ".$this->getValueUnits($obj->radius).";";
            return;
        }
        if (isset($obj->style) || isset($obj->color) || isset($obj->width) || isset($obj->radius)) {
            $object = $this->cascade->border;
            $object = isset($object->{$this->state}) ? $object->{$this->state} : $object;
            foreach ($properties as $property) {
                if (!isset($obj->{$property})) {
                    $obj->{$property} = $object->{$property};
                }
            }
        }
        foreach ($obj as $key => $value) {
            if ($key == 'color') {
                $value = $this->getCorrectColor($value);
            } else if (($key == 'width' || $key == 'radius') && $value !== '') {
                $value = $this->getValueUnits($value);
            } else if ($key != 'style') {
                $value = (int)$value;
            }
            $this->css .= $variable."border-".$key.": ".$value.";";
        }
    }

    public function animation($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        foreach ($obj as $key => $value) {
            if ($key == 'effect') {
                $this->css .= "opacity: ".(!empty($value) ? "0" : "1").";";
            } else if ($key != 'repeat') {
                $this->css .= "animation-".$key.": ".$value."s;";
            }
        }
    }

    public function margin($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        foreach ($obj as $ind => $value) {
            if ($value === '') {
                continue;
            }
            $this->css .= ($variable ? $variable : '').'margin-'.$ind.': ';
            $this->css .= ($calc ? 'calc(' : '').$this->getValueUnits($value).($calc ? $calc.')' : '').';';
        }
    }

    public function padding($obj, $variable = null, $calc = null)
    {
        $this->css = '';
        foreach ($obj as $ind => $value) {
            if ($value === '') {
                continue;
            }
            $this->css .= ($variable ? $variable : '').'padding-'.$ind.': ';
            $this->css .= ($calc ? 'calc(' : '').$this->getValueUnits($value).($calc ? $calc.')' : '').';';
        }
    }

    public function shadow($obj, $variable = null, $calc = null)
    {
        $this->css = "";
        $array = [];
        if ((!isset($obj->advanced) || !$obj->advanced) && isset($obj->value)) {
            $value = $obj->value === '' ? 0 : $obj->value;
            $array = ['horizontal' => 0, 'vertical' => $value * 10, 'blur' => $value * 20, 'spread' => 0];
        } else if (isset($obj->advanced) && $obj->advanced) {
            foreach ($this->keys->shadow as $key) {
                if (isset($obj->{$key})) {
                    $array[$key] = $obj->{$key};
                }
            }
        }
        $flag = false;
        foreach ($array as $key => $value) {
            if ($value > 0 || $this->breakpoint != 'desktop' || $this->state != 'default') {
                $flag = true;
                break;
            }
        }
        if (!$flag) {
            return;
        }
        foreach ($array as $key => $value) {
            $this->css .= '--shadow-'.$key.': '.$value.'px;';
        }
        if (isset($obj->color)) {
            $this->css .= '--shadow-color: '.$this->getCorrectColor($obj->color).";";
        }
    }

    public function presetsCompatibility($obj)
    {
        if ((empty($obj->type) || $obj->type == 'side-navigation-menu') && isset($obj->hamburger)) {
            $obj->layout->type = $obj->type;
            $obj->type = 'one-page';
        }
        switch ($obj->type) {
            case 'overlay-section':
            case 'lightbox':
            case 'cookies':
            case 'mega-menu-section':
            case 'row':
            case 'section':
            case 'footer':
            case 'header':
            case 'column':
                if (!isset($obj->desktop->full)) {
                    $obj->desktop->full = new stdClass();
                    $obj->desktop->full->fullscreen = $obj->desktop->fullscreen == '1';
                    if (isset($obj->{'max-width'})) {
                        $obj->desktop->full->fullwidth = $obj->{'max-width'} == '100%';
                    }
                    $obj->desktop->image = new stdClass();
                    $obj->desktop->image->image = $obj->desktop->background->image->image;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            if (isset($obj->{$ind}->fullscreen)) {
                                $obj->{$ind}->full = new stdClass();
                                $obj->{$ind}->full->fullscreen = $obj->{$ind}->fullscreen == '1';
                            }
                        }
                        if (isset($obj->{$ind}->background) && isset($obj->{$ind}->background->image)
                            && isset($obj->{$ind}->background->image->image)) {
                            $obj->{$ind}->image = new stdClass();
                            $obj->{$ind}->image->image = $obj->{$ind}->background->image->image;
                        }
                    }
                    if ($obj->type == 'column') {
                        foreach (gridboxHelper::$breakpoints as $ind => $value) {
                            if (isset($obj->{$ind}) && isset($obj->{$ind}->{'column-width'})) {
                                $obj->{$ind}->span = new stdClass();
                                $obj->{$ind}->span->width = $obj->{$ind}->{'column-width'};
                            }
                        }
                    } else if ($obj->type == 'row') {
                        $obj->desktop->view = new stdClass();
                        $obj->desktop->view->gutter = $obj->desktop->gutter == '1';
                        foreach (gridboxHelper::$breakpoints as $ind => $value) {
                            if (isset($obj->{$ind}) && isset($obj->{$ind}->gutter)) {
                                $obj->{$ind}->view = new stdClass();
                                $obj->{$ind}->view->gutter = $obj->{$ind}->gutter == '1';
                            }
                        }
                    } else if ($obj->type == 'overlay-section' || $obj->type == 'lightbox' || $obj->type == 'cookies') {
                        $obj->lightbox = new stdClass();
                        if (isset($obj->layout) && isset($obj->position)) {
                            $obj->lightbox->layout = $obj->layout;
                            $obj->lightbox->position = $obj->position;
                        } else if (isset($obj->layout)) {
                            $obj->lightbox->layout = $obj->layout;
                        } else if (isset($obj->position)) {
                            $obj->lightbox->layout = $obj->position;
                        }
                        if (isset($obj->{'background-overlay'})) {
                            $obj->lightbox->background = $obj->{'background-overlay'};
                        }
                        $obj->desktop->view = new stdClass();
                        $obj->desktop->view->width = $obj->desktop->width;
                        if (isset($obj->desktop->height)) {
                            $obj->desktop->view->height = $obj->desktop->height;
                        }
                        foreach (gridboxHelper::$breakpoints as $ind => $value) {
                            if (isset($obj->{$ind})) {
                                $obj->{$ind}->view = new stdClass();
                                if (isset($obj->{$ind}->width)) {
                                    $obj->{$ind}->view->width = $obj->{$ind}->width;
                                }
                                if (isset($obj->{$ind}->height)) {
                                    $obj->{$ind}->view->height = $obj->{$ind}->height;
                                }
                            }
                        }
                    } else if ($obj->type == 'mega-menu-section') {
                        $obj->view = new stdClass();
                        $obj->view->width = $obj->width;
                        $obj->view->position = $obj->position;
                    }
                }
                break;
            case 'button':
            case 'overlay-button':
            case 'scroll-to':
            case 'scroll-to-top':
                if (!isset($obj->desktop->icons)) {
                    $obj->desktop->icons = new stdClass();
                    $obj->desktop->icons->size = $obj->desktop->size;
                    if ($obj->type == 'scroll-to') {
                        $obj->desktop->icons->align = 'center';
                    }
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind}) && isset($obj->{$ind}->size)) {
                            $obj->{$ind}->icons = new stdClass();
                            $obj->{$ind}->icons->size = $obj->{$ind}->size;
                        }
                    }
                }
                if ($obj->type == 'scroll-to-top' && !isset($obj->text)) {
                    $obj->text =  new stdClass();
                    $obj->text->align = $obj->{"scrolltop-align"};
                }
                if ($obj->type == 'scroll-to' && !isset($obj->desktop->typography)) {
                    $obj->desktop->icons->position = 'after';
                    $typography = '{"font-family":"@default","font-size":10,"font-style":"normal","font-weight":"700",';
                    $typography .= '"letter-spacing":4,"line-height":26,"text-align":"center","text-decoration":"none",';
                    $typography .= '"text-transform":"uppercase"}';
                    $obj->desktop->typography = json_decode($typography);
                    $obj->desktop->typography->{"text-align"} = $obj->desktop->icons->align;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind}) && isset($obj->{$ind}->icons) && isset($obj->{$ind}->align)) {
                            $obj->{$ind}->typography = new stdClass();
                            $obj->{$ind}->typography->{"text-align"} = $obj->{$ind}->icons->align;
                        }
                    }
                }
            case 'scroll-to':
            case 'scroll-to-top':
            case 'tags':
            case 'post-tags':
            case 'icon':
            case 'social-icons':
                if (!isset($obj->desktop->normal)) {
                    $obj->desktop->normal = new stdClass();
                    $obj->desktop->normal->color = $obj->desktop->color;
                    $obj->desktop->normal->{'background-color'} = $obj->desktop->{'background-color'};
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            if (isset($obj->{$ind}->color) || isset($obj->{$ind}->{'background-color'})) {
                                $obj->{$ind}->normal = new stdClass();
                                if (isset($obj->{$ind}->color)) {
                                    $obj->{$ind}->normal->color = $obj->{$ind}->color;
                                }
                                if (isset($obj->{$ind}->{'background-color'})) {
                                    $obj->{$ind}->normal->{'background-color'} = $obj->{$ind}->{'background-color'};
                                }
                            }
                        }
                    }
                }
                break;
            case 'counter':
            case 'countdown':
                if (!isset($obj->desktop->background)) {
                    $obj->desktop->background = new stdClass();
                    $obj->desktop->background->color = $obj->desktop->color;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind}) && isset($obj->{$ind}->color)) {
                            $obj->{$ind}->background = new stdClass();
                            $obj->{$ind}->background->color = $obj->{$ind}->color;
                        }
                    }
                }
                break;
            case 'categories':
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->view->counter = $obj->desktop->counter;
                    $obj->desktop->view->sub = $obj->desktop->sub;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->counter)) {
                                $obj->{$ind}->view->counter = $obj->{$ind}->counter;
                            }
                            if (isset($obj->{$ind}->sub)) {
                                $obj->{$ind}->view->sub = $obj->{$ind}->sub;
                            }
                        }
                    }
                }
                if (!isset($obj->layout)) {
                    $obj = $this->prepareBlogCategories($obj);
                }
                break;
            case 'carousel':
            case 'slideset':
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->gutter = ($obj->gutter != '');
                    $obj->desktop->view->height = $obj->desktop->height;
                    $obj->desktop->view->size = $obj->desktop->size;
                    $obj->desktop->view->dots = $obj->desktop->dots->enable;
                    $obj->desktop->view->arrows = $obj->desktop->arrows->enable;
                    $obj->desktop->overlay =  new stdClass();
                    $obj->desktop->overlay->color = $obj->desktop->caption->color;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->overflow)) {
                                $obj->{$ind}->view->overflow = $obj->{$ind}->overflow;
                            }
                            if (isset($obj->{$ind}->height)) {
                                $obj->{$ind}->view->height = $obj->{$ind}->height;
                            }
                            if (isset($obj->{$ind}->size)) {
                                $obj->{$ind}->view->size = $obj->{$ind}->size;
                            }
                        }
                    }
                }
                break;
            case 'slideshow':
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->view->fullscreen = $obj->desktop->fullscreen;
                    $obj->desktop->view->height = $obj->desktop->height;
                    $obj->desktop->view->size = $obj->desktop->size;
                    $obj->desktop->view->dots = $obj->desktop->dots->enable;
                    $obj->desktop->view->arrows = $obj->desktop->arrows->enable;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->fullscreen)) {
                                $obj->{$ind}->view->fullscreen = $obj->{$ind}->fullscreen;
                            }
                            if (isset($obj->{$ind}->height)) {
                                $obj->{$ind}->view->height = $obj->{$ind}->height;
                            }
                            if (isset($obj->{$ind}->size)) {
                                $obj->{$ind}->view->size = $obj->{$ind}->size;
                            }
                        }
                    }
                }
                break;
            case 'accordion':
                if (!isset($obj->desktop->icon)) {
                    $obj->desktop->icon = new stdClass();
                    $obj->desktop->icon->position = $obj->{'icon-position'};
                    $obj->desktop->icon->size = $obj->desktop->size;
                    $color = $obj->desktop->background;
                    $obj->desktop->background = new stdClass();
                    $obj->desktop->background->color = $color;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            if (isset($obj->{$ind}->size)) {
                                $obj->{$ind}->icon = new stdClass();
                                $obj->{$ind}->icon->size = $obj->{$ind}->size;
                            }
                            if (isset($obj->{$ind}->background)) {
                                $color = $obj->{$ind}->background;
                                $obj->{$ind}->background = new stdClass();
                                $obj->{$ind}->background->color = $color;
                            }
                        }
                    }
                }
                break;
            case 'tabs':
                if (!isset($obj->desktop->icon)) {
                    $obj->desktop->icon = new stdClass();
                    $obj->desktop->icon->position = $obj->{'icon-position'};
                    $obj->desktop->icon->size = $obj->desktop->size;
                    $color = $obj->desktop->background;
                    $obj->desktop->background = new  stdClass();
                    $obj->desktop->background->color = $color;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            if (isset($obj->{$ind}->size)) {
                                $obj->{$ind}->icon = new stdClass();
                                $obj->{$ind}->icon->size = $obj->{$ind}->size;
                            }
                            if (isset($obj->{$ind}->background)) {
                                $color = $obj->{$ind}->background;
                                $obj->{$ind}->background = new stdClass();
                                $obj->{$ind}->background->color = $color;
                            }
                        }
                    }
                }
                break;
            case 'image':
                if (!isset($obj->desktop->style)) {
                    if (!isset($obj->desktop->width)) {
                        $obj->desktop->width = $obj->width;
                    }
                    $obj->popup = (bool)($obj->lightbox->enable * 1);
                    $obj->desktop->style = new stdClass();
                    $obj->desktop->style->width = $obj->desktop->width;
                    $obj->desktop->style->align = $obj->align;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->style = new stdClass();
                            if (isset($obj->{$ind}->width)) {
                                $obj->{$ind}->style->width = $obj->{$ind}->width;
                            }
                        }
                    }
                }
                break;
            case 'simple-gallery':
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->view->height = $obj->desktop->height;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->count)) {
                                $obj->{$ind}->view->count = $obj->{$ind}->count;
                            }
                            if (isset($obj->{$ind}->height)) {
                                $obj->{$ind}->view->height = $obj->{$ind}->height;
                            }
                            if (isset($obj->{$ind}->gutter)) {
                                $obj->{$ind}->view->gutter = $obj->{$ind}->gutter;
                            }
                        }
                    }
                }
                break;
            case 'weather':
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->view->layout = $obj->layout;
                    $obj->desktop->view->forecast = $obj->desktop->forecast;
                    $obj->desktop->view->wind = $obj->desktop->wind;
                    $obj->desktop->view->humidity = $obj->desktop->humidity;
                    $obj->desktop->view->pressure = $obj->desktop->pressure;
                    $obj->desktop->view->{'sunrise-wrapper'} = $obj->desktop->{'sunrise-wrapper'};
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->forecast)) {
                                $obj->{$ind}->view->forecast = $obj->{$ind}->forecast;
                            }
                            if (isset($obj->{$ind}->wind)) {
                                $obj->{$ind}->view->forecast = $obj->{$ind}->wind;
                            }
                            if (isset($obj->{$ind}->humidity)) {
                                $obj->{$ind}->view->humidityhumidity = $obj->{$ind}->humidity;
                            }
                            if (isset($obj->{$ind}->pressure)) {
                                $obj->{$ind}->view->pressure = $obj->{$ind}->pressure;
                            }
                            if (isset($obj->{$ind}->{'sunrise-wrapper'})) {
                                $obj->{$ind}->view->{'sunrise-wrapper'} = $obj->{$ind}->{'sunrise-wrapper'};
                            }
                        }
                    }
                }
                break;
            case "menu":
                if (!isset($obj->desktop->nav)) {
                    $nav ='{"padding":{"bottom":"15","left":"15","right":"15","top":"15"},"margin":{"left":"0","right":"0"}';
                    $nav .= ',"icon":{"size":24},"border":{"bottom":"0","left":"0","right":"0","top":"0","color":"#000000",';
                    $nav .= '"style":"solid","radius":"0","width":"0"},"normal":{"color":"color","background":"rgba(0,0,0,';
                    $nav .= '0)"},"hover":{"color":"color","background":"rgba(0,0,0,0)"}}';
                    $obj->desktop->nav = json_decode($nav);
                    $obj->desktop->nav->normal->color = $obj->desktop->{'nav-typography'}->color;
                    $obj->desktop->nav->hover->color = $obj->desktop->{'nav-hover'}->color;
                    $sub = '{"padding":{"bottom":"10","left":"20","right":"20","top":"10"},"icon":{"size":24},"border":{';
                    $sub .= '"bottom":"0","left":"0","right":"0","top":"0","color":"#000000","style":"solid","radius":"0",';
                    $sub .= '"width":"0"},"normal":{"color":"color","background":"rgba(0,0,0,0)"},"hover":{"color":"color",';
                    $sub .= '"background":"rgba(0,0,0,0)"}}';
                    $obj->desktop->sub = json_decode($sub);
                    $obj->desktop->sub->normal->color = $obj->desktop->{'sub-typography'}->color;
                    $obj->desktop->sub->hover->color = $obj->desktop->{'sub-hover'}->color;
                    $dropdown = '{"width":250,"animation":{"effect":"fadeInUp","duration":"0.2"},"padding":{"bottom":"10",';
                    $dropdown .= '"left":"0","right":"0","top":"10"}}';
                    $obj->desktop->dropdown = json_decode($dropdown);
                }
                if (!isset($obj->desktop->background)) {
                    $obj->desktop->background = new stdClass();
                    $obj->desktop->background->color = $obj->desktop->{'background-color'};
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind}) && isset($obj->{$ind}->{'background-color'})) {
                            $obj->{$ind}->background = new stdClass();
                            $obj->{$ind}->background->color = $obj->{$ind}->{'background-color'};
                        }
                    }
                    $layout = $obj->layout;
                    $obj->layout = new stdClass();
                    $obj->layout->layout = $layout;
                }
                break;
            case "one-page":
                if (!isset($obj->desktop->nav)) {
                    $nav ='{"padding":{"bottom":"15","left":"15","right":"15","top":"15"},"margin":{"left":"0","right":"0"}';
                    $nav .= ',"icon":{"size":24},"border":{"bottom":"0","left":"0","right":"0","top":"0","color":"#000000",';
                    $nav .= '"style":"solid","radius":"0","width":"0"},"normal":{"color":"color","background":"rgba(0,0,0,';
                    $nav .= '0)"},"hover":{"color":"color","background":"rgba(0,0,0,0)"}}';
                    $obj->desktop->nav = json_decode($nav);
                    $obj->desktop->nav->normal->color = $obj->desktop->{'nav-typography'}->color;
                    $obj->desktop->nav->hover->color = $obj->desktop->{'nav-hover'}->color;
                }
                if (gettype($obj->layout) == 'string') {
                    $layout = $obj->layout;
                    $obj->layout = new stdClass();
                    $obj->layout->layout = $layout;
                    $obj->layout->type = isset($obj->{'menu-type'}) ? $obj->{'menu-type'} : '';
                }
                break;
            case 'social':
                if (!isset($obj->view)) {
                    $obj->view = new stdClass();
                    $obj->view->layout = $obj->layout;
                    $obj->view->size = $obj->size;
                    $obj->view->style = $obj->style;
                    $obj->view->counters = $obj->counters;
                }
                break;
            case 'recent-posts-slider':
                if (!isset($obj->desktop->reviews)) {
                    $obj->desktop->reviews = new stdClass();
                    $obj->desktop->reviews->margin = new stdClass();
                    $obj->desktop->reviews->margin->top = 0;
                    $obj->desktop->reviews->margin->bottom = 25;
                    $obj->desktop->reviews->typography = new stdClass();
                    $obj->desktop->reviews->typography->color = "@title";
                    $obj->desktop->reviews->typography->{"font-family"} = "@default";
                    $obj->desktop->reviews->typography->{"font-size"} = 12;
                    $obj->desktop->reviews->typography->{"font-style"} = "normal";
                    $obj->desktop->reviews->typography->{"font-weight"} = "900";
                    $obj->desktop->reviews->typography->{"letter-spacing"}  = 0;
                    $obj->desktop->reviews->typography->{"line-height"} = 18;
                    $obj->desktop->reviews->typography->{"text-decoration"} = "none";
                    $obj->desktop->reviews->typography->{"text-align"} = "left";
                    $obj->desktop->reviews->typography->{"text-transform"} = "none";
                    $obj->desktop->reviews->hover = new stdClass();
                    $obj->desktop->reviews->hover->color = "@primary";
                }
                break;
            case 'recent-posts':
            case 'search-result':
            case 'store-search-result':
            case 'post-navigation':
            case 'related-posts':
            case 'blog-posts':
                if (!isset($obj->desktop->reviews)) {
                    $obj->desktop->reviews = new stdClass();
                    $obj->desktop->reviews->margin = new stdClass();
                    $obj->desktop->reviews->margin->top = 0;
                    $obj->desktop->reviews->margin->bottom = 25;
                    $obj->desktop->reviews->typography = new stdClass();
                    $obj->desktop->reviews->typography->color = "@title";
                    $obj->desktop->reviews->typography->{"font-family"} = "@default";
                    $obj->desktop->reviews->typography->{"font-size"} = 12;
                    $obj->desktop->reviews->typography->{"font-style"} = "normal";
                    $obj->desktop->reviews->typography->{"font-weight"} = "900";
                    $obj->desktop->reviews->typography->{"letter-spacing"}  = 0;
                    $obj->desktop->reviews->typography->{"line-height"} = 18;
                    $obj->desktop->reviews->typography->{"text-decoration"} = "none";
                    $obj->desktop->reviews->typography->{"text-align"} = "left";
                    $obj->desktop->reviews->typography->{"text-transform"} = "none";
                    $obj->desktop->reviews->hover = new stdClass();
                    $obj->desktop->reviews->hover->color = "@primary";
                }
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->view->count = $obj->desktop->count;
                    $obj->desktop->view->gutter = $obj->desktop->gutter;
                    if ($obj->type == 'blog-posts' && !isset($obj->desktop->image->show)) {
                        $obj->desktop->image->show = $obj->desktop->title->show = $obj->desktop->date = true;
                        $obj->desktop->category = $obj->desktop->intro->show = $obj->desktop->button->show = true;
                        $obj->desktop->hits = true;
                    } else if ($obj->type != 'blog-posts') {
                        $obj->desktop->hits = false;
                    }
                    $obj->desktop->view->image = $obj->desktop->image->show;
                    $obj->desktop->view->title = $obj->desktop->title->show;
                    $obj->desktop->view->intro = $obj->desktop->intro->show;
                    $obj->desktop->view->button = $obj->desktop->button->show;
                    $obj->desktop->view->date = $obj->desktop->date;
                    $obj->desktop->view->category = $obj->desktop->category;
                    $obj->desktop->view->hits = $obj->desktop->hits;
                    $color = $obj->desktop->overlay;
                    $obj->desktop->overlay = new stdClass();
                    $obj->desktop->overlay->color = $color;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->count)) {
                                $obj->{$ind}->view->count = $obj->{$ind}->count;
                            }
                            if (isset($obj->{$ind}->gutter)) {
                                $obj->{$ind}->view->gutter = $obj->{$ind}->gutter;
                            }
                            if (isset($obj->{$ind}->date)) {
                                $obj->{$ind}->view->date = $obj->{$ind}->date;
                            }
                            if (isset($obj->{$ind}->category)) {
                                $obj->{$ind}->view->category = $obj->{$ind}->category;
                            }
                            if (isset($obj->{$ind}->hits)) {
                                $obj->{$ind}->view->hits = $obj->{$ind}->hits;
                            }
                            if (isset($obj->{$ind}->image) && isset($obj->{$ind}->image->show)) {
                                $obj->{$ind}->view->image = $obj->{$ind}->image->show;
                            }
                            if (isset($obj->{$ind}->title) && isset($obj->{$ind}->title->show)) {
                                $obj->{$ind}->view->title = $obj->{$ind}->title->show;
                            }
                            if (isset($obj->{$ind}->intro) && isset($obj->{$ind}->intro->show)) {
                                $obj->{$ind}->view->intro = $obj->{$ind}->intro->show;
                            }
                            if (isset($obj->{$ind}->button) && isset($obj->{$ind}->button->show)) {
                                $obj->{$ind}->view->button = $obj->{$ind}->button->show;
                            }
                        }
                    }
                    $layout = $obj->layout;
                    $obj->layout = new stdClass();
                    $obj->layout->layout = $layout;
                }
                break;
            case 'search':
                if (!isset($obj->desktop->icons)) {
                    $obj->desktop->icons = new stdClass();
                    $obj->desktop->icons->size = $obj->desktop->size;
                    $obj->desktop->icons->position = $obj->icon->position;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->icons = new stdClass();
                            if (isset($obj->{$ind}->size)) {
                                $obj->{$ind}->icons->size = $obj->{$ind}->size;
                            }
                        }
                    }
                }
                break;
            case 'category-intro':
            case 'post-intro':
                if (!isset($obj->desktop->info->hover)) {
                    $obj->desktop->info->hover = new stdClass();
                    $obj->desktop->info->hover->color = '#fc5859';
                }
                if (!isset($obj->desktop->image->show)) {
                    $obj->desktop->image->show = $obj->desktop->title->show = true;
                    $obj->desktop->date = $obj->desktop->category = $obj->desktop->hits = true;
                }
                if (!isset($obj->desktop->view)) {
                    $obj->desktop->view = new stdClass();
                    $obj->desktop->view->date = $obj->desktop->date;
                    $obj->desktop->view->category = $obj->desktop->category;
                    $obj->desktop->view->hits = $obj->desktop->hits;
                    $layout = $obj->layout;
                    $obj->layout = new stdClass();
                    $obj->layout->layout = $layout;
                    foreach (gridboxHelper::$breakpoints as $ind => $value) {
                        if (isset($obj->{$ind})) {
                            $obj->{$ind}->view = new stdClass();
                            if (isset($obj->{$ind}->date)) {
                                $obj->{$ind}->view->date = $obj->{$ind}->date;
                            }
                            if (isset($obj->{$ind}->category)) {
                                $obj->{$ind}->view->category = $obj->{$ind}->category;
                            }
                            if (isset($obj->{$ind}->hits)) {
                                $obj->{$ind}->view->hits = $obj->{$ind}->hits;
                            }
                        }
                    }
                }
                break;
        }
        if ($obj->type == 'icon' || $obj->type == 'social-icons') {
            if (!isset($obj->desktop->icon)) {
                $obj->desktop->icon = new stdClass();
                $obj->desktop->icon->size = $obj->desktop->size;
                $obj->desktop->icon->{'text-align'} = $obj->desktop->{'text-align'};
                foreach (gridboxHelper::$breakpoints as $ind => $value) {
                    if (isset($obj->{$ind})) {
                        $obj->{$ind}->icon = new stdClass();
                        if (isset($obj->{$ind}->size)) {
                            $obj->{$ind}->icon->size = $obj->{$ind}->size;
                        }
                        if (isset($obj->{$ind}->{'text-align'})) {
                            $obj->{$ind}->icon->{'text-align'} = $obj->{$ind}->{'text-align'};
                        }
                    }
                }
            }
        }

        return $obj;
    }

    public function comparePresets($obj)
    {
        if (!empty($obj->preset) && isset(gridboxHelper::$presets->{$obj->type})
            && isset(gridboxHelper::$presets->{$obj->type}->{$obj->preset})) {
            $object = gridboxHelper::$presets->{$obj->type}->{$obj->preset};
            foreach ($object->data as $ind => $data) {
                if ($ind == 'desktop' || isset(gridboxHelper::$breakpoints->{$ind})) {
                    foreach ($data as $key => $value) {
                        $obj->{$ind}->{$key} = $value;
                    }
                } else if ($obj->type == 'flipbox' && $ind == 'sides') {
                    $this->compareFlipboxPresets($obj->sides->backside, $data->backside);
                    $this->compareFlipboxPresets($obj->sides->frontside, $data->frontside);
                } else {
                    $obj->{$ind} = $data;
                }
            }
        }
    }

    public function compareFlipboxPresets($obj, $object)
    {
        $obj->parallax = $object->parallax;
        $obj->desktop->background = $object->desktop->background;
        $obj->desktop->overlay = $object->desktop->overlay;
        foreach (gridboxHelper::$breakpoints as $key => $value) {
            if (isset($object->{$key}->background)) {
                $obj->{$key}->background = $object->{$key}->background;
            }
            if (isset($object->{$key}->overlay)) {
                $obj->{$key}->overlay = $object->{$key}->overlay;
            }
        }
    }

    public function prepareBlogCategories($obj)
    {
        $array = array('desktop', 'tablet', 'phone', 'laptop', 'tablet-portrait', 'phone-portrait');
        $object = gridboxHelper::getOptions('categories');
        $object = gridboxHelper::object_extend($object, $obj);
        $obj = $object;
        $obj->desktop->view->image = false;
        $obj->desktop->view->intro = false;
        foreach ($array as $view) {
            if (!isset($obj->{$view}) || (!isset($obj->{$view}->{'nav-typography'}) && !isset($obj->{$view}->{'nav-hover'}))) {
                continue;
            }
            if (isset($obj->{$view}->title)) {
                $obj->{$view}->title->margin->bottom = $obj->{$view}->title->margin->top = 0;
                $obj->{$view}->info->margin->bottom = $obj->{$view}->info->margin->top = 0;
            }
            if (!isset($obj->{$view}->title) && (isset($obj->{$view}->{'nav-typography'}) || isset($obj->{$view}->{'nav-hover'}))) {
                $obj->{$view}->title = new stdClass();
                $obj->{$view}->info = new stdClass();
            }
            if (isset($obj->{$view}->{'nav-typography'})) {
                $obj->{$view}->title->typography = $obj->{$view}->{'nav-typography'};
                $obj->{$view}->info->typography = $obj->{$view}->{'nav-typography'};
                unset($obj->{$view}->{'nav-typography'});
            }
            if (isset($obj->{$view}->{'nav-hover'})) {
                $obj->{$view}->title->hover = $obj->{$view}->{'nav-hover'};
                $obj->{$view}->info->hover = $obj->{$view}->{'nav-hover'};
                unset($obj->{$view}->{'nav-hover'});
            }
        }

        return $obj;
    }

    public function getTextParentFamily($key)
    {
        $desktop = gridboxHelper::$parentFonts->desktop;
        if (!$desktop) {
            return;
        }
        if (!isset($desktop->body)) {
            $empty = new stdClass();
            $desktop->body = gridboxHelper::object_extend($empty, $desktop->p);
        }
        $family = $desktop->{$key}->{'font-family'};
        if ($family == '@default') {
            $family = $desktop->body->{'font-family'};
        }

        return $family;
    }

    public function getTextParentWeight($key)
    {
        if (!gridboxHelper::$parentFonts->desktop) {
            return;
        }
        $weight = gridboxHelper::$parentFonts->desktop->{$key}->{'font-weight'};
        if ($weight == '@default') {
            $weight = gridboxHelper::$parentFonts->desktop->body->{'font-weight'};
        }

        return $weight;
    }

    public function getTextParentCustom($key)
    {
        if (!gridboxHelper::$parentFonts->desktop) {
            return;
        }
        $obj = gridboxHelper::$parentFonts->desktop->{$key};
        $custom = isset($obj->custom) ? $obj->custom : '';
        $family = $obj->{'font-family'};
        if ($family == '@default') {
            $body = gridboxHelper::$parentFonts->desktop->body;
            $custom = isset($body->custom) ? $body->custom : '';
        }

        return $custom;
    }

    public function createTypography($obj, $footer = false)
    {
        $str = "";
        $ind = $footer ? ' footer' : '';
        if (isset($obj->links->color)) {
            $str .= "body".$ind." a {";
            $str .= "color : ".$this->getCorrectColor($obj->links->color).";";
            $str .= "}";
        }
        if (isset($obj->links->{'hover-color'})) {
            $str .= "body".$ind." a:hover {";
            $str .= "color : ".$this->getCorrectColor($obj->links->{'hover-color'}).";";
            $str .= "}";
        }
        foreach ($this->keys->fonts as $key) {
            if (!isset($obj->{$key})) {
                continue;
            }
            $css = $this->getTypographyRule($obj->{$key});
            if (empty($css)) {
                continue;
            }
            $selector = $ind." ".$key;
            if ($key == 'body') {
                $selector = "body".$ind.",".$ind." ul,".$ind." ol,".$ind." table,".$ind." blockquote".($footer ? '' : ', html');
            } else if ($key == 'p') {
                $selector .= ','.$ind.' .content-text pre';
            }
            $str .= $selector." {";
            $str .= $css;
            $str .= "}";
            if ($key == 'body' && isset($obj->{$key}->{'line-height'})) {
                $str .= 'body'.$ind.' {';
                $str .= '--icon-list-line-height: '.$this->getValueUnits($obj->body->{'line-height'}).';';
                $str .= "}";
            }
        }

        return $str;
    }

    public function getPageCSS($obj, $key)
    {
        $obj = $this->presetsCompatibility($obj);
        $this->item = $obj;
        $this->comparePresets($obj);
        $this->breakpoint = 'desktop';
        $str = '';
        switch ($obj->type) {
            case 'field':
            case 'field-group':
                $str = $this->createFieldRules($obj, $key);
                break;
            case 'fields-filter':
                $str = $this->createFieldsFilterRules($obj, $key);
                break;
            case 'event-calendar':
                $str = $this->createEventCalendarRules($obj, $key);
                break;
            case 'preloader' :
                $str = $this->createPreloaderRules($obj, $key);
                break;
            case 'checkout-order-form':
                $str = '';
                break;
            case 'breadcrumbs':
                $str = $this->createBreadcrumbsRules($obj, $key);
                break;
            case 'checkout-form':
            case 'submission-form':
                $str = $this->createCheckoutFormRules($obj, $key);
                break;
            case 'icon-list':
                $str = $this->createIconListRules($obj, $key);
                break;
            case 'star-ratings':
                $str = $this->createStarRatingsRules($obj, $key);
                break;
            case 'blog-posts':
            case 'search-result':
            case 'store-search-result':
            case 'recent-posts':
            case 'related-posts':
            case 'post-navigation':
                $str = $this->createBlogPostsRules($obj, $key);
                break;
            case 'add-to-cart':
                $str = $this->createAddToCartRules($obj, $key);
                break;
            case 'categories':
                $str = $this->createCategoriesRules($obj, $key);
                break;
            case 'recent-comments':
            case 'recent-reviews':
                $str = $this->createRecentCommentsRules($obj, $key);
                break;
            case 'author':
                $str = $this->createAuthorRules($obj, $key);
                break;
            case 'post-intro':
            case 'category-intro':
                $str = $this->createPostIntroRules($obj, $key);
                break;
            case 'blog-content':
                $str = '';
                break;
            case 'search':
            case 'store-search':
                $str = $this->createSearchRules($obj, $key);
                break;
            case 'login':
                $str = $this->createLoginRules($obj, $key);
                break;
            case 'logo':
                $str = $this->createLogoRules($obj, $key);
                break;
            case 'feature-box':
                $str = $this->createFeatureBoxRules($obj, $key);
                break;
            case 'before-after-slider':
                $str = $this->createBeforeAfterSliderRules($obj, $key);
                break;
            case 'slideshow':
            case 'field-slideshow':
            case 'product-slideshow':
                $str = $this->createSlideshowRules($obj, $key);
                break;
            case 'carousel':
            case 'slideset':
                $str = $this->createCarouselRules($obj, $key);
                break;
            case 'testimonials-slider':
                $str = $this->createTestimonialsRules($obj, $key);
                break;
            case 'recent-posts-slider':
            case 'related-posts-slider':
            case 'recently-viewed-products':
                $str = $this->createRecentSliderRules($obj, $key);
                break;
            case 'content-slider':
                $str = $this->createContentRules($obj, $key);
                break;
            case 'menu':
                $str = $this->createMenuRules($obj, $key);
                break;
            case 'one-page':
                $str = $this->createOnePageRules($obj, $key);
                break;
            case 'map':
            case 'field-google-maps':
            case 'yandex-maps':
            case 'openstreetmap':
            case 'google-maps-places':
                $str = $this->createMapRules($obj, $key);
                break;
            case 'weather':
                $str = $this->createWeatherRules($obj, $key);
                break;
            case 'scroll-to-top':
                $str = $this->createScrollTopRules($obj, $key);
                break;
            case 'image':
            case 'image-field':
                $str = $this->createImageRules($obj, $key);
                break;
            case 'lottie-animations':
                $str = $this->createLottieRules($obj, $key);
                break;
            case 'video':
            case 'field-video':
                $str = $this->createVideoRules($obj, $key);
                break;
            case 'tabs':
                $str = $this->createTabsRules($obj, $key);
                break;
            case 'accordion':
                $str = $this->createAccordionRules($obj, $key);
                break;
            case 'icon':
            case 'social-icons':
                $str = $this->createIconRules($obj, $key);
                break;
            case 'cart':
            case 'submit-button':
            case 'button':
            case 'tags':
            case 'post-tags':
            case 'overlay-button':
            case 'scroll-to':
            case 'wishlist':
            case 'field-button':
                $str = $this->createButtonRules($obj, $key);
                break;
            case 'hotspot':
                $str = $this->createHotspotRules($obj, $key);
                break;
            case 'countdown':
                $str = $this->createCountdownRules($obj, $key);
                break;
            case 'counter':
                $str = $this->createCounterRules($obj, $key);
                break;
            case 'text':
            case 'headline':
                $str = $this->createTextRules($obj, $key);
                break;
            case 'reading-progress-bar':
                $str = $this->createReadingProgressBarRules($obj, $key);
                break;
            case 'progress-bar':
                $str = $this->createProgressBarRules($obj, $key);
                break;
            case 'progress-pie':
                $str = $this->createProgressPieRules($obj, $key);
                break;
            case 'social':
                $str = $this->createSocialRules($obj, $key);
                break;
            case 'disqus':
            case 'vk-comments':
            case 'facebook-comments':
            case 'hypercomments':
            case 'modules':
            case 'custom-html':
            case 'gallery':
            case 'forms':
                $str = $this->createModulesRules($obj, $key);
                break;
            case 'language-switcher':
                $str = $this->createLanguageSwitcherRules($obj, $key);
                break;
            case 'currency-switcher':
                $str = $this->createCurrencySwitcherRules($obj, $key);
                break;
            case 'comments-box':
            case 'reviews':
                $str = $this->createCommentsBoxRules($obj, $key);
                break;
            case 'instagram':
                $str = '';
                break;
            case 'simple-gallery':
            case 'field-simple-gallery':
            case 'product-gallery':
                $str = $this->createSimpleGalleryRules($obj, $key);
                break;
            case 'mega-menu-section':
                $str = $this->createMegaMenuSectionRules($obj, $key);
                break;
            case 'flipbox':
                $str = $this->createFlipboxRules($obj, $key);
                break;
            case 'error-message':
                $str = $this->createErrorRules($obj, $key);
                break;
            case 'search-result-headline':
                $str = $this->createSearchHeadlineRules($obj, $key);
                break;
            default:
                $str = $this->createSectionRules($obj, $key);
        }
        
        return $str;
    }

    public function createRules($obj)
    {
        $str = '';
        $this->item = null;
        if (isset($obj->padding)) {
            $this->padding($obj->padding);
        }
        if (!empty($this->css)) {
            $str .= "body {";
            $str .= $this->css;
            $str .= "}";
        }
        $str .= $this->createTypography($obj);
        $str .= $this->backgroundRule($obj, 'body', '../../../../');
        
        return $str;
    }

    public function getTypographyRule($obj, $not = '', $ind = null, $variables = false, $varKey = '')
    {
        $str = "";
        $family = $weight = $custom = '';
        $font = $ind ? $ind : 'body';
        if (isset($obj->{'font-family'}) && $obj->{'font-family'} == '@default') {
            $custom = $this->getTextParentCustom($font);
        } else if (isset($obj->custom)) {
            $custom = $obj->custom;
        }
        foreach ($obj as $key => $value) {
            if ($key == $not || $key == 'custom' || $key == 'type' || ($key == 'color' && !empty($this->object->{$ind}->type))
                || ($key == 'gradient' && empty($this->object->{$ind}->type))) {
                continue;
            }
            if ($value !== '@default' && $key != 'gradient') {
                $str .= ($variables ? $varKey.'-' : '').$key.": ";
            }
            if ($key == 'font-family') {
                $family = $value;
                if ($family == '@default') {
                    $family = $this->getTextParentFamily($font);
                } else if (gridboxHelper::$website->google_fonts == 0 && empty($custom)) {
                    $str .= "'Helvetica', 'Arial', sans-serif";
                } else {
                    $str .= "'".str_replace('+', ' ', $family)."'";
                }
            } else if ($key == 'font-weight') {
                $weight = $value;
                if ($weight == '@default') {
                    $weight = $this->getTextParentWeight($font);
                } else {
                    $str .= str_replace('i', '', $weight);
                }
            } else if ($key == 'color' && empty($this->object->{$ind}->type)) {
                $str .= $this->getCorrectColor($value).';';
                $str .= 'background-image: none';
            } else if ($key == 'gradient' && !empty($this->object->{$ind}->type)) {
                $this->gradient($this->object->{$ind}->gradient);
                $str .= $this->css;
                $str .= '-webkit-background-clip: text;';
                $str .= 'color: transparent';
            } else if ($key == 'letter-spacing' || $key == 'font-size' || $key == 'line-height') {
                $str .= $this->getValueUnits($value);
            } else {
                $str .=  $value;
            }
            if ($value == '@default' && $variables) {
                $str .= $varKey.'-'.$key.": ".($key == 'font-family' ? "'".str_replace('+', ' ', $family)."'" : str_replace('i', '', $weight));
            }
            if ($value !== '@default' || $variables) {
                $str .= ";";
            }
        }
        if (!empty($family)) {
            if (!empty($custom) && $custom != 'web-safe-fonts') {
                if (!isset(gridboxHelper::$customFonts[$family])) {
                    gridboxHelper::$customFonts[$family] = array();
                }
                if (!in_array($weight, gridboxHelper::$customFonts[$family])) {
                    gridboxHelper::$customFonts[$family][$weight] = $custom;
                }
            } else if (empty($custom)) {
                if (!isset(gridboxHelper::$fonts[$family])) {
                    gridboxHelper::$fonts[$family] = array();
                }
                if (!in_array($weight, gridboxHelper::$fonts[$family])) {
                    gridboxHelper::$fonts[$family][] = $weight;
                }
            }
        }
        
        return $str;
    }

    public function getMediaRules()
    {
        $str = '';
        foreach ($this->media as $media) {
            if (empty($media['css'])) {
                continue;
            }
            $str .= "@media (max-width: ".$media['width']."px) {";
            $str .= $media['css'];
            $str .= "}";
        }

        return $str;
    }

    public function prepareMediaRules()
    {
        $this->media = [];
        foreach (gridboxHelper::$breakpoints as $ind => $value) {
            $this->media[$ind] = ['width' => $value, 'css' => ''];
        }
    }

    public function getAnimationRules($obj, $key)
    {
        $str = $css = '';
        if (isset($obj->appearance->duration)) {
            $css .= "animation-duration: ".$obj->appearance->duration."s;";
        }
        if (isset($obj->appearance->delay)) {
            $css .= "animation-delay: ".$obj->appearance->delay."s;";
        }
        if (isset($obj->appearance->effect)) {
            $css .= "opacity: ".(!empty($obj->appearance->effect) ? 0 : 1).";";
        }
        if (!empty($css)) {
            $str .= "#".$key." {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->appearance->effect) && !empty($obj->appearance->effect)) {
            $str .= "#".$key.".visible {opacity : 1;}";
        }

        return $str;
    }

    public function setMediaRules($obj, $key, $callback)
    {
        $empty = new stdClass();
        $this->object = gridboxHelper::object_extend($empty, $obj->desktop);
        $this->breakpoint = 'desktop';
        $str = $this->{$callback}($obj->desktop, $key);
        if (isset($obj->desktop->positioning)) {
            $str .= $this->positioningRules($obj->desktop, $key);
        }
        $str .= $this->getAnimationRules($obj->desktop, $key);
        if ((bool)gridboxHelper::$website->disable_responsive) {
            return $str;
        }
        foreach (gridboxHelper::$breakpoints as $ind => $value) {
            $this->breakpoint = $ind;
            if (!isset($obj->{$ind})) {
                continue;
            }
            $this->object = gridboxHelper::object_extend($this->object, $obj->{$ind});
            $css = $this->{$callback}($obj->{$ind}, $key);
            if (isset($obj->{$ind}->positioning)) {
                $css .= $this->positioningRules($obj->{$ind}, $key);
            }
            $css .= $this->getAnimationRules($obj->{$ind}, $key);
            if (empty($css)) {
                continue;
            }
            $this->media[$ind]['css'] .= $css;
        }
        
        return $str;
    }

    public function setItemsVisability($disable, $display)
    {
        $str = "display : ".($disable == 1 ? "none" : $display).";";

        return $str;
    }

    public function positioningRules($obj, $selector)
    {
        if (empty($this->item->positioning->position)) {
            return '';
        }
        $str = $css = "";
        if ($this->breakpoint == 'desktop') {
            $css .= "position: ".$this->item->positioning->position.";";
        }
        if (isset($obj->positioning->z)) {
            $css .= "z-index: ".($obj->positioning->z * 1 + 10).";";
        }
        if (isset($obj->positioning->x)) {
            $h = $this->object->positioning->horizontal;
            $css .= (($h == '' || $h == 'left' || $h == 'center') ? 'left' : 'right').": ".$this->getValueUnits($obj->positioning->x).";";
        }
        if (isset($obj->positioning->y)) {
            $v = $this->object->positioning->vertical;
            $css .= (($v == '' || $v == 'top' || $v == 'center') ? 'top' : 'bottom').": ".$this->getValueUnits($obj->positioning->y).";";

        }
        if (isset($obj->positioning->horizontal)) {
            $h = $obj->positioning->horizontal;
            $css .= (($h == '' || $h == 'left' || $h == 'center') ? 'right' : 'left').": auto;";
        }
        if (isset($obj->positioning->vertical)) {
            $v = $obj->positioning->vertical;
            $css .= (($v == '' || $v == 'top' || $v == 'center') ? 'bottom' : 'top').": auto;";
        }
        if (isset($obj->positioning->width)) {
            $css .= 'width: '.$this->getValueUnits($obj->positioning->width).' !important;';
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->positioning->z)) {
            $str .= "#".$this->item->positioning->row.", #".$this->item->positioning->section." {";
            $str .= "z-index: ".($obj->positioning->z * 1 + 1).";";
            $str .= "}";
        }

        return $str;
    }

    public function backgroundRule($obj, $selector, $up)
    {
        $str = '';
        if (isset($obj->background) && isset($obj->background->type) && ($obj->background->type == 'video' || $this->breakpoint != 'desktop')) {
            $str .= $selector." > .ba-video-background {";
            $str .= "display: ".($obj->background->type == 'video' ? 'block' : 'none').";";
            $str .= "}";
        }
        $empty = new stdClass();
        $bg = isset($obj->background) ? gridboxHelper::object_extend($empty, $obj->background) : null;
        $empty = new stdClass();
        $states = null;
        $type = $this->object->background->type;
        if (isset($obj->{'background-states'})) {
            $states = gridboxHelper::object_extend($empty, $obj->{'background-states'});
        }
        $image = isset($bg->image->image) ? $bg->image->image : null;
        $image = isset($obj->image->image) ? $obj->image->image : $image;
        if (isset($bg->image->image) && $image) {
            $bg->image->image = $image;
        }
        if ($bg && !$states && isset($this->object->{'background-states'})) {
            $states = gridboxHelper::object_extend(new stdClass(), $this->object->{'background-states'});
        }
        if (!$states) {
            $states = new stdClass();
        }
        if (!isset($states->default)) {
            $states->default = new stdClass();
            $states->default->color = isset($bg->color) ? $bg->color : null;
            $states->default->image = $image;
        }
        if ($type == 'image' && $this->breakpoint != 'desktop' && !empty($states->default->image)) {
            $bg->image = gridboxHelper::object_extend(new stdClass(), $this->object->background->image);
        }
        if (isset($bg->image->image)) {
            unset($bg->image->image);
        }
        if (isset($bg->color)) {
            unset($bg->color);
        }
        if ($type == 'image' && $this->breakpoint != 'desktop' && gridboxHelper::$website->adaptive_images == 1) {
            $image = isset($this->object->background->image->image) ? $this->object->background->image->image : null;
            $image = isset($this->object->image->image) ? $this->object->image->image : $image;
            $image = isset($this->object->{'background-states'}->default->image) ? $this->object->{'background-states'}->default->image : $image;
            $states->default->image = $image;
        }
        if ($type == 'image' && $this->breakpoint != 'desktop' && gridboxHelper::$website->adaptive_images == 1
            && isset($this->object->{'background-states'}->hover)) {
            $image = isset($this->object->{'background-states'}->hover->image) ? $this->object->{'background-states'}->hover->image : $image;
            $states->hover = isset($states->hover) ? $states->hover : new stdClass();
            $states->hover->image = $image;
        }
        $states->default->type = $type;
        if ($states && isset($states->hover)) {
            $states->hover->type = $type;
        }
        $css = '';
        if ($type != 'gradient') {
            $css .= $this->get('background', $states, 'default');
        }
        if (($type == 'video' || $type == 'none') && !isset($bg->type)) {
            $css = '';
        }
        if ($type == 'gradient' && (isset($bg->gradient) || isset($bg->type))) {
            $gradient = isset($bg->gradient) ? $bg->gradient : new stdClass();
            $bg->gradient = gridboxHelper::object_extend($this->object->background->gradient, $gradient);
            $this->backgrounds($bg);
            $css .= $this->css;
        } else if ($type == 'image' && isset($bg->image)) {
            $this->backgroundImage($bg->image);
            $css .= $this->css;
        } else if ($type == 'blur' && !isset($states->default->blur) && isset($bg->blur)) {
            $this->backgroundBlur($bg->blur);
            $css .= $this->css;
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= $selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($selector.":hover", 'hover');
        $str .= $this->getTransitionRule($selector);
        if ($this->item && $this->item->parallax && $this->item->parallax->enable) {
            $css = $this->get('background', $states, 'default');
            if (!empty($css)) {
                $str .= $selector." > .parallax-wrapper .parallax {";
                $str .= $css;
                $str .= "}";
                $str .= $this->getStateRule($selector.":hover > .parallax-wrapper .parallax", 'hover');
                $str .= $this->getTransitionRule($selector." > .parallax-wrapper .parallax");
            }
        }
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= $selector." > .ba-overlay {";
            $str .= $css;
            $str .= '}';
            $str .= $this->getStateRule($selector.":hover > .ba-overlay", 'hover');
            $str .= $this->getTransitionRule($selector." .ba-overlay");
        }
        
        return $str;
    }

    public function createCurrencySwitcherRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getCurrencySwitcherRules');

        return $str;
    }

    public function createLanguageSwitcherRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getLanguageSwitcherRules');

        return $str;
    }

    public function createModulesRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getModulesRules');

        return $str;
    }

    public function createErrorRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $str = $this->setMediaRules($obj, $key, 'getErrorRules');
        if ($desktop->view->message) {
            $str .= "#".$key." p.ba-error-message {";
            $str .= "display: block;";
            $str .= "}";
        }
        if ($desktop->view->code) {
            $str .= "#".$key." h1.ba-error-code {";
            $str .= "display: block;";
            $str .= "}";
        }

        return $str;
    }

    public function createTextRules($obj, $key)
    {
        $array = ['h1' ,'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'links'];
        if (isset($obj->global) && $obj->global) {
            unset($obj->global);
            foreach ($array as $value) {
                unset($obj->desktop->{$value});
                foreach (gridboxHelper::$breakpoints as $ind => $property) {
                    unset($obj->{$ind}->{$value});
                }
            }
        }
        if (!isset($obj->desktop->p)) {
            foreach ($array as $value) {
                if ($value == 'links') {
                    continue;
                }
                $obj->desktop->{$value} = new stdClass();
                foreach (gridboxHelper::$breakpoints as $ind => $property) {
                    if (!isset($obj->{$ind})) {
                        $obj->{$ind} = new stdClass();
                    }
                    $obj->{$ind}->{$value} = new stdClass();
                }
            }
        }
        $str = $this->setMediaRules($obj, $key, 'getTextRules');

        return $str;
    }

    public function createHotspotRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getHotspotRules');

        return $str;
    }

    public function createButtonRules($obj, $key)
    {
        if ($obj->type == 'overlay-button' && isset($obj->trigger) && $obj->trigger == 'image') {
            $this->prepareBorder($obj->desktop->border);
            $str = $this->setMediaRules($obj, $key, 'getImageRules');
        } else {
            $this->prepareColors($obj);
            $this->prepareBorder($obj->desktop->border);
            $str = $this->setMediaRules($obj, $key, 'getButtonRules');
            if (isset($obj->icon) && is_object($obj->icon)) {
                $str .= "#".$key." .ba-button-wrapper a {";
                if ($obj->icon->position == '') {
                    $str .= 'flex-direction: row-reverse;';
                } else {
                    $str .= 'flex-direction: row;';
                }
                $str .= "}";
                $str .= "#".$key." .ba-button-wrapper a i {";
                if ($obj->icon->position == '') {
                    $str .= 'margin: 0 10px 0 0;';
                } else {
                    $str .= 'margin: 0 0 0 10px;';
                }
                $str .= "}";
            }
        }

        return $str;
    }

    public function createIconRules($obj, $key)
    {
        $this->prepareColors($obj);
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getIconRules');

        return $str;
    }

    public function createVideoRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getVideoRules');

        return $str;
    }

    public function createMapRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getMapRules');

        return $str;
    }

    public function createIconListRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getIconListRules');
        $str .= "#".$key." .ba-icon-list-wrapper ul li a:hover span {";
        $str .= "color : inherit;";
        $str .= "}";
        $str .= "#".$key." .ba-icon-list-wrapper ul li i, #".$key." ul li a:before, #";
        $str .= $key." ul li.list-item-without-link:before {";
        $str .= "order: ".($obj->icon->position == '' ? 0 : 2).";";
        $str .= "margin-".($obj->icon->position == '' ? 'right' : 'left').": 20px;";
        $str .= "}";

        return $str;
    }

    public function createStarRatingsRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $str = $this->setMediaRules($obj, $key, 'getStarRatingsRules');
        if ($desktop->view->rating == 1) {
            $str .= "#".$key." .rating-wrapper {";
            $str .= "display: inline;";
            $str .= "}";
        }
        if ($desktop->view->votes == 1) {
            $str .= "#".$key." .votes-wrapper {";
            $str .= "display: inline;";
            $str .= "}";
        }

        return $str;
    }

    public function createFieldRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getFieldRules');

        return $str;
    }

    public function createFieldsFilterRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $this->prepareBorder($desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getFieldsFilterRules');
        foreach ($obj->fields as $i => $field) {
            $str .= '#'.$key.' .ba-field-filter[data-id="'.$field.'"] {';
            $str .= "order: ".$i.";";
            $str .= "}";
        }
        $visibleField = null;
        foreach ($obj->fields as $field) {
            if ($desktop->fields->{$field}) {
                $visibleField = $field;
                $str .= '#'.$key.' .ba-field-filter[data-id="'.$field.'"] {';
                $str .= "display: flex;";
                $str .= "}";
            }
        }
        if ($visibleField) {
            $str .= '#'.$key.' .ba-field-filter[data-id="'.$visibleField.'"] {';
            $str .= "margin-bottom: 0;";
            $str .= "}";
        }

        return $str;
    }

    public function createEventCalendarRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getEventCalendarRules');

        return $str;
    }

    public function createPreloaderRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getPreloaderRules');

        return $str;
    }

    public function createBreadcrumbsRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->style);
        if (!isset($obj->desktop->style->colors->active)) {
            $empty = new stdClass();
            $obj->desktop->style->colors->active = gridboxHelper::object_extend($empty, $obj->desktop->style->colors->hover);
        }
        $str = $this->setMediaRules($obj, $key, 'getBreadcrumbsRules');

        return $str;
    }

    public function createCheckoutFormRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getCheckoutFormRules');

        return $str;
    }

    public function createSearchRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getSearchRules');

        return $str;
    }

    public function createLogoRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getLogoRules');

        return $str;
    }

    public function createLoginRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getLoginRules');

        return $str;
    }

    public function createScrollTopRules($obj, $key)
    {
        $this->prepareColors($obj);
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getScrollTopRules');

        return $str;
    }

    public function createCountdownRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getCountdownRules');

        return $str;
    }

    public function createCounterRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getCounterRules');

        return $str;
    }

    public function createReadingProgressBarRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getReadingProgressBarRules');

        return $str;
    }

    public function createProgressBarRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getProgressBarRules');
        if ($obj->desktop->display->label) {
            $str .= "#".$key." .progress-bar-title {";
            $str .= 'display: inline-block;';
            $str .= "}";
        }
        if ($obj->desktop->display->target) {
            $str .= "#".$key." .progress-bar-number {";
            $str .= 'display: inline-block;';
            $str .= "}";
        }

        return $str;
    }

    public function createProgressPieRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getProgressPieRules');
        if ($obj->desktop->display->target) {
            $str .= "#".$key." .progress-pie-number {";
            $str .= 'display: inline-block;';
            $str .= "}";
        }

        return $str;
    }

    public function createSocialRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getModulesRules');
        if ($obj->view->counters) {
            $str .= '#'.$key.' .social-counter {display:inline-block;}';
        }

        return $str;
    }

    public function createMegaMenuSectionRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'createMegaMenuRules');
        if (isset($obj->parallax) && $obj->parallax->enable) {
            $pHeight = 100 + $obj->parallax->offset * 2 * 200;
            $pTop = $obj->parallax->offset * 2 * -100;
            $str .= "#".$key." > .parallax-wrapper.scroll .parallax {";
            $str .= "height: ".$pHeight."%;";
            $str .= "top: ".$pTop."%;";
            $str .= "}";
        }
        $str .= "#".$key.' {';
        $str .= "width: ".$this->getValueUnits($obj->view->width).";";
        $str .= "}";
        $str .= '.tabs-content-wrapper[data-id="'.$key.'"] {';
        $str .= "--megamenu-width: ".$this->getValueUnits($obj->view->width).";";
        $str .= "}";
        
        return $str;
    }

    public function createFlipboxRules($obj, $key)
    {
        $this->setFlipboxSide($obj, $obj->side);
        $empty = new stdClass();
        $object = gridboxHelper::object_extend($empty, $obj);
        $str = $this->setMediaRules($obj, $key, 'getFlipboxRules');
        $this->setFlipboxSide($object, 'frontside');
        $key1 = $key.' > .ba-flipbox-wrapper > .ba-flipbox-frontside > .ba-grid-column-wrapper > .ba-grid-column';
        $key2 = $key.' > .ba-flipbox-wrapper > .ba-flipbox-backside > .ba-grid-column-wrapper > .ba-grid-column';
        if (isset($obj->parallax) && $obj->parallax->enable) {
            $pHeight = 100 + $object->parallax->offset * 2 * 200;
            $pTop = $object->parallax->offset * 2 * -100;
            $str .= "#".$key1." > .parallax-wrapper.scroll .parallax, #".$key2." > .parallax-wrapper.scroll .parallax {";
            $str .= "height: ".$pHeight."%;";
            $str .= "top: ".$pTop."%;";
            $str .= "}";
        }
        $str .= $this->setMediaRules($object, $key1, 'getFlipsidesRules');
        $this->setFlipboxSide($object, 'backside');
        $str .= $this->setMediaRules($object, $key2, 'getFlipsidesRules');
        
        return $str;
    }

    public function createSearchHeadlineRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getSearchHeadlineRules');

        return $str;
    }

    public function createTabsRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getTabsRules');
        $str .= "#".$key." ul.nav.nav-tabs li a:hover {";
        $str .= "color : ".$this->getCorrectColor($obj->desktop->hover->color).";";
        $str .= "}";
        if ($obj->desktop->icon->position == 'icon-position-left') {
            $str .= '#'.$key.' .ba-tabs-wrapper > ul li a > span {direction: rtl;display: inline-flex;';
            $str .= 'flex-direction: row;}';
            $str .= '#'.$key.' .ba-tabs-wrapper > ul li a > span i {margin-bottom:0;}';
        } else if ($obj->desktop->icon->position == 'icon-position-top') {
            $str .= '#'.$key.' .ba-tabs-wrapper > ul li a > span {display: inline-flex;';
            $str .= 'flex-direction: column-reverse;}';
            $str .= '#'.$key.' .ba-tabs-wrapper > ul li a > span i {margin-bottom:10px;}';
        } else {
            $str .= '#'.$key.' .ba-tabs-wrapper > ul li a > span {direction: ltr;display: inline-flex;';
            $str .= 'flex-direction: row;}';
            $str .= '#'.$key.' .ba-tabs-wrapper > ul li a > span i {margin-bottom:0;}';
        }

        return $str;
    }

    public function createAccordionRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'getAccordionRules');

        return $str;
    }

    public function createWeatherRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $str = $this->setMediaRules($obj, $key, 'getWeatherRules');
        if ($desktop->view->wind) {
            $str .= "#".$key." .weather-info .wind {";
            $str .= "display : inline;";
            $str .= "}";
        }
        if ($desktop->view->humidity) {
            $str .= "#".$key." .weather-info .humidity {";
            $str .= "display : inline-block;";
            $str .= "}";
        }
        if ($desktop->view->pressure) {
            $str .= "#".$key." .weather-info .pressure {";
            $str .= "display : inline-block;";
            $str .= "}";
        }
        for ($i = 0; $i < $desktop->view->forecast; $i++) {
            $str .= "#".$key."  .forecast:nth-child(".($i + 1).")";
            if ($i != $desktop->view->forecast - 1 ) {
                $str .= ",";
            }
        }
        $str .= " {display: ".($desktop->view->layout == 'forecast-block' ? 'inline-block' : 'block').";";
        $str .= "}";

        return $str;
    }

    public function createCommentsBoxRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getCommentsBoxRules');

        return $str;
    }

    public function createSimpleGalleryRules($obj, $key)
    {
        if (isset($obj->desktop->border)) {
            $this->prepareBorder($obj->desktop->border);
        }
        $str = $this->setMediaRules($obj, $key, 'getSimpleGalleryRules');
        $str .= '#'.$key.' .ba-instagram-image {';
        $str .= 'cursor: zoom-in;';
        $str .= '}';
        
        return $str;
    }

    public function createLottieRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getLottieRules');

        return $str;
    }

    public function createImageRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $str = $this->setMediaRules($obj, $key, 'getImageRules');
        if (isset($obj->link) && !empty($obj->link->link)) {
            $str .= '#'.$key.' .ba-image-wrapper { cursor: pointer; }';
        } else if (isset($obj->popup) && $obj->popup) {
            $str .= '#'.$key.' .ba-image-wrapper { cursor: zoom-in; }';
        } else {
            $str .= '#'.$key.' .ba-image-wrapper { cursor: default; }';
        }

        return $str;
    }

    public function createOnePageRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->nav);
        if (!isset($obj->desktop->nav->colors->active)) {
            $empty = new stdClass();
            $obj->desktop->nav->colors->active = gridboxHelper::object_extend($empty, $obj->desktop->nav->colors->hover);
        }
        $this->prepareBorder($obj->desktop->nav->border);
        $str = $this->setMediaRules($obj, $key, 'getOnePageRules');
        if (!(bool)gridboxHelper::$website->disable_responsive) {
            $str .= "@media (max-width: ".gridboxHelper::$menuBreakpoint."px) {";
            $str .= "#".$key." .ba-hamburger-menu .main-menu {";
            $str .= "background-color : ".$this->getCorrectColor($obj->hamburger->background).";";
            if (isset($obj->hamburger->width)) {
                $str .= "width: ".$this->getValueUnits($obj->hamburger->width).";";
                $str .= $this->get('shadow', $obj->hamburger->shadow, 'default');
                $str .= $this->get('padding', $obj->hamburger->padding, 'default');
            }
            $str .= "}";
            if (isset($obj->hamburger->overlay)) {
                $str .= "#".$key." > .ba-menu-backdrop {";
                $str .= "background-color : ".$this->getCorrectColor($obj->hamburger->overlay).";";
                $str .= "}";
            }
            $str .= "#".$key." .ba-hamburger-menu .open-menu {";
            $str .= "color : ".$this->getCorrectColor($obj->hamburger->open).";";
            $str .= "text-align : ".$obj->hamburger->{'open-align'}.";";
            if (isset($obj->hamburger->icons)) {
                $str .= "font-size: ".$this->getValueUnits($obj->hamburger->icons->open->size).";";
            }
            $str .= "}";
            $str .= "#".$key." .ba-hamburger-menu .close-menu {";
            $str .= "color : ".$this->getCorrectColor($obj->hamburger->close).";";
            $str .= "text-align : ".$obj->hamburger->{'close-align'}.";";
            if (isset($obj->hamburger->icons)) {
                $str .= "font-size: ".$this->getValueUnits($obj->hamburger->icons->close->size).";";
            }
            $str .= "}";
            $str .= "}";
        }

        return $str;
    }

    public function createMenuRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->nav);
        $this->prepareColors($obj->desktop->sub);
        $this->prepareBorder($obj->desktop->nav->border);
        $this->prepareBorder($obj->desktop->sub->border);
        if (isset($obj->desktop->dropdown->border)) {
            $this->prepareBorder($obj->desktop->dropdown->border);
        }
        if (!isset($obj->desktop->nav->colors->active)) {
            $empty = new stdClass();
            $obj->desktop->nav->colors->active = gridboxHelper::object_extend($empty, $obj->desktop->nav->colors->hover);
        }
        if (!isset($obj->desktop->sub->colors->active)) {
            $empty = new stdClass();
            $obj->desktop->sub->colors->active = gridboxHelper::object_extend($empty, $obj->desktop->sub->colors->hover);
        }
        if (!isset($obj->desktop->dropdown->padding)) {
            $obj->desktop->dropdown = $obj->desktop->dropdown->default;
        }
        $str = $this->setMediaRules($obj, $key, 'getMenuRules');
        $str .= "#".$key." li.deeper.parent > ul {";
        $str .= "width: ".$this->getValueUnits($obj->desktop->dropdown->width).";";
        $str .= "background-color : ".$this->getCorrectColor($obj->desktop->background->color).";";
        $str .= "}";
        $padding = $obj->desktop->dropdown->padding;
        $top = isset($padding->default) ? $padding->default->top : $padding->top;
        $str .= "#".$key." li.deeper.parent > ul > .deeper:hover > ul {";
        $str .= "top : -".$this->getValueUnits($top).";";
        $str .= "}";
        if (!(bool)gridboxHelper::$website->disable_responsive) {
            $str .= "@media (max-width: ".gridboxHelper::$menuBreakpoint."px) {";
            $str .= "#".$key." > .ba-hamburger-menu > .main-menu {";
            $str .= "background-color : ".$this->getCorrectColor($obj->hamburger->background).";";
            if (isset($obj->hamburger->width)) {
                $str .= "width: ".$this->getValueUnits($obj->hamburger->width).";";
                $str .= $this->get('shadow', $obj->hamburger->shadow, 'default');
                $str .= $this->get('padding', $obj->hamburger->padding, 'default');
            }
            $str .= "}";
            if (isset($obj->hamburger->overlay)) {
                $str .= "#".$key." > .ba-menu-backdrop {";
                $str .= "background-color : ".$this->getCorrectColor($obj->hamburger->overlay).";";
                $str .= "}";
            }
            $str .= "#".$key." .ba-hamburger-menu .open-menu {";
            $str .= "color : ".$this->getCorrectColor($obj->hamburger->open).";";
            $str .= "text-align : ".$obj->hamburger->{'open-align'}.";";
            if (isset($obj->hamburger->icons)) {
                $str .= "font-size: ".$this->getValueUnits($obj->hamburger->icons->open->size).";";
            }
            $str .= "}";
            $str .= "#".$key." .ba-hamburger-menu .close-menu {";
            $str .= "color : ".$this->getCorrectColor($obj->hamburger->close).";";
            $str .= "text-align : ".$obj->hamburger->{'close-align'}.";";
            if (isset($obj->hamburger->icons)) {
                $str .= "font-size: ".$this->getValueUnits($obj->hamburger->icons->close->size).";";
            }
            $str .= "}";
            $str .= "}";
        }

        return $str;
    }

    public function createContentRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->arrows);
        $this->prepareBorder($obj->desktop->border);
        $this->prepareBorder($obj->desktop->arrows->border);
        $str = $this->setMediaRules($obj, $key, 'getContentSliderRules');
        $i = 1;
        foreach ($obj->slides as $ind => $slide) {
            if (isset($slide->unpublish) && $slide->unpublish) {
                continue;
            }
            $query = "#".$key." > .slideshow-wrapper > .ba-slideshow > .slideshow-content > li.item:nth-child(".$i++.")";
            $str .= $this->setMediaRules($slide, $query, 'getContentSliderItemsRules');
        }

        return $str;
    }

    public function createRecentSliderRules($obj, $key)
    {
        if (!isset($obj->info)) {
            $obj->info = array('author', 'date', 'category', 'hits', 'comments');
        }
        if (!isset($obj->desktop->store)) {
            $obj->desktop->store = new stdClass();
            $obj->desktop->badge = true;
            $obj->desktop->wishlist = true;
            $obj->desktop->price = true;
            $obj->desktop->cart = true;
        }
        $this->prepareColors($obj->desktop->button);
        $this->prepareColors($obj->desktop->arrows);
        $this->prepareBorder($obj->desktop->arrows->border);
        $this->prepareBorder($obj->desktop->button->border);
        $str = $this->setMediaRules($obj, $key, 'getRecentSliderRules');
        if (isset($obj->fields)) {
            foreach ($obj->fields as $i => $value) {
                $str .= '#'.$key.' .ba-blog-post-field-row[data-id="'.$value.'"] {';
                $str .= "order: ".$i.";";
                $str .= "}";
            }
        }
        if (isset($obj->info)) {
            foreach ($obj->info as $i => $value) {
                $str .= '#'.$key.' .ba-blog-post-'.$value.' {';
                $str .= "order: ".$i.";";
                $str .= "}";
            }
        }
        $desktop = $obj->desktop;
        if ($desktop->store->badge) {
            $str .= "#".$key." .ba-blog-post-badge-wrapper {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->store->wishlist) {
            $str .= "#".$key." .ba-blog-post-wishlist-wrapper {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->store->price) {
            $str .= "#".$key." .ba-blog-post-add-to-cart-price {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->store->cart) {
            $str .= "#".$key." .ba-blog-post-add-to-cart-button {";
            $str .= "display:flex;";
            $str .= "}";
        }
        foreach ($desktop->store as $ind => $value) {
            if ($ind == 'badge' || $ind == 'wishlist' || $ind == 'price' || $ind == 'cart' || !$value) {
                continue;
            }
            $str .= "#".$key.' .ba-blog-post-product-options[data-key="'.$ind.'"] {';
            $str .= "display:flex;";
            $str .= "}";
        }
        if (!isset($desktop->view->author)) {
            $desktop->view->author = false;
        }
        if (!isset($desktop->view->comments)) {
            $desktop->view->comments = false;
        }
        if (!isset($desktop->view->reviews)) {
            $desktop->view->reviews = false;
        }
        if ($desktop->view->author) {
            $str .= "#".$key." .ba-blog-post-info-wrapper span.ba-blog-post-author {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->date) {
            $str .= "#".$key." .ba-blog-post-info-wrapper span.ba-blog-post-date {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->category) {
            $str .= "#".$key." .ba-blog-post-info-wrapper span.ba-blog-post-category {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->comments) {
            $str .= "#".$key." .ba-blog-post-info-wrapper span.ba-blog-post-comments {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        foreach ($obj->info as $i => $value) {
            if (isset($desktop->view->{$value}) && $desktop->view->{$value}) {
                for ($j = $i + 1; $j < count($obj->info); $j++) {
                    $str .= "#".$key." .ba-blog-post-".$obj->info[$j].":before {";
                    $str .= 'margin: 0 10px;content: "'.($obj->info[$j] == 'author' ? '' : '\2022').'";color: inherit;';
                    $str .= "}";
                }
                break;
            }
        }
        if ($desktop->view->reviews) {
            $str .= "#".$key." .ba-blog-post-reviews {";
            $str .= 'display:flex;';
            $str .= "}";
        }
        if ($desktop->view->intro) {
            $str .= "#".$key." .ba-blog-post-intro-wrapper {";
            $str .= 'display:block;';
            $str .= "}";
        }
        if (isset($desktop->fields)) {
            $visibleField = null;
            foreach ($obj->fields as $i => $value) {
                if ($desktop->fields->{$value}) {
                    $visibleField = $value;
                    $str .= '#'.$key.' .ba-blog-post-field-row[data-id="'.$value.'"] {';
                    $str .= "display: flex;";
                    $str .= "margin-bottom: 10px;";
                    $str .= "}";
                }
            }
            if ($visibleField) {
                $str .= '#'.$key.' .ba-blog-post-field-row[data-id="'.$visibleField.'"] {';
                $str .= "margin-bottom: 0;";
                $str .= "}";
            }
        }
        if ($desktop->view->button) {
            $str .= "#".$key." .ba-blog-post-button-wrapper a {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($obj->type == 'recently-viewed-products' && $desktop->view->arrows) {
            $str .= "#".$key." .enabled-carousel-sliding .ba-slideset-nav {";
            $str .= "display: block;";
            $str .= "}";
            $str .= "#".$key." .ba-slideset-nav {";
            $str .= "display: none;";
            $str .= "}";
        } else if ($desktop->view->arrows) {
            $str .= "#".$key." .ba-slideset-nav {";
            $str .= "display: block;";
            $str .= "}";
        }
        if ($desktop->view->dots) {
            $str .= "#".$key." .ba-slideset-dots {";
            $str .= "display: flex;";
            $str .= "}";
        }

        return $str;
    }

    public function createTestimonialsRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->arrows);
        $this->prepareBorder($obj->desktop->border);
        $this->prepareBorder($obj->desktop->image->border);
        $this->prepareBorder($obj->desktop->arrows->border);
        $str = $this->setMediaRules($obj, $key, 'getTestimonialsRules');
        $this->breakpoint = 'desktop';
        $ind = 1;
        foreach ($obj->slides as $slide) {
            if (isset($slide->unpublish) && $slide->unpublish) {
                continue;
            }
            if (!empty($slide->image)) {
                $str .= "#".$key." li.item:nth-child(".$ind.") .testimonials-img,";
                $str .= " #".$key." ul.style-6 .ba-slideset-dots > div:nth-child(".$ind.") {";
                $str .= "background-image: url(".$this->setBackgroundImage($slide->image).");";
                $str .= "}"; 
            }
            $ind++;
        }
        if ($obj->desktop->view->arrows == 1) {
            $str .= "#".$key." .ba-slideset-nav {";
            $str .= 'display:block;';
            $str .= "}";
        }
        if ($obj->desktop->view->dots == 1) {
            $str .= "#".$key." .ba-slideset-dots {";
            $str .= 'display:flex;';
            $str .= "}";
        }

        return $str;
    }

    public function createCarouselRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->button);
        $this->prepareColors($obj->desktop->arrows);
        $this->prepareBorder($obj->desktop->arrows->border);
        $this->prepareBorder($obj->desktop->button->border);
        $str = $this->setMediaRules($obj, $key, 'getCarouselRules');
        if ($obj->desktop->view->arrows) {
            $str .= "#".$key." .ba-slideset-nav {";
            $str .= "display:block";
            $str .= "}";
        }
        if ($obj->desktop->view->dots) {
            $str .= "#".$key." .ba-slideset-dots {";
            $str .= "display:flex";
            $str .= "}";
        }

        return $str;
    }

    public function createBeforeAfterSliderRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->slider);
        $str = $this->setMediaRules($obj, $key, 'getBeforeAfterSliderRules');

        return $str;
    }

    public function createSlideshowRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->button);
        $this->prepareColors($obj->desktop->arrows);
        $this->prepareBorder($obj->desktop->arrows->border);
        $this->prepareBorder($obj->desktop->button->border);
        $str = $this->setMediaRules($obj, $key, 'getSlideshowRules');
        if ($obj->type == 'field-slideshow' || $obj->type == 'product-slideshow') {
            $str .= "body.com_gridbox.gridbox #".$key." li.item .ba-slideshow-img,";
            $str .= "body.com_gridbox.gridbox #".$key." .thumbnails-dots div {";
            $str .= "background-image: url(".JUri::root()."components/com_gridbox/assets/images/default-theme.png);";
            $str .= "}";
            for ($i = 0; $i < 100; $i++) {
                $str .= "body:not(.gridbox) #".$key." .thumbnails-dots > div:nth-child(".($i + 1).") {";
                $str .= "background-image: var(--thumbnails-dots-image-".$i.");";
                $str .= "}";
            }
        }
        if ($obj->desktop->view->arrows) {
            $str .= "#".$key." .ba-slideshow-nav {";
            $str .= "display:block";
            $str .= "}";
        }

        return $str;
    }

    public function createFeatureBoxRules($obj, $key)
    {
        $this->prepareColors($obj->desktop->icon);
        $this->prepareColors($obj->desktop->button);
        $this->prepareBorder($obj->desktop->border);
        $this->prepareBorder($obj->desktop->image->border);
        $this->prepareBorder($obj->desktop->icon->border);
        $this->prepareBorder($obj->desktop->button->border);
        $str = $this->setMediaRules($obj, $key, 'getFeatureBoxRules');
        foreach ($obj->items as $ind => $item) {
            if ($item->type == 'image' && !empty($item->image)) {
                $str .= "#".$key." .ba-feature-box:nth-child(".($ind * 1 + 1).") .ba-feature-image {";
                $str .= "background-image: url(".$this->setBackgroundImage($item->image).");";
                $str .= "}";
            }
        }

        return $str;
    }

    public function createPostIntroRules($obj, $key)
    {
        if (!isset($obj->info)) {
            $obj->info = array('author', 'date', 'category', 'comments', 'hits', 'reviews');
        }
        $desktop = $obj->desktop;
        $str = $this->setMediaRules($obj, $key, 'getPostIntroRules');
        $str .= "#".$key." .intro-post-wrapper .intro-post-info > * a:hover {";
        $str .= "color: ".$this->getCorrectColor($obj->desktop->info->hover->color).";";
        $str .= "}";
        if (isset($obj->info)) {
            foreach ($obj->info as $i => $value) {
                $str .= '#'.$key.' .intro-post-'.$value.' {';
                $str .= "order: ".$i.";";
                $str .= "}";
            }
        }
        if (isset($desktop->info->show) && $desktop->info->show) {
            $str .= "#".$key." .intro-post-info {";
            $str .= 'display:block;';
            $str .= "}";
        }
        if (!isset($desktop->image->show)) {
            $desktop->image->show = $desktop->title->show = $desktop->date = $desktop->category = $desktop->hits = true;
        }
        if (isset($desktop->info->show) && $desktop->info->show) {
            $str .= "#".$key." .intro-category-author-social-wrapper {";
            $str .= 'display:'.($desktop->info->show ? 'block' : 'none').';';
            $str .= "}";
        }
        if ($desktop->image->show) {
            $str .= "#".$key." .intro-post-wrapper:not(.fullscreen-post) .intro-post-image-wrapper {";
            $str .= 'display:block;';
            $str .= "}";
        }
        if ($desktop->title->show) {
            $str .= "#".$key." .intro-post-title-wrapper {";
            $str .= 'display:block;';
            $str .= "}";
        }
        if (!isset($desktop->view->author)) {
            $desktop->view->author = false;
        }
        if (!isset($desktop->view->comments)) {
            $desktop->view->comments = false;
        }
        if (!isset($desktop->view->reviews)) {
            $desktop->view->reviews = false;
        }
        if ($desktop->view->author) {
            $str .= "#".$key." .intro-post-author {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->date) {
            $str .= "#".$key." .intro-post-date {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->category) {
            $str .= "#".$key." .intro-post-category {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->comments) {
            $str .= "#".$key." .intro-post-comments {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->hits) {
            $str .= "#".$key." .intro-post-hits {";
            $str .= 'display:inline-block;';
            $str .= "}";
        }
        if ($desktop->view->reviews) {
            $str .= "#".$key." .intro-post-reviews {";
            $str .= 'display:inline-flex;';
            $str .= "}";
        }
        foreach ($obj->info as $i => $value) {
            if (isset($desktop->view->{$value}) && $desktop->view->{$value}) {
                for ($j = $i + 1; $j < count($obj->info); $j++) {
                    $str .= "#".$key." .intro-post-".$obj->info[$j].":before {";
                    $str .= 'margin: 0 10px;content: "'.($obj->info[$j] == 'author' ? '' : '\2022').'";color: inherit;';
                    $str .= "}";
                }
                break;
            }
        }

        return $str;
    }

    public function createAuthorRules($obj, $key)
    {
        $this->prepareBorder($obj->desktop->border);
        $this->prepareBorder($obj->desktop->image->border);
        $str = $this->setMediaRules($obj, $key, 'getAuthorRules');
        $str .= "#".$key." .ba-post-author-title a:hover {";
        $str .= "color: ".$this->getCorrectColor($obj->desktop->title->hover->color).";";
        $str .= "}";
        if ($obj->desktop->view->image) {
            $str .= "#".$key." .ba-post-author-image {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($obj->desktop->view->title) {
            $str .= "#".$key." .ba-post-author-title-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($obj->desktop->view->intro) {
            $str .= "#".$key." .ba-post-author-description {";
            $str .= "display:block;";
            $str .= "}";
        }

        return $str;
    }

    public function createRecentCommentsRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $this->prepareBorder($desktop->border);
        $this->prepareBorder($desktop->image->border);
        $str = $this->setMediaRules($obj, $key, 'getRecentCommentsRules');
        if ($desktop->view->image) {
            $str .= "#".$key." .ba-blog-post-image {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($desktop->view->date) {
            $str .= "#".$key." .ba-blog-post-date {";
            $str .= "display:inline-block;";
            $str .= "}";
        }
        if ($desktop->view->intro) {
            $str .= "#".$key." .ba-blog-post-intro-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }
        if (isset($desktop->view->source)) {
            if ($desktop->view->source) {
                $str .= "#".$key." .ba-reviews-source {";
                $str .= "display:inline-block;";
                $str .= "}";
            }
            if ($desktop->view->title) {
                $str .= "#".$key." .ba-reviews-name {";
                $str .= "display:inline-block;";
                $str .= "}";
            }
            if ($desktop->view->title || $desktop->view->source) {
                $str .= "#".$key." .ba-blog-post-title-wrapper {";
                $str .= "display:block;";
                $str .= "}";
            }
        } else if ($desktop->view->title) {
            $str .= "#".$key." .ba-blog-post-title-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }

        return $str;
    }

    public function createCategoriesRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $this->prepareBorder($desktop->border);
        $this->prepareBorder($desktop->image->border);
        $str = $this->setMediaRules($obj, $key, 'getCategoriesRules');
        $str .= "#".$key." .ba-blog-post-title a:hover, ";
        $str .= "#".$key." .ba-blog-post.active .ba-blog-post-title a, ";
        $str .= "#".$key." .ba-blog-post-title i.collapse-categories-list:hover {";
        $str .= "color: ".$this->getCorrectColor($obj->desktop->title->hover->color).";";
        $str .= "}";
        $str .= "#".$key." .ba-blog-post-info-wrapper a:hover, ";
        $str .= "#".$key." .ba-blog-post-info-wrapper a.active, ";
        $str .= "#".$key." .ba-blog-post-info-wrapper i.collapse-categories-list:hover {";
        $str .= "color: ".$this->getCorrectColor($obj->desktop->info->hover->color).";";
        $str .= "}";
        if ($desktop->view->image) {
            $str .= "#".$key." .ba-blog-post-image {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($desktop->view->title) {
            $str .= "#".$key." .ba-blog-post-title {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->sub) {
            $str .= "#".$key." .ba-blog-post-info-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($desktop->view->intro) {
            $str .= "#".$key." .ba-blog-post-intro-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($desktop->view->counter) {
            $str .= "#".$key." .ba-app-category-counter {";
            $str .= "display:inline;";
            $str .= "}";
        }

        return $str;
    }

    public function createBlogPostsRules($obj, $key)
    {
        $desktop = $obj->desktop;
        if (!isset($desktop->store)) {
            $desktop->store = new stdClass();
            $desktop->badge = true;
            $desktop->wishlist = true;
            $desktop->price = true;
            $desktop->cart = true;
        }
        $this->prepareColors($obj->desktop->button);
        $this->prepareBorder($obj->desktop->border);
        if (isset($obj->desktop->image->border)) {
            $this->prepareBorder($obj->desktop->image->border);
        }
        $this->prepareBorder($obj->desktop->button->border);
        if (isset($obj->desktop->pagination->border)) {
            $this->prepareColors($obj->desktop->pagination);
            $this->prepareBorder($obj->desktop->pagination->border);
        }
        $str = $this->setMediaRules($obj, $key, 'getBlogPostsRules');
        $str .= "#".$key." .ba-blog-post-title a:hover {";
        $str .= "color: ".$this->getCorrectColor($obj->desktop->title->hover->color).";";
        $str .= "}";
        $str .= "#".$key." .ba-blog-post-info-wrapper > * a:hover, #".$key." .ba-post-navigation-info a:hover {";
        $str .= "color: ".$this->getCorrectColor($obj->desktop->info->hover->color).";";
        $str .= "}";
        if (isset($obj->fields)) {
            foreach ($obj->fields as $i => $value) {
                $str .= '#'.$key.' .ba-blog-post-field-row[data-id="'.$value.'"] {';
                $str .= "order: ".$i.";";
                $str .= "}";
            }
        }
        if (isset($obj->info)) {
            foreach ($obj->info as $i => $value) {
                $str .= '#'.$key.' .ba-blog-post-'.$value.' {';
                $str .= "order: ".$i.";";
                $str .= "}";
            }
        }
        if (!isset($desktop->view->author)) {
            $desktop->view->author = false;
        }
        if (!isset($desktop->view->comments)) {
            $desktop->view->comments = false;
        }
        if (!isset($desktop->view->reviews)) {
            $desktop->view->reviews = false;
        }
        if (isset($desktop->view->sorting) && $desktop->view->sorting) {
            $str .= "#".$key." .blog-posts-sorting-wrapper {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->image) {
            $str .= "#".$key." .ba-blog-post-image {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($desktop->view->title) {
            $str .= "#".$key." .ba-blog-post-title-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }
        if ($desktop->view->author) {
            $str .= "#".$key." .ba-blog-post-author {";
            $str .= "display:inline-block;";
            $str .= "}";
        }
        if ($desktop->view->date) {
            $str .= "#".$key." .ba-blog-post-date {";
            $str .= "display:inline-block;";
            $str .= "}";
        }
        if ($desktop->view->category) {
            $str .= "#".$key." .ba-blog-post-category {";
            $str .= "display:inline-block;";
            $str .= "}";
        }
        if ($desktop->view->hits) {
            $str .= "#".$key." .ba-blog-post-hits {";
            $str .= "display:inline-block;";
            $str .= "}";
        }
        if ($desktop->view->comments) {
            $str .= "#".$key." .ba-blog-post-comments {";
            $str .= "display:inline-block;";
            $str .= "}";
        }
        if ($desktop->store->badge) {
            $str .= "#".$key." .ba-blog-post-badge-wrapper {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->store->wishlist) {
            $str .= "#".$key." .ba-blog-post-wishlist-wrapper {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->store->price) {
            $str .= "#".$key." .ba-blog-post-add-to-cart-price {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->store->cart) {
            $str .= "#".$key." .ba-blog-post-add-to-cart-button {";
            $str .= "display:flex;";
            $str .= "}";
        }
        foreach ($desktop->store as $ind => $value) {
            if (($ind == 'badge' || $ind == 'wishlist' || $ind == 'price' || $ind == 'cart') && $value) {
                continue;
            }
            $str .= "#".$key.' .ba-blog-post-product-options[data-key="'.$key.'"] {';
            $str .= "display:flex;";
            $str .= "}";
        }
        if (!isset($obj->info)) {
            $obj->info = array('author', 'date', 'category', 'hits', 'comments');
        }
        $order = $obj->info;
        $count = count($order);
        $visible = false;
        foreach ($order as $i => $value) {
            if (isset($desktop->view->{$value}) && $desktop->view->{$value}) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $str .= "#".$key." .ba-blog-post-".$order[$j].":before {";
                    $str .= 'margin: 0 10px;content: "'.($order[$j] == 'author' ? '' : '\2022').'";color: inherit;';
                    $str .= "}";
                }
                $str .= "#".$key." .ba-blog-post-info-wrapper {";
                $str .= '--visible-info: 1;';
                $str .= "}";
                $visible = true;
                break;
            }
        }
        if (!$visible) {
            $str .= "#".$key." .ba-blog-post-info-wrapper {";
            $str .= '--visible-info: 0;';
            $str .= "}";
        }
        if ($desktop->view->reviews) {
            $str .= "#".$key." .ba-blog-post-reviews {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->intro) {
            $str .= "#".$key." .ba-blog-post-intro-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }
        if (isset($desktop->fields)) {
            $visibleField = null;
            foreach ($obj->fields as $i => $value) {
                if ($desktop->fields->{$value}) {
                    $visibleField = $value;
                    $str .= '#'.$key.' .ba-blog-post-field-row[data-id="'.$value.'"] {';
                    $str .= "display:flex;";
                    $str .= "margin-bottom: 10px;";
                    $str .= "}";
                }
            }
            if ($visibleField) {
                $str .= '#'.$key.' .ba-blog-post-field-row[data-id="'.$visibleField.'"] {';
                $str .= "margin-bottom: 0;";
                $str .= "}";
            }
        }
        if ($desktop->view->button) {
            $str .= "#".$key." .ba-blog-post-button-wrapper {";
            $str .= "display:block;";
            $str .= "}";
        }

        return $str;
    }

    public function createAddToCartRules($obj, $key)
    {
        $desktop = $obj->desktop;
        $this->prepareColors($obj->desktop->button);
        $this->prepareBorder($obj->desktop->button->border);
        $str = $this->setMediaRules($obj, $key, 'getAddToCartRules');
        if ($desktop->view->availability) {
            $str .= "#".$key." .ba-add-to-cart-stock {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->sku) {
            $str .= "#".$key." .ba-add-to-cart-sku {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->quantity) {
            $str .= "#".$key." .ba-add-to-cart-quantity {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->button) {
            $str .= "#".$key." .ba-add-to-cart-button-wrapper a {";
            $str .= "display:flex;";
            $str .= "}";
        }
        if ($desktop->view->wishlist) {
            $str .= "#".$key." .ba-add-to-wishlist {";
            $str .= "display:flex;";
            $str .= "}";
        }

        return $str;
    }

    public function createSectionRules($obj, $key)
    {
        $str = $this->setMediaRules($obj, $key, 'createPageRules');
        if ($obj->type == 'lightbox') {
            $str .= ".ba-lightbox-backdrop[data-id=".$key."] .close-lightbox {";
            $str .= "color: ".$this->getCorrectColor($obj->close->color).";";
            $str .= "text-align: ".$obj->close->{'text-align'}.";";
            $str .= "}";
            $str .= "body.gridbox .ba-lightbox-backdrop[data-id=".$key."] > .ba-lightbox-close {";
            $str .= "background-color: ".$this->getCorrectColor($obj->lightbox->background).";";
            $str .= "}";
            $str .= "body:not(.gridbox) .ba-lightbox-backdrop[data-id=".$key."] {";
            $str .= "background-color: ".$this->getCorrectColor($obj->lightbox->background).";";
            $str .= "}";
        }
        if ($obj->type == 'overlay-section') {
            $str .= ".ba-overlay-section-backdrop[data-id=".$key."] .close-overlay-section {";
            $str .= "color: ".$this->getCorrectColor($obj->close->color).";";
            $str .= "text-align: ".$obj->close->{'text-align'}.";";
            $str .= "}";
            $str .= "body.gridbox .ba-overlay-section-backdrop[data-id=".$key."] > .ba-overlay-section-close {";
            $str .= "background-color: ".$this->getCorrectColor($obj->lightbox->background).";";
            $str .= "}";
            $str .= "body:not(.gridbox) .ba-overlay-section-backdrop[data-id=".$key."] {";
            $str .= "background-color: ".$this->getCorrectColor($obj->lightbox->background).";";
            $str .= "}";
        }
        if (isset($obj->parallax) && $obj->parallax->enable) {
            $pHeight = 100 + $obj->parallax->offset * 2 * 200;
            $pTop = $obj->parallax->offset * 2 * -100;
            $str .= "#".$key." > .parallax-wrapper.scroll .parallax {";
            $str .= "height: ".$pHeight."%;";
            $str .= "top: ".$pTop."%;";
            $str .= "}";
        }
        
        return $str;
    }

    public function createPageRules($obj, $selector)
    {
        $height = (isset($obj->view) && isset($obj->view->height) ? $obj->view->height : '50').'px';
        $fullscreen = $this->item->type != 'lightbox' ? "100vh" : "calc(100vh - 50px)";
        $str = $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->animation) && (!empty($obj->animation->effect) || (empty($obj->animation->effect) && $this->breakpoint != 'desktop'))) {
            $this->animation($obj->animation);
            $css .= $this->css;
        }
        if (isset($obj->full->fullscreen)) {
            $css .= "min-height: ".($obj->full->fullscreen ? $fullscreen : $height).";";
        }
        if (isset($obj->disable)) {
            $display = $this->object->full->fullscreen ? 'flex' :  'block';
            $css .= $this->setItemsVisability($obj->disable, $display);
        }
        if (isset($obj->view->width)) {
            $css .= "width: ".$this->getValueUnits($obj->view->width).";";
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->backgroundRule($obj, '#'.$selector, gridboxHelper::$up);
        if (isset($obj->animation) && !empty($obj->animation->effect)) {
            $str .= "#".$selector.".visible {";
            $str .= "opacity: 1;";
            $str .= "}";
        }

        if (isset($obj->shape->bottom->effect) || isset($obj->shape->bottom->value) || isset($obj->shape->bottom->width)
            || isset($obj->shape->bottom->height)) {
            $str .= $this->getShapeRules($selector, $this->object->shape->bottom, 'bottom');
        }
        if (isset($obj->shape->top->effect) || isset($obj->shape->top->value) || isset($obj->shape->top->width)
            || isset($obj->shape->top->height)) {
            $str .= $this->getShapeRules($selector, $this->object->shape->top, 'top');
        }
        if (isset($obj->sticky) && $this->object->sticky->enable && isset($obj->sticky->offset) && $this->item->type == 'column') {
            $str .= "#".$selector." {";
            $str .= "top: ".$this->getValueUnits($obj->sticky->offset).";";
            $str .= "}";
        }
        if ($this->item->type == 'header') {
            $str .= $this->createHeaderRules($obj);
        }
        if ($this->item->type == 'footer') {
            $str .= $this->createTypography($obj, true);
        }

        return $str;
    }

    public function getShapeRules($selector, $obj, $type)
    {
        if (empty($obj->effect) && $this->breakpoint == 'desktop') {
            return '';
        }
        $str = "#".$selector." > .ba-shape-divider.ba-shape-divider-".$type." {";
        $width = isset($obj->width) ? $obj->width : 100;
        $height = isset($obj->height) ? $obj->height : $obj->value * 10;
        if ($obj->effect == 'arrow') {
            $path = "polygon(100% ".(100 - ($height / 10));
            $path .= "%, 100% 100%, 0 100%, 0 ".(100 - ($height / 10));
            $path .= "%, ".(50 - ($height / 10) / 2)."% ".(100 - ($height / 10));
            $path .= "%, 50% 100%, ".(50 + ($height / 10) / 2)."% ";
            $path .= (100 - ($height / 10))."%)";
        } else if ($obj->effect == 'zigzag') {
            $path = "polygon(";
            $delta = 0;
            $delta2 = 100 / (($height / 10) * 2);
            for ($i = 0; $i < ($height / 10); $i++) {
                if ($i != 0) {
                    $path .= ",";
                }
                $path .= $delta."% 100%,";
                $path .= $delta2."% calc(100% - 15px),";
                $delta += 100 / ($height / 10);
                $delta2 += 100 / ($height / 10);
                $path .= $delta."% 100%";
            }
            $path .= ")";
        } else if ($obj->effect == 'circle') {
            $path = "circle(".($height / 10)."% at 50% 100%)";
        } else if ($obj->effect == 'vertex') {
            $path = "polygon(20% calc(".(100 - ($height / 10))."% + 15%), 35%  calc(".(100 - ($height / 10));
            $path .= "% + 45%), 65%  ".(100 - ($height / 10))."%, 100% 100%, 100% 100%, 0% 100%, 0  calc(";
            $path .= (100 - ($height / 10))."% + 10%), 10%  calc(".(100 - ($height / 10))."% + 30%))";
        } else if ($obj->effect != 'arrow' && $obj->effect != 'zigzag' &&
            $obj->effect != 'circle' && $obj->effect != 'vertex') {
            $path = "none";
            $str .= "background-color: none;";
            $str .= isset($obj->color) ? "color: ".$this->getCorrectColor($obj->color).";" : '';
        }
        if (isset($obj->color) && isset($obj->effect) &&
            ($obj->effect == 'arrow' || $obj->effect == 'zigzag' || $obj->effect == 'circle' || $obj->effect == 'vertex')) {
            $str .= "background-color: ".$this->getCorrectColor($obj->color).";";
        }
        $str .= "clip-path: ".$path.";";
        $str .= 'display: '.($obj->effect == '' ? 'none' : 'block').';';
        $str .= "}";
        $str .= "#".$selector." > .ba-shape-divider.ba-shape-divider-".$type." svg:not(.shape-divider-".$obj->effect.") {";
        $str .= "display: none;";
        $str .= "}";
        $str .= "#".$selector." > .ba-shape-divider.ba-shape-divider-".$type." svg.shape-divider-".$obj->effect." {";
        $str .= "display: block;";
        $str .= "height: ".$this->getValueUnits($height).";";
        $str .= "}";
        $str .= "#".$selector." > .ba-shape-divider.ba-shape-divider-".$type." {";
        $str .= "width: ".$width."%;";
        $str .= "}";

        return $str;
    }

    public function createHeaderRules($obj)
    {
        $str = '';
        if (isset($obj->position)) {
            $str .= "body header.header {";
            $str .= "position:".$obj->position.";";
            if ($obj->position == 'fixed' || $obj->position == 'absolute') {
                $str .= "top: 0;";
                $str .= "left: 0;";
            }
            $str .= "}";
        }
        if (!isset($obj->width) && $this->breakpoint == 'desktop') {
            $obj->width = 250;
        }
        if (isset($obj->width)) {
            $str .= "body {";
            $str .= "--sidebar-menu-width:".$this->getValueUnits($obj->width).";";
            $str .= "}";
        }
        if (isset($obj->position) && $obj->position == 'fixed') {
            $str .= ".ba-container .header {";
            $str .= "margin-left: calc((100vw - 1280px)/2);";
            $str .= "max-width: 1170px;";
            $str .= "}";
        } else if (isset($obj->position)) {
            $str .= ".ba-container .header {";
            $str .= "margin-left: 0;";
            $str .= "max-width: none;";
            $str .= "}";
        }

        return $str;
    }

    public function getCurrencySwitcherRules($obj, $selector)
    {
        $str = $css = "";
        if (isset($obj->margin)) {
            $str .= "#".$selector." {";
            $str .= $this->get('margin', $obj->margin, 'default');
            $str .= "}";
            $str .= $this->getStateRule("#".$selector.":hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector);
        }
        if (isset($obj->switcher->typography)) {
            $css .= $this->getTypographyRule($obj->switcher->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-currency-switcher-active span {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->switcher->typography->color)) {
            $str .= "#".$selector." .ba-currency-switcher-active i {";
            $str .= "color: ".$this->getCorrectColor($obj->switcher->typography->color).";";
            $str .= "}";
        }
        if (isset($obj->switcher->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-currency-switcher-active {";
            $str .= "text-align: ".$obj->switcher->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->list->typography)) {
            $css .= $this->getTypographyRule($obj->list->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-currency-switcher-list span {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->list->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-currency-switcher-list {";
            $str .= "text-align: ".$obj->list->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->list->typography->color)) {
            $str .= "#".$selector." .ba-currency-switcher-list i {";
            $str .= "color: ".$this->getCorrectColor($obj->list->typography->color).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->dropdown->padding)) {
            $css .= $this->get('padding', $obj->dropdown->padding, 'default');
        }
        if (isset($obj->dropdown->border)) {
            $this->cascade->border = $this->object->dropdown->border;
            $css .= $this->get('border', $obj->dropdown->border, 'default');
        }
        if (isset($obj->dropdown->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->dropdown->background->color).";";
            $css .= "--background-color: ".$this->getCorrectColor($obj->dropdown->background->color).";";
        }
        if (isset($obj->dropdown->shadow)) {
            $css .= $this->get('shadow', $obj->dropdown->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-currency-switcher-list {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-currency-switcher-list:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-currency-switcher-list");
        }
        
        return $str;
    }

    public function getLanguageSwitcherRules($obj, $selector)
    {
        $str = $css = "";
        $isDefault = $this->item->layout == 'ba-default-layout';
        if (isset($obj->margin)) {
            $str .= "#".$selector." {";
            $str .= $this->get('margin', $obj->margin, 'default');
            $str .= "}";
            $str .= $this->getStateRule("#".$selector.":hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector);
        }
        if ($isDefault && isset($obj->flag->size)) {
            $css .= "width: ".$this->getValueUnits($obj->flag->size).";";
            $css .= "height: ".$this->getValueUnits($obj->flag->size).";";
        }
        if ($isDefault && isset($obj->flag->radius)) {
            $css .= "border-radius: ".$this->getValueUnits($obj->flag->radius).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-language-switcher-item img {";
            $str .= $css;
            $str .= "}";
        }
        if ($isDefault && isset($obj->flag->align)) {
            $justify = str_replace('right', 'flex-end', $obj->flag->align);
            $justify = str_replace('left', 'flex-start', $justify);
            $str .= "#".$selector." .ba-language-switcher-list {";
            $str .= "justify-content: ".$justify.";";
            $str .= "}";
        }
        $css = '';
        if (!$isDefault && isset($obj->switcher->flag->size)) {
            $css .= "width: ".$this->getValueUnits($obj->switcher->flag->size).";";
            $css .= "height: ".$this->getValueUnits($obj->switcher->flag->size).";";
        }
        if (!$isDefault && isset($obj->switcher->flag->radius)) {
            $css .= "border-radius: ".$this->getValueUnits($obj->switcher->flag->radius).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-language-switcher-active img {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (!$isDefault && isset($obj->switcher->typography)) {
            $css .= $this->getTypographyRule($obj->switcher->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-language-switcher-active span {";
            $str .= $css;
            $str .= "}";
        }
        if (!$isDefault && isset($obj->switcher->typography->color)) {
            $str .= "#".$selector." .ba-language-switcher-active i {";
            $str .= "color: ".$this->getCorrectColor($obj->switcher->typography->color).";";
            $str .= "}";
        }
        if (!$isDefault && isset($obj->switcher->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-language-switcher-active {";
            $str .= "text-align: ".$obj->switcher->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (!$isDefault && isset($obj->list->flag->size)) {
            $css .= "width: ".$this->getValueUnits($obj->list->flag->size).";";
            $css .= "height: ".$this->getValueUnits($obj->list->flag->size).";";
        }
        if (!$isDefault && isset($obj->list->flag->size)) {
            $css .= "border-radius: ".$this->getValueUnits($obj->list->flag->size).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-language-switcher-list img {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (!$isDefault && isset($obj->list->typography)) {
            $css .= $this->getTypographyRule($obj->list->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-language-switcher-list span {";
            $str .= $css;
            $str .= "}";
        }
        if (!$isDefault && isset($obj->list->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-language-switcher-list {";
            $str .= "text-align: ".$obj->list->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (!$isDefault && isset($obj->list->typography->color)) {
            $str .= "#".$selector." .ba-language-switcher-list i {";
            $str .= "color: ".$this->getCorrectColor($obj->list->typography->color).";";
            $str .= "}";
        }
        $css = '';
        if (!$isDefault && isset($obj->dropdown->padding)) {
            $css .= $this->get('padding', $obj->dropdown->padding, 'default');
        }
        if (!$isDefault && isset($obj->dropdown->border)) {
            $this->cascade->border = $this->object->dropdown->border;
            $css .= $this->get('border', $obj->dropdown->border, 'default');
        }
        if (!$isDefault && isset($obj->dropdown->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->dropdown->background->color).";";
            $css .= "--background-color: ".$this->getCorrectColor($obj->dropdown->background->color).";";
        }
        if (!$isDefault && isset($obj->dropdown->shadow)) {
            $css .= $this->get('shadow', $obj->dropdown->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-language-switcher-list {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-language-switcher-list:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-language-switcher-list");
        }
        
        return $str;
    }

    public function getModulesRules($obj, $selector)
    {
        $str = $css = "";
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);

        return $str;
    }

    public function getErrorRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->code->typography)) {
            $css .= $this->getTypographyRule($obj->code->typography);
        }
        if (isset($obj->code->margin)) {
            $css .= $this->get('margin', $obj->code->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." h1.ba-error-code {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." h1.ba-error-code:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." h1.ba-error-code");
        $css = '';
        if (isset($obj->message->typography)) {
            $css .= $this->getTypographyRule($obj->message->typography);
        }
        if (isset($obj->message->margin)) {
            $css .= $this->get('margin', $obj->message->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." p.ba-error-message {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." p.ba-error-message:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." p.ba-error-message");

        return $str;
    }

    public function getTextRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $array = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        foreach ($array as $key => $value) {
            if (isset($obj->{$value}) && isset($obj->{$value}->{'font-style'})
                && $obj->{$value}->{'font-style'} == '@default') {
                unset($obj->{$value}->{'font-style'});
            }
            $css = '';
            if (isset($obj->{$value})) {
                $css .= $this->getTypographyRule($obj->{$value}, '', $value);
            }
            if (!empty($css)) {
                $str .= "#".$selector." ".$value." {";
                $str .= $css;
                $str .= "}";
            }
        }
        if (isset($obj->animation->duration)) {
            $str .= "#".$selector." .headline-wrapper > * {";
            $str .= 'animation-duration: '.$obj->animation->duration.'s;';
            $str .= "}";
        }
        if (isset($obj->links->color)) {
            $str .= "#".$selector.' a {';
            $str .= 'color:'.$this->getCorrectColor($obj->links->color).';';
            $str .= '}';
        }
        if (isset($obj->links->{'hover-color'})) {
            $str .= "#".$selector.' a:hover {';
            $str .= 'color:'.$this->getCorrectColor($obj->links->{'hover-color'}).';';
            $str .= '}';
        }

        return $str;
    }

    public function getHotspotRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj, 'default', '--');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." > .ba-button-wrapper a {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." > .ba-button-wrapper a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." > .ba-button-wrapper a");
        if (isset($obj->style->size)) {
            $str .= "#".$selector." > .ba-button-wrapper i {";
            $str .= "font-size : ".$this->getValueUnits($obj->style->size).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->popover->style->width)) {
            $css .= "width: ".$this->getValueUnits($obj->popover->style->width).";";
        }
        if (isset($obj->popover->background->color)) {
            $css .= "--background-color: ".$this->getCorrectColor($obj->popover->background->color).";";
        }
        if (isset($obj->popover->border)) {
            $this->cascade->border = $this->object->popover->border;
            $css .= $this->get('border', $obj->popover->border, 'default');
        }
        if (isset($obj->popover->shadow)) {
            $css .= $this->get('shadow', $obj->popover->shadow, 'default');
        }
        if (isset($obj->popover->padding)) {
            $css .= $this->get('padding', $obj->popover->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." > .ba-hotspot-popover {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." > .ba-hotspot-popover:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." > .ba-hotspot-popover");

        return $str;
    }

    public function getButtonRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-button-wrapper {";
            $str .= "text-align: ".$obj->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->typography)) {
            $str .= "#".$selector." .ba-button-wrapper a span {";
            $str .= $this->getTypographyRule($obj->typography);
            $str .= "}";
        }
        $css = '';
        if (isset($obj->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-button-wrapper a {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-button-wrapper a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-button-wrapper a");
        if (isset($obj->icons->size)) {
            $str .= "#".$selector." .ba-button-wrapper a i {";
            $str .= "font-size : ".$this->getValueUnits($obj->icons->size).";";
            $str .= "}";
        }
        if (isset($obj->icons->position)) {
            $str .= "#".$selector." .ba-button-wrapper a {";
            if ($obj->icons->position == '') {
                $str .= 'flex-direction: row-reverse;';
            } else {
                $str .= 'flex-direction: row;';
            }
            $str .= "}";
            if ($obj->icons->position == '') {
                $str .= "#".$selector." .ba-button-wrapper a i {";
                $str .= 'margin: 0 10px 0 0;';
                $str .= "}";
            } else {
                $str .= "#".$selector." .ba-button-wrapper a i {";
                $str .= 'margin: 0 0 0 10px;';
                $str .= "}";
            }
        }
        if (isset($obj->view->subtotal)) {
            $str .= "#".$selector." .ba-button-wrapper a span.ba-cart-subtotal {";
            $str .= 'display: '.($obj->view->subtotal ? 'flex' : 'none').';';
            $str .= "}";
        }
        
        return $str;
    }

    public function getIconRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->icon->{'text-align'})) {
            $css .= "text-align: ".$obj->icon->{'text-align'}.";";
        }
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->icon->size)) {
            $css .= "width : ".$this->getValueUnits($obj->icon->size).";";
            $css .= "height : ".$this->getValueUnits($obj->icon->size).";";
            $css .= "font-size : ".$this->getValueUnits($obj->icon->size).";";
        }
        if (isset($obj->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-icon-wrapper i {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-icon-wrapper i:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-icon-wrapper i");
        
        return $str;
    }

    public function getVideoRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-video-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-video-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-video-wrapper");

        return $str;
    }

    public function getMapRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->height)) {
            $str .= "#".$selector." .ba-map-wrapper {";
            $str .= "height: ".$this->getValueUnits($obj->height).";";
            $str .= "}";
        }

        return $str;
    }

    public function getIconListRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        $str .= "#".$selector." {";
        $str .= $css;
        $str .= "}";
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->body->{'text-align'})) {
            $align = $obj->body->{'text-align'};
            $align = str_replace('left', 'flex-start', $align);
            $align = str_replace('right', 'flex-end', $align);
            $str .= "#".$selector." .ba-icon-list-wrapper ul {";
            $str .= "align-items: ".$align.";";
            $str .= "justify-content: ".$align.";";
            $str .= "}";
        }
        $css = '';
        $query = "#".$selector." .ba-icon-list-wrapper ul li"; 
        if (isset($obj->background->color)) {
            $css .= "background-color:".$this->getCorrectColor($obj->background->color).';';
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        if (isset($obj->body)) {
            $str .= "#".$selector." .ba-icon-list-wrapper ul li span {";
            $str .= $this->getTypographyRule($obj->body);
            $str .= "}";
        }
        if (isset($obj->body->{'line-height'})) {
            $str .= "#".$selector." .ba-icon-list-wrapper ul li {";
            $str .= '--icon-list-line-height: '.$this->getValueUnits($obj->body->{'line-height'}).';';
            $str .= "}";
        }
        $css = '';
        if (isset($obj->icons->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->icons->color).";";
        }
        if (isset($obj->icons->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->icons->size).";";
        }
        if (isset($obj->icons->background)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->icons->background).";";
        }
        if (isset($obj->icons->padding)) {
            $css .= "padding: ".$this->getValueUnits($obj->icons->padding).";";
        }
        if (isset($obj->icons->radius)) {
            $css .= "border-radius: ".$this->getValueUnits($obj->icons->radius).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-icon-list-wrapper ul li i, #".$selector." ul li a:before, #";
            $str .= $selector." ul li.list-item-without-link:before {";
            $str .= $css;
            $str .= "}";
        }
        
        return $str;
    }

    public function getStarRatingsRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->icon->{'text-align'})) {
            $str .= "#".$selector." .star-ratings-wrapper {";
            $str .= "text-align: ".$obj->icon->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->icon->color)) {
            $str .= "#".$selector." .stars-wrapper {";
            $str .= "color:".$this->getCorrectColor($obj->icon->color).";";
            $str .= "}";
        }
        if (isset($obj->icon->size)) {
            $str .= "#".$selector." .star-ratings-wrapper i {";
            $str .= "font-size:".$this->getValueUnits($obj->icon->size).";";
            $str .= "}";
        }
        if (isset($obj->icon)) {
            $str .= "#".$selector." .star-ratings-wrapper i.active, #".$selector." .star-ratings-wrapper i.active + i:after";
            $str .= ", #".$selector." .stars-wrapper:hover i {";
            $str .= "color:".$this->getCorrectColor($obj->icon->hover).";";
            $str .= "}";
        }
        if (isset($obj->info)) {
            $str .= "#".$selector." .info-wrapper * {";
            $str .= $this->getTypographyRule($obj->info, 'text-align');
            $str .= "}";
        }

        return $str;
    }

    public function getFieldRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-field-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-field-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-field-wrapper");
        if (isset($obj->title->typography)) {
            $str .= "#".$selector." .ba-field-label, #".$selector." .ba-field-label *:not(i):not(.ba-tooltip) {";
            $str .= $this->getTypographyRule($obj->title->typography);
            $str .= "}";
        }
        $css = '';
        if (isset($obj->icons->color)) {
            $css .= "color : ".$this->getCorrectColor($obj->icons->color).";";
        }
        if (isset($obj->icons->size)) {
            $css .= "font-size : ".$this->getValueUnits($obj->icons->size).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-field-label i {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->value->typography)) {
            $str .= "#".$selector." .ba-field-content {";
            $str .= $this->getTypographyRule($obj->value->typography);
            $str .= "}";
        }

        return $str;
    }

    public function getFieldsFilterRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color : ".$this->getCorrectColor($obj->background->color).";";
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->title->typography)) {
            $str .= "#".$selector." .ba-field-filter-label, #".$selector." .ba-selected-filter-values-title {";
            $str .= $this->getTypographyRule($obj->title->typography);
            $str .= "}";
        }
        $css = '';
        if (isset($obj->value->typography)) {
            $css .= $this->getTypographyRule($obj->value->typography);
        }
        if (isset($obj->value->typography->{'line-height'})) {
            $css .= '--filter-value-line-height: '.$this->getValueUnits($obj->value->typography->{'line-height'}).';';
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-field-filter-value-wrapper, #".$selector." .ba-selected-filter-values-remove-all, #";
            $str .= $selector." .ba-selected-filter-values-body, #".$selector." .ba-items-filter-search-button {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->value->typography->{'text-align'})) {
            $justify = str_replace('right', 'flex-start', $obj->value->typography->{'text-align'});
            $justify = str_replace('left', 'flex-end', $justify);
            $str .= "#".$selector." .ba-checkbox-wrapper {";
            $str .= "justify-content: ".$justify.";";
            $str .= "}";
        }

        return $str;
    }

    public function getEventCalendarRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->months->typography)) {
            $str .= "#".$selector." .ba-event-calendar-title-wrapper {";
            $str .= $this->getTypographyRule($obj->months->typography);
            $str .= "}";
        }
        if (isset($obj->weeks->typography)) {
            $str .= "#".$selector." .ba-event-calendar-header * {";
            $str .= $this->getTypographyRule($obj->weeks->typography);
            $str .= "}";
        }
        if (isset($obj->days->typography)) {
            $str .= "#".$selector." .ba-event-calendar-body * {";
            $str .= $this->getTypographyRule($obj->days->typography);
            $str .= "}";
        }

        return $str;
    }

    public function getPreloaderRules($obj, $selector)
    {
        $str = '';
        if (isset($obj->disable)) {
            $str .= "#".$selector." {";
            $str .= $this->setItemsVisability($obj->disable, "block");
            $str .= "}";
        }
        if (isset($obj->background)) {
            $str .= "#".$selector." .preloader-wrapper, #".$selector." .preloader-wrapper:before, ";
            $str .= "#".$selector." .preloader-wrapper:after {";
            $str .= "background-color: ".$this->getCorrectColor($obj->background).";";
            $str .= "}";
            $str .= "#".$selector." .preloader-wrapper:before, ";
            $str .= "#".$selector." .preloader-wrapper:after {";
            $str .= "border-color: ".$this->getCorrectColor($obj->background).";";
            $str .= "}";
        }
        if (isset($obj->size)) {
            $str .= "#".$selector." .preloader-point-wrapper {";
            $str .= "width: ".$this->getValueUnits($obj->size).";";
            $str .= "height: ".$this->getValueUnits($obj->size).";";
            $str .= "}";
        }
        if (isset($obj->color)) {
            $str .= "#".$selector." .preloader-point-wrapper div, #".$selector." .preloader-point-wrapper div:before {";
            $str .= "background-color: ".$this->getCorrectColor($obj->color).";";
            $str .= "}";
        }
        if (isset($obj->width)) {
            $str .= "#".$selector." .preloader-image-wrapper {";
            $str .= "width: ".$this->getValueUnits($obj->width).";";
            $str .= "}";
        }

        return $str;
    }

    public function getBreadcrumbsRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->style->typography->{'text-align'})) {
            $justify = str_replace('right', 'flex-end', $obj->style->typography->{'text-align'});
            $justify = str_replace('left', 'flex-start', $justify);
            $str .= "#".$selector." ul {";
            $str .= "justify-content: ".$justify.";";
            $str .= "}";
        }
        if (isset($obj->style->padding)) {
            $str .= "#".$selector." li > * {";
            $str .= $this->get('padding', $obj->style->padding, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." li > *:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." li > *");
        if (isset($obj->style->typography->{'text-decoration'})) {
            $str .= "#".$selector." li span {";
            $str .= "text-decoration: ".$obj->style->typography->{'text-decoration'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->style->padding)) {
            $css .= $this->get('padding', $obj->style->padding, 'default', '--');
        }
        if (isset($obj->style->typography->{'line-height'})) {
            $css .= "--typography-line-height : ".$this->getValueUnits($obj->style->typography->{'line-height'}).";";
        }
        if (isset($obj->style->typography)) {
            $css .= $this->getTypographyRule($obj->style->typography, 'text-align');
        }
        if (isset($obj->style->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->style->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->style, 'default', '--', null, ['hover', 'active']);
        }
        if (!empty($css)) {
            $str .= "#".$selector." li {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->style->colors)) {
            $this->updateTransitions($obj->style->colors, 'border-color');
        }
        $str .= $this->getStateRule("#".$selector." li:hover", 'hover');
        $str .= $this->getStateRule("#".$selector." li.active", 'active');
        $str .= $this->getTransitionRule("#".$selector." li, #".$selector." li a:after, #".$selector." li a:before");
        if (isset($obj->style->icon->size)) {
            $str .= "#".$selector." li i {";
            $str .= "font-size: ".$this->getValueUnits($obj->style->icon->size).";";
            $str .= "}";
        }

        return $str;
    }

    public function getCheckoutFormRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default', '--');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, '', '', true, '--title');
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default', '--title-');
        }
        if (isset($obj->headline->typography)) {
            $css .= $this->getTypographyRule($obj->headline->typography, '', '', true, '--headline');
        }
        if (isset($obj->headline->margin)) {
            $css .= $this->get('margin', $obj->headline->margin, 'default', '--headline-');
        }
        if (isset($obj->field->background)) {
            $css .= $this->get('backgroundColor', $obj->field->background, 'default', '--');
        }
        if (isset($obj->field->border)) {
            $this->cascade->border = $this->object->field->border;
            $css .= $this->get('border', $obj->field->border, 'default', '--');
        }
        if (isset($obj->field->margin)) {
            $css .= $this->get('margin', $obj->field->margin, 'default', '--field-');
        }
        if (isset($obj->field->padding)) {
            $css .= $this->get('padding', $obj->field->padding, 'default', '--field-');
        }
        if (isset($obj->field->typography)) {
            $css .= $this->getTypographyRule($obj->field->typography, '', '', true, '--field');
        }
        if (!empty($css)) {
            $str .= "body, .ba-checkout-form-fields, .ba-item-checkout-order-form, #".$selector.".ba-item-submission-form {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-checkout-form-field-wrapper *:hover, .ba-checkout-authentication-input input:hover, ";
        $query .= ".ba-account-profile-field-wrapper input:hover, .ba-checkout-form-field-wrapper *:hover,";
        $query .= '.ba-checkout-authentication-wrapper .ba-login-integration-btn:hover, ';
        $query .= "#".$selector." input:hover, #".$selector." select:hover, #".$selector." textarea:hover";
        $str .= $this->getStateRule($query, 'hover');
        $query = "#".$selector." .ba-checkout-form-field-wrapper *, .ba-checkout-authentication-input input,";
        $query .= ".ba-account-profile-field-wrapper input, .ba-checkout-form-field-wrapper *, ";
        $query .= '.ba-checkout-authentication-wrapper .ba-login-integration-btn, ';
        $query .= "#".$selector." input, #".$selector." select, #".$selector." textarea";
        $str .= $this->getTransitionRule($query);
        $this->transitions = [];
        $this->states = new stdClass();
        
        return $str;
    }

    public function getSearchRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->typography)) {
            $str .= "#".$selector." .ba-search-wrapper input,";
            $str .= "#".$selector." .ba-search-wrapper input::placeholder {";
            $str .= $this->getTypographyRule($obj->typography);
            $str .= "}";
        }
        if (isset($obj->typography->{'line-height'})) {
            $str .= "#".$selector." .ba-search-wrapper input {";
            $str .= "height : ".$this->getValueUnits($obj->typography->{'line-height'}).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->background)) {
            $css .= $this->get('backgroundColor', $obj->background, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-search-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-search-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-search-wrapper");
        $css = '';
        if (isset($obj->typography->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->typography->color).";";
        }
        if (isset($obj->icons->size)) {
            $css .= "font-size : ".$this->getValueUnits($obj->icons->size).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-search-wrapper i {";
            $str .= $css;
            $str .= "}";
        }
        
        return $str;
    }

    public function getLoginRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector.":hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector);
        }
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default', '--');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'shadow', '--');
        }
        if (isset($obj->background)) {
            $css .= $this->get('backgroundColor', $obj->background, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-content-wrapper {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-content-wrapper:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-content-wrapper");
        }
        $css = '';
        if (isset($obj->headline->typography)) {
            $css .= $this->getTypographyRule($obj->headline->typography);
        }
        if (isset($obj->headline->margin)) {
            $css .= $this->get('margin', $obj->headline->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-headline {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-headline:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-headline");
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography);
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-field-label {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-field-label:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-field-label");
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-checkbox-wrapper > * {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->description->typography)) {
            $css .= $this->getTypographyRule($obj->description->typography);
        }
        if (isset($obj->description->margin)) {
            $css .= $this->get('margin', $obj->description->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-description {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-description:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-description");
        }
        $css = '';
        if (isset($obj->field->typography)) {
            $css .= $this->getTypographyRule($obj->field->typography);
        }
        if (isset($obj->field->padding)) {
            $css .= $this->get('padding', $obj->field->padding, 'default');
        }
        if (isset($obj->field->margin)) {
            $css .= $this->get('margin', $obj->field->margin, 'default');
        }
        if (isset($obj->field->background)) {
            $css .= $this->get('backgroundColor', $obj->field->background, 'default');
        }
        if (isset($obj->field->border)) {
            $this->cascade->border = $this->object->field->border;
            $css .= $this->get('border', $obj->field->border, 'default', '--');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-field {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-field:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-field");
        }
        $css = '';
        if (isset($obj->field->typography)) {
            $css .= $this->getTypographyRule($obj->field->typography, 'text-align');
        }
        if (isset($obj->field->background)) {
            $css .= $this->get('backgroundColor', $obj->field->background, 'default');
        }
        if (isset($obj->field->border)) {
            $this->cascade->border = $this->object->field->border;
            $css .= $this->get('border', $obj->field->border, 'default', '--');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-integration-btn {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-integration-btn:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-integration-btn");
        }
        $css = '';
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->margin)) {
            $css .= $this->get('margin', $obj->button->margin, 'default');
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default', '--');
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default', '--');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-login-btn {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-login-btn:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-login-btn");
        }

        return $str;
    }

    public function getLogoRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->{'text-align'})) {
            $css .= "text-align: ".$obj->{'text-align'}.";";
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->width)) {
            $str .= "#".$selector." img {";
            $str .= "width: ".$this->getValueUnits($obj->width).";";
            $str .= "}";
        }

        return $str;
    }

    public function getScrollTopRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->icons->align)) {
            $css .= "text-align : ".$obj->icons->align.";";
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj, 'default');
        }
        if (isset($obj->icons->size)) {
            $css .= "font-size : ".$this->getValueUnits($obj->icons->size).";";
            $css .= "width : ".$this->getValueUnits($obj->icons->size).";";
            $css .= "height : ".$this->getValueUnits($obj->icons->size).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." i.ba-btn-transition {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." i.ba-btn-transition:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." i.ba-btn-transition");

        return $str;
    }

    public function getCountdownRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-countdown > span {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-countdown > span:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-countdown > span");
        if (isset($obj->counter)) {
            $str .= "#".$selector." .countdown-time {";
            $str .= $this->getTypographyRule($obj->counter);
            $str .= "}";
        }
        if (isset($obj->label)) {
            $str .= "#".$selector." .countdown-label {";
            $str .= $this->getTypographyRule($obj->label);
            $str .= "}";
        }

        return $str;
    }

    public function getCounterRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->counter->{'text-align'})) {
            $css .= "text-align : ".$obj->counter->{'text-align'}.";";
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->counter)) {
            $css .= $this->getTypographyRule($obj->counter, 'text-align');
        }
        if (isset($obj->counter->{'line-height'})) {
            $css .= "width : ".$this->getValueUnits($obj->counter->{'line-height'}).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-counter span.counter-number {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-counter span.counter-number";
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        
        return $str;
    }

    public function getReadingProgressBarRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->view->height)) {
            $css .= 'height: '.$this->getValueUnits($obj->view->height).';';
        }
        if (isset($obj->view->background)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->view->background).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-reading-progress-bar {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->view->bar)) {
            $str .= "#".$selector." .ba-animated-bar {";
            $str .= "background-color: ".$this->getCorrectColor($obj->view->bar).";";
            $str .= "}";
        }

        return $str;
    }

    public function getProgressBarRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->shadow)){
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->view->height)) {
            $css .= 'height: '.$this->getValueUnits($obj->view->height).';';
        }
        if (isset($obj->view->background)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->view->background).";";
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-progress-bar {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-progress-bar:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-progress-bar");
        $css = '';
        if (isset($obj->view->bar)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->view->bar).";";
        }
        if (isset($obj->typography)) {
            $css .= $this->getTypographyRule($obj->typography);
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-animated-bar {";
            $str .= $css;
            $str .= "}";
        }

        return $str;
    }

    public function getProgressPieRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->view->width)) {
            $css .= 'width: '.$this->getValueUnits($obj->view->width).';';
        }
        if (isset($obj->typography)) {
            $css .= $this->getTypographyRule($obj->typography);
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-progress-pie {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->view->width)) {
            $str .= "#".$selector." .ba-progress-pie canvas {";
            $str .= 'width: '.$this->getValueUnits($obj->view->width).';';
            $str .= "}";
        }

        return $str;
    }

    public function createMegaMenuRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->backgroundRule($obj, '#'.$selector, gridboxHelper::$up);
        if (isset($obj->disable)) {
            $str .= 'li.deeper > .tabs-content-wrapper[data-id="'.$selector.'"] + a > i.ba-icon-caret-right {';
            $str .= $this->setItemsVisability($obj->disable, "inline-block");
            $str .= "}";
        }

        return $str;
    }

    public function getFlipboxRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.':hover', 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->view->height)) {
            $str .= "#".$selector." > .ba-flipbox-wrapper {";
            $str .= "height: ".$this->getValueUnits($obj->view->height).";";
            $str .= "}";
        }
        if (isset($obj->full->fullscreen)) {
            $str .= "#".$selector." > .ba-flipbox-wrapper > .column-wrapper > .ba-grid-column-wrapper > .ba-grid-column {";
            $str .= "min-height: ".($obj->full->fullscreen ? "100vh" : "50px").";";
            $str .= "}";
        }
        if (isset($obj->animation->duration)) {
            $str .= "#".$selector." > .ba-flipbox-wrapper > .column-wrapper {";
            $str .= "transition-duration: ".$obj->animation->duration."s;";
            $str .= "}";
        }

        return $str;
    }

    public function getFlipsidesRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str = '#'.$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->backgroundRule($obj, '#'.$selector, gridboxHelper::$up);

        return $str;
    }

    public function getSearchHeadlineRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->typography)) {
            $str .= "#".$selector." .search-result-headline-wrapper > * {";
            $str .= $this->getTypographyRule($obj->typography);
            $str .= "}";
        }
        

        return $str;
    }

    public function getTabsRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .tab-content {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .tab-content:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .tab-content");
        $css = '';
        if (isset($obj->typography)) {
            $css .= $this->getTypographyRule($obj->typography, 'text-decoration');
        }
        if (isset($obj->typography->{'text-align'})) {
            $align = str_replace('left', 'flex-start', $obj->typography->{'text-align'});
            $align = str_replace('right', 'flex-end', $align);
            $css .= 'align-items:'.$align.';';
        }
        if (!empty($css)) {
            $str .= "#".$selector." ul.nav.nav-tabs li a {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->typography->{'text-decoration'})) {
            $str .= "#".$selector." li span.tabs-title {";
            $str .= "text-decoration : ".$obj->typography->{'text-decoration'}.";";
            $str .= "}";
        }
        if (isset($obj->icon->size)) {
            $str .= "#".$selector." ul.nav.nav-tabs li a i {";
            $str .= "font-size: ".$this->getValueUnits($obj->icon->size).";";
            $str .= "}";
        }
        if (isset($obj->hover->color)) {
            $str .= "#".$selector." ul.nav.nav-tabs li.active a {";
            $str .= "color : ".$this->getCorrectColor($obj->hover->color).";";
            $str .= "}";
        }
        if (isset($obj->hover->color)) {
            $str .= "#".$selector." ul.nav.nav-tabs li.active a:before {";
            $str .= "background-color : ".$this->getCorrectColor($obj->hover->color).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->header->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->header->color).";";
        }
        if (isset($obj->header->border)) {
            $css .= "border-color: ".$this->getCorrectColor($obj->header->border).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." ul.nav.nav-tabs {";
            $str .= $css;
            $str .= "}";
        }

        return $str;
    }

    public function getAccordionRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->border->color)) {
            $str .= "#".$selector." .accordion-group, #".$selector." .accordion-inner {";
            $str .= "border-color: ".$this->getCorrectColor($obj->border->color).";"; 
            $str .= "}";
        }
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .accordion-inner {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .accordion-inner:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .accordion-inner");
        if (isset($obj->typography)) {
            $str .= "#".$selector." .accordion-heading a {";
            $str .= $this->getTypographyRule($obj->typography, 'text-decoration');
            $str .= "}";
        }
        if (isset($obj->typography->{'text-decoration'})) {
            $str .= "#".$selector." .accordion-heading span.accordion-title {";
            $str .= "text-decoration: ".$obj->typography->{'text-decoration'}.";";
            $str .= "}";
        }
        if (isset($obj->icon->size)) {
            $str .= "#".$selector." .accordion-heading a i {";
            $str .= "font-size: ".$this->getValueUnits($obj->icon->size).";";
            $str .= "}";
        }
        if (isset($obj->header->color)) {
            $str .= "#".$selector." .accordion-heading {";
            $str .= "background-color: ".$this->getCorrectColor($obj->header->color).";";
            $str .= "}";
        }
        if (isset($obj->icon->position)) {
            $str .= "#".$selector.' .accordion-toggle > span {';
            $str .= 'flex-direction: '.($obj->icon->position == 'icon-position-left' ? 'row-reverse' : 'row').';';
            $str .= '}';
        }

        return $str;
    }

    public function getWeatherRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->city)) {
            $str .= "#".$selector." .weather .city {";
            $str .= $this->getTypographyRule($obj->city);
            $str .= "}";
        }
        if (isset($obj->condition)) {
            $str .= "#".$selector." .weather .condition {";
            $str .= $this->getTypographyRule($obj->condition);
            $str .= "}";
        }
        if (isset($obj->info)) {
            $str .= "#".$selector." .weather-info > div,#".$selector." .weather .date {";
            $str .= $this->getTypographyRule($obj->info);
            $str .= "}";
        }
        if (isset($obj->forecasts)) {
            $str .= "#".$selector." .forecast > span {";
            $str .= $this->getTypographyRule($obj->forecasts);
            $str .= "}";
        }
        if (isset($obj->view->layout) && $obj->view->layout == 'forecast-block') {
            $str .= "#".$selector.' .forecast > span {display: block;width: initial;}';
            $str .= "#".$selector.' div:not(.weather):not(.weather-info) {text-align: center;}';
            $str .= "#".$selector.' .ba-weather div.forecast {margin: 0 20px 0 10px;}';
            $str .= "#".$selector.' .ba-weather div.forecast .day-temp,';
            $str .= "#".$selector.' .ba-weather div.forecast .night-temp {margin: 0 5px;}';
            $str .= "#".$selector.' .ba-weather div.forecast span.night-temp,';
            $str .= "#".$selector.' .ba-weather div.forecast span.day-temp {padding-right: 0;width: initial;}';
        } else if (isset($obj->view->layout)) {
            $str .= "#".$selector.' .forecast > span {display: inline-block;width: 33.3%;}';
            $str .= "#".$selector.' div:not(.weather):not(.weather-info) {text-align: left;}';
            $str .= "#".$selector.' .ba-weather div.forecast .day-temp,';
            $str .= "#".$selector.' .ba-weather div.forecast .night-temp {margin: 0;}';
            $str .= "#".$selector.' .ba-weather div.forecast {margin: 0;}';
            $str .= "#".$selector.' .ba-weather div.forecast span.night-temp,';
            $str .= "#".$selector.' .ba-weather div.forecast span.day-temp {padding-right: 1.5%;width: 14%;}';
        }

        return $str;
    }

    public function getCommentsBoxRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-comment-message, #".$selector." .user-comment-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-comment-message:hover, #".$selector." .user-comment-wrapper:hover";
        $str .= $this->getStateRule($query, 'hover');
        $query = "#".$selector." .ba-comment-message, #".$selector." .user-comment-wrapper";
        $str .= $this->getTransitionRule($query);
        if (isset($obj->typography)) {
            $str .= "#".$selector." .comment-message, #".$selector." .ba-comment-message::placeholder, ";
            $str .= "#".$selector." .ba-comments-total-count-wrapper select, #".$selector." .ba-comment-message, ";
            $str .= "#".$selector." .comment-delete-action, #".$selector." .comment-edit-action, ";
            $str .= "#".$selector." .comment-likes-action-wrapper > span > span, ";
            $str .= "#".$selector." .ba-review-rate-title, ";
            $str .= "#".$selector." span.ba-comment-attachment-trigger, ";
            $str .= "#".$selector." .comment-likes-wrapper .comment-action-wrapper > span.comment-reply-action > span, ";
            $str .= "#".$selector." .comment-likes-wrapper .comment-action-wrapper > span.comment-share-action > span, ";
            $str .= "#".$selector." .comment-user-date, #".$selector." .ba-social-login-wrapper > span, ";
            $str .= "#".$selector." .ba-user-login-btn, #".$selector." .ba-guest-login-btn, ";
            $str .= "#".$selector." .comment-logout-action, ";
            $str .= "#".$selector." .comment-user-name, #".$selector." .ba-comments-total-count {";
            $str .= $this->getTypographyRule($obj->typography);
            $str .= "}";
        }

        return $str;
    }

    public function getSimpleGalleryRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        /*
        if (isset($this->object->images) &&
            (isset($obj->images) || ($this->breakpoint != 'desktop' && gridboxHelper::$website->adaptive_images == 1))) {
            foreach ($this->object->images as $ind => $image) {
                $str .= '#'.$selector.' .ba-instagram-image:nth-child('.($ind * 1 + 1).'):not(.lazy-load-image) {';
                $str .= "background-image: url(".$this->setBackgroundImage($image).");";
                $str .= '}';
            }
        }
        */
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $str .= "#".$selector." .ba-instagram-image {";
            $str .= $this->get('border', $obj->border, 'default');
            $str .= "}";
        }
        if (isset($obj->animation->duration)) {
            $this->transitions[] = 'transform '.$obj->animation->duration.'s';
        }
        $str .= $this->getStateRule("#".$selector." .ba-instagram-image:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-instagram-image");
        if (isset($obj->view->height)) {
            $str .= "#".$selector." .instagram-wrapper:not(.simple-gallery-masonry-layout) .ba-instagram-image {";
            $str .= "height: ".$this->getValueUnits($obj->view->height).";";
            $str .= "}";
        }
        if (isset($obj->gutter)) {
            $str .= "#".$selector." .instagram-wrapper:not(.simple-gallery-masonry-layout) {";
            $str .= "grid-gap: ".($obj->gutter ? 10 : 0)."px;";
            $str .= "}";
        }
        if (isset($obj->count)) {
            $str .= "#".$selector." .instagram-wrapper {";
            $str .= "grid-template-columns: repeat(auto-fill, minmax(calc((100% / ".$obj->count.") - 20px),1fr));";
            $str .= "}";
        }
        if (isset($obj->animation->duration)) {
            $str .= "#".$selector." .ba-instagram-image > * {";
            $str .= "--transition-duration: ".$obj->animation->duration."s;";
            $str .= "}";
        }
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= "#".$selector." .ba-simple-gallery-caption .ba-caption-overlay {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography);
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-simple-gallery-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-simple-gallery-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-simple-gallery-title");
        $css = '';
        if (isset($obj->description->typography)) {
            $css .= $this->getTypographyRule($obj->description->typography);
        }
        if (isset($obj->description->margin)) {
            $css .= $this->get('margin', $obj->description->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-simple-gallery-description {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-simple-gallery-description:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-simple-gallery-description");

        return $str;
    }

    public function getOverlayRules($obj, $key = 'overlay')
    {
        $empty = new stdClass();
        $overlay = isset($obj->{$key}) ? gridboxHelper::object_extend($empty, $obj->{$key}) : null;
        $top = $this->object->{$key};
        if ($overlay && isset($top->type) && $top->type == 'gradient' && (isset($overlay->gradient) || isset($overlay->type))) {
            $overlay = $top;
        }
        $css = '';
        $states = 'overlay-states';
        $object = isset($obj->{$states}) ? $obj->{$states} : (isset($obj->{$key}->{$states}) ? $obj->{$key}->{$states} : null);
        $desktop = isset($this->object->{$states}) ? $this->object->{$states} : (isset($top->{$states}) ? $top->{$states} : null);
        if ((!$object || $top->type == 'gradient' || ($top->type == 'blur' && !isset($desktop->default->blur))) && $overlay) {
            $css = $this->get('overlay', $overlay, 'default');
        } else if ($object) {
            $css = $this->get('overlay', $object, 'default');
        }

        return $css;
    }

    public function getLottieRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->style->align)) {
            $css .= "text-align: ".$obj->style->align.";";
        }
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->style->width) && $obj->style->width !== '') {
            $css .= "width: ".$this->getValueUnits($obj->style->width).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-lottie-animations-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-lottie-animations-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-lottie-animations-wrapper");

        return $str;
    }

    public function getImageRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->style->align)) {
            $css .= "text-align: ".$obj->style->align.";";
        }
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->style->width) && $obj->style->width !== '') {
            $css .= "width: ".$this->getValueUnits($obj->style->width).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-image-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-image-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-image-wrapper");
        if (isset($obj->animation->duration)) {
            $str .= "#".$selector." .ba-image-wrapper {";
            $str .= "--transition-duration: ".$obj->animation->duration."s;";
            $str .= "}";
        }
        if (isset($obj->overlay)) {
            $str .= "#".$selector." .ba-image-item-caption .ba-caption-overlay {";
            $str .= $this->getOverlayRules($obj);
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography);
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-image-item-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-image-item-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-image-item-title");
        $css = '';
        if (isset($obj->description->typography)) {
            $css .= $this->getTypographyRule($obj->description->typography);
        }
        if (isset($obj->description->margin)) {
            $css .= $this->get('margin', $obj->description->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-image-item-description {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-image-item-description:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-image-item-description");

        return $str;
    }

    public function getOnePageRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->nav->margin)) {
            $str .= "#".$selector." .integration-wrapper > ul > li {";
            $str .= $this->get('margin', $obj->nav->margin, 'default', null, null, ['hover', 'active']);
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .integration-wrapper > ul > li.active", 'hover');
        $str .= $this->getStateRule("#".$selector." .integration-wrapper > ul > li:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->nav->icon->size)) {
            $str .= "#".$selector." i.ba-menu-item-icon {";
            $str .= "font-size: ".$this->getValueUnits($obj->nav->icon->size).";";
            $str .= "}";
        }
        $css = '';
        $query = "#".$selector." .main-menu li";
        if (isset($obj->{'nav-typography'})) {
            $css .= $this->getTypographyRule($obj->{'nav-typography'});
        }
        if (isset($obj->nav->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->nav->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->nav, 'default', null, null, ['hover', 'active']);
        }
        if (isset($obj->nav->padding)) {
            $css .= $this->get('padding', $obj->nav->padding, 'default', null, null, ['hover', 'active']);
        }
        if (isset($obj->nav->border)) {
            $this->cascade->border = $this->object->nav->border;
            $css .= $this->get('border', $obj->nav->border, 'default', null, null, ['hover', 'active']);
        }
        if (!empty($css)) {
            $str .= $query." a {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.".active > a", 'active');
        $str .= $this->getStateRule($query." a:hover, ".$query.".active a:hover", 'hover');
        $str .= $this->getTransitionRule($query." a");
        if (isset($obj->{'nav-typography'}->{'text-align'})) {
            $str .= "#".$selector." ul {";
            $str .= "text-align : ".$obj->{'nav-typography'}->{'text-align'}.";";
            $str .= "}";
        }

        return $str;
    }

    public function getMenuRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $query = "#".$selector." > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li";
        if (isset($obj->nav->margin)) {
            $str .= $query." {";
            $str .= $this->get('margin', $obj->nav->margin, 'default', null, null, ['hover', 'active']);
            $str .= "}";
        }
        $str .= $this->getStateRule($query.".active", 'hover');
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->nav->icon->size)) {
            $str .= $query." > * > i.ba-menu-item-icon {";
            $str .= "font-size: ".$this->getValueUnits($obj->nav->icon->size).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->{'nav-typography'})) {
            $css .= $this->getTypographyRule($obj->{'nav-typography'});
        }
        if (isset($obj->nav->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->nav->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->nav, 'default', null, null, ['hover', 'active']);
        }
        if (isset($obj->nav->padding)) {
            $css .= $this->get('padding', $obj->nav->padding, 'default', null, null, ['hover', 'active']);
        }
        if (isset($obj->nav->border)) {
            $this->cascade->border = $this->object->nav->border;
            $css .= $this->get('border', $obj->nav->border, 'default', null, null, ['hover', 'active']);
        }
        if (!empty($css)) {
            $str .= $query." > *:not(ul):not(div) {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.".active > *:not(ul):not(div), ".$query.".current > *:not(ul):not(div)", 'active');
        $str .= $this->getStateRule($query." > *:not(ul):not(div):hover", 'hover');
        $query = "#".$selector." > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li";
        $str .= $this->getTransitionRule($query." > *:not(ul):not(div)");
        $query = "#".$selector." .main-menu li.deeper.parent > ul li";
        if (isset($obj->sub->icon->size)) {
            $str .= $query." i.ba-menu-item-icon {";
            $str .= "font-size: ".$this->getValueUnits($obj->sub->icon->size).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->{'sub-typography'})) {
            $css .= $this->getTypographyRule($obj->{'sub-typography'});
        }
        if (isset($obj->sub->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->sub->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->sub, 'default', null, null, ['hover', 'active']);
        }
        if (isset($obj->sub->padding)) {
            $css .= $this->get('padding', $obj->sub->padding, 'default', null, null, ['hover', 'active']);
        }
        if (isset($obj->sub->border)) {
            $this->cascade->border = $this->object->sub->border;
            $css .= $this->get('border', $obj->sub->border, 'default', null, null, ['hover', 'active']);
        }
        if (!empty($css)) {
            $str .= $query." > *:not(ul):not(div) {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.".active > *:not(ul):not(div)", 'active');
        $str .= $this->getStateRule($query." > *:not(ul):not(div):hover", 'hover');
        $query = "#".$selector." .main-menu li.deeper.parent > ul li";
        $str .= $this->getTransitionRule($query." > *:not(ul):not(div)");
        if (isset($obj->{'nav-typography'}->{'text-align'})) {
            $str .= "#".$selector." > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul {";
            $str .= "text-align : ".$obj->{'nav-typography'}->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->dropdown->animation->duration)) {
            $css .= "animation-duration: ".$obj->dropdown->animation->duration."s;";
        }
        $query = "#".$selector." li.deeper.parent > ul, ";
        $query .= "#".$selector." li.megamenu-item > .tabs-content-wrapper > .ba-section";
        if (!empty($css)) {
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->dropdown->padding)) {
            $css .= $this->get('padding', $obj->dropdown->padding, 'default');
        }
        if (isset($obj->dropdown->border)) {
            $this->cascade->border = $this->object->dropdown->border;
            $css .= $this->get('border', $obj->dropdown->border, 'default');
        }
        if (!empty($css)) {
            $str .="#".$selector." li.deeper.parent > ul {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." li.deeper.parent > ul:hover, ";
        $query .= "#".$selector." li.megamenu-item > .tabs-content-wrapper > .ba-section:hover";
        $str .= $this->getStateRule($query, 'hover');
        $query = "#".$selector." li.deeper.parent > ul, ";
        $query .= "#".$selector." li.megamenu-item > .tabs-content-wrapper > .ba-section";
        $str .= $this->getTransitionRule($query);

        return $str;
    }

    public function getContentSliderRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $query = "#".$selector." > .slideshow-wrapper > .ba-slideshow";
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        if (isset($obj->view->fullscreen)) {
            $str .= "#".$selector." > .slideshow-wrapper {";
            $str .= "min-height: ".($obj->view->fullscreen ? "100vh" : "auto").";";
            $str .= "}";
        }
        if (isset($obj->view->height)) {
            $str .= "#".$selector." > .slideshow-wrapper > ul > .slideshow-content {";
            $str .= "height:".$this->getValueUnits($obj->view->height).";";
            $str .= "}";
        }
        $query = "#".$selector." > .slideshow-wrapper > .ba-slideshow > .slideshow-content > li.item > .ba-grid-column";
        if (isset($obj->padding)) {
            $str .= $query." {";
            $str .= $this->get('padding', $obj->padding, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query."");
        if (isset($obj->view->arrows)) {
            $str .= "#".$selector." > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-nav {";
            $str .= 'display:'.($obj->view->arrows == 1 ? 'block' : 'none').';';
            $str .= "}";
        }
        $query = "#".$selector." > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-nav a";
        $css = '';
        if (isset($obj->arrows->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "width: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "height: ".$this->getValueUnits($obj->arrows->size).";";
        }
        if (isset($obj->arrows->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->arrows->padding).";";
        }
        if (isset($obj->arrows->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->arrows->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->arrows, 'default');
        }
        if (isset($obj->arrows->shadow)) {
            $css .= $this->get('shadow', $obj->arrows->shadow, 'default');
        }
        if (isset($obj->arrows->border)) {
            $this->cascade->border = $this->object->arrows->border;
            $css .= $this->get('border', $obj->arrows->border, 'default');
        }
        if (!empty($css)) {
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        if (isset($obj->view->dots)) {
            $str .= "#".$selector." > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots {";
            $str .= 'display:'.($obj->view->dots == 1 ? 'flex' : 'none').';';
            $str .= "}";
        }
        $css = '';
        if (isset($obj->dots->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "width: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "height: ".$this->getValueUnits($obj->dots->size).";";
        }
        if (isset($obj->dots->normal->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->dots->normal->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots > div {";
            $str .= $css;
            $str .= "}";
        }
        if (!empty($css)) {
            $str .= "#".$selector." > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots > div:hover, ";
            $str .= "#".$selector." > .slideshow-wrapper > .ba-slideshow > .ba-slideshow-dots > div.active {";
            $str .= "color: ".$this->getCorrectColor($obj->dots->hover->color).";";
            $str .= "}";
        }
        
        
        return $str;
    }

    public function getContentSliderItemsRules($obj, $selector)
    {
        $str = '';
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= $selector." > .ba-overlay {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        $background = null;
        if (isset($obj->background)) {
            $background = $obj->background;
        }
        if ($this->breakpoint != 'desktop' && gridboxHelper::$website->adaptive_images == 1
            && $this->object->background->type == 'image' && !isset($obj->background->image->image)) {
            $background = $background ? $background : new stdClass();
            $background->image = $background->image ? $background->image : new stdClass();
            $background->image->image = $this->object->background->image->image;
        }
        if (isset($background->image) && $this->object->background->type == 'image') {
            foreach ($background->image as $key => $value) {
                if ($key == 'image') {
                    $value = "url(".$this->setBackgroundImage($value).");";
                }
                $css .= "background-".$key.": ".$value.";";
            }
            $css .= "background-color: rgba(0, 0, 0, 0);";
        } else if (isset($obj->background->color) && $this->object->background->type == 'color') {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
            $css .= "background-image: none;";
        } else if (isset($obj->background->gradient) && $this->object->background->type == 'gradient') {
            $this->gradient($this->object->background->gradient);
            $css .= $this->css;
        } else if (isset($obj->background->type) && $obj->background->type != 'image'
            && $obj->background->type != 'color' && $obj->background->type != 'gradient') {
            $css .= "background-image: none;";
            $css .= "background-color: rgba(0, 0, 0, 0);";
        }
        if (!empty($css)) {
            $str .= $selector." > .ba-slideshow-img {";
            $str .= $css;
            $str .= "}";
        }
        
        return $str;
    }

    public function getRecentSliderRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block", '#'.$selector);
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->overflow) || isset($obj->slideset->count)) {
            $overflow = $this->object->overflow;
            $count = $this->object->slideset->count;
            $str .= "#".$selector." ul.carousel-type .slideshow-content {";
            $str .= "width: ".($overflow ? "calc(100% + (100% / ".$count.") * 2)" : '100%').";";
            $str .= "margin-left: ".($overflow ? "calc((100% / ".$count.") * -1)" : 'auto').";";
            $str .= "}";
        }
        if (isset($obj->gutter) || isset($obj->slideset->count)) {
            $count = $this->object->slideset->count;
            $margin = ($this->object->gutter ? 30 : 0) * ($count - 1);
            $str .= "#".$selector." ul.carousel-type li {";
            $str .= "width: calc((100% - ".$margin."px) / ".$count.");";
            $str .= "}";
        }
        $str .= "#".$selector." ul.carousel-type:not(.slideset-loaded) li {";
        $str .= "position: relative; float:left;";
        $str .= "}";
        if (isset($obj->gutter)) {
            $str .= "#".$selector." ul.carousel-type:not(.slideset-loaded) li.item.active:not(:first-child) {";
            $str .= "margin-left: ".($obj->gutter ? 30 : 0)."px;";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->view->fullscreen)) {
            $css .= "min-height: ".($obj->view->fullscreen ? "100vh" : "auto").";";
        }
        if (isset($obj->view->height)) {
            $css .= "height:".$this->getValueUnits($obj->view->height).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." ul.slideshow-type {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->view->height)) {
            $str .= "#".$selector." ul.carousel-type .ba-slideshow-img {";
            $str .= "height:".$this->getValueUnits($obj->view->height).";";
            $str .= "}";
        }
        if (isset($obj->view->size)) {
            $str .= "#".$selector." .ba-slideshow-img {";
            $str .= "background-size :".$obj->view->size.";";
            $str .= "}";
        }
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= "#".$selector." .slideset-wrapper .ba-overlay {";
            $str .= $css;
            $str .= '}';
        }
        $str .= $this->getStateRule("#".$selector." .slideset-wrapper:hover .ba-overlay", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-overlay");
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-caption {";
            $str .= $this->get('overlay', $obj->overlay, 'default');
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (isset($obj->view->title)) {
            $css .= 'display:'.($obj->view->title ? 'block' : 'none').';';
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-title");
        $price = isset($obj->title) ? $obj->title : null;
        $price = isset($this->object->price) && is_object($this->object->price) && isset($obj->price) ? $obj->price : $price;
        if (isset($price->margin)) {
            $str .= "#".$selector." .ba-blog-post-add-to-cart-wrapper {";
            $str .= $this->get('margin', $price->margin, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-add-to-cart-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-add-to-cart-wrapper");
        $css = '';
        if (isset($price->typography)) {
            $css .= $this->getTypographyRule($price->typography, 'text-align');
        }
        if (isset($price->typography->{'text-align'})) {
            $align = str_replace('left', 'flex-start', $price->typography->{'text-align'});
            $align = str_replace('right', 'flex-end', $align);
            $css .= "align-items: ".$align.";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-add-to-cart-price {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->title->typography)) {
            $str .= "#".$selector." .ba-blog-post-title {";
            $str .= $this->getTypographyRule($obj->title->typography);
            $str .= "}";
        }
        if (isset($obj->title->hover->color)) {
            $str .= "#".$selector." .ba-blog-post-title:hover {";
            $str .= "color: ".$this->getCorrectColor($obj->title->hover->color).";";
            $str .= "}";
        }
        $css = '';
        $justify = '';
        if (isset($obj->reviews->typography->{'text-align'})) {
            $justify = str_replace('left', 'flex-start', $obj->reviews->typography->{'text-align'});
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "justify-content: ".$justify.";";
        }
        if (isset($obj->reviews->typography)) {
            $css .= $this->getTypographyRule($obj->reviews->typography, 'text-align');
        }
        if (isset($obj->reviews->margin)) {
            $css .= $this->get('margin', $obj->reviews->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-reviews {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-reviews:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-reviews");
        if (isset($obj->reviews->hover->color)) {
            $str .= "#".$selector." .ba-blog-post-reviews a:hover {";
            $str .= "color: ".$this->getCorrectColor($obj->reviews->hover->color).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->postFields->typography)) {
            $css .= $this->getTypographyRule($obj->postFields->typography, 'text-align');
        }
        if (isset($obj->postFields->margin)) {
            $css .= $this->get('margin', $obj->postFields->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-field-row-wrapper {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-blog-post-field-row-wrapper:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-field-row-wrapper");
        }
        $css = '';
        if (isset($obj->info->margin)) {
            $css .= $this->get('margin', $obj->info->margin, 'default');
        }
        if (isset($obj->info->typography->{'text-align'})) {
            $justify = str_replace('left', 'flex-start', $obj->info->typography->{'text-align'});
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "justify-content: ".$justify.";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-info-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-info-wrapper");
        if (isset($obj->info->typography)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper > span * {";
            $str .= $this->getTypographyRule($obj->info->typography, 'text-align');
            $str .= "}";
        }
        if (isset($obj->info->typography->color)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper > span {";
            $str .= "color: ".$this->getCorrectColor($obj->info->typography->color).";";
            $str .= "}";
        }
        if (isset($obj->info->hover->color)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper > * a:hover {";
            $str .= "color: ".$this->getCorrectColor($obj->info->hover->color).";";
            $str .= "}";
        }
        if (isset($obj->button->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-button {";
            $str .= "text-align :".$obj->button->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->intro->typography)) {
            $css .= $this->getTypographyRule($obj->intro->typography);
        }
        if (isset($obj->intro->margin)) {
            $css .= $this->get('margin', $obj->intro->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-intro-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-intro-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-intro-wrapper");
        if (isset($obj->button->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-blog-post-button-wrapper {";
            $str .= "text-align :".$obj->button->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->button->margin)) {
            $str .= "#".$selector." .ba-blog-post-button-wrapper a {";
            $str .= $this->get('margin', $obj->button->margin, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-button-wrapper a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-button-wrapper a");
        $css = '';
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default');
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-button-wrapper a, #".$selector." .ba-blog-post-add-to-cart {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-blog-post-button-wrapper a:hover, #".$selector." .ba-blog-post-add-to-cart:hover";
        $str .= $this->getStateRule($query, 'hover');
        $str .= $this->getTransitionRule($query);
        $css = '';
        if (isset($obj->arrows->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "width: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "height: ".$this->getValueUnits($obj->arrows->size).";";
        }
        if (isset($obj->arrows->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->arrows->padding).";";
        }
        if (isset($obj->arrows->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->arrows->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->arrows, 'default');
        }
        if (isset($obj->arrows->shadow)) {
            $css .= $this->get('shadow', $obj->arrows->shadow, 'default');
        }
        if (isset($obj->arrows->border)) {
            $this->cascade->border = $this->object->arrows->border;
            $css .= $this->get('border', $obj->arrows->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideset-nav a {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideset-nav a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideset-nav a");
        $css = '';
        if (isset($obj->dots->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "width: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "height: ".$this->getValueUnits($obj->dots->size).";";
        }
        if (isset($obj->dots->normal->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->dots->normal->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideset-dots > div {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->dots->hover->color)) {
            $str .= "#".$selector." .ba-slideset-dots > div:hover,#".$selector." .ba-slideset-dots > div.active {";
            $str .= "color: ".$this->getCorrectColor($obj->dots->hover->color).";";
            $str .= "}";
        }
        
        return $str;
    }

    public function getTestimonialsRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block", '#'.$selector);
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->slideset->count)) {
            $margin = 30 * ($obj->slideset->count - 1);
            $str .= "#".$selector." li {";
            $str .= "width: calc((100% - ".$margin."px) / ".$obj->slideset->count.");";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .slideshow-content .testimonials-wrapper, #".$selector." .testimonials-info {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .slideshow-content .testimonials-wrapper:hover, #".$selector." .testimonials-info:hover";
        $str .= $this->getStateRule($query, 'hover');
        $query = "#".$selector." .slideshow-content .testimonials-wrapper, #".$selector." .testimonials-info";
        $str .= $this->getTransitionRule($query);
        $css = '';
        if (isset($obj->background->color)) {
            $css .= "border-color: ".$this->getCorrectColor($obj->background->color).";";
        }
        if (isset($obj->image->width)) {
            $css .= "left: calc(".$this->getValueUnits($obj->image->width)." / 2);";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .testimonials-info:before {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->icon->size)) {
            $css .= "width : ".$this->getValueUnits($obj->icon->size).";";
            $css .= "height : ".$this->getValueUnits($obj->icon->size).";";
            $css .= "font-size : ".$this->getValueUnits($obj->icon->size).";";
        }
        if (isset($obj->icon->color)) {
            $css .= "color : ".$this->getCorrectColor($obj->icon->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .testimonials-icon-wrapper i {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->image->width)) {
            $css .= "width:".$this->getValueUnits($obj->image->width).";";
            $css .= "height:".$this->getValueUnits($obj->image->width).";";
        }
        if (isset($obj->image->border)) {
            $this->cascade->border = $this->object->image->border;
            $css .= $this->get('border', $obj->image->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .testimonials-img, ";
            $str .= "#".$selector." ul.style-6 .ba-slideset-dots div {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .testimonials-img:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .testimonials-img");
        if (isset($obj->name->typography)) {
            $str .= "#".$selector." .ba-testimonials-name {";
            $str .= $this->getTypographyRule($obj->name->typography);
            $str .= "}";
        }
        if (isset($obj->testimonial->typography)) {
            $str .= "#".$selector." .ba-testimonials-testimonial {";
            $str .= $this->getTypographyRule($obj->testimonial->typography);
            $str .= "}";
        }
        if (isset($obj->caption->typography)) {
            $str .= "#".$selector." .ba-testimonials-caption {";
            $str .= $this->getTypographyRule($obj->caption->typography);
            $str .= "}";
        }
        if (isset($obj->view->arrows)) {
            $padding = $this->object->arrows->padding;
            $size = $this->object->arrows->size;
            $width = $obj->view->arrows == 1 ? (40 + (($padding ? $padding : 0) * 2) + ($size ? $size : 0) * 1 ) * 2 : 50;
            $str .= "#".$selector." .testimonials-slideshow-content-wrapper {";
            $str .= "width: calc(100% - ".$this->getValueUnits($width).");";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->arrows->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "width: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "height: ".$this->getValueUnits($obj->arrows->size).";";
        }
        if (isset($obj->arrows->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->arrows->padding).";";
        }
        if (isset($obj->arrows->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->arrows->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->arrows, 'default');
        }
        if (isset($obj->arrows->shadow)) {
            $css .= $this->get('shadow', $obj->arrows->shadow, 'default');
        }
        if (isset($obj->arrows->border)) {
            $this->cascade->border = $this->object->arrows->border;
            $css .= $this->get('border', $obj->arrows->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideset-nav a {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideset-nav a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideset-nav a");
        $css = '';
        if (isset($obj->dots->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "width: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "height: ".$this->getValueUnits($obj->dots->size).";";
        }
        if (isset($obj->dots->normal->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->dots->normal->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideset-dots > div {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->dots->hover->color)) {
            $str .= "#".$selector." .ba-slideset-dots > div:hover,#".$selector." .ba-slideset-dots > div.active {";
            $str .= "color: ".$this->getCorrectColor($obj->dots->hover->color).";";
            $str .= "}";
        }
        
        return $str;
    }

    public function getCarouselRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->overflow) || isset($obj->slideset->count)) {
            $overflow = $this->object->overflow;
            $count = $this->object->slideset->count;
            $str .= "#".$selector." .slideshow-content {";
            $str .= "width: ".($overflow ? "calc(100% + (100% / ".$count.") * 2)" : '100%').";";
            $str .= "margin-left: ".($overflow ? "calc((100% / ".$count.") * -1)" : 'auto').";";
            $str .= "}";
        }
        if (isset($obj->gutter) || isset($obj->slideset->count)) {
            $margin = $this->object->gutter ? 30 : 0;
            $count = $this->object->slideset->count * 1;
            $margin = $margin * ($count - 1);
            $str .= "#".$selector." li {";
            $str .= "width: calc((100% - ".$margin."px) / ".$count.");";
            $str .= "}";
        }
        if (isset($obj->gutter)) {
            $str .= "#".$selector." ul:not(.slideset-loaded) li.item.active:not(:first-child) {";
            $str .= "margin-left: ".($obj->gutter ? 30 : 0)."px;";
            $str .= "}";
        }
        if (isset($obj->slides) || ($this->breakpoint != 'desktop' && gridboxHelper::$website->adaptive_images == 1)) {
            $key = 1;
            foreach ($this->object->slides as $slide) {
                if (!empty($slide->image) && (!isset($slide->unpublish) || !$slide->unpublish)) {
                    $str .= "#".$selector." li.item:nth-child(".($key++).") .ba-slideshow-img {";
                    $str .= "background-image: url(".$this->setBackgroundImage($slide->image).");";
                    $str .= "}"; 
                }
            }
        }
        $css = '';
        if (isset($obj->view->size)) {
            $css .= "background-size :".$obj->view->size.";";
        }
        if (isset($obj->view->height)) {
            $css .= "height:".$this->getValueUnits($obj->view->height).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-img {";
            $str .= $css;
            $str .= "}";
        }
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-caption {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->title->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-title-wrapper {";
            $str .= "text-align :".$obj->title->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, 'text-align');
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideshow-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideshow-title");
        if (isset($obj->description->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-description-wrapper {";
            $str .= "text-align :".$obj->description->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->description->typography)) {
            $css .= $this->getTypographyRule($obj->description->typography, 'text-align');
        }
        if (isset($obj->description->margin)) {
            $css .= $this->get('margin', $obj->description->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-description {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideshow-description:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideshow-description");
        if (isset($obj->button->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-button {";
            $str .= "text-align :".$obj->button->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->button->margin)) {
            $css .= $this->get('margin', $obj->button->margin, 'default');
        }
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default');
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .slideshow-button:not(.empty-content) a {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .slideshow-button a";
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        $css = '';
        if (isset($obj->arrows->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "width: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "height: ".$this->getValueUnits($obj->arrows->size).";";
        }
        if (isset($obj->arrows->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->arrows->padding).";";
        }
        if (isset($obj->arrows->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->arrows->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->arrows, 'default');
        }
        if (isset($obj->arrows->shadow)) {
            $css .= $this->get('shadow', $obj->arrows->shadow, 'default');
        }
        if (isset($obj->arrows->border)) {
            $this->cascade->border = $this->object->arrows->border;
            $css .= $this->get('border', $obj->arrows->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideset-nav a {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideset-nav a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideset-nav a");
        $css = '';
        if (isset($obj->dots->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "width: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "height: ".$this->getValueUnits($obj->dots->size).";";
        }
        if (isset($obj->dots->normal->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->dots->normal->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideset-dots > div {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->dots->hover->color)) {
            $str .= "#".$selector." .ba-slideset-dots > div:hover,#".$selector." .ba-slideset-dots > div.active {";
            $str .= "color: ".$this->getCorrectColor($obj->dots->hover->color).";";
            $str .= "}";
        }
        
        return $str;
    }

    public function getValueUnits($value)
    {
        $value = strval($value);
        $match = preg_match('/\s{0,1}[a-zA-Z%]+/', $value);
        
        return $value.($match ? '' : 'px');
    }

    public function getBeforeAfterSliderRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-before-after-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-before-after-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-before-after-wrapper");
        $css = '';
        if (isset($obj->slider->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->slider->size).";";
        }
        if (isset($obj->slider->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->slider->padding).";";
        }
        if (isset($obj->slider->shadow)) {
            $css .= $this->get('shadow', $obj->slider->shadow, 'default');
        }
        if (isset($obj->slider->border)) {
            $this->cascade->border = $this->object->slider->border;
            $css .= $this->get('border', $obj->slider->border, 'default');
        }
        if (isset($obj->slider->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->slider->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->slider, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-before-after-slider {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-before-after-slider:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-before-after-slider");
        $css = '';
        if (isset($obj->divider->color)) {
            $css .= "--divider-color: ".$this->getCorrectColor($obj->divider->color).";";
        }
        if (isset($obj->divider->width)) {
            $css .= "--divider-width: ".$this->getValueUnits($obj->divider->width).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-before-after-divider {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, 'text-align');
        }
        if (isset($obj->title->background->color)) {
            $css .= "background-color: ".$this->getCorrectColor($obj->title->background->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-before-after-label {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->title->typography->{'text-align'})) {
            $justify = str_replace('left', 'flex-start', $obj->title->typography->{'text-align'});
            $justify = str_replace('right', 'flex-end', $justify);
            $str .= "#".$selector." .ba-before-after-overlay {";
            $str .= "align-items:".$justify;
            $str .= "}";
        }

        return $str;
    }

    public function getSlideshowRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->slides) || ($this->breakpoint != 'desktop' && gridboxHelper::$website->adaptive_images == 1)) {
            $key = 1;
            foreach ($this->object->slides as $i => $slide) {
                if (isset($slide->unpublish) && $slide->unpublish) {
                    continue;
                }
                if ($slide->type == 'image') {
                    $str .= "#".$selector." li.item:nth-child(".$key.") .ba-slideshow-img, ";
                    $str .= "#".$selector.' .thumbnails-dots div[data-ba-slide-to="'.($key * 1 - 1).'"] {';
                    $str .= "background-image: url(".$this->setBackgroundImage($slide->image).");";
                    $str .= "}";
                } else if (isset($obj->slides->{$i}) && $slide->type == 'video' && $slide->video->type == 'youtube') {
                    $str .= "#".$selector.' .thumbnails-dots div[data-ba-slide-to="'.($key * 1 - 1).'"] {';
                    $str .= 'background-image: url(https://img.youtube.com/vi/'.$slide->video->id.'/maxresdefault.jpg);';
                    $str .= "}";
                } else if (isset($obj->slides->{$i}) && $slide->type == 'video' && $slide->video->type == 'vimeo') {
                    $str .= "#".$selector.' .thumbnails-dots div[data-ba-slide-to="'.($key * 1 - 1).'"] {';
                    $str .= 'background-image: url(https://vumbnail.com/'.$slide->video->id.'.jpg);';
                    $str .= "}";
                } else if (isset($obj->slides->{$i}) && $slide->type == 'video' && !isset($slide->video->thumbnail)) {
                    $str .= "#".$selector.' .thumbnails-dots div[data-ba-slide-to="'.($key * 1 - 1).'"] {';
                    $str .= 'background-image: url('.JUri::root().'components/com_gridbox/assets/images/thumb-square.png);';
                    $str .= "}";
                }
                $key++;
            }
        }
        if (isset($obj->view->fullscreen)) {
            $str .= "#".$selector." .slideshow-wrapper {";
            $str .= "min-height: ".($obj->view->fullscreen ? "100vh" : "auto").";";
            $str .= "}";
        }
        if (isset($obj->view->height)) {
            $str .= "#".$selector." .slideshow-content, #".$selector." .empty-list {";
            $str .= "height:".$this->getValueUnits($obj->view->height).";";
            $str .= "}";
        }
        if (isset($obj->view->size)) {
            $str .= "#".$selector." .ba-slideshow-img, #".$selector." .thumbnails-dots div {";
            $str .= "background-size :".$obj->view->size.";";
            $str .= "}";
        }
        $css = $this->getOverlayRules($obj);
        if (isset($obj->view->height)) {
            $css .= "height:".$this->getValueUnits($obj->view->height).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-overlay {";
            $str .= $css;
            $str .= '}';
        }
        $str .= $this->getStateRule("#".$selector." .slideshow-wrapper:hover .ba-overlay", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-overlay");
        if (isset($obj->title->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-title-wrapper {";
            $str .= "text-align :".$obj->title->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->animation->duration)) {
            $css .= "animation-duration :".$obj->title->animation->duration."s;";
        }
        if (isset($obj->title->animation->delay)) {
            $css .= "animation-delay :".$obj->title->animation->delay."s;";
        }
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, 'text-align');
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }

        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideshow-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideshow-title");
        if (isset($obj->description->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-description-wrapper {";
            $str .= "text-align :".$obj->description->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->description->animation->duration)) {
            $css .= "animation-duration :".$obj->description->animation->duration."s;";
        }
        if (isset($obj->description->animation->delay)) {
            $css .= "animation-delay :".$obj->description->animation->delay."s;";
        }
        if (isset($obj->description->typography)) {
            $css .= $this->getTypographyRule($obj->description->typography, 'text-align');
        }
        if (isset($obj->description->margin)) {
            $css .= $this->get('margin', $obj->description->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-description {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-slideshow-description:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-slideshow-description");
        if (isset($obj->button->typography->{'text-align'})) {
            $str .= "#".$selector." .slideshow-button {";
            $str .= "text-align :".$obj->button->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->button->animation->duration)) {
            $css .= "animation-duration :".$obj->button->animation->duration."s;";
        }
        if (isset($obj->button->animation->delay)) {
            $css .= "animation-delay :".$obj->button->animation->delay."s;";
        }
        if (isset($obj->button->margin)) {
            $css .= $this->get('margin', $obj->button->margin, 'default');
        }
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default');
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .slideshow-button:not(.empty-content) a {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .slideshow-button a";
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        $css = '';
        if (isset($obj->arrows->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "width: ".$this->getValueUnits($obj->arrows->size).";";
            $css .= "height: ".$this->getValueUnits($obj->arrows->size).";";
        }
        if (isset($obj->arrows->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->arrows->padding).";";
        }
        if (isset($obj->arrows->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->arrows->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->arrows, 'default');
        }
        if (isset($obj->arrows->shadow)) {
            $css .= $this->get('shadow', $obj->arrows->shadow, 'default');
        }
        if (isset($obj->arrows->border)) {
            $this->cascade->border = $this->object->arrows->border;
            $css .= $this->get('border', $obj->arrows->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-nav a {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-slideshow-nav a";
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        if (!isset($this->object->thumbnails) && isset($obj->view->dots)) {
            $str .= "#".$selector." .ba-slideshow-dots {";
            $str .= $this->setItemsVisability(!$obj->view->dots, "flex;");
            $str .= "}";
        }
        $css = '';
        if (isset($obj->thumbnails->count)) {
            $css .= "--thumbnails-count:".$obj->thumbnails->count.";";
        }
        if (isset($obj->thumbnails->height)) {
            $css .= "--bottom-thumbnails-height: ".$this->getValueUnits($obj->thumbnails->height).";";
        }
        if (isset($obj->thumbnails->width)) {
            $css .= "--left-thumbnails-width: ".$this->getValueUnits($obj->thumbnails->width).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .slideshow-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->dots->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "width: ".$this->getValueUnits($obj->dots->size).";";
            $css .= "height: ".$this->getValueUnits($obj->dots->size).";";
        }
        if (isset($obj->dots->normal->color)) {
            $css .= "color: ".$this->getCorrectColor($obj->dots->normal->color).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-slideshow-dots:not(.thumbnails-dots) > div {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->dots->hover->color)) {
            $str .= "#".$selector." .ba-slideshow-dots:not(.thumbnails-dots) > div:hover,#".$selector;
            $str .= " .ba-slideshow-dots:not(.thumbnails-dots) > div.active {";
            $str .= "color: ".$this->getCorrectColor($obj->dots->hover->color).";";
            $str .= "}";
        }
        
        return $str;
    }

    public function getFeatureBoxRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->view->count)) {
            $str .= "#".$selector." .ba-feature-box:nth-child(n) {";
            $str .= "width: calc((100% - ".(($obj->view->count - 1) * 30)."px) / ".$obj->view->count.");";
            $str .= "margin-right: 30px;";
            $str .= "margin-top: 30px;";
            $str .= "}";
            $str .= "#".$selector." .ba-feature-box:nth-child(".$obj->view->count."n) {";
            $str .= "margin-right: 0;";
            $str .= "}";
            for ($i = 0; $i < $obj->view->count; $i++) {
                $str .= "#".$selector." .ba-feature-box:nth-child(".($i + 1).") {";
                $str .= "margin-top: 0;";
                $str .= "}";
            }
        }
        if (isset($obj->shadow) && !isset($obj->shadow->default)) {
            $obj->shadow->default = $obj->shadow->normal;
            $obj->shadow->state = true;
            $obj->shadow->transition = $this->transition;
        }
        if (isset($obj->background) && !isset($obj->background->default)) {
            $obj->background->default = $obj->background->normal;
            $obj->background->state = true;
            $obj->background->transition = $this->transition;
        }
        $css = '';
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->background)) {
            $css .= $this->get('backgroundColor', $obj->background, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        $query = "#".$selector." .ba-feature-box";
        if (!empty($css)) {
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        if (isset($obj->title->hover->color)) {
            $str .= "#".$selector." .ba-feature-box:hover .ba-feature-title {";
            $str .= "color : ".$this->getCorrectColor($obj->title->hover->color).";";
            $str .= "}";
        }
        if (isset($obj->description->hover->color)) {
            $str .= "#".$selector." .ba-feature-box:hover .ba-feature-description-wrapper * {";
            $str .= "color : ".$this->getCorrectColor($obj->description->hover->color).";";
            $str .= "}";
        }
        if (isset($obj->icon->{'text-align'})) {
            $str .= '#'.$selector.' .ba-feature-image-wrapper[data-type="icon"] {';
            $str .= "text-align: ".$obj->icon->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->image->{'text-align'})) {
            $str .= '#'.$selector.' .ba-feature-image-wrapper:not([data-type="icon"]) {';
            $str .= "text-align: ".$obj->image->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->image->width)) {
            $css .= "width: ".$this->getValueUnits($obj->image->width).";";
        }
        if (isset($obj->image->height)) {
            $css .= "height: ".$this->getValueUnits($obj->image->height).";";
        }
        if (isset($obj->image->border)) {
            $this->cascade->border = $this->object->image->border;
            $css .= $this->get('border', $obj->image->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-feature-image {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-feature-box:hover .ba-feature-image", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-feature-image");
        $css = '';
        if (isset($obj->icon->padding)) {
            $css .= "padding : ".$this->getValueUnits($obj->icon->padding).";";
        }
        if (isset($obj->icon->size)) {
            $css .= "font-size : ".$this->getValueUnits($obj->icon->size).";";
        }
        if (isset($obj->icon->border)) {
            $this->cascade->border = $this->object->icon->border;
            $css .= $this->get('border', $obj->icon->border, 'default');
        }
        if (isset($obj->icon->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->icon->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->icon, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-feature-image-wrapper i {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-feature-box:hover .ba-feature-image-wrapper i", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-feature-image-wrapper i");
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography);
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-feature-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-feature-title:hover", 'hover');
        if (isset($obj->icon->colors)) {
            $this->updateTransitions($obj->icon->colors, 'color');
        }
        $str .= $this->getTransitionRule("#".$selector." .ba-feature-title");
        if (isset($obj->description->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-feature-description-wrapper {";
            $str .= "text-align :".$obj->description->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->description->typography)) {
            $css .= $this->getTypographyRule($obj->description->typography, 'text-align');
        }
        if (isset($obj->description->margin)) {
            $css .= $this->get('margin', $obj->description->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-feature-description-wrapper * {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-feature-description-wrapper *:hover", 'hover');
        if ($this->breakpoint == 'desktop') {
            $this->updateTransitions($obj->icon->colors, 'color');
        }
        $str .= $this->getTransitionRule("#".$selector." .ba-feature-description-wrapper *");
        if (isset($obj->button->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-feature-button {";
            $str .= "text-align :".$obj->button->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->button->margin)) {
            $css .= $this->get('margin', $obj->button->margin, 'default');
        }
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default');
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-feature-button:not(.empty-content) a {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-feature-button a";
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        
        return $str;
    }

    public function getPostIntroRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->image->height)) {
            $css .= "height :".$this->getValueUnits($obj->image->height).";";
        }
        if (isset($obj->image->fullscreen)) {
            $css .= "min-height: ".($obj->image->fullscreen ? "100vh" : "auto").";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .intro-post-wrapper.fullscreen-post {";
            $str .= $css;
            $str .= "}";
        }
        $css = $this->getOverlayRules($obj, 'image');
        if (!empty($css)) {
            $str .= "#".$selector." .ba-overlay {";
            $str .= $css;
            $str .= '}';
        }
        $str .= $this->getStateRule("#".$selector." .ba-overlay:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-overlay");
        $css = '';
        if (isset($obj->image->height)) {
            $css .= "height :".$this->getValueUnits($obj->image->height).";";
        }
        if (isset($obj->image->attachment)) {
            $css .= "background-attachment: ".$obj->image->attachment.";";
        }
        if (isset($obj->image->position)) {
            $css .= "background-position: ".$obj->image->position.";";
        }
        if (isset($obj->image->repeat)) {
            $css .= "background-repeat: ".$obj->image->repeat.";";
        }
        if (isset($obj->image->size)) {
            $css .= "background-size: ".$obj->image->size.";";
        }
        if (isset($obj->image->fullscreen)) {
            $css .= "min-height: ".($obj->image->fullscreen ? "100vh" : "auto").";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .intro-post-image {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->title->typography->{'text-align'})) {
            $str .= "#".$selector." .intro-post-title-wrapper {";
            $str .= "text-align :".$obj->title->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, 'text-align');
        }
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .intro-post-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .intro-post-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .intro-post-title");
        $css = '';
        if (isset($obj->info->typography->{'text-align'})) {
            $justify = str_replace('left', 'flex-start', $obj->info->typography->{'text-align'});
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "text-align :".$obj->info->typography->{'text-align'}.";";
            $css .= "justify-content: ".$justify.";";
        }
        if (isset($obj->info->margin)) {
            $css .= $this->get('margin', $obj->info->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .intro-post-info {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .intro-post-info:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .intro-post-info");
        if (isset($obj->info->typography)) {
            $str .= "#".$selector." .intro-post-info *:not(i):not(a) {";
            $str .= $this->getTypographyRule($obj->info->typography);
            $str .= "}";
        }
        if (isset($obj->info->typography->{'text-align'})) {
            $str .= "#".$selector." .intro-category-author-social-wrapper {";
            $str .= "text-align: ".$obj->info->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->info->typography->color)) {
            $str .= "#".$selector." .intro-category-author-social-wrapper a {";
            $str .= "color: ".$this->getCorrectColor($obj->info->typography->color).";";
            $str .= "}";
        }
        
        return $str;
    }

    public function getAuthorRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->padding)) {
            $str .= "#".$selector." .ba-posts-author-wrapper .ba-post-author {";
            $str .= $this->get('padding', $obj->padding, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-posts-author-wrapper .ba-post-author:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-posts-author-wrapper .ba-post-author");
        if (isset($obj->view->count)) {
            $str .= "#".$selector." .ba-grid-layout .ba-post-author:nth-child(n) {";
            $str .= "margin-top: 30px;";
            $str .= "width: calc((100% / ".$obj->view->count.") - 21px);";
            $str .= "}";
            for ($i = 0; $i < $obj->view->count; $i++) {
                $str .= "#".$selector." .ba-grid-layout .ba-post-author:nth-child(".($i + 1).") {";
                $str .= "margin-top: 0;";
                $str .= "}";
            }
        }
        $css = '';
        if (isset($obj->background)) {
            $css .= $this->get('backgroundColor', $obj->background, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-post-author {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-post-author:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-post-author");
        if (isset($obj->image->border)) {
            $this->cascade->border = $this->object->image->border;
            $str .= "#".$selector." .ba-post-author-image {";
            $str .= $this->get('border', $obj->image->border, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-post-author-image:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-post-author-image");
        $css = '';
        if (isset($obj->image->width)) {
            $css .= "width :".$this->getValueUnits($obj->image->width).";";
        }
        if (isset($obj->image->height)) {
            $css .= "height :".$this->getValueUnits($obj->image->height).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-post-author-image {";
            $str .= $css;
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography);
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-post-author-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-post-author-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-post-author-title");
        if (isset($obj->intro->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-post-author-social-wrapper {";
            $str .= "text-align: ".$obj->intro->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->intro->typography->color)) {
            $str .= "#".$selector." .ba-post-author-social-wrapper a {";
            $str .= "color: ".$this->getCorrectColor($obj->intro->typography->color).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->intro->typography)) {
            $css .= $this->getTypographyRule($obj->intro->typography);
        }
        if (isset($obj->intro->margin)) {
            $css .= $this->get('margin', $obj->intro->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-post-author-description {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-post-author-description:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-post-author-description");

        return $str;
    }

    public function getRecentCommentsRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->view->count)) {
            $str .= "#".$selector." .ba-masonry-layout {";
            $str .= "grid-template-columns: repeat(auto-fill, minmax(calc((100% / ".$obj->view->count.") - 21px),1fr));";
            $str .= "}";
            $str .= "#".$selector." .ba-grid-layout .ba-blog-post:nth-child(n) {";
            $str .= "width: calc((100% / ".$obj->view->count.") - 21px);";
            $str .= "margin-top: 30px;";
            $str .= "}";
            for ($i = 0; $i < $obj->view->count; $i++) {
                $str .= "#".$selector." .ba-grid-layout .ba-blog-post:nth-child(".($i + 1).") {";
                $str .= "margin-top: 0;";
                $str .= "}";
            }
        }
        $css = '';
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->background->color)) {
            $css .= "background-color:".$this->getCorrectColor($obj->background->color).';';
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post");
        $css = '';
        if (isset($obj->image->width)) {
            $css .= "width :".$this->getValueUnits($obj->image->width).";";
        }
        if (isset($obj->image->height)) {
            $css .= "height :".$this->getValueUnits($obj->image->height).";";
        }
        if (isset($obj->image->border)) {
            $this->cascade->border = $this->object->image->border;
            $css .= $this->get('border', $obj->image->border, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-image {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-image:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-image");
        $css = '';
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography);
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-title {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-title");
        $css = '';
        if (isset($obj->info->margin)) {
            $css .= $this->get('margin', $obj->info->margin, 'default');
        }
        if (isset($obj->info->typography->{'text-align'})) {
            $css .= "text-align :".$obj->info->typography->{'text-align'}.";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-info-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-info-wrapper");
        if (isset($obj->info->typography)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper > * {";
            $str .= $this->getTypographyRule($obj->info->typography, 'text-align');
            $str .= "}";
        }
        $css = '';
        if (isset($obj->stars->icon->{'text-align'})) {
            $justify = str_replace('left', 'flex-start', $obj->stars->icon->{'text-align'});
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "justify-content: ".$justify.";";
        }
        if (isset($obj->stars->margin)) {
            $css .= $this->get('margin', $obj->stars->margin, 'default');
        }
        if (isset($obj->stars->icon->size)) {
            $css .= "font-size: ".$this->getValueUnits($obj->stars->icon->size).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-review-stars-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-review-stars-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-review-stars-wrapper");
        $css = '';
        if (isset($obj->intro->typography)) {
            $css .= $this->getTypographyRule($obj->intro->typography);
        }
        if (isset($obj->intro->margin)) {
            $css .= $this->get('margin', $obj->intro->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-intro-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-intro-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-intro-wrapper");

        return $str;
    }

    public function getCategoriesRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->view->gutter)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post:nth-child(n) {";
            $str .= "margin-top: ".($obj->view->gutter ? 30 : 0)."px;";
            $str .= "margin-left: ".($obj->view->gutter ? "10px" : "0").";";
            $str .= "margin-right: ".($obj->view->gutter ? "10px" : "0").";";
            $str .= "}";
            $str .= "#".$selector." .ba-cover-layout {";
            $str .= "margin-left: ".($obj->view->gutter ? "-10px" : "0").";";
            $str .= "margin-right: ".($obj->view->gutter ? "-10px" : "0").";";
            $str .= "}";
        }
        if (isset($obj->view->image)) {
            $str .= "#".$selector." .ba-classic-layout .ba-blog-post:nth-child(n) {";
            $str .= "margin-top: ".($obj->view->image ? 30 : 0)."px;";
            $str .= "}";
        }
        if (isset($obj->view->count)) {
            $str .= "#".$selector." .ba-masonry-layout {";
            $str .= "grid-template-columns: repeat(auto-fill, minmax(calc((100% / ".$obj->view->count.") - 21px),1fr));";
            $str .= "}";
            $str .= "#".$selector." .ba-grid-layout .ba-blog-post, ";
            $str .= "#".$selector." .ba-classic-layout .ba-blog-post {";
            $str .= "width: calc((100% / ".$obj->view->count.") - 21px);";
            $str .= "}";
            $str .= "#".$selector." .ba-grid-layout .ba-blog-post:nth-child(n) {";
            $str .= "margin-top: 30px;";
            $str .= "}";
            for ($i = 0; $i < $obj->view->count; $i++) {
                $str .= "#".$selector." .ba-grid-layout .ba-blog-post:nth-child(".($i + 1)."), ";
                $str .= "#".$selector." .ba-classic-layout .ba-blog-post:nth-child(".($i + 1)."), ";
                $str .= "#".$selector." .ba-cover-layout .ba-blog-post:nth-child(".($i + 1).") {";
                $str .= "margin-top: 0;";
                $str .= "}";
            }
        }
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= "#".$selector." .ba-overlay {";
            $str .= $css;
            $str .= '}';
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post:hover .ba-overlay", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-overlay");
        if (isset($obj->view->gutter) || isset($obj->view->count)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post {";
            $str .= "width: calc((100% / ".$this->object->view->count.") - ".($this->object->view->gutter ? 21 : 0)."px);";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->background->color)) {
            $css .= "background-color:".$this->getCorrectColor($obj->background->color).';';
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post");
        $css = '';
        if (isset($obj->image->size)) {
            $css .= "background-size: ".$obj->image->size.";";
        }
        if (isset($obj->image->border)) {
            $this->cascade->border = $this->object->image->border;
            $css .= $this->get('border', $obj->image->border, 'default');
        }
        if (isset($obj->image->width)) {
            $css .= "width :".$this->getValueUnits($obj->image->width).";";
        }
        if (isset($obj->image->height)) {
            $css .= "height :".$this->getValueUnits($obj->image->height).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-image {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-image:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-image");
        if (isset($obj->image->height)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post {";
            $str .= "height :".$this->getValueUnits($obj->image->height).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->margin)) {
            $css .= $this->get('margin', $obj->title->margin, 'default');
        }
        if (isset($obj->title->typography->color)) {
            $css .= "color:".$this->getCorrectColor($obj->title->typography->color).';';
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-title {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->title->typography)) {
            $str .= "#".$selector." .ba-blog-post-title a {";
            $str .= $this->getTypographyRule($obj->title->typography);
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-title");
        if (isset($obj->info->margin)) {
            $str .= "#".$selector." .ba-app-sub-categories {";
            $str .= $this->get('margin', $obj->info->margin, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-app-sub-categories:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-app-sub-categories");
        if (isset($obj->info->typography)) {
            $str .= "#".$selector." .ba-app-sub-category a {";
            $str .= $this->getTypographyRule($obj->info->typography);
            $str .= "}";
        }
        if (isset($obj->info->typography->color)) {
            $str .= "#".$selector." .ba-app-sub-category i {";
            $str .= "color:".$this->getCorrectColor($obj->info->typography->color).';';
            $str .= "}";
        }
        $css = '';
        if (isset($obj->intro->typography)) {
            $css .= $this->getTypographyRule($obj->intro->typography);
        }
        if (isset($obj->intro->margin)) {
            $css .= $this->get('margin', $obj->intro->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-intro-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-intro-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-intro-wrapper");

        return $str;
    }

    public function getBlogPostsRules($obj, $selector)
    {
        $type = $this->item->type;
        $flag = $type == 'post-navigation';
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        if (isset($obj->view->gutter) || isset($obj->view->count)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post:nth-child(n) {";
            $str .= "margin-top: ".($this->object->view->gutter ? 30 : 0)."px;";
            $str .= "}";
        }
        if (isset($obj->view->gutter)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post:nth-child(n) {";
            $str .= "margin-left: ".($obj->view->gutter ? "10px" : "0").";";
            $str .= "margin-right: ".($obj->view->gutter ? "10px" : "0").";";
            $str .= "}";
            $str .= "#".$selector." .ba-cover-layout {";
            $str .= "margin-left: ".($obj->view->gutter ? "-10px" : "0").";";
            $str .= "margin-right: ".($obj->view->gutter ? "-10px" : "0").";";
            $str .= "}";
        }
        if (isset($obj->view->count)) {
            $str .= "#".$selector." .ba-masonry-layout {";
            $str .= "grid-template-columns: repeat(auto-fill, minmax(calc((100% / ".$obj->view->count.") - 21px),1fr));";
            $str .= "}";
            $str .= "#".$selector." .ba-grid-layout .ba-blog-post:nth-child(n) {";
            $str .= "width: calc((100% / ".$obj->view->count.") - 21px);";
            $str .= "margin-top: 30px;";
            $str .= "}";
            for ($i = 0; $i < $obj->view->count; $i++) {
                $str .= "#".$selector." .ba-grid-layout .ba-blog-post:nth-child(".($i + 1)."), ";
                $str .= "#".$selector." .ba-cover-layout .ba-blog-post:nth-child(".($i + 1).") {";
                $str .= "margin-top: 0;";
                $str .= "}";
            }
        }
        $css = $this->getOverlayRules($obj);
        if (!empty($css)) {
            $str .= "#".$selector." .ba-overlay {";
            $str .= $css;
            $str .= '}';
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post:hover .ba-overlay", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-overlay");
        if (isset($obj->view->gutter) || isset($obj->view->count)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post {";
            $str .= "width: calc((100% / ".$this->object->view->count.") - ".($this->object->view->gutter ? 21 : 0)."px);";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->background)) {
            $css .= $this->get('backgroundColor', $obj->background, 'default');
        }
        if (isset($obj->border)) {
            $this->cascade->border = $this->object->border;
            $css .= $this->get('border', $obj->border, 'default');
        }
        if (isset($obj->shadow)) {
            $css .= $this->get('shadow', $obj->shadow, 'default');
        }
        if (isset($obj->padding)) {
            $css .= $this->get('padding', $obj->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post");
        if (isset($obj->image->border)) {
            $this->cascade->border = $this->object->image->border;
            $str .= "#".$selector." .ba-blog-post-image {";
            $str .= $this->get('border', $obj->image->border, 'default');
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-blog-post-image:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-image");
        }
        $css = '';
        if (isset($obj->image->width)) {
            $css .= "width :".$this->getValueUnits($obj->image->width).";";
        }
        if (isset($obj->image->height)) {
            $css .= "height :".$this->getValueUnits($obj->image->height).";";
        }
        if (isset($obj->image->size)) {
            $css .= "background-size: ".$obj->image->size.";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-image {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->image->height)) {
            $str .= "#".$selector." .ba-cover-layout .ba-blog-post {";
            $str .= "height :".$this->getValueUnits($obj->image->height).";";
            $str .= "}";
        }
        if (isset($obj->title->margin)) {
            $str .= "#".$selector." .ba-blog-post-title {";
            $str .= $this->get('margin', $obj->title->margin, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-title:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-title");
        $price = isset($obj->title) ? $obj->title : null;
        $price = isset($this->object->price) && is_object($this->object->price) && isset($obj->price) ? $obj->price : $price;
        if (isset($price->margin)) {
            $str .= "#".$selector." .ba-blog-post-add-to-cart-wrapper {";
            $str .= $this->get('margin', $price->margin, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-add-to-cart-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-add-to-cart-wrapper");
        $css = '';
        if (isset($price->typography)) {
            $css .= $this->getTypographyRule($price->typography, 'text-align');
        }
        $justify = '';
        if (isset($price->typography->{'text-align'})) {
            $align = $price->typography->{'text-align'};
            $justify = str_replace('left', 'flex-start', $align);
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "align-items :".($flag && $align == 'left' ? 'flex-end' : ($flag && $align == 'right' ? 'flex-start' : $justify)).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-add-to-cart-price {";
            $str .= $css;
            $str .= "}";
        }
        if ($flag && !empty($justify)) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-blog-post-add-to-cart-price {";
            $str .= "align-items :".$justify.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->title->typography)) {
            $css .= $this->getTypographyRule($obj->title->typography, 'text-align');
        }
        if (isset($obj->title->typography->{'text-align'})) {
            $align = $obj->title->typography->{'text-align'};
            $align = $flag && $align == 'left' ? 'right' : ($flag && $align == 'right' ? 'left' : $align);
            $css .= "text-align :".$align.";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-title {";
            $str .= $css;
            $str .= "}";
        }
        if ($flag && isset($obj->title->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-blog-post-title {";
            $str .= "text-align :".$obj->title->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->reviews->typography)) {
            $css .= $this->getTypographyRule($obj->reviews->typography, 'text-align');
        }
        if (isset($obj->reviews->margin)) {
            $css .= $this->get('margin', $obj->reviews->margin, 'default');
        }
        $justify = '';
        if (isset($obj->reviews->typography->{'text-align'})) {
            $align = $obj->reviews->typography->{'text-align'};
            $justify = str_replace('left', 'flex-start', $align);
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "justify-content :".($flag && $align == 'left' ? 'flex-end' : ($flag && $align == 'right' ? 'flex-start' : $justify)).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-reviews {";
            $str .= $css;
            $str .= "}";
        }
        if ($flag && !empty($justify)) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-blog-post-reviews {";
            $str .= "justify-content :".$justify.";";
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-reviews:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-reviews");
        if (isset($obj->reviews->hover->color)) {
            $str .= "#".$selector." .ba-blog-post-reviews a:hover {";
            $str .= "color: ".$this->getCorrectColor($obj->reviews->hover->color).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->postFields->typography)) {
            $css .= $this->getTypographyRule($obj->postFields->typography, 'text-align');
        }
        if (isset($obj->postFields->margin)) {
            $css .= $this->get('margin', $obj->postFields->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-field-row-wrapper {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-blog-post-field-row-wrapper:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-field-row-wrapper");
        }
        $css = '';
        if (isset($obj->info->margin)) {
            $css .= $this->get('margin', $obj->info->margin, 'default', null, ' * var(--visible-info)');
        }
        $justify = '';
        if (isset($obj->info->typography->{'text-align'})) {
            $align = $obj->info->typography->{'text-align'};
            $justify = str_replace('left', 'flex-start', $align);
            $justify = str_replace('right', 'flex-end', $justify);
            $css .= "justify-content :".($flag && $align == 'left' ? 'flex-end' : ($flag && $align == 'right' ? 'flex-start' : $justify)).";";
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-info-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-info-wrapper");
        if (!empty($justify) && $flag) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-blog-post-info-wrapper {";
            $str .= "justify-content :".$justify.";";
            $str .= "}";
        }
        if (isset($obj->info->typography->{'text-align'})) {
            $align = $obj->info->typography->{'text-align'};
            $align = $flag && $align == 'left' ? 'right' : ($flag && $align == 'right' ? 'left' : $align);
            $str .= "#".$selector." .ba-post-navigation-info {";
            $str .= "text-align :".$align.";";
            $str .= "}";
        }
        if ($flag && isset($obj->info->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-post-navigation-info {";
            $str .= "text-align :".$obj->info->typography->{'text-align'}.";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->info->typography)) {
            $css .= $this->getTypographyRule($obj->info->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper > span *:not(.ba-author-avatar), #".$selector." .ba-post-navigation-info a {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->info->typography->color)) {
            $str .= "#".$selector." .ba-blog-post-info-wrapper > span {";
            $str .= "color: ".$this->getCorrectColor($obj->info->typography->color).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->intro->typography)) {
            $css .= $this->getTypographyRule($obj->intro->typography, 'text-align');
        }
        if (isset($obj->intro->margin)) {
            $css .= $this->get('margin', $obj->intro->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-intro-wrapper {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-intro-wrapper:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-intro-wrapper");
        if (isset($obj->intro->typography->{'text-align'})) {
            $align = $obj->intro->typography->{'text-align'};
            $align = $flag && $align == 'left' ? 'right' : ($flag && $align == 'right' ? 'left' : $align);
            $str .= "#".$selector." .ba-blog-post-intro-wrapper {";
            $str .= "text-align :".$align.";";
            $str .= "}";
        }
        if ($flag && isset($obj->intro->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-blog-post-intro-wrapper {";
            $str .= "text-align :".$obj->intro->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->button->typography->{'text-align'})) {
            $align = $obj->button->typography->{'text-align'};
            $align = $flag && $align == 'left' ? 'right' : ($flag && $align == 'right' ? 'left' : $align);
            $str .= "#".$selector." .ba-blog-post-button-wrapper {";
            $str .= "text-align :".$align.";";
            $str .= "}";
        }
        if ($flag && isset($obj->button->typography->{'text-align'})) {
            $str .= "#".$selector." .ba-blog-post:first-child .ba-blog-post-button-wrapper {";
            $str .= "text-align :".$obj->button->typography->{'text-align'}.";";
            $str .= "}";
        }
        if (isset($obj->button->margin)) {
            $str .= "#".$selector." .ba-blog-post-button-wrapper a {";
            $str .= $this->get('margin', $obj->button->margin, 'default');
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-blog-post-button-wrapper a:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-blog-post-button-wrapper a");
        $css = '';
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default');
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-post-button-wrapper a, #".$selector." .ba-blog-post-add-to-cart {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-blog-post-button-wrapper a:hover, #".$selector." .ba-blog-post-add-to-cart:hover";
        $str .= $this->getStateRule($query, 'hover');
        $query = "#".$selector." .ba-blog-post-button-wrapper a, #".$selector." .ba-blog-post-add-to-cart";
        $str .= $this->getTransitionRule($query);
        if ($type != 'recent-posts' && isset($obj->pagination->color)) {
            $str .= "#".$selector." .ba-blog-posts-pagination span a {";
            $str .= "color: ".$this->getCorrectColor($obj->pagination->color).";";
            $str .= "}";
        }
        if ($type != 'recent-posts' && isset($obj->pagination->hover)) {
            $str .= "#".$selector." .ba-blog-posts-pagination span.active a, ";
            $str .= "#".$selector." .ba-blog-posts-pagination span:hover a {";
            $str .= "color: ".$this->getCorrectColor($obj->pagination->hover).";";
            $str .= "}";
        }
        $css = '';
        if (isset($obj->pagination->typography->{'text-align'})) {
            $css .= "text-align :".$obj->pagination->typography->{'text-align'}.";";
        }
        if (isset($obj->pagination->margin)) {
            $css .= $this->get('margin', $obj->pagination->margin, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-blog-posts-pagination {";
            $str .= $css;
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-blog-posts-pagination:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-blog-posts-pagination");
        }
        $css = '';
        if (isset($obj->pagination->shadow)) {
            $css .= $this->get('shadow', $obj->pagination->shadow, 'default');
        }
        if (isset($obj->pagination->typography)) {
            $css .= $this->getTypographyRule($obj->pagination->typography, 'text-align');
        }
        if (isset($obj->pagination->border)) {
            $this->cascade->border = $this->object->pagination->border;
            $css .= $this->get('border', $obj->pagination->border, 'default');
        }
        if (isset($obj->pagination->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->pagination->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->pagination, 'default');
        }
        if (isset($obj->pagination->padding)) {
            $css .= $this->get('padding', $obj->pagination->padding, 'default');
        }
        if (!empty($css)) {
            $query = "#".$selector." .ba-blog-posts-pagination a";
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
            $query = "#".$selector." .ba-blog-posts-pagination a:hover, #".$selector." .ba-blog-posts-pagination span.active a";
            $str .= $this->getStateRule($query, 'hover');
            $str .= $this->getTransitionRule($query);
        }

        return $str;
    }

    public function getAddToCartRules($obj, $selector)
    {
        $str = $css = '';
        if (isset($obj->disable)) {
            $css .= $this->setItemsVisability($obj->disable, "block");
        }
        if (isset($obj->margin)) {
            $css .= $this->get('margin', $obj->margin, 'default');
        }
        if (!empty($css)) {
            $str = "#".$selector." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->price->margin)) {
            $css .= $this->get('margin', $obj->price->margin, 'default');
        }
        if (isset($obj->price->typography)) {
            $css .= $this->getTypographyRule($obj->price->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-add-to-cart-price {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector.":hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector);
        $css = '';
        if (isset($obj->info->margin)) {
            $css .= $this->get('margin', $obj->info->margin, 'default');
        }
        if (isset($obj->info->typography)) {
            $css .= $this->getTypographyRule($obj->info->typography, 'text-align');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-add-to-cart-info {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule("#".$selector." .ba-add-to-cart-info:hover", 'hover');
        $str .= $this->getTransitionRule("#".$selector." .ba-add-to-cart-info");
        if (isset($obj->info->typography)) {
            $str .= "#".$selector." .ba-add-to-cart-variations, #".$selector." .ba-add-to-cart-extra-options, ";
            $str .= "#".$selector." .ba-add-to-cart-upload-file, #".$selector." .add-to-cart-booking-hours-wrapper, ";
            $str .= "#".$selector." .add-to-cart-booking-calendar-wrapper, #".$selector." .add-to-cart-booking-guests-wrapper {";
            $str .= $this->getTypographyRule($obj->info->typography, 'text-align');
            $str .= "}";
        }
        $css = '';
        if (isset($obj->button->typography->{'font-family'})) {
            $family = str_replace('+', ' ', $obj->button->typography->{'font-family'});
            $family = $family == '@default' ? '' : $family;
            $css .= !empty($family) ? "font-family: '".$family."';" : '';
        }
        if (isset($obj->button->typography->{'font-size'})) {
            $css .= 'font-size: '.$this->getValueUnits($obj->button->typography->{'font-size'}).';';
        }
        if (isset($obj->button->typography->{'letter-spacing'})) {
            $css .= 'letter-spacing: '.$this->getValueUnits($obj->button->typography->{'letter-spacing'}).';';
        }
        if (isset($obj->price->typography->color)) {
            $css .= 'color: '.$this->getCorrectColor($obj->price->typography->color).';';
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-add-to-cart-quantity {";
            $str .= $css;
            $str .= "}";
        }
        if (isset($obj->button->margin)) {
            $str .= "#".$selector." .ba-add-to-cart-button-wrapper {";
            $str .= $this->get('margin', $obj->button->margin, 'default');
            $str .= "}";
            $str .= $this->getStateRule("#".$selector." .ba-add-to-cart-button-wrapper:hover", 'hover');
            $str .= $this->getTransitionRule("#".$selector." .ba-add-to-cart-button-wrapper");
        }
        $query = "#".$selector." .ba-add-to-cart-buttons-wrapper";
        $css = '';
        if (isset($obj->button->colors->default->{'background-color'})) {
            $css .= "background-color: ".$this->getCorrectColor($obj->button->colors->default->{'background-color'}).";";
        }
        if (isset($obj->button->border)) {
            $this->cascade->border = $this->object->button->border;
            $css .= $this->get('border', $obj->button->border, 'default', '--');
        }
        if (isset($obj->view->wishlist)) {
            $css .= "--display-wishlist: ".($obj->view->wishlist ? 0 : 1).";";
        }
        if (isset($obj->button->shadow)) {
            $css .= $this->get('shadow', $obj->button->shadow, 'default');
        }
        if (isset($obj->button->padding)) {
            $css .= $this->get('padding', $obj->button->padding, 'default', '--');
        }
        if (!empty($css)) {
            $str .= $query." {";
            $str .= $css;
            $str .= "}";
        }
        $str .= $this->getStateRule($query.":hover", 'hover');
        $str .= $this->getTransitionRule($query);
        $css = '';
        if (isset($obj->button->typography)) {
            $css .= $this->getTypographyRule($obj->button->typography, 'text-align');
        }
        if (isset($obj->button->colors)) {
            $this->cascade->{'colors-bg'} = $this->object->button->{'colors-bg'} ?? null;
            $css .= $this->getColors('colors', $obj->button, 'default');
        }
        if (!empty($css)) {
            $str .= "#".$selector." .ba-add-to-cart-button-wrapper a, #".$selector." .ba-add-to-wishlist {";
            $str .= $css;
            $str .= "}";
        }
        $query = "#".$selector." .ba-add-to-cart-button-wrapper a:hover, #".$selector." .ba-add-to-wishlist:hover";
        $str .= $this->getStateRule($query, 'hover');
        if (isset($obj->button->border->transition)) {
            $this->updateTransitions($obj->button->border, 'border-radius');
        }
        if (isset($obj->button->padding->transition)) {
            $this->updateTransitions($obj->button->padding, 'padding');
        }
        $query = "#".$selector." .ba-add-to-cart-button-wrapper a, #".$selector." .ba-add-to-wishlist";
        $str .= $this->getTransitionRule($query);

        return $str;
    }

    public function getCorrectColor($key)
    {
        return strpos($key, '@') === false ? $key : 'var('.str_replace('@', '--', $key).')';
    }

    public function setBackgroundImage($image)
    {
        if (!gridboxHelper::isExternal($image)) {
            $website = gridboxHelper::$website;
            $url = gridboxHelper::$up.$image;
            if (($website->compress_images == 1 && (empty($this->breakpoint) || $this->breakpoint == 'desktop'))
                || ($website->adaptive_images == 1 && !empty($this->breakpoint) && $this->breakpoint != 'desktop')) {
                gridboxHelper::$breakpoint = $this->breakpoint;
                $src = gridboxHelper::getCompressedImageURL($image, true);
                if ($src) {
                    $url = $src;
                }
            }
        } else {
            $url = $image;
        }

        return str_replace(' ', '%20', $url);
    }

    public function setFlipboxSide($obj, $side)
    {
        $array = ['background', 'background-states', 'overlay', 'overlay-states', 'image', 'video'];
        $object = $obj->sides->{$side};
        if (!isset($object->desktop->{'overlay-states'})) {
            $states = new stdClass();
            $states->default = (object)[
                'type' => $object->desktop->overlay->type,
                'color' => $object->desktop->overlay->color
            ];
            $states->transition = $this->transition;
            $states->state = false;
            $object->desktop->{'overlay-states'} = $states;
        }
        if (!isset($object->desktop->{'background-states'})) {
            $states = new stdClass();
            $states->default = (object)[
                'image' => $object->desktop->image->image,
                'color' => $object->desktop->background->color
            ];
            $states->transition = $this->transition;
            $states->state = false;
            $object->desktop->{'background-states'} = $states;
        }
        $obj->parallax = $object->parallax;
        for ($i = 0; $i < count($array); $i++) {
            $obj->desktop->{$array[$i]} = $object->desktop->{$array[$i]};
        }
        foreach (gridboxHelper::$breakpoints as $ind => $value) {
            if (isset($obj->{$ind})) {
                for ($i = 0; $i < count($array); $i++) {
                    if (isset($object->{$ind}->{$array[$i]})) {
                        $obj->{$ind}->{$array[$i]} = $object->{$ind}->{$array[$i]};
                    } else if (isset($obj->{$ind}->{$array[$i]})) {
                        unset($obj->{$ind}->{$array[$i]});
                    }
                }
            }
        }
    }
}