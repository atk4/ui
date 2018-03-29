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
    console.log(message);
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
          result = $('#'+response.id).replaceWith(response.html);
          if (!result.length) {
            //TODO Find a better solution for long term.
            //Need a way to gracefully abort server request.
            //when user cancel a request by selecting another request.
            console.log('Unable to replace element with id: '+ response.id);
            //throw({message:'Unable to replace element with id: '+ response.id});
          }
        }
        if (response && response.atkjs) {
          // Call evalResponse with proper context, js code and jQuery as $ var.
          apiService.evalResponse.call(this, response.atkjs.replace(/<\/?script>/g, ''), jQuery);
        }
        if (apiService.afterSuccessCallbacks.length > 0) {
          const self = this;
          let callbacks = apiService.afterSuccessCallbacks;
          callbacks.forEach(function(callback){
            apiService.evalResponse.call(self, callback, jQuery);
          });
          apiService.afterSuccessCallbacks.splice(0);
        }
      } else if (response.isServiceError) {
        // service can still throw an error
        throw ({message:response.message});
      }
    } catch (e) {
        apiService.showErrorModal(apiService.getErrorHtml(e.message));
    }
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
    if (response.hasOwnProperty('success') && !response.success) {
      if (response.hasOwnProperty('useWindow') && response.useWindow) {
        apiService.showErrorWindow(response.message)
      } else {
        apiService.showErrorModal(response.message);
      }
    } else {
      //check if we have html returned by server with <body> content.
      var body = response.match(/<body[^>]*>[\s\S]*<\/body>/gi);
      if (body) {
        apiService.showErrorModal(body);
      } else {
        apiService.showErrorModal(response);
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

  /**
   * Display App error in a separate window.
   * @param errorMsg
   */
  showErrorWindow(errorMsg) {
    var error = $('<div class="atk-exception">')
      .css({
        'padding':'8px',
        'background-color': 'rgba(0, 0, 0, 0.5)',
        'padding':'4px',
        'margin': 'auto',
        'width': '100%',
        'height': '100%',
        'position': 'fixed',
        'top': 0,
        'bottom': 0,
        'z-index': '100000',
        'overflow-y': 'scroll',
      })
      .html($('<div>')
        .css({
          'width': '70%',
          'margin-top': '4%',
          'margin-bottom': '4%',
          'margin-left': 'auto',
          'margin-right': 'auto',
          'background': 'white',
          'padding': '4px',
          'overflow-x': 'scroll'
        }).html(errorMsg)
        .prepend($('<i class="ui big close icon"></i>').css('float', 'right').click(function(){
          var $this = $(this).parents('.atk-exception');
          $this.hide();
          $this.remove();
        }))
      );
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
