import atkPlugin from 'plugins/atkPlugin';

export default class serverEvent extends atkPlugin {

  main() {

    if (typeof(EventSource) !== "undefined") {
      let source = new EventSource(this.settings.uri);
      source.onmessage = function (e) {
        //console.log('event', e.lastEventId, e.data);
        console.log('event', JSON.parse(e.data));
      };
      source.onerror = function (e) {
        //console.log('error', e);
      }
    } else {
      console.log('server side event not supported');
    }
  }
}

serverEvent.DEFAULTS = {
  uri: null,
  uri_options: {},
};