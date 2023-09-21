"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-query-builder"],{

/***/ "./src/vue-components/share/atk-date-picker.js":
/*!*****************************************************!*\
  !*** ./src/vue-components/share/atk-date-picker.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* global flatpickr */ // loaded after main JS

/**
 * Wrapper for vue-flatpickr-component component.
 *
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Properties:
 * config: Any of Flatpickr options
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkDatePicker',
  template: `
        <FlatpickrPicker
            :config="flatPickr"
            :modelValue="getFlatpickrValue(modelValue)"
            @update:modelValue="onUpdate"
        />`,
  props: ['config', 'modelValue'],
  data: function () {
    const config = {
      ...this.config
    };
    if (config.defaultDate && !this.modelValue) {
      config.defaultDate = new Date();
    } else if (this.modelValue) {
      config.defaultDate = this.modelValue;
    }
    if (!config.locale) {
      config.locale = flatpickr.l10ns.default;
    }
    return {
      flatPickr: config
    };
  },
  emits: ['setDefault'],
  mounted: function () {
    // if value is not set but default date is, then emit proper string value to parent
    if (!this.modelValue && this.flatPickr.defaultDate) {
      this.onUpdate(this.flatPickr.defaultDate instanceof Date ? flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat) : this.flatPickr.defaultDate);
    }
  },
  methods: {
    getFlatpickrValue: function (value) {
      return value;
    },
    onUpdate: function (value) {
      this.$emit('update:modelValue', value);
    }
  }
});

/***/ }),

/***/ "./src/vue-components/share/atk-lookup.js":
/*!************************************************!*\
  !*** ./src/vue-components/share/atk-lookup.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.find.js */ "./node_modules/core-js/modules/esnext.async-iterator.find.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "./node_modules/core-js/modules/esnext.iterator.find.js");
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_2__);



/**
 * Wrapper for Fomantic-UI dropdown component into a lookup component.
 *
 * Properties:
 * config:
 * reference: the reference field name associate with model or hasOne name. This field name will be sent along with URL callback parameter as of 'field=name'.
 * Note: The remaining config object may contain any or SuiDropdown { props: value } pair.
 *
 * modelValue: The selected value.
 * optionalValue: The initial list of options for the dropdown.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkLookup',
  template: `
        <SuiDropdown
            v-bind="dropdownProps"
            ref="drop"
            :modelValue="getDropdownValue(modelValue)"
            @update:modelValue="onUpdate"
        ></SuiDropdown>`,
  props: ['config', 'modelValue', 'optionalValue'],
  data: function () {
    const {
      url,
      reference,
      ...otherConfig
    } = this.config;
    otherConfig.selection = true;
    return {
      dropdownProps: otherConfig,
      url: url || null,
      field: reference,
      query: '',
      temp: ''
    };
  },
  mounted: function () {
    if (this.optionalValue) {
      this.dropdownProps.options = Array.isArray(this.optionalValue) ? this.optionalValue : [this.optionalValue];
    }
  },
  emits: ['update:modelValue'],
  methods: {
    getDropdownValue: function (value) {
      return this.dropdownProps.options.find(item => item.value === value);
    },
    onUpdate: function (value) {
      this.$emit('update:modelValue', value.value);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=script&lang=js":
/*!************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=script&lang=js ***!
  \************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.find.js */ "./node_modules/core-js/modules/esnext.async-iterator.find.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "./node_modules/core-js/modules/esnext.iterator.find.js");
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_query_builder_src_components_QueryBuilderGroup__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-query-builder/src/components/QueryBuilderGroup */ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue");




/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'QueryBuilderGroup',
  extends: vue_query_builder_src_components_QueryBuilderGroup__WEBPACK_IMPORTED_MODULE_3__["default"],
  data: function () {
    return {
      selectedSuiRule: null
    };
  },
  methods: {
    /**
     * Add a new rule via Dropdown item.
     */
    addNewRule: function (ruleId) {
      this.selectedRule = this.rules.find(rule => rule.id === ruleId);
      if (this.selectedRule) {
        this.addRule();
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=script&lang=js":
/*!***********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=script&lang=js ***!
  \***********************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue_query_builder_src_components_QueryBuilderRule__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-query-builder/src/components/QueryBuilderRule */ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue");
/* harmony import */ var _share_atk_date_picker__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../share/atk-date-picker */ "./src/vue-components/share/atk-date-picker.js");
/* harmony import */ var _share_atk_lookup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../share/atk-lookup */ "./src/vue-components/share/atk-lookup.js");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {
    AtkDatePicker: _share_atk_date_picker__WEBPACK_IMPORTED_MODULE_1__["default"],
    AtkLookup: _share_atk_lookup__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  extends: vue_query_builder_src_components_QueryBuilderRule__WEBPACK_IMPORTED_MODULE_0__["default"],
  inject: ['getRootData'],
  data: function () {
    return {};
  },
  computed: {
    isInput: function () {
      return this.rule.type === 'text' || this.rule.type === 'numeric';
    },
    isComponent: function () {
      return this.rule.type === 'custom-component';
    },
    isRadio: function () {
      return this.rule.type === 'radio';
    },
    isCheckbox: function () {
      return this.rule.type === 'checkbox' || this.isRadio;
    },
    isSelect: function () {
      return this.rule.type === 'select';
    }
  },
  methods: {
    /**
     * Check if an input can be display in regards to:
     * it's operator and then it's type.
     *
     * @returns {boolean}
     */
    canDisplay: function (type) {
      if (this.labels.hiddenOperator.includes(this.query.operator)) {
        return false;
      }
      switch (type) {
        case 'input':
          {
            return this.isInput;
          }
        case 'checkbox':
          {
            return this.isCheckbox;
          }
        case 'select':
          {
            return this.isSelect;
          }
        case 'custom-component':
          {
            return this.isComponent;
          }
        default:
          {
            return false;
          }
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/query-builder.component.vue?vue&type=script&lang=js":
/*!********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/query-builder.component.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue_query_builder_src_VueQueryBuilder__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-query-builder/src/VueQueryBuilder */ "./node_modules/vue-query-builder/src/VueQueryBuilder.vue");
/* harmony import */ var _fomantic_ui_group_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./fomantic-ui-group.component */ "./src/vue-components/query-builder/fomantic-ui-group.component.vue");
/* harmony import */ var _fomantic_ui_rule_component__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./fomantic-ui-rule.component */ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'QueryBuilder',
  components: {
    VueQueryBuilder: vue_query_builder_src_VueQueryBuilder__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  props: {
    groupComponent: {
      type: Object,
      default: _fomantic_ui_group_component__WEBPACK_IMPORTED_MODULE_1__["default"]
    },
    ruleComponent: {
      type: Object,
      default: _fomantic_ui_rule_component__WEBPACK_IMPORTED_MODULE_2__["default"]
    },
    data: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      query: this.data.query ?? {},
      rules: this.data.rules ?? [],
      name: this.data.name ?? '',
      maxDepth: this.data.maxDepth ?? 1,
      labels: this.getLabels(this.data.labels),
      form: this.data.form,
      debug: this.data.debug ?? false
    };
  },
  computed: {
    valueJson: function () {
      return JSON.stringify(this.query, null);
    }
  },
  methods: {
    /**
     * Return default label and option.
     */
    getLabels: function (labels) {
      labels = labels || {};
      return {
        matchType: 'Match Type',
        matchTypes: [{
          id: 'AND',
          label: 'And'
        }, {
          id: 'OR',
          label: 'Or'
        }],
        addRule: 'Add Rule',
        removeRuleClass: 'small icon times',
        addGroup: 'Add Group',
        removeGroupClass: 'small icon times',
        textInputPlaceholder: 'value',
        spaceRule: 'fitted',
        // can be fitted, compact or padded
        hiddenOperator: ['is empty', 'is not empty'],
        // a list of operators that when select, will hide user input
        ...labels
      };
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3 ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");

const _hoisted_1 = {
  class: "ui grid"
};
const _hoisted_2 = {
  class: "fourteen wide column"
};
const _hoisted_3 = {
  class: "ui horizontal list"
};
const _hoisted_4 = {
  class: "item"
};
const _hoisted_5 = {
  class: "ui inline"
};
const _hoisted_6 = {
  class: "item"
};
const _hoisted_7 = ["value"];
const _hoisted_8 = {
  class: "item"
};
const _hoisted_9 = {
  class: "rule-actions"
};
const _hoisted_10 = {
  class: "two wide right aligned column"
};
const _hoisted_11 = {
  class: "vbq-group-body content"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SuiDropdownItem = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("SuiDropdownItem");
  const _component_SuiDropdownMenu = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("SuiDropdownMenu");
  const _component_SuiDropdown = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("SuiDropdown");
  const _component_QueryBuilderChildren = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("QueryBuilderChildren");
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["vqb-group ui fluid card", [_ctx.labels.spaceRule, 'depth-' + _ctx.depth]])
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["vbq-group-heading content", 'depth-' + _ctx.depth])
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h4", _hoisted_5, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.labels.matchType), 1 /* TEXT */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.query.logicalOperator = $event),
    class: "atk-qb-select"
  }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.labels.matchTypes, label => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
      key: label.id,
      value: label.id
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(label.label), 9 /* TEXT, PROPS */, _hoisted_7);
  }), 128 /* KEYED_FRAGMENT */))], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.logicalOperator]])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_8, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_SuiDropdown, {
    text: _ctx.labels.addRule,
    class: "ui mini basic button atk-qb-rule-select",
    selection: ""
  }, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_SuiDropdownMenu, {
      class: "atk-qb-rule-select-menu"
    }, {
      default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rules, rule => {
        return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_SuiDropdownItem, {
          key: rule.id,
          text: rule.label,
          onClick: $event => $options.addNewRule(rule.id)
        }, null, 8 /* PROPS */, ["text", "onClick"]);
      }), 128 /* KEYED_FRAGMENT */))]),

      _: 1 /* STABLE */
    })]),

    _: 1 /* STABLE */
  }, 8 /* PROPS */, ["text"]), _ctx.depth < _ctx.maxDepth ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("button", {
    key: 0,
    type: "button",
    class: "ui mini basic button",
    onClick: _cache[1] || (_cache[1] = function () {
      return _ctx.addGroup && _ctx.addGroup(...arguments);
    })
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.labels.addGroup), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [_ctx.depth > 1 ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("i", {
    key: 0,
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["atk-qb-remove", _ctx.labels.removeGroupClass]),
    onClick: _cache[2] || (_cache[2] = function () {
      return _ctx.remove && _ctx.remove(...arguments);
    })
  }, null, 2 /* CLASS */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])], 2 /* CLASS */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_QueryBuilderChildren, (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)(_ctx.$props)), null, 16 /* FULL_PROPS */)])], 2 /* CLASS */);
}

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=template&id=70644af6":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=template&id=70644af6 ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");

