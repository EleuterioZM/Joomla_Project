<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
?>
<div id="slideshow-settings-dialog" class="ba-modal-cp draggable-modal-cp modal hide">
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
                    <a href="#slideshow-general-options" data-toggle="tab">
                        <i class="zmdi zmdi-settings"></i>
                        <span><?php echo JText::_('GENERAL'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#slideshow-design-options" data-toggle="tab">
                        <i class="zmdi zmdi-format-color-fill"></i>
                        <span><?php echo JText::_('DESIGN'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="#slideshow-layout-options" data-toggle="tab">
                        <i class="zmdi zmdi-fullscreen"></i>
                        <span><?php echo JText::_('LAYOUT'); ?></span>
                    </a>
                </li>
            </ul>
            <div class="tabs-underline"></div>
            <div class="tab-content">
                <div id="slideshow-general-options" class="row-fluid tab-pane active">
                    <div class="not-field-slideshow">
<?php
                    include $htmlPath.'ba-settings-group-items-list.php';
?>
                    </div>
                    <div class="ba-settings-group field-slideshow-options">
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('ADMIN_LABEL'); ?></span>
                            <input type="text" data-option="label" placeholder="<?php echo JText::_('ADMIN_LABEL'); ?>"
                                class="set-value-css">
                        </div>
                        <div class="ba-settings-item input-resize">
                            <span><?php echo JText::_('ADMIN_DESCRIPTION'); ?></span>
                            <input type="text" data-option="description" data-group="options" class="field-admin-description" 
                                placeholder="<?php echo JText::_('ADMIN_DESCRIPTION'); ?>"
                                class="set-value-css">
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SOURCE'); ?>
                            </span>
                            <div class="ba-custom-select select-field-upload-source">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('MEDIA_MANAGER'); ?></li>
                                    <li data-value="desktop"><?php echo JText::_('DESKTOP'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item desktop-source-filesize">
                            <span><?php echo JText::_('MAX_UPLOAD_FILE_SIZE'); ?></span>
                            <input type="text" data-option="size" data-group="options" class="set-value-css"
                                placeholder="<?php echo JText::_('MAX_UPLOAD_FILE_SIZE'); ?>">
                        </div>
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('REQUIRED'); ?></span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="required" class="set-value-css">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="ba-settings-group recent-posts-slider-options">
                        <div class="ba-settings-item recent-posts-slider-app-select">
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
                                    echo '<li data-value="'.$value->id.'" data-type="'.$value->type.'">'.$value->title.'</li>';
                                }
?>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
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
                        <div class="ba-settings-item post-tags-list tags-type-options">
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
                        <div class="ba-settings-item tags-categories-list categories-type-options">
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
                        <div class="ba-settings-item recent-posts-slider-featured">
                            <span>
                                <?php echo JText::_('FEATURED'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="featured" class="set-featured-posts">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-sorting">
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
                        <div class="ba-settings-item related-posts-slider-options">
                            <span>
                                <?php echo JText::_('SORT_BY'); ?>
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
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('MAX_ITEMS'); ?>
                            </span>
                            <input type="number" data-option="limit" class="lightbox-settings-input recent-limit"
                                placeholder="5">
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-desktop-windows"></i>
                            <span><?php echo JText::_('VIEW'); ?></span>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options">
                            <span>
                                <?php echo JText::_('TYPE'); ?>
                            </span>
                            <div class="ba-custom-select recent-posts-layout-select">
                                <input readonly="" onfocus="this.blur()" type="text">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="carousel"><?php echo JText::_('CAROUSEL'); ?></li>
                                    <li data-value="slideshow"><?php echo JText::_('SLIDESHOW'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item carousel-options slideset-options">
                            <span>
                                <?php echo JText::_('IMAGES_PER_SLIDE'); ?>
                            </span>
                            <input type="number" data-option="count" data-group="slideset" class="lightbox-settings-input"
                                placeholder="3">
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('FULLSCREEN'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="fullscreen" data-group="view" class="set-value-css">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('HEIGHT'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="1500">
                                <input type="text" data-option="height" data-group="view" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-size-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="" data-option="size" data-group="view" class="set-value-css">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="cover">Cover</li>
                                    <li data-value="contain">Contain</li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item carousel-options slideset-options">
                            <span>
                                <?php echo JText::_('COLUMNS_GUTTER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="gutter" class="set-value-css">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item carousel-options">
                            <span>
                                <?php echo JText::_('OVERFLOW'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="overflow" class="set-value-css">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options">
                            <span>
                                <?php echo JText::_('TITLE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="title" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options">
                            <span>
                                <?php echo JText::_('INFO'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="info"></i>
                            </span>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options">
                            <span>
                                <?php echo JText::_('REVIEWS'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="reviews" data-group="view" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options">
                            <span>
                                <?php echo JText::_('INTRO_TEXT'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="intro" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options category-list-store-view-options">
                            <span>
                                <?php echo JText::_('STORE'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="store"></i>
                            </span>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options category-list-fields-view-options">
                            <span>
                                <?php echo JText::_('FIELDS'); ?>
                            </span>
                            <span class="category-list-fields-wrapper">
                                <i class="zmdi zmdi-playlist-plus open-category-list-fields" data-target="fields"></i>
                            </span>
                        </div>
                        <div class="ba-settings-item recent-posts-slider-options">
                            <span>
                                <?php echo JText::_('BUTTON'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="button" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ARROWS'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="arrows" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item slideset-options">
                            <span>
                                <?php echo JText::_('DOTS'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="view" data-option="dots" class="set-desktop-view-value">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('NAVIGATION'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-navigation-select">
                                <input readonly onfocus="this.blur()" type="text">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="disabled-dots"><?php echo JText::_('NO_NE'); ?></li>
                                    <li data-value="enabled-dots"><?php echo JText::_('DOTS'); ?></li>
                                    <li data-value="thumbnails-dots"><?php echo JText::_('THUMBNAILS'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item slideshow-options thumbnails-navigation-options">
                            <span>
                                <?php echo JText::_('LAYOUT'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-navigation-layout-select">
                                <input readonly onfocus="this.blur()" type="text">
                                <input type="hidden">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="ba-left-thumbnails-navigation"><?php echo JText::_('LEFT'); ?></li>
                                    <li data-value=""><?php echo JText::_('BOTTOM'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('NAVIGATION_OUTSIDE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-group="dots" data-option="outside">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item slideshow-options thumbnails-navigation-options">
                            <span>
                                <?php echo JText::_('THUMBNAILS_PER_SLIDE'); ?>
                            </span>
                            <input type="number" data-option="count" data-group="thumbnails" placeholder="3"
                                class="lightbox-settings-input set-value-css">
                        </div>
                        <div class="ba-settings-item slideshow-options thumbnails-navigation-options">
                            <span>
                                <?php echo JText::_('THUMBNAILS_WIDTH'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="1500">
                                <input type="text" data-option="width" data-group="thumbnails" data-callback="sectionRules"
                                    class="set-value-css">
                            </div>
                        </div>
                        <div class="ba-settings-item slideshow-options thumbnails-navigation-options">
                            <span>
                                <?php echo JText::_('THUMBNAILS_HEIGHT'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="1500">
                                <input type="text" data-option="height" data-group="thumbnails" data-callback="sectionRules"
                                    class="set-value-css">
                            </div>
                        </div>
                        <div class="ba-settings-item slideshow-options thumbnails-navigation-options">
                            <span>
                                <?php echo JText::_('POSITION'); ?>
                            </span>
                            <div class="ba-settings-toolbar">
                                    <label data-option="align" data-value="" data-group="thumbnails"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-left"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('LEFT'); ?>
                                        </span>
                                    </label>
                                    <label data-option="align" data-value="center-align" data-group="thumbnails"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-center"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('CENTER'); ?>
                                        </span>
                                    </label>
                                    <label data-option="align" data-value="right-align" data-group="thumbnails"
                                        data-callback="sectionRules">
                                        <i class="zmdi zmdi-format-align-right"></i>
                                        <span class="ba-tooltip">
                                            <?php echo JText::_('RIGHT'); ?>
                                        </span>
                                    </label>
                                </div>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-tune"></i>
                            <span><?php echo JText::_('SETTINGS'); ?></span>
                        </div>
                        <div class="ba-settings-item carousel-options slideset-options">
                            <span>
                                <?php echo JText::_('AUTOPLAY'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="autoplay" data-group="slideset">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item carousel-options slideset-options">
                            <span>
                                <?php echo JText::_('PAUSE_ON_MOUSEOVER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="pause" data-group="slideset">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item carousel-options slideset-options">
                            <span>
                                <?php echo JText::_('SLIDE_DELAY'); ?>, ms
                            </span>
                            <input type="number" data-option="delay" data-group="slideset" class="lightbox-settings-input"
                                placeholder="<?php echo JText::_('SLIDE_DELAY'); ?>">
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('AUTOPLAY'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="autoplay" data-group="slideshow">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('PAUSE_ON_MOUSEOVER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="pause" data-group="slideshow">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('SLIDE_DELAY'); ?>, ms
                            </span>
                            <input type="number" data-option="delay" data-group="slideshow" class="lightbox-settings-input"
                                placeholder="3000">
                        </div>
                        <div class="ba-settings-item slideshow-options">
                            <span>
                                <?php echo JText::_('ANIMATION'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-animation-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="ba-fade-in" selected="selected">Fade</li>
                                    <li data-value="ba-offset-horizontal">Offset Horizontal</li>
                                    <li data-value="ba-offset-horizontal-fast">Offset Horizontal Fast</li>
                                    <li data-value="ba-offset-vertical">Offset Vertical</li>
                                    <li data-value="ba-offset-vertical-fast">Offset Vertical Fast</li>
                                    <li data-value="ba-ken-burns">Ken Burns</li>
                                </ul>
                            </div>
                        </div>
                    </div>
<?php
                    $options = ['class' => 'not-field-slideshow slideshow-options'];
                    echo $this->html->load('overlay', $options);
?>
                    <div class="ba-settings-group caption-settings-group carousel-options slideset-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-tune"></i>
                            <span><?php echo JText::_('CAPTION'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('LAYOUT'); ?>
                            </span>
                            <div class="ba-custom-select slideset-caption-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="caption-over"><?php echo JText::_('COVER'); ?></li>
                                    <li data-value=""><?php echo JText::_('CARD'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ON_MOUSEOVER'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="hover" data-group="caption">
                                <span></span>
                            </label>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('TYPE'); ?>
                            </span>
                            <div class="ba-custom-select background-overlay-select">
                                <input readonly onfocus="this.blur()" type="text">
                                <input type="hidden" data-property="overlay">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="color"><?php echo JText::_('COLOR'); ?></li>
                                    <li data-value="gradient"><?php echo JText::_('GRADIENT'); ?></li>
                                    <li data-value="none"><?php echo JText::_('NO_NE'); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="overlay-color-options">
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('COLOR'); ?>
                                </span>
                                <input type="text" data-type="color" data-option="color" data-group="overlay">
                                <span class="minicolors-opacity-wrapper">
                                    <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                    min="0" max="1" step="0.01">
                                    <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                </span>
                            </div>
                        </div>
                        <div class="overlay-gradient-options">
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('EFFECT'); ?>
                                </span>
                                <div class="ba-custom-select gradient-effect-select">
                                    <input readonly onfocus="this.blur()" value="" type="text">
                                    <input type="hidden" value="" data-property="overlay">
                                    <i class="zmdi zmdi-caret-down"></i>
                                    <ul>
                                        <li data-value="linear">Linear</li>
                                        <li data-value="radial">Radial</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="ba-settings-item overlay-linear-gradient">
                                <span>
                                    <?php echo JText::_('ANGLE'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="360" step="1">
                                    <input type="number" data-option="angle" data-group="overlay" data-subgroup="gradient"
                                        step="1" data-callback="sectionRules">
                                </div>
                            </div>
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('START_COLOR'); ?>
                                </span>
                                <input type="text" data-type="color" data-option="color1" data-group="overlay"
                                    data-subgroup="gradient">
                                <span class="minicolors-opacity-wrapper">
                                    <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                    min="0" max="1" step="0.01">
                                    <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                </span>
                            </div>
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('POSITION'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="100" step="1">
                                    <input type="number" data-option="position1" data-group="overlay" data-subgroup="gradient"
                                        step="1" data-callback="sectionRules">
                                </div>
                            </div>
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('END_COLOR'); ?>
                                </span>
                                <input type="text" data-type="color" data-option="color2" data-group="overlay"
                                    data-subgroup="gradient">
                                <span class="minicolors-opacity-wrapper">
                                    <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                    min="0" max="1" step="0.01">
                                    <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                                </span>
                            </div>
                            <div class="ba-settings-item">
                                <span>
                                    <?php echo JText::_('POSITION'); ?>
                                </span>
                                <div class="ba-range-wrapper">
                                    <span class="ba-range-liner"></span>
                                    <input type="range" class="ba-range" min="0" max="100" step="1">
                                    <input type="number" data-option="position2" data-group="overlay" data-subgroup="gradient"
                                        step="1" data-callback="sectionRules">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-select-all"></i>
                            <span><?php echo JText::_('LIGHTBOX'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('ENABLE'); ?>
                            </span>
                            <label class="ba-checkbox">
                                <input type="checkbox" data-option="popup">
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
                <div id="slideshow-layout-options" class="row-fluid tab-pane">
<?php
                    echo $this->html->loadPassive('positioning');
                    echo $this->html->load('margin');
?>
                </div>
                <div id="slideshow-design-options" class="row-fluid tab-pane">
                    <div class="ba-settings-group slideshow-design-group">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SELECT'); ?>
                            </span>
                            <div class="ba-custom-select slideshow-style-custom-select">
                                <input readonly onfocus="this.blur()" type="text" value="">
                                <input type="hidden" value="">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value="title"><?php echo JText::_('TITLE'); ?></li>
                                    <li data-value="description"><?php echo JText::_('DESCRIPTION'); ?></li>
                                    <li data-value="info"><?php echo JText::_('INFO'); ?></li>
                                    <li data-value="reviews"><?php echo JText::_('REVIEWS'); ?></li>
                                    <li data-value="intro"><?php echo JText::_('INTRO_TEXT'); ?></li>
                                    <li data-value="postFields"><?php echo JText::_('FIELDS'); ?></li>
                                    <li data-value="price"><?php echo JText::_('PRICE'); ?></li>
                                    <li data-value="button"><?php echo JText::_('BUTTON'); ?></li>
                                    <li data-value="arrows"><?php echo JText::_('ARROWS'); ?></li>
                                    <li data-value="dots" class="slideset-options slideshow-options">
                                        <?php echo JText::_('DOTS'); ?>
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
                        <div class="ba-settings-item slideshow-button-options">
                            <span>
                                <?php echo JText::_('LABEL'); ?>
                            </span>
                            <input type="text" class="recent-posts-button-label">
                        </div>
                    </div>
                    <div class="ba-settings-group slideshow-typography-options">
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
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-family"
                                        data-group="" data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item">
                                    <span>
                                        <?php echo JText::_('FONT_WEIGHT'); ?>
                                    </span>
                                    <input readonly onfocus="this.blur()" type="text" data-option="font-weight" data-group=""
                                        data-subgroup="typography" data-callback="sectionRules">
                                </div>
                                <div class="ba-settings-item slideshow-typography-color">
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
                                <div class="ba-settings-item slideshow-typography-hover">
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
                    <div class="ba-settings-group slideshow-animation-options">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-skip-next"></i>
                            <span><?php echo JText::_('ANIMATION'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('EFFECT'); ?></span>
                            <div class="ba-custom-select slideshow-item-effect-select visible-select-top">
                                <input readonly onfocus="this.blur()" type="text" value="None">
                                <input type="hidden" value="" data-option="effect" data-group="" data-subgroup="animation">
                                <i class="zmdi zmdi-caret-down"></i>
                                <ul>
                                    <li data-value=""><?php echo JText::_('NO_NE'); ?></li>
                                    <li data-value="bounceIn">Bounce In</li>
                                    <li data-value="bounceInLeft">Bounce In Left</li>
                                    <li data-value="bounceInRight">Bounce In Right</li>
                                    <li data-value="bounceInUp">Bounce In Up</li>
                                    <li data-value="fadeIn">Fade In</li>
                                    <li data-value="fadeInLeft">Fade In Left</li>
                                    <li data-value="fadeInRight">Fade In Right</li>
                                    <li data-value="fadeInUp">Fade In Up</li>
                                    <li data-value="zoomIn">Zoom In</li>
                                </ul>
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('DURATION'); ?></span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="2" step="0.1">
                                <input type="number" data-option="duration" data-group="" data-subgroup="animation"
                                    step="0.1" data-callback="sectionRules">
                            </div>
                        </div>
                        <div class="ba-settings-item">
                            <span><?php echo JText::_('DELAY'); ?></span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="5" step="0.1">
                                <input type="number" data-option="delay" data-group="" data-subgroup="animation"
                                    step="0.1" data-callback="sectionRules">
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group slideshow-dots-options">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('COLOR'); ?>
                            </span>
                            <input type="text" data-type="color" data-option="color" data-group="" data-subgroup="normal"
                                class="icon-color">
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
                            <input type="text" data-type="color" data-option="color" data-group="" data-subgroup="hover"
                                class="icon-color">
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
                                <input type="range" class="ba-range" min="0" max="1000">
                                <input type="text" data-option="size" data-group="" data-subgroup="" data-callback="sectionRules">
                            </div>
                        </div>
                    </div>
                    <div class="ba-settings-group slideshow-arrows-options">
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('SIZE'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="1000">
                                <input type="text" data-option="size" data-group="" data-subgroup="" data-callback="sectionRules">
                            </div>
                        </div>
                    </div>
<?php
                    $options = ['group' => '', 'subgroup' => 'colors'];
                    echo $this->html->load('colors', $options);
                    $options = ['class' => 'slideshow-margin-options', 'group' => 'description', 'subgroup' => 'margin'];
                    echo $this->html->load('margin', $options);
                    $options = ['class' => 'slideshow-button-options', 'group' => 'button', 'subgroup' => 'padding'];
                    echo $this->html->load('padding', $options);
?>
                    <div class="ba-settings-group slideshow-arrows-options">
                        <div class="settings-group-title">
                            <span><?php echo JText::_('PADDING'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('VALUE'); ?>
                            </span>
                            <div class="ba-range-wrapper">
                                <span class="ba-range-liner"></span>
                                <input type="range" class="ba-range" min="0" max="1000">
                                <input type="text" data-option="padding" data-group="" data-subgroup="" data-callback="sectionRules">
                            </div>
                        </div>
                    </div>
<?php
                    $options = ['class' => 'slideshow-border-options', 'group' => '', 'subgroup' => 'border'];
                    echo $this->html->load('border', $options);
                    $options = ['class' => 'slideshow-shadow-options', 'subgroup' => 'shadow', 'group' => ''];
                    echo $this->html->load('shadow', $options);
?>
                    <div class="ba-settings-group slideshow-design-group">
                        <div class="settings-group-title">
                            <i class="zmdi zmdi-select-all"></i>
                            <span><?php echo JText::_('LIGHTBOX'); ?></span>
                        </div>
                        <div class="ba-settings-item">
                            <span>
                                <?php echo JText::_('BACKGROUND'); ?>
                            </span>
                            <input type="text" data-type="color" data-option="color" data-group="lightbox"
                                class="minicolors-top">
                            <span class="minicolors-opacity-wrapper">
                                <input type="number" class="minicolors-opacity" data-callback="sectionRules"
                                min="0" max="1" step="0.01">
                                <span class="ba-tooltip"><?php echo JText::_('OPACITY') ?></span>
                            </span>
                        </div>
                    </div>
                </div>
                <i class="zmdi zmdi-more resize-handle-bottom"></i>
            </div>
        </div>
    </div>
</div>