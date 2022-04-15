"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-inline-edit"],{

/***/ "./src/components/inline-edit.component.js":
/*!*************************************************!*\
  !*** ./src/components/inline-edit.component.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

/**
 * Vue component
 * Allow user to edit a db record inline and send
 * changes to server.
 *
 * Properties need for this component are:
 *
 * context: string, a jQuery selector where the 'loading' class will be apply by semantic-ui;
 *          - default to the requesting element.
 * url:     string, the url to call;
 * value:  array, array of value to send to server.
 *
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
            @blur="onBlur"/><i class="icon"></i>
      </div>`;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-inline-edit',
  template: template,
  props: {
    url: String,
    initValue: String,
    saveOnBlur: {
      type: Boolean,
      default: true
    },
    options: {
      type: Object,
      default: () => ({
        inputCss: '',
        inlineCss: '',
        fieldName: null,
        fieldType: 'text'
      })
    }
  },
  data: function () {
    return {
      value: this.initValue,
      temp: this.initValue,
      hasError: false
    };
  },
  computed: {
    isDirty: function () {
      return this.temp !== this.value;
    }
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
      if (this.isDirty && this.saveOnBlur && !this.hasError) {
        this.update();
      } else {
        this.value = this.temp;
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
      jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.$el).api({
        on: 'now',
        url: this.url,
        data: {
          value: this.value
        },
        method: 'POST',
        onComplete: function (r, e) {
          if (r.hasValidationError) {
            that.hasError = true;
          } else {
            that.temp = that.value;
          }
        }
      });
    }
  }
});

/***/ })

}]);
//# sourceMappingURL=atk-vue-inline-edit.js.map