<template>
    <div
        class="vqb-rule ui fluid card"
        :class="labels.spaceRule"
        :data-name="rule.id"
    >
        <div class="content">
            <div class="ui grid">
                <div class="middle aligned row atk-qb">
                    <div class="thirteen wide column">
                        <div class="ui horizontal list">
                            <div class="item vqb-rule-label">
                                <h5 class>
                                    {{ rule.label }}
                                </h5>
                            </div>
                            <div
                                v-if="rule.operands !== undefined"
                                class="item vqb-rule-operand"
                            >
                                <select
                                    v-model="query.operand"
                                    class="atk-qb-select"
                                >
                                    <option
                                        v-for="operand in rule.operands"
                                        :key="operand"
                                    >
                                        {{ operand }}
                                    </option>
                                </select>
                            </div>
                            <div
                                v-if="rule.operators !== undefined && rule.operators.length > 1"
                                class="item vqb-rule-operator"
                            >
                                <select
                                    v-model="query.operator"
                                    class="atk-qb-select"
                                >
                                    <option
                                        v-for="operator in rule.operators"
                                        :key="operator"
                                        :value="operator"
                                    >
                                        {{ operator }}
                                    </option>
                                </select>
                            </div>
                            <div class="item vqb-rule-input">
                                <template v-if="canDisplay('input')">
                                    <div class="ui small input atk-qb">
                                        <input
                                            v-model="query.value"
                                            :type="rule.inputType === 'number' ? 'text' : rule.inputType"
                                            :placeholder="labels.textInputPlaceholder"
                                        >
                                    </div>
                                </template>
                                <template v-if="canDisplay('checkbox')">
                                    <!-- TODO <SuiFormFields -->
                                    <div
                                        inline
                                        class="atk-qb"
                                    >
                                        <div
                                            v-for="choice in rule.choices"
                                            :key="choice.value"
                                            class="field"
                                        >
                                            <!-- TODO radio support in https://github.com/nightswinger/vue-fomantic-ui/blob/v0.13.0/src/modules/Checkbox/Checkbox.tsx -->
                                            <SuiCheckbox
                                                v-model="query.value"
                                                :label="choice.label"
                                                :radio="isRadio"
                                                :value="choice.value"
                                            />
                                        </div>
                                    <!-- TODO </SuiFormFields> -->
                                    </div>
                                </template>
                                <template v-if="canDisplay('select')">
                                    <select
                                        v-model="query.value"
                                        class="atk-qb-select"
                                    >
                                        <option
                                            v-for="choice in rule.choices"
                                            :key="choice.value"
                                            :value="choice.value"
                                        >
                                            {{ choice.label }}
                                        </option>
                                    </select>
                                </template>
                                <template v-if="canDisplay('custom-component')">
                                    <div class="ui small input atk-qb">
                                        <component
                                            :is="rule.component"
                                            v-model="query.value"
                                            :config="rule.componentProps"
                                            :optionalValue="query.option"
                                        />
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="right aligned three wide column">
                        <i
                            :class="labels.removeRuleClass"
                            class="atk-qb-remove"
                            @click="remove"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueQueryBuilderRule from 'vue-query-builder/src/components/QueryBuilderRule';
import AtkDatePicker from '../share/atk-date-picker';
import AtkLookup from '../share/atk-lookup';

export default {
    components: {
        AtkDatePicker: AtkDatePicker,
        AtkLookup: AtkLookup,
    },
    extends: VueQueryBuilderRule,
    inject: ['getRootData'],
    data: function () {
        return {};
    },
    computed: {
        isInput: function () {
            return this.rule.type === 'text' || this.rule.type === 'numeric';
        },
        isComponent: function () {
            return this.rule.type === 'custom-component';
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
         * @returns {boolean}
         */
        canDisplay: function (type) {
            if (this.labels.hiddenOperator.includes(this.query.operator)) {
                return false;
            }

            switch (type) {
                case 'input': {
                    return this.isInput;
                }
                case 'checkbox': {
                    return this.isCheckbox;
                }
                case 'select': {
                    return this.isSelect;
                }
                case 'custom-component': {
                    return this.isComponent;
                }
                default: {
                    return false;
                }
            }
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
