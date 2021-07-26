import $ from 'jquery';

/**
 * Singleton class
 * Handle Semantic-ui api functionality throughout the app.
 */

class ApiService {
    static getInstance() {
        return this.instance;
    }

    constructor() {
        if (!this.instance) {
            this.instance = this;
            this.afterSuccessCallbacks = [];
        }
        return this.instance;
    }

    /**
   * Execute js code.
   * This function should be call using .call() by
   * passing proper context for 'this'.
   * ex: apiService.evalResponse.call(this, code, jQuery)
   * By passig the jQuery reference, $ var use by code that need to be eval
   * will work just fine, even if $ is not assign globally.
   *
   * @param code // javascript to be eval.
   * @param $ // reference to jQuery.
   */
    evalResponse(code, $) { // eslint-disable-line
        eval(code); // eslint-disable-line
    }

    /**
   * Setup semantic-ui api callback with this service.
   * @param settings
   */
    setService(settings) {
    // settings.onResponse = this.handleResponse;
        settings.successTest = this.successTest;
        settings.onFailure = this.onFailure;
        settings.onSuccess = this.onSuccess;
        settings.onAbort = this.onAbort;
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
   *
   * @param response
   * @param element
   */
    onSuccess(response, element) {
        let result;
        try {
            if (response.success) {
                if (response && response.html && response.id) {
                    // prevent modal duplication.
                    // apiService.removeModalDuplicate(response.html);
                    const modalIDs = [];
                    $(response.html).find('.ui.modal[id]').each((i, e) => {
                        modalIDs.push('#' + $(e).attr('id'));
                    });

                    if (modalIDs.length) {
                        $('.ui.dimmer.modals.page').find(modalIDs.join(', ')).remove();
                    }
                    result = $('#' + response.id).replaceWith(response.html);
                    if (!result.length) {
                        // TODO Find a better solution for long term.
                        // Need a way to gracefully abort server request.
                        // when user cancel a request by selecting another request.
                        console.error('Unable to replace element with id: ' + response.id);
                        // throw({message:'Unable to replace element with id: '+ response.id});
                    }
                }
                if (response && response.modals) {
                    // Create app modal from json response.
                    const modals = Object.keys(response.modals);
                    modals.forEach((modal) => {
                        const m = $('.ui.dimmer.modals.page').find('#' + modal);
                        if (m.length === 0) {
                            $(document.body).append(response.modals[modal].html);
                            atk.apiService.evalResponse(response.modals[modal].js, jQuery);
                        }
                    });
                }
                if (response && response.atkjs) {
                    // Call evalResponse with proper context, js code and jQuery as $ var.
                    atk.apiService.evalResponse.call(this, response.atkjs.replace(/<\/?script>/g, ''), jQuery);
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
                throw ({ message: response.message }); // eslint-disable-line
            }
        } catch (e) {
            atk.apiService.showErrorModal(atk.apiService.getErrorHtml(e.message));
        }
    }

    /**
   * Will wrap semantic ui api call into a Promise.
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
   * @param url        // the url to fetch data
   * @param settings   // the Semantic api settings object.
   * @param el         // the element to apply Semantic Ui context.
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
   *
   * @param callback
   */
    onAfterSuccess(callback) {
        this.afterSuccessCallbacks.push(callback);
    }

    /**
   * Check server response and clear api.data object.
   *  - return true will call onSuccess
   *  - return false will call onFailure
   * @param response
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
   * if a plugin must call $.ajax or $.getJson directly instead of semantic-ui api,
   * we could send the json response to this.
   * @param response
   * @param content
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
   *
   * @param response
   */
    onFailure(response) {
    // if json is returned, it should contains the error within message property
        if (Object.prototype.hasOwnProperty.call(response, 'success') && !response.success) {
            if (Object.prototype.hasOwnProperty.call(response, 'useWindow') && response.useWindow) {
                atk.apiService.showErrorWindow(response.message);
            } else {
                atk.apiService.showErrorModal(response.message);
            }
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
   * Display App error in a semantic-ui modal.
   * @param errorMsg
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

    /**
   * Display App error in a separate window.
   * @param errorMsg
   */
    showErrorWindow(errorMsg) {
        const error = $('<div class="atk-exception">')
            .css({
                padding: '8px',
                'background-color': 'rgba(0, 0, 0, 0.5)',
                margin: 'auto',
                width: '100%',
                height: '100%',
                position: 'fixed',
                top: 0,
                bottom: 0,
                'z-index': '100000',
                'overflow-y': 'scroll',
            })
            .html($('<div>')
                .css({
                    width: '70%',
                    'margin-top': '4%',
                    'margin-bottom': '4%',
                    'margin-left': 'auto',
                    'margin-right': 'auto',
                    background: 'white',
                    padding: '4px',
                    'overflow-x': 'scroll',
                }).html(errorMsg)
                .prepend($('<i class="ui big close icon"></i>').css('float', 'right').click(function () {
                    const $this = $(this).parents('.atk-exception');
                    $this.hide();
                    $this.remove();
                })));
        error.appendTo('body');
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
