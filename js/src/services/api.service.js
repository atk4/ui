import $ from 'jquery';

/* eslint-disable jsdoc/require-param-type */

/**
 * Handle Fomantic-UI API functionality throughout the app.
 */
class ApiService {
    constructor() {
        this.afterSuccessCallbacks = [];
    }

    /**
     * Setup Fomantic-UI API with this service.
     */
    setService(settings) {
        // settings.onResponse = this.handleResponse;
        settings.successTest = this.successTest;
        settings.onFailure = this.onFailure;
        settings.onSuccess = this.onSuccess;
        settings.onAbort = this.onAbort;
    }

    /**
     * Execute js code.
     * This function should be call using .call() by
     * passing proper context for 'this'.
     * ex: apiService.evalResponse.call(this, code, jQuery)
     * By passig the jQuery reference, $ var use by code that need to be eval
     * will work just fine, even if $ is not assign globally.
     *
     * @param code javascript to be eval.
     * @param $    reference to jQuery.
     */
    evalResponse(code, $) { // eslint-disable-line no-shadow
        eval(code); // eslint-disable-line no-eval
    }

    onAbort(message) {
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
    onSuccess(response, element) {
        try {
            if (response.success) {
                if (response.html && response.id) {
                    // prevent modal duplication.
                    // apiService.removeModalDuplicate(response.html);
                    const modelsContainer = $('.ui.dimmer.modals.page')[0];
                    $($.parseHTML(response.html)).find('.ui.modal[id]').each((i, e) => {
                        $(modelsContainer).find('#' + e.id).remove();
                    });

                    const result = $('#' + response.id).replaceWith(response.html);
                    if (!result.length) {
                        // TODO Find a better solution for long term.
                        // Need a way to gracefully abort server request.
                        // when user cancel a request by selecting another request.
                        console.error('Unable to replace element with id: ' + response.id);
                        // throw({message:'Unable to replace element with id: '+ response.id});
                    }
                }
                if (response.portals) {
                    // Create app portal from json response.
                    const portals = Object.keys(response.portals);
                    portals.forEach((portalID) => {
                        const m = $('.ui.dimmer.modals.page, .atk-side-panels').find('#' + portalID);
                        if (m.length === 0) {
                            $(document.body).append(response.portals[portalID].html);
                            atk.apiService.evalResponse(response.portals[portalID].js, jQuery);
                        }
                    });
                }
                if (response.atkjs) {
                    // Call evalResponse with proper context, js code and jQuery as $ var.
                    atk.apiService.evalResponse.call(this, response.atkjs, jQuery);
                }
                if (atk.apiService.afterSuccessCallbacks.length > 0) {
                    const self = this;
                    const callbacks = atk.apiService.afterSuccessCallbacks;
                    callbacks.forEach((callback) => {
                        atk.apiService.evalResponse.call(self, callback, jQuery);
                    });
                    atk.apiService.afterSuccessCallbacks.splice(0);
                }
            } else if (response.isServiceError) {
                // service can still throw an error
                // TODO fix throw without eslint disable
                throw ({ message: response.message }); // eslint-disable-line no-throw-literal
            }
        } catch (e) {
            atk.apiService.showErrorModal(atk.apiService.getErrorHtml(e.message));
        }
    }

    /**
     * Will wrap Fomantic-UI api call into a Promise.
     * Can be used to retrieve json data from the server.
     * Using this will bypass regular successTest i.e. any
     * atkjs (javascript) return from server will not be evaluated.
     *
     * Make sure to control the server output when using
     * this function. It must at least return {success: true} in order for
     * the Promise to resolve properly, will reject otherwise.
     *
     * ex: $app->terminateJson(['success' => true, 'data' => $data]);
     *
     * @param                  url      the url to fetch data
     * @param                  settings the Fomantic-UI api settings object.
     * @param                  el       the element to apply Fomantic-UI context.
     * @returns {Promise<any>}
     */
    suiFetch(url, settings = {}, el = 'body') {
        const $el = $(el);
        const apiSettings = Object.assign(settings);

        if (!('on' in apiSettings)) {
            apiSettings.on = 'now';
        }

        if (!('method' in apiSettings)) {
            apiSettings.method = 'get';
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
     * Accumulate callbacks function to run after onSuccess.
     * Callback is a string containing code to be eval.
     */
    onAfterSuccess(callback) {
        this.afterSuccessCallbacks.push(callback);
    }

    /**
     * Check server response and clear api.data object.
     * - return true will call onSuccess
     * - return false will call onFailure
     *
     * @returns {boolean}
     */
    successTest(response) {
        this.data = {};
        if (response.success) {
            return true;
        }

        return false;
    }

    /**
     * Make our own ajax request test if need to.
     * if a plugin must call $.ajax or $.getJson directly instead of Fomantic-UI api,
     * we could send the json response to this.
     */
    atkSuccessTest(response, content = null) {
        if (response.success) {
            this.onSuccess(response, content);
        } else {
            this.onFailure(response);
        }
    }

    /**
     * Handle a server response failure.
     */
    onFailure(response) {
        // if json is returned, it should contain the error within message property
        if (Object.prototype.hasOwnProperty.call(response, 'success') && !response.success) {
            atk.apiService.showErrorModal(response.message);
        } else {
            // check if we have html returned by server with <body> content.
            const body = response.match(/<body[^>]*>[\s\S]*<\/body>/gi);
            if (body) {
                atk.apiService.showErrorModal(body);
            } else {
                atk.apiService.showErrorModal(response);
            }
        }
    }

    /**
     * Display App error in a Fomantic-UI modal.
     */
    showErrorModal(errorMsg) {
        // catch application error and display them in a new modal window.
        const m = $('<div>')
            .appendTo('body')
            .addClass('ui scrolling modal')
            .css('padding', '1em')
            .html(errorMsg);
        m.modal({
            duration: 100,
            allowMultiple: false,
            onHide: function () {
                m.children().remove();

                return true;
            },
        })
            .modal('show')
            .modal('refresh');
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

const apiService = new ApiService();
Object.freeze(apiService);

export default apiService;
