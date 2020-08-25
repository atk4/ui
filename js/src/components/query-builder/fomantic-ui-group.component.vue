<template>
    <div class="vqb-group ui fluid card" :class="[labels.spaceRule , 'depth-' + depth.toString()]">
        <div class="vbq-group-heading content" :class="'depth-' + depth.toString()">
            <div class="ui grid">
                <div class="fourteen wide column">
                    <div class="ui horizontal list">
                        <div class="item">
                            <h4 class="ui inline">{{ labels.matchType }}</h4>
                        </div>
                        <div class="item">
                            <select
                                    v-model="query.logicalOperator"
                                    class="atk-qb-select"
                            >
                                <option
                                        v-for="label in labels.matchTypes"
                                        :key="label.id"
                                        :value="label.id"
                                >{{ label.label }}</option>
                            </select>
                        </div>
                        <div class="item">
                            <div class="rule-actions ">
                                <div>
                                    <sui-dropdown
                                            :text="labels.addRule"
                                            class="ui mini basic button atk-qb-rule-select"
                                            selection
                                    >
                                        <sui-dropdown-menu class="atk-qb-rule-select-menu">
                                            <sui-dropdown-item
                                                @click="addNewRule(rule.id)"
                                                v-for="rule in rules"
                                                :key="rule.id" :value="rule"
                                            >{{ rule.label }}</sui-dropdown-item>
                                        </sui-dropdown-menu>
                                    </sui-dropdown>
                                    <button v-if="depth < maxDepth"
                                            type="button"
                                            class="ui mini basic button"
                                            @click="addGroup"
                                    >{{ labels.addGroup }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="two wide right aligned column">
                    <i v-if="depth > 1" class="atk-qb-remove" :class="labels.removeGroup" @click="remove"></i>
                </div>
            </div>
        </div>
        <div class="vbq-group-body content">
            <query-builder-children v-bind="$props"/>
        </div>
    </div>
</template>

<script>
import QueryBuilderGroup from 'vue-query-builder/dist/group/QueryBuilderGroup.umd';
import QueryBuilderRule from './fomantic-ui-rule.component.vue';

export default {
    name: 'QueryBuilderGroup',
    components: {
        QueryBuilderRule: QueryBuilderRule,
    },
    data: function () {
        return {
            selectedSuiRule: null,
        };
    },
    methods: {
        /**
       * Add a new rule via Dropdown item.
       */
        addNewRule: function (ruleId) {
            // eslint-disable-next-line prefer-destructuring
            this.selectedRule = this.rules.filter((rule) => rule.id === ruleId)[0];
            if (this.selectedRule) {
                this.addRule();
            }
        },
    },
    computed: {
        /**
       * Map rules to SUI Dropdown.
       *
       * @returns {*}
       */
        dropdownRules: function () {
            return this.rules.map((rule) => ({
                key: rule.id,
                text: rule.label,
                value: rule.id,
            }));
        },
    },
    extends: QueryBuilderGroup,
};
</script>

<style>
    .atk-qb-select, .ui.form select.atk-qb-select {
       padding: 2px 6px 4px 4px;
    }
    .atk-qb-remove {
        cursor: pointer;
        color: rgba(0,0,0,0.6);
    }
    .ui.selection.dropdown.atk-qb-rule-select {
        background-color: rgba(0,0,0,0);
    }
    .ui.selection.dropdown .atk-qb-rule-select-menu {
        width: max-content;
        z-index: 1000;
    }
    .vbq-group-heading > .ui.grid > .column:not(.row) {
        padding-bottom: 0.5em;
        padding-top: 0.5em;
    }
    .vue-query-builder .ui.card.compact {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    .vue-query-builder .ui.card.fitted {
        margin-top: 0em;
        margin-bottom: 0em;
    }
    .vue-query-builder .ui.card.padded {
        margin-top: 1em;
        margin-bottom: 1em;
    }
    .ui.card > .vbq-group-heading.content {
        background-color: #f3f4f5;
    }
    .vue-query-builder .vqb-group.depth-1 .vqb-rule,
    .vue-query-builder .vqb-group.depth-2 {
        border-left: 2px solid #8bc34a;
    }
    .vue-query-builder .vqb-group.depth-2 .vqb-rule,
    .vue-query-builder .vqb-group.depth-3 {
        border-left: 2px solid #00bcd4;
    }
    .vue-query-builder .vqb-group.depth-3 .vqb-rule,
    .vue-query-builder .vqb-group.depth-4 {
        border-left: 2px solid #ff5722;
    }

</style>
