<template>
    <!-- eslint-disable vue/no-v-html -->
    <div class="vqb-rule ui fluid card">
        <div class="content">
            <div class="ui grid">
                <div class="middle aligned row atk-qb">
                    <div class="thirteen wide column">
                        <div class="ui horizontal list">
                            <div class="item">
                                <h5 class>{{ rule.label }}</h5>
                            </div>
                            <div class="item" v-if="typeof rule.operands !== 'undefined'">
                                <!-- List of operands (optional) -->
                                <select v-model="query.operand" class="atk-qb-select">
                                    <option v-for="operand in rule.operands" :key="operand">{{ operand }}</option>
                                </select>
                            </div>
                            <div class="item"  v-if="typeof rule.operators !== 'undefined' && rule.operators.length > 1">
                                <!-- List of operators (e.g. =, !=, >, <) -->
                                <select v-model="query.operator" class="atk-qb-select">
                                    <option v-for="operator in rule.operators" :key="operator" :value="operator">
                                        {{operator}}
                                    </option>
                                </select>
                            </div>
                            <div class="item">
                                <!-- text input -->
                                <template v-if="getInputType === 'text'">
                                    <div class="ui small input atk-qb" >
                                        <input
                                                v-model="query.value"
                                                type="text"
                                                :placeholder="labels.textInputPlaceholder"
                                        >
                                    </div>
                                </template>
                                <!-- Radio input -->
                                <template v-if="getInputType === 'radio'">
                                    <div class="inline fields atk-qb">
                                        <div class="field">
                                            <div class="ui radio checkbox" v-for="choice in rule.choices" :key="choice.value">
                                                <input
                                                        :id="'depth' + depth + '-' + rule.id + '-' + index + '-' + choice.value"
                                                        v-model="query.value"
                                                        type="radio"
                                                        :value="choice.value"
                                                >
                                                <label style="color:black"
                                                       :for="'depth' + depth + '-' + rule.id + '-' + index + '-' + choice.value"
                                                >
                                                    {{choice.label}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="right aligned three wide column">
                        <!-- Remove rule button -->
                        <i class="icon times" @click="remove" style="cursor: pointer"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import QueryBuilderRule from "vue-query-builder/dist/rule/QueryBuilderRule.umd.js";

  export default {
    extends: QueryBuilderRule,
    computed: {
      // temp until more type are supported.
      getInputType: function() {
        if (this.rule.inputType === 'radio') {
          return 'radio';
        }
        return 'text';
      }
    }
  };
</script>

<style>
    .ui.input.atk-qb > input, .ui.form .input.atk-qb {
        padding: 6px;
    }
    .ui.grid > .row.atk-qb {
        padding: 8px 0px;
        min-height: 62px;
    }
    .inline.fields.atk-qb, .ui.form .inline.fields.atk-qb {
        margin: 0px;
    }
    .ui.card.vqb-rule > .content {
        padding-bottom: 0.5em;
        padding-top: 0.5em;
        background-color: #f3f4f5;
    }
</style>
