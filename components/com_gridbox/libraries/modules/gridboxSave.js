/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// location of function prepareItem in getSession

var scrollTop = {};

function prepareAnimation($this)
{
    var $item = $g($this);
    $item.find('meta[http-equiv]').remove();
    $item.find('.ba-item-slideshow .ba-slideshow').addClass('first-load-slideshow');
    $item.find('.ba-item-recent-posts-slider, .ba-item-related-posts-slider, .ba-item-recently-viewed-products')
        .find('.slideshow-type').addClass('first-load-slideshow');
    $item.find('.slideshow-content li.active').removeClass('active');
    $item.find('.slideshow-content li:first-child').addClass('active');
    $item.find('.slideset-loaded').removeClass('slideset-loaded');
    $item.find('.ba-item-slideset, .ba-item-carousel').each(function(){
        var obj = app.editor.app.items[this.id];
        if (obj) {
            $g(this).find('li').each(function(ind){
                this.style.left = '';
                if (ind == obj.desktop.slideset.count) {
                    return false;
                }
                this.classList.add('active');
            });
            $g(this).find('.slideshow-content, ul').removeAttr('style');
        }
        $g(this).find('.slideshow-content').css('left', '').find('li').css('order', '');
    });
    $item.find('.ba-masonry-image-loaded').removeClass('ba-masonry-image-loaded').css({
        'transition-delay': '',
        'grid-row-end': ''
    });
    $item.find('.simple-gallery-masonry-layout .ba-instagram-image').css('transition-delay', '');
    $item.find('.slideshow-content').css('height', '');
    $item.find('.ba-item-testimonials ul').removeAttr('style');
    $item.find('.ba-slideshow-dots div').removeAttr('style');
    $item.find('.slideshow-content').css('left', '');
    $item.find('.ba-item-progress-bar .ba-animated-bar').css('width', '0%').find('.progress-bar-number').text('0%');
    $item.find('.ba-item-progress-pie').find('.progress-pie-number').text('0%');
    $item.find('.ba-slideshow-dots .active').removeClass('active');
    $item.find('.ba-slideshow-dots div:first-child').addClass('active');
    $item.find('.hidden').removeClass('hidden');
    $item.find('.visible-sticky-header').removeClass('visible-sticky-header');
    $item.find('.ba-sticky-header').removeAttr('style');
    $item.find('.visible').each(function(){
        if ($g(this).closest('.ba-item-content-slider').length == 0) {
            this.classList.remove('visible');
            this.classList.remove('animated');
        }
    });
    $item.find('.ba-next').removeClass('ba-next');
    $item.find('.ba-prev').removeClass('ba-prev');
    $item.find('.ba-left').removeClass('ba-left');
    $item.find('.ba-right').removeClass('ba-right');
    $item.find('.burns-out').removeClass('burns-out');
    $item.find('.left-animation').removeClass('left-animation');
    $item.find('.right-animation').removeClass('right-animation');
    $item.find('.prev-animation').removeClass('prev-animation');
    $item.find('.next-animation').removeClass('next-animation');

    return $this;
}

function clearBlogPluginsContent(item)
{
    var searchStr = '.ba-item-search-result .ba-blog-posts-wrapper, .ba-item-search-result .ba-blog-posts-pagination-wrapper, '+
        '.ba-item-store-search-result .ba-blog-posts-wrapper, .ba-item-store-search-result .ba-blog-posts-pagination-wrapper, '+
        '.ba-item-categories .ba-categories-wrapper, .ba-item-post-navigation .ba-blog-posts-wrapper, '+
        '.ba-item-post-tags .ba-button-wrapper, .ba-item-recent-posts .ba-blog-posts-wrapper, '+
        '.ba-item-recent-posts-slider .slideshow-content, .ba-item-related-posts-slider .slideshow-content, '+
        '.ba-item-recently-viewed-products .slideshow-content, '+
        '.ba-item-related-posts .ba-blog-posts-wrapper, .ba-item-tags .ba-button-wrapper, '+
        '.ba-item-author .ba-posts-author-wrapper, .ba-item-recent-comments .ba-blog-posts-wrapper, '+
        '.ba-item-recent-reviews .ba-blog-posts-wrapper, .ba-item-fields-filter .ba-fields-filter-wrapper';
    item.find(searchStr).empty();
}

