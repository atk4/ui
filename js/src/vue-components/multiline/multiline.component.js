import $ from 'external/jquery';
import atk from 'atk';
import multilineBody from './multiline-body.component';
import multilineHeader from './multiline-header.component';

export default {
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
            valueJson: this.data.inputValue,
            name: this.data.inputName,
            rowData: [],
            fieldData: this.data.fields || [],
            eventFields: this.data.eventFields || [],
            deletables: [],
            hasChangeCb: this.data.hasChangeCb,
            errors: {},
            caption: this.data.caption || null,
            tableProp: { ...tableDefault, ...this.data.tableProps },
        };
    },
    components: {
        AtkMultilineHeader: multilineHeader,
        AtkMultilineBody: multilineBody,
    },
    mounted: function () {
        this.rowData = this.buildRowData(this.valueJson ?? '[]');
        this.updateInputValue();

        atk.eventBus.on(this.$root.$el.parentElement.id + '-update-row', (payload) => {
            this.onUpdate(payload.rowId, payload.fieldName, payload.value);
        });

        atk.eventBus.on(this.$root.$el.parentElement.id + '-toggle-delete', (payload) => {
            const i = this.deletables.indexOf(payload.rowId);
            if (i !== -1) {
                this.deletables.splice(i, 1);
            } else {
                this.deletables.push(payload.rowId);
            }
        });

        atk.eventBus.on(this.$root.$el.parentElement.id + '-toggle-delete-all', (payload) => {
            this.deletables = [];
            if (payload.isOn) {
                for (const row of this.rowData) {
                    this.deletables.push(row.__atkml);
                }
            }
        });

        atk.eventBus.on(this.$root.$el.parentElement.id + '-multiline-rows-error', (payload) => {
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
                this.onUpdate.debouncedFx = atk.createDebouncedFx(() => {
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
            this.rowData.splice(this.rowData.findIndex((row) => row.__atkml === atkmlId), 1);
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
            return this.fieldData.some((field) => field.isExpr);
        },
        /**
         * Send on change action to server.
         */
        fetchOnUpdateAction: function (fieldName = null) {
            if (this.hasChangeCb && (fieldName === null || this.eventFields.includes(fieldName))) {
                $(this.$refs.addButton.$el).api({
                    on: 'now',
                    url: this.data.url,
                    method: 'POST',
                    data: { __atkml_action: 'on-change', rows: this.valueJson },
                });
            }
        },
        postData: async function (row) {
            const data = { ...row };
            const context = this.$refs.addButton.$el;
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
            return this.rowData.find((row) => row.__atkml === atkmlId);
        },
        getInputElement: function () {
            return this.$refs.atkmlInput;
        },
        /**
         * UUID v4 generator.
         */
        getUUID: function () {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replaceAll(/[xy]/g, (c) => {
                const r = Math.floor(Math.random() * 16);
                const v = c === 'x' ? r : r & (0x3 | 0x8); // eslint-disable-line no-bitwise

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
            let res = 'off';
            if (this.deletables.length > 0) {
                res = this.deletables.length === this.rowData.length
                    ? 'on'
                    : 'indeterminate';
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
        },
    },
};
