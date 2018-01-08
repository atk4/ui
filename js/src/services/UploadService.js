import $ from 'jquery';

/**
 * Singleton class
 * Allow to upload files to server via semantic-ui api.
 */
class UploadService {

  static getInstance() {
    return this.instance;
  }

  constructor() {
    if(!UploadService.instance){
      UploadService.instance = this;
    }
    return UploadService.instance;
  }

  /**
   *  Will upload a FileList object to server.
   *  Each file in FileList will be include in formData as
   *  'file-(number)' param, except for the first one which will
   *  be set to 'file' only.
   *
   * @param files         A FileList object.
   * @param el            the jQuery element to attach to semantic api.
   * @param data          Extra data to pass with files.
   * @param uploadUrl     the url that handle upload.
   * @param completeCb    the callback for semantic-ui api.onComplete.
   * @param xhrCb         the xhr function to pass to server.
   */
  uploadFiles(files, el, data = [], uploadUrl, completeCb = function(r,c){}, xhrCb = function(){return new window.XMLHttpRequest()}) {

    let formData = new FormData();

    for (let i = 0; i < files.length; i++) {
      const param = (i===0)? 'file': 'file-'+i;
      formData.append(param, files.item(i));
    }

    if (!$.isEmptyObject(data)) {
      $.each(data, function(key, el) {
        formData.append(key, el);
      })
    }

    el.api({
      on: 'now',
      url: uploadUrl,
      cache: false,
      processData: false,
      contentType: false,
      data: formData,
      method: 'POST',
      obj: this.$el,
      xhr: xhrCb,
      onComplete: completeCb
    });
  }
}

const uploadService = new UploadService();
Object.freeze(uploadService);

export default uploadService;