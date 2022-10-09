import $ from 'jquery';
import apiService from 'services/api.service';
import modalService from 'services/modal.service';
import uploadService from './services/upload.service';
import formService from './services/form.service';
import popupService from './services/popup.service';
import accordionService from './services/accordion.service';

// setup app service for Fomantic-UI
apiService.setService($.fn.api.settings);
modalService.setModals($.fn.modal.settings);
formService.setService($.fn.form.settings);
popupService.setPopups($.fn.popup.settings);
accordionService.setService($.fn.accordion.settings);

export default {
    uploadService: uploadService,
    apiService: apiService,
    modalService: modalService,
    formService: formService,
};
