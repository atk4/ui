<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Test 1 - Basic reloading
Header::addTo($app, ['Button reloading segment']);
$v = View::addTo($app, ['ui' => 'segment'])->set((string) random_int(1, 100));
Button::addTo($app, ['Reload random number'])
    ->on('click', new JsReload($v, [], new JsExpression('console.log(\'Output with afterSuccess\');')));

// Reload but keep custom changes
Header::addTo($app, ['Button reloading View without loosing original values']);
$v = View::addTo($app)->set((string) random_int(1, 1000));
$inputControl = Form\Control\Line::addTo($v);
View::addTo($app)->js(true, null, $inputControl)->find('input')->val('test ' . (string) random_int(1, 1000)); // simulate change by user

Button::addTo($app, ['Reload but keep custom changes'])->js('click', new JsExpression('{}', [
    'var jsRenderFunc = function () { ' . (new JsReload($v))->jsRender() . ' };'
    . 'var reloadUrl = ' . (new JsExpression('[]', [$v->jsUrl(['__atk_reload' => $v->name])]))->jsRender() . ';'
    . <<<'EOF'
        // jsRenderFunc(); // reload like with JsReload

        $.get(reloadUrl, null, function(data) {
            if (data.success !== true) {
                alert('Invalid reload response');
            }
            var newHtml = data.html;
            // var newAtkJs = data.atkjs; // ignore JS, compare html only
            var id = data.id;
            console.log('Reload triggered, ID: ' + id);

            var dd = new diffDOM.DiffDOM();
            var cloneDom = function (elem) {
                var virtualElem = document.createElement(elem.tagName);
                dd.apply(virtualElem, dd.diff(virtualElem, elem));
                return virtualElem;
            };
            if (window.manualChangesEndVdom === undefined) {
                window.previousVdom = new DOMParser().parseFromString(window.snapshotAfterLoad, 'text/html').getElementById(id); // always clone
                window.manualChangesStartVdom = cloneDom(window.previousVdom);
                window.manualChangesEndVdom = cloneDom(window.previousVdom);
            }
            var realElem = document.getElementById(id);

            // find new manual changes
            var newManualChanges = dd.diff(window.previousVdom, realElem);
            dd.apply(window.manualChangesEndVdom, newManualChanges);
            var allManualChanges = dd.diff(window.manualChangesStartVdom, window.manualChangesEndVdom);

            // find all new changes (from server)
            var newChanges = dd.diff(realElem, newHtml);

            // combine diffs and apply at once
            var changes = [];
            changes.push(...newChanges);
            changes.push(...allManualChanges); // manual changes are prioritized
            dd.apply(realElem, changes);
            window.previousVdom = cloneDom(realElem);

            console.log('all ok');
        }, 'json')
        EOF,
]));

// Test 2 - Reloading self
Header::addTo($app, ['JS-actions will be re-applied']);
$b2 = Button::addTo($app, ['Reload Myself']);
$b2->on('click', new JsReload($b2));

// Test 3 - avoid duplicate
Header::addTo($app, ['No duplicate JS bindings']);
$b3 = Button::addTo($app, ['Reload other button']);
$b4 = Button::addTo($app, ['Add one dot']);

$b4->on('click', $b4->js()->text(new JsExpression('[] + \'.\'', [$b4->js()->text()])));
$b3->on('click', new JsReload($b4));

// Test 3 - avoid duplicate
Header::addTo($app, ['Make sure nested JS bindings are applied too']);
$seg = View::addTo($app, ['ui' => 'segment']);

// add 3 counters
Counter::addTo($seg);
Counter::addTo($seg, ['40']);
Counter::addTo($seg, ['-20']);

// Add button to reload all counters
$bar = View::addTo($app, ['ui' => 'buttons']);
$b = Button::addTo($bar, ['Reload counter'])
    ->on('click', new JsReload($seg));

// Relading with argument
Header::addTo($app, ['We can pass argument to reloader']);

$v = View::addTo($app, ['ui' => 'segment'])->set($_GET['val'] ?? 'No value');

Button::addTo($app, ['Set value to "hello"'])
    ->on('click', new JsReload($v, ['val' => 'hello']));
Button::addTo($app, ['Set value to "world"'])
    ->on('click', new JsReload($v, ['val' => 'world']));

$val = Form\Control\Line::addTo($app, ['']);
$val->addAction(['Set Custom Value'])
    ->on('click', new JsReload($v, ['val' => $val->jsInput()->val()], $val->jsInput()->focus()));
