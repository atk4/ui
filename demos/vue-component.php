<?php

require 'init.php';
require 'database.php';

$app->add(['Header', 'Component', 'size' => 2, 'icon' => 'vuejs', 'subHeader' => 'UI view handle by Vue.js']);
$app->add(['ui' => 'divider']);

//****** Inline Edit *****************************

$m = new Country($db);
$m->loadAny();

$subHeader = 'Try me. I will restore value on "Escape" or save it on "Enter" or when field get blur after it has been changed.';
$app->add(['Header', 'Inline editing.', 'size' => 3, 'subHeader' => $subHeader]);

$inline_edit = $app->add(['Component/InlineEdit']);
$inline_edit->setModel($m);

$inline_edit->onChange(function ($value) {
    $view = new \atk4\ui\Message();
    $view->init();
    $view->text->addParagraph('new value: '.$value);

    return $view;
});

$app->add(['ui' => 'divider']);

//****** ITEM SEARCH *****************************

$subHeader = 'Searching will reload the list of countries below with matching result.';
$app->add(['Header', 'Search using a Vue component', 'subHeader' => $subHeader]);

$m = new Country($db);

$lister_template = new atk4\ui\Template('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{/}</div>');

$view = $app->add('View');
$search = $view->add(['Component/ItemSearch', 'q' => $q, 'ui' => 'ui compact segment']);
$lister_container = $view->add(['View', 'template' => $lister_template]);
$lister = $lister_container->add('Lister', 'List')
            ->addHook('beforeRow', function ($l) {
                $l->current_row['iso'] = strtolower($l->current_row['iso']);
            });

$search->reload = $lister_container;
$lister->setModel($search->setModelCondition($m))->setLimit(100);

//****** CREATING CUSTOM VUE USING EXTERNAL COMPONENT *****************************

$app->requireJS('https://unpkg.com/vue-clock2@1.1.5/dist/vue-clock.min');

$clock_template =  new \atk4\ui\Template('<div id="{$_id}" class="ui center aligned segment"><my-clock inline-template v-bind="item"><div><clock :color="color" :border="border" :bg="bg"></clock></div></my-clock></div>{$script}');

$clock_script = "
    <script>
        //Register clock component from vue-clock2 to use with myClock.
        atk.vueService.getVue().component('clock', Clock.default);

        var myClock = {
          props : {clock: Object},
          data: function() {
            return {color : this.clock.color, border: this.clock.border, bg: this.clock.bg}
          },
          name: 'my-clock',
        } 
    </script>";

$clock = $app->add(['View', 'template' => $clock_template]);
$clock->template->trySetHtml('script', $clock_script);

$clock_attr = [
    'color' => '#4AB7BD',
    'border' => '',
    'bg' => 'none'
];

$clock->vue('my-clock', ['clock' => $clock_attr], 'myClock');
