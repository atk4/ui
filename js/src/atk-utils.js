import mitt from 'mitt';
import 'helpers/url.helper';

/**
 * Define atk global options.
 * In Js:
 *  atk.options.set('name','value');
 * In Php:
 *  (new JsChain('atk.options')->set('name', 'value');
 */
const atkOptions = (function () {
    const options = {
    // Value for debounce time out (in ms) that will be apply globally when set using atk.debounce.
        debounceTimeout: null,
    };
    return {
        set: (name, value) => { options[name] = value; },
        get: (name) => options[name],
    };
}());

/**
 * Subscribe too and publish events.
 * listen to an event
 *   atk.eventBus.on('foo', e => console.log('foo', e))
 * Fire an event
 *   atk.eventBus.emit('foo', { a: 'b' })
 *
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
* from atk context. Usage: atk.utils.date().parse('string');
*/
const atkUtils = (function () {
    return {
        date: function () {
            return {
                // fix date parsing for different time zone if time is not supply.
                parse: function (dateString) {
                    if (dateString.match(/^[0-9]{4}[/\-.][0-9]{2}[/\-.][0-9]{2}$/)) {
                        dateString += ' 00:00:00';
                    }
                    return dateString;
                },
            };
        },
    };
}());

export { atkOptions, atkEventBus, atkUtils };
