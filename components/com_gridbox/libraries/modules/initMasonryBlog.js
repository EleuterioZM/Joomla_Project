/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function setPostMasonryHeight(key)
{
    var computed = null,
        reviews = document.getElementById(key).classList.contains('ba-item-recent-reviews'),
        gap = 20,
        height = 0;
    $g('#'+key).find('.ba-blog-posts-wrapper, .ba-categories-wrapper').not('.ba-masonry-layout').find('.ba-blog-post').each(function(){
        this.classList.remove('ba-masonry-image-loaded');
        this.style.gridRowEnd = '';
    });
    $g('#'+key+' .ba-masonry-layout .empty-list').each(function(){
        this.closest('.ba-masonry-layout').classList.add('empty-masonry-wrapper');
    })
    $g('#'+key+' .ba-masonry-layout .ba-blog-post').each(function(){
        var post = this,
            offsetHeight = post.querySelector('.ba-blog-post-content').offsetHeight,
            $this = this.querySelector('.ba-blog-post-image img'),
            img = document.createElement('img');
        if (!computed) {
            computed = getComputedStyle(this)
        }
        offsetHeight += (computed.paddingBottom.replace(/[^\d\.]/g, '') * 1)+(computed.paddingTop.replace(/[^\d\.]/g, '') * 1);
        offsetHeight += (computed.borderBottomWidth.replace(/[^\d\.]/g, '') * 1)+(computed.borderTopWidth.replace(/[^\d\.]/g, '') * 1);
        if (!$this || reviews) {
            post.style.gridRowEnd = "span "+Math.ceil(((offsetHeight + gap) / (height + gap)) + 0);
            if (!post.classList.contains('ba-masonry-image-loaded')) {
                post.classList.add('ba-masonry-image-loaded');
            }
        } else if ($this.src.indexOf('default-lazy-load.webp') != -1) {
            $this.onload = function(){
                offsetHeight += $this.offsetHeight;
                post.style.gridRowEnd = "span "+Math.ceil(((offsetHeight + gap) / (height + gap)) + 0);
                if (!post.classList.contains('ba-masonry-image-loaded')) {
                    post.classList.add('ba-masonry-image-loaded');
                }
            }
        } else {
            img.onload = function(){
                offsetHeight += $this.offsetHeight;
                post.style.gridRowEnd = "span "+Math.ceil(((offsetHeight + gap) / (height + gap)) + 0);
                if (!post.classList.contains('ba-masonry-image-loaded')) {
                    post.classList.add('ba-masonry-image-loaded');
                }
            }
            img.src = $this.src;
        }
        this.closest('.ba-masonry-layout').classList.remove('empty-masonry-wrapper');
    });
}

