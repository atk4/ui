"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-tree-item-selector"],{

/***/ "./src/vue-components/tree-item-selector/tree-item-selector.component.js":
/*!*******************************************************************************!*\
  !*** ./src/vue-components/tree-item-selector/tree-item-selector.component.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_esnext_json_parse_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.json.parse.js */ "./node_modules/core-js/modules/esnext.json.parse.js");
/* harmony import */ var core_js_modules_esnext_json_parse_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_json_parse_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_push_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.push.js */ "./node_modules/core-js/modules/es.array.push.js");
/* harmony import */ var core_js_modules_es_array_push_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_push_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_set_add_all_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.set.add-all.js */ "./node_modules/core-js/modules/esnext.set.add-all.js");
/* harmony import */ var core_js_modules_esnext_set_add_all_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_add_all_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_esnext_set_delete_all_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.set.delete-all.js */ "./node_modules/core-js/modules/esnext.set.delete-all.js");
/* harmony import */ var core_js_modules_esnext_set_delete_all_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_delete_all_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_set_difference_v2_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.set.difference.v2.js */ "./node_modules/core-js/modules/esnext.set.difference.v2.js");
/* harmony import */ var core_js_modules_esnext_set_difference_v2_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_difference_v2_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_esnext_set_difference_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/esnext.set.difference.js */ "./node_modules/core-js/modules/esnext.set.difference.js");
/* harmony import */ var core_js_modules_esnext_set_difference_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_difference_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_esnext_set_every_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/esnext.set.every.js */ "./node_modules/core-js/modules/esnext.set.every.js");
/* harmony import */ var core_js_modules_esnext_set_every_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_every_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_esnext_set_filter_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/esnext.set.filter.js */ "./node_modules/core-js/modules/esnext.set.filter.js");
/* harmony import */ var core_js_modules_esnext_set_filter_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_filter_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_esnext_set_find_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/esnext.set.find.js */ "./node_modules/core-js/modules/esnext.set.find.js");
/* harmony import */ var core_js_modules_esnext_set_find_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_find_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_esnext_set_intersection_v2_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/esnext.set.intersection.v2.js */ "./node_modules/core-js/modules/esnext.set.intersection.v2.js");
/* harmony import */ var core_js_modules_esnext_set_intersection_v2_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_intersection_v2_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_esnext_set_intersection_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/esnext.set.intersection.js */ "./node_modules/core-js/modules/esnext.set.intersection.js");
/* harmony import */ var core_js_modules_esnext_set_intersection_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_intersection_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_esnext_set_is_disjoint_from_v2_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/esnext.set.is-disjoint-from.v2.js */ "./node_modules/core-js/modules/esnext.set.is-disjoint-from.v2.js");
/* harmony import */ var core_js_modules_esnext_set_is_disjoint_from_v2_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_is_disjoint_from_v2_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_esnext_set_is_disjoint_from_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! core-js/modules/esnext.set.is-disjoint-from.js */ "./node_modules/core-js/modules/esnext.set.is-disjoint-from.js");
/* harmony import */ var core_js_modules_esnext_set_is_disjoint_from_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_is_disjoint_from_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var core_js_modules_esnext_set_is_subset_of_v2_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core-js/modules/esnext.set.is-subset-of.v2.js */ "./node_modules/core-js/modules/esnext.set.is-subset-of.v2.js");
/* harmony import */ var core_js_modules_esnext_set_is_subset_of_v2_js__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_is_subset_of_v2_js__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var core_js_modules_esnext_set_is_subset_of_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! core-js/modules/esnext.set.is-subset-of.js */ "./node_modules/core-js/modules/esnext.set.is-subset-of.js");
/* harmony import */ var core_js_modules_esnext_set_is_subset_of_js__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_is_subset_of_js__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var core_js_modules_esnext_set_is_superset_of_v2_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! core-js/modules/esnext.set.is-superset-of.v2.js */ "./node_modules/core-js/modules/esnext.set.is-superset-of.v2.js");
/* harmony import */ var core_js_modules_esnext_set_is_superset_of_v2_js__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_is_superset_of_v2_js__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var core_js_modules_esnext_set_is_superset_of_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! core-js/modules/esnext.set.is-superset-of.js */ "./node_modules/core-js/modules/esnext.set.is-superset-of.js");
/* harmony import */ var core_js_modules_esnext_set_is_superset_of_js__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_is_superset_of_js__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var core_js_modules_esnext_set_join_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! core-js/modules/esnext.set.join.js */ "./node_modules/core-js/modules/esnext.set.join.js");
/* harmony import */ var core_js_modules_esnext_set_join_js__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_join_js__WEBPACK_IMPORTED_MODULE_17__);
/* harmony import */ var core_js_modules_esnext_set_map_js__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! core-js/modules/esnext.set.map.js */ "./node_modules/core-js/modules/esnext.set.map.js");
/* harmony import */ var core_js_modules_esnext_set_map_js__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_map_js__WEBPACK_IMPORTED_MODULE_18__);
/* harmony import */ var core_js_modules_esnext_set_reduce_js__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! core-js/modules/esnext.set.reduce.js */ "./node_modules/core-js/modules/esnext.set.reduce.js");
/* harmony import */ var core_js_modules_esnext_set_reduce_js__WEBPACK_IMPORTED_MODULE_19___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_reduce_js__WEBPACK_IMPORTED_MODULE_19__);
/* harmony import */ var core_js_modules_esnext_set_some_js__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! core-js/modules/esnext.set.some.js */ "./node_modules/core-js/modules/esnext.set.some.js");
/* harmony import */ var core_js_modules_esnext_set_some_js__WEBPACK_IMPORTED_MODULE_20___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_some_js__WEBPACK_IMPORTED_MODULE_20__);
/* harmony import */ var core_js_modules_esnext_set_symmetric_difference_v2_js__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! core-js/modules/esnext.set.symmetric-difference.v2.js */ "./node_modules/core-js/modules/esnext.set.symmetric-difference.v2.js");
/* harmony import */ var core_js_modules_esnext_set_symmetric_difference_v2_js__WEBPACK_IMPORTED_MODULE_21___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_symmetric_difference_v2_js__WEBPACK_IMPORTED_MODULE_21__);
/* harmony import */ var core_js_modules_esnext_set_symmetric_difference_js__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! core-js/modules/esnext.set.symmetric-difference.js */ "./node_modules/core-js/modules/esnext.set.symmetric-difference.js");
/* harmony import */ var core_js_modules_esnext_set_symmetric_difference_js__WEBPACK_IMPORTED_MODULE_22___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_symmetric_difference_js__WEBPACK_IMPORTED_MODULE_22__);
/* harmony import */ var core_js_modules_esnext_set_union_v2_js__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! core-js/modules/esnext.set.union.v2.js */ "./node_modules/core-js/modules/esnext.set.union.v2.js");
/* harmony import */ var core_js_modules_esnext_set_union_v2_js__WEBPACK_IMPORTED_MODULE_23___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_union_v2_js__WEBPACK_IMPORTED_MODULE_23__);
/* harmony import */ var core_js_modules_esnext_set_union_js__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! core-js/modules/esnext.set.union.js */ "./node_modules/core-js/modules/esnext.set.union.js");
/* harmony import */ var core_js_modules_esnext_set_union_js__WEBPACK_IMPORTED_MODULE_24___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_set_union_js__WEBPACK_IMPORTED_MODULE_24__);
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_25___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_25__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_26___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_26__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_27___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_27__);
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! external/jquery */ "external/jquery");
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_28___default = /*#__PURE__*/__webpack_require__.n(external_jquery__WEBPACK_IMPORTED_MODULE_28__);





























