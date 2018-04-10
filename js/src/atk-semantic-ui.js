import $ from 'jquery';
import apiService from 'services/ApiService';
import modalService from 'services/ModalService';
import uploadService from "./services/UploadService";
import popupService from "./services/PopupService";

// setup app service for semantic-ui
apiService.setService($.fn.api.settings);
modalService.setModals($.fn.modal.settings);
popupService.setPopups($.fn.popup.settings);

let atkSemantic = {
  uploadService: uploadService,
  apiService: apiService,
  modalService: modalService
};

module.exports = atkSemantic;