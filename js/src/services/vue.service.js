import Vue from 'vue';
import SuiVue from 'semantic-ui-vue';

import atkClickOutside from '../directives/click-outside.directive';
import { focus } from '../directives/commons.directive';

Vue.use(SuiVue);

const atkComponents = {
    'atk-inline-edit': () => import(/* webpackChunkName: "atk-vue-inline-edit" */'../components/inline-edit.component'),
    'atk-item-search': () => import(/* webpackChunkName: "atk-vue-item-search" */'../components/item-search.component'),
    'atk-multiline': () => import(/* webpackChunkName: "atk-vue-multiline" */'../components/multiline/multiline.component'),
    'atk-tree-item-selector': () => import(/* webpackChunkName: "atk-vue-tree-item-selector" */'../components/tree-item-selector/tree-item-selector.component'),
    'atk-query-builder': () => import(/* webpackChunkName: "atk-vue-query-builder" */'../components/query-builder/query-builder.component.vue'),
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
   *
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
                mounted: function () {
                    this.isReady = true;
                },
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
                data: { initData: data, isReady: false },
                components: { [componentName]: window[component] },
                mixins: [this.vueMixins],
                mounted: function () {
                    this.isReady = true;
                },
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
