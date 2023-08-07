import $ from 'external/jquery';
import atk from 'atk';

/**
 * This is default setup for Fomantic-UI modal.
 * Allow to manage URL pass to our modal and dynamically update content from this URL
 * using the Fomantic-UI api function.
 * Also keep track of created modals and display only the topmost modal.
 */
class ModalService {
    constructor() {
        this.modals = [];
    }

    getDefaultFomanticSettings() {
        return [
            {
                duration: 100,
            },
            {
                // never autoclose previously displayed modals, manage them thru this service only
                allowMultiple: true,
                // any change in modal DOM should automatically refresh cached positions
                // allow modal window to add scrolling when content is added after modal is created
                observeChanges: true,
                onShow: this.onShow,
                onHide: this.onHide,
                onHidden: this.onHidden,
            },
        ];
    }

    onShow() {
        const s = atk.modalService;

        for (const modal of s.modals) {
            if (modal === this) {
                throw new Error('Unexpected modal to show - modal is already active');
            }
        }
        s.modals.push(this);

        s.addModal($(this));
    }

    onHide() {
        const s = atk.modalService;

        if (s.modals.length === 0 || s.modals.at(-1) !== this) {
            throw new Error('Unexpected modal to hide - modal is not front');
        }
        s.modals.pop();

        s.removeModal($(this));

        return true;
    }

    onHidden() {
        const $modal = $(this);

        if ($modal.data('needRemove')) {
            $modal.remove();
        }
    }

    addModal($modal) {
        // hide other modals
        if (this.modals.length > 1) {
            const $previousModal = $(this.modals.at(-2));
            if ($previousModal.hasClass('visible')) {
                $previousModal.css('visibility', 'hidden');
                $previousModal.addClass('__hiddenNotFront');
                $previousModal.removeClass('visible');
            }
        }

        const data = $modal.data();
        let args = {};
        if (data.args) {
            args = data.args;
        }

        // check for data type, usually JSON or HTML
        if (data.type === 'json') {
            args = $.extend(true, args, { __atk_json: 1 });
        }

        // does modal content need to be loaded dynamically
        if (data.url) {
            $modal.data('closeOnLoadingError', true);

            const $content = $modal.find('.atk-dialog-content');

            $content.html(this.getLoaderHtml(data.loadingLabel ?? ''));

            $content.api({
                on: 'now',
                url: data.url,
                data: args,
                method: 'GET',
                obj: $content,
                onComplete: function (response, content) {
                    const modelsContainer = $('.ui.dimmer.modals.page')[0];
                    $($.parseHTML(response.html)).find('.ui.modal[id]').each((i, e) => {
                        $(modelsContainer).find('#' + e.id).remove();
                    });

                    const result = content.html(response.html);
                    if (result.length === 0) {
                        // TODO this if should be removed
                        response.success = false;
                        response.isServiceError = true;
                        response.message = 'Modal service error: Empty HTML, unable to replace modal content from server response';
                    } else {
                        // content is replace no need to do it in api
                        response.id = null;
                    }
                },
                onSuccess: function () {
                    $modal.removeData('closeOnLoadingError');
                },
            });
        }
    }

    removeModal($modal) {
        // https://github.com/fomantic/Fomantic-UI/issues/2528
        if ($modal.modal('get settings').transition) {
            $modal.transition('stop all');
        }

        // hide other modals
        if (this.modals.length > 0) {
            const $previousModal = $(this.modals.at(-1));
            if ($previousModal.hasClass('__hiddenNotFront')) {
                $previousModal.css('visibility', '');
                $previousModal.addClass('visible');
                $previousModal.removeClass('__hiddenNotFront');
                // recenter modal, needed even with observeChanges enabled
                // https://github.com/fomantic/Fomantic-UI/issues/2476
                $previousModal.modal('refresh');
            }
        }
    }

    getLoaderHtml(loaderText) {
        return '<div class="ui active inverted dimmer">'
            + '<div class="ui text loader">' + loaderText + '</div>'
            + '</div>';
    }
}

export default Object.freeze(new ModalService());
