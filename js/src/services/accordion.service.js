import $ from 'jquery';

class AccordionService {
    /**
     * Setup Fomantic-UI accordion for this service.
     */
    setService(settings) {
        settings.onOpening = this.onOpening;
    }

    onOpening() {
        if ($(this).data('path')) {
            $(this).atkReloadView({ uri: $(this).data('path'), uriOptions: { __atk_json: 1 } });
        }
    }
}

const accordionService = new AccordionService();
Object.freeze(accordionService);

export default accordionService;
