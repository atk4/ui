import atk from 'atk';

/**
 * This is default setup for Fomantic-UI popup.
 */
class PopupService {
    getDefaultFomanticSettings() {
        return [
            {},
            {
                onShow: this.onShow,
            },
        ];
    }

    /**
     * Check if popup needs to be setup dynamically using a callback.
     */
    onShow($module) {
        const $popup = this;
        const data = $popup.data();
        if (data.url !== '' && data.url !== undefined) {
            // only load if we are not using data.cache or content has not been loaded yet
            if (!data.cache || !data.hascontent) {
                // display default loader while waiting for content
                $popup.html(atk.popupService.getLoaderHtml());
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
                            response.message = 'Popup service error: Empty HTML, unable to replace popup content from server response';
                        } else {
                            response.id = null;
                            $popup.data('hascontent', true);
                        }
                    },
                });
            }
        }
    }

    getLoaderHtml() {
        return '<div class="ui active inverted dimmer">'
            + '<div class="ui mini text loader"></div>'
            + '</div>';
    }
}

export default Object.freeze(new PopupService());
