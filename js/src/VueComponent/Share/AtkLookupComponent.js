/**
 * Wrapper for Fomantic-UI dropdown component into a lookup component.
 *
 * Properties:
 * config:
 * reference: the reference field name associate with model or hasOne name. This field name will be sent along with URL callback parameter as of 'field=name'.
 * Note: The remaining config object may contain any or SuiDropdown { props: value } pair.
 *
 * modelValue: The selected value.
 * optionalValue: The initial list of options for the dropdown.
 */
export default {
    name: 'AtkLookup',
    template: `
        <SuiDropdown
            v-bind="dropdownProps"
            ref="drop"
            :modelValue="getDropdownValue(modelValue)"
            @update:modelValue="onUpdate"
        ></SuiDropdown>`,
    props: ['config', 'modelValue', 'optionalValue'],
    data: function () {
        const {
            url, reference, ...otherConfig
        } = this.config;
        otherConfig.selection = true;

        return {
            dropdownProps: otherConfig,
            url: url || null,
            field: reference,
            query: '',
            temp: '',
        };
    },
    mounted: function () {
        if (this.optionalValue) {
            this.dropdownProps.options = Array.isArray(this.optionalValue) ? this.optionalValue : [this.optionalValue];
        }
    },
    emits: ['update:modelValue'],
    methods: {
        getDropdownValue: function (value) {
            return this.dropdownProps.options.find((item) => item.value === value);
        },
        onUpdate: function (value) {
            this.$emit('update:modelValue', value.value);
        },
    },
};
