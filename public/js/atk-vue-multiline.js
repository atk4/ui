"use strict";
(self["webpackChunkatk"] = self["webpackChunkatk"] || []).push([["atk-vue-multiline"],{

/***/ "./src/vue-components/multiline/multiline-body.component.js":
/*!******************************************************************!*\
  !*** ./src/vue-components/multiline/multiline-body.component.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _multiline_row_component__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./multiline-row.component */ "./src/vue-components/multiline/multiline-row.component.js");

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-multiline-body',
  template: `
    <sui-table-body>
      <atk-multiline-row v-for="(row, idx) in rows" :key="row.__atkml"
      @onTabLastColumn="onTabLastColumn(idx)"
      :fields="fields"
      :rowId="row.__atkml"
      :isDeletable="isDeletableRow(row)"
      :rowValues="row"
      :error="getError(row.__atkml)"></atk-multiline-row>
    </sui-table-body>
  `,
  props: ['fieldDefs', 'rowData', 'deletables', 'errors'],
  data: function () {
    return {
      fields: this.fieldDefs
    };
  },
  created: function () {},
  components: {
    'atk-multiline-row': _multiline_row_component__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  computed: {
    rows: function () {
      return this.rowData;
    }
  },
  methods: {
    onTabLastColumn: function (idx) {
      if (idx + 1 === this.rowData.length) {
        this.$emit('onTabLastRow');
      }
    },
    isDeletableRow: function (row) {
      return this.deletables.indexOf(row.__atkml) > -1;
    },
    getError: function (rowId) {
      if (rowId in this.errors) {
        return this.errors[rowId];
      }
      return null;
    }
  }
});

/***/ }),

/***/ "./src/vue-components/multiline/multiline-cell.component.js":
/*!******************************************************************!*\
  !*** ./src/vue-components/multiline/multiline-cell.component.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _multiline_readonly_component__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./multiline-readonly.component */ "./src/vue-components/multiline/multiline-readonly.component.js");
/* harmony import */ var _multiline_textarea_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./multiline-textarea.component */ "./src/vue-components/multiline/multiline-textarea.component.js");
/* harmony import */ var _share_atk_date_picker__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../share/atk-date-picker */ "./src/vue-components/share/atk-date-picker.js");
/* harmony import */ var _share_atk_lookup__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../share/atk-lookup */ "./src/vue-components/share/atk-lookup.js");




/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-multiline-cell',
  template: `
    <component :is="getComponent()"
        :fluid="true"
        class="fluid"
        @input="onInput"
        @onChange="onChange"
        v-model="inputValue"
        :name="inputName"
        ref="cell"
        v-bind="getComponentProps()"></component>
  `,
  components: {
    'atk-multiline-readonly': _multiline_readonly_component__WEBPACK_IMPORTED_MODULE_0__["default"],
    'atk-multiline-textarea': _multiline_textarea_component__WEBPACK_IMPORTED_MODULE_1__["default"],
    'atk-date-picker': _share_atk_date_picker__WEBPACK_IMPORTED_MODULE_2__["default"],
    'atk-lookup': _share_atk_lookup__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  props: ['cellData', 'fieldValue'],
  data: function () {
    return {
      fieldName: this.cellData.name,
      type: this.cellData.type,
      inputName: '-' + this.cellData.name,
      inputValue: this.fieldValue
    };
  },
  methods: {
    getComponent: function () {
      return this.cellData.definition.component;
    },
    getComponentProps: function () {
      if (this.getComponent() === 'atk-multiline-readonly') {
        return {
          readOnlyValue: this.fieldValue
        };
      }
      return this.cellData.definition.componentProps;
    },
    onInput: function (value) {
      this.inputValue = this.getTypeValue(value);
      this.$emit('update-value', this.fieldName, this.inputValue);
    },
    onChange: function (value) {
      this.onInput(value);
    },
    /**
     * return input value based on their type.
     */
    getTypeValue: function (value) {
      let r = value;
      if (this.type === 'boolean') {
        r = value.target.checked;
      }
      return r;
    }
  }
});

/***/ }),

