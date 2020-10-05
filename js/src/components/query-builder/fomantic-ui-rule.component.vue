<template>
    <!-- eslint-disable vue/no-v-html -->
    <div class="vqb-rule ui fluid card" :class="labels.spaceRule">
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
                            <div class="item" v-if="typeof rule.operators !== 'undefined' && rule.operators.length > 1">
                                <!-- List of operators (e.g. =, !=, >, <) -->
                                <select v-model="query.operator" class="atk-qb-select">
                                    <option v-for="operator in rule.operators" :key="operator" :value="operator">
                                        {{operator}}
                                    </option>
                                </select>
                            </div>
                            <div class="item">
                                <!-- text input -->
                                <template v-if="canDisplay('input')">
                                    <div class="ui small input atk-qb" >
                                        <input
                                                v-model="query.value"
                                                :type="rule.inputType"
                                                :placeholder="labels.textInputPlaceholder"
                                        >
                                    </div>
                                </template>
                                <!-- Date input -->
                                <template v-if="canDisplay('date')">
                                    <div class="ui small input atk-qb">
                                      <atk-date-picker
                                          :datePickerProps="getDatePickerProps()"
                                          :atkDateOptions="getAtkDatePickerProps()"
                                          :value="query.value"
                                          @dateChange="onDateChange"></atk-date-picker>
                                    </div>
                                </template>
                                <!-- Checkbox or Radio input -->
                                <template v-if="canDisplay('checkbox')">
                                    <sui-form-fields inline class="atk-qb">
                                        <div class="field" v-for="choice in rule.choices" :key="choice.value">
                                            <sui-checkbox
                                                :label="choice.label"
                                                :radio="isRadio"
                                                :value="choice.value"
                                                v-model="query.value">
                                            </sui-checkbox>
                                        </div>
                                    </sui-form-fields>
                                </template>
                                <!-- Select input -->
                                <template v-if="canDisplay('select')">
                                    <select v-model="query.value" class="atk-qb-select">
                                        <option
                                            v-for="choice in rule.choices"
                                            :key="choice.value"
                                            :value="choice.label">
                                            {{choice.label}}
                                        </option>
                                    </select>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="right aligned three wide column">
                        <!-- Remove rule button -->
                        <i :class="labels.removeRule" @click="remove" class="atk-qb-remove"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import QueryBuilderRule from 'vue-query-builder/dist/rule/QueryBuilderRule.umd';
import AtkDatePicker from '../share/atk-date-picker';

export default {
    extends: QueryBuilderRule,
    components: { 'atk-date-picker': AtkDatePicker },
    data: function () {
        return {};
    },
    inject: ['getRootData'],
    computed: {
        isInput: function () {
            return this.rule.type === 'text' || this.rule.type === 'numeric';
        },
        isDatePicker: function () {
            return this.rule.type === 'custom-component' && this.rule.component === 'DatePicker';
        },
        isRadio: function () {
            return this.rule.type === 'radio';
        },
        isCheckbox: function () {
            return this.rule.type === 'checkbox' || this.isRadio;
        },
        isSelect: function () {
            return this.rule.type === 'select';
        },
    },
    methods: {
        /**
       * Check if an input can be display in regards to:
       * it's operator and then it's type.
       *
       * @param type
       * @returns {boolean|*}
       */
        canDisplay: function (type) {
            if (this.labels.hiddenOperator.includes(this.query.operator)) {
                return false;
            }

            switch (type) {
            case 'input': return this.isInput;
            case 'date': return this.isDatePicker;
            case 'checkbox': return this.isCheckbox;
            case 'select': return this.isSelect;
            default: return false;
            }
        },
        onDateChange: function (date) {
            this.query.value = date;
        },
        getDatePickerProps: function () {
            return {
                'input-props': { class: 'atk-qb-date-picker' },
                popover: { placement: 'bottom', visibility: 'click' },
                ...this.getRootData().data.componentsProps.datePicker || {},
            };
        },
        getAtkDatePickerProps: function () {
            return this.getRootData().data.componentsProps.atkDateOptions || {};
        },
    },
};
</script>

<style>
    .ui.input.atk-qb > input, .ui.input.atk-qb span > input, .ui.form .input.atk-qb {
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
