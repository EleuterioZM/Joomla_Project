<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>
                <label class="ba-hide-checkbox">
                    <input type="checkbox" name="checkall-toggle" value=""
                           title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    <i class="zmdi zmdi-check-circle check-all"></i>
                </label>
            </th>
            <th class="status-th <?php echo $listOrder == 'published' ? 'active' : ''; ?>">
                <span data-sorting="published">
                    <?php echo JText::_('JSTATUS'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
                <div class="state-filter">
                    <div class="ba-custom-select">
                        <input type="hidden" data-name="filter_state" value="<?php echo $state; ?>">
                        <i class="zmdi zmdi-caret-down"></i>
                        <ul>
                            <li data-value="">
                                <?php echo JText::_('JSTATUS');?>
                            </li>
                            <li data-value="1" >
                                <?php echo JText::_('JPUBLISHED');?>
                            </li>
                            <li data-value="0">
                                <?php echo JText::_('JUNPUBLISHED');?>
                            </li>
                        </ul>
                    </div>
                </div>
            </th>
            <th class="<?php echo $listOrder == 'title' ? 'active' : ''; ?>">
                <span data-sorting="title">
                    <?php echo JText::_('JGLOBAL_TITLE'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
            <th class="<?php echo $listOrder == 'price' ? 'active' : ''; ?>">
                <span data-sorting="price">
                    <?php echo JText::_('PRICE'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
            <th class="<?php echo $listOrder == 'stock' ? 'active' : ''; ?>">
                <span data-sorting="stock">
                    <?php echo JText::_('IN_STOCK'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
            <th class="sku-th <?php echo $listOrder == 'sku' ? 'active' : ''; ?>">
                <span data-sorting="sku">
                    <?php echo JText::_('SKU'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
            <th class="<?php echo $listOrder == 'page_category' ? 'active' : ''; ?>">
                <span data-sorting="page_category">
                    <?php echo JText::_('CATEGORY'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
            <th class="<?php echo $listOrder == 'hits' ? 'active' : ''; ?>">
                <span data-sorting="hits">
                    <?php echo JText::_('VIEWS'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
            <th class="<?php echo $listOrder == 'id' ? 'active' : ''; ?>">
                <span data-sorting="id">
                    <?php echo JText::_('ID'); ?>
                    <span class="ba-tooltip ba-top ba-hide-element ba-blog-id-tooltip">
                        <?php echo JText::_('SORT_BY_COLUMN'); ?>
                    </span>
                </span>
            </th>
        </tr>
    </thead>
    <tbody class="<?php echo str_replace('_', '-', $listOrder); ?>-sorting"
        data-category="<?php echo !empty($this->category) ? $this->category : 'root'; ?>">
<?php
    foreach ($this->items as $i => $item) { 
        $str = json_encode($item);
        $pageAssets = new gridboxAssetsHelper($item->id, 'page');
        $canChange = $pageAssets->checkPermission('core.edit.state');
        $editPage = $pageAssets->checkPermission('core.edit');
        if (!$editPage) {
            $editPage = $pageAssets->checkEditOwn($item->page_category);
        }
        if (!empty($item->intro_image)) {
            $introStr = '<span class="post-intro-image" style="background-image: url(';
            if (strpos($item->intro_image, 'https://') === false && strpos($item->intro_image, 'http://') === false) {
                $introStr .= JUri::root().str_replace(' ', '%20', $item->intro_image);
            } else {
                $introStr .= $item->intro_image;
            }
            $introStr .= ');"></span>';
        } else {
            $introStr = '';
        }
        $category = [$item->category];
        foreach ($item->categories as $obj) {
            $category[] = $obj->title;
        }
?>
        <tr>
            <td class="select-td">
                <label class="ba-hide-checkbox">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    <i class="zmdi zmdi-circle-o ba-icon-md"></i>
                    <i class="zmdi zmdi-check ba-icon-md"></i>
                </label>
                <input type="hidden"
                       value='<?php echo htmlspecialchars($str, ENT_QUOTES); ?>'>
            </td>
            <td class="status-td">
<?php
            if ($canChange) {
                echo JHtml::_('gridboxhtml.jgrid.published', $item->published, $i, 'pages.', $canChange);
            } else {
                $published = '<a class="disabled" href="javascript:void(0);"><i class="'.
                    ($item->published == 1 ? 'zmdi zmdi-eye' : 'zmdi zmdi-eye-off').
                    ' ba-icon-md"></i><span class="ba-tooltip ba-hide-element ba-top">'.
                    ($item->published == 1 ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED')).'</span></a>';
                echo $published;
            }
            if ($item->featured == 1) {
?>
                <span class="featured-post-wrapper active">
                    <i class="zmdi zmdi-star ba-icon-md set-featured-post" data-featured="0"></i>
                    <span class="ba-tooltip ba-hide-element ba-top">
                        <?php echo JText::_('FEATURED') ?>
                    </span>
                </span>
<?php
            } else {
?>
                <span class="featured-post-wrapper">
                    <i class="zmdi zmdi-star ba-icon-md set-featured-post" data-featured="1"></i>
                    <span class="ba-tooltip ba-hide-element ba-top">
                        <?php echo JText::_('FEATURED') ?>
                    </span>
                </span>
<?php
            }
?>
            </td>
            <td class="title-cell">
<?php
            if ($editPage) {
?>
                <a target="_blank"
                   href="<?php echo 'index.php?option=com_gridbox&task=gridbox.edit&id='. $item->id; ?>">
<?php
            } else {
?>
                <span class="not-permitted-wrapper">
<?php
            }
?>
                    <?php echo $introStr; ?>
                    <?php echo $item->title; ?>
                    <input type="hidden" name="order[]" value="<?php echo $item->order_list; ?>">
                    <input type="hidden" name="root_order[]" value="<?php echo $item->root_order_list; ?>">
<?php
            if ($editPage) {
?>
                </a>
<?php
            } else {
?>
                </span>
<?php
            }
?>
            </td>
            <td class="price-cell">
<?php
                echo gridboxHelper::preparePrice($item->price);
?>
            </td>
            <td class="stock-cell">
                <?php echo $item->stock; ?>
            </td>
            <td class="sku-cell">
                <?php echo $item->sku; ?>
            </td>
            <td class="category-cell">
                <?php echo implode(', ', $category); ?>
            </td>
            <td class="hits-cell">
                <?php echo $item->hits; ?>
            </td>
            <td>
                <?php echo $item->id; ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>