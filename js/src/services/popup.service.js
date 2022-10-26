import atk from 'atk';

/**
 * This is default setup for Fomantic-UI popup.
 */
class PopupService {
    getDefaultFomanticSettings() {
        return [
            {
            },
            {
                onCreate: this.onCreate,
                onShow: this.onShow,
                onHide: this.onHide,
                onVisible: this.onVisible,
                onRemove: this.onRemove,
            },
        ];
    }

    /**
     * OnShow callback when a popup is trigger.
     * Will check if popup needs to be setup dynamically using a callback.
     */
    onShow($module) {
        const $popup = this;
        const data = $popup.data();
        if (data.url !== '' && data.url !== undefined) {
            // Only load if we are not using data.cache or content has not been loaded yet.
            if (!data.cache || !data.hascontent) {
                // display default loader while waiting for content.
                $popup.html(atk.popupService.getLoader());
                $popup.api({
                    on: 'now',
                    url: data.url,
                    method: 'GET',
                    obj: $popup,
                    onComplete: function (response, content) {
                        const result = $popup.html(response.html);
                        if (result.length === 0) {
                            response.success = false;
                            response.isServiceError = true;
                            response.message = 'Popup service error: Empty html, unable to replace popup content from server response';
                        } else {
                            response.id = null;
                            $popup.data('hascontent', true);
                        }
                    },
                });
            }
        }
    }

    onHide() {}

    onVisible() {}

    /**
     * Only call when popup are created from metadata
     * and trigger action is fired.
     */
    onCreate() {
        // console.log('onCreate');
    }

    /**
     * Called only if onCreate was called.
     */
    onRemove() {
        // console.log('onRemove');
    }

    getLoader() {
        return `<div class="ui active inverted dimmer">
              <div class="ui mini text loader"></div>`;
    }
}

export default Object.freeze(new PopupService());
