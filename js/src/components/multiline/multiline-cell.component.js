import multilineReadOnly from './multiline-readonly.component';
import multilineTextarea from './multiline-textarea.component';

export default {
  name: 'atk-multiline-cell',
  template: ` 
    <component :is="componentName"
        :fluid="true"  
        class="fluid" 
        @blur="onBlur"
        @input="onInput"
        v-model="inputValue"
        :readOnlyValue="fieldValue"
        :name="fieldName"
        ref="cell"
        v-bind="componentProps"></component>
  `,
  components: {
    'atk-multiline-readonly': multilineReadOnly,
    'atk-multiline-textarea': multilineTextarea
  },
  props: ['cellData', 'componentName', 'fieldValue', 'options', 'componentProps'],
  data() {
    return {
      field: this.cellData.field,
      type: this.cellData.type,
      fieldName: '-'+this.cellData.field,
      inputValue: this.fieldValue,
      dirtyValue: this.fieldValue,
    }
  },
  computed: {
    isDirty() {
      return this.dirtyValue != this.inputValue;
    }
  },
  methods: {
    onInput: function(value) {
      this.inputValue = this.getTypeValue(value);
      this.$emit('update-value', this.field, this.inputValue);
    },
    /**
     * Tell parent row that input value has changed.
     *
     * @param e
     */
    onBlur: function(e) {
      if (this.isDirty) {
        this.$emit('post-value', this.field);
        this.dirtyValue = this.inputValue;
      }
    },
    /**
     * return input value based on their type.
     *
     * @param value
     * @returns {*}
     */
    getTypeValue(value) {
      let r = value;
      if (this.type === 'boolean') {
        r = value.target.checked;
      }
      return r;
    }
  }
}
