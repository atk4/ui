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
            $(this).atkReloadView({ url: $(this).data('url') });
        }
    }
}

export default Object.freeze(new AccordionService());
