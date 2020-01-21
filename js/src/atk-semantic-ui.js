import $ from 'jquery';
import apiService from 'services/api.service';
import modalService from 'services/modal.service';
import uploadService from "./services/upload.service";
import formService from "./services/form.service";
import popupService from "./services/popup.service";
import accordionService from "./services/accordion.service";

// setup app service for semantic-ui
apiService.setService($.fn.api.settings);
modalService.setModals($.fn.modal.settings);
formService.setService($.fn.form.settings);
popupService.setPopups($.fn.popup.settings);
accordionService.setService($.fn.accordion.settings);

if (typeof FormSerializer != "undefined") {
  //setup jQuery FormSerializer to accept in input name with dash char (-)
  $.extend(FormSerializer.patterns, {
    validate: /^[a-z_][a-z0-9_-]*(?:\[(?:\d*|[a-z0-9_-]+)\])*$/i,
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

export default atkSemantic;
