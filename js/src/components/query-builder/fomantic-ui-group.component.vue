<template>
    <div class="vqb-group ui fluid card" :class="'depth-' + depth.toString()">
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
                    </div>
                </div>
                <div class="two wide right aligned column">
                    <i v-if="depth > 1" style="cursor: pointer" class="icon times" @click="remove"></i>
                </div>
            </div>
        </div>
        <div class="vbq-group-body content">
            <div class="rule-actions ui basic vertically fitted segment right aligned">
                <div class="ui horizontal divided list">
                    <div class="item">
                        <div class="ui horizontal list">
                            <div class=" item">
                                <select v-model="selectedRule" class="atk-qb-select">
                                    <option v-for="rule in rules" :key="rule.id" :value="rule">{{ rule.label }}</option>
                                </select>
                            </div>
                            <div class=" item">
                                <button
                                        type="button"
                                        class="ui mini primary button"
                                        @click="addRule"
                                >{{ labels.addRule }}</button>
                            </div>

                        </div>
                    </div>
                    <div class="item"  v-if="depth < maxDepth">
                        <button
                                type="button"
                                class="ui mini primary button"
                                @click="addGroup"
                        >{{ labels.addGroup }}</button>
                    </div>
                </div>
            </div>
            <query-builder-children v-bind="$props"/>
        </div>
    </div>

</template>

<script>
  import QueryBuilderGroup from "vue-query-builder/dist/group/QueryBuilderGroup.umd.js";
  import QueryBuilderRule from "./fomantic-ui-rule.component.vue";

  export default {
    name: "QueryBuilderGroup",
    components: {
      QueryBuilderRule: QueryBuilderRule
    },
    computed: {
      getLevel() {
        let level;
        switch (this.depth) {
          case 2:
            level = 'secondary';
            break;
            case 3:
              level = 'tertiary';
              break;
        }
        return level;
      },
      getOptions() {
        return [
          {
            text: 'Male',
            value: 1,
          },
          {
            text: 'Female',
            value: 2,
          },
        ];
      }
    },
    extends: QueryBuilderGroup
  };
</script>

<style>
    .atk-qb-select, .ui.form select.atk-qb-select {
       padding: 2px 6px 4px 4px;
    }
    .vbq-group-heading > .ui.grid > .column:not(.row) {
        padding-bottom: 0.5em;
        padding-top: 0.5em;
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
