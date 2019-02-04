(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("jQuery"));
	else if(typeof define === 'function' && define.amd)
		define("atk", ["jQuery"], factory);
	else if(typeof exports === 'object')
		exports["atk"] = factory(require("jQuery"));
	else
		root["atk"] = factory(root["jQuery"]);
})(this, function(__WEBPACK_EXTERNAL_MODULE_0__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 15);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_0__;

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Base implementation of jQuery plugin in Agile Toolkit.
 *
 */

var atkPlugin = function () {

  /**
   * Default plugin constructor
   * @param element
   * @param options
   * @returns {atkPlugin}
   */
  function atkPlugin(element, options) {
    _classCallCheck(this, atkPlugin);

    this.$el = (0, _jquery2.default)(element);
    this.settings = options;
    this.main();
  }

  /**
   * The main plugin method. This is the method call by default
   * when invoking the plugin on a jQuery element.
   * $(selector).pluginName({});
   * The plugin should normally override this class.
   */


  _createClass(atkPlugin, [{
    key: 'main',
    value: function main() {}

    /**
     * Call a plugin method via the initializer function.
     * Simply call the method like: $(selector).pluginName('method', [arg1, arg2])
     *
     * @param fn : string representing the method name to execute.
     * @param args : array of arguments need for the method to execute.
     * @returns {*}
     */

  }, {
    key: 'call',
    value: function call(fn, args) {
      return this[fn].apply(this, _toConsumableArray(args));
    }
  }]);

  return atkPlugin;
}();

exports.default = atkPlugin;
module.exports = exports.default;

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Singleton class
 * Handle Semantic-ui api functionality throughout the app.
 */

var ApiService = function () {
  _createClass(ApiService, null, [{
    key: 'getInstance',
    value: function getInstance() {
      return this.instance;
    }
  }]);

  function ApiService() {
    _classCallCheck(this, ApiService);

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


  _createClass(ApiService, [{
    key: 'evalResponse',
    value: function evalResponse(code, $) {
      eval(code);
    }

    /**
     * Setup semantic-ui api callback with this service.
     * @param settings
     */

  }, {
    key: 'setService',
    value: function setService(settings) {
      //settings.onResponse = this.handleResponse;
      settings.successTest = this.successTest;
      settings.onFailure = this.onFailure;
      settings.onSuccess = this.onSuccess;
      settings.onAbort = this.onAbort;
    }
  }, {
    key: 'onAbort',
    value: function onAbort(message) {
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

  }, {
    key: 'onSuccess',
    value: function onSuccess(response, element) {
      var result = void 0;
      try {
        if (response.success) {
          if (response && response.html && response.id) {
            result = (0, _jquery2.default)('#' + response.id).replaceWith(response.html);
            if (!result.length) {
              throw { message: 'Unable to replace element with id: ' + response.id };
            }
          }
          if (response && response.atkjs) {
            // Call evalResponse with proper context, js code and jQuery as $ var.
            apiService.evalResponse.call(this, response.atkjs.replace(/<\/?script>/g, ''), jQuery);
          }
        } else if (response.isServiceError) {
          // service can still throw an error
          throw { message: response.message };
        }
      } catch (e) {
        apiService.showErrorModal(apiService.getErrorHtml(e.message));
      }
    }

    /**
     * Check server response
     *  - return true will call onSuccess
     *  - return false will call onFailure
     * @param response
     * @returns {boolean}
     */

  }, {
    key: 'successTest',
    value: function successTest(response) {
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

  }, {
    key: 'atkSuccessTest',
    value: function atkSuccessTest(response) {
      var content = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

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

  }, {
    key: 'onFailure',
    value: function onFailure(response) {
      // if json is returned, it should contains the error within message property
      if (response.hasOwnProperty('success') && !response.success) {
        if (response.hasOwnProperty('useWindow') && response.useWindow) {
          apiService.showErrorWindow(response.message);
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

  }, {
    key: 'showErrorModal',
    value: function showErrorModal(errorMsg) {
      //catch application error and display them in a new modal window.
      var m = (0, _jquery2.default)("<div>").appendTo('body').addClass('ui scrolling modal').css('padding', '1em').html(errorMsg);
      m.modal({
        duration: 100,
        allowMultiple: false,
        onHide: function onHide() {
          m.children().remove();
          return true;
        }
      }).modal('show').modal('refresh');
    }

    /**
     * Display App error in a separate window.
     * @param errorMsg
     */

  }, {
    key: 'showErrorWindow',
    value: function showErrorWindow(errorMsg) {
      var _$$css;

      var error = (0, _jquery2.default)('<div class="atk-exception">').css((_$$css = {
        'padding': '8px',
        'background-color': 'rgba(0, 0, 0, 0.5)'
      }, _defineProperty(_$$css, 'padding', '4px'), _defineProperty(_$$css, 'margin', 'auto'), _defineProperty(_$$css, 'width', '100%'), _defineProperty(_$$css, 'height', '100%'), _defineProperty(_$$css, 'position', 'fixed'), _defineProperty(_$$css, 'top', 0), _defineProperty(_$$css, 'bottom', 0), _defineProperty(_$$css, 'z-index', '100000'), _defineProperty(_$$css, 'overflow-y', 'scroll'), _$$css)).html((0, _jquery2.default)('<div>').css({
        'width': '70%',
        'margin-top': '4%',
        'margin-bottom': '4%',
        'margin-left': 'auto',
        'margin-right': 'auto',
        'background': 'white',
        'padding': '4px',
        'overflow-x': 'scroll'
      }).html(errorMsg).prepend((0, _jquery2.default)('<i class="ui big close icon"></i>').css('float', 'right').click(function () {
        var $this = (0, _jquery2.default)(this).parents('.atk-exception');
        $this.hide();
        $this.remove();
      })));
      error.appendTo('body');
    }
  }, {
    key: 'getErrorHtml',
    value: function getErrorHtml(error) {
      return '<div class="ui negative icon message">\n                <i class="warning sign icon"></i>\n                <div class="content">\n                  <div class="header">Javascript Error</div>\n                  <div>' + error + '</div>\n                </div>\n              </div>';
    }
  }]);

  return ApiService;
}();

var apiService = new ApiService();
Object.freeze(apiService);

exports.default = apiService;
module.exports = exports.default;

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Singleton class
 * Allow to upload files to server via semantic-ui api.
 */
var UploadService = function () {
  _createClass(UploadService, null, [{
    key: 'getInstance',
    value: function getInstance() {
      return this.instance;
    }
  }]);

  function UploadService() {
    _classCallCheck(this, UploadService);

    if (!UploadService.instance) {
      UploadService.instance = this;
    }
    return UploadService.instance;
  }

  /**
   *  Will upload a FileList object to server.
   *  Each file in FileList will be include in formData as
   *  'file-(number)' param, except for the first one which will
   *  be set to 'file' only.
   *
   * @param files         A FileList object.
   * @param el            the jQuery element to attach to semantic api.
   * @param data          Extra data to pass with files.
   * @param uploadUrl     the url that handle upload.
   * @param completeCb    the callback for semantic-ui api.onComplete.
   * @param xhrCb         the xhr function to pass to server.
   */


  _createClass(UploadService, [{
    key: 'uploadFiles',
    value: function uploadFiles(files, el) {
      var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
      var uploadUrl = arguments[3];
      var completeCb = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : function (r, c) {};
      var xhrCb = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : function () {
        return new window.XMLHttpRequest();
      };


      var formData = new FormData();

      for (var i = 0; i < files.length; i++) {
        var param = i === 0 ? 'file' : 'file-' + i;
        formData.append(param, files.item(i));
      }

      if (!_jquery2.default.isEmptyObject(data)) {
        _jquery2.default.each(data, function (key, el) {
          formData.append(key, el);
        });
      }

      el.api({
        on: 'now',
        url: uploadUrl,
        cache: false,
        processData: false,
        contentType: false,
        data: formData,
        method: 'POST',
        obj: this.$el,
        xhr: xhrCb,
        onComplete: completeCb
      });
    }
  }]);

  return UploadService;
}();

var uploadService = new UploadService();
Object.freeze(uploadService);

exports.default = uploadService;
module.exports = exports.default;

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

var _ApiService = __webpack_require__(2);

var _ApiService2 = _interopRequireDefault(_ApiService);

var _ModalService = __webpack_require__(14);

var _ModalService2 = _interopRequireDefault(_ModalService);

var _UploadService = __webpack_require__(3);

var _UploadService2 = _interopRequireDefault(_UploadService);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// setup app service for semantic-ui
_ApiService2.default.setService(_jquery2.default.fn.api.settings);
_ModalService2.default.setModals(_jquery2.default.fn.modal.settings);

var atkSemantic = {
  uploadService: _UploadService2.default,
  apiService: _ApiService2.default,
  modalService: _ModalService2.default
};

module.exports = atkSemantic;

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
// Add atk namespace to jQuery global space.

(function ($) {
    if (!$.atk) {
        $.atk = new Object();
    };

    $.atk['addParams'] = function (url, data) {
        if (!$.isEmptyObject(data)) {
            url += (url.indexOf('?') >= 0 ? '&' : '?') + $.param(data);
        }

        return url;
    };
})(jQuery);

exports.default = function ($) {
    $.atkAddParams = $.atk.addParams;
}(jQuery);

module.exports = exports.default;

/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

exports.default = plugin;

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

var _spinner = __webpack_require__(13);

var _spinner2 = _interopRequireDefault(_spinner);

var _serverEvent = __webpack_require__(12);

var _serverEvent2 = _interopRequireDefault(_serverEvent);

var _reloadView = __webpack_require__(11);

var _reloadView2 = _interopRequireDefault(_reloadView);

var _ajaxec = __webpack_require__(7);

var _ajaxec2 = _interopRequireDefault(_ajaxec);

var _createModal = __webpack_require__(8);

var _createModal2 = _interopRequireDefault(_createModal);

var _notify = __webpack_require__(10);

var _notify2 = _interopRequireDefault(_notify);

var _fileUpload = __webpack_require__(9);

var _fileUpload2 = _interopRequireDefault(_fileUpload);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Generate a jQuery plugin
 * @param name [string] Plugin name
 * @param className [object] Class of the plugin
 * @param shortHand [bool] Generate a shorthand as $.pluginName
 *
 * @example
 * import plugin from 'plugin';
 *
 * class MyPlugin {
 *     constructor(element, options) {
 *         // ...
 *     }
 * }
 *
 * MyPlugin.DEFAULTS = {};
 *
 * plugin('myPlugin', MyPlugin);
 *
 * credit : https://gist.github.com/monkeymonk/c08cb040431f89f99928132ca221d647
 *
 * import $ from 'jquery' will bind '$' var to jQuery var without '$' var conflicting with other library
 * in final webpack output.
 */

//Create plugins.
plugin('Spinner', _spinner2.default);
plugin('ReloadView', _reloadView2.default);
plugin('Ajaxec', _ajaxec2.default);
plugin('CreateModal', _createModal2.default);
plugin('Notify', _notify2.default, true);
plugin('ServerEvent', _serverEvent2.default, true);
plugin('FileUpload', _fileUpload2.default);

function plugin(name, className) {
    var shortHand = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

    // Add atk namespace to jQuery global space.
    if (!_jquery2.default.atk) {
        _jquery2.default.atk = new Object();
    };

    var pluginName = 'atk' + name;
    var dataName = "__" + pluginName;
    var old = _jquery2.default.fn[pluginName];

    // add plugin to atk namespace.
    _jquery2.default.atk[name] = className;

    // register plugin to jQuery fn prototype.
    _jquery2.default.fn[pluginName] = function () {
        var option = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
        var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];


        // Check if we are calling a plugin specific function: $(element).plugin('function',[arg1, arg2]);
        if (typeof option === 'string') {
            if (this.data(dataName) && typeof this.data(dataName)[option] === 'function') {
                return this.data(dataName)['call'](option, args);
            }
        }

        return this.each(function () {
            var options = _jquery2.default.extend({}, className.DEFAULTS, (typeof option === "undefined" ? "undefined" : _typeof(option)) === 'object' && option);
            // create plugin using the constructor function store in atk namespace object
            // and add a reference of it to this jQuery object data.
            (0, _jquery2.default)(this).data(dataName, new _jquery2.default.atk[name](this, options));
        });
    };

    // - Short hand
    if (shortHand) {
        _jquery2.default[pluginName] = function (options) {
            return (0, _jquery2.default)({})[pluginName](options);
        };
    }
    // - No conflict
    _jquery2.default.fn[pluginName].noConflict = function () {
        return _jquery2.default.fn[pluginName] = old;
    };
}
module.exports = exports.default;

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ajaxec = function (_atkPlugin) {
    _inherits(ajaxec, _atkPlugin);

    function ajaxec() {
        _classCallCheck(this, ajaxec);

        return _possibleConstructorReturn(this, (ajaxec.__proto__ || Object.getPrototypeOf(ajaxec)).apply(this, arguments));
    }

    _createClass(ajaxec, [{
        key: 'main',
        value: function main() {
            //Allow user to confirm if available.
            if (this.settings.confirm) {
                if (confirm(this.settings.confirm)) {
                    this.doExecute();
                }
            } else {
                this.doExecute();
            }
        }
    }, {
        key: 'doExecute',
        value: function doExecute() {
            this.$el.api({
                on: 'now',
                url: this.settings.uri,
                data: this.settings.uri_options,
                method: 'POST',
                obj: this.$el
            });
        }
    }]);

    return ajaxec;
}(_atkPlugin3.default);

exports.default = ajaxec;


ajaxec.DEFAULTS = {
    uri: null,
    uri_options: {},
    confirm: null
};
module.exports = exports.default;

/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var createModal = function (_atkPlugin) {
    _inherits(createModal, _atkPlugin);

    function createModal() {
        _classCallCheck(this, createModal);

        return _possibleConstructorReturn(this, (createModal.__proto__ || Object.getPrototypeOf(createModal)).apply(this, arguments));
    }

    _createClass(createModal, [{
        key: 'main',
        value: function main() {
            var options = this.settings;
            // make sure we have an object when no option is passed
            if (_jquery2.default.isArray(options.uri_options)) {
                options.uri_options = {};
            }
            // create modal and add it to the DOM
            var $m = (0, _jquery2.default)('<div class="atk-modal ui modal scrolling"/>').appendTo('body').html(this.getDialogHtml(options.title));

            //add setting to our modal for modalService
            $m.data({ uri: options.uri, type: options.mode, args: options.uri_options, needRemove: true, needCloseTrigger: true, label: options.label });

            //call semantic-ui modal
            $m.modal(options.modal).modal('show');
        }
    }, {
        key: 'getDialogHtml',
        value: function getDialogHtml(title) {
            return '<i class="close icon"></i>\n          <div class="header">' + title + '</div>\n          <div class="image content atk-dialog-content">\n            </div>\n          </div>';
        }
    }]);

    return createModal;
}(_atkPlugin3.default);

exports.default = createModal;


createModal.DEFAULTS = {
    title: '',
    uri: null,
    uri_options: {},
    label: 'Loading...',
    modal: {
        duration: 100
    }
};
module.exports = exports.default;

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

var _UploadService = __webpack_require__(3);

var _UploadService2 = _interopRequireDefault(_UploadService);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var fileUpload = function (_atkPlugin) {
  _inherits(fileUpload, _atkPlugin);

  function fileUpload() {
    _classCallCheck(this, fileUpload);

    return _possibleConstructorReturn(this, (fileUpload.__proto__ || Object.getPrototypeOf(fileUpload)).apply(this, arguments));
  }

  _createClass(fileUpload, [{
    key: 'main',
    value: function main() {
      var that = this;

      this.textInput = this.$el.find('input[type="text"]');
      this.hiddenInput = this.$el.find('input[type="hidden"]');

      this.fileInput = this.$el.find('input[type="file"]');
      this.action = $('#' + this.settings.action);
      this.actionContent = this.action.html();

      this.bar = this.$el.find('.progress').progress({
        text: {
          percent: '{percent}%',
          active: '{percent}%'
        }
      }).hide();

      // Open file dialog on focus.
      if (this.settings.hasFocus) {
        this.textInput.on('focus', function (e) {
          if (!e.target.value) {
            that.fileInput.click();
          }
        });
      }

      // add event handler to action button.
      this.action.on('click', function (e) {
        if (!that.textInput.val()) {
          that.fileInput.click();
        } else {
          // When upload is complete a js action can be send to set an id
          // to the uploaded file via the jQuery data property.
          // Check if that id exist and send it with
          // delete callback, If not, default to file name.
          var id = that.$el.data().fileId;
          if (id === '' || typeof id === 'undefined') {
            id = that.textInput.val();
          }
          that.doFileDelete(id);
        }
      });

      // add event handler to file input.
      this.fileInput.on('change', function (e) {
        if (e.target.files.length > 0) {
          that.textInput.val(e.target.files[0].name);
          //that.doFileUpload(e.target.files[0]);
          that.doFileUpload(e.target.files);
        }
      });
    }

    /**
     * Set the action button html content.
     * Set the input text content.
     */

  }, {
    key: 'setState',
    value: function setState(mode) {
      var that = this;

      switch (mode) {
        case 'delete':
          this.action.html(this.getEraseContent);
          setTimeout(function () {
            that.bar.progress('reset');
            that.bar.hide('fade');
          }, 1000);
          break;
        case 'upload':
          this.action.html(this.actionContent);
          this.textInput.val('');
          this.fileInput.val('');
          this.hiddenInput.val('');
          this.$el.data().fileId = null;
          break;
      }
    }

    /**
     * Do the actual file uploading process.
     *
     * @param file the FileList object.
     */

  }, {
    key: 'doFileUpload',
    value: function doFileUpload(file) {

      var that = this;
      // if submit button id is set, then disable submit
      // during upload.
      if (this.settings.submit) {
        $('#' + this.settings.submit).addClass('disabled');
      }

      // setup task on upload completion.
      var completeCb = function completeCb(response, content) {
        if (response.success) {
          that.bar.progress('set label', that.settings.completeLabel);
          that.setState('delete');
        }
        if (that.settings.submit) {
          $('#' + that.settings.submit).removeClass('disabled');
        }
      };

      // setup progress bar update via xhr.
      var xhrCb = function xhrCb() {
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function (evt) {
          if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total;
            that.bar.progress('set percent', parseInt(percentComplete * 100));
          }
        }, false);
        return xhr;
      };

      that.bar.show();
      _UploadService2.default.uploadFiles(file, this.$el, { action: 'upload' }, this.settings.uri, completeCb, xhrCb);
    }

    /**
     * Callback server for file delete.
     *
     * @param fileName
     */

  }, {
    key: 'doFileDelete',
    value: function doFileDelete(fileName) {

      var that = this;

      this.$el.api({
        on: 'now',
        url: this.settings.uri,
        data: { 'action': 'delete', 'f_name': fileName },
        method: 'POST',
        obj: this.$el,
        onComplete: function onComplete(response, content) {
          if (response.success) {
            that.setState('upload');
          }
        }
      });
    }

    /**
     * Return the html content for erase action button.
     *
     * @returns {string}
     */

  }, {
    key: 'getEraseContent',
    value: function getEraseContent() {
      return '<i class="red remove icon" style=""></i>';
    }
  }]);

  return fileUpload;
}(_atkPlugin3.default);

exports.default = fileUpload;


fileUpload.DEFAULTS = {
  uri: null,
  uri_options: {},
  hasFocus: true,
  action: null,
  completeLabel: '100%',
  submit: null
};
module.exports = exports.default;

/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

/**
 * Create notification message.
 *
 * Can be attach to an element using $('selector')->atkNotify(options);
 * or to the body using $.atkNotify($option)
 */
var notify = function (_atkPlugin) {
    _inherits(notify, _atkPlugin);

    function notify() {
        _classCallCheck(this, notify);

        return _possibleConstructorReturn(this, (notify.__proto__ || Object.getPrototypeOf(notify)).apply(this, arguments));
    }

    _createClass(notify, [{
        key: 'main',
        value: function main() {
            var _this2 = this;

            var cssStyle = void 0;
            this.timer = null;
            var domElement = 'body';

            if (!_jquery2.default.isEmptyObject(this.$el[0])) {
                domElement = this.$el;
            }

            cssStyle = this.getClasses(domElement);
            cssStyle.base.width = this.settings.width + '%';
            cssStyle.base.opacity = this.settings.opacity;

            this.notify = (0, _jquery2.default)(this.getNotifier(this.settings)).hide();
            this.notify.css(_jquery2.default.extend(cssStyle.base, this.getPosition(this.settings.position)));

            this.notify.on('click', '.icon.close', { self: this }, this.removeNotifier);

            this.notify.appendTo(domElement);

            this.notify.transition(this.settings.openTransition);

            if (this.settings.duration) {
                this.timer = setTimeout(function () {
                    _this2.removeNotifier({ data: { self: _this2 } });
                }, this.settings.duration);
            }
        }

        /**
         * Return the html for this notifications.
         * @param options
         * @returns {string}
         */

    }, {
        key: 'getNotifier',
        value: function getNotifier(options) {
            return '<div class="atk-notify"> \n                <div class="ui ' + options.color + ' ' + options.size + ' inverted segment" style="overflow: auto; display: block !important">\n                    <i class="close icon" style="float:right"></i>\n                    <div class="content">\n                        <i class="' + options.icon + ' icon" style=""></i>\n                        <span>' + options.content + '</span>\n                    </div>\n                </div>\n             </div>';
        }

        /**
         * Remove this notification from the element it was add to.
         *
         * @param e
         */

    }, {
        key: 'removeNotifier',
        value: function removeNotifier(e) {
            var self = e.data.self;
            clearTimeout(self.timer);
            self.notify.transition(self.settings.closeTransition);
            self.notify.remove();
        }

        /**
         * Return basis css class use for this notification.
         *
         * @param el
         * @returns {{base: {position: string, z-index: number}}}
         */

    }, {
        key: 'getClasses',
        value: function getClasses(el) {
            return {
                base: {
                    position: el === 'body' ? 'fixed' : 'absolute',
                    'z-index': 9999
                }
            };
        }

        /**
         * Return the css classes needed for positioning this notification.
         * @param position
         * @returns {*}
         */

    }, {
        key: 'getPosition',
        value: function getPosition(position) {
            var positions = {
                topLeft: {
                    top: '0px',
                    left: '0px'
                },
                topCenter: {
                    margin: 'auto',
                    top: '0px',
                    left: '0px',
                    right: '0px'
                },
                topRight: {
                    top: '0px',
                    right: '0px'
                },
                bottomLeft: {
                    bottom: '0px',
                    left: '0px'
                },
                bottomCenter: {
                    margin: 'auto',
                    bottom: '0px',
                    left: '0px',
                    right: '0px'
                },
                bottomRight: {
                    bottom: '0px',
                    right: '0px'
                },
                center: {
                    margin: 'auto',
                    top: '0px',
                    left: '0px',
                    bottom: '0px',
                    right: '0px',
                    'max-height': '1%'
                }
            };
            return positions[position];
        }
    }]);

    return notify;
}(_atkPlugin3.default);

exports.default = notify;


notify.DEFAULTS = {
    color: 'green',
    size: 'small',
    icon: null,
    content: null,
    width: 100,
    closeTransition: 'scale',
    openTransition: 'scale',
    duration: 3000,
    opacity: '1',
    position: 'topLeft'
};
module.exports = exports.default;

/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var reloadView = function (_atkPlugin) {
    _inherits(reloadView, _atkPlugin);

    function reloadView() {
        _classCallCheck(this, reloadView);

        return _possibleConstructorReturn(this, (reloadView.__proto__ || Object.getPrototypeOf(reloadView)).apply(this, arguments));
    }

    _createClass(reloadView, [{
        key: 'main',
        value: function main() {

            if (this.settings.uri) {
                this.$el.api({
                    on: 'now',
                    url: this.settings.uri,
                    data: this.settings.uri_options,
                    method: 'GET',
                    obj: this.$el
                });
            }
        }
    }]);

    return reloadView;
}(_atkPlugin3.default);

exports.default = reloadView;


reloadView.DEFAULTS = {
    uri: null,
    uri_options: {}
};
module.exports = exports.default;

/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

var _ApiService = __webpack_require__(2);

var _ApiService2 = _interopRequireDefault(_ApiService);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var serverEvent = function (_atkPlugin) {
  _inherits(serverEvent, _atkPlugin);

  function serverEvent() {
    _classCallCheck(this, serverEvent);

    return _possibleConstructorReturn(this, (serverEvent.__proto__ || Object.getPrototypeOf(serverEvent)).apply(this, arguments));
  }

  _createClass(serverEvent, [{
    key: 'main',
    value: function main() {

      var element = this.$el;
      var hasLoader = this.settings.showLoader;

      if (typeof EventSource !== "undefined") {
        var source = new EventSource(this.settings.uri + '&event=' + this.settings.event);
        if (hasLoader) {
          element.addClass('loading');
        }
        source.onmessage = function (e) {
          _ApiService2.default.atkSuccessTest(JSON.parse(e.data));
        };
        source.onerror = function (e) {
          if (e.eventPhase === EventSource.CLOSED) {
            if (hasLoader) {
              element.removeClass('loading');
            }
            source.close();
          }
        };
        source.addEventListener("jsAction", function (e) {
          _ApiService2.default.atkSuccessTest(JSON.parse(e.data));
        }, false);
      } else {
        //console.log('server side event not supported fallback to atkReloadView');
        this.$el.atkReloadView({
          uri: this.settings.uri
        });
      }
    }
  }]);

  return serverEvent;
}(_atkPlugin3.default);

exports.default = serverEvent;


serverEvent.DEFAULTS = {
  uri: null,
  uri_options: {},
  showLoader: false,
  event: 'sse'
};
module.exports = exports.default;

/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

var _atkPlugin2 = __webpack_require__(1);

var _atkPlugin3 = _interopRequireDefault(_atkPlugin2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var spinner = function (_atkPlugin) {
    _inherits(spinner, _atkPlugin);

    function spinner() {
        _classCallCheck(this, spinner);

        return _possibleConstructorReturn(this, (spinner.__proto__ || Object.getPrototypeOf(spinner)).apply(this, arguments));
    }

    _createClass(spinner, [{
        key: 'main',
        value: function main() {
            this.timer;
            var options = this.settings;
            // Remove any existing dimmers/spinners
            this.$el.remove('.dimmer');
            this.$el.remove('.spinner');

            var $baseDimmer = (0, _jquery2.default)(options.baseDimmerMarkup);
            var $baseLoader = (0, _jquery2.default)(options.baseLoaderMarkup);

            var $finalSpinner = null;

            $baseLoader.toggleClass('active', options.active);
            $baseLoader.toggleClass('indeterminate', options.indeterminate);
            $baseLoader.toggleClass('centered', options.centered);
            $baseLoader.toggleClass('inline', options.inline);

            var isText = !!options.loaderText;
            if (isText) {
                $baseLoader.toggleClass('text', true);
                $baseLoader.text(options.loaderText);
            }

            if (options.dimmed) {
                $baseDimmer.toggleClass('active', options.active);
                $finalSpinner = $baseDimmer.append($baseLoader);
            } else {
                $finalSpinner = $baseLoader;
            }

            // If replace is true we remove the existing content in the $element.
            this.showSpinner(this.$el, $finalSpinner, options.replace);
        }
    }, {
        key: 'showSpinner',
        value: function showSpinner($element, $spinner) {
            var replace = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            this.timer = setTimeout(function () {
                if (replace) $element.empty();
                $element.append($spinner);
            }, 500);
        }
    }, {
        key: 'remove',
        value: function remove() {
            clearTimeout(this.timer);
            this.$el.find('.loader').remove();
        }
    }]);

    return spinner;
}(_atkPlugin3.default);

exports.default = spinner;


spinner.DEFAULTS = {
    active: false,
    replace: false,
    dimmed: false,
    inline: false,
    indeterminate: false,
    loaderText: 'Loading',
    centered: false,
    baseDimmerMarkup: '<div class="ui dimmer"></div>',
    baseLoaderMarkup: '<div class="ui loader"></div>'
};
module.exports = exports.default;

/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _jquery = __webpack_require__(0);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Singleton class
 * This is default setup for semantic-ui modal.
 * Allow to manage uri pass to our modal and dynamically update content from this uri
 * using the semantic api function.
 * Also keep track of how many modal are use by the app.
 */
var ModalService = function () {
    _createClass(ModalService, null, [{
        key: 'getInstance',
        value: function getInstance() {
            return this.instance;
        }
    }]);

    function ModalService() {
        _classCallCheck(this, ModalService);

        if (!ModalService.instance) {
            this.modals = [];
            ModalService.instance = this;
        }
        return ModalService.instance;
    }

    _createClass(ModalService, [{
        key: 'setModals',
        value: function setModals(settings) {
            settings.duration = 100;
            settings.allowMultiple = true;
            settings.onHidden = this.onHidden;
            settings.onShow = this.onShow;
            settings.onHide = this.onHide;
            settings.onVisible = this.onVisible;
        }
    }, {
        key: 'onHidden',
        value: function onHidden() {
            modalService.removeModal((0, _jquery2.default)(this));
        }
    }, {
        key: 'onVisible',
        value: function onVisible() {
            var args = {},
                data = void 0;
            // const service = apiService;
            var $modal = (0, _jquery2.default)(this);
            var $content = (0, _jquery2.default)(this).find('.atk-dialog-content');

            // check data associated with this modal.
            if (!_jquery2.default.isEmptyObject($modal.data())) {
                data = $modal.data();
            }

            // add data argument
            if (data && data.args) {
                args = data.args;
            }

            // check for data type, usually json or html
            if (data && data.type === 'json') {
                args = _jquery2.default.extend(true, args, { json: true });
            }

            // does modal content need to be loaded dynamically
            if (data && data.uri) {
                $content.html(modalService.getLoader(data.label ? data.label : ''));
                $content.api({
                    on: 'now',
                    url: data.uri,
                    data: args,
                    method: 'GET',
                    obj: $content,
                    onComplete: function onComplete(response, content) {
                        var result = content.html(response.html);
                        if (!result.length) {
                            response.success = false;
                            response.isServiceError = true;
                            response.message = 'Modal service error: Unable to replace atk-dialog content in modal from server response. Empty Content.';
                        } else {
                            $modal.modal('refresh');
                            //content is replace no need to do it in api
                            response.id = null;
                        }
                    }
                });
            }
            modalService.addModal($modal);
        }
    }, {
        key: 'onShow',
        value: function onShow() {}
    }, {
        key: 'onHide',
        value: function onHide() {
            return (0, _jquery2.default)(this).data('isClosable');
        }
    }, {
        key: 'addModal',
        value: function addModal(modal) {
            this.modals.push(modal);
            this.setCloseTriggerEventInModals();
            this.hideShowCloseIcon();
        }
    }, {
        key: 'removeModal',
        value: function removeModal(modal) {
            if (modal.data().needRemove) {
                //This modal was add by createModal and need to be remove.
                modal.remove();
            }
            this.modals.pop();
            this.setCloseTriggerEventInModals();
            this.hideShowCloseIcon();
        }

        /**
         * Will loop through modals in reverse order an
         * attach the close event handler in the last one available.
         */

    }, {
        key: 'setCloseTriggerEventInModals',
        value: function setCloseTriggerEventInModals() {
            var _this = this;

            var _loop = function _loop(i) {
                var modal = _this.modals[i];
                if (modal.data().needCloseTrigger) {
                    modal.on('close', '.atk-dialog-content', function () {
                        modal.modal('hide');
                    });
                } else {
                    modal.off('close', '.atk-dialog-content');
                }
            };

            for (var i = this.modals.length - 1; i >= 0; --i) {
                _loop(i);
            }
        }

        /**
         * Only last modal in queue should have the close icon
         */

    }, {
        key: 'hideShowCloseIcon',
        value: function hideShowCloseIcon() {
            for (var i = this.modals.length - 1; i >= 0; --i) {
                var _modal = this.modals[i];
                if (i === this.modals.length - 1) {
                    _modal.find('i.icon.close').show();
                    _modal.data('isClosable', true);
                } else {
                    _modal.find('i.icon.close').hide();
                    _modal.data('isClosable', false);
                }
            }
        }
    }, {
        key: 'getLoader',
        value: function getLoader(loaderText) {
            return '<div class="ui active inverted dimmer">\n              <div class="ui text loader">' + loaderText + '</div>';
        }
    }]);

    return ModalService;
}();

var modalService = new ModalService();
Object.freeze(modalService);

exports.default = modalService;
module.exports = exports.default;

/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _atkSemanticUi = __webpack_require__(4);

var _atkSemanticUi2 = _interopRequireDefault(_atkSemanticUi);

__webpack_require__(5);

var _plugin = __webpack_require__(6);

var _plugin2 = _interopRequireDefault(_plugin);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

//Allow to register a plugin with jQuery;
_atkSemanticUi2.default.registerPlugin = _plugin2.default;

/**
 * Exporting services in order to be available globally
 * or by importing it into your own module.
 *
 * Available as a global Var: atk.uploadService.fileUpload()
 * Available as an import:
 *  import atk from atk4JS;
 *  atk.uploadService.fileUpload();
 */
module.exports = _atkSemanticUi2.default;

/***/ })
/******/ ]);
});
//# sourceMappingURL=atkjs-ui.js.map