const _hoisted_1 = ["data-name"];
const _hoisted_2 = {
  class: "content"
};
const _hoisted_3 = {
  class: "ui grid"
};
const _hoisted_4 = {
  class: "middle aligned row atk-qb"
};
const _hoisted_5 = {
  class: "thirteen wide column"
};
const _hoisted_6 = {
  class: "ui horizontal list"
};
const _hoisted_7 = {
  class: "item vqb-rule-label"
};
const _hoisted_8 = {
  class: ""
};
const _hoisted_9 = {
  key: 0,
  class: "item vqb-rule-operand"
};
const _hoisted_10 = {
  key: 1,
  class: "item vqb-rule-operator"
};
const _hoisted_11 = ["value"];
const _hoisted_12 = {
  class: "item vqb-rule-input"
};
const _hoisted_13 = {
  key: 0,
  class: "ui small input atk-qb"
};
const _hoisted_14 = ["type", "placeholder"];
const _hoisted_15 = {
  inline: "",
  class: "atk-qb"
};
const _hoisted_16 = ["value"];
const _hoisted_17 = {
  key: 3,
  class: "ui small input atk-qb"
};
const _hoisted_18 = {
  class: "right aligned three wide column"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SuiCheckbox = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("SuiCheckbox");
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["vqb-rule ui fluid card", _ctx.labels.spaceRule]),
    "data-name": _ctx.rule.id
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_5, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", _hoisted_8, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.rule.label), 1 /* TEXT */)]), _ctx.rule.operands !== undefined ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_9, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.query.operand = $event),
    class: "atk-qb-select"
  }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.operands, operand => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
      key: operand
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(operand), 1 /* TEXT */);
  }), 128 /* KEYED_FRAGMENT */))], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.operand]])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), _ctx.rule.operators !== undefined && _ctx.rule.operators.length > 1 ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
    "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.query.operator = $event),
    class: "atk-qb-select"
  }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.operators, operator => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
      key: operator,
      value: operator
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(operator), 9 /* TEXT, PROPS */, _hoisted_11);
  }), 128 /* KEYED_FRAGMENT */))], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.operator]])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_12, [$options.canDisplay('input') ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_13, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
    "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => _ctx.query.value = $event),
    type: _ctx.rule.inputType === 'number' ? 'text' : _ctx.rule.inputType,
    placeholder: _ctx.labels.textInputPlaceholder
  }, null, 8 /* PROPS */, _hoisted_14), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelDynamic, _ctx.query.value]])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $options.canDisplay('checkbox') ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
    key: 1
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" TODO <SuiFormFields "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_15, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.choices, choice => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
      key: choice.value,
      class: "field"
    }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" TODO radio support in https://github.com/nightswinger/vue-fomantic-ui/blob/v0.13.0/src/modules/Checkbox/Checkbox.tsx "), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_SuiCheckbox, {
      modelValue: _ctx.query.value,
      "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => _ctx.query.value = $event),
      label: choice.label,
      radio: $options.isRadio,
      value: choice.value
    }, null, 8 /* PROPS */, ["modelValue", "label", "radio", "value"])]);
  }), 128 /* KEYED_FRAGMENT */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" TODO </SuiFormFields> ")])], 64 /* STABLE_FRAGMENT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $options.canDisplay('select') ? (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)(((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("select", {
    key: 2,
    "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => _ctx.query.value = $event),
    class: "atk-qb-select"
  }, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.choices, choice => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
      key: choice.value,
      value: choice.value
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(choice.label), 9 /* TEXT, PROPS */, _hoisted_16);
  }), 128 /* KEYED_FRAGMENT */))], 512 /* NEED_PATCH */)), [[vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.value]]) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), $options.canDisplay('custom-component') ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_17, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)(_ctx.rule.component), {
    modelValue: _ctx.query.value,
    "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => _ctx.query.value = $event),
    config: _ctx.rule.componentProps,
    optionalValue: _ctx.query.option
  }, null, 8 /* PROPS */, ["modelValue", "config", "optionalValue"]))])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_18, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([_ctx.labels.removeRuleClass, "atk-qb-remove"]),
    onClick: _cache[6] || (_cache[6] = function () {
      return _ctx.remove && _ctx.remove(...arguments);
    })
  }, null, 2 /* CLASS */)])])])])], 10 /* CLASS, PROPS */, _hoisted_1);
}

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/query-builder.component.vue?vue&type=template&id=5e810cb3":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/query-builder.component.vue?vue&type=template&id=5e810cb3 ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");

const _hoisted_1 = {
  class: ""
};
const _hoisted_2 = ["form", "name", "value"];
const _hoisted_3 = {
  key: 0
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_VueQueryBuilder = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("VueQueryBuilder");
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
    form: _ctx.form,
    name: _ctx.name,
    type: "hidden",
    value: $options.valueJson
  }, null, 8 /* PROPS */, _hoisted_2), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_VueQueryBuilder, {
    modelValue: _ctx.query,
    "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.query = $event),
    groupComponent: $props.groupComponent,
    ruleComponent: $props.ruleComponent,
    rules: _ctx.rules,
    maxDepth: _ctx.maxDepth,
    labels: _ctx.labels
  }, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(slotProps => [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)($props.groupComponent), (0,vue__WEBPACK_IMPORTED_MODULE_0__.mergeProps)(slotProps, {
      query: _ctx.query,
      "onUpdate:query": _cache[0] || (_cache[0] = $event => _ctx.query = $event)
    }), null, 16 /* FULL_PROPS */, ["query"]))]),
    _: 1 /* STABLE */
  }, 8 /* PROPS */, ["modelValue", "groupComponent", "ruleComponent", "rules", "maxDepth", "labels"]), _ctx.debug ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("pre", _hoisted_3, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(JSON.stringify(_ctx.query, null, 2)), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)]);
}

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../css-loader/dist/runtime/sourceMaps.js */ "./node_modules/css-loader/dist/runtime/sourceMaps.js");
/* harmony import */ var _css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
// Imports


