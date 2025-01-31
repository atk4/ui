import $ from 'external/jquery';

class AccordionService {
    getDefaultFomanticUiSettings() {
        return [
            {},
            {
                onOpening: this.onOpening,
            },
        ];
    }

    onOpening() {
        if ($(this).data('url')) {
            $(this).atkReloadView({ url: $(this).data('url'), urlOptions: { __atk_json: 1 } });
        }
    }
}

export default Object.freeze(new AccordionService());
