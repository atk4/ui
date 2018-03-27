import atkPlugin from 'plugins/atkPlugin';
import $ from 'jquery';
import formService from "../services/FormService";


export default class conditionalForm extends atkPlugin {

  main() {
    this.inputs = {};
    //add change listener to all inputs
    this.$el.find('input').on('change', this, this.onInputChange);
    this.$el.find('select').on('change', this, this.onInputChange);
    this.initialize();
  }

  initialize() {
    const that = this;
    //hide each field where visibility rule apply.
    $.each(this.settings.fieldRules, (idx, field) => {
      //get field where visibility rule apply
      const fname = Object.keys(field)[0];
      const $field = formService.getField(this.$el, field);
      if ($field.length > 0) {
        const $container = $field.closest('.field').hide();
        $field.data('original', $field.val());
        that.inputs[field] = {state: false};
      } else {
        console.log('Unable to find field: '+field);
      }
    });
  }

  onInputChange(e) {
    //check rule when inputs has changed.
   const that = e.data;
   $.each(that.settings.fieldRules, (field, rules) => {
     const $field = formService.getField(that.$el, field);
     const $fieldGroup =  $field.closest('.field');
     let passed = true;
     $.each(rules, (fieldName, rule) => {
       if (!Array.isArray(rule)) {
          rule = [rule];
       }
       rule.forEach((r) => {
         passed &= formService.validateField(that.$el, fieldName, r);
       })
     });
     that.inputs[field].state |= passed;
     that.setFieldState(passed, $field, $fieldGroup);
     //passed ? $fieldGroup.show() : $fieldGroup.hide();
   });
  }

  setFieldState(passed, field, fieldGroup) {
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
              {surname:{name:"empty",gender:"is[1]",value:"integer[1..10]"}},
              {surname:{name:"isExactly[dog]"}},
              ]
};

// fieldRules: {
//   surname: {name: 'empty'/*, gender:'is[1]'*/, value: 'integer[0..10]'},
//   surname: {name: 'isExactly[dog]'}
// },
// fieldRules: [
//              {surname: {name: 'empty'/*, gender:'is[1]'*/, value: 'integer[0..10]'}},
//              {surname: {name: 'isExactly[dog]'}},
//             ],

//{fieldRules:[{surname:{name:"empty",gender:"is[1]",value:"integer[1..10]"}},{surname:{name:"isExactly[dog]"}}]}