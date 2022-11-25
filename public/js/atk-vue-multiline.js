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
                @onTabLastColumn="onTabLastColumn(i)"
                :rowId="row.__atkml"
                :isDeletable="isDeletableRow(row)"
                :rowValues="row"
                :error="getError(row.__atkml)"
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
  name: 'AtkMultilineCell',
  template: `
        <component
            :is="getComponent()"
            v-bind="getComponentProps()"
            ref="cell"
            :fluid="true"
            class="fluid"
            @update:modelValue="onInput"
            v-model="inputValue"
            :name="inputName"
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
  emits: ['update-value'],
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
      this.$emit('update-value', this.fieldName, this.inputValue);
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
                    state="error"
                    v-for="column in columns"
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
                    <input ref="check" type="checkbox" @input="onToggleDeleteAll" :checked="isChecked" :indeterminate="isIndeterminate" />
                </SuiTableHeaderCell>
                <SuiTableHeaderCell
                    v-for="column in columns"
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
        atk__WEBPACK_IMPORTED_MODULE_3__["default"].eventBus.emit(this.$root.$el.id + '-toggle-delete-all', {
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
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");
/* harmony import */ var _multiline_cell_component__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./multiline-cell.component */ "./src/vue-components/multiline/multiline-cell.component.js");






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
                <input type="checkbox" @input="onToggleDelete" v-model="toDelete" />
            </SuiTableCell>
            <SuiTableCell
                v-for="(column, i) in columns"
                @keydown.tab="onTab(i)"
                v-bind="column.cellProps"
                :width=null
                :state="getErrorState(column)"
                :style="{ overflow: 'visible' }"
            >
                <AtkMultilineCell
                    :cellData="column"
                    @update-value="onUpdateValue"
                    :fieldValue="getValue(column)"
                ></AtkMultilineCell>
            </SuiTableCell>
        </SuiTableRow>`,
  props: ['fields', 'rowId', 'isDeletable', 'rowValues', 'error'],
  data: function () {
    return {
      columns: this.fields
    };
  },
  components: {
    AtkMultilineCell: _multiline_cell_component__WEBPACK_IMPORTED_MODULE_4__["default"]
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
    onTab: function (columnIndex) {
      if (columnIndex === this.columns.filter(column => column.isEditable).length) {
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
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.filter.js */ "./node_modules/core-js/modules/esnext.async-iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_filter_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "./node_modules/core-js/modules/esnext.iterator.filter.js");
/* harmony import */ var core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_filter_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.some.js */ "./node_modules/core-js/modules/esnext.async-iterator.some.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_some_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/esnext.iterator.some.js */ "./node_modules/core-js/modules/esnext.iterator.some.js");
/* harmony import */ var core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_some_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/esnext.async-iterator.find.js */ "./node_modules/core-js/modules/esnext.async-iterator.find.js");
/* harmony import */ var core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_async_iterator_find_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "./node_modules/core-js/modules/esnext.iterator.find.js");
/* harmony import */ var core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_find_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! external/jquery */ "external/jquery");
/* harmony import */ var external_jquery__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(external_jquery__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var atk__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! atk */ "./src/setup-atk.js");
/* harmony import */ var _multiline_body_component__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./multiline-body.component */ "./src/vue-components/multiline/multiline-body.component.js");
/* harmony import */ var _multiline_header_component__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./multiline-header.component */ "./src/vue-components/multiline/multiline-header.component.js");












