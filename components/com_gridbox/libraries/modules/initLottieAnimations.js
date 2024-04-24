/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

app.initLottieAnimations = function(obj, key){
    $g('#'+key+' .ba-lottie-animations-wrapper').each(function(){
        if (!this.querySelector('lottie-player')) {
            this.innerHTML = '<lottie-player></lottie-player>'
        }
    });
    $g('#'+key+' lottie-player').each(function(){
        this.addEventListener('load', app.lottie.loaded);
        setTimeout(function(){
            this.autoplay = obj.trigger == 'autoplay';
            this.loop = obj.loop;
            this.speed = obj.speed;
            let src = obj.source == 'link' ? obj.link : (app.isExternal(obj.file) ? '' : JUri)+obj.file;
            this.load(src);
            this.pause();
            if (this.observer) {
                this.observer.unobserve(this);
            }
            if (obj.trigger == 'viewport' && !this.observer) {
                this.observer = new IntersectionObserver(function(entries){
                    if (entries[0].isIntersecting) {
                        this.play();
                    }
                }.bind(this))
            }
            if (obj.trigger == 'viewport') {
                this.observer.observe(this);
            }
            this.removeEventListener('mouseenter', app.lottie.play);
            this.removeEventListener('mouseout', app.lottie.pause);
            if (obj.trigger == 'hover') {
                this.addEventListener('mouseenter', app.lottie.play);
                this.addEventListener('mouseout', app.lottie.pause);
            }
            if (this.interactivity) {
                this.interactivity.stop();
            }
            if (obj.trigger == 'scroll' && !this.interactivity) {
                this.interactivity = LottieInteractivity.create(app.lottie.interactivity(this));
            } else if (obj.trigger == 'scroll') {
                this.interactivity = this.interactivity.redefineOptions(app.lottie.interactivity(this));
            }
        }.bind(this), 50);
    });
    initItems();
}

app.lottie = {
    loaded: function(){
        $g(window).trigger('resize')
    },
    load: function(){
        let file = document.createElement('script');
        file.onload = function(){
            file = document.createElement('script');
            file.onload = function(){
                app.initLottieAnimations(app.modules.initLottieAnimations.data, app.modules.initLottieAnimations.selector);
            }
            file.src = 'https://unpkg.com/@lottiefiles/lottie-interactivity@latest/dist/lottie-interactivity.min.js';
            document.head.append(file);
        }
        file.src = 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js';
        document.head.append(file);
    },
    play: function(){
        this.play();
    },
    pause: function(){
        this.pause();
    },
    interactivity: function(player){
        return {
            mode: 'scroll',
            player: player,
            actions: [
                {
                    visibility: [0,1],
                    type: 'seek',
                    frames: [0]
                },
            ],
        };
    }
}

app.lottie.load();