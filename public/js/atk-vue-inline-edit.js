"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-inline-edit"],{

/***/ "./src/vue-components/inline-edit.component.js":
/*!*****************************************************!*\
  !*** ./src/vue-components/inline-edit.component.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! external/jquery */ "external/jquery");
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(external_jquery__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Allow user to edit a db record inline and send
 * changes to server.
 *
 * Properties need for this component are:
 * context: string, a jQuery selector where the 'loading' class will be apply by Fomantic-UI - default to the requesting element.
 * url:     string, the URL to call.
 * value:   array, array of value to send to server.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
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
    options: Object
  },
  data: function () {
    return {
      value: this.initValue,
      lastValueValid: this.initValue,
      hasError: false
    };
  },
  computed: {
    isDirty: function () {
      return this.lastValueValid !== this.value;
    }
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
      external_jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.$el).api({
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
            that.lastValueValid = that.value;
          }
        }
      });
    }
  }
});

/***/ })

}]);
//# sourceMappingURL=atk-vue-inline-edit.js.map