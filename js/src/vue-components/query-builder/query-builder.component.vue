<template>
    <div class="">
        <input
            :form="form"
            :name="name"
            type="hidden"
            :value="valueJson"
        >
        <VueQueryBuilder
            v-model="query"
            :groupComponent="groupComponent"
            :ruleComponent="ruleComponent"
            :rules="rules"
            :maxDepth="maxDepth"
            :labels="labels"
        >
            <template #default="slotProps">
                <component
                    :is="groupComponent"
                    v-bind="slotProps"
                    v-model:query="query"
                />
            </template>
        </VueQueryBuilder>
        <template v-if="debug">
            <pre>{{ JSON.stringify(query, null, 2) }}</pre>
        </template>
    </div>
</template>

<script>
import VueQueryBuilder from 'vue-query-builder/src/VueQueryBuilder';
import QueryBuilderGroup from './fomantic-ui-group.component';
import QueryBuilderRule from './fomantic-ui-rule.component';

export default {
    name: 'QueryBuilder',
    components: {
        VueQueryBuilder: VueQueryBuilder,
    },
    props: {
        groupComponent: {
            type: Object,
            default: QueryBuilderGroup,
        },
        ruleComponent: {
            type: Object,
            default: QueryBuilderRule,
        },
        data: {
            type: Object,
            required: true,
        },
    },
    data: function () {
        return {
            query: this.data.query ?? {},
            rules: this.data.rules ?? [],
            name: this.data.name ?? '',
            maxDepth: this.data.maxDepth ?? 1,
            labels: this.getLabels(this.data.labels),
            form: this.data.form,
            debug: this.data.debug ?? false,
        };
    },
    computed: {
        valueJson: function () {
            return JSON.stringify(this.query, null);
        },
    },
    methods: {
        /**
         * Return default label and option.
         */
        getLabels: function (labels) {
            labels = labels || {};

            return {
                matchType: 'Match Type',
                matchTypes: [
                    { id: 'AND', label: 'And' },
                    { id: 'OR', label: 'Or' },
                ],
                addRule: 'Add Rule',
                removeRuleClass: 'small icon times',
                addGroup: 'Add Group',
                removeGroupClass: 'small icon times',
                textInputPlaceholder: 'value',
                spaceRule: 'fitted', // can be fitted, compact or padded
                hiddenOperator: ['is empty', 'is not empty'], // a list of operators that when select, will hide user input
                ...labels,
            };
        },
    },
};
</script>
