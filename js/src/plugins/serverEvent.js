import atkPlugin from 'plugins/atkPlugin';
import apiService from '../services/ApiService';

export default class serverEvent extends atkPlugin {

  main() {

    if (typeof(EventSource) !== "undefined") {
      let source = new EventSource(this.settings.uri);
      source.onmessage = function (e) {
        apiService.atkSuccessTest(JSON.parse(e.data));
      };
      source.onerror = function (e) {
          if (e.eventPhase === EventSource.CLOSED) {
            source.close();
          }
      };
      source.addEventListener("jsAction", function(e) {
        apiService.atkSuccessTest(JSON.parse(e.data));
      }, false);
    } else {
      console.log('server side event not supported');
    }
  }
}

serverEvent.DEFAULTS = {
  uri: null,
  uri_options: {},
};