import multilineBody from './multiline-body.component';
import multilineHeader from './multiline-header.component';

/**
 * MultiLine component.
 *
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
                <input :form="form" :name="name" type="hidden" :value="value" ref="atkmlInput">
             </div>`,
    props: {
        data: Object,
    },
    data: function () {
        const tableDefault = {
            basic: false,
            celled: false,
            collapsing: false,
            stackable: false,
            inverted: false,
        };

        return {
            form: this.data.formName,
            value: this.data.inputValue,
            name: this.data.inputName, // form input name where to set multiline content value.
            rows: [],
            fieldData: this.data.fields || [],
            idField: this.data.idField,
            eventFields: this.data.eventFields || [],
            deletables: [],
            hasChangeCb: this.data.hasChangeCb,
            errors: {},
            caption: this.data.caption || null,
            tableProp: { ...tableDefault, ...this.data.tableProps || {} },
        };
    },
    components: {
        'atk-multiline-body': multilineBody,
        'atk-multiline-header': multilineHeader,
    },
    mounted: function () {
        this.rowData = this.buildRowData();

        atk.eventBus.on(this.$root.$el.id + '-update-row', (payload) => {
            this.onUpdate(payload.rowId, payload.field, payload.value);
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
                    this.deletables.push(this.getAtkmlId(row));
                });
            }
        });

        atk.eventBus.on(this.$root.$el.id + '-multiline-rows-error', (payload) => {
            this.errors = { ...payload.errors };
        });
    },
    methods: {
        onTabLastRow: function () {
            if (!this.isLimitReached && this.data.addOnTab) {
                this.onAdd();
            }
        },
        onAdd: function () {
            const row = this.createRow();
            this.rowData.push(row);
            this.updateInputValue();
            if (this.data.afterAdd && typeof this.data.afterAdd === 'function') {
                this.data.afterAdd(JSON.parse(this.value));
            }
            this.fetchExpression(this.getAtkmlId(row));
            this.fetchOnChangeAction();
        },
        onDelete: function () {
            this.deletables.forEach((id) => {
                this.deleteRow(id);
            });
            this.deletables = [];
            this.updateInputValue();
            this.fetchOnChangeAction();
            if (this.data.afterDelete && typeof this.data.afterDelete === 'function') {
                this.data.afterDelete(atk.utils.json().tryParse(this.value));
            }
        },
        onUpdate: function (atkmlId, field, value) {
            this.updateRow(atkmlId, field, value);
            this.clearError(atkmlId, field);
            this.updateInputValue();

            atk.debounce(() => {
                this.fetchExpression(atkmlId);
                this.fetchOnChangeAction(field);
            }, 300).call(this);
        },
        /**
         * Creates a new row of data and
         * set values to default if available.
         *
         * @returns {Array}
         */
        createRow: function () {
            const columns = [];
            // add __atkml property in order to identify each row.
            columns.push({ __atkml: this.getUUID() });
            this.data.fields.forEach((item) => {
                columns.push({ [item.field]: item.default });
            });

            return columns;
        },
        /**
         * Update row with proper data value.
         */
        updateRow: function (rowAtkmlId, field, value) {
            const idx = this.getRowIndex(rowAtkmlId);
            if (idx > -1) {
                this.updateFieldInRow(idx, field, value);
            }
        },
        deleteRow: function (id) {
            // find proper row index using id.
            const idx = this.getRowIndex(id);
            if (idx > -1) {
                this.rowData.splice(idx, 1);
                delete this.errors[id];
            }
        },
        /**
         * Update the value of the field in rowData.
         */
        updateFieldInRow: function (idx, field, value) {
            this.rowData[idx].forEach((cell) => {
                if (field in cell) {
                    cell[field] = value;
                }
            });
        },
        clearError: function (rowAtkmlId, field) {
            if (rowAtkmlId in this.errors) {
                const errors = this.errors[rowAtkmlId].filter((error) => error.field !== field);
                this.errors[rowAtkmlId] = [...errors];
                if (errors.length === 0) {
                    delete this.errors[rowAtkmlId];
                }
            }
        },
        /**
        * Update Multi-line Form input with all rowData values
        * as json string.
        */
        updateInputValue: function () {
            const data = this.rowData.map((item) => {
                const newItem = {};
                for (let i = 0; i < item.length; i++) {
                    const key = Object.keys(item[i])[0];
                    // eslint-disable-next-line prefer-destructuring
                    newItem[key] = Object.values(item[i])[0];
                }
                return { ...newItem };
            });

            this.value = JSON.stringify(data);
        },
        /**
        * Build rowData from input value.
        * We need to compare fields return by model vs what values give us because it could differ.
        * For example if a field was add or remove from model after a value was saved. Specially for
        * array type field like containsMany / containsOne.
         * In other word, rowData must match fields definition.
         *
         * @returns {Array}
         */
        buildRowData: function () {
            const rows = [];
            // Get field name.
            const fields = this.data.fields.map((item) => item.field);

            // Map value to our rowData.
            const values = atk.utils.json().tryParse(this.value, []);

            values.forEach((value) => {
                const data = fields.map((fieldName) => (
                    { [fieldName]: value[fieldName] || null }
                ));
                data.push({ __atkml: this.getUUID() });
                rows.push(data);
            });

            return rows;
        },
        /**
         * Check if one of the field use expression.
         */
        hasExpression: function () {
            return this.fieldData.filter((field) => field.isExpr).length > 0;
        },
        /**
         * Send on change action to server.
         * Use regular api call in order
         * for return js to be fully evaluate.
         */
        fetchOnChangeAction: function (field = null) {
            if (this.hasChangeCb && (field === null || this.eventFields.indexOf(field) > -1)) {
                jQuery(this.$refs.addBtn.$el).api({
                    on: 'now',
                    url: this.data.url,
                    method: 'post',
                    data: { __atkml_action: 'on-change', rows: this.value },
                });
            }
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
        /**
         * Get expressions from server.
         */
        fetchExpression: async function (rowAtkmlId) {
            if (this.hasExpression()) {
                const idx = this.getRowIndex(rowAtkmlId);
                // server will return expression field - value if define.
                if (idx > -1) {
                    const resp = await this.postData([...this.rowData[idx]]);
                    if (resp.expressions) {
                        const fields = Object.keys(resp.expressions);
                        fields.forEach((field) => {
                            this.updateFieldInRow(idx, field, resp.expressions[field]);
                        });
                        this.updateInputValue();
                    }
                }
            }
        },
        /**
         * Return the __atkml id from a row of data.
         */
        getAtkmlId: function (row) {
            let id;
            row.forEach((input) => {
                if ('__atkml' in input) {
                    id = input.__atkml;
                }
            });
            return id;
        },
        /**
         * Return the array index number base on an atkmlId or -1 if not found.
         */
        getRowIndex: function (atkmlId) {
            for (let i = 0; i < this.rowData.length; i++) {
                if (this.getAtkmlId(this.rowData[i]) === atkmlId) {
                    return i;
                }
            }
            return -1;
        },
        getInputElement: function () {
            return this.$refs.atkmlInput;
        },
        /**
         * UUID v4 generator.
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
