import multilineReadOnly from './multiline-readonly.component';
import multilineTextarea from './multiline-textarea.component';
import atkDatePicker from '../share/atk-date-picker';
import atkLookup from '../share/atk-lookup';

export default {
    name: 'atk-multiline-cell',
    template: `
    <component :is="getComponent()"
        :fluid="true"
        class="fluid"
        @input="onInput"
        @onChange="onChange"
        v-model="inputValue"
        :name="inputName"
        ref="cell"
        v-bind="getComponentProps()"></component>
  `,
    components: {
        'atk-multiline-readonly': multilineReadOnly,
        'atk-multiline-textarea': multilineTextarea,
        'atk-date-picker': atkDatePicker,
        'atk-lookup': atkLookup,
    },
    props: ['cellData', 'fieldValue'],
    data: function () {
        return {
            fieldName: this.cellData.name,
            type: this.cellData.type,
            inputName: '-' + this.cellData.name,
            inputValue: this.fieldValue,
        };
    },
    methods: {
        getComponent: function () {
            return this.cellData.definition.component;
        },
        getComponentProps: function () {
            if (this.getComponent() === 'atk-multiline-readonly') {
                return { readOnlyValue: this.fieldValue };
            }

            return this.cellData.definition.componentProps;
        },
        onInput: function (value) {
            this.inputValue = this.getTypeValue(value);
            this.$emit('update-value', this.fieldName, this.inputValue);
        },
        onChange: function (value) {
            this.onInput(value);
        },
        /**
         * return input value based on their type.
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
