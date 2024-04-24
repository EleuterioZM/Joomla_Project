<?php
/**
* @package   Gridbox template
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

function modChrome_Gridboxhtml($module, &$params, &$attribs)
{
    $moduleTag = $params->get('module_tag', 'div');
    $bootstrapSize = (int) $params->get('bootstrap_size', 0);
    $moduleClass = $bootstrapSize !== 0 ? ' span' . $bootstrapSize : '';
    $headerTag = htmlspecialchars($params->get('header_tag', 'h3'), ENT_COMPAT, 'UTF-8');
    $headerClass = htmlspecialchars($params->get('header_class', 'page-header'), ENT_COMPAT, 'UTF-8');
    if ($module->content) {
        $html = '<'.$moduleTag.' class="well ba-module-position '.htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
        $html .= $moduleClass.'">';
        if ($module->showtitle) {
            $html .= '<'.$headerTag.' class="'.$headerClass.'">'.$module->title.'</'.$headerTag.'>';
        }
        $html .= $module->content.'</'.$moduleTag.'>';
        echo $html;
    }
}