function prepareHTML(search, obj, items)
{
    var item = app.editor.document.querySelector(search);
    search = search.replace('.header', '').replace('.footer', '');
    if (!item) {
        return false;
    }
    item = item.cloneNode(true);
    var clone = $g(item),
        find = '.ba-item-main-menu > .ba-menu-wrapper > .main-menu > .integration-wrapper > ul > li > .tabs-content-wrapper';
    clone.find(find).each(function(){
        $g(this).closest('.ba-menu-wrapper').append(this);
    });
    clone.find('.ba-item-text > .content-text').each(function(){
        var div = document.createElement('div');
        div.className = 'content-text';
        div.setAttribute('contenteditable', true);
        div.innerHTML = this.innerHTML;
        $g(this).replaceWith(div);
    });
    if ('defaultElementsBox' in app.editor) {
        clone.find('.ba-item').each(function(){
            var className = this.className,
                match = className.match(/[-\w]+/g)
            if (app.editor.defaultElementsBox[match[0]]) {
                $g(this).find('> .ba-edit-item, > .ba-box-model').remove();
            }
        });
        clone.find('.ba-row').each(function(){
            if (app.editor.defaultElementsBox['ba-row']) {
                $g(this).find('> .ba-edit-item, > .ba-box-model').remove();
            }
        });
        clone.find('.ba-grid-column').each(function(){
            if (($g(this).closest('.ba-row-wrapper').parent().hasClass('ba-grid-column') ||
                    !$g(this).closest('.ba-wrapper').hasClass('tabs-content-wrapper'))) {
                $g(this).find('> .ba-edit-item, > .ba-box-model').remove();
            }
        });
    }
    item = prepareAnimation(item);
    clearBlogPluginsContent(clone);
    for (var i = 0; i < scrollTop.length; i++) {
        if (items[scrollTop[i].id]) {
            var parent = items[scrollTop[i].id].parent,
                column = $g(item).find('#'+parent);
            if (column.length > 0) {
                var scrollItem = scrollTop[i].cloneNode(true);
                column.find(' > .empty-item').before(scrollItem);
            }
        }
    }
    clone.find('.ba-item-simple-gallery .ba-instagram-image img').each(function(){
        this.src = this.dataset.src;
        $g(this).closest('.ba-instagram-image').css('background-image', 'url('+this.dataset.src+')');
    });
    clone.find('.page-structure-item-active').removeClass('page-structure-item-active');
    clone.find('.ba-item-blog-content .blog-content-wrapper').text('[blog_content]');
    clone.find('.visible-lightbox').removeClass('visible-lightbox');
    clone.find('> .page-layout').remove();
    clone.find('[data-global]').replaceWith(function(){
        obj.global[this.dataset.global] = {};
        obj.global[this.dataset.global].items = replaceGlobalItems(this, obj, items);
        obj.global[this.dataset.global].html = this.outerHTML;
        return '[global item='+this.dataset.global+']';
    });
    clone.find('.ba-item-blog-posts .ba-blog-posts-wrapper').html('[blog_posts_items]');
    clone.find('.ba-item-blog-posts .ba-blog-posts-pagination-wrapper').html('[blog_posts_pagination]');
    clone.find('.intro-post-image-wrapper').replaceWith('[intro-post-image]');
    clone.find('.intro-post-title').html('[intro-post-title]');
    clone.find('.intro-post-date').html('[intro-post-date]');
    clone.find('.intro-post-category').html('[intro-post-category]');
    clone.find('.intro-post-views').html('[intro-post-views]');
    clone.find('.ba-item-post-tags .ba-button-wrapper').html('[blog_post_tags]');
    clone.find('.ba-section').each(function(){
        if (items[this.id]) {
            if (items[this.id].type == 'header') {
                obj.theme.layout = items[this.id].layout
            }
            obj.theme[search].items[this.id] = items[this.id];
        }
    });
    clone.find('.ba-row').each(function(){
        if (items[this.id]) {
            obj.theme[search].items[this.id] = items[this.id];
        }
    });
    clone.find('.ba-grid-column').each(function(){
        if (items[this.id]) {
            obj.theme[search].items[this.id] = items[this.id];
        }
    });
    clone.find('.ba-item').each(function(){
        if (items[this.id]) {
            obj.theme[search].items[this.id] = items[this.id];
            prepareItem(this, items[this.id]);
        }
    });
    clone.find('.ba-item-main-menu').each(function(){
        this.innerHTML = this.innerHTML.trim().replace(/\n/g, "")
            .replace(/[\t ]+\</g, "<").replace(/\>[\t ]+\</g, "><").replace(/\>[\t ]+$/g, ">");
    });
    obj.theme[search].html = item.innerHTML.trim().replace(/\n+/g, '\n');
}

function replaceGlobalItems(item, obj, items)
{
    var glob = {};
    $g(item).find('.ba-section').each(function(){
        if (items[this.id]) {
            glob[this.id] = items[this.id];
        }
    });
    $g(item).find('.ba-row').each(function(){
        if (items[this.id]) {
            glob[this.id] = items[this.id];
        }
    });
    $g(item).find('.ba-grid-column').each(function(){
        if (items[this.id]) {
            glob[this.id] = items[this.id];
        }
    });
    $g(item).find('.ba-item').each(function(){
        if (items[this.id]) {
            glob[this.id] = items[this.id];
            prepareItem(this, items[this.id]);
        }
    });
    if (item.classList.contains('ba-item')) {
        if (items[item.id]) {
            glob[item.id] = items[item.id];
            prepareItem(item, items[item.id]);
        }
    }

    return glob;
}

