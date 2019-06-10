
export default {
  name: 'atk-multiline-cell',
  template: ` 
    <component :is="fieldType"
        :type="inputType"
        :fluid="true" 
        class="fluid" 
        @blur="onBlur"
        @input="onInput"
        v-model="inputValue"
        :name="fieldName"
        ref="cell"><slot></slot></component>
  `,
  props: ['cellData', 'fieldType', 'fieldValue'],
  data() {
    return {
      field: this.cellData.field,
      type: this.cellData.type,
      //this field name will not get serialized and sent on form submit.
      fieldName: '-'+this.cellData.field,
      inputValue: this.fieldValue,
      dirtyValue: this.fieldValue,
    }
  },
  computed: {
    inputType() {
      let type = this.cellData.type;
      switch (type) {
        case 'string':
          type = 'text';
          break;
        case 'integer':
        case 'money':
          type = 'number';
          break;
      }

      return type;
    },
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
