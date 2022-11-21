import multilineRow from './multiline-row.component';

export default {
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
        return { fields: this.fieldDefs };
    },
    created: function () {
    },
    components: {
        AtkMultilineRow: multilineRow,
    },
    computed: {
        rows: function () {
            return this.rowData;
        },
    },
    emits: ['onTabLastRow'],
    methods: {
        onTabLastColumn: function (rowIndex) {
            if (rowIndex + 1 === this.rowData.length) {
                this.$emit('onTabLastRow');
            }
        },
        isDeletableRow: function (row) {
            return this.deletables.indexOf(row.__atkml) !== -1;
        },
        getError: function (rowId) {
            if (rowId in this.errors) {
                return this.errors[rowId];
            }

            return null;
        },
    },
};
