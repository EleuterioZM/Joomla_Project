<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
if ($this->pages > 1) {
?>
<ul class="pagination-list">
    <li class="<?php echo $this->page == 0 ? 'disabled' : ''; ?>">
        <a href="#" data-page="0">
            <span class="zmdi zmdi-skip-previous"></span>
        </a>
    </li>
    <li class="<?php echo $this->page == 0 ? 'disabled' : ''; ?>">
        <a href="#" data-page="<?php echo $this->page - 1; ?>">
            <span class="zmdi zmdi-fast-rewind"></span>
        </a>
    </li>
<?php
    $start = 0;
    $max = $this->pages;
    if ($this->page > 2 && $this->pages > 4) {
        $start = $this->page - 2;
    }
    if ($this->pages > 4 && ($this->pages - $this->page) < 3) {
        $start = $this->pages - 5;
    }
    if ($this->pages > $this->page + 2) {
        $max = $this->page + 3;
        if ($this->pages > 4 && $this->page < 2) {
            $max = 5;
        }
    }
    for ($i = $start; $i < $max; $i++) {
?>
    <li class="<?php echo ($this->page == $i) ? 'active' : ''; ?>">
<?php 
        $number = $i + 1;
?>
        <a href="#" data-page="<?php echo $i; ?>"><?php echo $number; ?></a>
    </li>
<?php
    }
    $next = $this->page + 1;
    $end = $this->pages - 1
?>
    <li class="<?php echo ($this->page == $end) ? 'disabled' : ''; ?>">
        <a href="#" data-page="<?php echo $next ?>">
            <span class="zmdi zmdi-fast-forward"></span>
        </a>
    </li>
    <li class="<?php echo ($this->page == $end) ? 'disabled' : ''; ?>">
        <a href="#" data-page="<?php echo $end ?>">
            <span class="zmdi zmdi-skip-next"></span>
        </a>
    </li>
</ul>   
<?php
}
$out = ob_get_contents();
ob_end_clean();