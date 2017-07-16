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
     * If need, some data are set in the element, inlude into the api call, prior to the call.
     * This is the case for modal dialog that need to replace specific element content with html
     * returned by the server without the proper id being set as usual.
     * In case of modal, for example, the data 'isModal' is set to true and prior to be pass with the api call is set to true.
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

                if (response && response.eval) {
                    eval(response.eval.replace(/<\/?script>/g, ''));
                }
            } else {
                // other service can still throw an error
                throw ({message:response.message});
            }
        } catch (e) {
            alert('Error in ajax replace or eval:\n' + e.message);
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
