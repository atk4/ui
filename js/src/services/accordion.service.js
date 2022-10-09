import $ from 'external/jquery';

class AccordionService {
    setupFomanticUi(settings) {
        settings.onOpening = this.onOpening;
    }

    onOpening() {
        if ($(this).data('path')) {
            $(this).atkReloadView({ uri: $(this).data('path'), uriOptions: { __atk_json: 1 } });
        }
    }
}

export default Object.freeze(new AccordionService());
