/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initCategories = function(obj, key){
    let wrapper = $g('#'+key);
    wrapper.on('click', '.collapse-categories-list', function(){
        let sub = this.closest('.ba-app-sub-category-wrapper'),
            parent = sub ? sub : this.closest('.ba-blog-post-content'),
            h = parent.querySelector('.ba-app-sub-categories').scrollHeight;
        parent.style.setProperty('--categories-collapse-height', h+'px');
        parent.classList.remove('ba-categories-icon-rotated');
        if (parent.classList.contains('ba-categories-collapsed')) {
            parent.delay = setTimeout(function(){
                parent.classList.remove('ba-categories-collapsed');
                parent.style.setProperty('--categories-collapse-height', 'auto');
            }, 300);
        } else {
            clearTimeout(parent.delay);
            parent.classList.add('ba-categories-collapsed');
            parent.classList.add('ba-categories-icon-rotated');
            setTimeout(function(){
                parent.style.setProperty('--categories-collapse-height', 0);
            }, 50);
        }
    });
    initItems();
}

if (app.modules.initCategories) {
    app.initCategories(app.modules.initCategories.data, app.modules.initCategories.selector);
}