import $ from 'external/jquery';
import atk from 'atk';
import accordionService from './services/accordion.service';
import apiService from './services/api.service';
import dataService from './services/data.service';
import formService from './services/form.service';
import modalService from './services/modal.service';
import panelService from './services/panel.service';
import popupService from './services/popup.service';
import uploadService from './services/upload.service';
import vueService from './services/vue.service';

atk.accordionService = accordionService;
atk.apiService = apiService;
atk.dataService = dataService;
atk.formService = formService;
atk.modalService = modalService;
atk.panelService = panelService;
atk.popupService = popupService;
atk.uploadService = uploadService;
atk.vueService = vueService;

const fomanticServicesMap = {
    api: apiService,
    form: formService,
    modal: modalService,
    popup: popupService,
    accordion: accordionService,
};

// setup Fomantic-UI global overrides
// https://github.com/fomantic/Fomantic-UI/issues/2526
$.extend = $.fn.extend = new Proxy($.fn.extend, { // eslint-disable-line no-multi-assign
    apply: function (target, thisArg, args) {
        // https://github.com/fomantic/Fomantic-UI/blob/c30ed51ca12fc1762b04c2fd1a83d087c0124d07/src/definitions/behaviors/api.js#L48
        const firstIndex = args[0] === true ? 1 : 0;
        const secondIndex = args[0] === true ? 2 : 1;
        if (args.length >= (args[0] === true ? 3 : 2)
            && $.isPlainObject(args[firstIndex]) && $.isEmptyObject(args[firstIndex])
            && $.isPlainObject(args[secondIndex])
        ) {
            let name = null;
            for (const n of Object.keys(fomanticServicesMap)) {
                if (args[secondIndex] === $.fn[n].settings) {
                    name = n;
                }
            }
            if (name !== null) {
                const [customSettings, forcedSettings] = fomanticServicesMap[name].getDefaultFomanticSettings();

                const newSettings = new Proxy($.extend(true, {}, {}, args[secondIndex], forcedSettings), {
                    set: (obj, prop, value) => {
                        const origValue = obj[prop];

                        if (forcedSettings[prop] === undefined) {
                            obj[prop] = value;
                        } else if (name === 'api' && prop === 'successTest') {
                            obj[prop] = function (response) {
                                const resOrig = origValue(response);
                                const resNew = value.call(this, response);

                                return resOrig && resNew;
                            };
                        } else if (name === 'api' && prop === 'onSuccess') {
                            obj[prop] = function (response, $module, xhr) {
                                origValue(response, $module, xhr);

                                return value.call(this, response, $module, xhr);
                            };
                        } else if (name === 'api' && prop === 'onFailure') {
                            obj[prop] = function (response, $module, xhr) {
                                origValue(response, $module, xhr);

                                return value.call(this, response, $module, xhr);
                            };
                        } else if (name === 'api' && prop === 'onAbort') {
                            obj[prop] = function (errorMessage, $module, xhr) {
                                origValue(errorMessage, $module, xhr);

                                return value.call(this, errorMessage, $module, xhr);
                            };
                        } else if (name === 'api' && prop === 'onError') {
                            obj[prop] = function (errorMessage, $module, xhr) {
                                origValue(errorMessage, $module, xhr);

                                return value.call(this, errorMessage, $module, xhr);
                            };
                        } else if (name === 'form' && prop === 'onSuccess') {
                            obj[prop] = function (event, values) {
                                origValue(event, values);

                                return value.call(this, event, values);
                            };
                        } else if (name === 'modal' && prop === 'onHidden') {
                            obj[prop] = function (element) {
                                origValue(element);

                                return value.call(element);
                            };
                        } else {
                            throw new Error('Fomantic-UI "' + name + '.' + prop + '" setting cannot be customized outside atk');
                        }

                        return true;
                    },
                });

                $.extend(true, newSettings, customSettings, ...args.slice(secondIndex + 1));

                return newSettings;
            }
        }

        return target.call(thisArg, ...args);
    },
});

export default null;
