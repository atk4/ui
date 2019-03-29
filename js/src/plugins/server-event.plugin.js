import atkPlugin from './atk.plugin';
import apiService from '../services/api.service';

export default class serverEvent extends atkPlugin {

  main() {

    const element = this.$el;
    const hasLoader = this.settings.showLoader;

    if (typeof(EventSource) !== "undefined") {
      let source = new EventSource(`${this.settings.uri}&event=${this.settings.event}`);
      if(hasLoader) {
        element.addClass('loading');
      }
      source.onmessage = function (e) {
        apiService.atkSuccessTest(JSON.parse(e.data));
      };
      source.onerror = function (e) {
          if (e.eventPhase === EventSource.CLOSED) {
            if (hasLoader) {
              element.removeClass('loading');
            }
            source.close();
          }
      };
      source.addEventListener("jsAction", function(e) {
        apiService.atkSuccessTest(JSON.parse(e.data));
      }, false);
    } else {
      //console.log('server side event not supported fallback to atkReloadView');
      this.$el.atkReloadView({
        uri: this.settings.uri,
      });
    }
  }
}

serverEvent.DEFAULTS = {
  uri: null,
  uri_options: {},
  showLoader: false,
  event: 'sse',
};