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
     * @param code //javascript to be eval.
     * @param $  // reference to jQuery.
     */
    evalResponse(code, $) {
        eval(code);
    }


    /**
     * Setup semantic-ui api callback with this service.
     * @param settings
     */
    setService(settings) {
        //settings.onResponse = this.handleResponse;
        settings.successTest = this.successTest;
        settings.onFailure = this.onFailure;
        settings.onSuccess = this.onSuccess;
        settings.onAbort = this.onAbort;
    }

    onAbort(message) {
        alert(message);
    }
    /**
     * Handle a server response success
     * If successTest return true, then this function is call;
     * Within this function this is place in proper context
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
                    result = $('#'+response.id).replaceWith(response.html);
                    if (!result.length) {
                        throw({message:'Unable to replace element with id: '+ response.id});
                    }
                }
                if (response && response.atkjs) {
                    // Call evalResponse with proper context, js code and jQuery as $ var.
                    apiService.evalResponse.call(this, response.atkjs.replace(/<\/?script>/g, ''), jQuery);
                }
            } else if (response.isServiceError) {
                // service can still throw an error
                throw ({message:response.message});
            }
        } catch (e) {
            alert('Error in ajax replace or atkjs:\n' + e.message);
        }
    }

    /**
     * Check server response
     *  - return true will call onSuccess
     *  - return false will call onFailure
     * @param response
     * @returns {boolean}
     */
    successTest(response) {
        if (response.success) {
            this.data = {};
            return true;
        } else {
            return false;
        }
    }

    /**
     * Make our own ajax request test if need to.
     * if a plugin must call $.ajax or $.getJson directly instead of semantic-ui api,
     * we could send the json response to this.
     * @param response
     * @param content
     */
    atkSuccessTest(response, content) {
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
        if (!response.success) {
            apiService.showErrorModal(response.message);
        } else {
            var w = window.open(null,'Error in JSON response','height=1000,width=1100,location=no,menubar=no,scrollbars=yes,status=no,titlebar=no,toolbar=no');
            if(w){
                w.document.write('<h5>Error in JSON response</h5>');
                w.document.write(response);
                w.document.write('<center><input type=button onclick="window.close()" value="Close"></center>');
            }else{
                alert("Error in ajaxec response"+response);
            }
        }

    }

    /**
     * Display App error in a semantic-ui modal.
     * @param errorMsg
     */
    showErrorModal(errorMsg) {
        //catch application error and display them in a new modal window.
        var m = $("<div>")
            .appendTo('body')
            .addClass('ui scrolling modal')
            .css('padding', '1em')
            .html(errorMsg);
        m.modal({
            duration: 100,
            allowMultiple: false,
            onHide: function() {
                m.children().remove();
                return true;
            }
        })
            .modal('show')
            .modal('refresh');
    }

}

const apiService = new ApiService();
Object.freeze(apiService);

export default apiService;
