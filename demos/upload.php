<?php

require 'init.php';

$form = $app->add('Form');

$field = $form->addField('file', ['Upload']);
$field1 = $form->addField('file1', ['Upload', ['accept' => ['.png', '.jpg']]]);


$field->onDelete(function ($fileName) {
    return new atk4\ui\jsNotify(['content' => $fileName.' has been removed!','color' => 'green']);
});

$field1->onDelete(function ($fileName) {
    return new atk4\ui\jsNotify(['content' => $fileName.' has been removed!','color' => 'green']);
});

$field->onUpload(function ($files) use ($form, $field) {
    if ($files === 'error') {
        return $form->error('file', 'Error uploading file.');
    }
    //Do file processing here...

   /* This will get caught by jsCallback and show via modal. */
   //new Blabla();

   /* js Action can be return. */
   //if using form, can return an error to form field directly.
   //return $form->error('file', 'Unable to upload file.');

   // can also return a notifier.
   return new atk4\ui\jsNotify(['content' => 'File is uploaded!', 'color' => 'green']);

});

$field1->onUpload(function ($files) use ($form, $field) {

    if ($files === 'error') {
        return $form->error('file1', 'Error uploading file.');
    }
    return new atk4\ui\jsNotify(['content' => 'File is uploaded!', 'color' => 'green']);

});



$form->onSubmit(function ($form) {
    // implement submission here
    return $form->success('Thanks for submitting file: '.$form->model['file'].' / '.$form->model['file1']);
});
