import apiService from 'services/ApiService';
import modalService from 'services/ModalService';

(function ($, window, document, undefined) {

    // setup app service for semantic-ui
    apiService.setService($.fn.api.settings);
    modalService.setModals($.fn.modal.settings);

}) (jQuery, window, document);