/***/ "./src/vue-components/multiline/multiline-header.component.js":
/*!********************************************************************!*\
  !*** ./src/vue-components/multiline/multiline-header.component.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_esnext_async_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.for-each.js */ "./node_modules/core-js/modules/esnext.async-iterator.for-each.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "./node_modules/core-js/modules/esnext.iterator.for-each.js");
/* harmony import */ var core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");






/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-multiline-header',
  template: `
     <sui-table-header>
       <sui-table-row v-if="hasError()">
        <sui-table-cell :style="{ background: 'none' }"></sui-table-cell>
        <sui-table-cell :style="{ background: 'none' }" state="error" v-for="(column, idx) in columns" :key="idx" v-if="column.isVisible" :textAlign="getTextAlign(column)"><sui-icon name="attention" v-if="getErrorMsg(column)"></sui-icon>{{getErrorMsg(column)}}</sui-table-cell>
      </sui-table-row>
       <sui-table-row v-if="hasCaption()">
        <sui-table-headerCell :colspan="getVisibleColumns()">{{caption}}</sui-table-headerCell>
       </sui-table-row>
        <sui-table-row :verticalAlign="'top'">
        <sui-table-header-cell width="one" textAlign="center"><input type="checkbox" @input="onToggleDeleteAll" :checked.prop="isChecked" :indeterminate.prop="isIndeterminate" ref="check"></sui-table-header-cell>
        <sui-table-header-cell v-for="(column, idx) in columns" :key="idx" v-if="column.isVisible" :textAlign="getTextAlign(column)">
         <div>{{column.caption}}</div>
         <div :style="{ position: 'absolute', top: '-22px' }" v-if="false"><sui-label pointing="below" basic color="red" v-if="getErrorMsg(column)">{{getErrorMsg(column)}}</sui-label></div>
        </sui-table-header-cell>
      </sui-table-row>
    </sui-table-header>
  `,
  props: ['fields', 'state', 'errors', 'caption'],
  data: function () {
    return {
      columns: this.fields,
      isDeleteAll: false
    };
  },
  methods: {
    onToggleDeleteAll: function () {
      this.$nextTick(() => {
        atk__WEBPACK_IMPORTED_MODULE_5__["default"].eventBus.emit(this.$root.$el.id + '-toggle-delete-all', {
          isOn: this.$refs.check.checked
        });
      });
    },
    getTextAlign: function (column) {
      let align = 'left';
      if (!column.isEditable) {
        switch (column.type) {
          case 'integer':
          case 'float':
          case 'atk4_money':
            align = 'right';
            break;
        }
      }
      return align;
    },
    getVisibleColumns: function () {
      let count = 1; // add deletable column;
      this.columns.forEach(field => {
        count = field.isVisible ? count + 1 : count;
      });
      return count;
    },
    hasError: function () {
      return Object.keys(this.errors).length > 0;
    },
    hasCaption: function () {
      return this.caption;
    },
    getErrorMsg: function (column) {
      if (this.hasError()) {
        const rows = Object.keys(this.errors);
        for (let i = 0; i < rows.length; i++) {
          const error = this.errors[rows[i]].filter(col => col.name === column.name);
          if (error.length > 0) {
            return error[0].msg;
          }
        }
      }
      return null;
    }
  },
  computed: {
    isIndeterminate: function () {
      return this.state === 'indeterminate';
    },
    isChecked: function () {
      return this.state === 'on';
    }
  }
});

/***/ }),

/***/ "./src/vue-components/multiline/multiline-readonly.component.js":
/*!**********************************************************************!*\
  !*** ./src/vue-components/multiline/multiline-readonly.component.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Simply display a readonly value.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  template: '<div>{{readOnlyValue}}</div>',
  name: 'atk-multiline-readonly',
  props: ['readOnlyValue']
});

/***/ }),

/***/ "./src/vue-components/multiline/multiline-row.component.js":
/*!*****************************************************************!*\
  !*** ./src/vue-components/multiline/multiline-row.component.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");
/* harmony import */ var _multiline_cell_component__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./multiline-cell.component */ "./src/vue-components/multiline/multiline-cell.component.js");






