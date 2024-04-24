<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$input = JFactory::getApplication()->input;
$search = $input->cookie->get('pages_search', '', 'string');
$ordering = $input->cookie->get('pages_ordering', 'id', 'string');
$direction = $input->cookie->get('pages_direction', 'desc', 'string');
$status = $input->cookie->get('pages_status', '', 'string');
$limit = $input->cookie->get('pages_limit', 20, 'int');
$start = $input->cookie->get('pages_start', 0, 'int');
$type = $input->get('type', '', 'string');
if ($status == '') {
    $statusTitle = JText::_('STATUS');
} else if ($status == '0') {
    $statusTitle = JText::_('UNPUBLISHED');
} else {
    $statusTitle = JText::_('PUBLISHED');
}
$directTitle = array('desc' => JText::_('DESCENDING'), 'asc' => JText::_('ASCENDING'));
$orderTitle = array('published' => JText::_('STATUS'), 'title' => JText::_('TITLE'),
    'theme' => JText::_('THEME'), 'id' => 'ID');
$pagesLimit = array(0 => JText::_('JALL'), 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 50 => 50, 100 => 100);
?>
<script>
    jQuery(window).on('keydown', function(event){
        window.parent.$g(window.parent).trigger(event);
    });
</script>
<link rel="stylesheet" href="components/com_gridbox/assets/css/ba-style-editor.css" type="text/css"/>
<div id="ba-media-manager">
    <form  target="form-target" action=""
        method="post" autocomplete="off" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <div class ="row-fluid">
            <div class="row-fluid ba-media-header">
                <div class="span12">
                    <span class="ba-dialog-title"><?php echo JText::_('PAGES'); ?></span>
                    <i class="zmdi zmdi-fullscreen media-fullscrean"></i>
                    <i class="close-media zmdi zmdi-close"></i>
                </div>
                <div class="span12">
                    <div id="filter-bar">
                        <input type="text" data-pages="search" value="<?php echo $search; ?>"
                            placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
                        <i class="zmdi zmdi-search"></i>
                        <div class="pagination-limit">
                            <div class="ba-custom-select">
                                <input readonly onfocus="this.blur()" value="<?php echo $pagesLimit[$limit]; ?>" type="text">
                                <input type="hidden" data-pages="limit" value="<?php echo $limit; ?>">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="5">5</li>
                                    <li data-value="10">10</li>
                                    <li data-value="15">15</li>
                                    <li data-value="20">20</li>
                                    <li data-value="25">25</li>
                                    <li data-value="30">30</li>
                                    <li data-value="50">50</li>
                                    <li data-value="100">100</li>
                                    <li data-value="0"><?php echo JText::_('JALL'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="sorting-direction">
                            <div class="ba-custom-select">
                                <input readonly onfocus="this.blur()"value="<?php echo $directTitle[$direction]; ?>" type="text">
                                <input type="hidden" data-pages="direction" value="<?php echo $direction; ?>">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="asc">
                                        <?php echo JText::_('ASCENDING')?>
                                    </li>
                                    <li data-value="desc">
                                        <?php echo JText::_('DESCENDING')?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="sorting-table">
                            <div class="ba-custom-select">
                                <input readonly onfocus="this.blur()" value="<?php echo $orderTitle[$ordering]; ?>"
                                    size="<?php echo strlen($orderTitle[$ordering]); ?>" type="text">
                                <input type="hidden" data-pages="ordering" value="<?php echo $ordering; ?>">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="published"><?php echo JText::_('STATUS'); ?></li>
                                    <li data-value="title"><?php echo JText::_('TITLE'); ?></li>
                                    <li data-value="theme"><?php echo JText::_('THEME'); ?></li>
                                    <li data-value="id">ID</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-state">
                            <div class="ba-custom-select">
                                <input readonly onfocus="this.blur()" value="<?php echo $statusTitle; ?>"
                                    size="<?php echo strlen($statusTitle); ?>" type="text">
                                <input type="hidden" data-pages="status" value="<?php echo $status; ?>">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('STATUS'); ?></li>
                                    <li data-value="1"><?php echo JText::_('PUBLISHED'); ?></li>
                                    <li data-value="0"><?php echo JText::_('UNPUBLISHED'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid ba-media-manager">
                <div class="ba-folder-tree" style="width: 30%;">
                    <ul>
                        <li class="active">
                            <a href="index.php?option=com_gridbox&view=pages&tmpl=component" data-app="0">
                                <i class="zmdi zmdi-folder"></i>
                                <?php echo JText::_('PAGES'); ?>
                            </a>
                        </li>
                        <?php
                    foreach ($this->apps as $key => $app) { ?>
                        <li>
                            <a href="index.php?option=com_gridbox&view=pages&tmpl=component&app=<?php echo $app->id; ?>">
                                <i class="zmdi zmdi-folder"></i>
                                <?php echo $app->title; ?>
                            </a>
<?php
                        if (count($app->categories) > 0) {
?>
                            <i class="zmdi zmdi-chevron-right"></i>
<?php
                            echo $this->drawCategoryList($app->categories, $app->id);
                        }
?>
                        </li>
<?php
                    }
?>
                        <li>
                            <a href="index.php?option=com_gridbox&view=pages&tmpl=component&type=system" data-app="0">
                                <i class="zmdi zmdi-folder"></i>
                                <?php echo JText::_('SYSTEM_PAGES'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="ba-work-area" style="width: 70%;">
                    <div class="table-header">
                        <div>
                            <?php echo JText::_('STATUS'); ?>
                        </div>
                        <div>
                            <?php echo JText::_('TITLE'); ?>
                        </div>
                        <div>
                            <?php echo JText::_('THEME'); ?>
                        </div>
                        <div>
                            ID
                        </div>
                    </div>
                    <div id="workspace-wrapper">
                        <div>
                            <div id="ba-items-list-wrapper">
                                <table class="ba-items-list">
                                    <tbody>
<?php
                                    foreach ($this->pages as $page) {
                                        $link = 'index.php?option=com_gridbox&view=editor&tmpl=component';
                                        if ($type == 'system') {
                                            $link = 'index.php?option=com_gridbox&view=editor&edit_type=system&tmpl=component';
                                        }
                                        $link .= '&id='.$page->id;
?>
                                        <tr>
                                            <td class="status-td">
                                                <i class="zmdi zmdi-eye<?php echo $page->published == 1 ? '' : '-off'; ?> ba-icon-md"></i>
                                            </td>
                                            <td class="title-td">
                                                <a href="<?php echo $link; ?>"
                                                    target="_blank">
                                                    <?php echo $page->title; ?>
                                                </a>
                                            </td>
                                            <td class="theme-td">
                                                <?php echo $page->theme; ?>
                                            </td>
                                            <td class="id-td">
                                                <?php echo $page->id; ?>
                                            </td>
                                        </tr>
<?php
                                    }
?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            if ($this->count > 0) {
                                $prev = $start - 1;
                            ?>
                            <div class="pagination">
                                <ul class="pagination-list">
                                    <li class="<?php echo ($start == 0) ? 'disabled' : ''; ?>">
                                        <a href="#" data-page="0">
                                            <span class="zmdi zmdi-skip-previous"></span>
                                        </a>
                                    </li>
                                    <li class="<?php echo ($start == 0) ? 'disabled' : ''; ?>">
                                        <a href="#" data-page="<?php echo $prev; ?>">
                                            <span class="zmdi zmdi-fast-rewind"></span>
                                        </a>
                                    </li>
                                    <?php
                                    $min = $start;
                                    $max = $this->count;
                                    if ($start > 2 && $this->count > 4) {
                                        $min = $start - 2;
                                    }
                                    if ($start > 0 && $start < 3) {
                                        $min = 0;
                                    }
                                    if ($this->count > 4 && ($this->count - $start) < 3) {
                                        $min = $this->count - 4;
                                    }
                                    if ($this->count > $start + 2) {
                                        $max = $start + 2;
                                        if ($this->count > 4 && $start < 2) {
                                            $max = 4;
                                        }
                                    }
                                    for ($i = $min; $i <= $max; $i++) { ?>
                                    <li class="<?php echo ($start == $i) ? 'active' : ''; ?>">
                                    <?php 
                                    $numb = $i + 1;
                                    ?>
                                        <a href="#" data-page="<?php echo $i; ?>"><?php echo $numb; ?></a>
                                    </li>
                                    <?php
                                    }
                                    $next = $start + 1;
                                    ?>
                                    <li class="<?php echo ($start == $this->count) ? 'disabled' : ''; ?>">
                                        <a href="#" data-page="<?php echo $next; ?>">
                                            <span class="zmdi zmdi-fast-forward"></span>
                                        </a>
                                    </li>
                                    <li class="<?php echo ($start == $this->count) ? 'disabled' : ''; ?>">
                                        <a href="#" data-page="<?php echo $this->count; ?>">
                                            <span class="zmdi zmdi-skip-next"></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </form>
</div>