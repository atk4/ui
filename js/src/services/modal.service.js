import $ from 'external/jquery';
import atk from 'atk';

/**
 * This is default setup for Fomantic-UI modal.
 * Allow to manage URL pass to our modal and dynamically update content from this URL
 * using the Fomantic-UI api function.
 * Also keep track of how many modal are use by the app.
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
        atk.modalService.addModal($(this));
    }

    onHide() {
        return $(this).data('isClosable');
    }

    onHidden() {
        atk.modalService.removeModal($(this));
    }

    addModal($modal) {
        const that = this;
        this.modals.push($modal);

        this.setCloseTriggerEventInModals();
        this.hideShowCloseIcon();

        // hide other modals
        const $prevModal = this.modals.length > 1 ? this.modals[this.modals.length - 2] : null;
        if ($prevModal && $prevModal.hasClass('visible')) {
            $prevModal.css('visibility', 'hidden');
            $prevModal.addClass('hiddenNotFront');
            $prevModal.removeClass('visible');
        }

        // add modal esc handler
        if (this.modals.length === 1) {
            $(document).on('keyup.atk.modalService', (e) => {
                if (e.keyCode === 27) {
                    if (that.modals.length > 0) {
                        that.modals[that.modals.length - 1].modal('hide');
                    }
                }
            });
        }

        let args = {};
        const $content = $modal.find('.atk-dialog-content');

        // check data associated with this modal
        const data = $modal.data();

        // add data argument
        if (data.args) {
            args = data.args;
        }

        // check for data type, usually json or html
        if (data.type === 'json') {
            args = $.extend(true, args, { __atk_json: 1 });
        }

        // does modal content need to be loaded dynamically
        if (data.url) {
            $content.html(atk.modalService.getLoader(data.loadingLabel ? data.loadingLabel : ''));

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
                        response.success = false;
                        response.isServiceError = true;
                        response.message = 'Modal service error: Empty html, unable to replace modal content from server response';
                    } else {
                        if ($modal.modal('get settings').autofocus) {
                            atk.modalService.doAutoFocus($modal);
                        }
                        // content is replace no need to do it in api
                        response.id = null;
                    }
                },
            });
        }
    }

    removeModal($modal) {
        if ($modal.data().needRemove) {
            $modal.remove();
        }
        this.modals.pop();
        this.setCloseTriggerEventInModals();
        this.hideShowCloseIcon();

        // hide other modals
        const $prevModal = this.modals.length > 0 ? this.modals[this.modals.length - 1] : null;
        if ($prevModal && $prevModal.hasClass('hiddenNotFront')) {
            $prevModal.css('visibility', '');
            $prevModal.addClass('visible');
            $prevModal.removeClass('hiddenNotFront');
            // recenter modal, needed even with observeChanges enabled
            // https://github.com/fomantic/Fomantic-UI/issues/2476
            $prevModal.modal('refresh');
        }

        if (this.modals.length === 0) {
            $(document).off('atk.modalService');
        }
    }

    doAutoFocus($modal) {
        const inputs = $modal.find('[tabindex], :input').filter(':visible');
        const autofocus = inputs.filter('[autofocus]');
        const input = (autofocus.length > 0) ? autofocus.first() : inputs.first();

        if (input.length > 0) {
            input.focus().select();
        }
    }

    /**
     * Will loop through modals in reverse order an
     * attach the close event handler in the last one available.
     */
    setCloseTriggerEventInModals() {
        for (let i = this.modals.length - 1; i >= 0; --i) {
            const $modal = this.modals[i];
            if ($modal.data().needCloseTrigger) {
                $modal.on('close', '.atk-dialog-content', () => {
                    $modal.modal('hide');
                });
            } else {
                $modal.off('close', '.atk-dialog-content');
            }
        }
    }

    /**
     * Only last modal in queue should have the close icon
     */
    hideShowCloseIcon() {
        for (let i = this.modals.length - 1; i >= 0; --i) {
            const $modal = this.modals[i];
            if (i === this.modals.length - 1) {
                $modal.find('i.icon.close').show();
                $modal.data('isClosable', true);
            } else {
                $modal.find('i.icon.close').hide();
                $modal.data('isClosable', false);
            }
        }
    }

    getLoader(loaderText) {
        return `<div class="ui active inverted dimmer">
              <div class="ui text loader">${loaderText}</div>`;
    }
}

export default Object.freeze(new ModalService());
