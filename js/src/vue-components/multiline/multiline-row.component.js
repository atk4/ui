import atk from 'atk';
import multilineCell from './multiline-cell.component';

/**
 * This will create a table td element using sui-table-cell.
 * The td element is created only if column as set isVisible = true;
 * The td element will add a multiline cell element.
 * the multiline cell will set it's own template component depending on the fieldType.
 * getValue
 */
export default {
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
        return { columns: this.fields };
    },
    components: {
        'atk-multiline-cell': multilineCell,
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
    methods: {
        onTab: function (idx) {
            if (idx === this.columns.filter((column) => column.isEditable).length) {
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
