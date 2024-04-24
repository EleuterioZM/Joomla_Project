<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;
$type = gridboxHelper::checkCreatePage($this->app);
if (!empty($type) && $type != 'single' && empty($this->category)) {
    $categories = gridboxHelper::getAppCategories($this->app);
    foreach ($categories as $key => $category) {
        $categoryAssets = new gridboxAssetsHelper($category->id, 'category');
        $canCreate = $categoryAssets->checkPermission('core.create');
        if (!$canCreate) {
            unset($categories[$key]);
        }
    }
}
?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var notification = jQuery('#ba-notification'),
        JUri = '<?php echo JUri::root(); ?>',
        loginclk = true;

    function showNotice(message)
    {
        if (notification.hasClass('notification-in')) {
            setTimeout(function(){
                notification.removeClass('notification-in').addClass('animation-out');
                setTimeout(function(){
                    addNoticeText(message);
                }, 400);
            }, 2000);
        } else {
            addNoticeText(message);
        }
    }

    function addNoticeText(message)
    {
        notification.find('p').text(message);
        notification.removeClass('animation-out').addClass('notification-in');
        setTimeout(function(){
            notification.removeClass('notification-in').addClass('animation-out');
        }, 3000);
    }

    jQuery('.ba-custom-select').on('click', ' > i, input', function(event){
        event.stopPropagation();
        var $this = jQuery(this),
            parent = $this.parent();
        jQuery('.visible-select').removeClass('visible-select');
        parent.find('ul').addClass('visible-select');
        parent.find('li').one('click', function(){
            var text = jQuery.trim(jQuery(this).text()),
                val = jQuery(this).attr('data-value');
            parent.find('input[type="text"]').val(text);
            parent.find('input[type="hidden"]').val(val).trigger('change');
        });
        parent.trigger('show');
        setTimeout(function(){
            jQuery('body').one('click', function(){
                jQuery('.visible-select').parent().trigger('customHide');
                jQuery('.visible-select').removeClass('visible-select');
            });
        }, 50);
    });

    jQuery('div.ba-custom-select').on('show', function(){
        var $this = jQuery(this),
            ul = $this.find('ul'),
            value = $this.find('input[type="hidden"]').val();
        ul.find('i').remove();
        ul.find('.selected').removeClass('selected');
        ul.find('li[data-value="'+value+'"]').addClass('selected').prepend('<i class="zmdi zmdi-check"></i>');
    });

    if (jQuery('div.ba-login-dialog').length > 0) {
        jQuery('div.ba-create-page').hide();
        jQuery('.ba-login-dialog input').on('keydown', function(e){
            if (e.keyCode == 13) {
                login();
            }
        });
        jQuery('.login-button').on('click', function(event){
            event.preventDefault();
            login();
        });
    }

    function login()
    {
        if (loginclk) {
            loginclk = false;
            var login = jQuery('.ba-username').val(),
                pass = jQuery('.ba-password').val();
            jQuery.ajax({
                type : "POST",
                dataType : 'text',
                url : JUri+"index.php?option=com_gridbox&task=gridbox.login",
                data : {
                    ba_login : login,
                    ba_password : pass,
                },
                complete : function(msg){
                    if (msg.responseText) {
                        showNotice(msg.responseText);
                    } else if (jQuery('.ba-create-page').length > 0) {
                        jQuery('div.ba-login-dialog').addClass('ba-login-dialog-out');
                        fetch(window.location.href).then((response) => response.text()).then((data) => {
                            let div = document.createElement('div');
                            div.innerHTML = data;
                            div.querySelectorAll('.ba-custom-select.blog-category-select').forEach((select) => {
                                document.querySelector('.ba-custom-select.blog-category-select').innerHTML = select.innerHTML;
                            });
                        });
                        setTimeout(function(){
                            jQuery('div.ba-login-dialog').removeClass('ba-login-dialog-out').hide();
                            jQuery('div.ba-create-page').show();
                        }, 300);
                        
                    } else {
                        window.location.href = window.location.href;
                    }
                    loginclk = true;
                }
            });
        }
    }

    jQuery('.ba-create-page').each(function(){
        let $this = jQuery(this),
            inputs = $this.find('input[name]'),
            btn = this.querySelector('.create-button');
        this.validate = function(){
            let flag = true;
            this.querySelectorAll('input[name]').forEach(function(input){
                flag = flag && input.value.trim() != '';
            });
            btn.classList[flag ? 'add' : 'remove']('active-button');
        }
        $this.find('#ba-title').on('input', function(){
            clearTimeout(this.delay);
            this.delay = setTimeout(function(){
                $this[0].validate();
            }, 300);
        });
        inputs.on('change', function(){
            $this[0].validate();
        });
        btn.addEventListener('click', function(event){
            event.preventDefault();
            if (this.classList.contains('active-button') && this.dataset.clicked != 'true') {
                this.dataset.clicked = 'true';
                let data = {};
                document.querySelectorAll('#app_id').forEach(function(input){
                    data.app_id = input.value;
                });
                inputs.each(function(){
                    data[this.name] = this.value.trim();
                });
                jQuery.ajax({
                    type : "POST",
                    dataType : 'text',
                    url : JUri+"index.php?option=com_gridbox&task=gridbox."+this.dataset.action,
                    data : data,
                    complete : function(msg){
                        window.location.href = window.location.href+msg.responseText;
                    }
                });
            }
        })
    });
});
</script>
<div id="ba-notification" class="ba-alert">
    <i class="zmdi zmdi-close"></i>
    <h4><?php echo JText::_('ERROR'); ?></h4>
    <p></p>
