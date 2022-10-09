import $ from 'jquery';
import AtkServerEventPlugin from './plugins/server-event.plugin';
import AtkReloadViewPlugin from './plugins/reload-view.plugin';
import AtkAjaxecPlugin from './plugins/ajaxec.plugin';
import AtkCreateModalPlugin from './plugins/create-modal.plugin';
import AtkFileUploadPlugin from './plugins/file-upload.plugin';
import AtkJsSearchPlugin from './plugins/js-search.plugin';
import AtkJsSortablePlugin from './plugins/js-sortable.plugin';
import AtkConditionalFormPlugin from './plugins/conditional-form.plugin';
import AtkColumnResizerPlugin from './plugins/column-resizer.plugin';
import AtkScrollPlugin from './plugins/scroll.plugin';
import AtkConfirmPlugin from './plugins/confirm.plugin';
import AtkSidenavPlugin from './plugins/sidenav.plugin';

/**
 * Register a jQuery plugin.
 *
 * @param {string}   name      Plugin name
 * @param {Function} cl        Plugin class
 * @param {boolean}  shortHand Generate a shorthand as $.pluginName
 */
function registerPlugin(name, cl, shortHand = false) {
    // Add atk namespace to jQuery global space.
    // TODO should be initialized in entry JS if really needed
    if (!$.atk) {
        $.atk = {};
    }

    const pluginName = 'atk' + name;
    const dataName = '__' + pluginName;

    // add plugin to atk namespace.
    $.atk[name] = cl;

    // register plugin to jQuery fn prototype.
    $.fn[pluginName] = function (option = {}, args = []) {
        // Check if we are calling a plugin specific function: $(element).plugin('function', [arg1, arg2]);
        if (typeof option === 'string') {
            if (this.data(dataName) && typeof this.data(dataName)[option] === 'function') {
                return this.data(dataName).call(option, args);
            }
            // return if trying to call a plugin method prior to instantiate it.
            return;
        }

        return this.each(function () {
            const options = $.extend({}, cl.DEFAULTS, typeof option === 'object' && option);
            // create plugin using the constructor function store in atk namespace object
            // and add a reference of it to this jQuery object data.
            $(this).data(dataName, new $.atk[name](this, options));
        });
    };

    // short hand
    if (shortHand) {
        $[pluginName] = (options) => $({})[pluginName](options);
    }
}

/**
 * Register all jQuery plugins needed for atk.
 */
registerPlugin('ReloadView', AtkReloadViewPlugin);
registerPlugin('Ajaxec', AtkAjaxecPlugin);
registerPlugin('CreateModal', AtkCreateModalPlugin);
registerPlugin('ServerEvent', AtkServerEventPlugin, true);
registerPlugin('FileUpload', AtkFileUploadPlugin);
registerPlugin('JsSearch', AtkJsSearchPlugin);
registerPlugin('JsSortable', AtkJsSortablePlugin);
registerPlugin('ConditionalForm', AtkConditionalFormPlugin, true);
registerPlugin('ColumnResizer', AtkColumnResizerPlugin);
registerPlugin('Scroll', AtkScrollPlugin);
registerPlugin('Confirm', AtkConfirmPlugin, true);
registerPlugin('Sidenav', AtkSidenavPlugin);

export { registerPlugin };
