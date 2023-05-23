import $ from 'external/jquery';
import mitt from 'mitt';
import lodashDebounce from 'lodash/debounce';
import atk from 'atk';
import tableDropdownHelper from './helpers/table-dropdown.helper';
import urlHelper from './helpers/url.helper';

/**
 * Define atk global options.
 * In JS:
 * atk.options.set('name', 'value');
 * In PHP:
 * (new JsChain('atk.options')->set('name', 'value');
 */
atk.options = (function () {
    const data = {};

    return {
        set: (name, value) => { data[name] = value; },
        get: (name) => data[name],
    };
}());

/**
 * Subscribe too and publish events.
 * listen to an event
 * atk.eventBus.on('foo', e => console.log('foo', e))
 * Fire an event
 * atk.eventBus.emit('foo', { a: 'b' })
 */
atk.eventBus = (function () {
    const emitter = mitt();

    return {
        emit: (event, payload) => emitter.emit(event, payload),
        on: (event, ref) => emitter.on(event, ref),
        off: (event, ref) => emitter.off(event, ref),
        clearAll: () => emitter.all.clear(),
    };
}());

atk.createDebouncedFx = function (func, wait, options) {
    let timerId = null;
    let lodashDebouncedFx;

    function createTimer() {
        timerId = setInterval(() => {
            if (!lodashDebouncedFx.pending()) {
                clearInterval(timerId);
                timerId = null;
                $.active--;
            }
        }, 25);
        $.active++;
    }

    lodashDebouncedFx = lodashDebounce(func, wait, options);

    function debouncedFx(...args) {
        if (timerId === null) {
            createTimer();
        }

        return lodashDebouncedFx(...args);
    }
    debouncedFx.cancel = lodashDebouncedFx.cancel;
    debouncedFx.flush = lodashDebouncedFx.flush;
    debouncedFx.pending = lodashDebouncedFx.pending;

    return debouncedFx;
};

/**
 * Utilities function that you can execute from atk context.
 * Usage: atk.utils.redirect('url');
 */
atk.utils = {
    redirect: function (url, params) {
        document.location = atk.urlHelper.appendParams(url, params);
    },
};

atk.tableDropdownHelper = tableDropdownHelper;
atk.urlHelper = urlHelper;

export default null;
