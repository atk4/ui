/**
 * Wrapper for vue-flatpickr-component component from V-Calendar
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Props
 *  config: Any of flatpickr options
 *   Will emit a dateChange event when date is set.
 */

const template = '<flat-picker v-model="date" :config="flatPickrConfig" @on-change="onChange"></flat-picker>';

export default {
    name: 'atk-date-picker',
    template: template,
    props: ['config'],
    data: function () {
        if (!this.config.locale) {
            this.config.locale = flatpickr.l10ns.default;
        }
        return {
            flatPickrConfig: this.config,
            date: null,
        };
    },
    mounted: function () {
        if (this.config.defaultDate) {
            if (this.config.defaultDate instanceof Date) {
                this.$emit('setDefault', flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat));
            } else {
                this.$emit('setDefault', this.config.defaultDate);
            }
        }
    },
    methods: {
        onChange: function (date) {
            this.$emit('dateChange', flatpickr.formatDate(date[0], this.config.dateFormat));
        },
    },
};