app.gridboxSave = function(){
    var button = document.querySelector('.gridbox-save');
    if (button.dataset.action == 'clicked') {
        return false;
    } else {
        button.dataset.action = 'clicked';
    }
    fetch(JUri+'index.php?option=com_gridbox&task=editor.checkSitemap');
    if (app.editor.app.languageSwitcher) {
        app.editor.app.languageSwitcher.removeClass();
    }
    app.editor.$g('.ba-item-flipbox').each(function(){
        var $this = this;
        this.classList.remove('backside-fliped');
        this.classList.add('flipbox-animation-started');
        app.editor.app.items[this.id].side = "frontside";
        setTimeout(function(){
            $this.classList.remove('flipbox-animation-started');
        }, app.editor.app.items[this.id].desktop.animation.duration * 1000);
    });
    app.editor.$g('.ba-item-in-positioning').each(function(){
        let parent = app.positioning.getParent(app.editor.app.items[this.id]);
        $g(parent).find('> .empty-item').before(this);
    });
    app.editor.$g('.ba-item-hotspot').each(function(){
        app.editor.app.hotspot.hide();
    });
    app.editor.$g('.ba-item-reading-progress-bar').each(function(){
        let parent = app.editor.document.querySelector('#'+app.editor.app.items[this.id].parent);
        if (!parent) {
            parent = app.editor.document.querySelector('.ba-grid-column');
        }
        this.style.top = '';
        this.querySelector('.ba-animated-bar').style.width = '0%';
        $g(parent).find('> .empty-item').before(this);
    });
    var scrollToTopArray = [],
        socialSidebarArray = [];
    app.editor.$g('.ba-item-scroll-to-top').each(function(){
        if (scrollToTopArray.indexOf(this.id) != -1) {
            $g(this).remove();
        } else {
            scrollToTopArray.push(this.id);
        }
    });
    app.editor.$g('.ba-item-social.ba-social-sidebar').each(function(){
        if (socialSidebarArray.indexOf(this.id) != -1) {
            $g(this).remove();
        } else {
            socialSidebarArray.push(this.id);
        }
    });
    scrollTop = app.editor.document.querySelectorAll('.ba-item-scroll-to-top, .ba-social-sidebar');
    for (var i = 0; i < scrollTop.length; i++) {
        scrollTop[i].classList.remove('visible-scroll-to-top');
        var parent = app.editor.app.items[scrollTop[i].id].parent,
            item = app.editor.document.getElementById(parent);
        if (!item) {
            item = app.editor.document.querySelector('.ba-grid-column');
            if (item) {
                app.editor.app.items[scrollTop[i].id].parent = item.id;
            }
        }
    };
    var baItems = app.editor.document.querySelectorAll('.ba-item-overlay-section');
    for (var i = 0; i < baItems.length; i++) {
        var overlay =  app.editor.document.querySelector('.ba-overlay-section-backdrop[data-id="'+baItems[i].dataset.overlay+'"]');
        if (overlay) {
            overlay.classList.remove('visible-section');
            baItems[i].appendChild(overlay);
        }
    }
    baItems = app.editor.document.querySelectorAll('.visible-lightbox');
    for (var i = 0; i < baItems.length; i++) {
        baItems[i].classList.remove('visible-lightbox');
    }
    app.editor.document.body.classList.remove('lightbox-open');
    app.editor.document.body.classList.remove('ba-lightbox-open');
    app.editor.document.body.classList.remove('search-open');
    app.editor.document.body.style.width = '';
    baItems = app.editor.document.querySelectorAll('.visible-menu');
    for (var i = 0; i < baItems.length; i++) {
        baItems[i].classList.remove('visible-menu');
        baItems[i].classList.remove('hide-menu');
        baItems[i].style.right = '';
    }
    app.editor.$g('.column-with-menu').removeClass('column-with-menu');
    app.editor.$g('.row-with-menu').removeClass('row-with-menu');
    app.editor.$g('.ba-visible-menu-backdrop').removeClass('ba-visible-menu-backdrop');
    app.editor.document.body.classList.remove('ba-opened-menu');
    button.flags = {
        tags: false,
        products: false,
    }
    var page = app.editor.document.getElementById('ba-edit-section'),
        grid = app.editor.document.getElementById('grid_id'),
        theme = app.editor.themeData.theme,
        obj = {
            global: {},
            breakpoints: $g.extend(true, {}, app.editor.breakpoints),
            megamenu: {},
            theme: {
                params: app.editor.app.theme,
                header: {
                    items: {}
                },
                footer: {
                    items: {}
                },
                '#ba-edit-section': {
                    items: {}
                }
            },
            website: {
                container: $g('.website-container').val().trim(),
                favicon: $g('input.favicon').val().trim(),
                header_code: $g('textarea.header-code').val().trim(),
                body_code: $g('textarea.body-code').val().trim(),
                date_format: $g('.ba-custom-date-format input[type="text"]').val(),
                disable_responsive: Number($g('.disable-responsive').prop('checked')),
                compress_html: Number($g('.compress-html').prop('checked')),
                compress_css: Number($g('.compress-css').prop('checked')),
                compress_js: Number($g('.compress-js').prop('checked')),
                defer_loading: Number($g('.deferred-loading').prop('checked')),
                compress_images: Number($g('.images-compression').prop('checked')),
                compress_images_webp: Number($g('.images-compression-webp').prop('checked')),
                adaptive_images: Number($g('.adaptive-images').prop('checked')),
                adaptive_quality: $g('.adaptive-quality').val(),
                adaptive_images_webp: Number($g('.images-adaptive-webp').prop('checked')),
                images_lazy_load: Number($g('.images-lazy-load').prop('checked')),
                images_max_size: $g('.images-max-size').val(),
                images_quality: $g('.images-quality').val(),
                page_cache: Number($g('.page-cache').prop('checked')),
                browser_cache: Number($g('.browser-cache').prop('checked')),
                preloader: Number($g('.site-preloader').prop('checked')),
                enable_canonical: Number($g('.enable-canonical').prop('checked')),
                canonical_domain: $g('.canonical-domain').val().trim(),
                canonical_slash: Number($g('.canonical-trailing-slash').prop('checked')),
                enable_sitemap: Number($g('.enable-sitemap').prop('checked')),
                sitemap_domain: $g('.sitemap-domain').val().trim(),
                image_path: $g('.website-image-path').val(),
                file_types: $g('.website-file-types').val(),
                email_encryption: Number($g('.website-email-encryption').prop('checked')),
                sitemap_slash: Number($g('.sitemap-trailing-slash').prop('checked')),
                upload_compress_images: Number($g('.upload-images-compression').prop('checked')),
                upload_images_size: $g('.upload-images-size').val(),
                upload_images_quality: $g('.upload-images-quality').val(),
                upload_images_webp: Number($g('.upload-images-webp').prop('checked')),
                google_analytics: Number($g('.google-analytics').prop('checked')),
                google_analytics_id: $g('.google-analytics-id').val(),
                google_gtm_id: $g('.google-gtm-id').val(),
                ecommerce_tracking: Number($g('.ecommerce-tracking').prop('checked')),
                yandex_metrica: Number($g('.yandex-metrica').prop('checked')),
                yandex_tag_number: $g('.yandex-tag-number').val(),
                facebook_pixel: Number($g('.facebook-pixel').prop('checked')),
                facebook_pixel_id: $g('.facebook-pixel-id').val(),
                google_fonts: Number(!$g('.google-fonts').prop('checked')),
                versions_auto_save: Number($g('.versions-auto-save').prop('checked')),
                max_versions: $g('.max-versions').val(),
            },
            code: {
                css: app.editor.document.getElementById('code-css-value').value,
                js: app.editor.document.getElementById('code-js-value').value
            }
        }
    if (!obj.website.date_format) {
        obj.website.date_format = 'j F Y';
    }
    obj.breakpoints.menuBreakpoint = app.editor.menuBreakpoint;
    if (!app.editor.themeData.edit_type) {
        obj.page = {
            style : $g.extend({}, app.editor.app.items),
            id : grid.value,
            theme : theme,
            title : $g('#settings-dialog .page-title').val().trim(),
            page_alias : $g('#settings-dialog .page-alias').val().trim(),
            page_access : $g('#settings-dialog .access-select input[type="hidden"]').val(),
            created : $g('#settings-dialog .published_on').val(),
            end_publishing : $g('#settings-dialog .published_down').val(),
            language : $g('#settings-dialog .language-select input[type="hidden"]').val(),
            intro_image : $g('#settings-dialog .intro-image').attr('data-value'),
            intro_text : $g('#settings-dialog .intro-text').val(),
            meta_title : $g('#settings-dialog .page-meta-title').val().trim(),
            meta_description : $g('#settings-dialog .page-meta-description').val().trim(),
            meta_keywords : $g('#settings-dialog .page-meta-keywords').val().trim(),
            page_category : $g('#settings-dialog .page-category').val(),
            page_categories : $g('#settings-dialog input[name="page_categories"]').val(),
            robots : $g('#settings-dialog .robots-select input[type="hidden"]').val(),
            class_suffix : $g('#settings-dialog .page-class-suffix').val(),
            author: $g('#settings-dialog .select-post-author input[name="author"]').val(),
            share_image : $g('#settings-dialog .share-image').val(),
            share_title : $g('#settings-dialog .share-title').val(),
            share_description : $g('#settings-dialog .share-description').val(),
            sitemap_override: Number($g('#settings-dialog .sitemap-override').prop('checked')),
            sitemap_include: Number($g('#settings-dialog .sitemap-include').prop('checked')),
            changefreq : $g('#settings-dialog .changefreq').val(),
            priority : $g('#settings-dialog .priority').val(),
            schema_markup : $g('#settings-dialog .schema-markup').val(),
            published : Number($g('#settings-dialog input[name="published"]').prop('checked')),
            meta_tags : []
        };
        if (!obj.page.title) {
            $g('.page-title.page-settings-input-trigger').addClass('ba-alert-input');
            button.dataset.action = 'enabled';
            app.showNotice(gridboxLanguage['COMPLETE_REQUIRED_FIELDS'], 'ba-alert');
            return false;
        }
        if (!obj.page.end_publishing) {
            obj.page.end_publishing = '0000-00-00 00:00:00';
        }
        $g('#settings-dialog .meta_tags option').each(function(){
            if (this.value.indexOf('new$') != -1) {
                button.flags.tags = true;
            }
            obj.page.meta_tags.push(this.value);
        });
        obj.fields = {};
        obj.fieldsGroups = {};
        $g('#blog-post-editor-fields-options .ba-fields-group-wrapper').each(function(){
            let fieldsGroup = obj.fieldsGroups[this.id] = {
                title: this.querySelector('.ba-fields-group-title input').value,
                fields: []
            }
            $g(this).find('.blog-post-editor-options-group[data-field-type]').each(function(){
                fieldsGroup.fields.push(this.dataset.fieldKey);
            });
        });
        $g('#blog-post-editor-fields-options .ba-fields-group-wrapper').not('#ba-group-product-pricing')
            .not('#ba-group-digital-product').not('#ba-group-product-variations')
            .not('#ba-group-subscription-product').not('#ba-group-subscription-renewal')
            .find('.blog-post-editor-options-group[data-field-type]').each(function(){
            var field = {
                    field_id: null,
                    type: this.dataset.fieldType
                };
            switch (this.dataset.fieldType) {
                case 'text':
                case 'date':
                case 'event-date':
                case 'number':
                case 'range':
                case 'price':
                    var input = $g(this).find('input[name]')[0];
                    field.field_id = input.name;
                    field.value = input.value.trim();
                    break;
                case 'file':
                    var input = $g(this).find('input[name]')[0];
                    field.field_id = input.name;
                    field.value = input.dataset.value;
                    break;
                case 'textarea':
                    var input = $g(this).find('textarea[name="'+this.dataset.id+'"]')[0];
                    field.field_id = input.name;
                    if (input.dataset.texteditor && !input.dataset.jce) {
                        field.value = app.fieldsCKE[input.name].getData();
                    } else if (input.dataset.texteditor && input.dataset.jce) {
                        field.value = WFEditor.getContent('editor'+input.dataset.jce);
                    } else {
                        field.value = input.value.trim();
                    }
                    break;
                case 'select':
                    var input = $g(this).find('select[name]')[0];
                    field.field_id = input.name;
                    field.value = input.value;
                    break;
                case 'radio':
                    $g(this).find('input[type="radio"][name]').each(function(){
                        if (!('value' in field)) {
                            field.value = '';
                        }
                        if (this.checked) {
                            field.field_id = this.name;
                            field.value = this.value;
                        }
                    });
                    break;
                case 'checkbox':
                    $g(this).find('input[type="checkbox"][name]').each(function(){
                        field.field_id = this.name;
                        if (!('value' in field)) {
                            field.value = [];
                        }
                        if (this.checked) {
                            field.value.push(this.value);
                        }
                    });
                    break;
                case 'url':
                case 'field-button':
                    $g(this).find('input[type="text"][name]').each(function(){
                        field.field_id = this.name;
                        if (!field.value) {
                            field.value = {
                                label: '',
                                link: ''
                            };
                        }
                        field.value[this.dataset.name] = this.value;
                    });
                    break;
                case 'image-field':
                    $g(this).find('input[type="text"][name]').each(function(){
                        field.field_id = this.name;
                        if (!field.value) {
                            field.value = {};
                        }
                        if (this.dataset.name == 'src') {
                            field.value[this.dataset.name] = this.dataset.value;
                        } else {
                            field.value[this.dataset.name] = this.value;
                        }
                    });
                    break;
                case 'tag':
                    field.field_id = this.querySelector('.meta-tags').dataset.name;
                    if (obj.page.meta_tags.length > 0) {
                        field.value = 'value';
                    } else {
                        field.value = '';
                    }
                    break;
                case 'field-simple-gallery':
                case 'product-gallery':
                case 'field-slideshow':
                case 'product-slideshow':
                    field.field_id = this.dataset.id;
                    var value = [];
                    $g(this).find('.sorting-item').each(function(){
                        let obj = {
                            img: this.dataset.img,
                            alt: this.dataset.alt,
                            unpublish: this.dataset.unpublish == '1'
                        }
                        value.push(obj);
                    });
                    field.value = JSON.stringify(value);
                    break;
                case 'field-google-maps':
                    field.field_id = this.querySelector('input[data-autocomplete][name]').name;
                    var map = {
                        center: {
                            lat: app.fieldMaps[field.field_id].map.center.lat(),
                            lng: app.fieldMaps[field.field_id].map.center.lng()
                        },
                        zoom: app.fieldMaps[field.field_id].map.getZoom(),
                        marker: {
                            place: app.fieldMaps[field.field_id].input.value
                        }
                    }
                    if (app.fieldMaps[field.field_id].marker) {
                        map.marker.position = {
                            lat: app.fieldMaps[field.field_id].marker.position.lat(),
                            lng: app.fieldMaps[field.field_id].marker.position.lng()
                        }
                    }
                    field.value = JSON.stringify(map);
                    break;
                case 'field-video':
                    field.field_id = this.dataset.id;
                    var value = {};
                    $g(this).find('[name][data-name]').each(function(){
                        if (this.dataset.name == 'file') {
                            value[this.dataset.name] = this.dataset.value;
                        } else {
                            value[this.dataset.name] = this.value;
                        }
                    });
                    if (value.id || value.file) {
                        field.value = JSON.stringify(value);
                    } else {
                        field.value = '';
                    }
                    break;
                case 'time':
                    field.field_id = this.dataset.id;
                    var value = {};
                    $g(this).find('select[data-name]').each(function(){
                        value[this.dataset.name] = this.value;
                    });
                    if (this.hasAttribute('data-required') && (value.hours == '' || value.minuts == '')) {
                        field.value = '';
                    } else {
                        field.value = JSON.stringify(value);
                    }
                    break;
            }
            if (field.field_id) {
                obj.fields[field.field_id] = field;
            }
            if (this.hasAttribute('data-required')) {
                if ((field.type == 'url' && (!field.value.link || !field.value.label))
                    || (field.type == 'field-button' && !field.value.link)
                    || (field.type == 'image-field' && !field.value.src)
                    || ((field.type == 'field-simple-gallery' || field.type == 'product-gallery' || field.type == 'field-slideshow'
                        || field.type == 'product-slideshow') && field.value == '[]')
                    || !field.value) {
                    this.classList.add('ba-alert-label');
                    obj.fieldsAlert = true;
                }
            }
        });
        if (document.querySelector('#ba-group-product-pricing')) {
            obj.product = {
                data: {
                    product_id: obj.page.id,
                    stock: '',
                    sku: '',
                    min: ''
                },
                variations: {},
                extra_options: {},
                dimensions:{},
                variations_map: {},
                badges:{},
                booking:{},
                related:{}
            }
            document.querySelectorAll('#ba-group-related-product').forEach(function(related){
                related.querySelectorAll('.field-sorting-wrapper.related-product .selected-items').forEach(function($this, i){
                    obj.product.related[$this.dataset.id] = {
                        product_id: obj.page.id,
                        related_id: $this.dataset.id,
                        order_list: i
                    };
                })
            });
            document.querySelectorAll('#ba-group-product-booking').forEach((div) => {
                let booking = {
                        multiple: {},
                        single: {},
                        payment: {
                            type: document.querySelector('[data-field-key="booking_payment"] select[name="type"]').value,
                            unit: document.querySelector('[data-field-key="booking_payment"] select[name="unit"]').value,
                            value: document.querySelector('[data-field-key="booking_payment"] input[name="value"]').value
                        }
                    },
                    element = 'select';
                div.querySelectorAll('.blog-post-editor-options-group').forEach((group) => {
                    element = group.dataset.fieldType == 'text' ? 'input' : 'select';
                    if (group.dataset.fieldKey == 'booking_type') {
                        booking.type = group.querySelector(element).value;
                    } else if (group.dataset.fieldKey == 'min' || group.dataset.fieldKey == 'max') {
                        booking.multiple[group.dataset.fieldKey] = group.querySelector(element).value;
                    } else if (group.dataset.fieldKey == 'booking-hours' && booking.single.availability == 'custom') {
                        booking.single.hours = {}
                        group.querySelectorAll('.booking-working-hours-group[data-day]').forEach((working) => {
                            let hours = [];
                            working.querySelectorAll('.booking-working-hours-element').forEach((element) => {
                                hours.push({
                                    start: element.querySelector('select[data-option="start"]').value,
                                    end: element.querySelector('select[data-option="end"]').value
                                });
                            });
                            booking.single.hours[working.dataset.day] = {
                                enable: working.querySelector('input[type="checkbox"]').checked,
                                hours: hours
                            }
                        });
                    } else {
                        booking.single[group.dataset.fieldKey] = group.querySelector(element).value;
                    }
                });
                obj.product.booking = booking;
            });
            document.querySelectorAll('#ba-group-product-pricing').forEach(function(pricing){
                pricing.querySelectorAll('.blog-post-editor-options-group.product-data').forEach(function($this){
                    let input = $this.querySelector('input'),
                        key = $this.dataset.fieldKey;
                    obj.product.data[key] = input.type == 'checkbox' ? Number(input.checked) : input.value;
                    obj.product.data.id = $this.dataset.id;
                    if (key == 'price' && obj.product.data[key] === '') {
                        $this.classList.add('ba-alert-label');
                        obj.fieldsAlert = true;
                    }
                });
                pricing.querySelectorAll('.field-sorting-wrapper.product-badges .selected-items').forEach(function($this, i){
                    obj.product.badges[$this.dataset.id] = {
                        i: i
                    };
                });
                pricing.querySelectorAll('.blog-post-editor-options-group[data-id="dimensions"] input').forEach(function($this){
                    obj.product.dimensions[$this.name] = $this.value;
                });
            });
            document.querySelectorAll('#ba-group-subscription-product').forEach(function(subscription){
                let types = ['subscription-length', 'subscription-action', 'subscription-products',
                        'subscription-groups', 'subscription-remove', 'subscription-renewal-remind',
                        'subscription-renewal-plans', 'upgrade-plans'],
                    object = {
                        length: {},
                        action: '',
                        products: [],
                        groups: [],
                        remove: false,
                        renew: {
                            plans: {},
                            remind: {}
                        },
                        upgrade: []
                    };
                types.forEach(function(type){
                    $this = document.querySelector('.blog-post-editor-options-group[data-field-type="'+type+'"]');
                    if (type == 'subscription-length') {
                        object.length.value = $this.querySelector('input').value;
                        object.length.format = $this.querySelector('select').value;
                    } else if (type == 'subscription-action') {
                        object.action = $this.querySelector('select').value;
                    } else if (type == 'subscription-products') {
                        $this.querySelectorAll('.selected-items').forEach(function(item){
                            object.products.push(item.dataset.id);
                        });
                    } else if (type == 'upgrade-plans') {
                        $this.querySelectorAll('.selected-items').forEach(function(item){
                            object.upgrade.push(item.dataset.id);
                        });
                    } else if (type == 'subscription-groups') {
                        $this.querySelectorAll('.selected-items').forEach(function(item){
                            object.groups.push(item.dataset.id);
                        });
                    } else if (type == 'subscription-remove') {
                        object.remove = $this.querySelector('input').checked;
                    } else if (type == 'subscription-renewal-plans') {
                        $this.querySelectorAll('.renewal-plan').forEach(function(div){
                            let plan = {
                                length: {
                                    value: div.querySelector('.renewal-plan-length input').value,
                                    format: div.querySelector('.renewal-plan-length select').value
                                },
                                price: div.querySelector('.renewal-plan-price input').value
                            }
                            object.renew.plans[div.dataset.key] = plan;
                        });
                    } else if (type == 'subscription-renewal-remind') {
                        object.renew.remind.format = $this.querySelector('select').value;
                        object.renew.remind.value = $this.querySelector('input').value;
                    }
                });                obj.product.data.subscription = JSON.stringify(object);
            });
            document.querySelectorAll('#ba-group-digital-product').forEach(function(digital){
                let object = {};
                digital.querySelectorAll('.blog-post-editor-options-group').forEach(function($this){
                    if ($this.dataset.fieldType == 'digital-product-file') {
                        let input = $this.querySelector('input');
                        object.file = {
                            type: input.dataset.type,
                            name: input.value,
                            filename: input.dataset.value
                        }
                    } else if ($this.dataset.fieldType == 'digital-link-expires') {
                        object.expires = {}
                        $this.querySelectorAll('input, select').forEach(function(input){
                            object.expires[input.localName == 'select' ? 'format' : 'value'] = input.value;
                        });
                    } else {
                        object.max = $this.querySelector('input').value;
                    }
                    if (('required' in $this.dataset) && $this.dataset.fieldType == 'digital-product-file'
                        && !object.file.name) {
                        $this.classList.add('ba-alert-label');
                        obj.fieldsAlert = true;
                    }
                });
                obj.product.data.digital_file = JSON.stringify(object);
            });
            document.querySelectorAll('#ba-group-product-variations').forEach(function(variations){
                variations.querySelectorAll('.variations-table-body .variations-table-row').forEach(function($this){
                    obj.product.variations[$this.dataset.key] = $g.extend(true, {}, app.productVariations[$this.dataset.key]);
                });
                variations.querySelectorAll('div[data-field-type="product-options"] .sorting-item').forEach(function($this, order){
                    $this.querySelectorAll('.selected-items').forEach(function(selected, i){
                        obj.product.variations_map[selected.dataset.key] = {
                            id: selected.dataset.id,
                            product_id: obj.page.id,
                            field_id: $this.dataset.id,
                            option_key: selected.dataset.key,
                            images: JSON.stringify(app.productImages[selected.dataset.key]),
                            order_list: i,
                            order_group: order
                        }
                        if (selected.dataset.id == 0) {
                            button.flags.products = true;
                        }
                    });
                });
                variations.querySelectorAll('.product-extra-options .sorting-item').forEach(function($this, i){
                    obj.product.extra_options[i] = {
                        id: $this.dataset.id,
                        items: {}
                    };
                    let field_type = app.productOptions[$this.dataset.id].field_type,
                        isFile = field_type == 'file' || field_type == 'textarea' || field_type == 'textinput';
                    $this.querySelectorAll('.extra-product-options-row[data-key]').forEach(function(row){
                        obj.product.extra_options[i].items[row.dataset.key] = {
                            price: row.querySelector('.extra-product-option-price input').value,
                            weight: isFile ? '' : row.querySelector('.extra-product-option-weight input').value,
                            default: isFile ? false : Boolean(row.querySelector('.extra-product-option-default i').dataset.default * 1),
                        }
                    });
                });
            });
        }
        if (obj.fieldsAlert) {
            button.dataset.action = 'enabled';
            app.showNotice(gridboxLanguage['COMPLETE_REQUIRED_FIELDS'], 'ba-alert');
            return false;
        }
    } else if (app.editor.themeData.edit_type == 'system') {
        let options = {
            suffix : $g('#settings-dialog .page-class-suffix').val()
        };
        if (app.editor.systemType == '404') {
            options.enable_header = $g('#settings-dialog .page-enable-header').prop('checked');
        } else if (app.editor.systemType == 'submission-form') {
            options.premoderation = $g('#settings-dialog .submission-form-moderation').prop('checked');
            options.author = $g('#settings-dialog .submission-form-author').prop('checked');
            options.access = $g('#settings-dialog .submission-form-access input[type="hidden"]').val();
            options.emails = $g('#settings-dialog .submission-form-notifications').prop('checked');
            options.submited_email = $g('#settings-dialog .submission-form-submited').prop('checked');
            options.published_email = $g('#settings-dialog .submission-form-publishing').prop('checked');
        }
        obj.page = {
            style: $g.extend({}, app.editor.app.items),
            theme: theme,
            type: app.editor.systemType,
            id: grid.value,
            title: $g('#settings-dialog .page-title').val().trim(),
            language: $g('#settings-dialog .language-select input[type="hidden"]').val(),
            page_options: JSON.stringify(options)
        };
        $g('#settings-dialog .page-alias').each(function(){
            obj.page.alias = this.value.trim();
        })
        obj.edit_type = app.editor.themeData.edit_type;
        if (app.editor.systemType == 'checkout') {
            obj.page.customer = obj.page.style['item-15289771305'].items;
        }
    } else if (app.editor.themeData.edit_type == 'post-layout') {
        obj.page = {
            style : $g.extend({}, app.editor.app.items),
            id : grid.value,
            theme : theme
        };
        app.getItemBlogContentStyle();
        obj.page.post_editor_wrapper = app.post_editor_wrapper;
        obj.edit_type = app.editor.themeData.edit_type;
    } else {
        obj.page = {
            style : $g.extend({}, app.editor.app.items),
            id : grid.value,
            theme : theme
        };
        obj.edit_type = app.editor.themeData.edit_type;
    }
    if (app.editor.app.currencySwitcher) {
        app.editor.app.currencySwitcher.hide(0);
    }
    if (app.editor.app.languageSwitcher) {
        app.editor.app.languageSwitcher.hide(0);
    }
    if (app.editor.document.querySelector('header.header')) {
        prepareHTML('header.header', obj, obj.page.style);
        prepareHTML('footer.footer', obj, obj.page.style);
    } else {
        delete(obj.theme.header)
        delete(obj.theme.footer)
    }
    prepareHTML('#ba-edit-section', obj, obj.page.style);
    obj.page.params = obj.theme['#ba-edit-section'].html;
    obj.page.style = $g.extend(true, {}, obj.theme['#ba-edit-section'].items);
    for (let ind in obj.page.style) {
        if (obj.page.style[ind].type == 'google-maps-places') {
            delete(obj.page.style[ind].events);
            delete(obj.page.style[ind].marker);
        }
    }
    delete(obj.theme['#ba-edit-section']);
    $g('.create-new-page').each(function(){
        if (this.href.indexOf('&category=') != -1) {
            var array = this.href.split('&');
            for (var i = 0; i < array.length; i++) {
                if (array[i].indexOf('category=') != -1) {
                    array[i] = 'category='+obj.page.page_category;
                    break;
                }
            }
            this.href = array.join('&');
        }
    });
    if (!app.editor.themeData.edit_type || app.editor.themeData.edit_type == 'system') {
        app.associations.save();
    }
    if (app.saveMode == 'version') {
        obj = {
            page_id: obj.page.id,
            items: obj.page.style,
            html: obj.page.params
        }
    }
    //sendAjaxSave(obj, button);
    sendXHRSave(obj, button);
    app.editor.app.checkModule('checkOverlay');
    app.editor.$g('.ba-item-in-positioning, .ba-item-reading-progress-bar').each(function(){
        app.editor.document.body.append(this);
    });
}

