import atk from 'atk';
import AtkPlugin from './atk.plugin';

export default class AtkServerEventPlugin extends AtkPlugin {
    main() {
        const element = this.$el;
        const hasLoader = this.settings.showLoader;

        if (typeof (EventSource) !== 'undefined') {
            this.source = new EventSource(this.settings.url + '&__atk_sse=1');
            if (hasLoader) {
                element.addClass('loading');
            }

            this.source.onmessage = function (e) {
                atk.apiService.atkSuccessTest(JSON.parse(e.data));
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
                atk.apiService.atkSuccessTest(JSON.parse(e.data));
            }, false);

            if (this.settings.closeBeforeUnload) {
                window.addEventListener('beforeunload', (event) => {
                    this.source.close();
                });
            }
        } else {
            // console.log('server side event not supported fallback to atkReloadView');
            this.$el.atkReloadView({
                url: this.settings.url,
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

AtkServerEventPlugin.DEFAULTS = {
    url: null,
    urlOptions: {},
    showLoader: false,
    closeBeforeUnload: false,
};
