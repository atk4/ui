import Vue from 'vue';
import SuiVue from 'semantic-ui-vue';
import atkClickOutside from '../directives/click-outside.directive';
import { focus } from '../directives/commons.directive';

Vue.use(SuiVue);

// Vue loader component to display while dynamic component is loading.
const atkVueLoader = {
    name: 'atk-vue-loader',
    template: '<div><div class="ui active centered inline loader"></div></div>',
};

// Vue error component to display when dynamic component loading fail.
const atkVueError = {
    name: 'atk-vue-error',
    template: '<div class="ui negative message"><p>Error: Unable to load Vue component</p></div>',
};

// Return async component that will load on demand.
const componentFactory = (component) => () => ({
    component: component(),
    loading: atkVueLoader,
    error: atkVueError,
    delay: 200,
});

const atkComponents = {
    'atk-inline-edit': componentFactory(() => import(/* webpackChunkName: "atk-vue-inline-edit" */'../components/inline-edit.component')),
    'atk-item-search': componentFactory(() => import(/* webpackChunkName: "atk-vue-item-search" */'../components/item-search.component')),
    'atk-multiline': componentFactory(() => import(/* webpackChunkName: "atk-vue-multiline" */'../components/multiline/multiline.component')),
    'atk-tree-item-selector': componentFactory(() => import(/* webpackChunkName: "atk-vue-tree-item-selector" */'../components/tree-item-selector/tree-item-selector.component')),
    'atk-query-builder': componentFactory(() => import(/* webpackChunkName: "atk-vue-query-builder" */'../components/query-builder/query-builder.component.vue')),
};

// setup atk custom directives.
const atkDirectives = [{ name: 'click-outside', def: atkClickOutside }, { name: 'focus', def: focus }];
atkDirectives.forEach((directive) => {
    Vue.directive(directive.name, directive.def);
});

/**
 * Singleton class
 * Create Vue component.
 */
class VueService {
    static getInstance() {
        return this.instance;
    }

    constructor() {
        if (!VueService.instance) {
            this.vues = [];
            this.vueMixins = {
                methods: {
                    getData: function () {
                        return this.initData;
                    },
                    setReady: function () {
                        this.isReady = true;
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
            VueService.instance = this;
        }
        return VueService.instance;
    }

    /**
   * Created a Vue component and add it to the vues array.
   * For Root component (App) to be aware that it's children component is
   * mounted, you need to use @hook:mounted="setReady"
   * @param name
   * @param component
   * @param data
   */
    createAtkVue(name, component, data) {
        this.vues.push({
            name: name,
            instance: new Vue({
                el: name,
                data: { initData: data, isReady: false },
                components: { [component]: atkComponents[component] },
                mixins: [this.vueMixins],
            }),
        });
    }

    /**
   * Create a Vue instance from an external src component definition.
   *
   * @param name
   * @param component
   * @param data
   */
    createVue(name, componentName, component, data) {
        this.vues.push({
            name: name,
            instance: new Vue({
                el: name,
                data: { initData: data, isReady: true },
                components: { [componentName]: window[component] },
                mixins: [this.vueMixins],
            }),
        });
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
     * Check if all components on page are ready or fully loaded.
     */
    areComponentsReady() {
        return this.vues.filter((component) => component.instance.$root.isReady === false).length === 0;
    }
}

const vueService = new VueService();
Object.freeze(vueService);

export default vueService;