function sendXHRSave(obj, button)
{
    let XHR = new XMLHttpRequest(),
        url = JUri+'index.php?option=com_gridbox&task=editor.gridboxSave',
        loaded = false;
    if (app.saveMode == 'version') {
        url = JUri+'index.php?option=com_gridbox&task=editor.gridboxSaveVersion';
    }
    XHR.onreadystatechange = function(e){
        if (XHR.readyState == 4) {
            loaded = true;
            if (XHR.status == 200) {
                afterSaveAction(obj, button, XHR.responseText);
            } else {
                console.info(XHR.responseText);
                sendAjaxSave(obj, button);
            }
        }
    };
    XHR.open("POST", url, true);
    XHR.send(JSON.stringify(obj));
    setTimeout(() => {
        if (!loaded) {
            XHR.abort();
            sendAjaxSave(obj, button);
        }
    }, 7000)
}

function sendAjaxSave(obj, button)
{
    let url = JUri+'index.php?option=com_gridbox&task=editor.gridboxAjaxSave';
    if (app.saveMode == 'version') {
        url = JUri+'index.php?option=com_gridbox&task=editor.gridboxAjaxSaveVersion';
    }
    $g.ajax({
        type:"POST",
        dataType:'text',
        url: url,
        data: {
            obj: JSON.stringify(obj)
        },
        success:function(response){
            afterSaveAction(obj, button, response);
        },
        error: function(response){
            console.info(response.responseText);
            //sendXHRSave(obj, button);
        }
    });
}

