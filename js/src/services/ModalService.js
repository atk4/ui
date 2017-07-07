import apiService from 'services/ApiService';

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
        let arg = {}, data;
        // const service = apiService;
        const $modal = $(this);
        const $content = $(this).find('.atk-dialog-content');

        // does data come from DOM or createModal
        if (!$.isEmptyObject($modal.data('modalSettings'))) {
            data = $modal.data('modalSettings');
        } else if (!$.isEmptyObject($content.data())) {
            data = $content.data();
        }

        // add data argument
        if (data && data.arg) {
            arg = data.arg;
        }

        // check for data type, usually json or html
        if (data && data.type === 'json') {
            arg = $.extend(arg, {json:true});
        }

        // does modal content need to be loaded dynamically
        if (data && data.uri) {
            $content.api({
                on: 'now',
                url: data.uri,
                data: arg,
                method: 'GET',
                obj: $content,
                onComplete: function(response, content) {
                    const result = content.html(response.html);
                    if (!result.length) {
                        response.success = false;
                        response.message = 'Unable to replace atk-dialog content in modal from server response';
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
        const settings = modal.data('modalSettings');
        if (settings && settings.needRemove) {
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
            if (modal.data('modalSettings') && modal.data('modalSettings').needCloseTrigger) {
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
}

const modalService = new ModalService();
Object.freeze(modalService);

export default modalService;
