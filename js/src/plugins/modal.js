import $ from 'jquery';

export default function modal(options) {
    $('body > div.ui.dimmer.modals.page.transition.hidden').remove();

    let $newModal = $('<div class="ui fullscreen scrolling modal"></div>');

    $newModal.toggleClass('fullscreen', !!options.wide);

    let $modalContent = $('<div class="image content"></div>')
        .html(options.content);

    if(options.uri) {
        $modalContent.spinner({
            dimmed: true,
            active: true
        })
        .reloadView({
            uri: options.uri,
            replace: false,
            complete: () => {
                $modalContent.append(options.content);
            }
        });
    }

    $newModal
        .append($modalContent);

    $newModal.modal('show');
}