import Vue from 'vue';
import SemanticUiVue from 'semantic-ui-vue';

// disable console logs for non-minified build
Vue.config.productionTip = false;
Vue.config.devtools = false;

Vue.use(SemanticUiVue);

Vue.component('flat-picker', () => import('vue-flatpickr-component'));

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

// return async component that will load on demand
const componentFactory = (name, component) => () => ({
    component: component().then((r) => { atk.vueService.markComponentLoaded(name); return r; }),
    loading: atkVueLoader,
    error: atkVueError,
    delay: 200,
});

const atkComponents = {
    'atk-inline-edit': componentFactory('atk-inline-edit', () => import(/* webpackChunkName: "atk-vue-inline-edit" */'../components/inline-edit.component')),
    'atk-item-search': componentFactory('atk-item-search', () => import(/* webpackChunkName: "atk-vue-item-search" */'../components/item-search.component')),
    'atk-multiline': componentFactory('atk-multiline', () => import(/* webpackChunkName: "atk-vue-multiline" */'../components/multiline/multiline.component')),
    'atk-tree-item-selector': componentFactory('atk-tree-item-selector', () => import(/* webpackChunkName: "atk-vue-tree-item-selector" */'../components/tree-item-selector/tree-item-selector.component')),
    'atk-query-builder': componentFactory('atk-query-builder', () => import(/* webpackChunkName: "atk-vue-query-builder" */'../components/query-builder/query-builder.component.vue')),
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

    /**
     * Created a Vue component and add it to the vues array.
     * For Root component (App) to be aware that it's children component is
     * mounted, you need to use @hook:mounted="setReady"
     * @param name
     * @param component
     * @param data
     */
    createAtkVue(id, component, data) {
        this.registerComponent({
            ids: [id],
            name: component,
            instance: new Vue({
                el: id,
                data: { initData: data },
                components: { [component]: atkComponents[component] },
                mixins: [this.vueMixins],
            }),
            isLoaded: false,
        });
    }

    /**
     * Create a Vue instance from an external src component definition.
     *
     * @param name
     * @param component
     * @param data
     */
    createVue(id, componentName, component, data) {
        this.registerComponent({
            ids: [id],
            name: componentName,
            instance: new Vue({
                el: id,
                data: { initData: data, isReady: true },
                components: { [componentName]: window[component] },
                mixins: [this.vueMixins],
            }),
            isLoaded: true,
        });
    }

    /*
     * Add component to vues container.
     * Group ids that are using the same component.
     */
    registerComponent(component) {
        // check if that component is already registered
        const registered = this.vues.filter((comp) => comp.name === component.name);
        if (registered.length > 0) {
            registered[0].ids.push(component.ids[0]);
        } else {
            this.vues.push(component);
        }
    }

    /**
     * Register components within Vue.
     */
    useComponent(component) {
        if (window[component]) {
            Vue.use(window[component]);
        } else {
            console.error('Unable to register component: ' + component + '. Make sure it is load correctly.');
        }
    }

    /**
     * Return Vue.
     *
     * @returns {Vue | VueConstructor}
     */
    getVue() {
        return Vue;
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

const vueService = new VueService();
Object.freeze(vueService);

export default vueService;
