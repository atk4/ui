import $ from 'external/jquery';
import atk from 'atk';
import lodashEscape from 'lodash/escape';

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
     * @param {object} thisObject
     * @param {string} code
     */
    evalJsCode(thisObject, code) {
        (function () {
            eval('\'use strict\'; (() => {' + code + '})()'); // eslint-disable-line no-eval
        }).call(thisObject);
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
                    const $target = $('#' + response.id);
                    if ($target.length !== 1) {
                        throw new Error('Target DOM element not found');
                    }

                    let responseBody = new DOMParser().parseFromString('<body>' + response.html.trim() + '</body>', 'text/html').body;
                    const responseElement = responseBody.childNodes[0];
                    if (responseBody.childNodes.length !== 1 || responseElement.id !== response.id) {
                        throw new Error('Unexpected HTML response');
                    }
                    responseBody = null;

                    // prevent modal duplication
                    const $modalsContainers = $('body > .ui.dimmer.modals.page, body > .atk-side-panels');
                    $(responseElement).find('.ui.modal[id], .atk-right-panel[id]').each((i, e) => {
                        $modalsContainers.find('#' + e.id).remove();
                    });

                    if ($target.hasClass('ui modal') || $target.hasClass('atk-right-panel')) {
                        $.each([...$target[0].childNodes], (i, node) => {
                            if (node instanceof Element && node.classList.contains('ui') && node.classList.contains('dimmer')) {
                                return;
                            }

                            $(node).remove();
                        });
                        $.each([...responseElement.childNodes], (i, node) => {
                            if (node instanceof Element && node.classList.contains('ui') && node.classList.contains('dimmer')) {
                                return;
                            }

                            $target.append(node);
                        });
                    } else {
                        $target.replaceWith(response.html);
                    }
                }

                if (response.atkjs) {
                    atk.apiService.evalJsCode(this, response.atkjs);
                }

                if (atk.apiService.afterSuccessCallbacks.length > 0) {
                    const callbacks = atk.apiService.afterSuccessCallbacks;
                    for (const callback of callbacks) {
                        atk.apiService.evalJsCode(this, callback);
                    }
                    atk.apiService.afterSuccessCallbacks.splice(0);
                }
            } else if (response.isServiceError) {
                throw new Error(response.message);
            }
        } catch (e) {
            atk.apiService.showErrorModal(atk.apiService.getErrorHtml('API JavaScript Error', e.message));
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
        // if JSON is returned, it should contain the HTML error in message property
        if (Object.prototype.hasOwnProperty.call(response, 'success') && !response.success) {
            atk.apiService.showErrorModal(response.message);
        } else {
            atk.apiService.showErrorModal(
                atk.apiService.getErrorHtml('API Server Error', '')
                    + '<div><pre style="margin-bottom: 0px;"><code style="display: block; padding: 1em; color: #adbac7; background: #22272e;">'
                    + lodashEscape(response)
                    + '</code></pre></div>'
            );
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
     * atkjs (JavaScript) return from server will not be evaluated.
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
    showErrorModal(contentHtml) {
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
            .html(contentHtml);
        m.data('needRemove', true).modal().modal('show');
    }

    getErrorHtml(titleHtml, messageHtml) {
        return `<div class="ui negative icon message" style="margin: 0px;">
              <i class="warning sign icon"></i>
              <div class="content">
                <div class="header">${titleHtml}</div>
                <div>${messageHtml}</div>
              </div>
            </div>`;
    }
}

export default Object.freeze(new ApiService());