var ___CSS_LOADER_EXPORT___ = _css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.vue-query-builder .vqb-group .rule-actions {\n  margin-bottom: 20px;\n}\n.vue-query-builder .vqb-rule {\n  margin-top: 15px;\n  margin-bottom: 15px;\n  background-color: #f5f5f5;\n  border-color: #ddd;\n  padding: 15px;\n}\n.vue-query-builder .vqb-group.depth-1 .vqb-rule,\n.vue-query-builder .vqb-group.depth-2 {\n  border-left: 2px solid #8bc34a;\n}\n.vue-query-builder .vqb-group.depth-2 .vqb-rule,\n.vue-query-builder .vqb-group.depth-3 {\n  border-left: 2px solid #00bcd4;\n}\n.vue-query-builder .vqb-group.depth-3 .vqb-rule,\n.vue-query-builder .vqb-group.depth-4 {\n  border-left: 2px solid #ff5722;\n}\n.vue-query-builder .close {\n  opacity: 1;\n  color: rgb(150, 150, 150);\n}\n@media (min-width: 768px) {\n.vue-query-builder .vqb-rule.form-inline .form-group {\n    display: block;\n}\n}\n", "",{"version":3,"sources":["webpack://./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue"],"names":[],"mappings":";AA0FA;EACE,mBAAmB;AACrB;AAEA;EACE,gBAAgB;EAChB,mBAAmB;EACnB,yBAAyB;EACzB,kBAAkB;EAClB,aAAa;AACf;AAEA;;EAEE,8BAA8B;AAChC;AAEA;;EAEE,8BAA8B;AAChC;AAEA;;EAEE,8BAA8B;AAChC;AAEA;EACE,UAAU;EACV,yBAAyB;AAC3B;AAEA;AACE;IACE,cAAc;AAChB;AACF","sourcesContent":["<template>\n  <!-- eslint-disable vue/no-v-html -->\n  <div class=\"vqb-group card\" :class=\"'depth-' + depth.toString()\">\n    <div class=\"vqb-group-heading card-header\">\n      <div class=\"match-type-container row gy-2 gx-3 align-items-center\">\n        <div class=\"col-auto\">\n          <label class=\"me-2\" for=\"vqb-match-type\">\n            {{ labels.matchType }}\n          </label>\n        </div>\n        <div class=\"col-auto\">\n          <select\n            id=\"vqb-match-type\"\n            v-model=\"query.logicalOperator\"\n            class=\"form-select\"\n          >\n            <option\n              v-for=\"label in labels.matchTypes\"\n              :key=\"label.id\"\n              :value=\"label.id\"\n            >\n              {{ label.label }}\n            </option>\n          </select>\n        </div>\n        <div class=\"col-auto\" v-if=\"depth > 1\">\n          <button\n            type=\"button\"\n            class=\"btn-close btn-small\"\n            @click=\"remove\"\n          ></button>\n        </div>\n      </div>\n    </div>\n\n    <div class=\"vqb-group-body card-body\">\n      <div class=\"rule-actions\">\n        <div class=\"row gy-2 gx-3 align-items-center\">\n          <div class=\"col-auto\">\n            <select\n              :value=\"selectedRuleId\"\n              @input=\"updateRule\"\n              class=\"form-select me-2\"\n            >\n              <option v-for=\"rule in rules\" :key=\"rule.id\" :value=\"rule.id\">\n                {{ rule.label }}\n              </option>\n            </select>\n          </div>\n          <div class=\"col-auto\">\n            <button\n              type=\"button\"\n              class=\"btn btn-secondary me-2\"\n              @click=\"addRule\"\n            >\n              {{ labels.addRule }}\n            </button>\n          </div>\n          <div class=\"col-auto\">\n            <button\n              v-if=\"depth < maxDepth\"\n              type=\"button\"\n              class=\"btn btn-secondary\"\n              @click=\"addGroup\"\n            >\n              {{ labels.addGroup }}\n            </button>\n          </div>\n        </div>\n      </div>\n\n      <query-builder-children v-bind=\"$props\" />\n    </div>\n  </div>\n</template>\n\n<script>\nimport QueryBuilderGroup from \"../../components/QueryBuilderGroup\";\nimport QueryBuilderChildren from \"../../components/QueryBuilderChildren\";\nexport default {\n  name: \"QueryBuilderGroup\",\n\n  components: { QueryBuilderChildren },\n\n  extends: QueryBuilderGroup,\n  methods: {},\n};\n</script>\n\n<style>\n.vue-query-builder .vqb-group .rule-actions {\n  margin-bottom: 20px;\n}\n\n.vue-query-builder .vqb-rule {\n  margin-top: 15px;\n  margin-bottom: 15px;\n  background-color: #f5f5f5;\n  border-color: #ddd;\n  padding: 15px;\n}\n\n.vue-query-builder .vqb-group.depth-1 .vqb-rule,\n.vue-query-builder .vqb-group.depth-2 {\n  border-left: 2px solid #8bc34a;\n}\n\n.vue-query-builder .vqb-group.depth-2 .vqb-rule,\n.vue-query-builder .vqb-group.depth-3 {\n  border-left: 2px solid #00bcd4;\n}\n\n.vue-query-builder .vqb-group.depth-3 .vqb-rule,\n.vue-query-builder .vqb-group.depth-4 {\n  border-left: 2px solid #ff5722;\n}\n\n.vue-query-builder .close {\n  opacity: 1;\n  color: rgb(150, 150, 150);\n}\n\n@media (min-width: 768px) {\n  .vue-query-builder .vqb-rule.form-inline .form-group {\n    display: block;\n  }\n}\n</style>\n"],"sourceRoot":""}]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/sourceMaps.js */ "./node_modules/css-loader/dist/runtime/sourceMaps.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
// Imports


var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.vue-query-builder .vqb-group .rule-actions {\n        margin-bottom: 0px;\n}\n.vue-query-builder .vqb-rule {\n        margin-top: 0px;\n        margin-bottom: 0px;\n        padding: 0px;\n}\n.atk-qb-select, .ui.form select.atk-qb-select {\n       padding: 2px 6px 4px 4px;\n}\n.atk-qb-remove {\n        cursor: pointer;\n        color: rgba(0, 0, 0, 0.6);\n}\n.ui.selection.dropdown.atk-qb-rule-select {\n        background-color: rgba(0, 0, 0, 0);\n}\n.ui.selection.dropdown .atk-qb-rule-select-menu {\n        width: max-content;\n        z-index: 1000;\n}\n.vbq-group-heading > .ui.grid > .column:not(.row) {\n        padding-bottom: 0.5em;\n        padding-top: 0.5em;\n}\n.vue-query-builder .ui.card.compact {\n        margin-top: 0.5em;\n        margin-bottom: 0.5em;\n}\n.vue-query-builder .ui.card.fitted {\n        margin-top: 0em;\n        margin-bottom: 0em;\n}\n.vue-query-builder .ui.card.padded {\n        margin-top: 1em;\n        margin-bottom: 1em;\n}\n.ui.card > .vbq-group-heading.content {\n        background-color: #f3f4f5;\n}\n.vue-query-builder .vqb-group.depth-1 .vqb-rule,\n    .vue-query-builder .vqb-group.depth-2 {\n        border-left: 2px solid #8bc34a;\n}\n.vue-query-builder .vqb-group.depth-2 .vqb-rule,\n    .vue-query-builder .vqb-group.depth-3 {\n        border-left: 2px solid #00bcd4;\n}\n.vue-query-builder .vqb-group.depth-3 .vqb-rule,\n    .vue-query-builder .vqb-group.depth-4 {\n        border-left: 2px solid #ff5722;\n}\n", "",{"version":3,"sources":["webpack://./src/vue-components/query-builder/fomantic-ui-group.component.vue"],"names":[],"mappings":";AAuGI;QACI,kBAAkB;AACtB;AAEA;QACI,eAAe;QACf,kBAAkB;QAClB,YAAY;AAChB;AAEA;OACG,wBAAwB;AAC3B;AACA;QACI,eAAe;QACf,yBAAyB;AAC7B;AACA;QACI,kCAAkC;AACtC;AACA;QACI,kBAAkB;QAClB,aAAa;AACjB;AACA;QACI,qBAAqB;QACrB,kBAAkB;AACtB;AACA;QACI,iBAAiB;QACjB,oBAAoB;AACxB;AACA;QACI,eAAe;QACf,kBAAkB;AACtB;AACA;QACI,eAAe;QACf,kBAAkB;AACtB;AACA;QACI,yBAAyB;AAC7B;AACA;;QAEI,8BAA8B;AAClC;AACA;;QAEI,8BAA8B;AAClC;AACA;;QAEI,8BAA8B;AAClC","sourcesContent":["<template>\n    <div\n        class=\"vqb-group ui fluid card\"\n        :class=\"[labels.spaceRule, 'depth-' + depth]\"\n    >\n        <div\n            class=\"vbq-group-heading content\"\n            :class=\"'depth-' + depth\"\n        >\n            <div class=\"ui grid\">\n                <div class=\"fourteen wide column\">\n                    <div class=\"ui horizontal list\">\n                        <div class=\"item\">\n                            <h4 class=\"ui inline\">\n                                {{ labels.matchType }}\n                            </h4>\n                        </div>\n                        <div class=\"item\">\n                            <select\n                                v-model=\"query.logicalOperator\"\n                                class=\"atk-qb-select\"\n                            >\n                                <option\n                                    v-for=\"label in labels.matchTypes\"\n                                    :key=\"label.id\"\n                                    :value=\"label.id\"\n                                >\n                                    {{ label.label }}\n                                </option>\n                            </select>\n                        </div>\n                        <div class=\"item\">\n                            <div class=\"rule-actions\">\n                                <div>\n                                    <SuiDropdown\n                                        :text=\"labels.addRule\"\n                                        class=\"ui mini basic button atk-qb-rule-select\"\n                                        selection\n                                    >\n                                        <SuiDropdownMenu class=\"atk-qb-rule-select-menu\">\n                                            <SuiDropdownItem\n                                                v-for=\"rule in rules\"\n                                                :key=\"rule.id\"\n                                                :text=\"rule.label\"\n                                                @click=\"addNewRule(rule.id)\"\n                                            />\n                                        </SuiDropdownMenu>\n                                    </SuiDropdown>\n                                    <button\n                                        v-if=\"depth < maxDepth\"\n                                        type=\"button\"\n                                        class=\"ui mini basic button\"\n                                        @click=\"addGroup\"\n                                    >\n                                        {{ labels.addGroup }}\n                                    </button>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"two wide right aligned column\">\n                    <i\n                        v-if=\"depth > 1\"\n                        class=\"atk-qb-remove\"\n                        :class=\"labels.removeGroupClass\"\n                        @click=\"remove\"\n                    />\n                </div>\n            </div>\n        </div>\n        <div class=\"vbq-group-body content\">\n            <QueryBuilderChildren v-bind=\"$props\" />\n        </div>\n    </div>\n</template>\n\n<script>\nimport VueQueryBuilderGroup from 'vue-query-builder/src/components/QueryBuilderGroup';\n\nexport default {\n    name: 'QueryBuilderGroup',\n    extends: VueQueryBuilderGroup,\n    data: function () {\n        return {\n            selectedSuiRule: null,\n        };\n    },\n    methods: {\n        /**\n         * Add a new rule via Dropdown item.\n         */\n        addNewRule: function (ruleId) {\n            this.selectedRule = this.rules.find((rule) => rule.id === ruleId);\n            if (this.selectedRule) {\n                this.addRule();\n            }\n        },\n    },\n};\n</script>\n\n<style>\n    .vue-query-builder .vqb-group .rule-actions {\n        margin-bottom: 0px;\n    }\n\n    .vue-query-builder .vqb-rule {\n        margin-top: 0px;\n        margin-bottom: 0px;\n        padding: 0px;\n    }\n\n    .atk-qb-select, .ui.form select.atk-qb-select {\n       padding: 2px 6px 4px 4px;\n    }\n    .atk-qb-remove {\n        cursor: pointer;\n        color: rgba(0, 0, 0, 0.6);\n    }\n    .ui.selection.dropdown.atk-qb-rule-select {\n        background-color: rgba(0, 0, 0, 0);\n    }\n    .ui.selection.dropdown .atk-qb-rule-select-menu {\n        width: max-content;\n        z-index: 1000;\n    }\n    .vbq-group-heading > .ui.grid > .column:not(.row) {\n        padding-bottom: 0.5em;\n        padding-top: 0.5em;\n    }\n    .vue-query-builder .ui.card.compact {\n        margin-top: 0.5em;\n        margin-bottom: 0.5em;\n    }\n    .vue-query-builder .ui.card.fitted {\n        margin-top: 0em;\n        margin-bottom: 0em;\n    }\n    .vue-query-builder .ui.card.padded {\n        margin-top: 1em;\n        margin-bottom: 1em;\n    }\n    .ui.card > .vbq-group-heading.content {\n        background-color: #f3f4f5;\n    }\n    .vue-query-builder .vqb-group.depth-1 .vqb-rule,\n    .vue-query-builder .vqb-group.depth-2 {\n        border-left: 2px solid #8bc34a;\n    }\n    .vue-query-builder .vqb-group.depth-2 .vqb-rule,\n    .vue-query-builder .vqb-group.depth-3 {\n        border-left: 2px solid #00bcd4;\n    }\n    .vue-query-builder .vqb-group.depth-3 .vqb-rule,\n    .vue-query-builder .vqb-group.depth-4 {\n        border-left: 2px solid #ff5722;\n    }\n</style>\n"],"sourceRoot":""}]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css":
/*!*********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/sourceMaps.js */ "./node_modules/css-loader/dist/runtime/sourceMaps.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);
// Imports


var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_sourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.ui.input.atk-qb > input, .ui.input.atk-qb span > input, .ui.form .input.atk-qb {\n        padding: 6px;\n}\n.ui.grid > .row.atk-qb {\n        padding: 8px 0px;\n        min-height: 62px;\n}\n.inline.fields.atk-qb, .ui.form .inline.fields.atk-qb {\n        margin: 0px;\n}\n.atk-qb-date-picker {\n        border: 1px solid rgba(34, 36, 38, 0.15);\n}\ninput[type=input].atk-qb-date-picker:focus {\n        border-color: #85b7d9;\n}\n.ui.card.vqb-rule > .content {\n        padding-bottom: 0.5em;\n        padding-top: 0.5em;\n        background-color: #f3f4f5;\n}\n", "",{"version":3,"sources":["webpack://./src/vue-components/query-builder/fomantic-ui-rule.component.vue"],"names":[],"mappings":";AA4LI;QACI,YAAY;AAChB;AACA;QACI,gBAAgB;QAChB,gBAAgB;AACpB;AACA;QACI,WAAW;AACf;AACA;QACI,wCAAwC;AAC5C;AACA;QACI,qBAAqB;AACzB;AACA;QACI,qBAAqB;QACrB,kBAAkB;QAClB,yBAAyB;AAC7B","sourcesContent":["<template>\n    <div\n        class=\"vqb-rule ui fluid card\"\n        :class=\"labels.spaceRule\"\n        :data-name=\"rule.id\"\n    >\n        <div class=\"content\">\n            <div class=\"ui grid\">\n                <div class=\"middle aligned row atk-qb\">\n                    <div class=\"thirteen wide column\">\n                        <div class=\"ui horizontal list\">\n                            <div class=\"item vqb-rule-label\">\n                                <h5 class>\n                                    {{ rule.label }}\n                                </h5>\n                            </div>\n                            <div\n                                v-if=\"rule.operands !== undefined\"\n                                class=\"item vqb-rule-operand\"\n                            >\n                                <select\n                                    v-model=\"query.operand\"\n                                    class=\"atk-qb-select\"\n                                >\n                                    <option\n                                        v-for=\"operand in rule.operands\"\n                                        :key=\"operand\"\n                                    >\n                                        {{ operand }}\n                                    </option>\n                                </select>\n                            </div>\n                            <div\n                                v-if=\"rule.operators !== undefined && rule.operators.length > 1\"\n                                class=\"item vqb-rule-operator\"\n                            >\n                                <select\n                                    v-model=\"query.operator\"\n                                    class=\"atk-qb-select\"\n                                >\n                                    <option\n                                        v-for=\"operator in rule.operators\"\n                                        :key=\"operator\"\n                                        :value=\"operator\"\n                                    >\n                                        {{ operator }}\n                                    </option>\n                                </select>\n                            </div>\n                            <div class=\"item vqb-rule-input\">\n                                <template v-if=\"canDisplay('input')\">\n                                    <div class=\"ui small input atk-qb\">\n                                        <input\n                                            v-model=\"query.value\"\n                                            :type=\"rule.inputType === 'number' ? 'text' : rule.inputType\"\n                                            :placeholder=\"labels.textInputPlaceholder\"\n                                        >\n                                    </div>\n                                </template>\n                                <template v-if=\"canDisplay('checkbox')\">\n                                    <!-- TODO <SuiFormFields -->\n                                    <div\n                                        inline\n                                        class=\"atk-qb\"\n                                    >\n                                        <div\n                                            v-for=\"choice in rule.choices\"\n                                            :key=\"choice.value\"\n                                            class=\"field\"\n                                        >\n                                            <!-- TODO radio support in https://github.com/nightswinger/vue-fomantic-ui/blob/v0.13.0/src/modules/Checkbox/Checkbox.tsx -->\n                                            <SuiCheckbox\n                                                v-model=\"query.value\"\n                                                :label=\"choice.label\"\n                                                :radio=\"isRadio\"\n                                                :value=\"choice.value\"\n                                            />\n                                        </div>\n                                    <!-- TODO </SuiFormFields> -->\n                                    </div>\n                                </template>\n                                <template v-if=\"canDisplay('select')\">\n                                    <select\n                                        v-model=\"query.value\"\n                                        class=\"atk-qb-select\"\n                                    >\n                                        <option\n                                            v-for=\"choice in rule.choices\"\n                                            :key=\"choice.value\"\n                                            :value=\"choice.value\"\n                                        >\n                                            {{ choice.label }}\n                                        </option>\n                                    </select>\n                                </template>\n                                <template v-if=\"canDisplay('custom-component')\">\n                                    <div class=\"ui small input atk-qb\">\n                                        <component\n                                            :is=\"rule.component\"\n                                            v-model=\"query.value\"\n                                            :config=\"rule.componentProps\"\n                                            :optionalValue=\"query.option\"\n                                        />\n                                    </div>\n                                </template>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"right aligned three wide column\">\n                        <i\n                            :class=\"labels.removeRuleClass\"\n                            class=\"atk-qb-remove\"\n                            @click=\"remove\"\n                        />\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</template>\n\n<script>\nimport VueQueryBuilderRule from 'vue-query-builder/src/components/QueryBuilderRule';\nimport AtkDatePicker from '../share/atk-date-picker';\nimport AtkLookup from '../share/atk-lookup';\n\nexport default {\n    components: {\n        AtkDatePicker: AtkDatePicker,\n        AtkLookup: AtkLookup,\n    },\n    extends: VueQueryBuilderRule,\n    inject: ['getRootData'],\n    data: function () {\n        return {};\n    },\n    computed: {\n        isInput: function () {\n            return this.rule.type === 'text' || this.rule.type === 'numeric';\n        },\n        isComponent: function () {\n            return this.rule.type === 'custom-component';\n        },\n        isRadio: function () {\n            return this.rule.type === 'radio';\n        },\n        isCheckbox: function () {\n            return this.rule.type === 'checkbox' || this.isRadio;\n        },\n        isSelect: function () {\n            return this.rule.type === 'select';\n        },\n    },\n    methods: {\n        /**\n         * Check if an input can be display in regards to:\n         * it's operator and then it's type.\n         *\n         * @returns {boolean}\n         */\n        canDisplay: function (type) {\n            if (this.labels.hiddenOperator.includes(this.query.operator)) {\n                return false;\n            }\n\n            switch (type) {\n                case 'input': {\n                    return this.isInput;\n                }\n                case 'checkbox': {\n                    return this.isCheckbox;\n                }\n                case 'select': {\n                    return this.isSelect;\n                }\n                case 'custom-component': {\n                    return this.isComponent;\n                }\n                default: {\n                    return false;\n                }\n            }\n        },\n    },\n};\n</script>\n\n<style>\n    .ui.input.atk-qb > input, .ui.input.atk-qb span > input, .ui.form .input.atk-qb {\n        padding: 6px;\n    }\n    .ui.grid > .row.atk-qb {\n        padding: 8px 0px;\n        min-height: 62px;\n    }\n    .inline.fields.atk-qb, .ui.form .inline.fields.atk-qb {\n        margin: 0px;\n    }\n    .atk-qb-date-picker {\n        border: 1px solid rgba(34, 36, 38, 0.15);\n    }\n    input[type=input].atk-qb-date-picker:focus {\n        border-color: #85b7d9;\n    }\n    .ui.card.vqb-rule > .content {\n        padding-bottom: 0.5em;\n        padding-top: 0.5em;\n        background-color: #f3f4f5;\n    }\n</style>\n"],"sourceRoot":""}]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/api.js":
/*!*****************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/api.js ***!
  \*****************************************************/
/***/ ((module) => {



/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
module.exports = function (cssWithMappingToString) {
  var list = [];

  // return the list of modules as css string
  list.toString = function toString() {
    return this.map(function (item) {
      var content = "";
      var needLayer = typeof item[5] !== "undefined";
      if (item[4]) {
        content += "@supports (".concat(item[4], ") {");
      }
      if (item[2]) {
        content += "@media ".concat(item[2], " {");
      }
      if (needLayer) {
        content += "@layer".concat(item[5].length > 0 ? " ".concat(item[5]) : "", " {");
      }
      content += cssWithMappingToString(item);
      if (needLayer) {
        content += "}";
      }
      if (item[2]) {
        content += "}";
      }
      if (item[4]) {
        content += "}";
      }
      return content;
    }).join("");
  };

  // import a list of modules into the list
  list.i = function i(modules, media, dedupe, supports, layer) {
    if (typeof modules === "string") {
      modules = [[null, modules, undefined]];
    }
    var alreadyImportedModules = {};
    if (dedupe) {
      for (var k = 0; k < this.length; k++) {
        var id = this[k][0];
        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }
    for (var _k = 0; _k < modules.length; _k++) {
      var item = [].concat(modules[_k]);
      if (dedupe && alreadyImportedModules[item[0]]) {
        continue;
      }
      if (typeof layer !== "undefined") {
        if (typeof item[5] === "undefined") {
          item[5] = layer;
        } else {
          item[1] = "@layer".concat(item[5].length > 0 ? " ".concat(item[5]) : "", " {").concat(item[1], "}");
          item[5] = layer;
        }
      }
      if (media) {
        if (!item[2]) {
          item[2] = media;
        } else {
          item[1] = "@media ".concat(item[2], " {").concat(item[1], "}");
          item[2] = media;
        }
      }
      if (supports) {
        if (!item[4]) {
          item[4] = "".concat(supports);
        } else {
          item[1] = "@supports (".concat(item[4], ") {").concat(item[1], "}");
          item[4] = supports;
        }
      }
      list.push(item);
    }
  };
  return list;
};

/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/sourceMaps.js":
/*!************************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/sourceMaps.js ***!
  \************************************************************/
/***/ ((module) => {



module.exports = function (item) {
  var content = item[1];
  var cssMapping = item[3];
  if (!cssMapping) {
    return content;
  }
  if (typeof btoa === "function") {
    var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(cssMapping))));
    var data = "sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(base64);
    var sourceMapping = "/*# ".concat(data, " */");
    return [content].concat([sourceMapping]).join("\n");
  }
  return [content].join("\n");
};

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ ((module) => {



var stylesInDOM = [];
function getIndexByIdentifier(identifier) {
  var result = -1;
  for (var i = 0; i < stylesInDOM.length; i++) {
    if (stylesInDOM[i].identifier === identifier) {
      result = i;
      break;
    }
  }
  return result;
}
function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];
  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var indexByIdentifier = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3],
      supports: item[4],
      layer: item[5]
    };
    if (indexByIdentifier !== -1) {
      stylesInDOM[indexByIdentifier].references++;
      stylesInDOM[indexByIdentifier].updater(obj);
    } else {
      var updater = addElementStyle(obj, options);
      options.byIndex = i;
      stylesInDOM.splice(i, 0, {
        identifier: identifier,
        updater: updater,
        references: 1
      });
    }
    identifiers.push(identifier);
  }
  return identifiers;
}
function addElementStyle(obj, options) {
  var api = options.domAPI(options);
  api.update(obj);
  var updater = function updater(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap && newObj.supports === obj.supports && newObj.layer === obj.layer) {
        return;
      }
      api.update(obj = newObj);
    } else {
      api.remove();
    }
  };
  return updater;
}
module.exports = function (list, options) {
  options = options || {};
  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];
    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDOM[index].references--;
    }
    var newLastIdentifiers = modulesToDom(newList, options);
    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];
      var _index = getIndexByIdentifier(_identifier);
      if (stylesInDOM[_index].references === 0) {
        stylesInDOM[_index].updater();
        stylesInDOM.splice(_index, 1);
      }
    }
    lastIdentifiers = newLastIdentifiers;
  };
};

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/insertBySelector.js":
/*!********************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/insertBySelector.js ***!
  \********************************************************************/