/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkTreeItemSelector',
  template: `
        <div class="item" :style="itemMargin">
            <i :class="toggleIcon" v-show="!isRoot" @click="onToggleShow" />
            <i :class="getIcon" v-show="!isRoot" @click="onToggleSelect" />
            <div class="content">
                <div :style="itemCursor" @click="onToggleSelect">{{title}}</div>
                <div v-if="isParent" class="list" v-show="open || isRoot">
                    <AtkTreeItemSelector
                        v-for="item in item.nodes" :key="item.id"
                        :item="item"
                        :values="values"
                    ></AtkTreeItemSelector>
                </div>
            </div>
        </div>`,
  props: {
    item: Object,
    values: Array
  },
  inject: ['getRootData'],
  data: function () {
    return {
      open: false,
      isRoot: this.item.id === 'atk-root',
      isInitialized: false,
      id: this.item.id,
      nodes: this.item.nodes,
      icons: {
        single: {
          on: 'circle',
          off: 'circle outline',
          indeterminate: 'dot circle outline'
        },
        multiple: {
          on: 'check square outline',
          off: 'square outline',
          indeterminate: 'minus square outline'
        }
      }
    };
  },
  created: function () {
    this.getInitData();
  },
  mounted: function () {},
  computed: {
    itemMargin: function () {
      return {
        marginLeft: this.item.nodes && this.item.nodes.length > 0 ? this.open ? '-13px' : '-10px' : null
      };
    },
    itemCursor: function () {
      return {
        cursor: this.isParent && this.getRootData().options.mode === 'single' ? 'default' : 'pointer'
      };
    },
    title: function () {
      return this.item.name;
    },
    isParent: function () {
      return this.nodes && this.nodes.length > 0;
    },
    toggleIcon: function () {
      return this.isParent ? (this.open ? 'caret down' : 'caret right') + ' icon' : null;
    },
    state: function () {
      let state = 'off';
      if (this.isParent) {
        state = this.hasAllFill(this.nodes) ? 'on' : this.hasSomeFill(this.nodes) ? 'indeterminate' : 'off';
      } else if (this.isSelected(this.id)) {
        state = 'on';
      }
      return state;
    },
    getIcon: function () {
      return this.icons[this.getRootData().options.mode][this.state] + ' icon';
    }
  },
  methods: {
    isSelected: function (id) {
      return this.values.includes(id);
    },
    /**
     * Get input initial data.
     */
    getInitData: function () {
      // check if input containing data is set and initialized
      if (!this.getRootData().item.isInitialized) {
        this.getRootData().values = this.getValues();
        this.getRootData().item.isInitialized = true;
      }
    },
    getValues: function () {
      const initValues = JSON.parse(this.getInputElement().value);
      let values = [];
      if (Array.isArray(initValues)) {
        values = initValues;
      } else {
        values.push(initValues);
      }
      return values;
    },
    /**
     * Check if all children nodes are on.
     *
     * @returns {boolean}
     */
    hasAllFill: function (nodes) {
      let state = true;
      for (const node of nodes) {
        // check children first;
        if (node.nodes && node.nodes.length > 0) {
          if (!this.hasAllFill(node.nodes)) {
            state = false;
            break;
          }
        } else if (!this.values.includes(node.id)) {
          state = false;
          break;
        }
      }
      return state;
    },
    /**
     * Check if some children nodes are on.
     *
     * @returns {boolean}
     */
    hasSomeFill: function (nodes) {
      let state = false;
      for (const node of nodes) {
        // check children first;
        if (node.nodes && node.nodes.length > 0) {
          if (this.hasSomeFill(node.nodes)) {
            state = true;
            break;
          }
        }
        if (this.values.includes(node.id)) {
          state = true;
          break;
        }
      }
      return state;
    },
    /**
     * Fire when arrow are click in order to show or hide children.
     */
    onToggleShow: function () {
      if (this.isParent) {
        this.open = !this.open;
      }
    },
    /**
     * Fire when checkbox is click.
     */
    onToggleSelect: function () {
      const {
        options
      } = this.getRootData();
      switch (options.mode) {
        case 'single':
          {
            this.handleSingleSelect();
            break;
          }
        case 'multiple':
          {
            this.handleMultipleSelect();
            break;
          }
      }
    },
    /**
     * Merge array and remove duplicate.
     *
     * @returns {*[]}
     */
    mergeArrays: function () {
      let jointArray = [];
      for (var _len = arguments.length, arrays = new Array(_len), _key = 0; _key < _len; _key++) {
        arrays[_key] = arguments[_key];
      }
      for (const array of arrays) {
        jointArray = [...jointArray, ...array];
      }
      return [...new Set(jointArray)];
    },
    /**
     * Get all ID from all children node.
     *
     * @returns {Array.<string>}
     */
    collectAllChildren: function (nodes) {
      let ids = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
      for (const node of nodes) {
        if (node.nodes && node.nodes.length > 0) {
          ids = [...ids, ...this.collectAllChildren(node.nodes, ids)];
        } else {
          ids.push(node.id);
        }
      }
      return ids;
    },
    remove: function (values, value) {
      return values.filter(val => val !== value);
    },
    /**
     * Handle a selection when in single mode.
     */
    handleSingleSelect: function () {
      if (this.state === 'off' && !this.isParent) {
        this.getRootData().values = [this.item.id];
        this.setInput(this.item.id);
        if (this.getRootData().options.url) {
          this.postValue();
        }
      }
      if (this.isParent) {
        this.open = !this.open;
      }
    },
    /**
     * Handle a selection when in multiple mode.
     */
    handleMultipleSelect: function () {
      let values;
      if (this.isParent) {
        // collect all children value
        const childValues = this.collectAllChildren(this.nodes);
        if (this.state === 'off' || this.state === 'indeterminate') {
          values = this.mergeArrays(this.values, childValues);
        } else {
          let temp = this.values;
          for (const value of childValues) {
            temp = this.remove(temp, value);
          }
          values = temp;
        }
      } else if (this.state === 'on') {
        values = this.remove(this.values, this.item.id);
      } else if (this.state === 'off') {
        values = this.values;
        values.push(this.item.id);
      }
      this.getRootData().values = [...values];
      this.setInput(JSON.stringify(values));
      if (this.getRootData().options.url) {
        this.postValue();
      }
    },
    /**
     * Set input field with current mapped model value.
     */
    setInput: function (value) {
      this.getInputElement().value = value;
    },
    /**
     * Get input element set for this Item Selector.
     *
     * @returns {HTMLElement}
     */
    getInputElement: function () {
      return document.getElementsByName(this.getRootData().field)[0];
    },
    /**
     * Send data using callback URL.
     */
    postValue: function () {
      external_jquery__WEBPACK_IMPORTED_MODULE_28___default()(this.$el).parents('.' + this.getRootData().options.loader).api({
        on: 'now',
        url: this.getRootData().options.url,
        method: 'POST',
        data: {
          data: JSON.stringify(this.getRootData().values)
        }
      });
    }
  }
});

/***/ })

}]);
//# sourceMappingURL=atk-vue-tree-item-selector.js.map