function afterSaveMessage(obj, button, text)
{
    button.dataset.action = 'enabled';
    $g(button).trigger('saved');
    app.showNotice(text);
    if (app.saveMode == 'preview') {
        window.open(document.querySelector('.page-link a').href, '_blank');
    } else if (app.saveMode == 'close') {
        window.location.href = document.querySelector('.save-as-context-menu [data-action="close"]').dataset.link;
    } else if (app.saveMode == 'new') {
        window.location.href = document.querySelector('.page-context-menu a.create-new-page').href;
    } else if (app.saveMode == 'copy' && !obj.edit_type) {
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.duplicate', {
            id: obj.page.id
        }).then((id) => {
            let params = new URLSearchParams(window.location.search);
            params.set('id', id);
            window.location.search = params.toString();
        });
    } else if ((app.saveMode == 'version' || obj.website.versions_auto_save === 1) && app.versions && app.versions.modal.hasClass('in')) {
        app.versions.get();
    }
}

function afterSaveAction(obj, button, text)
{
    if (app.saveMode == 'version' || (!button.flags.tags && !button.flags.products)) {
        afterSaveMessage(obj, button, text)
    } else if (button.flags.tags) {
        $g.ajax({
            type:"GET",
            dataType:'text',
            url:JUri+"index.php?option=com_gridbox&task=editor.getPageTags",
            data : {
                id : obj.page.id
            },
            success: function(msg){
                let array = JSON.parse(msg),
                    str = '';
                array.tags.forEach((tag) => {
                    str += '<div class="ba-settings-item ba-settings-input-type" data-folder="'+tag.folder_id+'"'+
                        'data-id="'+tag.id+'"><i class="zmdi zmdi-label"></i><span class="ba-settings-item-title">'+tag.title+'</span></div>';
                });
                $g('.post-tags-wrapper').html(str);
                str = '';
                array.page.forEach((tag) => {
                    str += '<li class="tags-chosen"><span>'+tag.title+'</span><i class="zmdi zmdi-close" data-remove="'+tag.id+'"></i></li>';
                });
                $g('.meta-tags .picked-tags').html(str);
                str = '';
                array.page.forEach((tag) => {
                    str += '<option value="'+tag.id+'" selected="">'+tag.title+'</option>';
                });
                $g('.meta-tags select').html(str);
                afterSaveMessage(obj, button, text)
            }
        });
    } else if (button.flags.products) {
        $g.ajax({
            type:"GET",
            dataType:'text',
            url:JUri+"index.php?option=com_gridbox&task=editor.getProductData",
            data : {
                id : obj.page.id
            },
            success: function(msg){
                let obj = JSON.parse(msg)
                    variations = document.querySelector('#ba-group-product-variations');
                obj.variations_map.forEach(function($this){
                    variations.querySelector('.selected-items[data-key="'+$this.option_key+'"]').dataset.id = $this.id;
                });
                afterSaveMessage(obj, button, text)
            }
        });
    }
}

app.modules.gridboxSave = true;
app.gridboxSave();