"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-item-search"],{

/***/ "./src/vue-components/item-search.component.js":
/*!*****************************************************!*\
  !*** ./src/vue-components/item-search.component.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! external/jquery */ "external/jquery");
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(external_jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");



/**
 * Will allow user to send data query request to server.
 * Request should filter the data and reload the data view.
 *
 * Properties need for this component are:
 * context: string, a jQuery selector where the 'loading' class will be apply by Fomantic-UI - default to this component.
 * url:     string, the URL to call.
 * q:       string, the initial string for the query. Useful if this search is part of the reload.
 * reload:  string, an Id selector for jQuery, '#' is append automatically.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkItemSearch',
  template: `
        <div class="atk-item-search" :class="inputCss">
            <input
                class="ui"
                v-model="query"
                type="text"
                name="atk-vue-search"
                placeholder="Search..."
                @keyup="onKeyup"
                @keyup.esc="onEscape"
            />
            <i class="atk-search-icon" :class="classIcon" />
            <span style="width: 12px; cursor: pointer;" @click="onClear" />
        </div>`,
  props: {
    context: String,
    url: String,
    q: String,
    reload: String,
    queryArg: String,
    options: Object
  },
  data: function () {
    return {
      query: this.q,
      temp: this.q,
      isActive: false,
      extraQuery: null,
      inputCss: this.options.inputCss
    };
  },
  computed: {
    classIcon: function () {
      return {
        'search icon': this.query === null || this.query === '',
        'remove icon': this.query !== null
      };
    }
  },
  methods: {
    onKeyup: function () {
      if (!this.onKeyup.debouncedFx) {
        this.onKeyup.debouncedFx = atk__WEBPACK_IMPORTED_MODULE_1__["default"].createDebouncedFx(e => {
          this.onKeyup.debouncedFx = null;
          if (this.query !== this.temp) {
            if (this.query === '') {
              this.query = null;
            }
            this.sendQuery();
            this.temp = this.query;
          }
        }, this.options.inputTimeOut);
      }
      this.onKeyup.debouncedFx.call(this);
    },
    onEscape: function () {
      if (this.query !== null) {
        this.query = null;
        this.temp = null;
        this.sendQuery();
      }
    },
    onEnter: function () {
      if (this.query !== null) {
        this.query = null;
        this.temp = null;
        this.sendQuery();
      }
    },
    onClear: function () {
      if (this.query) {
        this.query = null;
        this.temp = null;
        this.sendQuery();
      }
    },
    sendQuery: function () {
      const that = this;
      const options = external_jquery__WEBPACK_IMPORTED_MODULE_0___default().extend({}, this.extraQuery, {
        __atk_reload: this.reload,
        [this.queryArg]: this.query
      });
      const $reload = external_jquery__WEBPACK_IMPORTED_MODULE_0___default()('#' + this.reload);
      this.isActive = true;
      $reload.api({
        on: 'now',
        url: this.url,
        data: options,
        method: 'GET',
        stateContext: this.context ? external_jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.context) : external_jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.$el),
        onComplete: function (e, r) {
          that.isActive = false;
        }
      });
    }
  }
});

/***/ })

}]);
//# sourceMappingURL=atk-vue-item-search.js.map