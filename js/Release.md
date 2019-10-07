## Release note

### version 1.10.0 (2019-10-07)

- Add new component TreeItemSelector.

### version 1.9.0 (2019-08-16)

- Add textarea support in Multiline Vue Component
- Add input options support in Multiline Vue Component

- Include semantic-ui-vue in bundle.
    - atkjs-ui.min.js bundle file now include semantic-ui-vue package so there is no need to explicitly load it.

### version 1.8.0 (2019-07-23)

- Multiline Vue component now support containsMany / containsOne.
- Multiline Vue component now support limiting number of row data.
- Multiline Vue component now support Dropdown as a field type.

### version 1.7.0

- New atkConfirm plugin. Will display a user confirmaton dialog using fomantic ui modal.

### version 1.6.8

- Babel configuration.
    - fix core.js warning during build. 
- create-modal.plugin
    - fix: now allow to pass css class name to create modal. 
    Useful when need to create modal with full width for example.
- file-upload.plugin
    - remove support for opening file dialog when input get focus. Because File dialog
    will remove blur and focus input field again, this was causing the file dialog to open
    multiple time. Now only open when input or button is clicked.

### version 1.6.7
   
- plugin.js
   - fix: Prevent plugin from being create if a plugin method is called prior to the plugin
   instantiation. Was causing unexpected result when plugin was create using a method call, since
   there was no setting. 
- atkJsSearch plugin
  - Now support initial display setup value.
  - Add support for reloading page via window.location and not using ajax. 
- url.helpers, previously add-param.helpers
  - Add new plugin function: atkRemoveParam. 
  You can use this function for removing an url query parameter from an url string.
  ex: $.atkRemoveParam('test.php?sort=2&id=3', 'sort') will output:  test.php?id=3
   

### version 1.6.6

 - FormService changed: Add ability to prevent user from leaving page when form is not submit.
 - Fix conditional-form plugin file name.

### version 1.6.5

 - Add multiline component for MultiLine form field.

### version 1.6.4

 - Add directives to Vue component;
 - Allow uses of Semantic Ui Vue;
 - Update packages version;
 - Configure babel in order to use Async - Await
 - New suiFetch() method for server callback in api service.

### version 1.6.3

 - Prevent Modal duplication in apiService.

### version 1.6.2

 - Refactored js file name;
 - add Vue js service for creating Vue component;
 - Add some vue component:
    - inline-edit;
    - item-search;

### version 1.6.1

- fix/notify plugin for fomantic v.2.7.2
  This version require notify element to have transition hidden set.

### version 1.6.0

Maintenance release.
 - upgrade webpack to verson 4 (4.26.1);
 - upgrade babel to version 7;
 - upgrade eslint to version 5;
 - remove unneccessary dependencies;
 - add package-lock file for newer npm release;
 - update webpack configuration to newer version;
 - remove old babel preset env dependency;
 - remove all vulnerabilities issue on npm install;

### version 1.5.0

 - add new plugin scroll. This new jQuery plugin allow to use dynamic scrolling to Lister, Table, Grid views.

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