/**
 * This will create a table td element using sui-table-cell.
 * The td element is created only if column as set isVisible = true;
 * The td element will add a multiline cell element.
 * the multiline cell will set it's own template component depending on the fieldType.
 * getValue
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-multiline-row',
  template: `
    <sui-table-row :verticalAlign="'middle'">
        <sui-table-cell width="one" textAlign="center"><input type="checkbox" @input="onToggleDelete" v-model="toDelete"></sui-table-cell>
        <sui-table-cell @keydown.tab="onTab(idx)" v-for="(column, idx) in columns" :key="idx" :state="getErrorState(column)" v-bind="column.cellProps" :style="{ overflow: 'visible' }" v-if="column.isVisible">
         <atk-multiline-cell
           :cellData="column"
           @update-value="onUpdateValue"
           :fieldValue="getValue(column)"></atk-multiline-cell>
        </sui-table-cell>
    </sui-table-row>
  `,
  props: ['fields', 'rowId', 'isDeletable', 'rowValues', 'error'],
  data: function () {
    return {
      columns: this.fields
    };
  },
  components: {
    'atk-multiline-cell': _multiline_cell_component__WEBPACK_IMPORTED_MODULE_4__["default"]
  },
  computed: {
    /**
     * toDelete is bind by v-model, thus we need a setter for
     * computed property to work.
     * When isDeletable is pass, will set checkbox according to it's value.
     */
    toDelete: {
      get: function () {
        return this.isDeletable;
      },
      set: function (v) {
        return v;
      }
    }
  },
  methods: {
    onTab: function (idx) {
      if (idx === this.columns.filter(column => column.isEditable).length) {
        this.$emit('onTabLastColumn');
      }
    },
    getErrorState: function (column) {
      if (this.error) {
        const error = this.error.filter(e => column.name === e.name);
        if (error.length > 0) {
          return 'error';
        }
      }
      return null;
    },
    getColumnWidth: function (column) {
      return column.fieldOptions ? column.fieldOptions.width : null;
    },
    onEdit: function () {
      this.isEditing = true;
    },
    onToggleDelete: function (e) {
      atk__WEBPACK_IMPORTED_MODULE_3__["default"].eventBus.emit(this.$root.$el.id + '-toggle-delete', {
        rowId: this.rowId
      });
    },
    onUpdateValue: function (fieldName, value) {
      atk__WEBPACK_IMPORTED_MODULE_3__["default"].eventBus.emit(this.$root.$el.id + '-update-row', {
        rowId: this.rowId,
        fieldName: fieldName,
        value: value
      });
    },
    getValue: function (column) {
      return this.rowValues[column.name] || column.default;
    }
  }
});

/***/ }),

/***/ "./src/vue-components/multiline/multiline-textarea.component.js":
/*!**********************************************************************!*\
  !*** ./src/vue-components/multiline/multiline-textarea.component.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-textarea',
  template: '<textarea v-model="text" @input="handleChange"></textarea>',
  props: {
    value: [String, Number]
  },
  data: function () {
    return {
      text: this.value
    };
  },
  methods: {
    handleChange: function (event) {
      this.$emit('input', event.target.value);
    }
  }
});

/***/ }),

/***/ "./src/vue-components/multiline/multiline.component.js":
/*!*************************************************************!*\
  !*** ./src/vue-components/multiline/multiline.component.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_esnext_async_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.for-each.js */ "./node_modules/core-js/modules/esnext.async-iterator.for-each.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "./node_modules/core-js/modules/esnext.iterator.for-each.js");
/* harmony import */ var core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.find.js */ "./node_modules/core-js/modules/esnext.async-iterator.find.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "./node_modules/core-js/modules/esnext.iterator.find.js");
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! external/jquery */ "external/jquery");
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(external_jquery__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");
/* harmony import */ var _multiline_body_component__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./multiline-body.component */ "./src/vue-components/multiline/multiline-body.component.js");
/* harmony import */ var _multiline_header_component__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./multiline-header.component */ "./src/vue-components/multiline/multiline-header.component.js");











