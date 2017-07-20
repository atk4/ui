import $ from 'jquery';
import apiService from 'services/ApiService';
import modalService from 'services/ModalService';

// setup app service for semantic-ui
apiService.setService($.fn.api.settings);
modalService.setModals($.fn.modal.settings);

