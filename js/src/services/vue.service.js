import Vue from 'vue';
import atkInlineEdit from '../components/inline-edit.component';
import itemSearch from '../components/item-search.component';
import atkTable from '../components/table/table.component';
import atkClickOutside from '../directives/click-outside.directive';
import {focus} from '../directives/commons.directive';


let atkComponents = {
  'atk-inline-edit' : atkInlineEdit,
  'atk-item-search' : itemSearch,
  'atk-table-edit'  : atkTable
};


// setup atk custom directives.
let atkDirectives = [{name: 'click-outside', def: atkClickOutside}, {name: 'focus', def: focus}];
atkDirectives.forEach(directive => {
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
    if(!VueService.instance){
      this.vues = [];
      this.eventBus = new Vue();
      this.vueMixins = {
        methods: {
          getData: function() {
            return this.initData;
          }
        },
        // provide method to our child component.
        // child component would need to inject a method to have access using the inject property,
        // inject: ['getRootData'],
        // Once inject you can get initial data using this.getRootData().
        provide: function() {
          return {
            getRootData: this.getData
          };
        }
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
    this.vues.push({name: name, instance: new Vue({
          el: name,
          data: {initData:data},
          components: {[component]: atkComponents[component]},
          mixins: [this.vueMixins],
        }
      )}
    );
  }

  /**
   * Create a Vue instance from an external src component definition.
   *
   * @param name
   * @param component
   * @param data
   */
  createVue(name, componentName, component, data) {
    this.vues.push({name: name, instance: new Vue({
          el: name,
          data: {initData:data},
          components: {[componentName]: window[component]},
          mixins: [this.vueMixins],
        }
      )}
    );
  }

  /**
   * Emit an event to the eventBus.
   * Listener to eventBus can respond to emitted event.
   *
   * @param event
   * @param data
   */
  emitEvent(event, data = {}) {
    this.eventBus.$emit(event, data);
  }

  /**
   * Register components within Vue.
   */
  useComponent(component) {
    if (window[component]) {
      Vue.use(window[component]);
      // let vcomponent = Vue.component('SuiInput').extend({props:{isFluid: true}});
      // console.log(vcomponent);
    } else {
      console.error('Unable to register component: '+ component + '. Make sure it is load correctly.');
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
}

const vueService = new VueService();
Object.freeze(vueService);

export default vueService;
