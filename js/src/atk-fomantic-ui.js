import $ from 'external/jquery';
import apiService from './services/api.service';
import formService from './services/form.service';
import modalService from './services/modal.service';
import uploadService from './services/upload.service';
import popupService from './services/popup.service';
import accordionService from './services/accordion.service';

// setup app service for Fomantic-UI
apiService.setService($.fn.api.settings);
formService.setService($.fn.form.settings);
modalService.setModals($.fn.modal.settings);
popupService.setPopups($.fn.popup.settings);
accordionService.setService($.fn.accordion.settings);

export default {
    apiService: apiService,
    formService: formService,
    modalService: modalService,
    uploadService: uploadService,
};
