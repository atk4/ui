import $ from 'external/jquery';

/**
 * Allow user to edit a db record inline and send
 * changes to server.
 *
 * Properties need for this component are:
 * context: string, a jQuery selector where the 'loading' class will be apply by Fomantic-UI - default to the requesting element.
 * url:     string, the URL to call.
 * value:   array, array of value to send to server.
 */
export default {
    name: 'AtkInlineEdit',
    template: `
        <div :class="[options.inputCss, hasError ? 'error' : '' ]">
            <input
                :class="options.inlineCss"
                :name="options.fieldName"
                v-model="value"
                @keyup="onKeyup"
                @focus="onFocus"
                @blur="onBlur"
            />
            <i class="icon" />
        </div>`,
    props: {
        url: String,
        initValue: String,
        saveOnBlur: Boolean,
        options: Object,
    },
    data: function () {
        return {
            value: this.initValue,
            lastValueValid: this.initValue,
            hasError: false,
        };
    },
    computed: {
        isDirty: function () {
            return this.lastValueValid !== this.value;
        },
    },
    methods: {
        onFocus: function () {
            if (this.hasError) {
                this.clearError();
            } else {
                this.lastValueValid = this.value;
            }
        },
        onKeyup: function (e) {
            const key = e.keyCode;
            if (key === 13) {
                this.onEnter();
            } else if (key === 27) {
                this.onEscape();
            }
        },
        onBlur: function () {
            if (this.isDirty) {
                if (this.saveOnBlur) {
                    this.update();
                } else {
                    this.value = this.lastValueValid;
                }
            }
        },
        onEscape: function () {
            this.value = this.lastValueValid;
            this.$el.querySelector('input').blur();
        },
        onEnter: function () {
            if (this.isDirty) {
                this.update();
            }
        },
        clearError: function () {
            this.hasError = false;
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
                        that.lastValueValid = that.value;
                    }
                },
            });
        },
    },
};
