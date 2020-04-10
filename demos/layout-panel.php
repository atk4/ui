<?php

require_once __DIR__ . '/init.php';

$panel = $app->layout->addRightPanel(new \atk4\ui\Panel\Slide());
$panel_1 = $app->layout->addRightPanel(new \atk4\ui\Panel\Slide());
$panel_2 = $app->layout->addRightPanel(new \atk4\ui\Panel\Slide());

\atk4\ui\Text::addTo($panel, ['This panel contains only static content.']);
$btn = \atk4\ui\Button::addTo($app, ['Open Static']);
$btn->on('click', $panel->jsOpen(new \atk4\ui\jQuery()));



\atk4\ui\View::addTo($app, ['ui' => 'divider']);

\atk4\ui\Text::addTo($panel_1, ['This panel will load content dynamically below according to button select on the right.']);
$btn = \atk4\ui\Button::addTo($app, ['Button 1']);
$btn->js(true)->data('btn', '1');
$btn->on('click', $panel_1->jsOpen(new \atk4\ui\jQuery(), ['btn'], 'orange'));

$btn = \atk4\ui\Button::addTo($app, ['Button 2']);
$btn->js(true)->data('btn', '2');
$btn->on('click', $panel_1->jsOpen(new \atk4\ui\jQuery(), ['btn'], 'orange'));

$panel_1->onOpen(function($p) {
    $btn_number = $_GET['btn'] ?? null;
    $text =  'You loaded panel content using button #' . $btn_number;
    \atk4\ui\Message::addTo($p, ['Panel 1', 'text' => $text]);
});


\atk4\ui\View::addTo($app, ['ui' => 'divider']);

$icon = \atk4\ui\Icon::addTo($app, ['big cog'])->addStyle('cursor', 'pointer');
$icon->on('click', $panel_2->jsOpen(new \atk4\ui\jQuery()));
$panel_2->addConfirmation('Changes will be lost. Are you sure?');

$msg = \atk4\ui\Text::addTo($panel_2);
$msg->addParagraph('Try to change dropdown value and close without saving!');

$panel_2->onOpen(function($p) {
    $f = \atk4\ui\Form::addTo($p);
    $f->addField('name', ['DropDown', 'values' => ['1' => 'Option 1', '2' => 'Option 2']])
      ->set('1')
      ->onChange($p->owner->jsDisplayWarning(true));

    $f->onSubmit(function ($f) use ($p) {
       return [
          new \atk4\ui\jsToast('Saved'),
          $p->owner->jsDisplayWarning(false),
       ];
    });
});
