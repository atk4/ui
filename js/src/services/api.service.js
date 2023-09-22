import $ from 'external/jquery';
import atk from 'atk';

/**
 * Handle Fomantic-UI API functionality throughout the app.
 */
class ApiService {
    constructor() {
        this.afterSuccessCallbacks = [];
    }

    getDefaultFomanticSettings() {
        return [
            {},
            {
                // override supported via "../setup-fomantic-ui.js", both callbacks are always evaluated
                successTest: this.successTest,
                onFailure: this.onFailure,
                onSuccess: this.onSuccess,
                onAbort: this.onAbort,
                onError: this.onError,
            },
        ];
    }

    /**
     * Execute JS code.
     *
     * This function should be called using .call() by passing proper context for 'this'.
     * ex: apiService.evalResponse.call(this, code)
     *
     * @param {string} code
     */
    evalResponse(code) {
        eval(code); // eslint-disable-line no-eval
    }

    /**
     * Check server response.
     *
     * @returns {boolean}
     */
    successTest(response) {
        if (response.success) {
            return true;
        }

        return false;
    }

    onAbort(message) {
        console.warn(message);
    }

    onError(message) {
        console.warn(message);
    }

    /**
     * Handle a server response success
     * If successTest return true, then this function is call;
     * Within this function "this" is place in proper context
     * and allow us to properly eval the response.
     * Furthermore, the dom element responsible of the api call is returned if needed.
     *
     * Change in response object property from eval to atkjs.
     * Under certain circumstance, response.eval was run and execute prior to onSuccess eval,
     * thus causing some code to be running twice.
     * To avoid conflict, property name in response was change from eval to atkjs.
     * Which mean response.atkjs now contains code to be eval.
     */
    onSuccess(response) {
        try {
            if (response.success) {
                if (response.html && response.id) {
                    // prevent modal duplication
                    // apiService.removeModalDuplicate(response.html);
                    const modelsContainer = $('.ui.dimmer.modals.page')[0];
                    $($.parseHTML(response.html)).find('.ui.modal[id]').each((i, e) => {
                        $(modelsContainer).find('#' + e.id).remove();
                    });

                    const result = $('#' + response.id).replaceWith(response.html);
                    if (result.length === 0) {
                        // TODO find a better solution for long term
                        // need a way to gracefully abort server request
                        // when user cancel a request by selecting another request
                        console.error('Unable to replace element with id: ' + response.id);
                        // throw Error('Unable to replace element with id: ' + response.id);
                    }
                }
                if (response.atkjs) {
                    atk.apiService.evalResponse.call(this, response.atkjs);
                }
                if (atk.apiService.afterSuccessCallbacks.length > 0) {
                    const callbacks = atk.apiService.afterSuccessCallbacks;
                    for (const callback of callbacks) {
                        atk.apiService.evalResponse.call(this, callback);
                    }
                    atk.apiService.afterSuccessCallbacks.splice(0);
                }
            } else if (response.isServiceError) {
                throw new Error(response.message);
            }
        } catch (e) {
            atk.apiService.showErrorModal(atk.apiService.getErrorHtml(e.message));
        }
    }

    /**
     * Accumulate callbacks function to run after onSuccess.
     * Callback is a string containing code to be eval.
     */
    onAfterSuccess(callback) {
        this.afterSuccessCallbacks.push(callback);
    }

    /**
     * Handle a server response failure.
     */
    onFailure(response) {
        // if JSON is returned, it should contain the error within message property
        if (Object.prototype.hasOwnProperty.call(response, 'success') && !response.success) {
            atk.apiService.showErrorModal(response.message);
        } else {
            // check if we have HTML returned by server with <body> content
            // TODO test together /w onError using non-200 HTTP AJAX response code
            const body = response.match(/<body[^>]*>[\S\s]*<\/body>/gi);
            if (body) {
                atk.apiService.showErrorModal(body);
            } else {
                atk.apiService.showErrorModal(response);
            }
        }
    }

    /**
     * Make our own ajax request test if need to.
     * if a plugin must call $.ajax or $.getJson directly instead of Fomantic-UI api,
     * we could send the JSON response to this.
     */
    atkProcessExternalResponse(response, content = null) {
        if (response.success) {
            this.onSuccess(response, content);
        } else {
            this.onFailure(response);
        }
    }

    /**
     * Will wrap Fomantic-UI api call into a Promise.
     * Can be used to retrieve JSON data from the server.
     * Using this will bypass regular successTest i.e. any
     * atkjs (javascript) return from server will not be evaluated.
     *
     * Make sure to control the server output when using
     * this function. It must at least return { success: true } in order for
     * the Promise to resolve properly, will reject otherwise.
     *
     * ex: $app->terminateJson(['success' => true, 'data' => $data]);
     *
     * @param   {string}       url      the URL to fetch data
     * @param   {object}       settings the Fomantic-UI api settings object.
     *
     * @returns {Promise<any>}
     */
    suiFetch(url, settings = {}, el = 'body') {
        const $el = $(el);
        const apiSettings = Object.assign(settings);

        if (!('on' in apiSettings)) {
            apiSettings.on = 'now';
        }

        if (!('method' in apiSettings)) {
            apiSettings.method = 'GET';
        }

        apiSettings.url = url;

        return new Promise((resolve, reject) => {
            apiSettings.onFailure = function (r) {
                atk.apiService.onFailure(r);
                reject(r);
            };
            apiSettings.onSuccess = function (r, e) {
                resolve(r);
            };
            $el.api(apiSettings);
        });
    }

    /**
     * Display App error in a Fomantic-UI modal.
     */
    showErrorModal(errorMsg) {
        if (atk.modalService.modals.length > 0) {
            const $modal = $(atk.modalService.modals.at(-1));
            if ($modal.data('closeOnLoadingError')) {
                $modal.removeData('closeOnLoadingError').modal('hide');
            }
        }

        // catch application error and display them in a new modal window
        const m = $('<div>')
            .appendTo('body')
            .addClass('ui scrolling modal')
            .css('padding', '1em')
            .html(errorMsg);
        m.data('needRemove', true).modal().modal('show');
    }

    getErrorHtml(error) {
        return `<div class="ui negative icon message">
                <i class="warning sign icon"></i>
                <div class="content">
                  <div class="header">Javascript Error</div>
                  <div>${error}</div>
                </div>
              </div>`;
    }
}

export default Object.freeze(new ApiService());
