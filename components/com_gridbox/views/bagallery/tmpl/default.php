<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$input = JFactory::getApplication()->input;
$search = $input->cookie->get('bagallery_search', '', 'string');
$ordering = $input->cookie->get('bagallery_ordering', 'id', 'string');
$direction = $input->cookie->get('bagallery_direction', 'desc', 'string');
$directTitle = array('desc' => JText::_('DESCENDING'), 'asc' => JText::_('ASCENDING'));
$orderTitle = array('title' => JText::_('TITLE'), 'id' => 'ID');
?>
<script>
    jQuery(window).on('keydown', function(event){
        window.parent.$g(window.parent).trigger(event);
    });
</script>
<link rel="stylesheet" href="components/com_gridbox/assets/css/ba-style-editor.css" type="text/css"/>
<div id="ba-media-manager" class="bagallery ba-integration-plugin" data-type="bagallery">
    <form  target="form-target" action=""
        method="post" autocomplete="off" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
        <div class ="row-fluid">
            <div class="row-fluid ba-media-header">
                <div class="span12">
                    <span class="ba-dialog-title"><?php echo JText::_('BALBOOA_GALLERY'); ?></span>
                    <i class="zmdi zmdi-fullscreen media-fullscrean"></i>
                    <i class="close-media zmdi zmdi-close"></i>
                </div>
                <div class="span12">
                    <div id="filter-bar">
                        <input type="text" data-pages="search" value="<?php echo $search; ?>"
                            placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>">
                        <i class="zmdi zmdi-search"></i>
                        <div class="sorting-direction">
                            <div class="ba-custom-select">
                                <input readonly onfocus="this.blur()" value="<?php echo $directTitle[$direction]; ?>" type="text">
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
                                <input readonly onfocus="this.blur()" value="<?php echo $orderTitle[$ordering]; ?>" type="text">
                                <input type="hidden" data-pages="ordering" value="<?php echo $ordering; ?>">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="title"><?php echo JText::_('TITLE'); ?></li>
                                    <li data-value="id">ID</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fonts-table">
                <div class="ba-group-wrapper">
                    <p class="ba-group-title">
                        <span class="title"><?php echo JText::_('TITLE'); ?></span>
                        <span class="category"><?php echo JText::_('CATEGORY'); ?></span>
                        <span class="id">ID</span>
                    </p>
<?php
                foreach ($this->items as $item) {
?>
                    <div class="ba-options-group">
                        <div class="ba-group-element">
                            <label class="element-title">
                                <span data-id="<?php echo $item->id; ?>">
                                    <?php echo $item->title; ?>
                                </span>
                            </label>
                            <label class="element-category">
                                <span data-category="">
                                    <?php echo JText::_('JALL'); ?>
                                </span>
                            </label>
                            <label class="element-id">
                                <?php echo $item->id; ?>
                            </label>
                        </div>
                    </div>
<?php
                }
?>
                </div>
            </div>
        </div>
    </form>
</div>
<input class="constant-all" type="hidden" value="<?php echo JText::_('JALL'); ?>">
<div id="category-dialog" class="ba-modal-md modal ba-modal-dialog hide" style="display:none">
    <div class="modal-body">
        <div class="gallery-categories-list">
            <div class="gallery-categories-header">
                <div class="gallery-categories-title"><?php echo JText::_('CATEGORY'); ?></div>
                <div class="gallery-categories-id"><?php echo JText::_('ID'); ?></div>
            </div>
            <div class="gallery-categories-body"></div>
        </div>
    </div>
</div>