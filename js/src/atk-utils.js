import mitt from 'mitt';

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

export { atkOptions, atkEventBus };
