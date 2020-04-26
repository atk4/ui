import $ from 'jquery';
import atkPlugin from './atk.plugin';
import apiService from '../services/api.service';

export default class serverEvent extends atkPlugin {

  main() {

    const element = this.$el;
    const hasLoader = this.settings.showLoader;
    const that = this;

    if (typeof(EventSource) !== "undefined") {
      this.source = new EventSource(`${this.settings.uri}&__atk_sse=1`);
      if(hasLoader) {
        element.addClass('loading');
      }

      this.source.onmessage = function (e) {
        apiService.atkSuccessTest(JSON.parse(e.data));
      };

      this.source.onerror = function (e) {
          if (e.eventPhase === EventSource.CLOSED) {
            if (hasLoader) {
              element.removeClass('loading');
            }
            that.source.close();
          }
      };

      this.source.addEventListener("atk_sse_action", function(e) {
        apiService.atkSuccessTest(JSON.parse(e.data));
      }, false);

      if (this.settings.closeBeforeUnload) {
        window.addEventListener('beforeunload', function(event) {
          that.source.close();
        });
      }
    } else {
      //console.log('server side event not supported fallback to atkReloadView');
      this.$el.atkReloadView({
        uri: this.settings.uri,
      });
    }
  }

  /**
   * To close ServerEvent.
   */
  stop() {
    this.source.close();
    if (this.settings.showLoader) {
      this.$el.removeClass('loading');
    }
  }
}

serverEvent.DEFAULTS = {
  uri: null,
  uri_options: {},
  showLoader: false,
  closeBeforeUnload: false
};
