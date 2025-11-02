<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;
use Doctrine\DBAL\Platforms\MySQLPlatform;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);
$form->setEntity((new MultilineDelivery($app->db))->createEntity());
$form->onSubmit(static function (Form $form) {
    // save ContainsXxx data to JSON
    // https://github.com/atk4/data/blob/6.0.0/src/Reference/ContainsOne.php#L29-L40
    $form->entity->save();
    $form->entity->setNull($form->entity->idField);

    // fix Behat for MySQL with unordered/normalized JSON
    $data = $form->entity->get();
    if ($form->getApp()->db->getDatabasePlatform() instanceof MySQLPlatform) {
        assert($form->entity instanceof MultilineDelivery);
        $itemKey = $form->entity->fieldName()->item;
        if ($data[$itemKey] !== null) {
            uksort($data[$itemKey], static function ($a, $b) use ($form) {
                $fieldNamesOrder = array_flip(array_values(array_map(static fn ($v) => $v->getPersistenceName(), $form->entity->item->getFields())));

                return $fieldNamesOrder[$a] <=> $fieldNamesOrder[$b];
            });
        }
    }

    return new JsToast($form->getApp()->encodeJson($data));
});
