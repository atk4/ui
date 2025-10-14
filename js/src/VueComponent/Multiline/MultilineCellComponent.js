import MultilineReadonly from './MultilineReadonlyComponent';
import MultilineTextarea from './MultilineTextareaComponent';
import AtkDatePicker from '../Share/AtkDatePickerComponent';
import AtkLookup from '../Share/AtkLookupComponent';

export default {
    name: 'AtkMultilineCell',
    template: `
        <component
            :is="getComponent()"
            v-bind="getComponentProps()"
            ref="cell"
            :name="inputName"
            v-model="inputValue"
            @update:modelValue="onInput"
        ></component>`,
    components: {
        AtkMultilineReadonly: MultilineReadonly,
        AtkMultilineTextarea: MultilineTextarea,
        AtkDatePicker: AtkDatePicker,
        AtkLookup: AtkLookup,
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
    emits: ['updateValue'],
    methods: {
        getComponent: function () {
            return this.cellData.definition.component;
        },
        getComponentProps: function () {
            if (this.getComponent() === 'AtkMultilineReadonly') {
                return { readOnlyValue: this.fieldValue };
            }

            return this.cellData.definition.componentProps;
        },
        onInput: function (value) {
            this.inputValue = value;
            this.$emit('updateValue', this.fieldName, this.inputValue);
        },
    },
};
