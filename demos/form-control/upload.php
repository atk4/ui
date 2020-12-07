<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);
$img = $form->addControl('img', [Form\Control\UploadImage::class, ['defaultSrc' => '../images/default.png', 'placeholder' => 'Click to add an image.']]);
$img->cb->appSticky = true;
//$img->set('a_new_token', 'an-img-file-name');
//$img->setThumbnailSrc('./images/logo.png');

$control = $form->addControl('file', [Form\Control\Upload::class, ['accept' => ['.png', '.jpg']]]);

//$control->set('a_generated_token', 'a-file-name');
//$control->set('a_generated_token');

$img->onDelete(function ($fileId) use ($img) {
    $img->clearThumbnail('./images/default.png');

    return new \Atk4\Ui\JsToast([
        'title' => 'Delete successfully',
        'message' => $fileId . ' has been removed',
        'class' => 'success',
    ]);
});

$img->onUpload(function ($postFile) use ($form, $img) {
    if ($postFile['error'] !== 0) {
        return $form->error('img', 'Error uploading image.');
    }

    $img->setThumbnailSrc('./images/logo.png');
    $img->set('123456', $postFile['name'] . ' (token: 123456)');

    // Do file processing here...

    // This will get caught by JsCallback and show via modal.
    // new Blabla();

    // js Action can be return.
    // if using form, can return an error to form control directly.
    // return $form->error('file', 'Unable to upload file.');

    // can also return a notifier.
    return new \Atk4\Ui\JsToast([
        'title' => 'Upload success',
        'message' => 'Image is uploaded!',
        'class' => 'success',
    ]);
});

$control->onDelete(function ($fileId) {
    return new \Atk4\Ui\JsToast([
        'title' => 'Delete successfully',
        'message' => $fileId . ' has been removed',
        'class' => 'success',
    ]);
});

$control->onUpload(function ($files) use ($form, $control) {
    if ($files === 'error') {
        return $form->error('file', 'Error uploading file.');
    }
    $control->setFileId('a_token');

    return new \Atk4\Ui\JsToast([
        'title' => 'Upload success',
        'message' => 'File is uploaded!',
        'class' => 'success',
    ]);
});

$form->onSubmit(function (Form $form) {
    // implement submission here
    return $form->success('Thanks for submitting file: ' . $form->model->get('img') . ' / ' . $form->model->get('file'));
});
