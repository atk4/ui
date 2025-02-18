import $ from 'external/jquery';
import atk from 'atk';
import AbstractPlugin from './AbstractPlugin';

export default class AtkServerSentEventPlugin extends AbstractPlugin {
    main() {
        const hasLoader = this.settings.showLoader;
        const stateContext = $(this.settings.stateContext ?? this.el);

        this.source = new EventSource(this.settings.url);

        if (hasLoader) {
            stateContext.addClass('loading');
        }

        this.source.addEventListener('message', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        this.source.addEventListener('error', (e) => {
            this.stop();
        });

        this.source.addEventListener('atkSseAction', (e) => {
            atk.apiService.atkProcessExternalResponse(JSON.parse(e.data));
        });

        // fix https://github.com/atk4/ui/issues/393
        const ownerElem = stateContext[0];
        const ownerRemoveHandler = () => this.stop();
        atk.elementRemoveObserver.addHandler(ownerElem, ownerRemoveHandler);

        // prevent "The connection to http://xxx was interrupted while the page was loading." browser console warning
        const windowUnloadHandler = () => this.source.close();
        window.addEventListener('beforeunload', windowUnloadHandler);

        const intervalId = setInterval(() => {
            if (this.source.readyState === EventSource.CLOSED) {
                clearInterval(intervalId);
                atk.elementRemoveObserver.removeHandler(ownerElem, ownerRemoveHandler);
                window.removeEventListener('beforeunload', windowUnloadHandler);
            }
        }, 250);
    }

    stop() {
        const wasActive = this.source.readyState !== EventSource.CLOSED;

        this.source.close();

        if (wasActive) {
            console.warn('SSE plugin - request aborted');
        }

        if (this.settings.showLoader) {
            $(this.el).removeClass('loading');
        }
    }
}

AtkServerSentEventPlugin.DEFAULTS = {
    url: null,
    stateContext: null,
    showLoader: false,
};
