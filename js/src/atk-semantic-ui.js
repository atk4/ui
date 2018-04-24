import $ from 'jquery';
import apiService from 'services/ApiService';
import modalService from 'services/ModalService';
import uploadService from "./services/UploadService";
import formService from "./services/FormService";
import popupService from "./services/PopupService";

// setup app service for semantic-ui
apiService.setService($.fn.api.settings);
modalService.setModals($.fn.modal.settings);
formService.setService($.fn.form.settings);
popupService.setPopups($.fn.popup.settings);

let atkSemantic = {
  uploadService: uploadService,
  apiService: apiService,
  modalService: modalService,
  formService: formService
};

module.exports = atkSemantic;