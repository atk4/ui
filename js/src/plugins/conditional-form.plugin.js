import atkPlugin from './atk.plugin';
import formService from '../services/form.service';

/* eslint-disable no-bitwise */

/**
 * Show or hide input field base on other input field condition.
 * Support all semantic-ui form validation rule.
 * Note on rule. FormService also add two more rule to semantic-ui existing ones:
 *    - notEmpty;
 *    - isVisible;
 *    - isEqual[number] for number comparaison.
 *
 * Here is the phrasing of the rule.
 *  - Show "this field" if all condition are met.
 *    fieldRules is an array that contains items where each item describe the field to hide or show
 *    that depends on other field with their input value conditions.
 *
 *    $form->js()->atkConditionalForm(
 *      [ 'fieldRules =>
 *        [
 *          'fieldToShow' => ['field1' => 'notEmpty', 'field2' => 'number']
 *        ]
 *      ]);
 *   Can be phrase this way: Display 'fieldToShow' if 'field1' is not empty AND field2 is a number.
 *
 *   Adding and array of field => rules for the same field will OR the condition for that field.
 *    $form->js()->atkConditionalForm(
 *      [ 'fieldRules =>
 *        [
 *          'hair_cut' => [
 *                          ['race' => 'contains[poodle]', 'age'=>'integer[0..5]'],
 *                          ['race' => 'isExactly[bichon]']
 *                        ]
 *       ]
 *      ]);
 *   Can be phrase this way: Display 'hair_cut' if 'race' contains 'poodle' AND 'age' is between 0 and 5 OR 'race' contains the exact word 'bichon'.
 *
 *
 *   Adding an array of conditions for the same field is also support.
 *
 *    $form->js()->atkConditionalForm(
 *      [ 'fieldRules =>
 *        [
 *          'ext' => ['phone' => ['number', 'minLength[7]']]
 *        ]
 *      ]);
 *   Can be phrase this way: Display 'ext' if phone is a number AND phone has at least 7 char.
 *
 *   See semantic-ui validation rule for more details: https://semantic-ui.com/behaviors/form.html#validation-rules
 */
export default class conditionalForm extends atkPlugin {
    main() {
        this.inputs = [];
        this.selector = this.settings.selector;
        if (!this.selector) {
            this.selector = formService.getDefaultSelector();
        }
        // add change listener to inputs according to selector
        this.$el.find(':checkbox')
            .on('change', this, atk.debounce(this.onInputChange, 100, true));
        this.$el.find(':radio')
            .on('change', this, atk.debounce(this.onInputChange, 100, true));
        this.$el.find('input[type="hidden"]')
            .on('change', this, atk.debounce(this.onInputChange, 100, true));
        this.$el.find('input')
            .on(this.settings.validateEvent, this, atk.debounce(this.onInputChange, 250));
        this.$el.find('select')
            .on('change', this, atk.debounce(this.onInputChange, 100));

        this.initialize();
    }

    getRule(ruleToSearch) {
        return this.settings.fieldRules[ruleToSearch];
    }

    initialize() {
        const ruleKeys = Object.keys(this.settings.fieldRules);
        // map inputs according to ruleKeys.
        this.inputs = ruleKeys.map((ruleKey, idx, org) => {
            const tempRule = this.settings.fieldRules[ruleKey];
            const temp = [];
            if (Array.isArray(tempRule)) {
                tempRule.forEach((rule) => temp.push(rule));
            } else {
                temp.push(tempRule);
            }
            return { inputName: ruleKey, rules: temp, state: false };
        });

        this.applyRules();
        this.setInputsState();
    }

    /**
   * Field change handler.
   *
   * @param e
   */
    onInputChange(e) {
    // check rule when inputs has changed.
        e.data.resetInputStatus();
        e.data.applyRules();
        e.data.setInputsState();
    }

    /**
   * Check each validation rule and apply proper visibility state to the
   * input where rules apply.
   *
   */
    applyRules() {
        this.inputs.forEach((input, idx) => {
            input.rules.forEach((rules) => {
                let isAndValid = true;
                const validateInputNames = Object.keys(rules);
                validateInputNames.forEach((inputName) => {
                    const validationRule = rules[inputName];
                    if (Array.isArray(validationRule)) {
                        validationRule.forEach((rule) => {
                            isAndValid &= formService.validateField(this.$el, inputName, rule);
                        });
                    } else {
                        isAndValid &= formService.validateField(this.$el, inputName, validationRule);
                    }
                });
                // Apply OR condition between rules.
                input.state |= isAndValid;
            });
        });
    }

    /**
   * Set all input state visibility to false.
   */
    resetInputStatus() {
        this.inputs.forEach((input) => {
            input.state = false;
        });
    }

    /**
   * Set fields visibility according to their state.
   */
    setInputsState() {
        this.inputs.forEach((input) => {
            const $input = formService.getField(this.$el, input.inputName);
            if ($input) {
                const $container = formService.getContainer($input, this.selector);
                if ($container) {
                    $container.hide();
                    this.setInputState(input.state, $input, $container);
                }
            }
        });
    }

    setInputState(passed, field, fieldGroup) {
        if (passed) {
            fieldGroup.show();
        } else if (!passed && this.settings.autoReset) {
            fieldGroup.hide();
            // field.val(field.data('original'));
        } else if (!passed && !this.settings.autoReset) {
            fieldGroup.hide();
        }
    }
}

conditionalForm.DEFAULTS = {
    autoReset: true,
    validateEvent: 'keydown',
    selector: null,
    fieldRules: [],
};