</div>
<div id='login-modal' class='ba-modal-sm modal ba-modal-dialog in'>
    <div class='modal-body'>
<?php
if (!$this->editFlag) {
?>
        <div class="ba-login-dialog">
            <div class="ba-header-content">
                <h3 class='ba-modal-title'>
                    <?php echo JText::_('LOGIN'); ?>
                </h3>
                <label class="ba-help-icon">
                    <i class="zmdi zmdi-help"></i>
                    <span class="ba-tooltip ba-help">
                        <?php echo JText::_('LOGIN_TOOLTIP'); ?>
                    </span>
                </label>
            </div>
            <div class="ba-body-content">
                <div class="ba-input-lg">
                    <input class='ba-username reset-input-margin' type='text' placeholder="<?php echo JText::_('USERNAME'); ?>">
                    <span class="focus-underline"></span>
                </div>
                <div class="ba-input-lg">
                    <input class='ba-password' type='password' placeholder="<?php echo JText::_('PASSWORD'); ?>">
                    <span class="focus-underline"></span>
                </div>
            </div>
            <div class="ba-footer-content">
                <a href="#" class="ba-btn-primary login-button active-button">
                    <?php echo JText::_('NEXT'); ?>
                </a>
            </div>
        </div>
<?php
}
    if (empty($this->item->id) && $this->edit_type != 'system') {
?>
        <div class="ba-create-page">
            <div class="ba-header-content">
                <h3 class='ba-modal-title'>
                    <?php echo JText::_('NEW_PAGE'); ?>
                </h3>
            </div>
            <div class="ba-body-content">
                <form name="create_form" id="create_form" method='post'>
                    <div class="ba-input-lg">
                        <input class="reset-input-margin" name="title" type="text" id="ba-title"
                            placeholder="<?php echo JText::_('PAGE_TITLE'); ?>">
                        <span class="focus-underline"></span>
                    </div class="ba-input-lg">
<?php
                if (!empty($type) && $type != 'single' && empty($this->category)) {
?>
                    <div class="ba-custom-select blog-category-select">
                        <input class="reset-input-margin" readonly onfocus="this.blur()"
                            placeholder="<?php echo JText::_('CATEGORY') ?>" type="text">
                        <input type="hidden" id="category" name="category" value="">
                        <ul>
<?php
                            foreach ($categories as $category) {
                                $content = '';
                                for ($i = 0; $i < $category->level; $i++) {
                                    $content .= '- ';
                                }
                                if ($category->level != 0) {
                                    $content .= '-';
                                }
?>
                                <li data-value="<?php echo $category->id; ?>" style="--content: '<?php echo $content; ?>';">
<?php
                                    echo $category->title;
?>
                                </li>
<?php
                            }
?>
                        </ul>
                        <i class="zmdi zmdi-caret-down"></i>
                    </div>
<?php
                }
                if ($type == 'products' && empty($this->product_type)) {
?>
                    <div class="ba-custom-select select-product-type">
                        <input class="reset-input-margin" readonly onfocus="this.blur()"
                            placeholder="<?php echo JText::_('TYPE') ?>" type="text">
                        <input type="hidden" name="product_type" value="">
                        <ul>
                            <li data-value="physical"><?php echo JText::_('PHYSICAL'); ?></li>
                            <li data-value="digital"><?php echo JText::_('DIGITAL'); ?></li>
                            <li data-value="subscription"><?php echo JText::_('SUBSCRIPTION'); ?></li>
                        </ul>
                        <i class="zmdi zmdi-caret-down"></i>
                    </div>
<?php
                } else if ($type == 'products') {
?>
                    <input type="hidden" name="product_type" value="<?php echo $this->product_type; ?>">
<?php
                }
?>
                    <div class="ba-custom-select blog-theme-select">
                        <input readonly onfocus="this.blur()" value="<?php echo $this->themes->default->title; ?>" type="text">
                        <input type="hidden" name="page_theme" class="page-theme" value="<?php echo $this->themes->default->id; ?>">
                        <ul>
                            <?php
                            foreach ($this->themes->list as $theme) {
                                $str = '<li data-value="'.$theme->id.'">';
                                $str .= $theme->title.'</li>';
                                echo $str;
                            }
                            ?>
                        </ul>
                        <i class="zmdi zmdi-caret-down"></i>
                    </div>
                    <input type="hidden" id="app_id" value="<?php echo $this->app; ?>">
<?php
                    if (!empty($type) && $type != 'single' && !empty($this->category)) {
?>
                    <input type="hidden" id="category" name="category" value="<?php echo $this->category; ?>">
<?php
                    }
?>
                </form>
            </div>
            <div class="ba-footer-content">
                <a href="#" class="ba-btn-primary create-button disable-button" data-action="createPage">
                    <?php echo JText::_('NEXT'); ?>
                </a>
            </div>
        </div>
<?php 
    } else if (empty($this->item->id)) {
        $types = [
            '404' => JText::_('ERROR_PAGE'),
            'offline' => JText::_('COMING_SOON_PAGE'),
            'search' => JText::_('SEARCH_RESULTS_PAGE'),
            'preloader' => JText::_('PRELOADER'),
            'checkout' => JText::_('CHECKOUT_PAGE'),
            'thank-you-page' => JText::_('THANK_YOU_PAGE'),
            'store-search' => JText::_('STORE_SEARCH_RESULTS_PAGE'),
            'submission-form' => JText::_('SUBMISSION_FORM'),
        ];
?>
        <div class="ba-create-page">
            <div class="ba-header-content">
                <h3 class='ba-modal-title'>
                    <?php echo JText::_('SYSTEM_PAGE'); ?>
                </h3>
            </div>
            <div class="ba-body-content">
                <form name="create_form" id="create_form" method='post'>
                    <div class="ba-input-lg">
                        <input class="reset-input-margin" name="title" type="text" id="ba-title"
                            placeholder="<?php echo JText::_('PAGE_TITLE'); ?>">
                        <span class="focus-underline"></span>
                    </div class="ba-input-lg">
                    <div class="ba-custom-select system-type-select">
                        <input readonly class="reset-input-margin" onfocus="this.blur()"
                            value="<?php echo $types['404']; ?>" type="text">
                        <input type="hidden" name="page_type" class="page-type" value="404">
                        <ul>
<?php
                            foreach ($types as $key => $type) {
?>
                                <li data-value="<?php echo $key; ?>">
                                    <?php echo $type; ?>
                                </li>
<?php
                            }
?>
                        </ul>
                        <i class="zmdi zmdi-caret-down"></i>
                    </div>
                    <div class="ba-custom-select blog-theme-select">
                        <input readonly onfocus="this.blur()" value="<?php echo $this->themes->default->title; ?>" type="text">
                        <input type="hidden" name="page_theme" class="page-theme"
                            value="<?php echo $this->themes->default->id; ?>">
                        <ul>
<?php
                            foreach ($this->themes->list as $theme) {
?>
                                <li data-value="<?php echo $theme->id; ?>">
                                    <?php echo $theme->title; ?>
                                </li>
<?php
                            }
?>
                        </ul>
                        <i class="zmdi zmdi-caret-down"></i>
                    </div>
                </form>
            </div>
            <div class="ba-footer-content">
                <a href="#" class="ba-btn-primary create-button disable-button" data-action="createSystemPage">
                    <?php echo JText::_('NEXT'); ?>
                </a>
            </div>
        </div>
<?php
    }
?>
    </div>
</div>