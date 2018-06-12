# Agile Toolkit js package

The javascript package is necessary to run Agile Toolkit UI. It provide necessary
jQuery plugin needed for atk4\ui and also provide app wide services for semantic-ui module.

The package also export some functions via the atk global object.

### Getting package version

Calling this function in your custom js script or in console will output the package version number.
```
    atk.version();
```

### Services 

All services are export via the atk global object. You can access them via atk.serviceName.
Certain functionalities are offered from these services. 

For example, if one of your script need to send an ajax request directly, without using semantic-ui api request, you could use the apiService.atkSuccessTest
to run and evaluate the server response from atk4\ui.

```
    $.getJSON( "myajax.php", function( resp ) {
      atk.apiService.atkSuccessTest(resp);
     });

```

Another example would be the upload service for file uploading using one of your script.

```
    atk.uploadService.uploadFiles(
      filesArraytoUpload,
      theElement,
      {data: 'value'},
      url,             
      onComplete(){}, //the callback function when upload is complete.
      onXhr(){}       //the callback function when uploading files is in progress.
    );
```

### jQuery plugin

The atk global object may be used as a quick way of registering a jQuery plugin under the atk namespace.

Let's create a new jQuery plugin that will change every selected dom element text color to green.

```
    atk.registerPlugin('Greenify', function(el) {
        $(el).css("color", "green");
    })
```

The plugin can now by invoke using:

```
    //Change all link color text to green.
    $('a').atkGreenify();
```

## Developping and building package.

You may change this package to suit your own needs.

### Package installation

First start by installing the package using npm install. 

```
    cd atk4/ui/js
    npm install
```

### Development

For development and debugging, simply use the "dev" script supply in package.json file by running this command:

```
    npm run dev
```

This command will output the atkjs-ui.js file inside the /public directory including the .map file need for debugging
the package. Once load in your page, code can be debugged in browser from the webpack source.

Any change made to the source, will also be re-compile automatically when using the "dev" script.

### Production

For production, simply use the "build" script supply in package.json.

```
    npm run build
```

This command will output the atkjs-ui.min.js file, also in /public folder. 

## Release note

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