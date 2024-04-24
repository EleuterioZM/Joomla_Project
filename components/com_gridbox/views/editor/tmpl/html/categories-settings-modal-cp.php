<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="categories-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
    <div class="modal-header">
        <span class="ba-dialog-title"></span>
        <div class="modal-header-icon">
            <div class="ba-custom-select select-modal-cp-position">
                <i class="zmdi zmdi-more-vert"></i>
                <input type="hidden">
                <ul>
                    <li data-value=""><?php echo JText::_('SEPARATE_WINDOW') ?></li>
                    <li data-value="right"><?php echo JText::_('PANEL_TO_RIGHT') ?></li>
                </ul>
            </div>
            <i class="zmdi zmdi-close close-cp-modal"></i>
        </div>
    </div>
    <div class="modal-body">
        <div class="general-tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#categories-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#categories-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#categories-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="categories-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('APP'); ?>
                            </span>
                            <div class="ba-custom-select categories-app-custom-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
<?php
                                foreach ($this->apps as $value) {
                                    echo '<li data-value="'.$value->id.'">'.$value->title.'</li>';
                                }
?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('LAYOUT'); ?>
                            </span>
                            <div class="ba-custom-select blog-posts-layout-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="ba-grid-layout"><?php echo JText::_('CARD'); ?></li>
                                    <li data-value="ba-cover-layout"><?php echo JText::_('COVER'); ?></li>
                                    <li data-value="ba-classic-layout"><?php echo JText::_('LIST'); ?></li>
                                    <li data-value="ba-masonry-layout"><?php echo JText::_('MASONRY'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item ba-classic-layout-options">
                            <span>
                                <?php echo JText::_('COLLAPSIBLE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="collapsible">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('NUMBER_OF_COLUMNS'); ?>
                            </span>
                            <input type="number" data-option="count" data-group="view" class="lightbox-settings-input set-value-css">
                        </div>
                        <div class="ba-settings-item blog-posts-cover-options">
                            <span>
                                <?php echo JText::_('COLUMNS_GUTTER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="gutter" data-group="view" class="set-value-css">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-desktop-windows"></i>
                            <span><?php echo JText::_('VIEW'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('IMAGE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="image" class="set-categories-view">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('TITLE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="title" class="set-categories-view">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SUBCATEGORIES'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="sub" data-group="view" class="set-categories-view">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ITEMS_COUNTER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="counter" data-group="view" class="set-categories-view">
                                <span></span>
                            </label>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
?>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-roller"></i>
                            <span><?php echo JText::_('PRESETS'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="ba-lg-custom-select select-preset">
                                <input type="text" readonly onfocus="this.blur()">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <div class="ba-lg-custom-select-header">
                                        <span class="create-new-preset">
                                            <i class="zmdi zmdi-plus-circle"></i>
                                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('SAVE_PRESET'); ?></span>
                                        </span>
                                        <span class="edit-preset-item">
                                            <i class="zmdi zmdi-edit"></i>
                                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('EDIT'); ?></span>
                                        </span>
                                        <span class="delete-preset-item">
                                            <i class="zmdi zmdi-delete"></i>
                                            <span class="ba-tooltip ba-bottom"><?php echo JText::_('DELETE'); ?></span>
                                        </span>
                                    </div>
                                    <div class="ba-lg-custom-select-body">
                                        <li data-value="">
                                            <label>
                                                <input type="radio" name="preset-checkbox" value="">
                                                <i class="zmdi zmdi-circle-o"></i>
                                                <i class="zmdi zmdi-check"></i>
                                            </label>
                                            <span><?php echo JText::_('NO_NE'); ?></span>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('item-animation');
                    echo $this->html->loadPassive('advanced', $this->access);
?>
                </div>
                <div id="categories-design-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group slideshow-design-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="ba-custom-select ba-style-custom-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="image"><?php echo JText::_('IMAGE'); ?></li>
                                    <li data-value="title"><?php echo JText::_('TITLE'); ?></li>
                                    <li data-value="info"><?php echo JText::_('SUBCATEGORIES'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item title-html-tag">
                            <span>
                                <?php echo JText::_('HTML_TAG'); ?>
                            </span>
                            <div class="ba-custom-select select-title-html-tag">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="h1">H1</li>
                                    <li data-value="h2">H2</li>
                                    <li data-value="h3">H3</li>
                                    <li data-value="h4">H4</li>
                                    <li data-value="h5">H5</li>
                                    <li data-value="h6">H6</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group ba-style-typography-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-format-size"></i>
                            <span><?php echo JText::_('TYPOGRAPHY'); ?></span>
                        </div>
                        <div class="theme-typography-options">
                            <div class="typography-options">
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_FAMILY'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-family" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_WEIGHT'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-weight"
                                        data-group="" data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item ba-style-typography-color">
                                    <span>
                                        <?php echo JText::_('COLOR'); ?>
                                    </span>
                                    <input type="text" data-type="color" data-option="color" data-group="" data-subgroup="typography">
                                    <span class="minicolors-opacity-wrapper">
                                        <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                        min="0" max="1" step="0.01">
                                        <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                    </span>
                                </div>
                                <div class="ba-settings-item ba-style-typography-hover-color desktop-only" style="display: none;">
                                    <span>
                                        <?php echo JText::_('HOVER'); ?>
                                    </span>
                                    <input type="text" data-type="color" data-option="color" data-group="" data-subgroup="hover">
                                    <span class="minicolors-opacity-wrapper">
                                        <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                        min="0" max="1" step="0.01">
                                        <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                    </span>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('SIZE'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner"></span>
                                        <input type="range" class="ba-range" min="0" max="320">
                                        <input type="text" data-option="font-size" data-group="" data-subgroup="typography"
                                            data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('LETTER_SPACING'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner letter-spacing"></span>
                                        <input type="range" class="ba-range" min="-10" max="10">
                                        <input type="text" data-option="letter-spacing" data-group="" data-subgroup="typography"
                                            data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('LINE_HEIGHT'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner"></span>
                                        <input type="range" class="ba-range" min="0" max="640">
                                        <input type="text" data-option="line-height" data-group="" data-subgroup="typography"
                                            data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-toolbar">
                                    <label data-option="text-decoration" data-value="underline" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-underlined"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UNDERLINE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-transform" data-value="uppercase" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-size"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UPPERCASE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="font-style" data-value="italic" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-italic"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('ITALIC'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="left" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-left"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('LEFT'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="center" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-center"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('CENTER'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="right" data-group="" data-subgroup="typography"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-right"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('RIGHT'); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group ba-style-image-options">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('WIDTH'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="10" max="1500">
                                <input type="text" data-option="width" data-group="image" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('HEIGHT'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="10" max="1500">
                                <input type="text" data-option="height" data-group="image" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-item not-author-options">
                            <span>
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-custom-select">
                                <input readonly onfocus="this.blur()" type="text">
                                <input type="hidden" data-option="size" data-group="image" class="set-value-css">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="cover">Cover</li>
                                    <li data-value="contain">Contain</li>
                                </ul>
                            </div>
                        </div>
                    </div>
<?php
                    $options = ['class' => 'ba-style-image-options blog-posts-cover-options'];
                    echo $this->html->load('overlay', $options);
                    $options = ['class' => 'ba-style-margin-options', 'group' => 'description', 'subgroup' => 'margin'];
                    echo $this->html->load('margin', $options);
                    $options = ['class' => 'ba-style-border-options', 'group' => '', 'subgroup' => 'border'];
                    echo $this->html->load('border', $options);
?>
                    <div class="ba-settings-group blog-posts-background-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-format-color-fill"></i>
                            <span><?php echo JText::_('BACKGROUND'); ?></span>
                        </div>
                        <div class="ba-settings-item background">
                            <span>
                                <?php echo JText::_('COLOR'); ?>
                            </span>
                            <input type="text" data-type="color" data-option="color" data-group="background" class="minicolors-top">
                            <span class="minicolors-opacity-wrapper">
                                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                min="0" max="1" step="0.01">
                                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div id="categories-layout-options" class="row-fluid tab-pane active">
<?php
                    echo $this->html->loadPassive('positioning');
                    echo $this->html->load('margin');
                    echo $this->html->load('padding');
                    echo $this->html->load('border');
                    $options = ['class' => 'blog-posts-shadow-options'];
                    echo $this->html->load('shadow');
?>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>