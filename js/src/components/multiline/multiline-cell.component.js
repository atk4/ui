import multilineReadOnly from './multiline-readonly.component';
import multilineTextarea from './multiline-textarea.component';
import atkDatePicker from '../share/atk-date-picker';

export default {
    name: 'atk-multiline-cell',
    template: ` 
    <component :is="componentName"
        :fluid="true"  
        class="fluid" 
        @blur="onBlur"
        @input="onInput"
        @dateChange="onDateChange"
        v-model="inputValue"
        :readOnlyValue="fieldValue"
        :name="fieldName"
        ref="cell"
        v-bind="componentProps"></component>
  `,
    components: {
        'atk-multiline-readonly': multilineReadOnly,
        'atk-multiline-textarea': multilineTextarea,
        'atk-date-picker': atkDatePicker,
    },
    props: ['cellData', 'componentName', 'fieldValue', 'options', 'componentProps'],
    data: function () {
        return {
            field: this.cellData.field,
            type: this.cellData.type,
            fieldName: '-' + this.cellData.field,
            inputValue: this.fieldValue,
            dirtyValue: this.fieldValue,
        };
    },
    computed: {
        isDirty: function () {
            return this.dirtyValue !== this.inputValue;
        },
    },
    methods: {
        onInput: function (value) {
            this.inputValue = this.getTypeValue(value);
            this.$emit('update-value', this.field, this.inputValue);
        },
        onDateChange: function (value) {
            this.onInput(value);
        },
        /**
     * Tell parent row that input value has changed.
     *
     * @param e
     */
        onBlur: function (e) {
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
        getTypeValue: function (value) {
            let r = value;
            if (this.type === 'boolean') {
                r = value.target.checked;
            }
            return r;
        },
    },
};
