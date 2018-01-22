
===========
File Upload
===========

.. figure:: images/fileupload.png

Upload or UploadImg field may be use in Form in order to upload file or image file from client to server. This is a single file upload field, i.e. only allow to upload
one file at a time per field. Both field contains an upload button action in order to open the user File dialog. UploadImg also has an image preview where a preview of the image
can be show to user. Clicking the input field will open the user file open dialog by default. They also display a progress bar indicating the upload progress status.

.. php:namespace:: atk4\ui\FormField

.. php:class:: Upload
.. php:class:: UploadImg

Attributes
==========

Upload field has the following properties:

.. php:attr:: accept

An array of string containing the file type accept by the field, default is empty.
Example would be: ['application/pdf', 'images/*'].

.. php:attr:: hasFocusEnable

Whether file open dialog will show by clicking the input field, default to true.

.. php:attr:: action

The button view to use for displaying the file open dialog. A default action button is supply if omitted.

UploadImg field inherits all of the Upload properties plus these ones:

.. php:attr:: thumbnail

The thumbnail view associate with the field.

.. php:attr:: thumnailRegion

The region in input template where to add the thumbnail view, default to AfterAfterInput region.

.. php:attr:: defaultSrc

The default image source to display to user, prior to uploading the images.

Callback
========

When adding an Upload or UploadImg field to a form, onUpload and onDelete callback must be define, otherwise the field simply throw an error::

    $img = $form->addField('img', ['UploadImg', ['defaultSrc' => './images/default.png', 'placeholder' => 'Click to add an image.']]);

    $img->onUpload(function ($files) {
        //callback action here...
    });

    $img->onDelete(function ($fileId) {
        //callback action here...
    });


onUpload
========

The onUpload callback get call as soon as the upload process is finished. This callback function receive the $_FILES request as function parameter.

The onUpload callback function is a good place to:

- ensure the file just uploaded is conform to the requirement;
- move file to a proper location on server or in cloud,
- save file property in db;
- setup a fileId that will be submit on form save,
- setup a file preview to display back to user,
- notify your user of the file upload process,

Example showing the onUpload callback on the UploadImg field::

    $img->onUpload(function ($files) use ($form, $img) {
        if ($files === 'error') {
            return $form->error('img', 'Error uploading image.');
        }

        //Do file processing here...

        $img->setThumbnailSrc('./images/'.$file_name);
        $img->setFileId('123456');

        // can also return a notifier.
        return new atk4\ui\jsNotify(['content' => 'File is uploaded!', 'color' => 'green']);
    });

When user submit the form, the field data value that will be submitted is the fileId set during the onUpload callback.
The fileId is set to file name by default if omitted::

    $form->onSubmit(function ($form) {
        // implement submission here
        return $form->success('Thanks for submitting file: '.$form->model['img']);
    });

onDelete
========

The onDelete callback get call when user click the delete button. This callback function receive the same fileId set during the onUpload callback as function parameter.

The onDelete callback function is a good place to:

- remove previously uploaded file from server or cloud,
- delete db entry according to the fileId,
- reset thumbnail preview,

Example showing the onDelete callback on the UploadImg field::

    $img->onDelete(function ($fileId) use ($img) {
        //reset thumbanil
        $img->clearThumbnail('./images/default.png');

        return new atk4\ui\jsNotify(['content' => $fileId.' has been removed!', 'color' => 'green']);
    });
