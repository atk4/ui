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

$lister_template = new atk4\ui\Template('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{$end}{/}</div>');

$view = $app->add('View');
$search = $view->add(['Component/ItemSearch', 'q' => $q, 'ui' => 'ui compact segment']);
$lister_container = $view->add(['View', 'template' => $lister_template]);
$lister = $lister_container->add('Lister', 'List')
            ->addHook('beforeRow', function ($l) {
                $l->ipp++;
                $l->current_row['iso'] = strtolower($l->current_row['iso']);
                if ($l->ipp === $l->model->limit[0]) {
                    $l->t_row->setHtml('end', '<div class="ui circular basic label"> ...</div>');
                }
            });

$search->reload = $lister_container;
$lister->setModel($search->setModelCondition($m))->setLimit(50);

$app->add(['ui' => 'divider']);

//****** CREATING CUSTOM VUE USING EXTERNAL COMPONENT *****************************
$app->add(['Header', 'External Component', 'subHeader' => 'Creating component using an external component definition.']);

$app->requireJS('https://unpkg.com/vue-clock2@1.1.5/dist/vue-clock.min');

// Injecting template but normally you would create a template file.
$clock_template = new \atk4\ui\Template('
    <div id="{$_id}" class="ui center aligned segment">
    <my-clock inline-template v-bind="initData">
        <div>
            <clock :color="color" :border="border" :bg="bg"></clock>
            <div class="ui basic segment inline"><div class="ui button primary" @click="onChangeStyle">Change Style</div></div>
        </div>
    </my-clock>
    </div>{$script}');

// Injecting script but normally you would create a separate js file and include it in your page.
// This is the vue component definition. It is also using another external vue component 'vue-clock2'
$clock_script = "
    <script>
        //Register clock component from vue-clock2 to use with myClock.
        atk.vueService.getVue().component('clock', Clock.default);

        var myClock = {
          props : {clock: Array},
          data: function() {
            return {style : this.clock, currentIdx : 0}
          },
          created: function() {
            // add a listener for changing clock style.
            // this will listen to event 'update-style' emit on the eventBus.
            atk.vueService.eventBus.\$on('change-style', (data) => {
              // make sure we are talking to the right component.
              if (this.\$parent.\$el.id === data.id) {
                this.onChangeStyle();
              }
            });
          },
          computed: {
            color: function() {
              return this.style[this.currentIdx].color
            },
            border: function() {
              return this.style[this.currentIdx].border
            },
            bg: function() {
              return this.style[this.currentIdx].bg
            }
          },
          name: 'my-clock',
          methods: {
            onChangeStyle: function() {
              this.currentIdx = this.currentIdx + 1;
              if (this.currentIdx > this.style.length - 1) {
                this.currentIdx = 0;
              }
            }
          },
        } 
    </script>";

// Creating the clock view and injecting js.
$clock = $app->add(['View', 'template' => $clock_template]);
$clock->template->trySetHtml('script', $clock_script);

// passing some style to my-clock component.
$clock_style = [
    ['color' => '#4AB7BD', 'border' => '', 'bg' => 'none'],
    ['color' => '#FFFFFF', 'border' => 'none', 'bg' => '#E0DCFF'],
    ['color' => '', 'border' => 'none', 'bg' => 'radial-gradient(circle, #ecffe5, #fffbe1, #38ff91)'],
];

// creating vue using an external definition.
$clock->vue('my-clock', ['clock' => $clock_style], 'myClock');

$btn = $app->add(['Button', 'Change Style']);
$btn->on('click', $clock->jsVueEmit('change-style'));
$app->add(['View', 'element' => 'p', 'I am not part of the component but I can still change style using the eventBus.']);