/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-multiline',
  template: `<div>
                <sui-table v-bind="tableProp">
                  <atk-multiline-header :fields="fieldData" :state="getMainToggleState" :errors="errors" :caption="caption"></atk-multiline-header>
                  <atk-multiline-body @onTabLastRow="onTabLastRow" :fieldDefs="fieldData" :rowData="rowData" :deletables="getDeletables" :errors="errors"></atk-multiline-body>
                  <sui-table-footer>
                    <sui-table-row>
                        <sui-table-header-cell />
                        <sui-table-header-cell :colspan="getSpan" textAlign="right">
                        <div is="sui-button-group">
                         <sui-button size="small" @click.stop.prevent="onAdd" type="button" icon="plus" ref="addBtn" :disabled="isLimitReached"></sui-button>
                         <sui-button size="small" @click.stop.prevent="onDelete" type="button" icon="trash" :disabled="isDeleteDisable"></sui-button>
                         </div>
                        </sui-table-header-cell>
                    </sui-table-row>
                  </sui-table-footer>
                </sui-table>
                <div><input :form="form" :name="name" type="hidden" :value="value" ref="atkmlInput"></div>
             </div>`,
  props: {
    data: Object
  },
  data: function () {
    const tableDefault = {
      basic: false,
      celled: false,
      collapsing: false,
      stackable: false,
      inverted: false
    };
    return {
      form: this.data.formName,
      value: this.data.inputValue,
      name: this.data.inputName,
      // form input name where to set multiline content value.
      rowData: [],
      fieldData: this.data.fields || [],
      eventFields: this.data.eventFields || [],
      deletables: [],
      hasChangeCb: this.data.hasChangeCb,
      errors: {},
      caption: this.data.caption || null,
      tableProp: {
        ...tableDefault,
        ...(this.data.tableProps || {})
      }
    };
  },
  components: {
    'atk-multiline-body': _multiline_body_component__WEBPACK_IMPORTED_MODULE_9__["default"],
    'atk-multiline-header': _multiline_header_component__WEBPACK_IMPORTED_MODULE_10__["default"]
  },
  mounted: function () {
    this.rowData = this.buildRowData(this.value ? this.value : '[]');
    this.updateInputValue();
    atk__WEBPACK_IMPORTED_MODULE_8__["default"].eventBus.on(this.$root.$el.id + '-update-row', payload => {
      this.onUpdate(payload.rowId, payload.fieldName, payload.value);
    });
    atk__WEBPACK_IMPORTED_MODULE_8__["default"].eventBus.on(this.$root.$el.id + '-toggle-delete', payload => {
      const idx = this.deletables.indexOf(payload.rowId);
      if (idx > -1) {
        this.deletables.splice(idx, 1);
      } else {
        this.deletables.push(payload.rowId);
      }
    });
    atk__WEBPACK_IMPORTED_MODULE_8__["default"].eventBus.on(this.$root.$el.id + '-toggle-delete-all', payload => {
      this.deletables = [];
      if (payload.isOn) {
        this.rowData.forEach(row => {
          this.deletables.push(row.__atkml);
        });
      }
    });
    atk__WEBPACK_IMPORTED_MODULE_8__["default"].eventBus.on(this.$root.$el.id + '-multiline-rows-error', payload => {
      this.errors = {
        ...payload.errors
      };
    });
  },
  methods: {
    onTabLastRow: function () {
      if (!this.isLimitReached && this.data.addOnTab) {
        this.onAdd();
      }
    },
    onAdd: function () {
      const newRow = this.createRow(this.data.fields);
      this.rowData.push(newRow);
      this.updateInputValue();
      if (this.data.afterAdd && typeof this.data.afterAdd === 'function') {
        this.data.afterAdd(JSON.parse(this.value));
      }
      this.fetchExpression(newRow.__atkml);
      this.fetchOnChangeAction();
    },
    onDelete: function () {
      this.deletables.forEach(atkmlId => {
        this.deleteRow(atkmlId);
      });
      this.deletables = [];
      this.updateInputValue();
      this.fetchOnChangeAction();
      if (this.data.afterDelete && typeof this.data.afterDelete === 'function') {
        this.data.afterDelete(JSON.parse(this.value));
      }
    },
    onUpdate: function (atkmlId, fieldName, value) {
      this.updateFieldInRow(atkmlId, fieldName, value);
      this.clearError(atkmlId, fieldName);
      this.updateInputValue();
      if (!this.onUpdate.debouncedFx) {
        this.onUpdate.debouncedFx = atk__WEBPACK_IMPORTED_MODULE_8__["default"].createDebouncedFx(() => {
          this.onUpdate.debouncedFx = null;
          this.fetchExpression(atkmlId);
          this.fetchOnChangeAction(fieldName);
        }, 250);
      }
      this.onUpdate.debouncedFx.call(this);
    },
    /**
     * Creates a new row of data and
     * set values to default if available.
     */
    createRow: function (fields) {
      const row = {};
      fields.forEach(field => {
        row[field.name] = field.default;
      });
      row.__atkml = this.getUUID();
      return row;
    },
    deleteRow: function (atkmlId) {
      this.rowData.splice(this.rowData.findIndex(row => row.__atkml === atkmlId), 1);
      delete this.errors[atkmlId];
    },
    /**
     * Update the value of the field in rowData.
     */
    updateFieldInRow: function (atkmlId, fieldName, value) {
      this.rowData.forEach(row => {
        if (row.__atkml === atkmlId) {
          row[fieldName] = value;
        }
      });
    },
    clearError: function (atkmlId, fieldName) {
      if (atkmlId in this.errors) {
        const errors = this.errors[atkmlId].filter(error => error.name !== fieldName);
        this.errors[atkmlId] = [...errors];
        if (errors.length === 0) {
          delete this.errors[atkmlId];
        }
      }
    },
    /**
     * Update Multi-line Form input with all rowData values
     * as JSON string.
     */
    updateInputValue: function () {
      this.value = JSON.stringify(this.rowData);
    },
    /**
     * Build rowData from JSON string.
     */
    buildRowData: function (jsonValue) {
      const rows = JSON.parse(jsonValue);
      rows.forEach(row => {
        row.__atkml = this.getUUID();
      });
      return rows;
    },
    /**
     * Check if one of the field use expression.
     */
    hasExpression: function () {
      return this.fieldData.filter(field => field.isExpr).length > 0;
    },
    /**
     * Send on change action to server.
     * Use regular api call in order
     * for return js to be fully evaluated.
     */
    fetchOnChangeAction: function () {
      let fieldName = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      if (this.hasChangeCb && (fieldName === null || this.eventFields.indexOf(fieldName) > -1)) {
        external_jquery__WEBPACK_IMPORTED_MODULE_7___default()(this.$refs.addBtn.$el).api({
          on: 'now',
          url: this.data.url,
          method: 'POST',
          data: {
            __atkml_action: 'on-change',
            rows: this.value
          }
        });
      }
    },
    postData: async function (row) {
      const data = {
        ...row
      };
      const context = this.$refs.addBtn.$el;
      data.__atkml_action = 'update-row';
      try {
        return await atk__WEBPACK_IMPORTED_MODULE_8__["default"].apiService.suiFetch(this.data.url, {
          data: data,
          method: 'POST',
          stateContext: context
        });
      } catch (e) {
        console.error(e);
      }
    },
    /**
     * Get expressions values from server.
     */
    fetchExpression: async function (atkmlId) {
      if (this.hasExpression()) {
        const row = this.findRow(atkmlId);
        // server will return expression field - value if define.
        if (row) {
          const resp = await this.postData(row);
          if (resp.expressions) {
            const fields = Object.keys(resp.expressions);
            fields.forEach(field => {
              this.updateFieldInRow(atkmlId, field, resp.expressions[field]);
            });
            this.updateInputValue();
          }
        }
      }
    },
    findRow: function (atkmlId) {
      return this.rowData.find(row => row.__atkml === atkmlId);
    },
    getInputElement: function () {
      return this.$refs.atkmlInput;
    },
    /**
     * UUID v4 generator.
     */
    getUUID: function () {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.floor(Math.random() * 16);
        const v = c === 'x' ? r : r & (0x3 | 0x8); // eslint-disable-line no-bitwise

        return v.toString(16);
      });
    }
  },
  computed: {
    getSpan: function () {
      return this.fieldData.length - 1;
    },
    getDeletables: function () {
      return this.deletables;
    },
    /**
     * Return Delete all checkbox state base on
     * deletables entries.
     */
    getMainToggleState: function () {
      let state = 'off';
      if (this.deletables.length > 0) {
        if (this.deletables.length === this.rowData.length) {
          state = 'on';
        } else {
          state = 'indeterminate';
        }
      }
      return state;
    },
    isDeleteDisable: function () {
      return !this.deletables.length > 0;
    },
    isLimitReached: function () {
      if (this.data.rowLimit === 0) {
        return false;
      }
      return this.data.rowLimit < this.rowData.length + 1;
    }
  }
});