/***/ ((module) => {



var memo = {};

/* istanbul ignore next  */
function getTarget(target) {
  if (typeof memo[target] === "undefined") {
    var styleTarget = document.querySelector(target);

    // Special case to return head of iframe instead of iframe itself
    if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
      try {
        // This will throw an exception if access to iframe is blocked
        // due to cross-origin restrictions
        styleTarget = styleTarget.contentDocument.head;
      } catch (e) {
        // istanbul ignore next
        styleTarget = null;
      }
    }
    memo[target] = styleTarget;
  }
  return memo[target];
}

/* istanbul ignore next  */
function insertBySelector(insert, style) {
  var target = getTarget(insert);
  if (!target) {
    throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
  }
  target.appendChild(style);
}
module.exports = insertBySelector;

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/insertStyleElement.js":
/*!**********************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/insertStyleElement.js ***!
  \**********************************************************************/
/***/ ((module) => {



/* istanbul ignore next  */
function insertStyleElement(options) {
  var element = document.createElement("style");
  options.setAttributes(element, options.attributes);
  options.insert(element, options.options);
  return element;
}
module.exports = insertStyleElement;

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js ***!
  \**********************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {



/* istanbul ignore next  */
function setAttributesWithoutAttributes(styleElement) {
  var nonce =  true ? __webpack_require__.nc : 0;
  if (nonce) {
    styleElement.setAttribute("nonce", nonce);
  }
}
module.exports = setAttributesWithoutAttributes;

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/styleDomAPI.js":
/*!***************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/styleDomAPI.js ***!
  \***************************************************************/
/***/ ((module) => {



/* istanbul ignore next  */
function apply(styleElement, options, obj) {
  var css = "";
  if (obj.supports) {
    css += "@supports (".concat(obj.supports, ") {");
  }
  if (obj.media) {
    css += "@media ".concat(obj.media, " {");
  }
  var needLayer = typeof obj.layer !== "undefined";
  if (needLayer) {
    css += "@layer".concat(obj.layer.length > 0 ? " ".concat(obj.layer) : "", " {");
  }
  css += obj.css;
  if (needLayer) {
    css += "}";
  }
  if (obj.media) {
    css += "}";
  }
  if (obj.supports) {
    css += "}";
  }
  var sourceMap = obj.sourceMap;
  if (sourceMap && typeof btoa !== "undefined") {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  }

  // For old IE
  /* istanbul ignore if  */
  options.styleTagTransform(css, styleElement, options.options);
}
function removeStyleElement(styleElement) {
  // istanbul ignore if
  if (styleElement.parentNode === null) {
    return false;
  }
  styleElement.parentNode.removeChild(styleElement);
}

/* istanbul ignore next  */
function domAPI(options) {
  if (typeof document === "undefined") {
    return {
      update: function update() {},
      remove: function remove() {}
    };
  }
  var styleElement = options.insertStyleElement(options);
  return {
    update: function update(obj) {
      apply(styleElement, options, obj);
    },
    remove: function remove() {
      removeStyleElement(styleElement);
    }
  };
}
module.exports = domAPI;

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/styleTagTransform.js":
/*!*********************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/styleTagTransform.js ***!
  \*********************************************************************/
/***/ ((module) => {



/* istanbul ignore next  */
function styleTagTransform(css, styleElement) {
  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css;
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild);
    }
    styleElement.appendChild(document.createTextNode(css));
  }
}
module.exports = styleTagTransform;

/***/ }),

/***/ "./node_modules/vue-query-builder/src/utilities.js":
/*!*********************************************************!*\
  !*** ./node_modules/vue-query-builder/src/utilities.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Returns a depply cloned object without reference.
 * Copied from Vue MultiSelect and Vuex.
 * @type {Object}
 */
const deepClone = function (obj) {
  if (Array.isArray(obj)) {
    return obj.map(deepClone)
  } else if (obj && typeof obj === 'object') {
    var cloned = {}
    var keys = Object.keys(obj)
    for (var i = 0, l = keys.length; i < l; i++) {
      var key = keys[i]
      cloned[key] = deepClone(obj[key])
    }
    return cloned
  } else {
    return obj
  }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (deepClone);

/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !../../../../style-loader/dist/runtime/styleDomAPI.js */ "./node_modules/style-loader/dist/runtime/styleDomAPI.js");
/* harmony import */ var _style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../style-loader/dist/runtime/insertBySelector.js */ "./node_modules/style-loader/dist/runtime/insertBySelector.js");
/* harmony import */ var _style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../../style-loader/dist/runtime/setAttributesWithoutAttributes.js */ "./node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js");
/* harmony import */ var _style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! !../../../../style-loader/dist/runtime/insertStyleElement.js */ "./node_modules/style-loader/dist/runtime/insertStyleElement.js");
/* harmony import */ var _style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! !../../../../style-loader/dist/runtime/styleTagTransform.js */ "./node_modules/style-loader/dist/runtime/styleTagTransform.js");
/* harmony import */ var _style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _css_loader_dist_cjs_js_vue_loader_dist_stylePostLoader_js_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! !!../../../../css-loader/dist/cjs.js!../../../../vue-loader/dist/stylePostLoader.js!../../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css");

      
      
      
      
      
      
      
      
      

