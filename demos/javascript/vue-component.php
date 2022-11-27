<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Lister;
use Atk4\Ui\Message;
use Atk4\Ui\View;
use Atk4\Ui\VueComponent;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Component', 'size' => 2, 'icon' => 'vuejs', 'subHeader' => 'UI view handle by Vue.js']);
View::addTo($app, ['ui' => 'divider']);

// Inline Edit

$model = new Country($app->db);
$model = $model->loadAny();

$subHeader = 'Try me. I will restore value on "Escape" or save it on "Enter" or when field get blur after it has been changed.';
Header::addTo($app, ['Inline editing.', 'size' => 3, 'subHeader' => $subHeader]);

$inline_edit = VueComponent\InlineEdit::addTo($app);
$inline_edit->fieldName = $model->fieldName()->name;
$inline_edit->setModel($model);

$inline_edit->onChange(function (string $value) {
    $view = new Message();
    $view->invokeInit();
    $view->text->addParagraph('new value: ' . $value);

    return $view;
});

View::addTo($app, ['ui' => 'divider']);

// ITEM SEARCH

$subHeader = 'Searching will reload the list of countries below with matching result.';
Header::addTo($app, ['Search using a Vue component', 'subHeader' => $subHeader]);

$model = new Country($app->db);

$lister_template = new HtmlTemplate('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{$end}{/}</div>');

$view = View::addTo($app);

$search = VueComponent\ItemSearch::addTo($view, ['ui' => 'ui compact segment']);
$lister_container = View::addTo($view, ['template' => $lister_template]);
$lister = Lister::addTo($lister_container, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);

    ++$lister->ipp;
    if ($lister->ipp === $lister->model->limit[0]) {
        $lister->tRow->dangerouslySetHtml('end', '<div class="ui circular basic label"> ...</div>');
    }
});

$search->reload = $lister_container;
$search->setModelCondition($model);
$model->setLimit(50);
$lister->setModel($model);

View::addTo($app, ['ui' => 'divider']);

// CREATING CUSTOM VUE USING EXTERNAL COMPONENT

Header::addTo($app, ['External Component', 'subHeader' => 'Creating component using an external component definition.']);

$app->html->template->dangerouslyAppendHtml('Head', $app->getTag('script', [], <<<'EOF'
    window.vueDemoClock = {
        template: '<div :style="{ fontSize: \'80px\', padding: \'25px\', color: color, textShadow: textShadow, background: background }">{{ time }}</div>',
        props: ['color', 'textShadow', 'background'],
        data: function () {
            return {
                time: '-',
            };
        },
        mounted: function () {
            this.interval = setInterval(this.updateClock, 100);
        },
        beforeUnmount: function () {
            clearInterval(this.interval);
        },
        methods: {
            updateClock: function () {
                const date = new Date();
                this.time = date.getHours().toString().padStart(2, '0')
                    + ':' + date.getMinutes().toString().padStart(2, '0')
                    + ':' + date.getSeconds().toString().padStart(2, '0');
            },
        },
    };
    EOF));

// Injecting template but normally you would create a template file.
$clockTemplate = new HtmlTemplate(<<<'EOF'
    <div id="{$_id}" class="ui center aligned segment">
        <my-clock v-bind="initData"></my-clock>
    </div>
    {$script}
    EOF);

// Injecting script but normally you would create a separate js file and include it in your page.
$clockScript = $app->getTag('script', [], <<<'EOF'
    let myClock = {
        template: `
            <div>
                <demo-clock :color="color" :text-shadow="textShadow" :background="background"></demo-clock>
                <div class="ui basic segment inline"><div class="ui button primary" @click="onChangeStyle">Change Style</div></div>
            </div>`,
        components: {
            'demo-clock': window.vueDemoClock,
        },
        props: { styles: Array },
        data: function () {
            return { style: this.styles, currentIndex: 0 };
        },
        mounted: function () {
            // add a listener for changing clock style.
            // this will listen to event '-clock-change-style' emit on the eventBus.
            atk.eventBus.on(this.$root.$el.parentElement.id + '-clock-change-style', (payload) => {
                this.onChangeStyle();
            });
        },
        computed: {
            color: function () {
                return this.style[this.currentIndex].color;
            },
            textShadow: function () {
                return this.style[this.currentIndex].textShadow;
            },
            background: function () {
                return this.style[this.currentIndex].background;
            },
        },
        name: 'my-clock',
        methods: {
            onChangeStyle: function () {
                this.currentIndex++;
                if (this.currentIndex >= this.style.length) {
                    this.currentIndex = 0;
                }
            },
        },
    };
    EOF);

// Creating the clock view and injecting js.
$clock = View::addTo($app, ['template' => $clockTemplate]);
$clock->template->dangerouslySetHtml('script', $clockScript);

// passing some style to my-clock component.
$clockStyle = [
    ['color' => 'maroon', 'background' => '', 'textShadow' => '5px 5px 10px teal'],
    ['color' => 'white', 'background' => '', 'textShadow' => '0px 0px 10px blue'],
    ['color' => '', 'background' => 'radial-gradient(ellipse at center, rgba(0, 255, 0, 0.25) 0%,rgba(0, 255, 0, 0) 50%)', 'textShadow' => ''],
];

// creating vue using an external definition.
$clock->vue('my-clock', ['styles' => $clockStyle], new JsExpression('myClock'));

$btn = Button::addTo($app, ['Change Style']);
$btn->on('click', $clock->jsEmitEvent($clock->name . '-clock-change-style'));
View::addTo($app, ['element' => 'p', 'I am not part of the component but I can still change style using the eventBus.']);
