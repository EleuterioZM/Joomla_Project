/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.login = function(){
    setTimeout(function(){
        app.checkGridboxState();
    }, 50);
}

app.checkGridboxData = function(obj){
    var url = 'https://www.balbooa.com/demo/index.php?',
        domain = JUri.replace(/https?:\/\//, '').replace('www.', ''),
        script = document.createElement('script');
    url += 'option=com_baupdater&task=gridbox.checkGridboxUser';
    url += '&data='+obj.data;
    if (domain[domain.length - 1] != '/') {
        domain += '/';
    }
    url += '&domain='+window.btoa(domain);
    url += '&time='+(+(new Date()));
    script.src = url;
    document.head.appendChild(script);
}

app.showGridboxLogin = function(){
    $g('.ba-username').val('');
    $g('.ba-password').val('');
    $g('#login-modal').modal();
}

app.checkGridboxState = function(){
    $g.ajax({
        type:"POST",
        dataType:'text',
        url:JUri+"index.php?option=com_gridbox&task=editor.checkGridboxState",
        success: function(msg){
            var flag = true,
                obj;
            if (msg) {
                obj = JSON.parse(msg);
                flag = !obj.data;
            }
            if (flag) {
                app.showGridboxLogin();
            } else {
                app.checkGridboxData(obj);
            }
        }
    });
}

app.getAppLicense = function(){
    $g.ajax({
        type:"POST",
        dataType:'text',
        url:JUri+"index.php?option=com_gridbox&task=editor.getAppLicense",
        data:{
            data: gridboxUser.data
        },
        success : function(msg){
            if ($g('#login-modal').hasClass('in')) {
                $g('#login-modal').modal('hide');
            }
            if ('callback' in gridboxUser) {
                gridboxUser.callback();
            }
        }
    });
}

$g('.ba-username, .ba-password').on('keyup', function(event){
    if (event.keyCode == 13 && $g('.ba-password').val() != '') {
        $g('.login-button').trigger('click');
    }
});

function fetchPageBlock(block, blocks, plugins, n)
{
    if (block) {
        block.method = window.atob('YmFzZTY0X2RlY29kZQ==');
        app.fetch(JUri+'index.php?option=com_gridbox&task=editor.fetchPageBlock', block).then((text) => {
            console.info(text)
            app.notification.find('.installed-page-block-count').text(++n);
            $g('.ba-page-block-item[data-id="'+block.title+'"]').removeClass('disabled');
            block = blocks.shift();
            fetchPageBlock(block, blocks, plugins, n);
        });
    } else {
        $g('.ba-page-block-item.disabled').removeClass('disabled');
        uploadPlugins(plugins);
        app.showNotice(app._('BLOCKS_INSTALLED'));
    }
}

function uploadPageBlock(blocks, plugins)
{
    if (blocks.length > 0) {
        let XHR = new XMLHttpRequest(),
            n = app.notification.find('.installed-page-block-count').text(),
            time = null,
            block = blocks.shift();
        block.method = window.atob('YmFzZTY0X2RlY29kZQ==');
        XHR.onreadystatechange = function(e) {
            if (XHR.readyState == 4 && XHR.status == 200) {
                clearTimeout(time);
                app.notification.find('.installed-page-block-count').text(++n);
                $g('.ba-page-block-item[data-id="'+block.title+'"]').removeClass('disabled');
                uploadPageBlock(blocks, plugins);
            }
        };
        XHR.open("POST", JUri+'index.php?option=com_gridbox&task=editor.getBlocksLicense', true);
        XHR.send(JSON.stringify(block));
        time = setTimeout(() => {
            XHR.abort();
            fetchPageBlock(block, blocks, plugins, n);
        }, 5000);
    } else {
        $g('.ba-page-block-item.disabled').removeClass('disabled');
        uploadPlugins(plugins);
        app.showNotice(app._('BLOCKS_INSTALLED'));
    }
}

function uploadPlugins(plugins)
{
    $g.ajax({
        type:"POST",
        dataType:'text',
        url: JUri+'index.php?option=com_gridbox&task=editor.getPluginLicense',
        data : {
            data : JSON.stringify(plugins)
        },
        complete: function(msg){
            $g('#add-plugin-dialog .ba-plugin.disable-plugin').removeClass('disable-plugin');
        }
    });
}

app.updatePlugins = function(plugins){
    var str = '<span>'+gridboxLanguage['INSTALLING'];
    str += '</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
    app.notification.find('p').html(str);
    app.notification.removeClass('animation-out').addClass('notification-in');
    uploadPlugins(plugins);
    app.showNotice(gridboxLanguage['PLUGIN_INSTALLED']);
}

app.updatePageBlocks = function(pblocks, plugins){
    var str = '<span>'+gridboxLanguage['INSTALLING'],
        blocks = [];
    for (var key in pblocks) {
        for (var ind in pblocks[key]) {
            pblocks[key][ind].type = key;
            blocks.push(pblocks[key][ind]);
        }
    }
    str += ' <span class="installed-page-block-count">0</span> / '+blocks.length;
    str +='</span><img src="'+JUri+'components/com_gridbox/assets/images/reload.svg"></img>';
    app.notification.find('p').html(str);
    app.notification.removeClass('animation-out').addClass('notification-in');
    uploadPageBlock(blocks, plugins);
}

$g('.login-button').on('click', function(event){
    event.preventDefault();
    if (this.dataset.clicked == 'disabled') {
    	return false;
    }
    this.dataset.clicked = 'disabled';
    var url = 'https://www.balbooa.com/demo/index.php?',
        domain = window.location.host.replace('www.', ''),
        script = document.createElement('script');
    domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
    url += 'option=com_baupdater&task=gridbox.getGridboxUser';
    url += '&login='+window.btoa($('.ba-username').val().trim());
    url += '&password='+window.btoa($('.ba-password').val().trim());
    if (domain[domain.length - 1] != '/') {
        domain += '/';
    }
    url += '&domain='+window.btoa(domain);
    script.onload = function(){
        $g('.login-button')[0].dataset.clicked = 'enabled';
    }
    script.src = url;
    document.head.appendChild(script);
})

app.modules.login = true;
app.login();