var options = {};

options.styleTagTransform = (_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default());
options.setAttributes = (_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default());

      options.insert = _style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default().bind(null, "head");
    
options.domAPI = (_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default());
options.insertStyleElement = (_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default());

var update = _style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_css_loader_dist_cjs_js_vue_loader_dist_stylePostLoader_js_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"], options);




       /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_css_loader_dist_cjs_js_vue_loader_dist_stylePostLoader_js_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"] && _css_loader_dist_cjs_js_vue_loader_dist_stylePostLoader_js_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"].locals ? _css_loader_dist_cjs_js_vue_loader_dist_stylePostLoader_js_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"].locals : undefined);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/styleDomAPI.js */ "./node_modules/style-loader/dist/runtime/styleDomAPI.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/insertBySelector.js */ "./node_modules/style-loader/dist/runtime/insertBySelector.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js */ "./node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/insertStyleElement.js */ "./node_modules/style-loader/dist/runtime/insertStyleElement.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/styleTagTransform.js */ "./node_modules/style-loader/dist/runtime/styleTagTransform.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css");

      
      
      
      
      
      
      
      
      

var options = {};

options.styleTagTransform = (_node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default());
options.setAttributes = (_node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default());

      options.insert = _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default().bind(null, "head");
    
options.domAPI = (_node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default());
options.insertStyleElement = (_node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default());

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"], options);




       /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"] && _node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"].locals ? _node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"].locals : undefined);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/styleDomAPI.js */ "./node_modules/style-loader/dist/runtime/styleDomAPI.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/insertBySelector.js */ "./node_modules/style-loader/dist/runtime/insertBySelector.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js */ "./node_modules/style-loader/dist/runtime/setAttributesWithoutAttributes.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/insertStyleElement.js */ "./node_modules/style-loader/dist/runtime/insertStyleElement.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/styleTagTransform.js */ "./node_modules/style-loader/dist/runtime/styleTagTransform.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css");

      
      
      
      
      
      
      
      
      

var options = {};

options.styleTagTransform = (_node_modules_style_loader_dist_runtime_styleTagTransform_js__WEBPACK_IMPORTED_MODULE_5___default());
options.setAttributes = (_node_modules_style_loader_dist_runtime_setAttributesWithoutAttributes_js__WEBPACK_IMPORTED_MODULE_3___default());

      options.insert = _node_modules_style_loader_dist_runtime_insertBySelector_js__WEBPACK_IMPORTED_MODULE_2___default().bind(null, "head");
    
options.domAPI = (_node_modules_style_loader_dist_runtime_styleDomAPI_js__WEBPACK_IMPORTED_MODULE_1___default());
options.insertStyleElement = (_node_modules_style_loader_dist_runtime_insertStyleElement_js__WEBPACK_IMPORTED_MODULE_4___default());

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"], options);




       /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"] && _node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"].locals ? _node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_6__["default"].locals : undefined);


/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _layouts_Bootstrap5_Bootstrap5Group__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./layouts/Bootstrap5/Bootstrap5Group */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue");
/* harmony import */ var _layouts_Bootstrap5_Bootstrap5Rule__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./layouts/Bootstrap5/Bootstrap5Rule */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue");
/* harmony import */ var _utilities_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utilities.js */ "./node_modules/vue-query-builder/src/utilities.js");





var defaultLabels = {
  matchType: "Match Type",
  matchTypes: [
    { id: "all", label: "All" },
    { id: "any", label: "Any" },
  ],
  addRule: "Add Rule",
  removeRule: "&times;",
  addGroup: "Add Group",
  removeGroup: "&times;",
  textInputPlaceholder: "value",
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "VueQueryBuilder",

  components: {
    QueryBuilderGroup: _layouts_Bootstrap5_Bootstrap5Group__WEBPACK_IMPORTED_MODULE_0__["default"],
  },

  props: {
    rules: Array,
    labels: {
      type: Object,
      default() {
        return defaultLabels;
      },
    },
    maxDepth: {
      type: Number,
      default: 3,
      validator: function (value) {
        return value >= 1;
      },
    },
    groupComponent: {
      type: Object,
      default: _layouts_Bootstrap5_Bootstrap5Group__WEBPACK_IMPORTED_MODULE_0__["default"],
    },
    ruleComponent: {
      type: Object,
      default: _layouts_Bootstrap5_Bootstrap5Rule__WEBPACK_IMPORTED_MODULE_1__["default"],
    },
    modelValue: Object,
  },

  data() {
    return {
      query: {
        logicalOperator: this.labels.matchTypes[0].id,
        children: [],
      },
      ruleTypes: {
        text: {
          operators: [
            "equals",
            "does not equal",
            "contains",
            "does not contain",
            "is empty",
            "is not empty",
            "begins with",
            "ends with",
          ],
          inputType: "text",
          id: "text-field",
        },
        numeric: {
          operators: ["=", "<>", "<", "<=", ">", ">="],
          inputType: "number",
          id: "number-field",
        },
        custom: {
          operators: [],
          inputType: "text",
          id: "custom-field",
        },
        radio: {
          operators: [],
          choices: [],
          inputType: "radio",
          id: "radio-field",
        },
        checkbox: {
          operators: [],
          choices: [],
          inputType: "checkbox",
          id: "checkbox-field",
        },
        select: {
          operators: [],
          choices: [],
          inputType: "select",
          id: "select-field",
        },
        "multi-select": {
          operators: ["="],
          choices: [],
          inputType: "select",
          id: "multi-select-field",
        },
      },
    };
  },

  computed: {
    mergedLabels() {
      return Object.assign({}, defaultLabels, this.labels);
    },

    mergedRules() {
      var mergedRules = [];
      // eslint-disable-next-line @typescript-eslint/no-this-alias
      var vm = this;

      vm.rules.forEach(function (rule) {
        if (typeof vm.ruleTypes[rule.type] !== "undefined") {
          mergedRules.push(Object.assign({}, vm.ruleTypes[rule.type], rule));
        } else {
          mergedRules.push(rule);
        }
      });

      return mergedRules;
    },

    vqbProps() {
      return {
        index: 0,
        depth: 1,
        maxDepth: this.maxDepth,
        ruleTypes: this.ruleTypes,
        rules: this.mergedRules,
        labels: this.mergedLabels,
        groupComponent: this.groupComponent,
        ruleComponent: this.ruleComponent,
      };
    },
  },

  mounted() {
    this.$watch(
      "query",
      (newQuery) => {
        if (JSON.stringify(newQuery) !== JSON.stringify(this.modelValue)) {
          this.$emit("update:modelValue", (0,_utilities_js__WEBPACK_IMPORTED_MODULE_2__["default"])(newQuery));
        }
      },
      {
        deep: true,
      }
    );

    this.$watch(
      "modelValue",
      (newValue) => {
        if (JSON.stringify(newValue) !== JSON.stringify(this.query)) {
          this.query = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_2__["default"])(newValue);
        }
      },
      {
        deep: true,
      }
    );

    if (typeof this.modelValue !== "undefined") {
      this.query = Object.assign(this.query, this.modelValue);
    }
  },
});


