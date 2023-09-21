import $ from 'external/jquery';
import atk from 'atk';
import AtkPlugin from './atk.plugin';

export default class AtkFileUploadPlugin extends AtkPlugin {
    main() {
        this.textInput = this.$el.find('input[type="text"]');
        this.hiddenInput = this.$el.find('input[type="hidden"]');

        this.fileInput = this.$el.find('input[type="file"]');
        this.action = this.$el.find('#' + this.settings.action);
        this.actionContent = this.action.html();

        this.bar = this.$el.find('.progress');
        this.setEventHandler();
        this.setInitialState();
    }

    /**
     * Setup field initial state.
     */
    setInitialState() {
        // set progress bar
        this.bar.progress({
            text: {
                percent: '{percent}%',
                active: '{percent}%',
            },
        }).hide();

        this.$el.data().fileId = this.settings.file.id;
        this.hiddenInput.val(this.settings.file.id);
        this.textInput.val(this.settings.file.name);
        this.textInput.data('isTouch', false);
        if (this.settings.file.id) {
            this.setState('delete');
        }
    }

    /**
     * Update input value.
     */
    updateField(fileId, fileName) {
        this.$el.data().fileId = fileId;
        this.hiddenInput.val(fileId);

        if (fileName === '' || fileName === undefined || fileName === null) {
            this.textInput.val(fileId);
        } else {
            this.textInput.val(fileName);
        }
    }

    /**
     * Add event handler to input element.
     */
    setEventHandler() {
        this.textInput.on('click', (e) => {
            if (!e.target.value) {
                this.fileInput.click();
            }
        });

        // add event handler to action button
        this.action.on('click', (e) => {
            if (!this.textInput.val()) {
                this.fileInput.click();
            } else {
                // When upload is complete a JS action can be send to set an ID
                // to the uploaded file via the jQuery data property.
                // Check if that ID exist and send it with
                // delete callback, If not, default to file name.
                let id = this.$el.data().fileId;
                if (id === '' || id === undefined || id === null) {
                    id = this.textInput.val();
                }
                this.doFileDelete(id);
            }
        });

        // add event handler to file input
        this.fileInput.on('change', (e) => {
            if (e.target.files.length > 0) {
                this.textInput.val(e.target.files[0].name);
                this.doFileUpload(e.target.files);
            }
        });
    }

    /**
     * Set the action button HTML content.
     * Set the input text content.
     */
    setState(mode) {
        switch (mode) {
            case 'delete': {
                this.action.html(this.getEraseContent);
                setTimeout(() => {
                    this.bar.progress('reset');
                    this.bar.hide('fade');
                }, 1000);

                break;
            }
            case 'upload': {
                this.action.html(this.actionContent);
                this.textInput.val('');
                this.fileInput.val('');
                this.hiddenInput.val('');
                this.$el.data().fileId = null;

                break;
            }
        }
    }

    /**
     * Do the actual file uploading process.
     *
     * @param {FileList} files
     */
    doFileUpload(files) {
        // if submit button id is set, then disable submit during upload
        if (this.settings.submit) {
            $('#' + this.settings.submit).addClass('disabled');
        }

        // setup task on upload completion
        const completeCb = (response, content) => {
            if (response.success) {
                this.bar.progress('set label', this.settings.completeLabel);
                this.setState('delete');
            }

            if (this.settings.submit) {
                $('#' + this.settings.submit).removeClass('disabled');
            }
        };

        // setup progress bar update via xhr
        const xhrCb = () => {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    const percentComplete = event.loaded / event.total;
                    this.bar.progress('set percent', Number.parseInt(percentComplete * 100, 10));
                }
            }, false);

            return xhr;
        };

        this.bar.show();
        atk.uploadService.uploadFiles(
            files,
            this.$el,
            { fUploadAction: 'upload' },
            this.settings.url,
            completeCb,
            xhrCb
        );
    }

    /**
     * Callback server for file delete.
     */
    doFileDelete(fileId) {
        this.$el.api({
            on: 'now',
            url: this.settings.url,
            data: { fUploadAction: 'delete', fUploadId: fileId },
            method: 'POST',
            obj: this.$el,
            onComplete: (response, content) => {
                if (response.success) {
                    this.setState('upload');
                }
            },
        });
    }

    /**
     * Return the HTML content for erase action button.
     *
     * @returns {string}
     */
    getEraseContent() {
        return '<i class="red remove icon"></i>';
    }
}

AtkFileUploadPlugin.DEFAULTS = {
    url: null,
    file: { id: null, name: null },
    urlOptions: {},
    action: null,
    completeLabel: '100%',
    submit: null,
};
