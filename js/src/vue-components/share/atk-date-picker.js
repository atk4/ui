/* global flatpickr */ // loaded after main JS

/**
 * Wrapper for vue-flatpickr-component component.
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Properties:
 * config: Any of flatpickr options
 *
 * Will emit a dateChange event when date is set.
 */
export default {
    name: 'AtkDatePicker',
    template: '<FlatpickrPicker v-model="date" :config="flatPickr" />',
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
    emits: ['setDefault'],
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
};
