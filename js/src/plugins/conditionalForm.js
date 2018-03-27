import atkPlugin from 'plugins/atkPlugin';
import $ from 'jquery';
import formService from "../services/FormService";


export default class conditionalForm extends atkPlugin {

  main() {
    this.inputs = {};
    //add change listener to all inputs
    this.$el.find('input, select').on('change', this, this.onInputChange);
    this.initialize();
  }

  initialize() {
    const that = this;

    // extract field name from rules and setup initial state.
    this.inputs = this.settings.fieldRules.reduce((inputs, rules, idx) => {
      const inputName = Object.keys(rules)[0];
      inputs[inputName] = false;
      return inputs;
    },{});

    //hide fields and get original value.
    for (const inputName in this.inputs) {
      const $input = formService.getField(that.$el, inputName);
      if ($input) {
        const $container = $input.closest('.field').hide();
        $input.data('original', $input.val());
      }
    }
  }

  onInputChange(e) {
    //check rule when inputs has changed.
   const that = e.data;
   that.resetInputStatus();
   that.settings.fieldRules.forEach((fieldRules) => {
     const inputName = Object.keys(fieldRules)[0];
     let isValid = true;
     for (const input in fieldRules[inputName]) {
       isValid &= formService.validateField(that.$el, input, fieldRules[inputName][input]);
     }
     //apply or conditions.
     that.inputs[inputName] |= isValid;
   });

   that.setInputsState();
  }

  resetInputStatus(){
    for (const inputName in this.inputs) {
      this.inputs[inputName] = false;
    }
  }

  setInputsState() {
    for (const inputName in this.inputs) {
      const $input = formService.getField(this.$el, inputName);
      if ($input) {
        const $container = $input.closest('.field').hide();
        this.setInputState(this.inputs[inputName], $input, $container);
      }
    }
  }

  setInputState(passed, field, fieldGroup) {
    if (passed) {
      fieldGroup.show();
    } else if (!passed && this.settings.autoReset) {
      fieldGroup.hide();
      field.val(field.data('original'));
    } else if (!passed && !this.settings.autoReset) {
      fieldGroup.hide();
    }

  }
}

conditionalForm.DEFAULTS = {
  autoReset: true,
  fieldRules:[
              {surname:{name:"notEmpty",gender:"is[1]",value:"integer[1..10]"}},
              {surname:{name:"isExactly[dog]"}},
              ]
};
