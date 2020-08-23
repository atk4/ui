import $ from 'jquery';

class AccordionService {

  static getInstance() {
    return this.instance;
  }


  constructor() {
    if (!this.instance) {
      this.instance = this;
    }
    return this.instance;
  }

  /**
   * Setup semantic-ui accordion for this service.
   * @param settings
   */
  setService(settings) {
    settings.onOpening= this.onOpening;
  }

  onOpening() {
    if ($(this).data('path')){
      $(this).atkReloadView({uri: $(this).data('path'), uri_options:{__atk_json:1}});
    }
  }

}

const accordionService = new AccordionService();
Object.freeze(accordionService);

export default accordionService;