/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=script&lang=js":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=script&lang=js ***!
  \*************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  // eslint-disable-next-line vue/require-prop-types
  props: [
    "query",
    "ruleTypes",
    "rules",
    "maxDepth",
    "labels",
    "depth",
    "groupComponent",
    "ruleComponent",
  ],

  methods: {
    getComponent(type) {
      return type === "query-builder-group"
        ? this.groupComponent
        : this.ruleComponent;
    },
  },
});


/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=script&lang=js":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=script&lang=js ***!
  \**********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _utilities_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utilities.js */ "./node_modules/vue-query-builder/src/utilities.js");
/* harmony import */ var _QueryBuilderChildren_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./QueryBuilderChildren.vue */ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue");

/* eslint-disable vue/require-default-prop */



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  components: {
    // eslint-disable-next-line vue/no-unused-components
    QueryBuilderChildren: _QueryBuilderChildren_vue__WEBPACK_IMPORTED_MODULE_1__["default"],
  },

  props: {
    ruleTypes: Object,
    type: {
      type: String,
      default: "query-builder-group",
    },
    query: Object,
    rules: Array,
    index: Number,
    maxDepth: Number,
    depth: Number,
    labels: Object,
    groupComponent: Object,
    ruleComponent: Object,
  },

  data() {
    return {
      selectedRule: this.rules[0],
    };
  },
  watch: {
    rules: function () {
      if (this.rules) {
        this.selectedRule = this.rules[0];
      }
    },
  },
  computed: {
    selectedRuleId: function () {
      if (this.selectedRule) return this.selectedRule.id;
      else return null;
    },
  },

  methods: {
    ruleById(ruleId) {
      var rule = null;

      this.rules.forEach(function (value) {
        if (value.id === ruleId) {
          rule = value;
          return false;
        }
      });

      return rule;
    },

    addRule() {
      let updated_query = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.query);
      let child = {
        type: "query-builder-rule",
        query: {
          rule: this.selectedRule.id,
          operator: this.selectedRule.operators[0],
          operand:
            typeof this.selectedRule.operands === "undefined"
              ? this.selectedRule.label
              : this.selectedRule.operands[0],
          value: null,
        },
      };
      // A bit hacky, but `v-model` on `select` requires an array.
      if (this.ruleById(child.query.rule).type === "multi-select") {
        child.query.value = [];
      }
      updated_query.children.push(child);
      this.$emit("update:query", updated_query);
    },

    addGroup() {
      let updated_query = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.query);
      if (this.depth < this.maxDepth) {
        updated_query.children.push({
          type: "query-builder-group",
          query: {
            logicalOperator: this.labels.matchTypes[0].id,
            children: [],
          },
        });
        this.$emit("update:query", updated_query);
      }
    },

    remove() {
      this.$emit("child-deletion-requested", this.index);
    },

    removeChild(index) {
      let updated_query = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.query);
      updated_query.children.splice(index, 1);
      this.$emit("update:query", updated_query);
    },
    updateRule: function (x) {
      const id = x.target.selectedOptions[0].value;
      this.selectedRule = this.ruleById(id);
    },
  },
});


/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _utilities_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utilities.js */ "./node_modules/vue-query-builder/src/utilities.js");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  // eslint-disable-next-line vue/require-prop-types
  props: ["query", "index", "rule", "labels", "depth"],
  components: {},
  computed: {
    isCustomComponent() {
      return this.rule.type === "custom-component";
    },

    selectOptions() {
      if (typeof this.rule.choices === "undefined") {
        return {};
      }

      // Nest items to support <optgroup> if the rule's choices have
      // defined groups. Otherwise just return a single-level array
      return this.rule.choices.reduce(function (groups, item, index) {
        let key = item["group"];
        if (typeof key !== "undefined") {
          groups[key] = groups[key] || [];
          groups[key].push(item);
        } else {
          groups[index] = item;
        }

        return groups;
      }, {});
    },

    hasOptionGroups() {
      return this.selectOptions.length && Array.isArray(this.selectOptions[0]);
    },
  },

  beforeMount() {
    if (this.rule.type === "custom-component") {
      this.$options.components[this.id] = this.rule.component;
    }
  },

  mounted() {
    let updated_query = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.query);

    // Set a default value for these types if one isn't provided already
    if (this.query.value === null) {
      if (this.rule.inputType === "checkbox") {
        updated_query.value = [];
      }
      if (this.rule.type === "select") {
        updated_query.value = this.rule.choices[0].value;
      }
      if (this.rule.type === "custom-component") {
        updated_query.value = null;
        if (typeof this.rule.default !== "undefined") {
          updated_query.value = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.rule.default);
        }
      }

      this.$emit("update:query", updated_query);
    }
  },

  methods: {
    remove: function () {
      this.$emit("child-deletion-requested", this.index);
    },
    updateQuery(value) {
      console.log(value);
      let updated_query = (0,_utilities_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.query);
      updated_query.value = value;
      this.$emit("update:query", updated_query);
    },
  },
});


/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=script&lang=js":
/*!****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=script&lang=js ***!
  \****************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _components_QueryBuilderGroup__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/QueryBuilderGroup */ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue");
/* harmony import */ var _components_QueryBuilderChildren__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/QueryBuilderChildren */ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: "QueryBuilderGroup",

  components: { QueryBuilderChildren: _components_QueryBuilderChildren__WEBPACK_IMPORTED_MODULE_1__["default"] },

  extends: _components_QueryBuilderGroup__WEBPACK_IMPORTED_MODULE_0__["default"],
  methods: {},
});


/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=script&lang=js":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=script&lang=js ***!
  \***************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _components_QueryBuilderRule__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/QueryBuilderRule */ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  extends: _components_QueryBuilderRule__WEBPACK_IMPORTED_MODULE_0__["default"],
});


/***/ }),

/***/ "./node_modules/vue-query-builder/src/VueQueryBuilder.vue":
/*!****************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/VueQueryBuilder.vue ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _VueQueryBuilder_vue_vue_type_template_id_204b91d2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VueQueryBuilder.vue?vue&type=template&id=204b91d2 */ "./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=template&id=204b91d2");
/* harmony import */ var _VueQueryBuilder_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VueQueryBuilder.vue?vue&type=script&lang=js */ "./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=script&lang=js");
/* harmony import */ var _vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_VueQueryBuilder_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_VueQueryBuilder_vue_vue_type_template_id_204b91d2__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"node_modules/vue-query-builder/src/VueQueryBuilder.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue":
/*!********************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _QueryBuilderChildren_vue_vue_type_template_id_c30a3bae__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QueryBuilderChildren.vue?vue&type=template&id=c30a3bae */ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=template&id=c30a3bae");
/* harmony import */ var _QueryBuilderChildren_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./QueryBuilderChildren.vue?vue&type=script&lang=js */ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=script&lang=js");
/* harmony import */ var _vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_QueryBuilderChildren_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_QueryBuilderChildren_vue_vue_type_template_id_c30a3bae__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue":
/*!*****************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _QueryBuilderGroup_vue_vue_type_template_id_160f5c76__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QueryBuilderGroup.vue?vue&type=template&id=160f5c76 */ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=template&id=160f5c76");
/* harmony import */ var _QueryBuilderGroup_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./QueryBuilderGroup.vue?vue&type=script&lang=js */ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=script&lang=js");
/* harmony import */ var _vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_QueryBuilderGroup_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_QueryBuilderGroup_vue_vue_type_template_id_160f5c76__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue":
/*!****************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _QueryBuilderRule_vue_vue_type_template_id_c96aa4b4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QueryBuilderRule.vue?vue&type=template&id=c96aa4b4 */ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=template&id=c96aa4b4");
/* harmony import */ var _QueryBuilderRule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./QueryBuilderRule.vue?vue&type=script&lang=js */ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=script&lang=js");
/* harmony import */ var _vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_QueryBuilderRule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_QueryBuilderRule_vue_vue_type_template_id_c96aa4b4__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"node_modules/vue-query-builder/src/components/QueryBuilderRule.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue":
/*!***********************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Bootstrap5Group_vue_vue_type_template_id_987e31f0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Bootstrap5Group.vue?vue&type=template&id=987e31f0 */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=template&id=987e31f0");
/* harmony import */ var _Bootstrap5Group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Bootstrap5Group.vue?vue&type=script&lang=js */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=script&lang=js");
/* harmony import */ var _Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css");
/* harmony import */ var _vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_Bootstrap5Group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Bootstrap5Group_vue_vue_type_template_id_987e31f0__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue":
/*!**********************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Bootstrap5Rule_vue_vue_type_template_id_076e37fa__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Bootstrap5Rule.vue?vue&type=template&id=076e37fa */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=template&id=076e37fa");
/* harmony import */ var _Bootstrap5Rule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Bootstrap5Rule.vue?vue&type=script&lang=js */ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=script&lang=js");
/* harmony import */ var _vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Bootstrap5Rule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Bootstrap5Rule_vue_vue_type_template_id_076e37fa__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-group.component.vue":
/*!**************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-group.component.vue ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _fomantic_ui_group_component_vue_vue_type_template_id_5a4d40f3__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3 */ "./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3");
/* harmony import */ var _fomantic_ui_group_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./fomantic-ui-group.component.vue?vue&type=script&lang=js */ "./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=script&lang=js");
/* harmony import */ var _fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css */ "./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_fomantic_ui_group_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_fomantic_ui_group_component_vue_vue_type_template_id_5a4d40f3__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/vue-components/query-builder/fomantic-ui-group.component.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue":
/*!*************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-rule.component.vue ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _fomantic_ui_rule_component_vue_vue_type_template_id_70644af6__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./fomantic-ui-rule.component.vue?vue&type=template&id=70644af6 */ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=template&id=70644af6");
/* harmony import */ var _fomantic_ui_rule_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./fomantic-ui-rule.component.vue?vue&type=script&lang=js */ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=script&lang=js");
/* harmony import */ var _fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css */ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_fomantic_ui_rule_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_fomantic_ui_rule_component_vue_vue_type_template_id_70644af6__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/vue-components/query-builder/fomantic-ui-rule.component.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./src/vue-components/query-builder/query-builder.component.vue":
/*!**********************************************************************!*\
  !*** ./src/vue-components/query-builder/query-builder.component.vue ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _query_builder_component_vue_vue_type_template_id_5e810cb3__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./query-builder.component.vue?vue&type=template&id=5e810cb3 */ "./src/vue-components/query-builder/query-builder.component.vue?vue&type=template&id=5e810cb3");
