import multilineRow from "./multiline-row.component";

export default {
  name: "atk-multiline-body",
  template: `
    <sui-table-body>
      <atk-multiline-row v-for="(row , idx) in rows" :key="getId(row)" 
      :fields="fields" 
      :rowId="getId(row)" 
      :isDeletable="isDeletableRow(row)" 
      :values="row"
      :error="getError(getId(row))"></atk-multiline-row>
    </sui-table-body>
  `,
  props: ['fieldDefs', 'rowData', 'rowIdField', 'deletables', 'errors'],
  data: function() {
    return {fields: this.fieldDefs}
  },
  created: function() {
  },
  components: {
    'atk-multiline-row' : multilineRow,
  },
  computed: {
    rows() {
      return this.rowData;
    },
  },
  methods: {
    isDeletableRow(row) {
      return this.deletables.indexOf(this.getId(row)) > -1;
    },
    getId: function(row) {
      let id;
      row.forEach(input => {
        if ('__atkml' in input) {
          id = input['__atkml'];
        }
      });
      return id;
    },
    getError: function(rowId){
      if (rowId in this.errors) {
        return this.errors[rowId];
      }
      return null;
    }
  }
}
