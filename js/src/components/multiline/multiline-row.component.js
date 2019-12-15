import multilineCell from './multiline-cell.component';

/**
 * A row component.
 * This will create a table td element using sui-table-cell.
 * The td element is created only if column as set isVisible = true;
 * The td element will add a multiline cell element.
 *  the multiline cell will set it's own template component depending on the fieldType.
 *  getValue
 *
 */
export default {
  name: 'atk-multiline-row',
  template: `
    <sui-table-row :verticalAlign="'middle'">
        <sui-table-cell width="one" textAlign="center"><input type="checkbox" @input="onToggleDelete" v-model="toDelete"></input></sui-table-cell>
        <sui-table-cell  @keydown.tab="onTab(idx)" v-for="(column, idx) in columns" :key="idx" :state="getErrorState(column)" :width="getColumnWidth(column)" :style="{overflow: 'visible'}" v-if="column.isVisible" :textAlign="getTextAlign(column)">
         <atk-multiline-cell 
           :componentName="getMapComponent(column)" 
           :cellData="column" 
           @update-value="onUpdateValue" 
           @post-value="onPostRow"
           :fieldValue="getValue(column)"
           :componentProps="getComponentProps(column)"></atk-multiline-cell>
        </sui-table-cell>
    </sui-table-row>
  `,
  props : ['fields', 'rowId', 'isDeletable', 'values', 'error'],
  data() {
    return {columns: this.fields}
  },
  components: {
    'atk-multiline-cell': multilineCell
  },
  inject: ['getRootData'],
  computed: {
    /**
     * toDelete is bind by v-model, thus we need a setter for
     * computed property to work.
     * When isDeletable is pass, will set checkbox according to it's value.
     */
    toDelete: {
      get: function() {
        return this.isDeletable;
      },
      set: function(v) {
        return v;
      }
    }
  },
  methods: {
    onTab: function(idx) {
      if (idx === this.columns.filter(column => column.isEditable).length) {
        this.$emit("onTabLastColumn");
      }
    },
    getErrorState: function(column){
      //console.log(column);
      if (this.error) {
        let error = this.error.filter(e => column.field === e.field);
        if (error.length > 0) {
          return 'error';
        }
      }
      return null;
    },
    getColumnWidth: function(column) {
      return column.fieldOptions ? column.fieldOptions.width : null;
    },
    onEdit: function () {
      this.isEditing = true;
    },
    onToggleDelete(e) {
      this.$root.$emit('toggle-delete', this.rowId);
    },
    onUpdateValue: function (field, value) {
      this.$root.$emit('update-row', this.rowId, field, value);
    },
    onPostRow: function(field) {
      this.$root.$emit('post-row', this.rowId, field);
    },
    getReadOnlyValue(column) {
      if (!column.isEditable) {
        return this.getValue(column);
      }
      return null;
    },
    getValue: function(column) {
      let temp = column.default;
      this.values.forEach(field => {
        if (column.field in field) {
            temp = field[column.field];
        }
      });
      return temp;
    },
    /**
     * Return component specific props.
     * When dropdown is use for example.
     *
     * @param column
     */
    getComponentProps: function(column) {
      let props = {};
      if (column.component === 'dropdown') {
        const values = column.fieldOptions ? column.fieldOptions.values : null;
        const userOptions = column.fieldOptions ? column.fieldOptions : {};
        const defaultOptions = {
          floating : true,
          closeOnBlur : true,
          openOnFocus : false,
          selection: true,
         };
        props = Object.assign(defaultOptions, userOptions);
        props.options = this.getEnumValues(values);
      } else {
        props = Object.assign(props, column.fieldOptions);
      }
      return props;
    },
    /**
     * Map values for Sui Dropdown.
     * Values are possible value for dropdown.
     *
     * @param values
     * @returns {{text: *, value: string, key: string}[]}
     */
    getEnumValues: function(values){
      if(values) {
        return Object.keys(values).map(key => {
          return {key: key, value: key, text: values[key]}
        });

      }
    },
    /**
     * Return proper component name based on component set.
     *
     * @param column
     * @returns {string}
     */
    getMapComponent: function (column) {
      let component;
      if (!column.isEditable){
        component = 'atk-multiline-readonly';
      } else {
        switch (column.component) {
          case 'input':
          case 'dropdown':
          case 'checkbox':
            component = 'sui-'+ column.component;
            break;
          case 'textarea':
            component = 'atk-multiline-textarea';
            break;
          default:
            component = 'sui-input';
        }
      }
      return component;
    },
    /**
     * return text alignement for cell depending on field type.
     *
     * @param column
     * @returns {string}
     */
    getTextAlign(column) {
      let align;
      let type = column.fieldOptions ? column.fieldOptions.type : 'text';
      switch(type) {
        case 'money':
        case 'integer':
        case 'number':
          align = 'right';
          break;
        default:
          align = 'left';
          break;
      }
      return align;
    }
  }
}
