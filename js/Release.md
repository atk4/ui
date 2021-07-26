## Release note

### version 1.19.4 (2021-07-11)

- Remove "debounceTimeout" atk option.

### version 1.19.3 (2021-07-09)

- Update package dependencies.

### version 1.19.2 (2021-02-05)

- Fix issue in js-search plugin when not using ajax request.
- Add missing popupService to atk object.

### version 1.19.1 (2021-01-05)

- Update package dependency

### version 1.19.0 (2020-12-18)

- Multiline component
  - add support for Lookup;
  - refactor entire component;

### version 1.18.0 (2020-11-25)

- Add atk-lookup share component for query-builder.

### version 1.17.0 (2020-11-10)

- atk-date-picker use vue-flatpickr-component instead of v-date-picker;
- enable time and datetime in query builder;
- enable time and datetime in multiline;

### version 1.16.4 (2020-11-09)

- adding textarea form control to be detect by form leave when set.
  see https://github.com/atk4/ui/issues/1527
- Refactor arrow function.

### version 1.16.3 (2020-09-30)

- create atk-date-picker vue component.
- Update query-builder
    - allow date picker customization;

- add atk-date-picker to multiline component.


### version 1.16.2 (2020-09-23)
- VueService
    - Add possibility to check if all components on page are load.
    - Add Loading and Error component for dynamic component.
- Rename window var bundlePublicPath to __atkBundlePublicPath

### version 1.16.1 (2020-09-17)
- set js bundle path dynamically for loading ressource.

### version 1.16.0 (2020-09-16)

- Split Vue component into chunk files.
    - each vue component is now load dynamically as needed.

### version 1.15.4 (2020-09-08)

- add atk eventBus for listening and publishing event.
    - replace deprecated Vue eventBus;
- create atk-utils file for options and eventBus.
- remove Vue component deprecated inline-template in v3.

### version 1.15.3 (2020-08-27)

 - set proper debounce value in:
    - item-search vue component
    - condition-form plugin
    - panel service
 - Fix issue with Querybuilder date.
   - https://github.com/atk4/ui/issues/1420
 - Package
    - update package dependencies to latest release;
        - note: css-loader v4 now require style-loader

  PR: https://github.com/atk4/ui/pull/1430

### version 1.15.2 (2020-08-19)

 - sidenav.plugin.js
    - fix issue (1406) when using Control or Command click on a link (https://github.com/atk4/ui/issues/1406 )

 - Package
    - update package dependencies to latest release;
    - fix eslint;

### version 1.15.1 (2020-08-19)
 - URL query parameter for Upload plugin/control was renamed from "action" to "f_upload_action"
 - URL query parameter for Upload plugin/control was renamed from "f_name" to "f_upload_id"

### version 1.15.0 (2020-07-16)
 - update package dependency
 - configure webpack in order to load and bundle .vue file.
    - now use terser plugin instead of uglify js for minimizing js.
 - add query builder component for ScopeBuilder form field
   - extends Vue Query Builder default to work with Fomantic ui.

### version 1.14.3 (2020-07-13)
 - Update dependencies.

### version 1.14.2 (2020-05-04)
 - Build releases automatically on ui.agiletoolkit.org deploy

### version 1.14.1 (2020-04-25)

- add stop function to server-event.plugin;
    You can now explicitly stop event.
    Add option to close event on window beforeUnload

### version 1.14.0 (2020-04-21)

- Add a jQuery plugin for layout that support side navigation.
  - sidenav.plugin.js

### version 1.13.0 (2020-04-14)

- New PanelService.

### version 1.12.8 (2020-04-14)

- Import corejs into package for polyfill requirement on older browser.

### version 1.12.7 (2020-04-08)

- URL query parameter to enable SSE response was renamed from "event=sse" to "__atk_sse".

### version 1.12.6 (2020-04-03)

- URL query parameter to force JSON response was renamed from "json" to "__atk_json".

### version 1.12.5 (2020-03-24)

- make sure $ var is assign to jQuery ($ = jQuery) in all module by adding missing import statement.

### version 1.12.4 (2020-03-18)

- Update dependencie package
    - @babel/cli from 7.8.3 to 7.8.7
    - @bable/core from 7.8.3 to 7.8.7
    - @babel/preset-env from 7.8.3 to 7.8.7
    - @babel/polyfill from 7.8.3 to 7.8.7
    - webpack from 4.35.3 to 4.42.0
- Update Babel config in order to use corejs v3

### version 1.12.3 (2020-02-11)

- Fix duplicate url encoding for reload-view and ajaxec plugins.
   see https://github.com/atk4/ui/issues/924

### version 1.12.2 (2020-02-04)

- Fix https://github.com/atk4/ui/issues/902
   Throwing error on server was not clearing api.data object, thus keeping
   old value with new server request. This fix make sure api.data gets clear
   on all server response, successful or not.

### version 1.12.1 (2020-01-14)

- update api service to generate modals in json request.
- update npm dependencies.

### version 1.12.0 (2019-11-06)

- add table-dropdown-helper.js
    Utlity to dipslay Fomantic ui drodown as a table column.

### version 1.11.0 (2019-10-24)

- Add DataService.
    Enable usage of web storage: local or session.
- Update atk.reloadView jQuery plugin.
    Can include web storage in api request,
    Possible to set specific Fomantic api settings
- Update atk.ajaxExec jQuery plugin.
    Can include web storage in api request,
    Possible to set specific Fomantic api settings

- Update url.helpers

Fix
- issue #487 - Avoid Multi modal
  Update modal service in order to refresh current modal when top modal is close.

### version 1.10.0 (2019-10-07)
- Add new component TreeItemSelector.


### version 1.9.1 (2019-10-06)

- fix issue where new row is added pressing Enter key in form.
- Add new option in order to add row automatically when tabbing out of
the last column and last row.
- Add ability to execute javascript when adding or deleting a row.

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

 - Add multiline component for MultiLine form control.

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

  - Allow JsSearch to load using already set input value.
  - Allow to set input filter in JsSearch using setFilter(text) function.

### version 1.3.3

   - Add onChange event handler for hidden input in conditionalForm in order to include new Dropdown field.

### version 1.3.2

  - Prevent ajaxec from firing while it is loading.

### version 1.3.1

  - Add more generic method JsSearch::setUrlArgs(arg, value) in favor of deprecared setSortArgs method.

### version 1.3.0

  - add plugin, conditionalForm, to allow field to show or hide upon other field condition.
  - add formService throughout the app.
  - Add PopupService in order to be able to load popup content dynamically via Callback.

### version 1.2.1

  - Quick fix for issue #421.

### version 1.2.0

  - add plugin, JsSortable, to allow reordering of list element via drap n drop.

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
