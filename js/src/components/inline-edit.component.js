
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
    initialValue: String,
    id: Number,
    saveOnBlur: {type: Boolean, default: true},
  },
  data: function() {
    return {value: this.initialValue, temp: this.initialValue};
  },
  computed: {
    isDirty: function() {
      return this.temp !== this.value;
    }
  },
  methods: {
    onFocus: function() {
      this.temp = this.value;
    },
    onBlur: function() {
      if (this.isDirty && this.saveOnBlur) {
        this.update();
        this.temp = this.value;
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
        this.temp = this.value;
      }
      this.$el.querySelector('input').blur();
    },
    update: function() {
      const $obj = $(this.$el);
      $obj.api({
        on: 'now',
        url: this.url,
        data: {id: this.id, value: this.value},
        method: 'POST',
        context: this.$el.parentElement,
        obj: $obj,
        onComplete: function(r,e) {
        }
      });
    }
  },
}
