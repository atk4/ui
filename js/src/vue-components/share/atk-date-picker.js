/* global flatpickr */ // loaded after main JS

/**
 * Wrapper for vue-flatpickr-component component.
 *
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Properties:
 * config: Any of Flatpickr options
 */
export default {
    name: 'AtkDatePicker',
    template: `
        <FlatpickrPicker
            :config="flatPickr"
            :modelValue="getFlatpickrValue(modelValue)"
            @update:modelValue="onUpdate"
        />`,
    props: ['config', 'modelValue'],
    data: function () {
        const config = { ...this.config };

        if (config.defaultDate && !this.modelValue) {
            config.defaultDate = new Date();
        } else if (this.modelValue) {
            config.defaultDate = this.modelValue;
        }

        if (!config.locale) {
            config.locale = flatpickr.l10ns.default;
        }

        return {
            flatPickr: config,
        };
    },
    emits: ['setDefault'],
    mounted: function () {
        // if value is not set but default date is, then emit proper string value to parent
        if (!this.modelValue && this.flatPickr.defaultDate) {
            this.onUpdate(
                this.flatPickr.defaultDate instanceof Date
                    ? flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat)
                    : this.flatPickr.defaultDate
            );
        }
    },
    methods: {
        getFlatpickrValue: function (value) {
            return value;
        },
        onUpdate: function (value) {
            this.$emit('update:modelValue', value);
        },
    },
};
