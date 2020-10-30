/**
 * Wrapper for vue-flatpickr-component component from V-Calendar
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Props
 *  config: Any of flatpickr options
 *   Will emit a dateChange event when date is set.
 */

const template = '<flat-picker v-model="date" :config="flatPickr" @on-change="onChange"></flat-picker>';

export default {
    name: 'atk-date-picker',
    template: template,
    props: ['config', 'value'],
    data: function () {
        const { useDefault, phpFormat, ...fpickr } = this.config;

        if (useDefault && !fpickr.defaultDate && !this.value) {
            fpickr.defaultDate = new Date();
        } else if (this.value && phpFormat) {
            // make sure phpFormat is also supported by flatpickr.
            fpickr.defaultDate = flatpickr.parseDate(this.value, phpFormat);
        } else if (this.value) {
            fpickr.defaultDate = this.value;
        }

        if (!fpickr.locale) {
            fpickr.locale = flatpickr.l10ns.default;
        }

        return {
            phpFormat: phpFormat,
            flatPickr: fpickr,
            date: null,
        };
    },
    mounted: function () {
        // if value is not set but default date is, then emit proper string value to parent.
        if (!this.value && this.flatPickr.defaultDate) {
            if (this.flatPickr.defaultDate instanceof Date) {
                const output = this.phpFormat
                    ? atk.phpDate(this.phpFormat, this.config.defaultDate)
                    : flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat);
                this.$emit('setDefault', output);
            } else {
                this.$emit('setDefault', this.flatPickr.defaultDate);
            }
        }
    },
    methods: {
        onChange: function (date) {
            const output = this.phpFormat
                ? atk.phpDate(this.phpFormat, date[0])
                : flatpickr.formatDate(date[0], this.flatPickr.dateFormat);
            this.$emit('dateChange', output);
        },
    },
};
