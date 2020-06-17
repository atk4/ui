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
                                <template v-if="isInput">
                                    <div class="ui small input atk-qb" >
                                        <input
                                                v-model="query.value"
                                                :type="rule.inputType"
                                                :placeholder="labels.textInputPlaceholder"
                                        >
                                    </div>
                                </template>
                                <!-- Date input -->
                                <template v-if="isDatePicker">
                                    <div class="ui small input atk-qb">
                                        <v-date-picker
                                                :locale='dateLocale'
                                                :input-props="{class: 'atk-qb-date-picker'}"
                                                v-model="dateValue"
                                                :masks="dateMask"
                                                :popover="{ placement: 'bottom', visibility: 'click' }"
                                                ref="dateRef"></v-date-picker>
                                    </div>
                                </template>
                                <!-- Radio input -->
                                <template v-if="isRadio">
                                    <sui-form-fields inline class="atk-qb">
                                        <div class="field" v-for="choice in rule.choices" :key="choice.value">
                                            <sui-checkbox :label="choice.label" radio :value="choice.value" v-model="query.value"></sui-checkbox>
                                        </div>
                                    </sui-form-fields>
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
    component: {
    },
    data: function() {
      return {
        dateMask: {input:  this.rule.format ? this.rule.format : 'YYYY-MM-DD'},
        dateString: this.isDatePicker ? this.query.value : null,
        dateLocale: this.rule.locale ? this.rule.locale : 'en-En'
      }
    },
    computed: {
      isInput: function() {
        return this.rule.type === 'text' || this.rule.type === 'numeric';
      },
      isDatePicker: function() {
        return this.rule.type === 'custom-component' && this.rule.component === 'DatePicker';
      },
      isRadio: function() {
        return this.rule.type === 'radio';
      },
      dateValue: {
        get: function() {
          if (this.dateString) {
            return new Date(this.dateString);
          }
          return new Date();
        },
        set: function(date){
          this.query.value =  atk.phpDate("Y-m-d", date);
        }
      }
    },
    methods: {
      onDateChange: function(e) {
        console.log('change', e);
      },

    }
  };
</script>

<style>
    .ui.input.atk-qb > input, .ui.input.atk-qb > span > input, .ui.form .input.atk-qb {
        padding: 6px;
    }
    .ui.grid > .row.atk-qb {
        padding: 8px 0px;
        min-height: 62px;
    }
    .inline.fields.atk-qb, .ui.form .inline.fields.atk-qb {
        margin: 0px;
    }
    .atk-qb-date-picker {
        border: 1px solid rgba(34, 36, 38, 0.15);
    }
    input[type=input].atk-qb-date-picker:focus {
        border-color: #85b7d9;
    }
    .ui.card.vqb-rule > .content {
        padding-bottom: 0.5em;
        padding-top: 0.5em;
        background-color: #f3f4f5;
    }
</style>
