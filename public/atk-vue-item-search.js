"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-item-search"],{

/***/ "./src/components/item-search.component.js":
/*!*************************************************!*\
  !*** ./src/components/item-search.component.js ***!
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
 * Will allow user to send data query request to server.
 * Request should filter the data and reload the data view.
 * The request is send using semantic-ui api.
 *
 * Properties need for this component are:
 *
 * context: string, a jQuery selector where the 'loading' class will be apply by semantic-ui;
 *          - default to this component.
 * url:     string, the url to call;
 * q:       string, the initial string for the query. Useful if this search is part of the relaod.
 * reload:  string, an Id selector for jQuery, '#' is append automatically.
 *
 */

const template = `<div class="atk-item-search" :class="inputCss">
      <input class="ui"
        v-model="query"
        type="text" placeholder="Search..."
        @keyup="onKeyup"
        @keyup.esc="onEscape"
        name="atk-vue-search"/>
        <i class="atk-search-icon" :class="classIcon"></i><span style="width:12px;cursor:pointer" @click="onClear"></span>
    </div>
`;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-item-search',
  template: template,
  props: {
    context: String,
    url: String,
    q: String,
    reload: String,
    queryArg: String,
    options: {
      type: Object,
      default: () => ({
        inputTimeOut: 350,
        inputCss: ''
      })
    }
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
      atk.debounce(e => {
        if (this.query !== this.temp) {
          if (this.query === '') {
            this.query = null;
          }

          this.sendQuery();
          this.temp = this.query;
        }
      }, this.options.inputTimeOut).call(this);
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
      const options = jquery__WEBPACK_IMPORTED_MODULE_0___default().extend({}, this.extraQuery, {
        __atk_reload: this.reload,
        [this.queryArg]: this.query
      });
      const $reload = jquery__WEBPACK_IMPORTED_MODULE_0___default()('#' + this.reload);
      this.isActive = true;
      $reload.api({
        on: 'now',
        url: this.url,
        data: options,
        method: 'GET',
        stateContext: this.context ? jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.context) : jquery__WEBPACK_IMPORTED_MODULE_0___default()(this.$el),
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