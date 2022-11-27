import multilineReadonly from './multiline-readonly.component';
import multilineTextarea from './multiline-textarea.component';
import atkDatePicker from '../share/atk-date-picker';
import atkLookup from '../share/atk-lookup';

export default {
    name: 'AtkMultilineCell',
    template: `
        <component
            :is="getComponent()"
            v-bind="getComponentProps()"
            ref="cell"
            :fluid="true"
            class="fluid"
            :name="inputName"
            v-model="inputValue"
            @update:modelValue="onInput"
        ></component>`,
    components: {
        AtkMultilineReadonly: multilineReadonly,
        AtkMultilineTextarea: multilineTextarea,
        AtkDatePicker: atkDatePicker,
        AtkLookup: atkLookup,
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
