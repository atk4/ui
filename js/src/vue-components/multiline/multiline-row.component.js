import atk from 'atk';
import multilineCell from './multiline-cell.component';

/**
 * This will create a table td element using SuiTableCell.
 * The td element is created only if column as set isVisible = true;
 * The td element will add a multiline cell element.
 * the multiline cell will set it's own template component depending on the fieldType.
 * getValue
 */
export default {
    name: 'AtkMultilineRow',
    template: `
        <SuiTableRow :verticalAlign="'middle'">
            <SuiTableCell textAlign="center">
                <input type="checkbox" v-model="toDelete" @input="onToggleDelete" />
            </SuiTableCell>
            <SuiTableCell
                v-for="(column, i) in columns"
                v-bind="column.cellProps"
                :width=null
                :state="getErrorState(column)"
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
    props: ['fields', 'rowId', 'isDeletable', 'rowValues', 'error'],
    data: function () {
        return { columns: this.fields };
    },
    components: {
        AtkMultilineCell: multilineCell,
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
            },
        },
    },
    emits: ['onTabLastColumn'],
    methods: {
        onTab: function (columnIndex) {
            if (columnIndex === this.columns.filter((column) => column.isEditable).length) {
                this.$emit('onTabLastColumn');
            }
        },
        getErrorState: function (column) {
            if (this.error) {
                const error = this.error.filter((e) => column.name === e.name);
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
            atk.eventBus.emit(this.$root.$el.id + '-toggle-delete', { rowId: this.rowId });
        },
        onUpdateValue: function (fieldName, value) {
            atk.eventBus.emit(this.$root.$el.id + '-update-row', { rowId: this.rowId, fieldName: fieldName, value: value });
        },
        getValue: function (column) {
            return this.rowValues[column.name] || column.default;
        },
    },
};
