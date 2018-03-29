import atkPlugin from 'plugins/atkPlugin';
import $ from 'jquery';
import formService from "../services/FormService";

/**
 * Show or hide input field base on other input field condition.
 * Support all semantic-ui form validation rule.
 * Note on rule. FormService also add two more rule to semantic-ui existing ones:
 *    notEmpty and isVisible.
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
    //add change listener to inputs according to selector
    this.$el.find('input, select').on('change', this, this.onInputChange);
    this.initialize();
  }

  getRule(ruleToSearch) {
    return this.settings.fieldRules[ruleToSearch];
  }

  initialize() {
    const that = this;

    const ruleKeys = Object.keys(this.settings.fieldRules);
    //map inputs according to ruleKeys.
    this.inputs = ruleKeys.map((ruleKey, idx, org)=> {
      let tempRule =  that.settings.fieldRules[ruleKey];
      let temp = [];
      if (Array.isArray(tempRule)) {
        tempRule.forEach(rule => temp.push(rule));
      } else {
        temp.push(tempRule);
      }
      return {inputName : ruleKey, rules: temp, state: false}
    });

    that.setInputsState();
  }

  /**
   * Check each validation rule and apply proper state to the
   * input where rules apply when a field is change.
   *
   * @param e
   */
  onInputChange(e) {
    //check rule when inputs has changed.
   const that = e.data;
   that.resetInputStatus();
   that.inputs.forEach((input, idx) => {
     input.rules.forEach((rules) => {
       let isAndValid = true;
       let validateInputNames = Object.keys(rules);
       validateInputNames.forEach( (inputName) => {
         const validationRule = rules[inputName];
         if (Array.isArray(validationRule)){
            validationRule.forEach((rule) => {
              isAndValid &= formService.validateField(that.$el, inputName, rule);
            });
         } else {
           isAndValid &= formService.validateField(that.$el, inputName, validationRule);
         }
       });
       // Apply OR conditon between rules.
       input.state |= isAndValid;
     });
    });

   that.setInputsState();
  }

  resetInputStatus(){
    this.inputs.forEach((input) => {
      input.state = false;
    });
  }

  setInputsState() {
    const that = this;
    this.inputs.forEach((input) => {
      //console.log(input);
      const $input = formService.getField(that.$el, input.inputName);
      if ($input) {
        const $container = formService.getContainer($input);
        if ($container) {
          $container.hide();
        }
        that.setInputState(input.state, $input, $container );
      }
    });
  }

  setInputState(passed, field, fieldGroup) {
    if (passed) {
      fieldGroup.show();
    } else if (!passed && this.settings.autoReset) {
      fieldGroup.hide();
      //field.val(field.data('original'));
    } else if (!passed && !this.settings.autoReset) {
      fieldGroup.hide();
    }

  }
}

conditionalForm.DEFAULTS = {
  autoReset: true,
  fieldRules:[],
};
