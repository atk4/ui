import atkPlugin from './atk.plugin';
import apiService from '../services/api.service';

export default class serverEvent extends atkPlugin {
    main() {
        const element = this.$el;
        const hasLoader = this.settings.showLoader;

        if (typeof (EventSource) !== 'undefined') {
            this.source = new EventSource(this.settings.uri + '&__atk_sse=1');
            if (hasLoader) {
                element.addClass('loading');
            }

            this.source.onmessage = function (e) {
                apiService.atkSuccessTest(JSON.parse(e.data));
            };

            this.source.onerror = (e) => {
                if (e.eventPhase === EventSource.CLOSED) {
                    if (hasLoader) {
                        element.removeClass('loading');
                    }
                    this.source.close();
                }
            };

            this.source.addEventListener('atkSseAction', (e) => {
                apiService.atkSuccessTest(JSON.parse(e.data));
            }, false);

            if (this.settings.closeBeforeUnload) {
                window.addEventListener('beforeunload', (event) => {
                    this.source.close();
                });
            }
        } else {
            // console.log('server side event not supported fallback to atkReloadView');
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
    uriOptions: {},
    showLoader: false,
    closeBeforeUnload: false,
};
