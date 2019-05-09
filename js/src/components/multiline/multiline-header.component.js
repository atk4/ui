
export default {
  name: 'atk-multiline-header',
  template: `
     <sui-table-header>
       <sui-table-row v-if="hasError()">
        <sui-table-cell :style="{background:'none'}"></sui-table-cell>
        <sui-table-cell :style="{background:'none'}" state="error" v-for="(column, idx) in columns" :key="idx" v-if="column.isVisible" :textAlign="getTextAlign(column)"><sui-icon name="attention" v-if="getErrorMsg(column)"></sui-icon>{{getErrorMsg(column)}}</sui-table-cell>
      </sui-table-row>
        <sui-table-row :verticalAlign="'top'">
        <sui-table-header-cell width="one" textAlign="center"><input type="checkbox" @input="onToggleDeleteAll" :checked.prop="isChecked" :indeterminate.prop="isIndeterminate" ref="check"></input></sui-table-header-cell>
        <sui-table-header-cell v-for="(column, idx) in columns" :key="idx" v-if="column.isVisible" :textAlign="getTextAlign(column)">
         <div>{{column.caption}}</div>
         <div :style="{position: 'absolute', top: '-22px'}" v-if="false"><sui-label pointing="below" basic color="red" v-if="getErrorMsg(column)">{{getErrorMsg(column)}}</sui-label></div>
        </sui-table-header-cell>
      </sui-table-row>
    </sui-table-header>
  `,
  props: ['fields', 'state', 'errors'],
  data() {
    return {columns: this.fields, isDeleteAll: false}
  },
  methods: {
    onToggleDeleteAll: function() {
      this.$nextTick(() => {
        this.$root.$emit('toggle-delete-all', this.$refs['check'].checked);
      });
    },
    getTextAlign(column) {
      let align = 'left';
      if (!column.isEditable) {
        switch(column.type) {
          case 'money':
          case 'integer':
          case 'number':
            align = 'right';
            break;
        }
      }

      return align;
    },
    hasError() {
      return Object.keys(this.errors).length > 0;
    },
    getErrorMsg: function(column) {
      if (this.hasError()) {
        let rows = Object.keys(this.errors);
        for( let i = 0; i < rows.length; i++) {
          let error = this.errors[rows[i]].filter(col => col.field === column.field);
          if (error.length > 0) {
            return error[0].msg;
          }
        }
      }
      return null;
    }
  },
  computed: {
    isIndeterminate() {
      return this.state === 'indeterminate';
    },
    isChecked() {
      return this.state === 'on';
    }
  }
}
