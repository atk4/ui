import atk from 'atk';

export default {
    name: 'AtkMultilineHeader',
    template: `
        <SuiTableHeader>
            <SuiTableRow v-if="hasError()">
                <SuiTableCell :style="{ background: 'none' }" />
                <SuiTableCell :style="{ background: 'none' }"
                    state="error"
                    v-for="column in columns"
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
                    <input ref="check" type="checkbox" @input="onToggleDeleteAll" :checked="isChecked" :indeterminate="isIndeterminate" />
                </SuiTableHeaderCell>
                <SuiTableHeaderCell
                    v-for="column in columns"
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
    props: ['fields', 'state', 'errors', 'caption'],
    data: function () {
        return { columns: this.fields, isDeleteAll: false };
    },
    methods: {
        onToggleDeleteAll: function () {
            this.$nextTick(() => {
                atk.eventBus.emit(this.$root.$el.id + '-toggle-delete-all', { isOn: this.$refs.check.checked });
            });
        },
        getTextAlign: function (column) {
            let align = 'left';
            if (!column.isEditable) {
                switch (column.type) {
                    case 'integer':
                    case 'float':
                    case 'atk4_money':
                        align = 'right';

                        break;
                }
            }

            return align;
        },
        getVisibleColumns: function () {
            let count = 1; // add deletable column;
            this.columns.forEach((field) => {
                count = field.isVisible ? count + 1 : count;
            });

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
                for (let i = 0; i < rows.length; i++) {
                    const error = this.errors[rows[i]].filter((col) => col.name === column.name);
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
            return this.state === 'indeterminate';
        },
        isChecked: function () {
            return this.state === 'on';
        },
    },
};
