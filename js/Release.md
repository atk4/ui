## Release note

### version 1.5.0

 - add new plugin scroll. This new jQuery plugin allow to use dynamic scrolling to Lister view.

### version 1.4.1

 - Modify reloadView plugin in order to accept semantic-ui api settings.

### version 1.4.0

 - add new plugin atkColumnresizer. This new jQuery plugin allow to resize table column using drag n drop,
 - fix padding setting in apiService error modal.

### version 1.3.9

  - Add option for creating modal in createModal plugin.

### version 1.3.8

  - allow jQuery FormSerializer to accept _ char at beginning of input name. ex: _e-mail
  - Check for FormSerializer to be present before extending it. 


### version 1.3.7

  - allow jQuery FormSerializer to accept dash char in input name. ex: e-mail. 

### version 1.3.6

  - Add locutus library dependency for outputing js date using php format.
    - use in Calendar.js formatter. 
  - Fix fileUpload plugin to handle click event 
    - allow to use click event after user cancel file upload open dialog using cancel button.
  - fix fileUload plugin to find button eleemnt instead of direct setup. 
    - now use this.$el.find... for multi jQuery compatibility.

### version 1.3.5

#### Changes in ModalService
  - Set top modal position value to 'absolute'
    - this fix semantic.ui 2.3.2 modal positioning problem.
  - Add esc key handler to document while modal are in service.
    - this allow to close all open modal window using esc key one after the others.  
#### Changes in createModal
  - Allow to pass a string icon value for closing icon.  

### version 1.3.4

  - Allow jsSearch to load using already set input value.
  - Allow to set input filter in jsSearch using setFilter(text) function.

### version 1.3.3

   - Add onChange event handler for hidden input in conditionalForm in order to include new Dropdown field.

### version 1.3.2

  - Prevent ajaxec from firing while it is loading.

### version 1.3.1

  - Add more generic method jsSearch::setUrlArgs(arg, value) in favor of deprecared setSortArgs method.
  
### version 1.3.0

  - add plugin, conditionalForm, to allow field to show or hide upon other field condition.
  - add formService throughout the app.  
  - Add PopupService in order to be able to load popup content dynamically via Callback.

### version 1.2.1

  - Quick fix for issue #421.

### version 1.2.0

  - add plugin, jsSortable, to allow reordering of list element via drap n drop.

### version 1.1.0

  - update reloadView plugin to accept new parameter for executing callback.
  - update apiService to execute callback after on Success has run.

### version 1.0.2

  - update uploadField plugin.
    - Allow initial value to be displayed.
  - add Autofocus to modal when using form.

### version 1.0.1

- add new function for exporting package version number;
  ```
    atk.version()
  ``` 