import $ from 'jquery';
import atkPlugin from './atk.plugin';
import apiService from "../services/api.service";

export default class redirect extends atkPlugin {
  main() {
    if (!this.settings.uri) {
      console.error('Trying to reload view without url.');
      return;
    }


    document.location = $.atkAddParams(this.settings.uri, this.settings.uri_options);
  }
}

redirect.DEFAULTS = {
  uri: null,
  uri_options: {},
};