/***/ }),

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
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Props:
 * config: Any of flatpickr options
 *
 * Will emit a dateChange event when date is set.
 */

const template = '<flatpickr-picker v-model="date" :config="flatPickr" @on-change="onChange"></flatpickr-picker>';
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-date-picker',
  template: template,
  props: ['config', 'value'],
  data: function () {
    const {
      useDefault,
      ...fpickr
    } = this.config;
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
      date: null
    };
  },
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
  methods: {
    onChange: function (date) {
      this.$emit('onChange', flatpickr.formatDate(date[0], this.flatPickr.dateFormat));
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
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");


/**
 * Wrapper for Fomantic-UI dropdown component into a lookup component.
 *
 * Props:
 * config:
 * url: the callback URL. Callback should return model data in form of { key: modelId, text: modelTitle, value: modelId }
 * reference: the reference field name associate with model or hasOne name. This field name will be sent along with URL callback parameter as of 'field=name'.
 * ui: the css class name to apply to dropdown.
 * Note: The remaining config object may contain any or sui-dropdown { props: value } pair.
 *
 * value: The selected value.
 * optionalValue: The initial list of options for the dropdown.
 */

const template = `<sui-dropdown
                    ref="drop"
                    v-bind="dropdownProps"
                    :loading="isLoading"
                    @input="onChange"
                    @filtered="onFiltered"
                    v-model="current"
                    :class="css"></sui-dropdown>`;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'atk-lookup',
  template: template,
  props: ['config', 'value', 'optionalValue'],
  data: function () {
    const {
      url,
      reference,
      ui,
      ...suiDropdown
    } = this.config;
    suiDropdown.selection = true;
    return {
      dropdownProps: suiDropdown,
      current: this.value,
      url: url || null,
      css: [ui],
      isLoading: false,
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
  methods: {
    onChange: function (value) {
      this.$emit('onChange', value);
    },
    /**
     * Receive user input text for search.
     */
    onFiltered: function (inputValue) {
      if (inputValue) {
        this.isLoading = true;
      }
      if (!this.onFiltered.debouncedFx) {
        this.onFiltered.debouncedFx = atk__WEBPACK_IMPORTED_MODULE_0__["default"].createDebouncedFx(() => {
          this.onFiltered.debouncedFx = null;
          if (this.query !== this.temp) {
            this.query = this.temp;
            if (this.query) {
              this.fetchItems(this.query);
            }
          }
        }, 250);
      }
      this.temp = inputValue;
      this.onFiltered.debouncedFx(this);
    },
    /**
     * Fetch new data from server.
     */
    fetchItems: async function (q) {
      try {
        const data = {
          atkVueLookupQuery: q,
          atkVueLookupField: this.field
        };
        const response = await atk__WEBPACK_IMPORTED_MODULE_0__["default"].apiService.suiFetch(this.url, {
          method: 'get',
          data: data
        });
        if (response.success) {
          this.dropdownProps.options = response.results;
        }
      } catch (e) {
        console.error(e);
      } finally {
        this.isLoading = false;
      }
    }
  }
});

/***/ })

}]);
//# sourceMappingURL=atk-vue-multiline.js.map