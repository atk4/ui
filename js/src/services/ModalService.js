import $ from 'jquery';

/**
 * Singleton class
 * This is default setup for semantic-ui modal.
 * Allow to manage uri pass to our modal and dynamically update content from this uri
 * using the semantic api function.
 * Also keep track of how many modal are use by the app.
 */
class ModalService {

    static getInstance() {
        return this.instance;
    }

    constructor() {
        if(!ModalService.instance){
            this.modals = [];
            ModalService.instance = this;
        }
        return ModalService.instance;
    }

    setModals(settings) {
        settings.duration = 100;
        settings.allowMultiple = true;
        settings.onHidden = this.onHidden;
        settings.onShow = this.onShow;
        settings.onHide = this.onHide;
        settings.onVisible = this.onVisible;
    }

    onHidden() {
        modalService.removeModal($(this));
    }

    onVisible() {
        let args = {}, data;
        // const service = apiService;
        const $modal = $(this);
        const $content = $(this).find('.atk-dialog-content');

        // check data associated with this modal.
        if (!$.isEmptyObject($modal.data())) {
          data = $modal.data();
        }

        // add data argument
        if (data && data.args) {
            args = data.args;
        }

        // check for data type, usually json or html
        if (data && data.type === 'json') {
            args = $.extend(true, args, {json:true});
        }

        // does modal content need to be loaded dynamically
        if (data && data.uri) {
            $content.html(modalService.getLoader(data.label ? data.label : ''));
            $content.api({
                on: 'now',
                url: data.uri,
                data: args,
                method: 'GET',
                obj: $content,
                onComplete: function(response, content) {
                    const result = content.html(response.html);
                    if (!result.length) {
                        response.success = false;
                        response.isServiceError = true;
                        response.message = 'Modal service error: Unable to replace atk-dialog content in modal from server response. Empty Content.';
                    } else {
                        $modal.modal('refresh');
                        //content is replace no need to do it in api
                        response.id = null;
                    }
                }
            });
        }
        modalService.addModal($modal);
    }

    onShow() {}

    onHide() {
        return $(this).data('isClosable');
    }

    addModal(modal) {
        this.modals.push(modal);
        this.setCloseTriggerEventInModals();
        this.hideShowCloseIcon();
    }

    removeModal(modal) {
        if (modal.data().needRemove) {
            //This modal was add by createModal and need to be remove.
            modal.remove();
        }
        this.modals.pop();
        this.setCloseTriggerEventInModals();
        this.hideShowCloseIcon();
    }

    /**
     * Will loop through modals in reverse order an
     * attach the close event handler in the last one available.
     */
    setCloseTriggerEventInModals() {
        for (let i = this.modals.length - 1; i >= 0; --i) {
            const modal = this.modals[i];
            if (modal.data().needCloseTrigger) {
                modal.on('close', '.atk-dialog-content', function(){
                    modal.modal('hide');
                });
            } else {
                modal.off('close', '.atk-dialog-content');
            }
        }
    }

    /**
     * Only last modal in queue should have the close icon
     */
    hideShowCloseIcon() {
        for (let i = this.modals.length - 1; i >= 0; --i) {
            const modal = this.modals[i];
            if (i === this.modals.length - 1) {
                modal.find('i.icon.close').show();
                modal.data('isClosable', true);
            } else {
                modal.find('i.icon.close').hide();
                modal.data('isClosable', false);
            }
        }
    }

    getLoader(loaderText) {
        return `<div class="ui active inverted dimmer">
              <div class="ui text loader">${loaderText}</div>`
    }
}

const modalService = new ModalService();
Object.freeze(modalService);

export default modalService;
