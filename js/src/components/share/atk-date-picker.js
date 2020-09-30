/**
 * Wrapper for v-date-picker component from V-Calendar
 * https://vcalendar.io/
 *
 * Props
 *  value: The initial date vale as a string.
 *  datePickerProps: Any of v-date-picker components props
 *  atkDateOptions:
 *    useTodayDefault: Will set picker to today when value is null, false by default.
 *    phpDateFormat: The string format for representing the date value.
 *
 *   Will emit a dateChange event when date is set.
 */

const template = '<v-date-picker v-model="date" v-bind="datePickerProps" @input="onChange"></v-date-picker>';

export default {
    name: 'atk-date-picker',
    template: template,
    props: ['value', 'datePickerProps', 'atkDateOptions'],
    data: function () {
        const { useTodayDefault = false, phpDateFormat = 'Y-m-d' } = this.atkDateOptions || {};
        return {
            useTodayDefault: useTodayDefault,
            phpDateFormat: phpDateFormat,
            date: null,
        };
    },
    mounted: function () {
        if (this.useTodayDefault && this.value === null) {
            this.date = new Date();
        } else if (this.value) {
            this.date = this.getDateFromString(this.value);
        }

        if (this.date) {
            this.$emit('dateChange', atk.phpDate(this.phpDateFormat, this.date));
        }
    },
    computed: {
    },
    methods: {
        onChange: function (date) {
            this.$emit('dateChange', atk.phpDate(this.phpDateFormat, date));
        },
        getDateFromString: function (dateString) {
            return new Date(atk.utils.date().parse(dateString));
        },
    },
};
