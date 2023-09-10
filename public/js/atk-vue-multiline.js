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
  name: 'AtkMultilineBody',
  template: `
        <SuiTableBody>
            <AtkMultilineRow
                :fields="fields"
                v-for="(row, i) in rows" :key="row.__atkml"
                :rowId="row.__atkml"
                :isDeletable="isDeletableRow(row)"
                :rowValues="row"
                :errors="getRowErrors(row.__atkml)"
                @onTabLastColumn="onTabLastColumn(i)"
            ></AtkMultilineRow>
        </SuiTableBody>`,
  props: ['fieldDefs', 'rowData', 'deletables', 'errors'],
  data: function () {
    return {
      fields: this.fieldDefs
    };
  },
  created: function () {},
  components: {
    AtkMultilineRow: _multiline_row_component__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  computed: {
    rows: function () {
      return this.rowData;
    }
  },
  emits: ['onTabLastRow'],
  methods: {
    onTabLastColumn: function (rowIndex) {
      if (rowIndex + 1 === this.rowData.length) {
        this.$emit('onTabLastRow');
      }
    },
    isDeletableRow: function (row) {
      return this.deletables.includes(row.__atkml);
    },
    getRowErrors: function (rowId) {
      return this.errors[rowId] ?? [];
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
  name: 'AtkMultilineCell',
  template: `
        <component
            :is="getComponent()"
            v-bind="getComponentProps()"
            ref="cell"
            :fluid="true"
            class="fluid"
            :name="inputName"
            v-model="inputValue"
            @update:modelValue="onInput"
        ></component>`,
  components: {
    AtkMultilineReadonly: _multiline_readonly_component__WEBPACK_IMPORTED_MODULE_0__["default"],
    AtkMultilineTextarea: _multiline_textarea_component__WEBPACK_IMPORTED_MODULE_1__["default"],
    AtkDatePicker: _share_atk_date_picker__WEBPACK_IMPORTED_MODULE_2__["default"],
    AtkLookup: _share_atk_lookup__WEBPACK_IMPORTED_MODULE_3__["default"]
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
  emits: ['updateValue'],
  methods: {
    getComponent: function () {
      return this.cellData.definition.component;
    },
    getComponentProps: function () {
      if (this.getComponent() === 'AtkMultilineReadonly') {
        return {
          readOnlyValue: this.fieldValue
        };
      }
      return this.cellData.definition.componentProps;
    },
    onInput: function (value) {
      this.inputValue = value;
      this.$emit('updateValue', this.fieldName, this.inputValue);
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
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");




/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkMultilineHeader',
  template: `
        <SuiTableHeader>
            <SuiTableRow v-if="hasError()">
                <SuiTableCell :style="{ background: 'none' }" />
                <SuiTableCell :style="{ background: 'none' }"
                    error="true"
                    v-for="column in filterVisibleColumns(columns)"
                    :textAlign="getTextAlign(column)"
                >
                    <SuiIcon v-if="getErrorMsg(column)" name="attention" />
                    {{getErrorMsg(column)}}
                </SuiTableCell>
            </SuiTableRow>
            <SuiTableRow v-if="hasCaption()">
                <SuiTableHeaderCell :colspan="getVisibleColumns()">{{caption}}</SuiTableHeaderCell>
            </SuiTableRow>
            <SuiTableRow :verticalAlign="'top'">
                <SuiTableHeaderCell :width=1 textAlign="center">
                    <input ref="check" type="checkbox" :checked="isChecked" :indeterminate="isIndeterminate" @input="onToggleDeleteAll" />
                </SuiTableHeaderCell>
                <SuiTableHeaderCell
                    v-for="column in filterVisibleColumns(columns)"
                    :width=column.cellProps.width
                    :textAlign="getTextAlign(column)"
                >
                    <div>{{column.caption}}</div>
                    <div v-if="false" :style="{ position: 'absolute', top: '-22px' }">
                        <SuiLabel v-if="getErrorMsg(column)" pointing="below" basic color="red">{{getErrorMsg(column)}}</SuiLabel>
                    </div>
                </SuiTableHeaderCell>
            </SuiTableRow>
        </SuiTableHeader>`,
  props: ['fields', 'selectionState', 'errors', 'caption'],
  data: function () {
    return {
      columns: this.fields,
      isDeleteAll: false
    };
  },
  methods: {
    filterVisibleColumns: function (columns) {
      return columns.filter(v => v.isVisible);
    },
    onToggleDeleteAll: function () {
      this.$nextTick(() => {
        atk__WEBPACK_IMPORTED_MODULE_3__["default"].eventBus.emit(this.$root.$el.parentElement.id + '-toggle-delete-all', {
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
            {
              align = 'right';
              break;
            }
        }
      }
      return align;
    },
    getVisibleColumns: function () {
      let count = 1; // add deletable column;
      for (const field of this.columns) {
        count = field.isVisible ? count + 1 : count;
      }
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
        for (const row of rows) {
          const error = this.errors[row].filter(col => col.name === column.name);
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
      return this.selectionState === 'indeterminate';
    },
    isChecked: function () {
      return this.selectionState === 'on';
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
  name: 'AtkMultilineReadonly',
  template: '<div>{{readOnlyValue}}</div>',
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
/* harmony import */ var core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.some.js */ "./node_modules/core-js/modules/esnext.async-iterator.some.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.iterator.some.js */ "./node_modules/core-js/modules/esnext.iterator.some.js");
/* harmony import */ var core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");
/* harmony import */ var _multiline_cell_component__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./multiline-cell.component */ "./src/vue-components/multiline/multiline-cell.component.js");








/**
 * This will create a table td element using SuiTableCell.
 * The td element is created only if column as set isVisible = true;
 * The td element will add a multiline cell element.
 * the multiline cell will set it's own template component depending on the fieldType.
 * getValue
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkMultilineRow',
  template: `
        <SuiTableRow :verticalAlign="'middle'">
            <SuiTableCell textAlign="center">
                <input type="checkbox" v-model="toDelete" @input="onToggleDelete" />
            </SuiTableCell>
            <SuiTableCell
                v-for="(column, i) in filterVisibleColumns(columns)"
                v-bind="column.cellProps"
                :width=null
                :error="hasColumnError(column)"
                :style="{ overflow: 'visible' }"
                @keydown.tab="onTab(i)"
            >
                <AtkMultilineCell
                    :cellData="column"
                    :fieldValue="getValue(column)"
                    @updateValue="onUpdateValue"
                ></AtkMultilineCell>
            </SuiTableCell>
        </SuiTableRow>`,
  props: ['fields', 'rowId', 'isDeletable', 'rowValues', 'errors'],
  data: function () {
    return {
      columns: this.fields
    };
  },
  components: {
    AtkMultilineCell: _multiline_cell_component__WEBPACK_IMPORTED_MODULE_6__["default"]
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
  emits: ['onTabLastColumn'],
  methods: {
    filterVisibleColumns: function (columns) {
      return columns.filter(v => v.isVisible);
    },
    onTab: function (columnIndex) {
      if (columnIndex === this.columns.filter(column => column.isEditable).length) {
        this.$emit('onTabLastColumn');
      }
    },
    hasColumnError: function (column) {
      return this.errors.some(v => column.name === v.name);
    },
    getColumnWidth: function (column) {
      return column.fieldOptions ? column.fieldOptions.width : null;
    },
    onEdit: function () {
      this.isEditing = true;
    },
    onToggleDelete: function (e) {
      atk__WEBPACK_IMPORTED_MODULE_5__["default"].eventBus.emit(this.$root.$el.parentElement.id + '-toggle-delete', {
        rowId: this.rowId
      });
    },
    onUpdateValue: function (fieldName, value) {
      atk__WEBPACK_IMPORTED_MODULE_5__["default"].eventBus.emit(this.$root.$el.parentElement.id + '-update-row', {
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
  name: 'AtkMultilineTextarea',
  template: '<textarea v-model="modelValue" @input="onInput" />',
  props: ['modelValue'],
  emits: ['update:modelValue'],
  methods: {
    onInput: function (event) {
      this.$emit('update:modelValue', event.target.value);
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
/* harmony import */ var core_js_modules_es_array_push_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.push.js */ "./node_modules/core-js/modules/es.array.push.js");
/* harmony import */ var core_js_modules_es_array_push_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_push_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_esnext_json_parse_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.json.parse.js */ "./node_modules/core-js/modules/esnext.json.parse.js");
/* harmony import */ var core_js_modules_esnext_json_parse_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_json_parse_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.some.js */ "./node_modules/core-js/modules/esnext.async-iterator.some.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/esnext.iterator.some.js */ "./node_modules/core-js/modules/esnext.iterator.some.js");
/* harmony import */ var core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.find.js */ "./node_modules/core-js/modules/esnext.async-iterator.find.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "./node_modules/core-js/modules/esnext.iterator.find.js");
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! external/jquery */ "external/jquery");
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(external_jquery__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");
/* harmony import */ var _multiline_body_component__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./multiline-body.component */ "./src/vue-components/multiline/multiline-body.component.js");
/* harmony import */ var _multiline_header_component__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./multiline-header.component */ "./src/vue-components/multiline/multiline-header.component.js");













/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkMultiline',
  template: `
        <div>
            <SuiTable v-bind="tableProp">
                <AtkMultilineHeader
                    :fields="fieldData"
                    :selectionState="getMainToggleState"
                    :errors="errors"
                    :caption="caption"
                ></AtkMultilineHeader>
                <AtkMultilineBody
                    :fieldDefs="fieldData"
                    :rowData="rowData"
                    :deletables="getDeletables"
                    :errors="errors"
                    @onTabLastRow="onTabLastRow"
                ></AtkMultilineBody>
                <SuiTableFooter>
                    <SuiTableRow>
                        <SuiTableHeaderCell />
                        <SuiTableHeaderCell :colspan="getSpan" textAlign="right">
                            <SuiButtonGroup>
                                <SuiButton ref="addButton" size="small" type="button" icon :disabled="isLimitReached" @click.stop.prevent="onAdd">
                                    <SuiIcon name="plus" />
                                </SuiButton>
                                <SuiButton size="small" type="button" icon :disabled="isDeleteDisable" @click.stop.prevent="onDelete">
                                    <SuiIcon name="trash" />
                                </SuiButton>
                            </SuiButtonGroup>
                        </SuiTableHeaderCell>
                    </SuiTableRow>
                </SuiTableFooter>
            </SuiTable>
            <div>
                <input ref="atkmlInput" :form="form" :name="name" type="hidden" :value="valueJson" />
            </div>
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
      valueJson: this.data.inputValue,
      name: this.data.inputName,
      rowData: [],
      fieldData: this.data.fields || [],
      eventFields: this.data.eventFields || [],
      deletables: [],
      hasChangeCb: this.data.hasChangeCb,
      errors: {},
      caption: this.data.caption || null,
      tableProp: {
        ...tableDefault,
        ...this.data.tableProps
      }
    };
  },
  components: {
    AtkMultilineHeader: _multiline_header_component__WEBPACK_IMPORTED_MODULE_12__["default"],
    AtkMultilineBody: _multiline_body_component__WEBPACK_IMPORTED_MODULE_11__["default"]
  },
  mounted: function () {
    this.rowData = this.buildRowData(this.valueJson ?? '[]');
    this.updateInputValue();
    atk__WEBPACK_IMPORTED_MODULE_10__["default"].eventBus.on(this.$root.$el.parentElement.id + '-update-row', payload => {
      this.onUpdate(payload.rowId, payload.fieldName, payload.value);
    });
    atk__WEBPACK_IMPORTED_MODULE_10__["default"].eventBus.on(this.$root.$el.parentElement.id + '-toggle-delete', payload => {
      const i = this.deletables.indexOf(payload.rowId);
      if (i !== -1) {
        this.deletables.splice(i, 1);
      } else {
        this.deletables.push(payload.rowId);
      }
    });
    atk__WEBPACK_IMPORTED_MODULE_10__["default"].eventBus.on(this.$root.$el.parentElement.id + '-toggle-delete-all', payload => {
      this.deletables = [];
      if (payload.isOn) {
        for (const row of this.rowData) {
          this.deletables.push(row.__atkml);
        }
      }
    });
    atk__WEBPACK_IMPORTED_MODULE_10__["default"].eventBus.on(this.$root.$el.parentElement.id + '-multiline-rows-error', payload => {
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
        this.data.afterAdd(JSON.parse(this.valueJson));
      }
      this.fetchExpression(newRow.__atkml);
      this.fetchOnUpdateAction();
    },
    onDelete: function () {
      for (const atkmlId of this.deletables) {
        this.deleteRow(atkmlId);
      }
      this.deletables = [];
      this.updateInputValue();
      this.fetchOnUpdateAction();
      if (this.data.afterDelete && typeof this.data.afterDelete === 'function') {
        this.data.afterDelete(JSON.parse(this.valueJson));
      }
    },
    onUpdate: function (atkmlId, fieldName, value) {
      this.updateFieldInRow(atkmlId, fieldName, value);
      this.clearError(atkmlId, fieldName);
      this.updateInputValue();
      if (!this.onUpdate.debouncedFx) {
        this.onUpdate.debouncedFx = atk__WEBPACK_IMPORTED_MODULE_10__["default"].createDebouncedFx(() => {
          this.onUpdate.debouncedFx = null;
          this.fetchExpression(atkmlId);
          this.fetchOnUpdateAction(fieldName);
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
      for (const field of fields) {
        row[field.name] = field.default;
      }
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
      for (const row of this.rowData) {
        if (row.__atkml === atkmlId) {
          row[fieldName] = value;
        }
      }
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
      this.valueJson = JSON.stringify(this.rowData);
    },
    /**
     * Build rowData from JSON string.
     */
    buildRowData: function (jsonValue) {
      const rows = JSON.parse(jsonValue);
      for (const row of rows) {
        row.__atkml = this.getUUID();
      }
      return rows;
    },
    /**
     * Check if one of the field use expression.
     */
    hasExpression: function () {
      return this.fieldData.some(field => field.isExpr);
    },
    /**
     * Send on change action to server.
     */
    fetchOnUpdateAction: function () {
      let fieldName = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      if (this.hasChangeCb && (fieldName === null || this.eventFields.includes(fieldName))) {
        external_jquery__WEBPACK_IMPORTED_MODULE_9___default()(this.$refs.addButton.$el).api({
          on: 'now',
          url: this.data.url,
          method: 'POST',
          data: {
            __atkml_action: 'on-change',
            rows: this.valueJson
          }
        });
      }
    },
    postData: async function (row) {
      const data = {
        ...row
      };
      const context = this.$refs.addButton.$el;
      data.__atkml_action = 'update-row';
      try {
        return await atk__WEBPACK_IMPORTED_MODULE_10__["default"].apiService.suiFetch(this.data.url, {
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
        // server will return expression field/value if defined
        if (row) {
          const resp = await this.postData(row);
          if (resp.expressions) {
            const fields = Object.keys(resp.expressions);
            for (const field of fields) {
              this.updateFieldInRow(atkmlId, field, resp.expressions[field]);
            }
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
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replaceAll(/[xy]/g, c => {
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
      let res = 'off';
      if (this.deletables.length > 0) {
        res = this.deletables.length === this.rowData.length ? 'on' : 'indeterminate';
      }
      return res;
    },
    isDeleteDisable: function () {
      return this.deletables.length === 0;
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

/***/ "./node_modules/core-js/modules/esnext.async-iterator.some.js":
/*!********************************************************************!*\
  !*** ./node_modules/core-js/modules/esnext.async-iterator.some.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {


var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var $some = (__webpack_require__(/*! ../internals/async-iterator-iteration */ "./node_modules/core-js/internals/async-iterator-iteration.js").some);

// `AsyncIterator.prototype.some` method
// https://github.com/tc39/proposal-async-iterator-helpers
$({ target: 'AsyncIterator', proto: true, real: true }, {
  some: function some(predicate) {
    return $some(this, predicate);
  }
});


/***/ }),

/***/ "./node_modules/core-js/modules/esnext.iterator.some.js":
/*!**************************************************************!*\
  !*** ./node_modules/core-js/modules/esnext.iterator.some.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {


var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var iterate = __webpack_require__(/*! ../internals/iterate */ "./node_modules/core-js/internals/iterate.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "./node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "./node_modules/core-js/internals/an-object.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "./node_modules/core-js/internals/get-iterator-direct.js");

// `Iterator.prototype.some` method
// https://github.com/tc39/proposal-iterator-helpers
$({ target: 'Iterator', proto: true, real: true }, {
  some: function some(predicate) {
    anObject(this);
    aCallable(predicate);
    var record = getIteratorDirect(this);
    var counter = 0;
    return iterate(record, function (value, stop) {
      if (predicate(value, counter++)) return stop();
    }, { IS_RECORD: true, INTERRUPTED: true }).stopped;
  }
});


/***/ })

}]);
//# sourceMappingURL=atk-vue-multiline.js.map