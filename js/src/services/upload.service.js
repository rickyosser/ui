import $ from 'jquery';

/* eslint-disable jsdoc/require-param-type */

/**
 * Allow to upload files to server via Fomantic-UI API.
 */
class UploadService {
    /**
     * Will upload a FileList object to server.
     * Each file in FileList will be include in formData as
     * 'file-(number)' param, except for the first one which will
     * be set to 'file' only.
     *
     * @param files      A FileList object.
     * @param element    the jQuery element to attach to Fomantic-UI api.
     * @param data       Extra data to pass with files.
     * @param uploadUrl  the url that handle upload.
     * @param completeCb the callback for Fomantic-UI api.onComplete.
     * @param xhrCb      the xhr function to pass to server.
     */
    uploadFiles(
        files,
        element,
        data,
        uploadUrl,
        completeCb = function (r, c) {},
        xhrCb = function () { return new window.XMLHttpRequest(); },
    ) {
        const formData = new FormData();

        for (let i = 0; i < files.length; i++) {
            const param = (i === 0) ? 'file' : 'file-' + i;
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

const uploadService = new UploadService();
Object.freeze(uploadService);

export default uploadService;
