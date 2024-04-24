<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

class gridboxHTMLHelper
{
    private $dir;
    private $keys;
    private $passive;

    public function __construct()
    {
        $this->dir = JPATH_ROOT.'/components/com_gridbox/views/layout/patterns/editor/';
        $this->keys = ['margin' => ['top', 'right', 'bottom', 'left'], 'padding' => ['top', 'right', 'bottom', 'left']];
        $this->passive = [];
    }

    public function loadPassive($name, $options = null)
    {
        if (isset($this->passive[$name])) {
            $out = $this->passive[$name];
        } else {
            $out = $this->load($name, $options);
            $this->passive[$name] = $out;
        }

        return $out;
    }

    public function load($name, $options = null)
    {
        include $this->dir.$name.'.php';

        return $out;
    }
}