/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkMultiline',
  template: `
        <div>
            <SuiTable v-bind="tableProp">
                <AtkMultilineHeader
                    :fields="fieldData"
                    :state="getMainToggleState"
                    :errors="errors"
                    :caption="caption"
                ></AtkMultilineHeader>
                <AtkMultilineBody
                    @onTabLastRow="onTabLastRow"
                    :fieldDefs="fieldData"
                    :rowData="rowData"
                    :deletables="getDeletables"
                    :errors="errors"
                ></AtkMultilineBody>
                <SuiTableFooter>
                    <SuiTableRow>
                        <SuiTableHeaderCell />
                        <SuiTableHeaderCell :colspan="getSpan" textAlign="right">
                            <SuiButtonGroup>
                                <SuiButton ref="addBtn" size="small" @click.stop.prevent="onAdd" type="button" icon :disabled="isLimitReached">
                                    <SuiIcon name="plus" />
                                </SuiButton>
                                <SuiButton size="small" @click.stop.prevent="onDelete" type="button" icon :disabled="isDeleteDisable">
                                    <SuiIcon name="trash" />
                                </SuiButton>
                            </SuiButtonGroup>
                        </SuiTableHeaderCell>
                    </SuiTableRow>
                </SuiTableFooter>
            </SuiTable>
            <div>
                <input ref="atkmlInput" :form="form" :name="name" type="hidden" :value="value" />
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
        ...this.data.tableProps
      }
    };
  },
  components: {
    AtkMultilineHeader: _multiline_header_component__WEBPACK_IMPORTED_MODULE_11__["default"],
    AtkMultilineBody: _multiline_body_component__WEBPACK_IMPORTED_MODULE_10__["default"]
  },
  mounted: function () {
    this.rowData = this.buildRowData(this.value ? this.value : '[]');
    this.updateInputValue();
    atk__WEBPACK_IMPORTED_MODULE_9__["default"].eventBus.on(this.$root.$el.id + '-update-row', payload => {
      this.onUpdate(payload.rowId, payload.fieldName, payload.value);
    });
    atk__WEBPACK_IMPORTED_MODULE_9__["default"].eventBus.on(this.$root.$el.id + '-toggle-delete', payload => {
      const i = this.deletables.indexOf(payload.rowId);
      if (i !== -1) {
        this.deletables.splice(i, 1);
      } else {
        this.deletables.push(payload.rowId);
      }
    });
    atk__WEBPACK_IMPORTED_MODULE_9__["default"].eventBus.on(this.$root.$el.id + '-toggle-delete-all', payload => {
      this.deletables = [];
      if (payload.isOn) {
        for (const row of this.rowData) {
          this.deletables.push(row.__atkml);
        }
      }
    });
    atk__WEBPACK_IMPORTED_MODULE_9__["default"].eventBus.on(this.$root.$el.id + '-multiline-rows-error', payload => {
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
      for (const atkmlId of this.deletables) {
        this.deleteRow(atkmlId);
      }
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
        this.onUpdate.debouncedFx = atk__WEBPACK_IMPORTED_MODULE_9__["default"].createDebouncedFx(() => {
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
      this.value = JSON.stringify(this.rowData);
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
     * Use regular api call in order
     * for return js to be fully evaluated.
     */
    fetchOnChangeAction: function () {
      let fieldName = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      if (this.hasChangeCb && (fieldName === null || this.eventFields.includes(fieldName))) {
        external_jquery__WEBPACK_IMPORTED_MODULE_8___default()(this.$refs.addBtn.$el).api({
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
        return await atk__WEBPACK_IMPORTED_MODULE_9__["default"].apiService.suiFetch(this.data.url, {
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
      let state = 'off';
      if (this.deletables.length > 0) {
        state = this.deletables.length === this.rowData.length ? 'on' : 'indeterminate';
      }
      return state;
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
 * https://github.com/ankurk91/vue-flatpickr-component
 *
 * Properties:
 * config: Any of flatpickr options
 *
 * Will emit a dateChange event when date is set.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkDatePicker',
  template: '<FlatpickrPicker v-model="date" :config="flatPickr" />',
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
  emits: ['setDefault'],
  mounted: function () {
    // if value is not set but default date is, then emit proper string value to parent.
    if (!this.value && this.flatPickr.defaultDate) {
      if (this.flatPickr.defaultDate instanceof Date) {
        this.$emit('setDefault', flatpickr.formatDate(this.config.defaultDate, this.config.dateFormat));
      } else {
        this.$emit('setDefault', this.flatPickr.defaultDate);
      }
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
 * Properties:
 * config:
 * url: the callback URL. Callback should return model data in form of { key: modelId, text: modelTitle, value: modelId }
 * reference: the reference field name associate with model or hasOne name. This field name will be sent along with URL callback parameter as of 'field=name'.
 * ui: the css class name to apply to dropdown.
 * Note: The remaining config object may contain any or SuiDropdown { props: value } pair.
 *
 * value: The selected value.
 * optionalValue: The initial list of options for the dropdown.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AtkLookup',
  template: `
        <SuiDropdown
            v-bind="dropdownProps"
            ref="drop"
            ` /* :loading="isLoading" */ + `@update:modelValue="onChange"
            @filtered="onFiltered"
            v-model="current"
            :class="css"
        ></SuiDropdown>`,
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
  emits: ['update:modelValue'],
  methods: {
    onChange: function (value) {
      this.current = value.value;
      this.$emit('update:modelValue', this.current);
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

/***/ }),

/***/ "./node_modules/core-js/modules/esnext.async-iterator.some.js":
/*!********************************************************************!*\
  !*** ./node_modules/core-js/modules/esnext.async-iterator.some.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {


// https://github.com/tc39/proposal-iterator-helpers
var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var $some = (__webpack_require__(/*! ../internals/async-iterator-iteration */ "./node_modules/core-js/internals/async-iterator-iteration.js").some);

$({ target: 'AsyncIterator', proto: true, real: true, forced: true }, {
  some: function some(fn) {
    return $some(this, fn);
  }
});


/***/ }),

/***/ "./node_modules/core-js/modules/esnext.iterator.some.js":
/*!**************************************************************!*\
  !*** ./node_modules/core-js/modules/esnext.iterator.some.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {


// https://github.com/tc39/proposal-iterator-helpers
var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var iterate = __webpack_require__(/*! ../internals/iterate */ "./node_modules/core-js/internals/iterate.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "./node_modules/core-js/internals/a-callable.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "./node_modules/core-js/internals/get-iterator-direct.js");

$({ target: 'Iterator', proto: true, real: true, forced: true }, {
  some: function some(fn) {
    var record = getIteratorDirect(this);
    var counter = 0;
    aCallable(fn);
    return iterate(record, function (value, stop) {
      if (fn(value, counter++)) return stop();
    }, { IS_RECORD: true, INTERRUPTED: true }).stopped;
  }
});


/***/ })

}]);
//# sourceMappingURL=atk-vue-multiline.js.map