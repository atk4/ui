<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);
$img = $form->addControl('img', [Form\Control\UploadImage::class, ['defaultSrc' => '../images/default.png', 'placeholder' => 'Click to add an image.']]);
// $img->set('a_new_token', 'an-img-file-name');
// $img->setThumbnailSrc($app->cdn['atk'] . '/logo.png');

$control = $form->addControl('file', [Form\Control\Upload::class, ['accept' => ['.png', '.jpg']]]);

// $control->set('a_generated_token', 'a-file-name');
// $control->set('a_generated_token', null);

$img->onDelete(static function (string $fileId) use ($img) {
    $img->clearThumbnail();

    return new JsToast([
        'title' => 'Delete successfully',
        'message' => $fileId . ' has been removed',
        'class' => 'success',
    ]);
});

$img->onUpload(static function (array $postFile) use ($form, $img) {
    if ($postFile['error'] !== 0) {
        return $form->jsError('img', 'Error uploading image.');
    }

    $img->setThumbnailSrc($img->getApp()->cdn['atk'] . '/logo.png');
    $img->set('123456', $postFile['name'] . ' (token: 123456)'); // @phpstan-ignore-line

    // do file processing here...

    // this will get caught by JsCallback and show via modal
    // new Blabla();

    // JS Action can be return if using form, can return an error to form control directly
    // return $form->jsError('file', 'Unable to upload file.');

    // can also return a notifier
    return new JsToast([
        'title' => 'Upload success',
        'message' => 'Image is uploaded!',
        'class' => 'success',
    ]);
});

$control->onDelete(static function (string $fileId) {
    return new JsToast([
        'title' => 'Delete successfully',
        'message' => $fileId . ' has been removed',
        'class' => 'success',
    ]);
});

$control->onUpload(static function (array $postFile) use ($form, $control) {
    if ($postFile['error'] !== 0) {
        return $form->jsError('file', 'Error uploading file.');
    }
    $control->setFileId('a_token');

    $tmpFilePath = sys_get_temp_dir() . '/atk4-ui-upload-'
        . hash('sha256', random_bytes(64) . microtime(true) . $postFile['tmp_name']) . '.bin';
    try {
        move_uploaded_file($postFile['tmp_name'], $tmpFilePath);
        $data = file_get_contents($tmpFilePath);
    } finally {
        @unlink($tmpFilePath);
    }

    return new JsToast([
        'title' => 'Upload success',
        'message' => 'File is uploaded! (name: ' . $postFile['name'] . ', md5: ' . md5($data) . ')',
        'class' => 'success',
    ]);
});

$form->onSubmit(static function (Form $form) {
    // implement submission here
    return $form->jsSuccess('Thanks for submitting file: ' . $form->model->get('img') . ' / ' . $form->model->get('file'));
});
