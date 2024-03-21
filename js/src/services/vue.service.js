import $ from 'external/jquery';
import {
    createApp, camelize, capitalize, defineAsyncComponent,
} from 'vue';

const vueFomanticUiComponentNamesSet = new Set(__VUE_FOMANTICUI_COMPONENT_NAMES__); // eslint-disable-line no-undef

class VueService {
    constructor() {
        this.vues = [];
        this.vueMixins = {
            methods: {
                getData: function () {
                    return this.initData;
                },
            },
            // provide method to our child component
            // child component would need to inject a method to have access using the inject property,
            // inject: ['getRootData'],
            // once inject you can get initial data using this.getRootData()
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

    _setupComponentAutoloader(app) {
        const atkLoadingComponent = {
            name: 'AtkAutoloaderLoading',
            template: '<div><div class="ui active centered inline loader"></div></div>',
        };

        const atkErrorComponent = {
            name: 'AtkAutoloaderError',
            template: '<div class="ui negative message"><p>Error: Unable to load Vue component</p></div>',
        };

        const asyncComponentFactory = (name, component) => defineAsyncComponent({
            loader: () => {
                this.registerComponent({
                    name: name,
                    apps: [],
                    isLoaded: false,
                });

                return component().then((r) => {
                    this.markComponentLoaded(name);

                    return r;
                });
            },
            loadingComponent: atkLoadingComponent,
            errorComponent: atkErrorComponent,
            delay: 200,
            timeout: 5000,
        });

        const lazyRegisterSuiPrefixedComponent = function (registry, name) {
            // https://github.com/vuejs/core/blob/v3.2.45/packages/runtime-core/src/helpers/resolveAssets.ts#L136
            if (registry[name] === undefined && registry[camelize(name)] === undefined) {
                const namePascalized = capitalize(camelize(name));
                if (registry[namePascalized] === undefined && vueFomanticUiComponentNamesSet.has(namePascalized)) {
                    registry[namePascalized] = asyncComponentFactory(namePascalized, () => (import('vue-fomantic-ui')).then((r) => r[namePascalized])); // eslint-disable-line import/no-unresolved
                }
            }
        };
        app._context.components = new Proxy(app._context.components, {
            has: (obj, prop) => {
                lazyRegisterSuiPrefixedComponent(obj, prop);

                return obj[prop] !== undefined;
            },
            get: (obj, prop) => {
                lazyRegisterSuiPrefixedComponent(obj, prop);

                return obj[prop];
            },
        });

        app.component('FlatpickrPicker', asyncComponentFactory('FlatpickrPicker', () => import('vue-flatpickr-component')));

        app.component('AtkInlineEdit', asyncComponentFactory('AtkInlineEdit', () => import(/* webpackChunkName: 'atk-vue-inline-edit' */'../vue-components/inline-edit.component')));
        app.component('AtkItemSearch', asyncComponentFactory('AtkItemSearch', () => import(/* webpackChunkName: 'atk-vue-item-search' */'../vue-components/item-search.component')));
        app.component('AtkMultiline', asyncComponentFactory('AtkMultiline', () => import(/* webpackChunkName: 'atk-vue-multiline' */'../vue-components/multiline/multiline.component')));
        app.component('AtkTreeItemSelector', asyncComponentFactory('AtkTreeItemSelector', () => import(/* webpackChunkName: 'atk-vue-tree-item-selector' */'../vue-components/tree-item-selector/tree-item-selector.component')));
        app.component('AtkQueryBuilder', asyncComponentFactory('AtkQueryBuilder', () => import(/* webpackChunkName: 'atk-vue-query-builder' */'../vue-components/query-builder/query-builder.component')));
    }

    /**
     * Created a Vue component and add it to the vues array.
     * For root component (App) to be aware that it's children component is
     * mounted, you need to use @hook:mounted="setReady"
     */
    createAtkVue(id, componentName, data) {
        const app = this.createApp({
            data: () => ({ initData: data }),
            mixins: [this.vueMixins],
        });
        this._setupComponentAutoloader(app);

        app.mount(id);

        this.registerComponent({
            name: componentName,
            apps: [app],
            isLoaded: false,
        });
    }

    /**
     * Create a Vue instance from an external src component definition.
     */
    createVue(id, componentName, component, data) {
        const app = this.createApp({
            data: () => ({ initData: data, isReady: true }),
            mixins: [this.vueMixins],
        });
        this._setupComponentAutoloader(app);

        const def = $.extend({}, component);
        const defData = def.data;
        def.data = function () {
            const res = $.extend({}, defData.call(this));
            res.initData = data;

            return res;
        };
        app.component(componentName, def);

        app.mount(id);

        this.registerComponent({
            name: componentName,
            apps: [app],
            isLoaded: true,
        });
    }

    /**
     * Add component to vues container.
     * Group apps that are using the same component.
     */
    registerComponent(component) {
        if (this.vues[component.name] === undefined) {
            this.vues[component.name] = component;
        } else {
            this.vues[component.name].apps.push(...component.apps);
        }
    }

    /**
     * Mark a component as loaded.
     */
    markComponentLoaded(name) {
        this.vues[name].isLoaded = true;
    }

    /**
     * Check if all components on page are ready and fully loaded.
     */
    areComponentsLoaded() {
        return this.vues.filter((component) => !component.isLoaded).length === 0;
    }
}

export default Object.freeze(new VueService());
