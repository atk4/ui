import $ from 'jquery';

/**
 * Singleton class
 * Handle Semantic-ui form functionality throughout the app.
 */

class FormService {

  static getInstance() {
    return this.instance;
  }


  constructor() {
    if (!this.instance) {
      this.instance = this;
      this.formSettings = $.fn.form.settings;
      // A collection of jQuery form object where preventLeave is set.
      this.prevents = [];
      window.onbeforeunload = function (event) {
        $('form').each(function(){
          if ($(this).data('__atkCheckDirty') && $(this).data('isDirty')) {
            let message = "unsaved";
            if (event) {
              event.returnValue = message;
            }
            return message;
          }
        });
      };
    }
    return this.instance;
  }

  /**
   * Setup semantic-ui form callback with this service.
   * @param settings
   */
  setService(settings) {
    settings.rules.isVisible = this.isVisible;
    settings.rules.notEmpty = settings.rules.empty;
    settings.rules.isEqual = this.isEqual;
    settings.onSuccess = this.onSuccess;
  }

  /**
   * Form onSuccess handler when submit.
   */
  onSuccess() {
    formService.clearDirtyForm($(this).attr('id'));
    return true;
  }

  /**
   * Set form in order to detect
   * input changed before leaving page.
   *
   * @param id
   */
  preventFormLeave(id) {
    const $form =  $('#'+id);
    $form.data('__atkCheckDirty', true);
    $form.on('change.__atkCanLeave', 'input', function(){
      $form.data('isDirty', true);
    });
    this.prevents.push($form);
  }

  /**
   * Clear Form from being dirty.
   * Use this function if you define your own onSuccess handler.
   *
   * @param id
   */
  clearDirtyForm(id) {
    const forms = this.prevents.filter($form => $form.attr('id') === id);
    forms.forEach( $form => {
      $form.data('isDirty', false);
    });
  }

  /**
   * Visibility rule.
   *
   * @returns {boolean | jQuery}
   */
  isVisible() {
    return $(this).is(':visible');
  }

  isEqual(value, compare) {
    return parseInt(value) === parseInt(compare);
  }

  /**
   * Validate a field using our own or semantic-ui validation rule function.
   *
   * @param form  Form containing the field.
   * @param fieldName Name of field
   * @param rule  Rule to apply test.
   * @returns {*|boolean}
   */
  validateField(form, fieldName, rule) {
    rule = this.normalizeRule(rule);
    const ruleFunction = this.getRuleFunction(this.getRuleName(rule));
    if (ruleFunction) {
      const $field = this.getField(form, fieldName);
      if (!$field) {
        console.log('You are validating a field that does not exist: ', fieldName);
        return false;
      }
      const value = this.getFieldValue($field);
      const ancillary = this.getAncillaryValue(rule);
      return ruleFunction.call($field, value, ancillary);
    } else {
      console.log('this rule does not exist: '+this.getRuleName(rule));
      return false;
    }
  }

  normalizeRule(rule) {
    if (typeof rule === 'string') {
      return {type: rule, value:null};
    }
    return rule;
  }

  getDefaultSelector() {
    return $.fn.form.settings.selector.group;
  }

  getContainer($field, selector){
    const $container = $field.closest(selector);
    if ($container.length > 1) {
      //radio button.
      return this.getContainer($container.parent(), selector);
    } else if ($container.length === 0) {
      return null;
    }
    return $container;
  }

  getField(form, identifier) {
    if(form.find('#' + identifier).length > 0 ) {
      return form.find('#' + identifier);
    }
    else if( form.find('[name="' + identifier +'"]').length > 0 ) {
      return form.find('[name="' + identifier +'"]');
    }
    else if( form.find('[name="' + identifier +'[]"]').length > 0 ) {
      return form.find('[name="' + identifier +'[]"]');
    }
    return false;
  }

  getFieldValue($field) {
    let value;
    if ($field.length > 1) {
      // radio button.
      value = $field.filter(':checked').val();
    } else {
      value = $field.val();
    }
    return value;
  }

  getRuleFunction(rule) {
    return this.formSettings.rules[rule];
  }

  getAncillaryValue(rule) {
    //must have a rule.value property and must be a bracketed rule.
    if(!rule.value && !this.isBracketedRule(rule)) {
      return false;
    }
    return (rule.value === undefined || rule.value === null)
      ? rule.type.match(this.formSettings.regExp.bracket)[1] + ''
      : rule.value
      ;
  }

  getRuleName(rule) {
    if( this.isBracketedRule(rule) ) {
      return rule.type.replace(rule.type.match(this.formSettings.regExp.bracket)[0], '');
    }
    return rule.type;
  }

  isBracketedRule(rule) {
    return (rule.type && rule.type.match(this.formSettings.regExp.bracket));
  }
}

const formService = new FormService();
Object.freeze(formService);

export default formService;
