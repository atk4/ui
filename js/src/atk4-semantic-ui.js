/**
 * Handle api server response.
 */

(function ($, window, document, undefined) {

    $.fn.api.settings.onResponse = function (response) {
        if (response.success) {
            try {
                if (response && response.html && response.id) {
                    var result = $('#'+response.id).replaceWith(response.html);
                    if (!result.length) {
                        throw({message:'Unable to replace element with id: '+ response.id});
                    }
                }
                if (response && response.eval) {
                    var result = function(){ eval(response.eval.replace(/<\/?script>/g, '')); }.call(this.obj);
                }
                return {success:true};
            } catch (e) {
                //send our eval or replaceWith error to successTest
                return {success:false, error: 'Error in ajax replace or eval:\n' + e.message };
            }
        } else {
            //catch application error and display them in a new modal window.
            var m = $("<div>")
                     .appendTo('body')
                     .addClass('ui scrolling modal')
                     .css('padding', '1em')
                     .html(response.message);
            m.modal({
                duration: 100,
                onHide: function() {
                  m.children().remove();
                  return true;
                }
              })
              .modal('show')
              .modal('refresh');
        }
    }

    $.fn.api.settings.successTest = function(response) {
        if(response.success){
            this.data = {};
            return true;
        } else if (response.error) {
            alert(response.error);
            return true;
        } else {
            return false;
        }
    }

    $.fn.api.settings.onFailure = function(response) {
        var w = window.open(null,'Error in JSON response','height=1000,width=1100,location=no,menubar=no,scrollbars=yes,status=no,titlebar=no,toolbar=no');
        if(w){
            w.document.write('<h5>Error in JSON response</h5>');
            w.document.write(response);
            w.document.write('<center><input type=button onclick="window.close()" value="Close"></center>');
        }else{
            alert("Error in ajaxec response"+response);
        }
    }

}) (jQuery, window, document);

