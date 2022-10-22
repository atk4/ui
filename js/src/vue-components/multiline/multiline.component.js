import $ from 'external/jquery';
import atk from 'atk';
import multilineBody from './multiline-body.component';
import multilineHeader from './multiline-header.component';

export default {
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
            rowData: [],
            fieldData: this.data.fields || [],
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
        this.rowData = this.buildRowData(this.value ? this.value : '[]');
        this.updateInputValue();

        atk.eventBus.on(this.$root.$el.id + '-update-row', (payload) => {
            this.onUpdate(payload.rowId, payload.fieldName, payload.value);
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
                    this.deletables.push(row.__atkml);
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
            this.deletables.forEach((atkmlId) => {
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
                this.onUpdate.debouncedFx = atk.createDebouncedFx(() => {
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
            fields.forEach((field) => {
                row[field.name] = field.default;
            });
            row.__atkml = this.getUUID();

            return row;
        },
        deleteRow: function (atkmlId) {
            this.rowData.splice(this.rowData.findIndex((row) => row.__atkml === atkmlId), 1);
            delete this.errors[atkmlId];
        },
        /**
         * Update the value of the field in rowData.
         */
        updateFieldInRow: function (atkmlId, fieldName, value) {
            this.rowData.forEach((row) => {
                if (row.__atkml === atkmlId) {
                    row[fieldName] = value;
                }
            });
        },
        clearError: function (atkmlId, fieldName) {
            if (atkmlId in this.errors) {
                const errors = this.errors[atkmlId].filter((error) => error.name !== fieldName);
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
            rows.forEach((row) => {
                row.__atkml = this.getUUID();
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
         * for return js to be fully evaluated.
         */
        fetchOnChangeAction: function (fieldName = null) {
            if (this.hasChangeCb && (fieldName === null || this.eventFields.indexOf(fieldName) > -1)) {
                $(this.$refs.addBtn.$el).api({
                    on: 'now',
                    url: this.data.url,
                    method: 'POST',
                    data: { __atkml_action: 'on-change', rows: this.value },
                });
            }
        },
        postData: async function (row) {
            const data = { ...row };
            const context = this.$refs.addBtn.$el;
            data.__atkml_action = 'update-row';
            try {
                return await atk.apiService.suiFetch(this.data.url, { data: data, method: 'POST', stateContext: context });
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
                        fields.forEach((field) => {
                            this.updateFieldInRow(atkmlId, field, resp.expressions[field]);
                        });
                        this.updateInputValue();
                    }
                }
            }
        },
        findRow: function (atkmlId) {
            return this.rowData.find((row) => row.__atkml === atkmlId);
        },
        getInputElement: function () {
            return this.$refs.atkmlInput;
        },
        /**
         * UUID v4 generator.
         */
        getUUID: function () {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                const r = Math.floor(Math.random() * 16);
                const v = c === 'x' ? r : (r & (0x3 | 0x8)); // eslint-disable-line no-bitwise

                return v.toString(16);
            });
        },
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
        },
    },
};