/* harmony import */ var _query_builder_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./query-builder.component.vue?vue&type=script&lang=js */ "./src/vue-components/query-builder/query-builder.component.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_query_builder_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_query_builder_component_vue_vue_type_template_id_5e810cb3__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/vue-components/query-builder/query-builder.component.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=script&lang=js":
/*!**************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=script&lang=js ***!
  \**************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_group_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_group_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../node_modules/source-map-loader/dist/cjs.js!./fomantic-ui-group.component.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=script&lang=js":
/*!*************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=script&lang=js ***!
  \*************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_rule_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_rule_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../node_modules/source-map-loader/dist/cjs.js!./fomantic-ui-rule.component.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/vue-components/query-builder/query-builder.component.vue?vue&type=script&lang=js":
/*!**********************************************************************************************!*\
  !*** ./src/vue-components/query-builder/query-builder.component.vue?vue&type=script&lang=js ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_query_builder_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_query_builder_component_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../node_modules/source-map-loader/dist/cjs.js!./query-builder.component.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/query-builder.component.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3":
/*!********************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3 ***!
  \********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_group_component_vue_vue_type_template_id_5a4d40f3__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_group_component_vue_vue_type_template_id_5a4d40f3__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../node_modules/source-map-loader/dist/cjs.js!./fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3 */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=template&id=5a4d40f3");


/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=template&id=70644af6":
/*!*******************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=template&id=70644af6 ***!
  \*******************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_rule_component_vue_vue_type_template_id_70644af6__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_fomantic_ui_rule_component_vue_vue_type_template_id_70644af6__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../node_modules/source-map-loader/dist/cjs.js!./fomantic-ui-rule.component.vue?vue&type=template&id=70644af6 */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=template&id=70644af6");


/***/ }),

/***/ "./src/vue-components/query-builder/query-builder.component.vue?vue&type=template&id=5e810cb3":
/*!****************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/query-builder.component.vue?vue&type=template&id=5e810cb3 ***!
  \****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_query_builder_component_vue_vue_type_template_id_5e810cb3__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_node_modules_source_map_loader_dist_cjs_js_query_builder_component_vue_vue_type_template_id_5e810cb3__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../node_modules/source-map-loader/dist/cjs.js!./query-builder.component.vue?vue&type=template&id=5e810cb3 */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./src/vue-components/query-builder/query-builder.component.vue?vue&type=template&id=5e810cb3");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css":
/*!*******************************************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css ***!
  \*******************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _style_loader_dist_cjs_js_css_loader_dist_cjs_js_vue_loader_dist_stylePostLoader_js_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_Bootstrap5Group_vue_vue_type_style_index_0_id_987e31f0_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../style-loader/dist/cjs.js!../../../../css-loader/dist/cjs.js!../../../../vue-loader/dist/stylePostLoader.js!../../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=style&index=0&id=987e31f0&lang=css");


/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css":
/*!**********************************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css ***!
  \**********************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_group_component_vue_vue_type_style_index_0_id_5a4d40f3_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-group.component.vue?vue&type=style&index=0&id=5a4d40f3&lang=css");


/***/ }),

/***/ "./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css":
/*!*********************************************************************************************************************!*\
  !*** ./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css ***!
  \*********************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_fomantic_ui_rule_component_vue_vue_type_style_index_0_id_70644af6_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./src/vue-components/query-builder/fomantic-ui-rule.component.vue?vue&type=style&index=0&id=70644af6&lang=css");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=script&lang=js":
/*!****************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=script&lang=js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_VueQueryBuilder_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_VueQueryBuilder_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../source-map-loader/dist/cjs.js!./VueQueryBuilder.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=script&lang=js":
/*!********************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderChildren_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderChildren_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../source-map-loader/dist/cjs.js!./QueryBuilderChildren.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderGroup_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderGroup_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../source-map-loader/dist/cjs.js!./QueryBuilderGroup.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=script&lang=js":
/*!****************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=script&lang=js ***!
  \****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderRule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderRule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../source-map-loader/dist/cjs.js!./QueryBuilderRule.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=script&lang=js":
/*!***********************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=script&lang=js ***!
  \***********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Group_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../../source-map-loader/dist/cjs.js!./Bootstrap5Group.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=script&lang=js":
/*!**********************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=script&lang=js ***!
  \**********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Rule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Rule_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../../source-map-loader/dist/cjs.js!./Bootstrap5Rule.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=template&id=204b91d2":
/*!**********************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=template&id=204b91d2 ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_VueQueryBuilder_vue_vue_type_template_id_204b91d2__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_VueQueryBuilder_vue_vue_type_template_id_204b91d2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../source-map-loader/dist/cjs.js!./VueQueryBuilder.vue?vue&type=template&id=204b91d2 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=template&id=204b91d2");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=template&id=c30a3bae":
/*!**************************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=template&id=c30a3bae ***!
  \**************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderChildren_vue_vue_type_template_id_c30a3bae__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderChildren_vue_vue_type_template_id_c30a3bae__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../source-map-loader/dist/cjs.js!./QueryBuilderChildren.vue?vue&type=template&id=c30a3bae */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=template&id=c30a3bae");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=template&id=160f5c76":
/*!***********************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=template&id=160f5c76 ***!
  \***********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderGroup_vue_vue_type_template_id_160f5c76__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderGroup_vue_vue_type_template_id_160f5c76__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../source-map-loader/dist/cjs.js!./QueryBuilderGroup.vue?vue&type=template&id=160f5c76 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=template&id=160f5c76");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=template&id=c96aa4b4":
/*!**********************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=template&id=c96aa4b4 ***!
  \**********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderRule_vue_vue_type_template_id_c96aa4b4__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_QueryBuilderRule_vue_vue_type_template_id_c96aa4b4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../source-map-loader/dist/cjs.js!./QueryBuilderRule.vue?vue&type=template&id=c96aa4b4 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=template&id=c96aa4b4");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=template&id=987e31f0":
/*!*****************************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=template&id=987e31f0 ***!
  \*****************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Group_vue_vue_type_template_id_987e31f0__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Group_vue_vue_type_template_id_987e31f0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../../source-map-loader/dist/cjs.js!./Bootstrap5Group.vue?vue&type=template&id=987e31f0 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=template&id=987e31f0");


/***/ }),

/***/ "./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=template&id=076e37fa":
/*!****************************************************************************************************************!*\
  !*** ./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=template&id=076e37fa ***!
  \****************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Rule_vue_vue_type_template_id_076e37fa__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_vue_loader_dist_index_js_ruleSet_1_rules_9_use_0_source_map_loader_dist_cjs_js_Bootstrap5Rule_vue_vue_type_template_id_076e37fa__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../../vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!../../../../source-map-loader/dist/cjs.js!./Bootstrap5Rule.vue?vue&type=template&id=076e37fa */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=template&id=076e37fa");


/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=template&id=204b91d2":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/VueQueryBuilder.vue?vue&type=template&id=204b91d2 ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


const _hoisted_1 = { class: "vue-query-builder" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_query_builder_group = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("query-builder-group")

  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default", (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)($options.vqbProps)), () => [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_query_builder_group, (0,vue__WEBPACK_IMPORTED_MODULE_0__.mergeProps)($options.vqbProps, {
        query: $data.query,
        "onUpdate:query": _cache[0] || (_cache[0] = $event => (($data.query) = $event))
      }), null, 16 /* FULL_PROPS */, ["query"])
    ])
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=template&id=c30a3bae":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderChildren.vue?vue&type=template&id=c30a3bae ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


const _hoisted_1 = { class: "vqb-children" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.query.children, (child, index) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)($options.getComponent(child.type)), {
        key: index,
        type: child.type,
        query: child.query,
        "onUpdate:query": $event => ((child.query) = $event),
        "rule-types": $props.ruleTypes,
        rules: $props.rules,
        rule: _ctx.$parent.ruleById(child.query.rule),
        index: index,
        "max-depth": $props.maxDepth,
        depth: $props.depth + 1,
        labels: $props.labels,
        onChildDeletionRequested: _ctx.$parent.removeChild,
        groupComponent: $props.groupComponent,
        ruleComponent: $props.ruleComponent
      }, null, 40 /* PROPS, HYDRATE_EVENTS */, ["type", "query", "onUpdate:query", "rule-types", "rules", "rule", "index", "max-depth", "depth", "labels", "onChildDeletionRequested", "groupComponent", "ruleComponent"]))
    }), 128 /* KEYED_FRAGMENT */))
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=template&id=160f5c76":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderGroup.vue?vue&type=template&id=160f5c76 ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div"))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=template&id=c96aa4b4":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/components/QueryBuilderRule.vue?vue&type=template&id=c96aa4b4 ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div"))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=template&id=987e31f0":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Group.vue?vue&type=template&id=987e31f0 ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


