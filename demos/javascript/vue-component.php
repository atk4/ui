<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\HtmlTemplate;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, ['Component', 'size' => 2, 'icon' => 'vuejs', 'subHeader' => 'UI view handle by Vue.js']);
\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// ****** Inline Edit *****************************

$model = new Country($app->db);
$model->loadAny();

$subHeader = 'Try me. I will restore value on "Escape" or save it on "Enter" or when field get blur after it has been changed.';
\Atk4\Ui\Header::addTo($app, ['Inline editing.', 'size' => 3, 'subHeader' => $subHeader]);

$inline_edit = \Atk4\Ui\Component\InlineEdit::addTo($app);
$inline_edit->setModel($model);

$inline_edit->onChange(function ($value) {
    $view = new \Atk4\Ui\Message();
    $view->invokeInit();
    $view->text->addParagraph('new value: ' . $value);

    return $view;
});

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// ****** ITEM SEARCH *****************************

$subHeader = 'Searching will reload the list of countries below with matching result.';
\Atk4\Ui\Header::addTo($app, ['Search using a Vue component', 'subHeader' => $subHeader]);

$model = new Country($app->db);

$lister_template = new HtmlTemplate('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{$end}{/}</div>');

$view = \Atk4\Ui\View::addTo($app);

$search = \Atk4\Ui\Component\ItemSearch::addTo($view, ['ui' => 'ui compact segment']);
$lister_container = \Atk4\Ui\View::addTo($view, ['template' => $lister_template]);
$lister = \Atk4\Ui\Lister::addTo($lister_container, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    ++$lister->ipp;
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
    if ($lister->ipp === $lister->model->limit[0]) {
        $lister->t_row->dangerouslySetHtml('end', '<div class="ui circular basic label"> ...</div>');
    }
});

$search->reload = $lister_container;
$lister->setModel($search->setModelCondition($model))->setLimit(50);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// ****** CREATING CUSTOM VUE USING EXTERNAL COMPONENT *****************************
\Atk4\Ui\Header::addTo($app, ['External Component', 'subHeader' => 'Creating component using an external component definition.']);

$app->requireJs('https://unpkg.com/vue-clock2@1.1.5/dist/vue-clock.min.js');

// Injecting template but normally you would create a template file.
$clock_template = new HtmlTemplate('
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
        // Register clock component from vue-clock2 to use with myClock.
        atk.vueService.getVue().component('clock', Clock.default);

        var myClock = {
          props : {clock: Array},
          data: function() {
            return {style : this.clock, currentIdx : 0}
          },
          mounted: function() {
            // add a listener for changing clock style.
            // this will listen to event '-clock-change-style' emit on the eventBus.
            atk.eventBus.on(this.\$root.\$el.id + '-clock-change-style', (payload) => {
                this.onChangeStyle();
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
$clock = \Atk4\Ui\View::addTo($app, ['template' => $clock_template]);
$clock->template->tryDangerouslySetHtml('script', $clock_script);

// passing some style to my-clock component.
$clock_style = [
    ['color' => '#4AB7BD', 'border' => '', 'bg' => 'none'],
    ['color' => '#FFFFFF', 'border' => 'none', 'bg' => '#E0DCFF'],
    ['color' => '', 'border' => 'none', 'bg' => 'radial-gradient(circle, #ecffe5, #fffbe1, #38ff91)'],
];

// creating vue using an external definition.
$clock->vue('my-clock', ['clock' => $clock_style], 'myClock');

$btn = \Atk4\Ui\Button::addTo($app, ['Change Style']);
$btn->on('click', $clock->jsEmitEvent($clock->name . '-clock-change-style'));
\Atk4\Ui\View::addTo($app, ['element' => 'p', 'I am not part of the component but I can still change style using the eventBus.']);
