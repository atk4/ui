import atk from 'atk';

export default {
    name: 'AtkMultilineHeader',
    template: `
        <SuiTableHeader>
            <SuiTableRow v-if="hasError()">
                <SuiTableCell :style="{ background: 'none' }" />
                <SuiTableCell :style="{ background: 'none' }"
                    error="true"
                    v-for="column in filterVisibleColumns(columns)"
                    :textAlign="getTextAlign(column)"
                >
                    <SuiIcon v-if="getErrorMsg(column)" name="attention" />
                    {{getErrorMsg(column)}}
                </SuiTableCell>
            </SuiTableRow>
            <SuiTableRow v-if="hasCaption()">
                <SuiTableHeaderCell :colspan="getVisibleColumns()">{{caption}}</SuiTableHeaderCell>
            </SuiTableRow>
            <SuiTableRow :verticalAlign="'top'">
                <SuiTableHeaderCell :width=1 textAlign="center">
                    <input ref="check" type="checkbox" :checked="isChecked" :indeterminate="isIndeterminate" @input="onToggleDeleteAll" />
                </SuiTableHeaderCell>
                <SuiTableHeaderCell
                    v-for="column in filterVisibleColumns(columns)"
                    :width=column.cellProps.width
                    :textAlign="getTextAlign(column)"
                >
                    <div>{{column.caption}}</div>
                    <div v-if="false" :style="{ position: 'absolute', top: '-22px' }">
                        <SuiLabel v-if="getErrorMsg(column)" pointing="below" basic color="red">{{getErrorMsg(column)}}</SuiLabel>
                    </div>
                </SuiTableHeaderCell>
            </SuiTableRow>
        </SuiTableHeader>`,
    props: ['fields', 'selectionState', 'errors', 'caption'],
    data: function () {
        return { columns: this.fields, isDeleteAll: false };
    },
    methods: {
        filterVisibleColumns: function (columns) {
            return columns.filter((v) => v.isVisible);
        },
        onToggleDeleteAll: function () {
            this.$nextTick(() => {
                atk.eventBus.emit(this.$root.$el.parentElement.id + '-toggle-delete-all', { isOn: this.$refs.check.checked });
            });
        },
        getTextAlign: function (column) {
            let align = 'left';
            if (!column.isEditable) {
                switch (column.type) {
                    case 'integer':
                    case 'float':
                    case 'atk4_money': {
                        align = 'right';

                        break;
                    }
                }
            }

            return align;
        },
        getVisibleColumns: function () {
            let count = 1; // add deletable column;
            for (const field of this.columns) {
                count = field.isVisible ? count + 1 : count;
            }

            return count;
        },
        hasError: function () {
            return Object.keys(this.errors).length > 0;
        },
        hasCaption: function () {
            return this.caption;
        },
        getErrorMsg: function (column) {
            if (this.hasError()) {
                const rows = Object.keys(this.errors);
                for (const row of rows) {
                    const error = this.errors[row].filter((col) => col.name === column.name);
                    if (error.length > 0) {
                        return error[0].msg;
                    }
                }
            }

            return null;
        },
    },
    computed: {
        isIndeterminate: function () {
            return this.selectionState === 'indeterminate';
        },
        isChecked: function () {
            return this.selectionState === 'on';
        },
    },
};
