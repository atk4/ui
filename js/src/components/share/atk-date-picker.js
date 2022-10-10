/* global flatpickr */ // loaded after main JS

/**
 * Wrapper for vue-flatpickr-component component.
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Props:
 * config: Any of flatpickr options
 *
 * Will emit a dateChange event when date is set.
 */

const template = '<flatpickr-picker v-model="date" :config="flatPickr" @on-change="onChange"></flatpickr-picker>';

export default {
    name: 'atk-date-picker',
    template: template,
    props: ['config', 'value'],
    data: function () {
        const { useDefault, ...fpickr } = this.config;

        if (useDefault && !fpickr.defaultDate && !this.value) {
            fpickr.defaultDate = new Date();
        } else if (this.value) {
            fpickr.defaultDate = this.value;
        }

        if (!fpickr.locale) {
            fpickr.locale = flatpickr.l10ns.default;
        }

        return {
            flatPickr: fpickr,
            date: null,
        };
    },
    mounted: function () {
        // if value is not set but default date is, then emit proper string value to parent.
        if (!this.value && this.flatPickr.defaultDate) {
            if (this.flatPickr.defaultDate instanceof Date) {
                this.$emit('setDefault', flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat));
            } else {
                this.$emit('setDefault', this.flatPickr.defaultDate);
            }
        }
    },
    methods: {
        onChange: function (date) {
            this.$emit('onChange', flatpickr.formatDate(date[0], this.flatPickr.dateFormat));
        },
    },
};
