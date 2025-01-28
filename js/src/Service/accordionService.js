import $ from 'external/jquery';

class AccordionService {
    getDefaultFomanticSettings() {
        return [
            {},
            {
                onOpening: this.onOpening,
            },
        ];
    }

    onOpening() {
        if ($(this).data('path')) {
            $(this).atkReloadView({ url: $(this).data('path'), urlOptions: { __atk_json: 1 } });
        }
    }
}

export default Object.freeze(new AccordionService());
