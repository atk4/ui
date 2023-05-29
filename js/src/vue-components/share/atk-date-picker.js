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
        const { useDefault, ...otherConfig } = this.config;

        if (useDefault && !otherConfig.defaultDate && !this.modelValue) {
            otherConfig.defaultDate = new Date();
        } else if (this.modelValue) {
            otherConfig.defaultDate = this.modelValue;
        }

        if (!otherConfig.locale) {
            otherConfig.locale = flatpickr.l10ns.default;
        }

        return {
            flatPickr: otherConfig,
        };
    },
    emits: ['setDefault'],
    mounted: function () {
        // if value is not set but default date is, then emit proper string value to parent.
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
