import $ from 'external/jquery';
import mitt from 'mitt';
import lodashDebounce from 'lodash/debounce';
import atk from 'atk';
import tableDropdownHelper from './helpers/table-dropdown.helper';
import urlHelper from './helpers/url.helper';

/**
 * Define atk global options.
 * In Js:
 * atk.options.set('name', 'value');
 * In Php:
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

atk.debounce = function (func, wait, options) {
    let timerId = null;
    let debouncedInner;

    function createTimer() {
        timerId = setInterval(() => {
            if (!debouncedInner.pending()) {
                clearInterval(timerId);
                timerId = null;
                $.active--;
            }
        }, 25);
        $.active++;
    }

    debouncedInner = lodashDebounce(func, wait, options);

    function debounced(...args) {
        if (timerId === null) {
            createTimer();
        }

        return debouncedInner(...args);
    }
    debounced.cancel = debouncedInner.cancel;
    debounced.flush = debouncedInner.flush;
    debounced.pending = debouncedInner.pending;

    return debounced;
};

/*
* Utilities function that you can execute
* from atk context. Usage: atk.utils.redirect('url');
*/
atk.utils = {
    json: function () {
        return {
            // try parsing string as JSON. Return parse if valid, otherwise return onError value.
            tryParse: function (str, onError = null) {
                try {
                    return JSON.parse(str);
                } catch (e) {
                    return onError;
                }
            },
        };
    },
    redirect: function (url, params) {
        document.location = atk.urlHelper.appendParams(url, params);
    },
};

atk.tableDropdownHelper = tableDropdownHelper;
atk.urlHelper = urlHelper;

export default null;
