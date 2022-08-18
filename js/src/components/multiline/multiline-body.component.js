import multilineRow from './multiline-row.component';

export default {
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
        return { fields: this.fieldDefs };
    },
    created: function () {
    },
    components: {
        'atk-multiline-row': multilineRow,
    },
    computed: {
        rows: function () {
            return this.rowData;
        },
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
        },
    },
};
