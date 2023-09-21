# Agile Toolkit JS package

The javascript package is necessary to run Agile Toolkit UI. It provide necessary
jQuery plugin needed for Atk4\Ui and also provide app wide services for Fomantic-UI module.

The package also export some functions via the atk global object.

### Services

All services are export via the atk global object. You can access them via atk.serviceName.
Certain functionalities are offered from these services.

For example, you can use an upload service for file uploading from your script:

```
atk.uploadService.uploadFiles(
    files,
    elem,
    { data: 'value' },
    url,
    onComplete() {}, // the callback function when upload is complete
    onXhr() {} // the callback function when uploading files is in progress
);
```

### jQuery plugin

The atk global object may be used as a quick way of registering a jQuery plugin under the atk namespace.

Let's create a new jQuery plugin that will change every selected dom element text color to green.

```
atk.registerPlugin('greenify', function (el) {
    $(el).css("color", "green");
});
```

The plugin can now by invoke using:

```
// change all link color text to green
$('a').greenify();
```

## Developing and building package.

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

This command will output the atkjs-ui.js file inside the ../public/js directory including the .map file need for debugging
the package. Once load in your page, code can be debugged in browser from the webpack source.

Any change made to the source, will also be re-compile automatically when using the "dev" script.

#### Analyzing bundle profile

Bundle profile may be analyze using various tools. npm script are available for producing
the JSON file for this.

```
npm run profile
```

This command will create a profile JSON file `atkjs-bundle-profile.json` with bundle information inside the profile folder. You can use this file with your
favorite bundle analyzer.

Another npm script is available for analyzing the bundle using the webpack-bundle-analyzer tool.

```
npm run analyze-profile
```

Note: In order to use this script, make sure that the webpack-bundle-analyzer package is install
globally.

```
npm install -g webpack-bundle-analyzer
```

### Production

For production, simply use the "build" script supply in package.json.

```
npm run build
```

This command will output the atkjs-ui.min.js file in ../public/js directory.
