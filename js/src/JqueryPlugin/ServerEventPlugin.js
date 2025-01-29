import atk from 'atk';
import AbstractPlugin from './AbstractPlugin';

export default class AtkServerEventPlugin extends AbstractPlugin {
    main() {
        const element = this.$el;
        const hasLoader = this.settings.showLoader;

        this.source = new EventSource(this.settings.url);
        if (hasLoader) {
            element.addClass('loading');
        }

        this.source.addEventListener('message', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        this.source.addEventListener('error', (e) => {
            if (e.eventPhase === EventSource.CLOSED) {
                if (hasLoader) {
                    element.removeClass('loading');
                }
                this.source.close();
            }
        });

        this.source.addEventListener('atkSseAction', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        if (this.settings.closeBeforeUnload) {
            window.addEventListener('beforeunload', (event) => {
                this.source.close();
            });
        }
    }

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
