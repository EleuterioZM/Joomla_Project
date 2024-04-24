<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

if (!function_exists('dd')) {
    function dd(...$data):void
    {
        echo "<pre>";
        foreach ($data as $variable) {
            print_r($variable);
        }
        echo "</pre>";
        exit();
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str, $encoding = 'utf-8')
    {
        return strtolower($str);
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen($str)
    {
        return strlen($str);
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $length = 0, $encoding = 'utf-8')
    {
        return substr($str, $start, $length);
    }
}

if (!function_exists('mb_substr')) {
    function mb_strpos($haystack, $needle, $offset = 0, $encoding = 'utf-8')
    {
        return strpos($haystack, $needle, $offset);
    }
}

if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($string, $encoding = 'UTF-8')
    {
        return strtoupper($string);
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string, $encoding = 'UTF-8')
    {
        return strtolower($string);
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string, $encoding = 'UTF-8')
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);
        
        return mb_strtoupper($firstChar, $encoding).mb_strtolower($then, $encoding);
    }
}