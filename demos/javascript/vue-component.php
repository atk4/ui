<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Lister;
use Atk4\Ui\Message;
use Atk4\Ui\View;
use Atk4\Ui\VueComponent;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Component', 'size' => 2, 'icon' => 'vuejs', 'subHeader' => 'UI view handle by Vue.js']);
View::addTo($app, ['ui' => 'divider']);

// InlineEdit

$entity = (new Country($app->db))
    ->setOrder(Country::hinting()->fieldName()->id)
    ->loadAny();

$subHeader = 'Try me. I will restore value on "Escape" or save it on "Enter" or when field get blur after it has been changed.';
Header::addTo($app, ['Inline editing.', 'size' => 3, 'subHeader' => $subHeader]);

View::addTo($app)->set('with autoSave');
$inlineEditWithAutoSave = VueComponent\InlineEdit::addTo($app, ['autoSave' => true]);
$inlineEditWithAutoSave->fieldName = $entity->fieldName()->name;
$inlineEditWithAutoSave->setModel($entity);

View::addTo($app)->set('with onChange callback');
$inlineEditWithCallback = VueComponent\InlineEdit::addTo($app);
$inlineEditWithCallback->fieldName = $entity->fieldName()->name;
$inlineEditWithCallback->setModel($entity);
$inlineEditWithCallback->onChange(static function (string $value) use ($app) {
    $view = new Message();
    $view->setApp($app);
    $view->invokeInit();
    $view->text->addParagraph('new value: ' . $value);

    return $view;
});

View::addTo($app, ['ui' => 'divider']);

// ITEM SEARCH

$subHeader = 'Searching will reload the list of countries below with matching result.';
Header::addTo($app, ['Search using a Vue component', 'subHeader' => $subHeader]);

$model = new Country($app->db);

$listerTemplate = new HtmlTemplate('<div {$attributes}>{List}<div class="ui icon label"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{$end}{/}</div>');

$view = View::addTo($app);

$search = VueComponent\ItemSearch::addTo($view, ['ui' => 'compact segment']);
$listerContainer = View::addTo($view, ['template' => $listerTemplate]);
$lister = Lister::addTo($listerContainer, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);

    ++$lister->ipp;
    if ($lister->ipp === $lister->model->limit[0]) {
        $lister->tRow->dangerouslySetHtml('end', '<div class="ui circular basic label"> ...</div>');
    }
});

$search->reload = $listerContainer;
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

// injecting template but normally you would create a template file
$clockTemplate = new HtmlTemplate(<<<'EOF'
    <div class="ui center aligned segment" {$attributes}>
        <my-clock v-bind="initData"></my-clock>
    </div>
    {$script}
    EOF);

// injecting script but normally you would create a separate JS file and include it in your page
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
            // add a listener for changing clock style
            // this will listen to event '-clock-change-style' emit on the eventBus
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

// creating the clock view and injecting JS
$clock = View::addTo($app, ['template' => $clockTemplate]);
$clock->template->dangerouslySetHtml('script', $clockScript);

// passing some style to my-clock component
$clockStyle = [
    ['color' => 'maroon', 'background' => '', 'textShadow' => '5px 5px 10px teal'],
    ['color' => 'white', 'background' => '', 'textShadow' => '0px 0px 10px blue'],
    ['color' => '', 'background' => 'radial-gradient(ellipse at center, rgba(0, 255, 0, 0.25) 0%,rgba(0, 255, 0, 0) 50%)', 'textShadow' => ''],
];

// creating vue using an external definition
$clock->vue('my-clock', ['styles' => $clockStyle], new JsExpression('myClock'));

$button = Button::addTo($app, ['Change Style']);
$button->on('click', $clock->jsEmitEvent($clock->name . '-clock-change-style'));
View::addTo($app, ['element' => 'p', 'I am not part of the component but I can still change style using the eventBus.']);
