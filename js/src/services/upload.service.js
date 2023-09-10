import $ from 'external/jquery';

/**
 * Allow to upload files to server.
 */
class UploadService {
    /**
     * Will upload a FileList object to server.
     * Each file in FileList will be include in formData as
     * 'file-(number)' param, except for the first one which will
     * be set to 'file' only.
     *
     * @param {FileList} files
     * @param {$}        element    the jQuery element to attach to Fomantic-UI api.
     * @param {object}   data       Extra data to pass with files.
     * @param {string}   uploadUrl  the URL that handle upload.
     * @param {Function} completeCb the callback for Fomantic-UI api.onComplete.
     * @param {Function} xhrCb      the xhr function to pass to server.
     */
    uploadFiles(
        files,
        element,
        data,
        uploadUrl,
        completeCb = function (r, c) {},
        xhrCb = function () { return new window.XMLHttpRequest(); }
    ) {
        const formData = new FormData();

        for (let i = 0; i < files.length; i++) {
            const param = i === 0 ? 'file' : 'file-' + i;
            formData.append(param, files.item(i));
        }

        if (!$.isEmptyObject(data)) {
            $.each(data, (key, el) => {
                formData.append(key, el);
            });
        }

        element.api({
            on: 'now',
            url: uploadUrl,
            cache: false,
            processData: false,
            contentType: false,
            data: formData,
            method: 'POST',
            obj: this.$el,
            xhr: xhrCb,
            onComplete: completeCb,
        });
    }
}

export default Object.freeze(new UploadService());
