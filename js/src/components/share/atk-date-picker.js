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
    props: ['config'],
    data: function () {
        const {useDefault, phpFormat, ...fpickr} = this.config;

        if (useDefault && !fpickr.defaultDate) {
            fpickr.defaultDate = new Date();
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
        if (this.flatPickr.defaultDate) {
            if (this.flatPickr.defaultDate instanceof Date) {
                const output = this.phpFormat ? atk.phpDate(this.phpFormat, this.config.defaultDate) : flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat);
                this.$emit('setDefault', output);
            } else {
                this.$emit('setDefault', this.flatPickr.defaultDate);
            }
        }
    },
    methods: {
        onChange: function (date) {
            const output = this.phpFormat ? atk.phpDate(this.phpFormat, date[0]) : flatpickr.formatDate(date[0], this.flatPickr.dateFormat);
            this.$emit('dateChange', output);
        },
    },
};
