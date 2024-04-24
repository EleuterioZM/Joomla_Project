/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initLogin = function(obj, key){
    $g('#'+key).on('click', 'span[data-step]', function(){
        this.closest('div[data-wrapper]').style.display = 'none';
        $g('#'+key).find('div[data-wrapper="'+this.dataset.step+'"]').css('display', '');
    }).on('click', 'span[data-action]', function(){
        app.login.execute(this);
    }).on('change', '.ba-login-acceptance-wrapper input', function(){
        app.login.removeAlertTooltip(this);
    }).on('focus', '.ba-login-field-wrapper input', function(){
        app.login.removeAlertTooltip(this);
    }).on('click', '.ba-login-integration-btn[data-integration="facebook"]', function(e){
        app.login.redirect = this.closest('.ba-login-content-wrapper').dataset.redirect;
        if (obj.facebook.enable && window.FB) {
            FB.login(function(response){
                app.facebook.getUserInfo(response).then((data) => {
                    app.login.request(JUri+'index.php?option=com_gridbox&task=account.socialLogin', data, true);
                });
            });
        }
    });
    $g('#'+key+' .ba-login-wrapper[data-wrapper="login"] input').on('keydown', function(e){
        if (e.keyCode == 13) {
            $g(this).closest('.ba-login-wrapper').find('span[data-action]').trigger('click');
        }
    });
    if (themeData.page.view != 'gridbox' && obj.facebook.enable && !app.loading.facebook) {
        app.facebook.load();
    }
    if (themeData.page.view != 'gridbox' && obj.google.enable && !app.loading.google) {
        app.login.redirect = document.querySelector('#'+key+' .ba-login-content-wrapper').dataset.redirect;
        app.google.load(app.login.google, '.ba-google-login-button', {
            type: 'standard',
            large: 'size',
            width: 400
        });
    }
    initItems();
}

