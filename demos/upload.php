<?php

require 'init.php';

$form = $app->add('Form');

$img = $form->addField('file', ['UploadImg', ['defaultSrc' => './images/default.png']]);
$field = $form->addField('file1', ['Upload', ['accept' => ['.png', '.jpg']]]);

$img->onDelete(function ($fileName) use ($img) {
    $img->clearThumbnail('./images/default.png');

    return new atk4\ui\jsNotify(['content' => $fileName.' has been removed!', 'color' => 'green']);
});

$field->onDelete(function ($fileName) {
    return new atk4\ui\jsNotify(['content' => $fileName.' has been removed!', 'color' => 'green']);
});

$img->onUpload(function ($files) use ($form, $img) {
    if ($files === 'error') {
        return $form->error('file', 'Error uploading file.');
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
        return $form->error('file1', 'Error uploading file.');
    }

    return new atk4\ui\jsNotify(['content' => 'File is uploaded!', 'color' => 'green']);
});

$form->onSubmit(function ($form) {
    // implement submission here
    return $form->success('Thanks for submitting file: '.$form->model['file_id'].' / '.$form->model['file1']);
});