const _hoisted_1 = { class: "vqb-group-heading card-header" }
const _hoisted_2 = { class: "match-type-container row gy-2 gx-3 align-items-center" }
const _hoisted_3 = { class: "col-auto" }
const _hoisted_4 = {
  class: "me-2",
  for: "vqb-match-type"
}
const _hoisted_5 = { class: "col-auto" }
const _hoisted_6 = ["value"]
const _hoisted_7 = {
  key: 0,
  class: "col-auto"
}
const _hoisted_8 = { class: "vqb-group-body card-body" }
const _hoisted_9 = { class: "rule-actions" }
const _hoisted_10 = { class: "row gy-2 gx-3 align-items-center" }
const _hoisted_11 = { class: "col-auto" }
const _hoisted_12 = ["value"]
const _hoisted_13 = ["value"]
const _hoisted_14 = { class: "col-auto" }
const _hoisted_15 = { class: "col-auto" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_query_builder_children = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("query-builder-children")

  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" eslint-disable vue/no-v-html "),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["vqb-group card", 'depth-' + _ctx.depth.toString()])
    }, [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_4, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.labels.matchType), 1 /* TEXT */)
          ]),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_5, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
              id: "vqb-match-type",
              "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => ((_ctx.query.logicalOperator) = $event)),
              class: "form-select"
            }, [
              ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.labels.matchTypes, (label) => {
                return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                  key: label.id,
                  value: label.id
                }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(label.label), 9 /* TEXT, PROPS */, _hoisted_6))
              }), 128 /* KEYED_FRAGMENT */))
            ], 512 /* NEED_PATCH */), [
              [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.logicalOperator]
            ])
          ]),
          (_ctx.depth > 1)
            ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_7, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
                  type: "button",
                  class: "btn-close btn-small",
                  onClick: _cache[1] || (_cache[1] = (...args) => (_ctx.remove && _ctx.remove(...args)))
                })
              ]))
            : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
        ])
      ]),
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_8, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                value: _ctx.selectedRuleId,
                onInput: _cache[2] || (_cache[2] = (...args) => (_ctx.updateRule && _ctx.updateRule(...args))),
                class: "form-select me-2"
              }, [
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rules, (rule) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                    key: rule.id,
                    value: rule.id
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(rule.label), 9 /* TEXT, PROPS */, _hoisted_13))
                }), 128 /* KEYED_FRAGMENT */))
              ], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_12)
            ]),
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
                type: "button",
                class: "btn btn-secondary me-2",
                onClick: _cache[3] || (_cache[3] = (...args) => (_ctx.addRule && _ctx.addRule(...args)))
              }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.labels.addRule), 1 /* TEXT */)
            ]),
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_15, [
              (_ctx.depth < _ctx.maxDepth)
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("button", {
                    key: 0,
                    type: "button",
                    class: "btn btn-secondary",
                    onClick: _cache[4] || (_cache[4] = (...args) => (_ctx.addGroup && _ctx.addGroup(...args)))
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.labels.addGroup), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
            ])
          ])
        ]),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_query_builder_children, (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)(_ctx.$props)), null, 16 /* FULL_PROPS */)
      ])
    ], 2 /* CLASS */)
  ], 2112 /* STABLE_FRAGMENT, DEV_ROOT_FRAGMENT */))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=template&id=076e37fa":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[9].use[0]!./node_modules/source-map-loader/dist/cjs.js!./node_modules/vue-query-builder/src/layouts/Bootstrap5/Bootstrap5Rule.vue?vue&type=template&id=076e37fa ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm-bundler.js");


const _hoisted_1 = { class: "vqb-rule card" }
const _hoisted_2 = { class: "row gy-2 gx-3 align-items-center card-body" }
const _hoisted_3 = { class: "col-auto" }
const _hoisted_4 = {
  key: 0,
  class: "col-auto"
}
const _hoisted_5 = {
  key: 1,
  class: "col-auto"
}
const _hoisted_6 = ["value"]
const _hoisted_7 = {
  key: 2,
  class: "col-auto"
}
const _hoisted_8 = ["placeholder"]
const _hoisted_9 = {
  key: 3,
  class: "col-auto"
}
const _hoisted_10 = {
  key: 4,
  class: "col-auto"
}
const _hoisted_11 = {
  key: 5,
  class: "col-auto vqb-custom-component-wrap"
}
const _hoisted_12 = {
  key: 6,
  class: "col-auto"
}
const _hoisted_13 = ["id", "value"]
const _hoisted_14 = ["for"]
const _hoisted_15 = {
  key: 7,
  class: "col-auto"
}
const _hoisted_16 = ["id", "name", "value"]
const _hoisted_17 = ["for"]
const _hoisted_18 = {
  key: 8,
  class: "col-auto"
}
const _hoisted_19 = ["multiple"]
const _hoisted_20 = ["value"]
const _hoisted_21 = {
  key: 9,
  class: "col-auto"
}
const _hoisted_22 = ["multiple"]
const _hoisted_23 = ["label"]
const _hoisted_24 = ["value"]
const _hoisted_25 = { class: "col-auto d-flex" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" eslint-disable vue/no-v-html "),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_3, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(_ctx.rule.label), 1 /* TEXT */),
        (typeof _ctx.rule.operands !== 'undefined')
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_4, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" List of operands (optional) "),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => ((_ctx.query.operand) = $event)),
                class: "form-select me-2"
              }, [
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.operands, (operand) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", { key: operand }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(operand), 1 /* TEXT */))
                }), 128 /* KEYED_FRAGMENT */))
              ], 512 /* NEED_PATCH */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.operand]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" List of operators (e.g. =, !=, >, <) "),
        (
          typeof _ctx.rule.operators !== 'undefined' && _ctx.rule.operators.length > 1
        )
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_5, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => ((_ctx.query.operator) = $event)),
                class: "form-select me-2"
              }, [
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.operators, (operator) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                    key: operator,
                    value: operator
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(operator), 9 /* TEXT, PROPS */, _hoisted_6))
                }), 128 /* KEYED_FRAGMENT */))
              ], 512 /* NEED_PATCH */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.operator]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Basic text input "),
        (_ctx.rule.inputType === 'text')
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_7, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => ((_ctx.query.value) = $event)),
                class: "form-control",
                type: "text",
                placeholder: _ctx.labels.textInputPlaceholder
              }, null, 8 /* PROPS */, _hoisted_8), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, _ctx.query.value]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Basic number input "),
        (_ctx.rule.inputType === 'number')
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_9, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => ((_ctx.query.value) = $event)),
                class: "form-control",
                type: "number"
              }, null, 512 /* NEED_PATCH */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, _ctx.query.value]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Datepicker "),
        (_ctx.rule.inputType === 'date')
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_10, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => ((_ctx.query.value) = $event)),
                class: "form-control",
                type: "date"
              }, null, 512 /* NEED_PATCH */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, _ctx.query.value]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Custom component input "),
        (_ctx.isCustomComponent)
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_11, [
              ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)(_ctx.rule.component), {
                modelValue: _ctx.query.value,
                "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => ((_ctx.query.value) = $event)),
                rule: _ctx.rule
              }, null, 8 /* PROPS */, ["modelValue", "rule"]))
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Checkbox input "),
        (_ctx.rule.inputType === 'checkbox')
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_12, [
              ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.choices, (choice) => {
                return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
                  key: choice.value,
                  class: "form-check form-check-inline"
                }, [
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                    id: 
              'depth' + _ctx.depth + '-' + _ctx.rule.id + '-' + _ctx.index + '-' + choice.value
            ,
                    "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => ((_ctx.query.value) = $event)),
                    type: "checkbox",
                    value: choice.value,
                    class: "form-check-input"
                  }, null, 8 /* PROPS */, _hoisted_13), [
                    [vue__WEBPACK_IMPORTED_MODULE_0__.vModelCheckbox, _ctx.query.value]
                  ]),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
                    class: "form-check-label",
                    for: 
              'depth' + _ctx.depth + '-' + _ctx.rule.id + '-' + _ctx.index + '-' + choice.value
            
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(choice.label), 9 /* TEXT, PROPS */, _hoisted_14)
                ]))
              }), 128 /* KEYED_FRAGMENT */))
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Radio input "),
        (_ctx.rule.inputType === 'radio')
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_15, [
              ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.rule.choices, (choice) => {
                return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
                  key: choice.value,
                  class: "form-check form-check-inline"
                }, [
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                    id: 
              'depth' + _ctx.depth + '-' + _ctx.rule.id + '-' + _ctx.index + '-' + choice.value
            ,
                    "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => ((_ctx.query.value) = $event)),
                    name: 'depth' + _ctx.depth + '-' + _ctx.rule.id + '-' + _ctx.index,
                    type: "radio",
                    value: choice.value,
                    class: "form-check-input"
                  }, null, 8 /* PROPS */, _hoisted_16), [
                    [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, _ctx.query.value]
                  ]),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
                    class: "form-check-label",
                    for: 
              'depth' + _ctx.depth + '-' + _ctx.rule.id + '-' + _ctx.index + '-' + choice.value
            
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(choice.label), 9 /* TEXT, PROPS */, _hoisted_17)
                ]))
              }), 128 /* KEYED_FRAGMENT */))
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Select without groups "),
        (_ctx.rule.inputType === 'select' && !_ctx.hasOptionGroups)
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_18, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                "onUpdate:modelValue": _cache[8] || (_cache[8] = $event => ((_ctx.query.value) = $event)),
                class: "form-select",
                multiple: _ctx.rule.type === 'multi-select'
              }, [
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.selectOptions, (option) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                    key: option.value,
                    value: option.value
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(option.label), 9 /* TEXT, PROPS */, _hoisted_20))
                }), 128 /* KEYED_FRAGMENT */))
              ], 8 /* PROPS */, _hoisted_19), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.value]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Select with groups "),
        (_ctx.rule.inputType === 'select' && _ctx.hasOptionGroups)
          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_21, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                "onUpdate:modelValue": _cache[9] || (_cache[9] = $event => ((_ctx.query.value) = $event)),
                class: "form-select",
                multiple: _ctx.rule.type === 'multi-select'
              }, [
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.selectOptions, (option, option_key) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("optgroup", {
                    key: option_key,
                    label: option_key
                  }, [
                    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(option, (sub_option) => {
                      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                        key: sub_option.value,
                        value: sub_option.value
                      }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(sub_option.label), 9 /* TEXT, PROPS */, _hoisted_24))
                    }), 128 /* KEYED_FRAGMENT */))
                  ], 8 /* PROPS */, _hoisted_23))
                }), 128 /* KEYED_FRAGMENT */))
              ], 8 /* PROPS */, _hoisted_22), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, _ctx.query.value]
              ])
            ]))
          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(" Remove rule button "),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_25, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
            type: "button",
            class: "btn-close btn-sm",
            onClick: _cache[10] || (_cache[10] = (...args) => (_ctx.remove && _ctx.remove(...args)))
          })
        ])
      ])
    ])
  ], 2112 /* STABLE_FRAGMENT, DEV_ROOT_FRAGMENT */))
}

/***/ })

}]);
//# sourceMappingURL=atk-vue-query-builder.js.map