app.login = {
    loading: {},
    google: (response) => {
        let json = app.google.JSONWebToken(response.credential),
            data = app.google.getUserInfo(json);
        app.login.request(JUri+'index.php?option=com_gridbox&task=account.socialLogin', data, true);
    },
    checkCaptcha: () => {
        app.login.wrapper.querySelectorAll('.login-recaptcha').forEach((elem) => {
            let response = app.login.getRecaptchaResponse(elem);
            if (!response) {
                app.login.toggleAlertTooltip(true, elem, elem, 'THIS_FIELD_REQUIRED');
            }
        });
    },
    remindPassword: () => {
        if (app.login.process) {
            return false;
        }
        let data = {};
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input').forEach((input) => {
            data.email = input.value.trim();
            if (input.type == 'email' && !(/@/g.test(data.email) && data.email.match(/@/g).length == 1)) {
                app.login.toggleAlertTooltip(true, input, input.closest('.ba-login-field-wrapper'), 'ENTER_VALID_VALUE');
            }
        });
        app.login.checkCaptcha();
        if (!app.login.wrapper.querySelector('.ba-login-alert')) {
            app.login.request(JUri+'index.php?option=com_gridbox&task=account.remindPassword', data, false).then((response) => {
                if (response.status) {
                    app.login.wrapper.style.display = 'none';
                    app.login.wrapper.closest('.ba-login-content-wrapper').querySelector('.ba-password-request-wrapper').style.display = '';
                }
            });
        }
    },
    requestPassword: () => {
        if (app.login.process) {
            return false;
        }
        let data = {};
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input').forEach((input) => {
            data[input.name] = input.value.trim();
            if (!data[input.name]) {
                app.login.toggleAlertTooltip(true, input, input.closest('.ba-login-field-wrapper'), 'THIS_FIELD_REQUIRED');
            }
        });
        if (!app.login.wrapper.querySelector('.ba-login-alert')) {
            app.login.request(JUri+'index.php?option=com_gridbox&task=account.requestPassword', data, false).then((response) => {
                if (response.status) {
                    app.login.wrapper.style.display = 'none';
                    let wrapper = app.login.wrapper.closest('.ba-login-content-wrapper').querySelector('.ba-password-reset-wrapper');
                    wrapper.style.display = '';
                    wrapper.querySelector('input[type="hidden"]').value = response.id;
                }
            });
        }
    },
    resetPassword: () => {
        if (app.login.process) {
            return false;
        }
        let data = {};
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input').forEach((input) => {
            data[input.name] = input.value.trim();
            input.inputWrapper = input.closest('.ba-login-field-wrapper');
            if (!data[input.name]) {
                app.login.toggleAlertTooltip(true, input, input.inputWrapper, 'THIS_FIELD_REQUIRED');
            }
        });
        if (data.password1 && data.password2 && data.password1 != data.password2) {
            app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input[name="password2"]').forEach((input) => {
                app.login.toggleAlertTooltip(true, input, input.inputWrapper, 'PASSWORDS_ENTERED_NOT_MATCH');
            });
        }
        if (!app.login.wrapper.querySelector('.ba-login-alert')) {
            app.login.request(JUri+'index.php?option=com_gridbox&task=account.resetPassword', data, false).then((response) => {
                if (response.status) {
                    app.login.wrapper.style.display = 'none';
                    app.login.wrapper.closest('.ba-login-content-wrapper').querySelector('.ba-password-successful-reset-wrapper').style.display = '';
                }
            });
        }
    },
    username: () => {
        if (app.login.process) {
            return false;
        }
        let data = {};
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input').forEach((input) => {
            data.email = input.value.trim();
            if (input.type == 'email' && !(/@/g.test(data.email) && data.email.match(/@/g).length == 1)) {
                app.login.toggleAlertTooltip(true, input, input.closest('.ba-login-field-wrapper'), 'ENTER_VALID_VALUE');
            }
        });
        app.login.checkCaptcha();
        if (!app.login.wrapper.querySelector('.ba-login-alert')) {
            app.login.request(JUri+'index.php?option=com_gridbox&task=account.remindUsername', data, false).then((response) => {
                if (response.status) {
                    app.login.wrapper.style.display = 'none';
                    app.login.wrapper.closest('.ba-login-content-wrapper').querySelector('.ba-forgot-username-sended-wrapper').style.display = '';
                }
            });
        }
    },
    registration: () => {
        if (app.login.process) {
            return false;
        }
        let data = {};
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input:not([type="checkbox"])').forEach((input) => {
            data[input.name] = input.value.trim();
            input.inputWrapper = input.closest('.ba-login-field-wrapper');
            if (!data[input.name]) {
                app.login.toggleAlertTooltip(true, input, input.inputWrapper, 'THIS_FIELD_REQUIRED');
            } else if (input.type == 'email' && !(/@/g.test(data[input.name]) && data[input.name].match(/@/g).length == 1)) {
                app.login.toggleAlertTooltip(true, input, input.inputWrapper, 'ENTER_VALID_VALUE');
            }
        });
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input[type="checkbox"]').forEach((input) => {
            if (!input.checked) {
                app.login.toggleAlertTooltip(true, input, input.closest('.ba-login-field-wrapper'), 'THIS_FIELD_REQUIRED');
            }
        });
        if (data.password1 && data.password2 && data.password1 != data.password2) {
            app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input[name="password2"]').forEach((input) => {
                app.login.toggleAlertTooltip(true, input, input.inputWrapper, 'PASSWORDS_ENTERED_NOT_MATCH');
            });
        }
        app.login.checkCaptcha();
        if (!app.login.wrapper.querySelector('.ba-login-alert')) {
            app.login.request(JUri+'index.php?option=com_gridbox&task=store.register', data, true);
        }
    },
    login: () => {
        if (app.login.process) {
            return false;
        }
        let data = {
                remember: Number(document.querySelector('.ba-login-field-wrapper .ba-checkbox input').checked)
            };
        app.login.wrapper.querySelectorAll('.ba-login-field-wrapper input:not([type="checkbox"])').forEach((input) => {
            data[input.name] = input.value.trim();
            if (!data[input.name]) {
                app.login.toggleAlertTooltip(true, input, input.closest('.ba-login-field-wrapper'), 'THIS_FIELD_REQUIRED');
            }
        });
        app.login.checkCaptcha();
        if (!app.login.wrapper.querySelector('.ba-login-alert')) {
            app.login.request(JUri+'index.php?option=com_gridbox&task=store.login', data, true);
        }
    },
    request: (url, data, redirect) => {
        app.login.process = true;
        return new Promise((resolve, reject) => {
            app.fetch(url, data).then(function(text){
                app.login.process = false;
                let response = JSON.parse(text);
                if (!response.status) {
                    app.showNotice(response.message, 'ba-alert');
                } else if (redirect) {
                    window.location.href = app.login.redirect ? app.login.redirect : window.location.href;
                }
                resolve(response);
            });
        });
    },
    getRecaptchaResponse: (elem) => {
        let response = '';
        try {
            response = grecaptcha.getResponse(recaptchaObject.data[elem.id]);
        } catch (err) {
            console.info(err)
        }

        return response != '';
    },
    execute: (btn) => {
        if (themeData.page.view == 'gridbox') {
            return;
        }
        app.login.wrapper = btn.closest('div[data-wrapper]');
        app.login.redirect = app.login.wrapper.closest('.ba-login-content-wrapper').dataset.redirect;
        app.login[btn.dataset.action]();
    },
    toggleAlertTooltip: function(alert, $this, parent, key){
        if (alert && !$this.alertTooltip) {
            $this.alertTooltip = document.createElement('span');
            $this.alertTooltip.className = 'ba-login-alert-tooltip';
            $this.alertTooltip.textContent = app._(key);
            parent.classList.add('ba-login-alert');
            parent.appendChild($this.alertTooltip);
        } else if (alert && $this.alertTooltip) {
            $this.alertTooltip.textContent = app._(key);
        } else if (!alert && $this.alertTooltip) {
            app.login.removeAlertTooltip($this);
        }
    },
    removeAlertTooltip: function($this){
        if ($this.alertTooltip) {
            $this.alertTooltip.remove();
            $this.alertTooltip = null;
            $this.closest('.ba-login-alert').classList.remove('ba-login-alert');
        }
    }
}

if (app.modules.initLogin) {
    app.initLogin(app.modules.initLogin.data, app.modules.initLogin.selector);
}