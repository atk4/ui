import atk from 'atk';
import AbstractPlugin from './AbstractPlugin';

export default class AtkServerSentEventPlugin extends AbstractPlugin {
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
                this.source.close();

                if (hasLoader) {
                    element.removeClass('loading');
                }
            }
        });

        this.source.addEventListener('atkSseAction', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        // prevent "The connection to http://xxx was interrupted while the page was loading." browser console warning
        window.addEventListener('beforeunload', () => this.source.close());
    }

    stop() {
        this.source.close();

        if (this.settings.showLoader) {
            this.$el.removeClass('loading');
        }
    }
}

AtkServerSentEventPlugin.DEFAULTS = {
    url: null,
    urlOptions: {},
    showLoader: false,
};