app.initMasonryBlog = function(obj, key){
    setPostMasonryHeight(key);
    $g('#'+key).off('mouseover.options').on('mouseover.options', '.ba-blog-post-product-option', function(event){
        let search = 'ba-blog-post-product-option',
            t1 = event.target ? event.target.closest('.'+search) : null,
            t2 = event.relatedTarget ? event.relatedTarget.closest('.'+search) : null;
        if (t1 != t2) {
            let post = this.closest('.ba-blog-post');
            if (this.dataset.image) {
                let image = !app.isExternal(this.dataset.image) ? JUri+this.dataset.image : this.dataset.image;
                post.style.setProperty('--product-option-image', 'url('+image+')');
            } else {
                post.style.setProperty('--product-option-image', '');
            }
            post.classList.add('product-option-hovered');
        }
    }).off('mouseout.options').on('mouseout.options', '.ba-blog-post-product-option', function(event){
        let search = 'ba-blog-post-product-option',
            t1 = event.target ? event.target.closest('.'+search) : null,
            t2 = event.relatedTarget ? event.relatedTarget.closest('.'+search) : null;
        if (t1 != t2 && (!t2 || !t2.classList.contains(search))) {
            let post = this.closest('.ba-blog-post');
            post.classList.remove('product-option-hovered');
            post.style.setProperty('--product-option-image', '');
        }
    }).off('click.wishlist').on('click.wishlist', '.ba-blog-post-wishlist-wrapper', function(){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        let post = this.closest('.ba-blog-post')
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.addPostToWishlist', {
            id: post.dataset.id
        }).then(function(text){
            let response = JSON.parse(text),
                str = '';
            if (response.status) {
                if (response.data.images.length) {
                    response.data.image = response.data.images[0];
                }
                if (response.data.image && !app.isExternal(response.data.image)) {
                    response.data.image = JUri+response.data.image;
                }
                str = '<span class="ba-product-notice-message">';
                if (response.data.image) {
                    str += '<span class="ba-product-notice-image-wrapper"><img src="'+response.data.image+'"></span>';
                }
                str += '<span class="ba-product-notice-text-wrapper">'+response.data.title+
                    ' '+gridboxLanguage['ADDED_TO_WISHLIST']+'</span></span>';
                app.showNotice(str, 'ba-product-notice');
                if (app.wishlist) {
                    app.wishlist.updateWishlist();
                }
            } else if (!response.status && response.message) {
                app.showNotice(response.message, 'ba-alert');
            } else {
                localStorage.setItem('select-options', gridboxLanguage['PLEASE_SELECT_OPTION']);
                post.querySelector('.ba-blog-post-title a').click();
            }
        });
    }).off('click.cart').on('click.cart', '.ba-blog-post-add-to-cart', function(){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        let post = this.closest('.ba-blog-post')
        app.fetch(JUri+'index.php?option=com_gridbox&task=store.addPostToCart', {
            id: post.dataset.id
        }).then(function(text){
            let response = JSON.parse(text);
            if (response.status) {
                if (app.storeCart) {
                    app.storeCart.updateCartTotal();
                    $g('.ba-item-cart a').first().trigger('click');
                }
            } else {
                localStorage.setItem('select-options', gridboxLanguage['PLEASE_SELECT_OPTION']);
                post.querySelector('.ba-blog-post-title a').click();
            }
        });
    }).on('change', '.blog-posts-sorting', function(){
        if (themeData.page.view == 'gridbox') {
            return false;
        }
        window.location.href = this.dataset.url+this.value;
    });
    if (obj.type == 'recent-posts' && themeData.page.view != 'gridbox') {
        $g('#'+key).off('click.pagination').on('click.pagination', '.ba-blog-posts-pagination a', function(event){
            event.preventDefault();
            if (!this.dataset.clicked) {
                this.dataset.clicked = true;
                let array = [],
                    match = this.href.match(/page=\d+/),
                    page = match[0].match(/\d+/),
                    notId = [],
                    notStr = '',
                    cats = tags = '';
                for (var ind in obj.categories) {
                    array.push(ind);
                }
                cats = array.join(',');
                array = [];
                if (obj.tags) {
                    for (let key in obj.tags) {
                        array.push(key);
                    }
                }
                tags = array.join(',');
                if (obj.sorting == 'random') {
                    $g('#'+key+' .ba-blog-post').each(function(){
                        notId.push(this.dataset.id);
                    });
                    notStr = notId.join(',');
                }
                if (obj.layout.pagination == 'load-more' || (obj.layout.pagination == 'load-more-infinity' && page[0] == 2)) {
                    $g('#'+key+' .ba-blog-posts-wrapper').addClass('ba-blog-posts-content-loading');
                }
                $g.ajax({
                    type: "POST",
                    dataType: 'text',
                    url: JUri+"index.php?option=com_gridbox&task=page.getRecentPosts",
                    data: {
                        id : obj.app,
                        limit : obj.limit,
                        sorting : obj.sorting,
                        category : cats,
                        tags: tags,
                        type: obj.posts_type ? obj.posts_type : '', 
                        maximum : obj.maximum,
                        featured: Number(obj.featured),
                        page: page[0],
                        pagination: obj.layout.pagination,
                        not: notStr,
                        item: JSON.stringify(obj)
                    },
                    complete: function(msg){
                        let object = JSON.parse(msg.responseText);
                        $g('#'+key+' .ba-blog-posts-pagination').remove();
                        $g('#'+key+' .ba-blog-posts-wrapper').append(object.posts).removeClass('ba-blog-posts-content-loading').after(object.pagination);
                        if (obj.tag != 'h3') {
                            $g('#'+key+' h3[class*="-title"]').each(function(){
                                var h = document.createElement(obj.tag);
                                h.className = this.className;
                                h.innerHTML = this.innerHTML;
                                $g(this).replaceWith(h);
                            });
                        }
                        setPostMasonryHeight(key);
                        $g('#'+key+' .ba-blog-post-button-wrapper a')
                            .text(obj.buttonLabel ? obj.buttonLabel : gridboxLanguage['READ_MORE']);
                        if (obj.layout.pagination == 'load-more-infinity' && page[0] == 2) {
                            $g(document).on('scroll.'+key, function(){
                                recentPostsInfinityAction(key);
                            });
                        }
                    }
                });
            }
        });
        if (obj.layout.pagination == 'infinity') {
            $g(document).on('scroll.'+key, function(){
                recentPostsInfinityAction(key);
            });
            recentPostsInfinityAction(key);
        }
    } else if ((obj.type == 'blog-posts' || obj.type == 'search-result' || obj.type == 'store-search-result') && themeData.page.view != 'gridbox') {
        let pagination = obj.pagination || obj.layout.pagination;
        $g('#'+key).off('click.pagination').on('click.pagination', '.ba-blog-posts-pagination a', function(event){
            if (!pagination) {
                return;
            }
            event.preventDefault();
            if (this.dataset.clicked || this.closest('.disabled')) {
                return;
            }
            let page = this.dataset.page,
                wrapper = $g('#'+key+' .ba-blog-posts-wrapper');
            if (pagination == 'load-more' || (pagination == 'load-more-infinity' && page == 2)) {
                wrapper.addClass('ba-blog-posts-content-loading');
            }
            this.dataset.clicked = true;
            fetch(this.href).then((response) => {
                return response.text();
            }).then((text) => {
                let div = document.createElement('div');
                div.innerHTML = text;
                div.querySelectorAll('#'+key+' .ba-blog-posts-wrapper .ba-blog-post').forEach((el) => {
                    if (wrapper.find('.ba-blog-post[data-id="'+el.dataset.id+'"]').length == 0) {
                        wrapper.append(el);
                    }
                });
                $g('style[data-id="adaptive-images"]').each(function(){
                    let item = div.querySelector('style[data-id="adaptive-images"]');
                    if (item) {
                        this.innerHTML = item.innerHTML;
                    }
                });
                $g('#'+key+' .ba-blog-posts-pagination')[0].innerHTML = div.querySelector('.ba-blog-posts-pagination').innerHTML;
                if (app.lazyLoad) {
                    app.lazyLoad.check();
                }
                setPostMasonryHeight(key);
                if (pagination == 'load-more-infinity' && page == 2) {
                    $g(document).on('scroll.'+key, function(){
                        recentPostsInfinityAction(key);
                    });
                }
                wrapper.removeClass('ba-blog-posts-content-loading');
            });
        });
        if (pagination == 'infinity') {
            $g(document).on('scroll.'+key, function(){
                recentPostsInfinityAction(key);
            });
            recentPostsInfinityAction(key);
        }
    }
    initItems();
}

if (app.modules.initMasonryBlog) {
    app.initMasonryBlog(app.modules.initMasonryBlog.data, app.modules.initMasonryBlog.selector);
}

function recentPostsInfinityAction(key)
{
    let scroll = window.scrollY + window.innerHeight,
        rect = document.querySelector('#'+key+' .ba-blog-posts-wrapper').getBoundingClientRect(),
        y = rect.bottom + window.scrollY,
        btn = document.querySelector('#'+key+' .ba-blog-posts-pagination a');
    if (y < scroll && btn && !btn.dataset.clicked) {
        btn.click();
    } else if (!btn) {
        $g(document).off('scroll.'+key);
    }
}