
const template =
  `<div class="item" :style="itemMargin">
        <i :class="toggleIcon" @click="onToggleShow" v-show="!isRoot"></i>
        <i :class="getIcon"  @click="onToggleSelect" v-show="!isRoot"></i>
        <div class="content" >
          <div @click="onToggleSelect" :style="itemCursor">{{title}}</div>
          <div class="list" v-show="open || isRoot" v-if="isParent" >
            <atk-tree-item-selector v-for="(item, index) in item.nodes" :key="item.id" :item="item" :values="values">
            </atk-tree-item-selector>
          </div>
        </div>
     </div>`;


export default {
  template: template,
  props: {item: Object, values: Array},
  inject: ['getRootData'],
  name: 'atk-tree-item-selector',
  data: function () {
    return {
      open: false,
      isRoot: this.item.id === 'atk-root',
      isInitialized: false,
      id: this.item.id,
      nodes: this.item.nodes,
      icons: {
        single: {
          on: 'circle icon',
          off: 'circle outline icon',
          indeterminate: 'dot circle outline icon'
        },
        multiple: {
          on: 'check square outline icon',
          off: 'square outline icon',
          indeterminate: 'minus square outline icon'
        }
      }
    }
  },
  created: function() {
    this.getInitData();
  },
  mounted: function() {
  },
  computed: {
    itemMargin: function() {
      return {marginLeft: (this.item.nodes && this.item.nodes.length > 0) ? this.open ? '-13px':'-10px' : null};
    },
    itemCursor: function() {
      return {cursor: this.isParent ? this.getRootData().options.mode === 'single' ? 'default' : 'pointer' : 'pointer'}
    },
    title: function() {
      return this.item.name;
    },
    isParent: function () {
      return (this.nodes && this.nodes.length > 0);
    },
    toggleIcon: function() {
      return this.isParent ? this.open ? 'caret down icon': 'caret right icon' : null;
    },
    state: function() {
      let state = 'off';
      if (this.isParent) {
        state = this.hasAllFill(this.nodes) ? 'on' : this.hasSomeFill(this.nodes) ? 'indeterminate' : 'off';
      } else if (this.isSelected(this.id)) {
        state = 'on'
      }
      return state;
    },
    getIcon: function() {
      return this.icons[this.getRootData().options.mode][this.state];
    }
  },
  methods: {
    isSelected: function(id) {
      return this.values.filter(val => val === id).length > 0;
    },
    /**
     * Get input initial data.
     */
    getInitData: function() {
      // check if input containing data is set and initialized.
      if (!this.getRootData().item.isInitialized) {
        this.getRootData().values = this.getValues();
        this.getRootData().item.isInitialized = true;
      }
    },
    getValues: function() {
      const initValues = JSON.parse(this.getInputElement().value);
      let values = [];
      if (Array.isArray(initValues)) {
        values = initValues;
      } else {
        values.push(initValues);
      }
      return values;
    },
    /**
     * Check if all children nodes are on.
     *
     * @param nodes
     * @returns {boolean}
     */
    hasAllFill: function(nodes) {
      let state = true;
      for (let i = 0; i < nodes.length; i++) {
        // check children first;
        if (nodes[i].nodes && nodes[i].nodes.length > 0) {
          if (!this.hasAllFill(nodes[i].nodes)) {
            state = false;
            break;
          }
        } else if (this.values.findIndex(id => id === nodes[i].id) === -1) {
          state = false;
          break;
        }
      }

      return state;
    },
    /**
     * Check if some children nodes are on.
     *
     * @param nodes
     * @returns {boolean}
     */
    hasSomeFill: function(nodes) {
      let state = false;
      for (let i = 0; i < nodes.length; i++) {
        // check children first;
        if (nodes[i].nodes && nodes[i].nodes.length > 0) {
          if (this.hasSomeFill(nodes[i].nodes)) {
            state = true;
            break;
          }
        }
        if (this.values.findIndex(id => id === nodes[i].id) > -1) {
          state = true;
          break;
        }
      }
      return state;
    },
    /**
     * Fire when arrow are click in order to show or hide children.
     */
    onToggleShow: function () {
      if (this.isParent) {
        this.open = !this.open
      }
    },
    /**
     * Fire when checkbox is click.
     *
     */
    onToggleSelect: function() {
      const options = this.getRootData().options;
      switch (options.mode) {
        case 'single':
          this.handleSingleSelect();
          break;
        case 'multiple':
          this.handleMultipleSelect();
          break;
      }
    },
    /**
     * Merge array and remove duplicate.
     *
     * @param arrays
     * @returns {*[]}
     */
    mergeArrays: function(...arrays) {
      let jointArray = [];

      arrays.forEach(array => {
        jointArray = [...jointArray, ...array];
      });

      return [...new Set([...jointArray])];
    },
    /**
     * Get all id from all chidren node.
     *
     * @param nodes
     * @param ids
     * @returns {Array}
     */
    collectAllChildren: function(nodes, ids = []) {
       nodes.forEach( node => {
         if (node.nodes && node.nodes.length > 0) {
           ids.concat(this.collectAllChildren(node.nodes, ids));
         } else {
           ids.push(node.id);
         }
       });
       return ids;
    },
    remove: function(values, value) {
      return values.filter( val => val != value);
    },
    /**
     * Handle a selection when in single mode.
     */
    handleSingleSelect: function() {
      let values;
      if (this.state === 'off' && !this.isParent) {
        this.getRootData().values = [this.item.id];
        this.setInput(this.item.id);
        if (this.getRootData().options.url) {
          this.postValue();
        }
      }
      if (this.isParent) {
        this.open = !this.open
      }
    },
    /**
     * Handle a selection when in multiple mode.
     */
    handleMultipleSelect: function() {
      let values;
      if (this.isParent) {
        // collect all children value
        const childValues = this.collectAllChildren(this.nodes);
        if (this.state === 'off' || this.state === 'indeterminate') {
          values = this.mergeArrays(this.values, childValues);
        } else {
          let temp = this.values;
          childValues.forEach( value => {
            temp = this.remove(temp, value);
          });
          values = temp;
        }
      } else if (this.state === 'on') {
        values = this.remove(this.values, this.item.id);
      } else if (this.state === 'off') {
        values = this.values;
        values.push(this.item.id);
      }

      this.getRootData().values = [...values];
      this.setInput(JSON.stringify(values));

      if (this.getRootData().options.url) {
        this.postValue();
      }
    },
    /**
     * Set input field with current mapped  model value.
     *
     * @param data
     */
    setInput(value) {
      // console.log('set input');
      this.getInputElement().value = value;
    },
    /**
     * Get input element set for this Item Selector.
     *
     * @returns {HTMLElement}
     */
    getInputElement() {
      return document.getElementsByName(this.getRootData().field)[0];
    },
    /**
     * Send data using callback url.
     */
    postValue: function() {
      jQuery(this.$el).parents('.' + this.getRootData().options.loader).api({
        on: 'now',
        url: this.getRootData().options.url,
        method: 'post',
        data: {data: JSON.stringify(this.getRootData().values)}
      });
    },
  }
};
