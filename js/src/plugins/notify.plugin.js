import $ from 'jquery';
import atkPlugin from './atk.plugin';

/**
 * Create notification message.
 *
 * Can be attach to an element using $('selector')->atkNotify(options);
 * or to the body using $.atkNotify($option)
 */
export default class notify extends atkPlugin {
    main() {
        let cssStyle = {};
        this.timer = null;
        let domElement = 'body';

        if (!$.isEmptyObject(this.$el[0])) {
            domElement = this.$el;
        }

        cssStyle = this.getClasses(domElement);
        cssStyle.base.width = this.settings.width + '%';
        cssStyle.base.opacity = this.settings.opacity;

        this.notify = $(this.getNotifier(this.settings)).hide();
        this.notify.css($.extend(cssStyle.base, this.getPosition(this.settings.position)));

        this.notify.on('click', '.icon.close', { self: this }, this.removeNotifier);

        this.notify.appendTo(domElement);

        this.notify.transition(this.settings.openTransition);

        if (this.settings.duration) {
            this.timer = setTimeout(() => {
                this.removeNotifier({ data: { self: this } });
            }, this.settings.duration);
        }
    }

    /**
     * Return the html for this notifications.
     * @param options
     * @returns {string}
     */
    getNotifier(options) {
        return `<div class="atk-notify transition hidden">
                <div class="ui ${options.color} ${options.size} inverted segment" style="overflow: auto; display: block !important">
                    <i class="close icon" style="float:right"></i>
                    <div class="content">
                        <i class="${options.icon} icon" style=""></i>
                        <span>${options.content}</span>
                    </div>
                </div>
             </div>`;
    }

    /**
     * Remove this notification from the element it was add to.
     *
     * @param e
     */
    removeNotifier(e) {
        const { self } = e.data;
        clearTimeout(self.timer);
        self.notify.transition(self.settings.closeTransition);
        self.notify.remove();
    }

    /**
     * Return basis css class use for this notification.
     *
     * @param el
     * @returns {{base: {position: string, z-index: number}}}
     */
    getClasses(el) {
        return {
            base: {
                position: (el === 'body') ? 'fixed' : 'absolute',
                'z-index': 9999,
            },
        };
    }

    /**
     * Return the css classes needed for positioning this notification.
     * @param position
     * @returns {*}
     */
    getPosition(position) {
        const positions = {
            topLeft: {
                top: '0px',
                left: '0px',
            },
            topCenter: {
                margin: 'auto',
                top: '0px',
                left: '0px',
                right: '0px',
            },
            topRight: {
                top: '0px',
                right: '0px',
            },
            bottomLeft: {
                bottom: '0px',
                left: '0px',
            },
            bottomCenter: {
                margin: 'auto',
                bottom: '0px',
                left: '0px',
                right: '0px',
            },
            bottomRight: {
                bottom: '0px',
                right: '0px',
            },
            center: {
                margin: 'auto',
                top: '0px',
                left: '0px',
                bottom: '0px',
                right: '0px',
                'max-height': '1%',
            },
        };
        return positions[position];
    }
}

notify.DEFAULTS = {
    color: 'green',
    size: 'small',
    icon: null,
    content: null,
    width: 100,
    closeTransition: 'scale',
    openTransition: 'scale',
    duration: 3000,
    opacity: '1',
    position: 'topLeft',
};
