import Vue from 'vue';
import atkInlineEdit from '../components/inline-edit.component';
import itemSearch from '../components/item-search.component';


let atkComponents = {
  'atk-inline-edit' : atkInlineEdit,
  'atk-item-search' : itemSearch,
};

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
          components: {[component]: atkComponents[component]},
          data: {item:data},
          methods: {
            getData: function() {
              return this.item
            }
          },
          // provide method to our child component.
          // child would need to inject a method to have access.
          provide: function() {
            return {
              getRootData: this.getData
            };
          }
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
    console.log(window[component]);
    this.vues.push({name: name, instance: new Vue({
          el: name,
          components: {[componentName]: window[component]},
          data: {item:data},
          methods: {
            getData: function() {
              return this.item
            }
          },
          // provide method to our child component.
          // child would need to inject a method to have access.
          provide: function() {
            return {
              getRootData: this.getData
            };
          }
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


  getVue() {
    return Vue;
  }
}

const vueService = new VueService();
Object.freeze(vueService);

export default vueService;
