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
            :modelValue="inputValue"
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
            inputValue: this.cellData.definition.component === 'SuiDropdown' // mimic https://github.com/atk4/ui/blob/6.0.0/js/src/VueComponent/Share/AtkLookupComponent.js#L44
                ? this.getDropdownValue(this.fieldValue)
                : this.fieldValue,
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
        getDropdownValue: function (value) {
            for (const option of this.cellData.definition.componentProps.options) {
                if (option.value === value) {
                    return option;
                }
            }

            return value;
        },
        onInput: function (value) {
            this.inputValue = value;
            this.$emit(
                'updateValue',
                this.fieldName,
                this.cellData.definition.component === 'SuiDropdown' // mimic https://github.com/atk4/ui/blob/ea4cc192c8/js/src/VueComponent/Share/AtkLookupComponent.js#L50
                    ? (value === null ? null : value.value)
                    : value
            );
        },
    },
};
