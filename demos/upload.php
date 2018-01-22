<?php

require 'init.php';

$form = $app->add('Form');

$img = $form->addField('img', ['UploadImg', ['defaultSrc' => './images/default.png', 'placeholder' => 'Click to add an image.']]);
$field = $form->addField('file', ['Upload', ['accept' => ['.png', '.jpg']]]);

$img->onDelete(function ($fileId) use ($img) {
    $img->clearThumbnail('./images/default.png');

    return new atk4\ui\jsNotify(['content' => $fileId.' has been removed!', 'color' => 'green']);
});

$field->onDelete(function ($fileId) {
    return new atk4\ui\jsNotify(['content' => $fileId.' has been removed!', 'color' => 'green']);
});

$img->onUpload(function ($files) use ($form, $img) {
    if ($files === 'error') {
        return $form->error('img', 'Error uploading image.');
    }

    $img->setThumbnailSrc('./images/logo.png');
    $img->setFileId('123456');
    //Do file processing here...

    /* This will get caught by jsCallback and show via modal. */
    //new Blabla();

    /* js Action can be return. */
    //if using form, can return an error to form field directly.
    //return $form->error('file', 'Unable to upload file.');

    // can also return a notifier.
    return new atk4\ui\jsNotify(['content' => 'File is uploaded!', 'color' => 'green']);
});

$field->onUpload(function ($files) use ($form, $img) {
    if ($files === 'error') {
        return $form->error('file', 'Error uploading file.');
    }

    return new atk4\ui\jsNotify(['content' => 'File is uploaded!', 'color' => 'green']);
});

$form->onSubmit(function ($form) {
    // implement submission here
    return $form->success('Thanks for submitting file: '.$form->model['img'].' / '.$form->model['file']);
});
