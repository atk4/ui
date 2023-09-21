import $ from 'external/jquery';
import atk from 'atk';
import AtkAjaxecPlugin from './plugins/ajaxec.plugin';
import AtkColumnResizerPlugin from './plugins/column-resizer.plugin';
import AtkConditionalFormPlugin from './plugins/conditional-form.plugin';
import AtkConfirmPlugin from './plugins/confirm.plugin';
import AtkCreateModalPlugin from './plugins/create-modal.plugin';
import AtkFileUploadPlugin from './plugins/file-upload.plugin';
import AtkJsSearchPlugin from './plugins/js-search.plugin';
import AtkJsSortablePlugin from './plugins/js-sortable.plugin';
import AtkReloadViewPlugin from './plugins/reload-view.plugin';
import AtkScrollPlugin from './plugins/scroll.plugin';
import AtkServerEventPlugin from './plugins/server-event.plugin';
import AtkSidenavPlugin from './plugins/sidenav.plugin';

/**
 * Register a jQuery plugin.
 *
 * @param {string}   name      Plugin name
 * @param {Function} cl        Plugin class
 * @param {boolean}  shorthand Map $.name(...) to $({}).name(...)
 */
atk.registerPlugin = function (name, cl, shorthand = false) {
    const dataName = '__' + name;

    // add plugin to atk namespace
    atk[name] = cl;

    // register plugin to jQuery fn prototype
    $.fn[name] = function (option = {}, args = []) {
        // check if we are calling a plugin specific function: $(element).plugin('function', [arg1, arg2]);
        if (typeof option === 'string') {
            return this.data(dataName).call(option, args);
        }

        return this.each(function () {
            const options = $.extend({}, cl.DEFAULTS, typeof option === 'object' && option);
            // create plugin using the constructor function store in atk namespace object
            // and add a reference of it to this jQuery object data
            $(this).data(dataName, new atk[name](this, options));
        });
    };

    if (shorthand) {
        $[name] = (options) => $({})[name](options);
    }
};

atk.registerPlugin('atkAjaxec', AtkAjaxecPlugin);
atk.registerPlugin('atkColumnResizer', AtkColumnResizerPlugin);
atk.registerPlugin('atkConditionalForm', AtkConditionalFormPlugin);
atk.registerPlugin('atkConfirm', AtkConfirmPlugin, true);
atk.registerPlugin('atkCreateModal', AtkCreateModalPlugin);
atk.registerPlugin('atkFileUpload', AtkFileUploadPlugin);
atk.registerPlugin('atkJsSearch', AtkJsSearchPlugin);
atk.registerPlugin('atkJsSortable', AtkJsSortablePlugin);
atk.registerPlugin('atkReloadView', AtkReloadViewPlugin);
atk.registerPlugin('atkScroll', AtkScrollPlugin);
atk.registerPlugin('atkServerEvent', AtkServerEventPlugin);
atk.registerPlugin('atkSidenav', AtkSidenavPlugin);

export default null;
