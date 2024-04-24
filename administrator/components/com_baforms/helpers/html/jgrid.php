<?php
/**
* @package   GRIDBOX
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;
use Joomla\Utilities\ArrayHelper;

abstract class baformshtmlJGrid
{
    public static function action($i, $task, $prefix = '', $text = '', $active_title = '', $inactive_title = '',
        $tip = false, $active_class = '', $inactive_class = '', $enabled = true, $translate = true, $checkbox = 'cb')
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $active_title = array_key_exists('active_title', $options) ? $options['active_title'] : $active_title;
            $inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactive_title;
            $tip = array_key_exists('tip', $options) ? $options['tip'] : $tip;
            $active_class = array_key_exists('active_class', $options) ? $options['active_class'] : $active_class;
            $inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactive_class;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }
        if ($tip) {
            $title = $enabled ? $active_title : $inactive_title;
            $title = $translate ? JText::_($title) : $title;
            $title = JHtml::tooltipText($title, '', 0);
        }
        if ($enabled) {
            $html[] = '<a class="' . ($active_class == 'publish' ? ' active' : '') . '"';
            $onclick = 'return Joomla.listItemTask(\''.$checkbox.$i.'\',\''.$prefix.$task.'\');';
            $html[] = ' href="javascript:void(0);" onclick="'.$onclick.'"';
            $html[] = '>';
            if ($active_class == 'publish') {
                $html[] = '<i class="zmdi zmdi-eye ba-icon-md"></i>';
            } else {
                $html[] = '<i class="zmdi zmdi-eye-off ba-icon-md"></i>';
            }
            $html[] = '<span class="ba-tooltip ba-hide-element ba-top">'.$title.'</span>';
            $html[] = '</a>';
        } else {
            $html[] = '<a class="disabled jgrid' . ($tip ? ' hasTooltip' : '') . '"';
            $html[] = $tip ? ' title="' . $title . '"' : '';
            $html[] = '>';
            $html[] = '<i class="zmdi zmdi-eye-off ba-icon-md"></i>';
            $html[] = '</a>';
        }

        return implode($html);
    }

    public static function state($states, $value, $i, $prefix = '', $enabled = true, $translate = true, $checkbox = 'cb')
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }
        if (class_exists('JArrayHelper')) {
            $state = JArrayHelper::getValue($states, (int) $value, $states[0]);
        } else {
            $state = ArrayHelper::getValue($states, (int) $value, $states[1]);
        }
        $task = array_key_exists('task', $state) ? $state['task'] : $state[0];
        $text = array_key_exists('text', $state) ? $state['text'] : (array_key_exists(1, $state) ? $state[1] : '');
        $active_title = array_key_exists('active_title', $state) ? $state['active_title'] : (array_key_exists(2, $state) ? $state[2] : '');
        $inactive_title = array_key_exists('inactive_title', $state) ? $state['inactive_title'] : (array_key_exists(3, $state) ? $state[3] : '');
        $tip = array_key_exists('tip', $state) ? $state['tip'] : (array_key_exists(4, $state) ? $state[4] : false);
        $active_class = array_key_exists('active_class', $state) ? $state['active_class'] : (array_key_exists(5, $state) ? $state[5] : '');
        $inactive_class = array_key_exists('inactive_class', $state) ? $state['inactive_class'] : (array_key_exists(6, $state) ? $state[6] : '');

        return static::action(
            $i, $task, $prefix, $text, $active_title, $inactive_title, $tip,
            $active_class, $inactive_class, $enabled, $translate, $checkbox
        );
    }

    public static function published($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb', $publish_up = null, $publish_down = null)
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }
        $states = array(1 => array('unpublish', 'JPUBLISHED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JPUBLISHED', true, 'publish', 'publish'),
            0 => array('publish', 'JUNPUBLISHED', 'JLIB_HTML_PUBLISH_ITEM', 'JUNPUBLISHED', true, 'unpublish', 'unpublish'),
            2 => array('unpublish', 'JARCHIVED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JARCHIVED', true, 'archive', 'archive'),
            -2 => array('publish', 'JTRASHED', 'JLIB_HTML_PUBLISH_ITEM', 'JTRASHED', true, 'trash', 'trash'));
        if ($publish_up || $publish_down) {
            $nullDate = JFactory::getDbo()->getNullDate();
            $nowDate = JFactory::getDate()->toUnix();
            $tz = new DateTimeZone(JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset')));
            $publish_up = ($publish_up != $nullDate) ? JFactory::getDate($publish_up, 'UTC')->setTimeZone($tz) : false;
            $publish_down = ($publish_down != $nullDate) ? JFactory::getDate($publish_down, 'UTC')->setTimeZone($tz) : false;
            $tips = array();
            if ($publish_up) {
                $tips[] = JText::sprintf('JLIB_HTML_PUBLISHED_START', $publish_up->format(JDate::$format, true));
            }
            if ($publish_down) {
                $tips[] = JText::sprintf('JLIB_HTML_PUBLISHED_FINISHED', $publish_down->format(JDate::$format, true));
            }
            $tip = empty($tips) ? false : implode('<br />', $tips);
            foreach ($states as $key => $state) {
                if ($key == 1) {
                    $states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_ITEM';
                    if ($publish_up > $nullDate && $nowDate < $publish_up->toUnix()) {
                        $states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
                        $states[$key][5] = $states[$key][6] = 'pending';
                    }
                    if ($publish_down > $nullDate && $nowDate > $publish_down->toUnix()) {
                        $states[$key][2] = $states[$key][3] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
                        $states[$key][5] = $states[$key][6] = 'expired';
                    }
                }
                if ($tip) {
                    $states[$key][1] = JText::_($states[$key][1]);
                    $states[$key][2] = JText::_($states[$key][2]) . '<br />' . $tip;
                    $states[$key][3] = JText::_($states[$key][3]) . '<br />' . $tip;
                    $states[$key][4] = true;
                }
            }

            return static::state($states, $value, $i, array('prefix' => $prefix, 'translate' => !$tip), $enabled, true, $checkbox);
        }

        return static::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
    }

    public static function isdefault($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }
        $states = array(
            0 => array('setDefault', '', 'JLIB_HTML_SETDEFAULT_ITEM', '', 1, 'unfeatured', 'unfeatured'),
            1 => array('unsetDefault', 'JDEFAULT', 'JLIB_HTML_UNSETDEFAULT_ITEM', 'JDEFAULT', 1, 'featured', 'featured'),
        );

        return static::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
    }

    public static function publishedOptions($config = array())
    {
        $options = array();
        if (!array_key_exists('published', $config) || $config['published']) {
            $options[] = JHtml::_('select.option', '1', 'JPUBLISHED');
        }
        if (!array_key_exists('unpublished', $config) || $config['unpublished']) {
            $options[] = JHtml::_('select.option', '0', 'JUNPUBLISHED');
        }
        if (!array_key_exists('archived', $config) || $config['archived']) {
            $options[] = JHtml::_('select.option', '2', 'JARCHIVED');
        }
        if (!array_key_exists('trash', $config) || $config['trash']) {
            $options[] = JHtml::_('select.option', '-2', 'JTRASHED');
        }
        if (!array_key_exists('all', $config) || $config['all']) {
            $options[] = JHtml::_('select.option', '*', 'JALL');
        }

        return $options;
    }

    public static function checkedout($i, $editorName, $time, $prefix = '', $enabled = false, $checkbox = 'cb')
    {
        JHtml::_('bootstrap.tooltip');

        if (is_array($prefix)) {
            $options = $prefix;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }
        $text = $editorName . '<br />' . JHtml::_('date', $time, JText::_('DATE_FORMAT_LC')) . '<br />' . JHtml::_('date', $time, 'H:i');
        $active_title = JHtml::tooltipText(JText::_('JLIB_HTML_CHECKIN'), $text, 0);
        $inactive_title = JHtml::tooltipText(JText::_('JLIB_HTML_CHECKED_OUT'), $text, 0);

        return static::action(
            $i, 'checkin', $prefix, JText::_('JLIB_HTML_CHECKED_OUT'), html_entity_decode($active_title, ENT_QUOTES, 'UTF-8'),
            html_entity_decode($inactive_title, ENT_QUOTES, 'UTF-8'), true, 'checkedout', 'checkedout', $enabled, false, $checkbox
        );
    }

    public static function orderUp($i, $task = 'orderup', $prefix = '', $text = 'JLIB_HTML_MOVE_UP', $enabled = true, $checkbox = 'cb')
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $text = array_key_exists('text', $options) ? $options['text'] : $text;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        return static::action($i, $task, $prefix, $text, $text, $text, false, 'uparrow', 'uparrow_disabled', $enabled, true, $checkbox);
    }

    public static function orderDown($i, $task = 'orderdown', $prefix = '', $text = 'JLIB_HTML_MOVE_DOWN', $enabled = true, $checkbox = 'cb')
    {
        if (is_array($prefix)) {
            $options = $prefix;
            $text = array_key_exists('text', $options) ? $options['text'] : $text;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        return static::action($i, $task, $prefix, $text, $text, $text, false, 'downarrow', 'downarrow_disabled', $enabled, true, $checkbox);
    }
}
