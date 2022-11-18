import $ from 'external/jquery';
import { createApp, defineAsyncComponent } from 'vue';
import VueFomanticUi from 'vue-fomantic-ui';
import atk from 'atk';

if (createApp === 'x') { // for debug vue3 migration only, never true
    Vue.use(VueFomanticUi);

    Vue.component('flatpickr-picker', () => import('vue-flatpickr-component'));
}

// vue loader component to display while dynamic component is loading
const atkVueLoader = {
    name: 'atk-vue-loader',
    template: '<div><div class="ui active centered inline loader"></div></div>',
};

// vue error component to display when dynamic component loading fail
const atkVueError = {
    name: 'atk-vue-error',
    template: '<div class="ui negative message"><p>Error: Unable to load Vue component</p></div>',
};

// async component that will load on demand
const asyncComponentFactory = (name, component) => defineAsyncComponent({
    loader: () => component().then((r) => { atk.vueService.markComponentLoaded(name); return r; }),
    loadingComponent: atkVueLoader,
    errorComponent: atkVueError,
    delay: 200,
    timeout: 5000,
});

const atkComponents = {
    'atk-inline-edit': asyncComponentFactory('atk-inline-edit', () => import(/* webpackChunkName: 'atk-vue-inline-edit' */'../vue-components/inline-edit.component')),
    'atk-item-search': asyncComponentFactory('atk-item-search', () => import(/* webpackChunkName: 'atk-vue-item-search' */'../vue-components/item-search.component')),
    'atk-multiline': asyncComponentFactory('atk-multiline', () => import(/* webpackChunkName: 'atk-vue-multiline' */'../vue-components/multiline/multiline.component')),
    'atk-tree-item-selector': asyncComponentFactory('atk-tree-item-selector', () => import(/* webpackChunkName: 'atk-vue-tree-item-selector' */'../vue-components/tree-item-selector/tree-item-selector.component')),
};

/**
 * Allow to create Vue component.
 */
class VueService {
    constructor() {
        this.vues = [];
        this.vueMixins = {
            methods: {
                getData: function () {
                    return this.initData;
                },
            },
            // provide method to our child component.
            // child component would need to inject a method to have access using the inject property,
            // inject: ['getRootData'],
            // Once inject you can get initial data using this.getRootData().
            provide: function () {
                return {
                    getRootData: this.getData,
                };
            },
        };
    }

    createApp(rootComponent) {
        return createApp(rootComponent);
    }

    /**
     * Created a Vue component and add it to the vues array.
     * For root component (App) to be aware that it's children component is
     * mounted, you need to use @hook:mounted="setReady"
     */
    createAtkVue(id, componentName, data) {
        const app = atk.vueService.createApp({
            el: id, // TODO is it needed with mount?
            data: () => ({ initData: data }),
            mixins: [this.vueMixins],
        });

        app.component(componentName, atkComponents[componentName]);

        app.mount(id);

        this.registerComponent({
            ids: [id],
            name: componentName,
            instance: app,
            isLoaded: false,
        });
    }

    /**
     * Create a Vue instance from an external src component definition.
     */
    createVue(id, componentName, component, data) {
        const app = atk.vueService.createApp({
            el: id, // TODO is it needed with mount?
            data: () => ({ initData: data, isReady: true }),
            mixins: [this.vueMixins],
        });

        app.component('demo-clock', window.vueDemoClock);

        const def = $.extend({ }, component);
        const defData = def.data;
        def.data = function () {
            const res = $.extend({ }, defData.call(this));
            res.initData = data;
            return res;
        };
        app.component(componentName, def);

        app.mount(id);

        this.registerComponent({
            ids: [id],
            name: componentName,
            instance: app,
            isLoaded: true,
        });
    }

    /*
     * Add component to vues container.
     * Group ids that are using the same component.
     */
    registerComponent(component) {
        const registered = this.vues.filter((v) => v.name === component.name);
        if (registered.length === 0) {
            this.vues.push(component);
        } else {
            registered[0].ids.push(component.ids[0]);
        }
    }

    /**
     * Mark a component as loaded.
     */
    markComponentLoaded(name) {
        this.vues.forEach((component) => {
            if (component.name === name) {
                component.isLoaded = true;
            }
        });
    }

    /**
     * Check if all components on page are ready and fully loaded.
     */
    areComponentsLoaded() {
        return this.vues.filter((component) => component.isLoaded === false).length === 0;
    }
}

export default Object.freeze(new VueService());
