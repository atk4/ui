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

if (typeof FormSerializer != "undefined") {
  //setup jQuery FormSerializer to accept in input name with dash char (-)
  $.extend(FormSerializer.patterns, {
    validate: /^[_a-z][a-z0-9_-]*(?:\[(?:\d*|[a-z0-9_-]+)\])*$/i,
    key:      /[a-z0-9_-]+|(?=\[\])/gi,
    named:    /^[a-z0-9_-]+$/i
  });
}

let atkSemantic = {
  uploadService: uploadService,
  apiService: apiService,
  modalService: modalService,
  formService: formService
};

module.exports = atkSemantic;