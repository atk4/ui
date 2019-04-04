import $ from 'jquery';

/**
 * Singleton class
 * This is default setup for semantic-ui Popup.
 */
class PopupService {

  static getInstance() {
    return this.instance;
  }

  constructor() {
    if (!PopupService.instance) {
      PopupService.instance = this;
    }
    return PopupService.instance;
  }

  setPopups(settings) {
    settings.onCreate = this.onCreate;
    settings.onShow = this.onShow;
    settings.onHide = this.onHide;
    settings.onVisible = this.onVisible;
    settings.onRemove = this.onRemove;
  }

  /**
   * OnShow callback when a popup is trigger.
   * Will check if popup need to be setup dynamically using a callback.
   *
   * @param $module
   */
  onShow($module) {
    const $popup = this;
    const data = $popup.data();
    if ((data.uri !== '') && (data.uri != undefined)) {
      //Only load if we are not using data.cache or content has not been loaded yet.
      if (!data.cache || !data.hascontent) {
        //display default loader while waiting for content.
        $popup.html(popupService.getLoader());
        $popup.api({
          on: 'now',
          url: data.uri,
          method: 'GET',
          obj: $popup,
          onComplete: function(response, content) {
            const result = $popup.html(response.html);
            if (!result.length) {
              response.success = false;
              response.isServiceError = true;
              response.message = 'Popup service error: Unable to replace popup content from server response. Empty Content.';
            } else {
              response.id = null;
              $popup.data('hascontent', true);
            }
          }
        });
      }
    }
  }

  /**
   * Call when hidding.
   */
  onHide() {}

  onVisible() {}

  /**
   * Only call when popup are created from metadata
   * and trigger action is fired.
   */
  onCreate() {
    //console.log('onCreate');
  }

  /**
   * Only call if onCreate was called.
   */
  onRemove() {
    //console.log('onRemvoe');
  }

  getLoader() {
    return `<div class="ui active inverted dimmer">
              <div class="ui mini text loader"></div>`
  }

}

const popupService = new PopupService();
Object.freeze(popupService);

export default popupService;
