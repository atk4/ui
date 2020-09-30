import multilineBody from './multiline-body.component';
import multilineHeader from './multiline-header.component';

/**
 * MultiLine component.
 *
 * 2019-07-23 - add support for containsMany.
 *  - updateLinesField method now return one level data row, {id:4, field1: 'value1'}
 *  - getInitData method now handle one level data row.
 */
export default {
    name: 'atk-multiline',
    template: `<div>
                <sui-table v-bind="tableProp">
                  <atk-multiline-header :fields="fieldData" :state="getMainToggleState" :errors="errors" :caption="caption"></atk-multiline-header>
                  <atk-multiline-body @onTabLastRow="onTabLastRow" :fieldDefs="fieldData" :rowData="rowData" :rowIdField="idField" :deletables="getDeletables" :errors="errors"></atk-multiline-body>
                  <sui-table-footer>
                    <sui-table-row>
                        <sui-table-header-cell/>
                        <sui-table-header-cell :colspan="getSpan" textAlign="right">
                        <div is="sui-button-group">
                         <sui-button size="small" @click.stop.prevent="onAdd" type="button" icon="plus" ref="addBtn" :disabled="isLimitReached"></sui-button>
                         <sui-button size="small" @click.stop.prevent="onDelete" type="button" icon="trash" :disabled="isDeleteDisable"></sui-button>                        
                         </div>
                        </sui-table-header-cell>
                    </sui-table-row>
                  </sui-table-footer>
                </sui-table>
             </div>`,
    props: {
        data: Object,
    },
    data: function () {
        return {
            linesField: this.data.linesField, // form control where to set multiline content value.
            rows: [],
            fieldData: this.data.fields,
            idField: this.data.idField,
            eventFields: this.data.eventFields,
            deletables: [],
            hasChangeCb: this.data.hasChangeCb,
            errors: {},
            caption: this.data.caption ? this.data.caption : null,
            tableDefault: {
                basic: false,
                celled: false,
                size: null,
                compact: null,
                collapsing: false,
                stackable: false,
                inverted: false,
                color: null,
                columns: null,
            },
            tableProp: { ...this.tableDefault, ...this.data.options.suiTable || {} },
        };
    },
    components: {
        'atk-multiline-body': multilineBody,
        'atk-multiline-header': multilineHeader,
    },
    mounted: function () {
        atk.eventBus.on(this.$root.$el.id + '-update-row', (payload) => {
            this.updateRow(payload.rowId, payload.field, payload.value);
        });

        atk.eventBus.on(this.$root.$el.id + '-post-row', (payload) => {
            if (this.hasExpression()) {
                this.postRow(payload.rowId, payload.field);
            }
            // fire change callback if set and field is part of it.
            if (this.hasChangeCb && (this.eventFields.indexOf(payload.field) > -1)) {
                this.postRaw();
            }
        });

        atk.eventBus.on(this.$root.$el.id + '-toggle-delete', (payload) => {
            const idx = this.deletables.indexOf(payload.rowId);
            if (idx > -1) {
                this.deletables.splice(idx, 1);
            } else {
                this.deletables.push(payload.rowId);
            }
        });

        atk.eventBus.on(this.$root.$el.id + '-toggle-delete-all', (payload) => {
            this.deletables = [];
            if (payload.isOn) {
                this.rowData.forEach((row) => {
                    this.deletables.push(this.getId(row));
                });
            }
        });

        atk.eventBus.on(this.$root.$el.id + '-multiline-rows-error', (payload) => {
            this.errors = { ...payload.errors };
        });
    },
    created: function () {
        this.rowData = this.getInitData();
        this.$nextTick(() => {
            this.updateLinesField();
        });
    },
    methods: {
    /**
     * UUID v4 generator.
     *
     * @returns {string}
     */
        getUUID: function () {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                // eslint-disable-next-line no-bitwise
                const r = Math.random() * 16 | 0;
                // eslint-disable-next-line no-bitwise
                const v = c === 'x' ? r : (r & (0x3 | 0x8));
                return v.toString(16);
            });
        },
        onAdd: function () {
            this.rowData.push(this.newDataRow());
            this.updateLinesField();
            if (this.data.afterAdd && typeof this.data.afterAdd === 'function') {
                this.data.afterAdd(JSON.parse(this.getInputElement().value));
            }
        },
        onDelete: function () {
            this.deletables.forEach((id) => {
                this.deleteRow(id);
            });
            this.deletables = [];
            if (this.data.afterDelete && typeof this.data.afterDelete === 'function') {
                this.data.afterDelete(JSON.parse(this.getInputElement().value));
            }
        },
        deleteRow: function (id) {
            // find proper row index using id.
            const idx = this.findRowIndex(id);
            if (idx > -1) {
                this.rowData.splice(idx, 1);
                delete this.errors[id];
            }
            this.updateLinesField();
            // fire change callback if set and field is part of it.
            if (this.hasChangeCb) {
                this.postRaw();
            }
        },
        findRowIndex: function (id) {
            for (let i = 0; i < this.rowData.length; i++) {
                if (this.getId(this.rowData[i]) === id) {
                    return i;
                }
            }
            return -1;
        },
        /**
     * Send a single row to server
     * usually to get data expression from server.
     *
     * @param rowId
     * @returns {Promise<void>}
     */
        postRow: async function (rowId) {
            // find proper row index using id.
            let idx = -1;
            for (let i = 0; i < this.rowData.length; i++) {
                // eslint-disable-next-line no-loop-func
                this.rowData[i].forEach((cell) => {
                    if (cell.__atkml === rowId) {
                        idx = i;
                    }
                });
            }
            // server will return expression field  - value if define.
            const resp = await this.postData([...this.rowData[idx]]);
            if (resp.expressions) {
                const fields = Object.keys(resp.expressions);
                fields.forEach((field) => {
                    this.updateFieldInRow(idx, field, resp.expressions[field]);
                });
            }
        },
        /**
     * Update row with proper data value.
     *
     * @param id
     * @param field
     * @param value
     */
        updateRow: function (rowId, field, value) {
            // find proper row index using id.
            let idx = -1;
            for (let i = 0; i < this.rowData.length; i++) {
                // eslint-disable-next-line no-loop-func
                this.rowData[i].forEach((cell) => {
                    if (cell.__atkml === rowId) {
                        idx = i;
                    }
                });
            }
            this.updateFieldInRow(idx, field, value);
            this.clearError(rowId, field);
            this.updateLinesField();
        },
        clearError: function (rowId, field) {
            if (rowId in this.errors) {
                const errors = this.errors[rowId].filter((error) => error.field !== field);
                this.errors[rowId] = [...errors];
                if (errors.length === 0) {
                    delete this.errors[rowId];
                }
            }
        },
        /**
     * Update the value of the field in rowData.
     *
     * @param idx
     * @param field
     * @param value
     */
        updateFieldInRow: function (idx, field, value) {
            this.rowData[idx].forEach((cell) => {
                if (field in cell) {
                    cell[field] = value;
                }
            });
        },
        /**
     * Update Multi-line Form input with all rowData values
     * as json string.
     */
        updateLinesField: function () {
            const field = document.getElementsByName(this.linesField)[0];

            const data = this.rowData.map((item) => {
                const newItem = {};
                for (let i = 0; i < item.length; i++) {
                    const key = Object.keys(item[i])[0];
                    // eslint-disable-next-line prefer-destructuring
                    newItem[key] = Object.values(item[i])[0];
                }
                return { ...newItem };
            });

            field.value = JSON.stringify(data);
        },
        /**
     * Get initial rowData value.
     * We need to compare fields return by model vs what values give us because it could differ.
     * For example if a field was add or remove from model after a value was saved. Specially for
     * array type field like containsMany / containsOne.
     * In other word, rowData must match fields definition.
     *
     * @returns {Array}
     */
        getInitData: function () {
            const rows = [];
            // Get field name.
            const fields = this.data.fields.map((item) => item.field);

            // check if input containing data is set and initialized.
            const field = document.getElementsByName(this.linesField)[0];
            if (field) {
                // Map value to our rowData.
                let values = JSON.parse(field.value);
                values = Array.isArray(values) ? values : [];

                values.forEach((value) => {
                    const data = fields.map((fieldName) => (
                        { [fieldName]: value[fieldName] ? value[fieldName] : null }
                    ));
                    data.push({ __atkml: this.getUUID() });
                    rows.push(data);
                });
            }

            return rows;
        },
        /**
     * Add a new row of data and
     * set values to default if available.
     *
     * @returns {Array}
     */
        newDataRow: function () {
            const columns = [];
            // add __atkml property in order to identify each row.
            columns.push({ __atkml: this.getUUID() });
            this.data.fields.forEach((item) => {
                columns.push({ [item.field]: item.default });
            });

            return columns;
        },
        /**
     * Return the __atkml id of the row.
     *
     * @param row
     * @returns {*}
     */
        getId: function (row) {
            let id;
            row.forEach((input) => {
                if ('__atkml' in input) {
                    id = input.__atkml;
                }
            });
            return id;
        },
        /**
     * Check if one of the field use expression.
     *
     * @returns {boolean}
     */
        hasExpression: function () {
            return this.fieldData.filter((field) => field.isExpr).length > 0;
        },
        /**
     * Post raw data.
     *
     * Use regular api call in order
     * for return js to be fully evaluate.
     */
        postRaw: function () {
            jQuery(this.$refs.addBtn.$el).api({
                on: 'now',
                url: this.data.url,
                method: 'post',
                data: { __atkml_action: 'on-change', rows: JSON.stringify(this.rowData) },
            });
        },
        postData: async function (row) {
            const data = {};
            const context = this.$refs.addBtn.$el;
            const fields = this.fieldData.map((field) => field.field);
            fields.forEach((field) => {
                data[field] = row.filter((item) => field in item)[0][field];
            });
            data.__atkml_action = 'update-row';
            try {
                const response = await atk.apiService.suiFetch(this.data.url, { data: data, method: 'post', stateContext: context });
                return response;
            } catch (e) {
                console.error(e);
            }
        },
        getInputElement: function () {
            return document.getElementsByName(this.linesField)[0];
        },
        onTabLastRow: function () {
            if (!this.isLimitReached && this.data.addOnTab) {
                this.onAdd();
            }
        },
    },
    computed: {
        rowData: {
            get: function () {
                return this.rows;
            },
            set: function (rows) {
                this.rows = [...rows];
            },
        },
        getSpan: function () {
            return this.fieldData.length - 1;
        },
        /**
     * Get id's of row set for deletion.
     * @returns {Array}
     */
        getDeletables: function () {
            return this.deletables;
        },
        /**
     * Return Delete all checkbox state base on
     * deletables entries.
     *
     * @returns {string}
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
        /**
     * Set delete button disabled property.
     *
     * @returns {boolean}
     */
        isDeleteDisable: function () {
            return !this.deletables.length > 0;
        },
        /**
     * Check if record limit is reach.
     * return false if not.
     *
     * @returns {boolean}
     */
        isLimitReached: function () {
            if (this.data.rowLimit === 0) {
                return false;
            }
            return this.data.rowLimit < this.rowData.length + 1;
        },
    },
};
