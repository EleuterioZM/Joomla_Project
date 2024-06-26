<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="recent-posts-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#recent-posts-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#recent-posts-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#recent-posts-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="recent-posts-general-options" class="row-fluid tab-pane active">
                    <div class="ba-settings-group recent-posts-options related-posts-options not-author-options">
                        <div class="ba-settings-item recent-posts-options">
                            <span>
                                <?php echo JText::_('APP'); ?>
                            </span>
                            <div class="ba-custom-select recent-posts-app-select">
                                <input readonly="" onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="1" data-option="app">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
<?php
                                foreach ($this->apps as $value) {
                                    echo '<li data-value="'.$value->id.'" data-type="'.$value->type.'">'
                                        .$value->title.'</li>';
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item recent-posts-options">
                            <span>
                                <?php echo JText::_('TYPE'); ?>
                            </span>
                            <div class="ba-custom-select recent-posts-type-select">
                                <input type="text" readonly="" onfocus="this.blur()">
                                <input type="hidden" value="1" data-option="app">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('CATEGORY'); ?></li>
                                    <li data-value="tags"><?php echo JText::_('TAGS'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item recent-posts-options post-tags-list tags-type-options">
                            <span>
                                <?php echo JText::_('TAGS'); ?>
                            </span>
                            <div>
                                <ul class="post-tags-list">
                                    <li class="trigger-post-tags-modal">
                                        <input type="text" placeholder="<?php echo JText::_('TAGS'); ?>" readonly>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item recent-posts-options tags-categories-list categories-type-options">
                            <span>
                                <?php echo JText::_('CATEGORY'); ?>
                            </span>
                            <div class="tags-categories">
                                <ul class="selected-categories">
                                    <li class="search-category">
                                        <input type="text" placeholder="<?php echo JText::_('CATEGORY'); ?>" readonly>
                                    </li>
                                </ul>
                                <ul class="all-categories-list">
<?php
                                foreach ($this->categories as $category) {
                                    $content = '';
                                    for ($i = 0; $i < $category->level; $i++) {
                                        $content .= '- ';
                                    }
                                    if ($category->level != 0) {
                                        $content .= '-';
                                    }
?>
                                    <li data-id="<?php echo $category->id; ?>" data-app="<?php echo $category->app_id; ?>"
                                        style="--content: '<?php echo $content; ?>';">
                                        <?php echo $category->title; ?>
                                    </li>
<?php
                                }
?>
                                </ul>
                            </div>
                            <label class="ba-help-icon">
                                <i class="zmdi zmdi-help"></i>
                                <span class="ba-tooltip ba-help">
                                    <?php echo JText::_('POSTS_CATEGORY_TOOLTIP'); ?>
                                </span>
                            </label>
                        </div>
                        <div class="ba-settings-item recent-posts-options">
                            <span>
                                <?php echo JText::_('FEATURED'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="featured" class="set-featured-posts">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item related-posts-options">
                            <span>
                                <?php echo JText::_('RELATED_BY'); ?>
                            </span>
                            <div class="ba-custom-select related-posts-display-select">
                                <input readonly="" onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="1" data-option="related">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="tags"><?php echo JText::_('TAGS'); ?></li>
                                    <li data-value="categories"><?php echo JText::_('CATEGORIES'); ?></li>
<?php
                                if ($this->edit_type == 'post-layout' && $this->item->type == 'products') {
?>
                                    <li data-value="custom"><?php echo JText::_('CUSTOM'); ?></li>
<?php
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item recent-posts-options related-posts-options">
                            <span>
                                <?php echo JText::_('SORT_BY'); ?>
                            </span>
                            <div class="ba-custom-select recent-posts-display-select">
                                <input readonly="" onfocus="this.blur()" value="" type="text">
                                <input type="hidden" value="1" data-option="sorting">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="created"><?php echo JText::_('RECENT'); ?></li>
                                    <li data-value="hits"><?php echo JText::_('POPULAR'); ?></li>
                                    <li data-value="title ASC"><?php echo JText::_('TITLE_A_Z'); ?></li>
                                    <li data-value="title DESC"><?php echo JText::_('TITLE_Z_A'); ?></li>
                                    <li data-value="order_list"><?php echo JText::_('CUSTOM'); ?></li>
                                    <li data-value="random"><?php echo JText::_('RANDOM'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item related-posts-options">
                            <span>
                                <?php echo JText::_('MAX_ITEMS'); ?>
                            </span>
                            <input type="number" data-option="limit" class="lightbox-settings-input recent-limit"
                                placeholder="5">
                        </div>
                        <div class="ba-settings-item recent-posts-options">
                            <span>
                                <?php echo JText::_('PAGINATION'); ?>
                            </span>
                            <div class="ba-custom-select recent-posts-pagination-select">
                                <input readonly="" onfocus="this.blur()" type="text">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('NO_NE'); ?></li>
                                    <li data-value="load-more"><?php echo JText::_('LOAD_MORE'); ?></li>
                                    <li data-value="infinity"><?php echo JText::_('INFINITE_SCROLLING'); ?></li>
                                    <li data-value="load-more-infinity">
                                        <?php echo JText::_('LOAD_MORE_INFINITE_SCROLLING'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item recent-posts-options">
                            <span>
                                <?php echo JText::_('ITEMS_PER_PAGE'); ?>
                            </span>
                            <input type="number" data-option="limit" class="lightbox-settings-input recent-limit"
                                placeholder="5">
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
                                    <li data-value="ba-one-column-grid-layout"><?php echo JText::_('CLASSIC'); ?></li>
                                    <li data-value="ba-grid-layout"><?php echo JText::_('CARD'); ?></li>
                                    <li data-value="ba-cover-layout" class="not-author-options">
                                        <?php echo JText::_('COVER'); ?>
                                    </li>
                                    <li data-value="ba-classic-layout"><?php echo JText::_('LIST'); ?></li>
                                    <li data-value="ba-masonry-layout" class="not-author-options">
                                        <?php echo JText::_('MASONRY'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item blog-posts-grid-options">
                            <span>
                                <?php echo JText::_('NUMBER_OF_COLUMNS'); ?>
                            </span>
                            <input type="number" data-option="count" data-group="view"
                                class="lightbox-settings-input set-value-css">
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
                        <div class="ba-settings-item search-result-options">
                            <span>
                                <?php echo JText::_('PAGINATION'); ?>
                            </span>
                            <div class="ba-custom-select search-result-pagination-select">
                                <input readonly="" onfocus="this.blur()" type="text">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('DEFAULT'); ?></li>
                                    <li data-value="load-more"><?php echo JText::_('LOAD_MORE'); ?></li>
                                    <li data-value="infinity"><?php echo JText::_('INFINITE_SCROLLING'); ?></li>
                                    <li data-value="load-more-infinity">
                                        <?php echo JText::_('LOAD_MORE_INFINITE_SCROLLING'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-group search-result-options">
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('ITEMS_PER_PAGE'); ?>
                                </span>
                                <input type="number" data-option="limit"
                                    class="lightbox-settings-input set-value-css" placeholder="3">
                            </div>
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
                                <input type="checkbox" data-group="view" data-option="image" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('TITLE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="title" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item not-author-options">
                            <span>
                                <?php echo JText::_('INFO'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="info"></i>
                            </span>
                        </div>
                        <div class="ba-settings-item not-author-options">
                            <span>
                                <?php echo JText::_('REVIEWS'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="reviews" data-group="view" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span class="not-author-options">
                                <?php echo JText::_('INTRO_TEXT'); ?>
                            </span>
                            <span class="author-options">
                                <?php echo JText::_('DESCRIPTION'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="intro" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item category-list-store-view-options">
                            <span>
                                <?php echo JText::_('STORE'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="store"></i>
                            </span>
                        </div>
                        <div class="ba-settings-item category-list-fields-view-options">
                            <span>
                                <?php echo JText::_('FIELDS'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="fields"></i>
                            </span>
                        </div>
                        <div class="ba-settings-item not-author-options">
                            <span>
                                <?php echo JText::_('BUTTON'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="button" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                    </div>
<?php
                    echo $this->html->loadPassive('disable');
?>
                    <div class="ba-settings-group preset-options">
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
                <div id="recent-posts-design-options" class="row-fluid tab-pane">
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
                                    <li data-value="info" class="not-author-options"><?php echo JText::_('INFO'); ?></li>
                                    <li data-value="reviews" class="not-author-options">
                                        <?php echo JText::_('REVIEWS'); ?>
                                    </li>
                                    <li data-value="intro" class="not-author-options">
                                        <?php echo JText::_('INTRO_TEXT'); ?>
                                    </li>
                                    <li data-value="intro" class="author-options"><?php echo JText::_('DESCRIPTION'); ?></li>
                                    <li data-value="postFields" class="not-author-options">
                                        <?php echo JText::_('FIELDS'); ?>
                                    </li>
                                    <li data-value="price" class="not-author-options"><?php echo JText::_('PRICE'); ?></li>
                                    <li data-value="button" class="not-author-options"><?php echo JText::_('BUTTON'); ?></li>
                                    <li data-value="pagination" class="search-result-options not-author-options">
                                        <?php echo JText::_('PAGINATION'); ?>
                                    </li>
                                    <li data-value="pagination" class="recent-posts-options not-author-options">
                                        <?php echo JText::_('PAGINATION'); ?>
                                    </li>
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
                        <div class="ba-settings-item ba-style-intro-options">
                            <span>
                                <?php echo JText::_('MAXIMUM_LENGTH'); ?>
                            </span>
                            <input type="number" data-option="maximum" class="lightbox-settings-input" placeholder="50">
                            <label class="ba-help-icon">
                                <i class="zmdi zmdi-help"></i>
                                <span class="ba-tooltip ba-help">
                                <?php echo JText::_('MAXIMUM_LENGTH_TOOLTIP'); ?>
                                </span>
                            </label>
                        </div>
                        <div class="ba-settings-item ba-style-button-options">
                            <span>
                                <?php echo JText::_('LABEL'); ?>
                            </span>
                            <input type="text" class="recent-posts-button-label">
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
                                    <input type="text" data-type="color" data-option="color" data-group=""
                                        data-subgroup="typography">
                                    <span class="minicolors-opacity-wrapper">
                                        <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                            min="0" max="1" step="0.01">
                                        <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                    </span>
                                </div>
                                <div class="ba-settings-item ba-style-typography-hover-color desktop-only"
                                    style="display: none;">
                                    <span>
                                        <?php echo JText::_('HOVER'); ?>
                                    </span>
                                    <input type="text" data-type="color" data-option="color" data-group=""
                                        data-subgroup="hover">
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
                                        <input type="text" data-option="letter-spacing" data-group=""
                                            data-subgroup="typography" data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('LINE_HEIGHT'); ?>
                                    </span>
                                    <div class="ba-range-wrapper">
                                        <span class="ba-range-liner"></span>
                                        <input type="range" class="ba-range" min="0" max="640">
                                        <input type="text" data-option="line-height" data-group=""
                                            data-subgroup="typography" data-callback="sectionRules">
                                    </div>
                                </div>
                                <div class="ba-settings-toolbar">
                                    <label data-option="text-decoration" data-value="underline"
                                        data-group="" data-subgroup="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-underlined"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UNDERLINE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-transform" data-value="uppercase" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-size"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('UPPERCASE'); ?>
                                        </span>
                                    </label>
                                    <label data-option="font-style" data-value="italic" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
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
                                    <label data-option="text-align" data-value="center" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-center"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('CENTER'); ?>
                                        </span>
                                    </label>
                                    <label data-option="text-align" data-value="right" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
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
?>
                    <div class="ba-settings-group ba-style-pagination-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-format-size"></i>
                            <span><?php echo JText::_('TYPOGRAPHY'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('COLOR'); ?>
                            </span>
                            <input type="text" data-type="color" data-option="color" data-group="pagination">
                            <span class="minicolors-opacity-wrapper">
                                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                min="0" max="1" step="0.01">
                                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                            </span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('HOVER_ACTIVE'); ?>
                            </span>
                            <input type="text" data-type="color" data-option="hover" data-group="pagination">
                            <span class="minicolors-opacity-wrapper">
                                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                min="0" max="1" step="0.01">
                                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                            </span>
                        </div>
                    </div>
<?php
                    $options = ['class' => 'ba-style-button-options', 'group' => '', 'subgroup' => 'colors'];
                    echo $this->html->load('colors', $options);
                    $options = ['class' => 'ba-style-margin-options', 'group' => 'description', 'subgroup' => 'margin'];
                    echo $this->html->load('margin', $options);
                    $options = ['class' => 'ba-style-button-options', 'group' => 'button', 'subgroup' => 'padding'];
                    echo $this->html->load('padding', $options);
                    $options = ['class' => 'ba-style-border-options', 'group' => '', 'subgroup' => 'border'];
                    echo $this->html->load('border', $options);
                    $options = ['class' => 'ba-style-button-options', 'subgroup' => 'shadow', 'group' => ''];
                    echo $this->html->load('shadow', $options);
                    $options = ['class' => 'blog-posts-background-options'];
                    echo $this->html->load('feature-background', $options);
?>
                </div>
                <div id="recent-posts-layout-options" class="row-fluid tab-pane">
<?php
                    echo $this->html->loadPassive('positioning');
                    echo $this->html->load('margin');
                    echo $this->html->load('padding');
                    echo $this->html->load('border');
                    $options = ['class' => 'blog-posts-shadow-options'];
                    echo $this->html->load('shadow', $options);
?>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>