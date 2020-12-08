<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$grid = \Atk4\Ui\Grid::addTo($app);
$model = new CountryLock($app->db);
$model->addUserAction('test', function ($model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

// Delete is already prevent by our lock Model, just simulating it.
$ex = new \Atk4\Ui\UserAction\JsCallbackExecutor();
$ex->onHook(\Atk4\Ui\UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function () {
    return [
        (new \Atk4\Ui\Jquery())->closest('tr')->transition('fade left'),
        new \Atk4\Ui\JsToast('Simulating delete in demo mode.'),
    ];
});
$model->getUserAction('delete')->ui['executor'] = $ex;

$grid->setModel($model);

// Adding Quicksearch on Name field using auto query.
$grid->addQuickSearch(['name'], true);

$grid->menu->addItem(['Add Country', 'icon' => 'add square'], new \Atk4\Ui\JsExpression('alert(123)'));
$grid->menu->addItem(['Re-Import', 'icon' => 'power'], new \Atk4\Ui\JsReload($grid));
$grid->menu->addItem(['Delete All', 'icon' => 'trash', 'red active']);

$grid->addColumn(null, [\Atk4\Ui\Table\Column\Template::class, 'hello<b>world</b>']);

$modalviewvp = \Atk4\Ui\VirtualPage::addTo($grid->getApp()); // Virtual Page with customized content for Grid button
$modalviewvp->set(function ($frame) use ($grid){
    \Atk4\Ui\Message::addTo($frame, ['Attention - you are doing something dangerous!']);
    $modalmodel = clone $grid->model;
    $modalmodel->load($frame->getApp()->stickyGet('mid'));

    $modalform = \Atk4\Ui\Form::addTo($frame);
    $modalform->setModel($modalmodel);

    $modalform->onSubmit(function($form) use ($grid) {
        $form->model->save();
        
        $modal_chain = new \atk4\ui\jQuery('.atk-modal'); // Close the modal form? Is there a more elegant way?
        $modal_chain->modal('hide');
        
        return [$modal_chain,
            new \Atk4\Ui\JsToast([
                'title'   => 'Save form data.',
                'message' => 'Message is saved.',
                'class'   => 'success',
            ]),
            new \Atk4\Ui\JsReload($grid),
            
        ];
    });
});
    
$modal = new \Atk4\Ui\JsModal('Edit', $modalviewvp->getUrl('cut'), ['mid' => $grid->table->jsRow()->data('id')]);
$grid->addActionButton('Modal form in VP', $modal);

$grid->addActionButton(['icon'=>'envelope'], function ($v, $id) use ($grid) {
    
    $model = (clone $grid->model)->load($id);
    $countryname =  $model->get('name');
    $model->set('name', lcfirst($countryname));
    $model->save();
    return [ new \Atk4\Ui\JsReload($grid),
        new \Atk4\Ui\JsToast([
            'title'    => 'Message',
            'message'  => 'Lower cased country '.$countryname. ' with id '. $id,
            'position' => 'bottom right',
            'class'   => 'success',
        ])];
});

$grid->addActionButton('test');

$grid->addActionButton('Say HI', function ($j, $id) use ($grid) {
    return 'Loaded "' . $grid->model->load($id)->get('name') . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => [\Atk4\Ui\Icon::class, 'external']], 'Modal Test', function ($p, $id) {
    \Atk4\Ui\Message::addTo($p, ['Clicked on ID=' . $id]);
});

    $grid->addActionButton(['icon' => 'delete'], $model->getUserAction('delete'));

$sel = $grid->addSelection();
$grid->menu->addItem('show selection')->on('click', new \Atk4\Ui\JsExpression(
    'alert("Selected: "+[])',
    [$sel->jsChecked()]
));

// Setting ipp with an array will add an ItemPerPageSelector to paginator.
$grid->setIpp([10, 25, 50, 100]);
