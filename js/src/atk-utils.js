import mitt from 'mitt';
import lodashDebounce from 'lodash/debounce';
import './helpers/url.helper';

/**
 * Define atk global options.
 * In Js:
 * atk.options.set('name', 'value');
 * In Php:
 * (new JsChain('atk.options')->set('name', 'value');
 */
const atkOptions = (function () {
    const options = {};

    return {
        set: (name, value) => { options[name] = value; },
        get: (name) => options[name],
    };
}());

/**
 * Subscribe too and publish events.
 * listen to an event
 * atk.eventBus.on('foo', e => console.log('foo', e))
 * Fire an event
 * atk.eventBus.emit('foo', { a: 'b' })
 */
const atkEventBus = (function () {
    const eventBus = mitt();

    return {
        emit: (event, payload) => eventBus.emit(event, payload),
        on: (event, ref) => eventBus.on(event, ref),
        off: (event, ref) => eventBus.off(event, ref),
        clearAll: () => eventBus.all.clear(),
    };
}());

/*
* Utilities function that you can execute
* from atk context. Usage: atk.utils.redirect('url');
*/
const atkUtils = (function () {
    return {
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
            document.location = $.atkAddParams(url, params);
        },
    };
}());

function atkDebounce(func, wait, options) {
    let timerId = null;
    let debouncedInner;

    function createTimer() {
        timerId = setInterval(() => {
            if (!debouncedInner.pending()) {
                clearInterval(timerId);
                timerId = null;
                jQuery.active--;
            }
        }, 25);
        jQuery.active++;
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
}

export {
    atkOptions, atkEventBus, atkUtils, atkDebounce,
};
