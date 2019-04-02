
/**
 * Vue component
 * Allow user to edit a db record inline and send
 * changes to server.
 *
 * Use an inline template.
 *
 * Properties need for this component are:
 *
 * context: string, a jQuery selector where the 'loading' class will be apply by semantic-ui;
 *          - default to the requesting element.
 * url:     string, the url to call;
 * value:  array, array of value to send to server.
 *
 */

export default {
  name: 'atk-inline-edit',
  props: {
    url: String,
    initValue: String,
    saveOnBlur: {type: Boolean, default: true},
  },
  data: function() {
    return {value: this.initValue, temp: this.initValue, hasError: null, isLoading: false};
  },
  computed: {
    isDirty: function() {
      return this.temp !== this.value;
    }
  },
  methods: {
    onFocus: function() {
      if (this.hasError) {
        this.clearError();
      } else {
        this.temp = this.value;
      }
    },
    onKeyUp: function(e) {
      const key  = e.keyCode;
      this.clearError();
      if (key === 13) {
        this.onEnter(e);
      } else if (key === 27) {
        this.onEscape();
      }
    },
    onBlur: function() {
      if (this.isDirty && this.saveOnBlur && !this.hasError) {
        this.update();
      } else {
        this.value = this.temp;
      }
    },
    onEscape: function() {
      this.value = this.temp;
      this.$el.querySelector('input').blur();
    },
    onEnter: function(e){
      if (this.isDirty) {
        this.update();
      }
    },
    clearError: function() {
      this.hasError = false;
    },
    flashError: function(count = 4) {
      if (count  === 0) {
        this.hasError = false;
        return;
      }
      this.hasError = !this.hasError;
      setTimeout(() => {
        this.flashError(count - 1);
      }, 300)
    },
    update: function() {
      const that = this;
      const $obj = $(this.$el);
      this.isLoading = true;
      $obj.api({
        on: 'now',
        url: this.url,
        data: {value: this.value},
        method: 'POST',
        onComplete: function(r,e) {
          if (r.hasValidationError) {
            that.hasError = true;
          } else {
            that.temp = that.value;
          }
          that.isLoading = false;
        }
      });
    }
  },
}
