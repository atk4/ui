import $ from 'external/jquery';

/**
 * Allow user to edit a db record inline and send
 * changes to server.
 *
 * Properties need for this component are:
 *
 * context: string, a jQuery selector where the 'loading' class will be apply by Fomantic-UI - default to the requesting element.
 * url:     string, the URL to call.
 * value:   array, array of value to send to server.
 */

const template = `
      <div :class="[options.inputCss, hasError ? 'error' : '' ]">
            <input
            :class="options.inlineCss"
            :name="options.fieldName"
            :type="options.fieldType"
            v-model="value"
            @keyup="onKeyup"
            @focus="onFocus"
            @blur="onBlur" /><i class="icon"></i>
      </div>`;

export default {
    name: 'atk-inline-edit',
    template: template,
    props: {
        url: String,
        initValue: String,
        saveOnBlur: Boolean,
        options: Object,
    },
    data: function () {
        return {
            value: this.initValue,
            temp: this.initValue,
            hasError: false,
        };
    },
    computed: {
        isDirty: function () {
            return this.temp !== this.value;
        },
    },
    methods: {
        onFocus: function () {
            if (this.hasError) {
                this.clearError();
            } else {
                this.temp = this.value;
            }
        },
        onKeyup: function (e) {
            const key = e.keyCode;
            this.clearError();
            if (key === 13) {
                this.onEnter(e);
            } else if (key === 27) {
                this.onEscape();
            }
        },
        onBlur: function () {
            if (this.isDirty && this.saveOnBlur) {
                this.update();
            } else {
                this.value = this.temp; // TODO will not save the value on 2nd edit and submit via enter
            }
        },
        onEscape: function () {
            this.value = this.temp;
            this.$el.querySelector('input').blur();
        },
        onEnter: function (e) {
            if (this.isDirty) {
                this.update();
            }
        },
        clearError: function () {
            this.hasError = false;
        },
        flashError: function (count = 4) {
            if (count === 0) {
                this.hasError = false;

                return;
            }
            this.hasError = !this.hasError;
            setTimeout(() => {
                this.flashError(count - 1);
            }, 300);
        },
        update: function () {
            const that = this;
            $(this.$el).api({
                on: 'now',
                url: this.url,
                data: { value: this.value },
                method: 'POST',
                onComplete: function (r, e) {
                    if (r.hasValidationError) {
                        that.hasError = true;
                    } else {
                        that.temp = that.value;
                    }
                },
            });
        },
    },
};
