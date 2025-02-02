import $ from 'external/jquery';
import atk from 'atk';
import AbstractPlugin from './AbstractPlugin';

export default class AtkServerSentEventPlugin extends AbstractPlugin {
    main() {
        const element = this.$el;
        const hasLoader = this.settings.showLoader;
        const stateContext = $(this.settings.stateContext ?? element);

        this.source = new EventSource(this.settings.url);

        if (hasLoader) {
            stateContext.addClass('loading');
        }

        this.source.addEventListener('message', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        this.source.addEventListener('error', (e) => {
            if (e.eventPhase === EventSource.CLOSED) {
                this.source.close();

                if (hasLoader) {
                    stateContext.removeClass('loading');
                }
            }
        });

        this.source.addEventListener('atkSseAction', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        // fix https://github.com/atk4/ui/issues/393
        atk.elementRemoveObserver.addHandler(stateContext[0], () => this.stop());

        // prevent "The connection to http://xxx was interrupted while the page was loading." browser console warning
        window.addEventListener('beforeunload', () => this.source.close());
    }

    stop() {
        const wasActive = this.source.readyState !== EventSource.CLOSED;

        this.source.close();

        if (wasActive) {
            console.warn('SSE plugin - request aborted');
        }

        if (this.settings.showLoader) {
            this.$el.removeClass('loading');
        }
    }
}

AtkServerSentEventPlugin.DEFAULTS = {
    url: null,
    stateContext: null,
    showLoader: false,
};
