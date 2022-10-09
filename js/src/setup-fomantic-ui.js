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

// setup Fomantic-UI globals
apiService.setupFomanticUi($.fn.api.settings);
formService.setupFomanticUi($.fn.form.settings);
modalService.setupFomanticUi($.fn.modal.settings);
popupService.setupFomanticUi($.fn.popup.settings);
accordionService.setupFomanticUi($.fn.accordion.settings);

export default null;
