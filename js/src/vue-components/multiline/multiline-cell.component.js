import multilineReadonly from './multiline-readonly.component';
import multilineTextarea from './multiline-textarea.component';
import atkDatePicker from '../share/atk-date-picker';
import atkLookup from '../share/atk-lookup';

export default {
    name: 'atk-multiline-cell',
    template: `
        <component
            :is="getComponent()"
            v-bind="getComponentProps()"
            ref="cell"
            :fluid="true"
            class="fluid"
            @update:modelValue="onInput"
            v-model="inputValue"
            :name="inputName"
        ></component>`,
    components: {
        'atk-multiline-readonly': multilineReadonly,
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
    emits: ['update-value'],
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
            this.inputValue = value;
            this.$emit('update-value', this.fieldName, this.inputValue);
        },
    },
};
