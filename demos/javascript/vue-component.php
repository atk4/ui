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
$model = $model->loadAny();

$subHeader = 'Try me. I will restore value on "Escape" or save it on "Enter" or when field get blur after it has been changed.';
\Atk4\Ui\Header::addTo($app, ['Inline editing.', 'size' => 3, 'subHeader' => $subHeader]);

$inline_edit = \Atk4\Ui\Component\InlineEdit::addTo($app);
$inline_edit->fieldName = $model->fieldName()->name;
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

$lister_template = new HtmlTemplate('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{$end}{/}</div>');

$view = \Atk4\Ui\View::addTo($app);

$search = \Atk4\Ui\Component\ItemSearch::addTo($view, ['ui' => 'ui compact segment']);
$lister_container = \Atk4\Ui\View::addTo($view, ['template' => $lister_template]);
$lister = \Atk4\Ui\Lister::addTo($lister_container, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
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

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// ****** CREATING CUSTOM VUE USING EXTERNAL COMPONENT *****************************
\Atk4\Ui\Header::addTo($app, ['External Component', 'subHeader' => 'Creating component using an external component definition.']);

// same as $app->requireJs('https://unpkg.com/vue-clock2@1.1.5/dist/vue-clock.min.js');
// for Behat testing without internet access
$app->requireJs('data:application/javascript;base64,IWZ1bmN0aW9uKHQsZSl7Im9iamVjdCI9PXR5cGVvZiBleHBvcnRzJiYib2JqZWN0Ij09dHlwZW9mIG1vZHVsZT9tb2R1bGUuZXhwb3J0cz1lKCk6ImZ1bmN0aW9uIj09dHlwZW9mIGRlZmluZSYmZGVmaW5lLmFtZD9kZWZpbmUoIkNsb2NrIixbXSxlKToib2JqZWN0Ij09dHlwZW9mIGV4cG9ydHM/ZXhwb3J0cy5DbG9jaz1lKCk6dC5DbG9jaz1lKCl9KHRoaXMsZnVuY3Rpb24oKXtyZXR1cm4gZnVuY3Rpb24odCl7ZnVuY3Rpb24gZShyKXtpZihvW3JdKXJldHVybiBvW3JdLmV4cG9ydHM7dmFyIG49b1tyXT17aTpyLGw6ITEsZXhwb3J0czp7fX07cmV0dXJuIHRbcl0uY2FsbChuLmV4cG9ydHMsbixuLmV4cG9ydHMsZSksbi5sPSEwLG4uZXhwb3J0c312YXIgbz17fTtyZXR1cm4gZS5tPXQsZS5jPW8sZS5pPWZ1bmN0aW9uKHQpe3JldHVybiB0fSxlLmQ9ZnVuY3Rpb24odCxvLHIpe2Uubyh0LG8pfHxPYmplY3QuZGVmaW5lUHJvcGVydHkodCxvLHtjb25maWd1cmFibGU6ITEsZW51bWVyYWJsZTohMCxnZXQ6cn0pfSxlLm49ZnVuY3Rpb24odCl7dmFyIG89dCYmdC5fX2VzTW9kdWxlP2Z1bmN0aW9uKCl7cmV0dXJuIHQuZGVmYXVsdH06ZnVuY3Rpb24oKXtyZXR1cm4gdH07cmV0dXJuIGUuZChvLCJhIixvKSxvfSxlLm89ZnVuY3Rpb24odCxlKXtyZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKHQsZSl9LGUucD0iL2Rpc3QvIixlKGUucz0yKX0oW2Z1bmN0aW9uKHQsZSxvKXtvKDUpO3ZhciByPW8oOCkobygxKSxvKDkpLCJkYXRhLXYtN2UzZjcxMjYiLG51bGwpO3QuZXhwb3J0cz1yLmV4cG9ydHN9LGZ1bmN0aW9uKHQsZSxvKXsidXNlIHN0cmljdCI7T2JqZWN0LmRlZmluZVByb3BlcnR5KGUsIl9fZXNNb2R1bGUiLHt2YWx1ZTohMH0pLGUuZGVmYXVsdD17ZGF0YTpmdW5jdGlvbigpe3JldHVybnt0aW1lTGlzdDpbMTIsMSwyLDMsNCw1LDYsNyw4LDksMTAsMTFdLHRyYW5zZm9ybToic2NhbGUoMSkiLGhvdXJSb3RhdGU6InJvdGF0ZXooMGRlZykiLG1pbnV0ZVJvdGF0ZToicm90YXRleigwZGVnKSIsc2Vjb25kUm90YXRlOiJyb3RhdGV6KDBkZWcpIn19LHByb3BzOlsidGltZSIsImNvbG9yIiwiYm9yZGVyIiwiYmciLCJzaXplIl0sY29tcHV0ZWQ6e2Nsb2NrU3R5bGU6ZnVuY3Rpb24oKXtyZXR1cm57aGVpZ2h0OnRoaXMuc2l6ZSx3aWR0aDp0aGlzLnNpemUsY29sb3I6dGhpcy5jb2xvcixib3JkZXI6dGhpcy5ib3JkZXIsYmFja2dyb3VuZDp0aGlzLmJnfX19LHdhdGNoOnt0aW1lOmZ1bmN0aW9uKCl7dGhpcy5zaG93KCl9fSxtZXRob2RzOntzaG93OmZ1bmN0aW9uKCl7dmFyIHQ9dGhpczt0aGlzLnNob3dUaW1lKCksdGhpcy5fdGltZXImJmNsZWFySW50ZXJ2YWwodGhpcy5fdGltZXIpLHRoaXMudGltZXx8KHRoaXMuX3RpbWVyPXNldEludGVydmFsKGZ1bmN0aW9uKCl7dC5zaG93VGltZSgpfSwxZTMpKX0sc2hvd1RpbWU6ZnVuY3Rpb24oKXt2YXIgdD12b2lkIDA7aWYodGhpcy50aW1lKXQ9dGhpcy50aW1lLnNwbGl0KCI6Iik7ZWxzZXt2YXIgZT1uZXcgRGF0ZTt0PVtlLmdldEhvdXJzKCksZS5nZXRNaW51dGVzKCksZS5nZXRTZWNvbmRzKCldfXZhciBvPSt0WzBdO289bz4xMT9vLTEyOm87dmFyIHI9K3RbMV0sbj0rdFsyXXx8MCxhPTMwKm8rNipyLzM2MCozMCxpPTYqcixzPTYqbjt0aGlzLmhvdXJSb3RhdGU9InJvdGF0ZXooIithKyJkZWcpIix0aGlzLm1pbnV0ZVJvdGF0ZT0icm90YXRleigiK2krImRlZykiLHRoaXMuc2Vjb25kUm90YXRlPSJyb3RhdGV6KCIrcysiZGVnKSJ9fSxtb3VudGVkOmZ1bmN0aW9uKCl7dmFyIHQ9dGhpcy4kZWwuY2xpZW50V2lkdGgvMTIwO3Q9dD4zPzM6dCx0aGlzLnRyYW5zZm9ybT0ic2NhbGUoIit0KyIpIix0aGlzLnNob3coKX0sZGVzdHJveWVkOmZ1bmN0aW9uKCl7dGhpcy5fdGltZXImJmNsZWFySW50ZXJ2YWwodGhpcy5fdGltZXIpfX19LGZ1bmN0aW9uKHQsZSxvKXsidXNlIHN0cmljdCI7T2JqZWN0LmRlZmluZVByb3BlcnR5KGUsIl9fZXNNb2R1bGUiLHt2YWx1ZTohMH0pO3ZhciByPW8oMCksbj1mdW5jdGlvbih0KXtyZXR1cm4gdCYmdC5fX2VzTW9kdWxlP3Q6e2RlZmF1bHQ6dH19KHIpO2UuZGVmYXVsdD1uLmRlZmF1bHQsInVuZGVmaW5lZCIhPXR5cGVvZiB3aW5kb3cmJndpbmRvdy5WdWUmJndpbmRvdy5WdWUuY29tcG9uZW50KCJjbG9jayIsbi5kZWZhdWx0KX0sZnVuY3Rpb24odCxlLG8pe2U9dC5leHBvcnRzPW8oNCkoKSxlLnB1c2goW3QuaSwnLmNsb2NrW2RhdGEtdi03ZTNmNzEyNl17cG9zaXRpb246cmVsYXRpdmU7ZGlzcGxheTppbmxpbmUtYmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlO3dpZHRoOjE1MHB4O2hlaWdodDoxNTBweDtib3JkZXI6MnB4IHNvbGlkO2JvcmRlci1yYWRpdXM6MTAwJTt0ZXh0LWFsaWduOmNlbnRlcjtmb250LXNpemU6MTRweH0uY2xvY2sgLmhvdXJbZGF0YS12LTdlM2Y3MTI2XXtwb3NpdGlvbjphYnNvbHV0ZTt0b3A6MDtsZWZ0OjUwJTtkaXNwbGF5OmJsb2NrO3dpZHRoOjIwcHg7aGVpZ2h0OjUwJTttYXJnaW4tbGVmdDotMTBweDtwYWRkaW5nLXRvcDo0JTtmb250LXdlaWdodDo0MDA7dHJhbnNmb3JtLW9yaWdpbjpib3R0b207dXNlci1zZWxlY3Q6bm9uZTtib3gtc2l6aW5nOmJvcmRlci1ib3h9LmNsb2NrIC5ob3VyPnNwYW5bZGF0YS12LTdlM2Y3MTI2XXtkaXNwbGF5OmJsb2NrfS5jbG9jayAuaG91cj5zcGFuPmlbZGF0YS12LTdlM2Y3MTI2XXtkaXNwbGF5OmJsb2NrO2ZvbnQtc3R5bGU6bm9ybWFsfS5jbG9jayAuaG91cltkYXRhLXYtN2UzZjcxMjZdOm50aC1vZi10eXBlKDIpe3RyYW5zZm9ybTpyb3RhdGV6KDMwZGVnKX0uY2xvY2sgLmhvdXI6bnRoLW9mLXR5cGUoMik+c3BhbltkYXRhLXYtN2UzZjcxMjZde3RyYW5zZm9ybTpyb3RhdGV6KC0zMGRlZyl9LmNsb2NrIC5ob3VyW2RhdGEtdi03ZTNmNzEyNl06bnRoLW9mLXR5cGUoMyl7dHJhbnNmb3JtOnJvdGF0ZXooNjBkZWcpfS5jbG9jayAuaG91cjpudGgtb2YtdHlwZSgzKT5zcGFuW2RhdGEtdi03ZTNmNzEyNl17dHJhbnNmb3JtOnJvdGF0ZXooLTYwZGVnKX0uY2xvY2sgLmhvdXJbZGF0YS12LTdlM2Y3MTI2XTpudGgtb2YtdHlwZSg0KXt0cmFuc2Zvcm06cm90YXRleig5MGRlZyl9LmNsb2NrIC5ob3VyOm50aC1vZi10eXBlKDQpPnNwYW5bZGF0YS12LTdlM2Y3MTI2XXt0cmFuc2Zvcm06cm90YXRleigtOTBkZWcpfS5jbG9jayAuaG91cltkYXRhLXYtN2UzZjcxMjZdOm50aC1vZi10eXBlKDUpe3RyYW5zZm9ybTpyb3RhdGV6KDEyMGRlZyl9LmNsb2NrIC5ob3VyOm50aC1vZi10eXBlKDUpPnNwYW5bZGF0YS12LTdlM2Y3MTI2XXt0cmFuc2Zvcm06cm90YXRleigtMTIwZGVnKX0uY2xvY2sgLmhvdXJbZGF0YS12LTdlM2Y3MTI2XTpudGgtb2YtdHlwZSg2KXt0cmFuc2Zvcm06cm90YXRleigxNTBkZWcpfS5jbG9jayAuaG91cjpudGgtb2YtdHlwZSg2KT5zcGFuW2RhdGEtdi03ZTNmNzEyNl17dHJhbnNmb3JtOnJvdGF0ZXooLTE1MGRlZyl9LmNsb2NrIC5ob3VyW2RhdGEtdi03ZTNmNzEyNl06bnRoLW9mLXR5cGUoNyl7dHJhbnNmb3JtOnJvdGF0ZXooMTgwZGVnKX0uY2xvY2sgLmhvdXI6bnRoLW9mLXR5cGUoNyk+c3BhbltkYXRhLXYtN2UzZjcxMjZde3RyYW5zZm9ybTpyb3RhdGV6KC0xODBkZWcpfS5jbG9jayAuaG91cltkYXRhLXYtN2UzZjcxMjZdOm50aC1vZi10eXBlKDgpe3RyYW5zZm9ybTpyb3RhdGV6KDIxMGRlZyl9LmNsb2NrIC5ob3VyOm50aC1vZi10eXBlKDgpPnNwYW5bZGF0YS12LTdlM2Y3MTI2XXt0cmFuc2Zvcm06cm90YXRleigtMjEwZGVnKX0uY2xvY2sgLmhvdXJbZGF0YS12LTdlM2Y3MTI2XTpudGgtb2YtdHlwZSg5KXt0cmFuc2Zvcm06cm90YXRleigyNDBkZWcpfS5jbG9jayAuaG91cjpudGgtb2YtdHlwZSg5KT5zcGFuW2RhdGEtdi03ZTNmNzEyNl17dHJhbnNmb3JtOnJvdGF0ZXooLTI0MGRlZyl9LmNsb2NrIC5ob3VyW2RhdGEtdi03ZTNmNzEyNl06bnRoLW9mLXR5cGUoMTApe3RyYW5zZm9ybTpyb3RhdGV6KDI3MGRlZyl9LmNsb2NrIC5ob3VyOm50aC1vZi10eXBlKDEwKT5zcGFuW2RhdGEtdi03ZTNmNzEyNl17dHJhbnNmb3JtOnJvdGF0ZXooLTI3MGRlZyl9LmNsb2NrIC5ob3VyW2RhdGEtdi03ZTNmNzEyNl06bnRoLW9mLXR5cGUoMTEpe3RyYW5zZm9ybTpyb3RhdGV6KDMwMGRlZyl9LmNsb2NrIC5ob3VyOm50aC1vZi10eXBlKDExKT5zcGFuW2RhdGEtdi03ZTNmNzEyNl17dHJhbnNmb3JtOnJvdGF0ZXooLTMwMGRlZyl9LmNsb2NrIC5ob3VyW2RhdGEtdi03ZTNmNzEyNl06bnRoLW9mLXR5cGUoMTIpe3RyYW5zZm9ybTpyb3RhdGV6KDMzMGRlZyl9LmNsb2NrIC5ob3VyOm50aC1vZi10eXBlKDEyKT5zcGFuW2RhdGEtdi03ZTNmNzEyNl17dHJhbnNmb3JtOnJvdGF0ZXooLTMzMGRlZyl9LmNsb2NrIC5jbG9jay1jaXJjbGVbZGF0YS12LTdlM2Y3MTI2XXtwb3NpdGlvbjphYnNvbHV0ZTt0b3A6NTAlO2xlZnQ6NTAlO3dpZHRoOjE2cHg7aGVpZ2h0OjE2cHg7dHJhbnNmb3JtOnRyYW5zbGF0ZSgtNTAlLC01MCUpO2JvcmRlcjoycHggc29saWQgIzY2Njtib3JkZXItcmFkaXVzOjEwMCU7YmFja2dyb3VuZC1jb2xvcjojZmZmO3otaW5kZXg6MTtib3gtc2l6aW5nOmJvcmRlci1ib3h9LmNsb2NrIC5jbG9jay1jaXJjbGVbZGF0YS12LTdlM2Y3MTI2XTpiZWZvcmV7cG9zaXRpb246YWJzb2x1dGU7dG9wOjUwJTtsZWZ0OjUwJTt0cmFuc2Zvcm06dHJhbnNsYXRlKC01MCUsLTUwJSk7ZGlzcGxheTpibG9jaztjb250ZW50OiIiO3dpZHRoOjRweDtoZWlnaHQ6NHB4O2JvcmRlci1yYWRpdXM6MTAwJTtiYWNrZ3JvdW5kLWNvbG9yOiM2NjZ9LmNsb2NrIC5jbG9jay1ob3VyW2RhdGEtdi03ZTNmNzEyNl0sLmNsb2NrIC5jbG9jay1taW51dGVbZGF0YS12LTdlM2Y3MTI2XSwuY2xvY2sgLmNsb2NrLXNlY29uZFtkYXRhLXYtN2UzZjcxMjZde3Bvc2l0aW9uOmFic29sdXRlO3RvcDoxNSU7bGVmdDo1MCU7ZGlzcGxheTpibG9jazt3aWR0aDoycHg7aGVpZ2h0OjM1JTttYXJnaW4tbGVmdDotMXB4O2JvcmRlci1yYWRpdXM6NXB4O3RyYW5zZm9ybS1vcmlnaW46Ym90dG9tO2JhY2tncm91bmQtY29sb3I6IzY2Nn0uY2xvY2sgLmNsb2NrLWhvdXJbZGF0YS12LTdlM2Y3MTI2XXt0b3A6MzAlO3dpZHRoOjRweDtoZWlnaHQ6MjAlO21hcmdpbi1sZWZ0Oi0ycHh9LmNsb2NrIC5jbG9jay1zZWNvbmRbZGF0YS12LTdlM2Y3MTI2XXt3aWR0aDoxcHh9LmNsb2NrLmlzLXNtYWxsW2RhdGEtdi03ZTNmNzEyNl17d2lkdGg6ODBweDtoZWlnaHQ6ODBweDtib3JkZXItd2lkdGg6MXB4O2ZvbnQtc2l6ZToxMnB4fS5jbG9jay5pcy1zbWFsbCAuY2xvY2stY2lyY2xlW2RhdGEtdi03ZTNmNzEyNl17d2lkdGg6MTBweDtoZWlnaHQ6MTBweDtib3JkZXItd2lkdGg6MXB4fS5jbG9jay5pcy1zbWFsbCAuY2xvY2stY2lyY2xlW2RhdGEtdi03ZTNmNzEyNl06YmVmb3Jle3dpZHRoOjJweDtoZWlnaHQ6MnB4fScsIiJdKX0sZnVuY3Rpb24odCxlKXt0LmV4cG9ydHM9ZnVuY3Rpb24oKXt2YXIgdD1bXTtyZXR1cm4gdC50b1N0cmluZz1mdW5jdGlvbigpe2Zvcih2YXIgdD1bXSxlPTA7ZTx0aGlzLmxlbmd0aDtlKyspe3ZhciBvPXRoaXNbZV07b1syXT90LnB1c2goIkBtZWRpYSAiK29bMl0rInsiK29bMV0rIn0iKTp0LnB1c2gob1sxXSl9cmV0dXJuIHQuam9pbigiIil9LHQuaT1mdW5jdGlvbihlLG8peyJzdHJpbmciPT10eXBlb2YgZSYmKGU9W1tudWxsLGUsIiJdXSk7Zm9yKHZhciByPXt9LG49MDtuPHRoaXMubGVuZ3RoO24rKyl7dmFyIGE9dGhpc1tuXVswXTsibnVtYmVyIj09dHlwZW9mIGEmJihyW2FdPSEwKX1mb3Iobj0wO248ZS5sZW5ndGg7bisrKXt2YXIgaT1lW25dOyJudW1iZXIiPT10eXBlb2YgaVswXSYmcltpWzBdXXx8KG8mJiFpWzJdP2lbMl09bzpvJiYoaVsyXT0iKCIraVsyXSsiKSBhbmQgKCIrbysiKSIpLHQucHVzaChpKSl9fSx0fX0sZnVuY3Rpb24odCxlLG8pe3ZhciByPW8oMyk7InN0cmluZyI9PXR5cGVvZiByJiYocj1bW3QuaSxyLCIiXV0pO3ZhciBuPXtobXI6ITB9O24udHJhbnNmb3JtPXZvaWQgMCxuLmluc2VydEludG89dm9pZCAwO28oNikocixuKTtyLmxvY2FscyYmKHQuZXhwb3J0cz1yLmxvY2Fscyl9LGZ1bmN0aW9uKHQsZSxvKXtmdW5jdGlvbiByKHQsZSl7Zm9yKHZhciBvPTA7bzx0Lmxlbmd0aDtvKyspe3ZhciByPXRbb10sbj12W3IuaWRdO2lmKG4pe24ucmVmcysrO2Zvcih2YXIgYT0wO2E8bi5wYXJ0cy5sZW5ndGg7YSsrKW4ucGFydHNbYV0oci5wYXJ0c1thXSk7Zm9yKDthPHIucGFydHMubGVuZ3RoO2ErKyluLnBhcnRzLnB1c2godShyLnBhcnRzW2FdLGUpKX1lbHNle2Zvcih2YXIgaT1bXSxhPTA7YTxyLnBhcnRzLmxlbmd0aDthKyspaS5wdXNoKHUoci5wYXJ0c1thXSxlKSk7dltyLmlkXT17aWQ6ci5pZCxyZWZzOjEscGFydHM6aX19fX1mdW5jdGlvbiBuKHQsZSl7Zm9yKHZhciBvPVtdLHI9e30sbj0wO248dC5sZW5ndGg7bisrKXt2YXIgYT10W25dLGk9ZS5iYXNlP2FbMF0rZS5iYXNlOmFbMF0scz1hWzFdLGM9YVsyXSxmPWFbM10sbD17Y3NzOnMsbWVkaWE6Yyxzb3VyY2VNYXA6Zn07cltpXT9yW2ldLnBhcnRzLnB1c2gobCk6by5wdXNoKHJbaV09e2lkOmkscGFydHM6W2xdfSl9cmV0dXJuIG99ZnVuY3Rpb24gYSh0LGUpe3ZhciBvPXkodC5pbnNlcnRJbnRvKTtpZighbyl0aHJvdyBuZXcgRXJyb3IoIkNvdWxkbid0IGZpbmQgYSBzdHlsZSB0YXJnZXQuIFRoaXMgcHJvYmFibHkgbWVhbnMgdGhhdCB0aGUgdmFsdWUgZm9yIHRoZSAnaW5zZXJ0SW50bycgcGFyYW1ldGVyIGlzIGludmFsaWQuIik7dmFyIHI9eFt4Lmxlbmd0aC0xXTtpZigidG9wIj09PXQuaW5zZXJ0QXQpcj9yLm5leHRTaWJsaW5nP28uaW5zZXJ0QmVmb3JlKGUsci5uZXh0U2libGluZyk6by5hcHBlbmRDaGlsZChlKTpvLmluc2VydEJlZm9yZShlLG8uZmlyc3RDaGlsZCkseC5wdXNoKGUpO2Vsc2UgaWYoImJvdHRvbSI9PT10Lmluc2VydEF0KW8uYXBwZW5kQ2hpbGQoZSk7ZWxzZXtpZigib2JqZWN0IiE9dHlwZW9mIHQuaW5zZXJ0QXR8fCF0Lmluc2VydEF0LmJlZm9yZSl0aHJvdyBuZXcgRXJyb3IoIltTdHlsZSBMb2FkZXJdXG5cbiBJbnZhbGlkIHZhbHVlIGZvciBwYXJhbWV0ZXIgJ2luc2VydEF0JyAoJ29wdGlvbnMuaW5zZXJ0QXQnKSBmb3VuZC5cbiBNdXN0IGJlICd0b3AnLCAnYm90dG9tJywgb3IgT2JqZWN0LlxuIChodHRwczovL2dpdGh1Yi5jb20vd2VicGFjay1jb250cmliL3N0eWxlLWxvYWRlciNpbnNlcnRhdClcbiIpO3ZhciBuPXkodC5pbnNlcnRBdC5iZWZvcmUsbyk7by5pbnNlcnRCZWZvcmUoZSxuKX19ZnVuY3Rpb24gaSh0KXtpZihudWxsPT09dC5wYXJlbnROb2RlKXJldHVybiExO3QucGFyZW50Tm9kZS5yZW1vdmVDaGlsZCh0KTt2YXIgZT14LmluZGV4T2YodCk7ZT49MCYmeC5zcGxpY2UoZSwxKX1mdW5jdGlvbiBzKHQpe3ZhciBlPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoInN0eWxlIik7aWYodm9pZCAwPT09dC5hdHRycy50eXBlJiYodC5hdHRycy50eXBlPSJ0ZXh0L2NzcyIpLHZvaWQgMD09PXQuYXR0cnMubm9uY2Upe3ZhciBvPWwoKTtvJiYodC5hdHRycy5ub25jZT1vKX1yZXR1cm4gZihlLHQuYXR0cnMpLGEodCxlKSxlfWZ1bmN0aW9uIGModCl7dmFyIGU9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgibGluayIpO3JldHVybiB2b2lkIDA9PT10LmF0dHJzLnR5cGUmJih0LmF0dHJzLnR5cGU9InRleHQvY3NzIiksdC5hdHRycy5yZWw9InN0eWxlc2hlZXQiLGYoZSx0LmF0dHJzKSxhKHQsZSksZX1mdW5jdGlvbiBmKHQsZSl7T2JqZWN0LmtleXMoZSkuZm9yRWFjaChmdW5jdGlvbihvKXt0LnNldEF0dHJpYnV0ZShvLGVbb10pfSl9ZnVuY3Rpb24gbCgpe3JldHVybiBvLm5jfWZ1bmN0aW9uIHUodCxlKXt2YXIgbyxyLG4sYTtpZihlLnRyYW5zZm9ybSYmdC5jc3Mpe2lmKCEoYT0iZnVuY3Rpb24iPT10eXBlb2YgZS50cmFuc2Zvcm0/ZS50cmFuc2Zvcm0odC5jc3MpOmUudHJhbnNmb3JtLmRlZmF1bHQodC5jc3MpKSlyZXR1cm4gZnVuY3Rpb24oKXt9O3QuY3NzPWF9aWYoZS5zaW5nbGV0b24pe3ZhciBmPWsrKztvPWd8fChnPXMoZSkpLHI9ZC5iaW5kKG51bGwsbyxmLCExKSxuPWQuYmluZChudWxsLG8sZiwhMCl9ZWxzZSB0LnNvdXJjZU1hcCYmImZ1bmN0aW9uIj09dHlwZW9mIFVSTCYmImZ1bmN0aW9uIj09dHlwZW9mIFVSTC5jcmVhdGVPYmplY3RVUkwmJiJmdW5jdGlvbiI9PXR5cGVvZiBVUkwucmV2b2tlT2JqZWN0VVJMJiYiZnVuY3Rpb24iPT10eXBlb2YgQmxvYiYmImZ1bmN0aW9uIj09dHlwZW9mIGJ0b2E/KG89YyhlKSxyPWguYmluZChudWxsLG8sZSksbj1mdW5jdGlvbigpe2kobyksby5ocmVmJiZVUkwucmV2b2tlT2JqZWN0VVJMKG8uaHJlZil9KToobz1zKGUpLHI9cC5iaW5kKG51bGwsbyksbj1mdW5jdGlvbigpe2kobyl9KTtyZXR1cm4gcih0KSxmdW5jdGlvbihlKXtpZihlKXtpZihlLmNzcz09PXQuY3NzJiZlLm1lZGlhPT09dC5tZWRpYSYmZS5zb3VyY2VNYXA9PT10LnNvdXJjZU1hcClyZXR1cm47cih0PWUpfWVsc2UgbigpfX1mdW5jdGlvbiBkKHQsZSxvLHIpe3ZhciBuPW8/IiI6ci5jc3M7aWYodC5zdHlsZVNoZWV0KXQuc3R5bGVTaGVldC5jc3NUZXh0PXooZSxuKTtlbHNle3ZhciBhPWRvY3VtZW50LmNyZWF0ZVRleHROb2RlKG4pLGk9dC5jaGlsZE5vZGVzO2lbZV0mJnQucmVtb3ZlQ2hpbGQoaVtlXSksaS5sZW5ndGg/dC5pbnNlcnRCZWZvcmUoYSxpW2VdKTp0LmFwcGVuZENoaWxkKGEpfX1mdW5jdGlvbiBwKHQsZSl7dmFyIG89ZS5jc3Mscj1lLm1lZGlhO2lmKHImJnQuc2V0QXR0cmlidXRlKCJtZWRpYSIsciksdC5zdHlsZVNoZWV0KXQuc3R5bGVTaGVldC5jc3NUZXh0PW87ZWxzZXtmb3IoO3QuZmlyc3RDaGlsZDspdC5yZW1vdmVDaGlsZCh0LmZpcnN0Q2hpbGQpO3QuYXBwZW5kQ2hpbGQoZG9jdW1lbnQuY3JlYXRlVGV4dE5vZGUobykpfX1mdW5jdGlvbiBoKHQsZSxvKXt2YXIgcj1vLmNzcyxuPW8uc291cmNlTWFwLGE9dm9pZCAwPT09ZS5jb252ZXJ0VG9BYnNvbHV0ZVVybHMmJm47KGUuY29udmVydFRvQWJzb2x1dGVVcmxzfHxhKSYmKHI9dyhyKSksbiYmKHIrPSJcbi8qIyBzb3VyY2VNYXBwaW5nVVJMPWRhdGE6YXBwbGljYXRpb24vanNvbjtiYXNlNjQsIitidG9hKHVuZXNjYXBlKGVuY29kZVVSSUNvbXBvbmVudChKU09OLnN0cmluZ2lmeShuKSkpKSsiICovIik7dmFyIGk9bmV3IEJsb2IoW3JdLHt0eXBlOiJ0ZXh0L2NzcyJ9KSxzPXQuaHJlZjt0LmhyZWY9VVJMLmNyZWF0ZU9iamVjdFVSTChpKSxzJiZVUkwucmV2b2tlT2JqZWN0VVJMKHMpfXZhciB2PXt9LG09ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHZvaWQgMD09PWUmJihlPXQuYXBwbHkodGhpcyxhcmd1bWVudHMpKSxlfX0oZnVuY3Rpb24oKXtyZXR1cm4gd2luZG93JiZkb2N1bWVudCYmZG9jdW1lbnQuYWxsJiYhd2luZG93LmF0b2J9KSxiPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGU/ZS5xdWVyeVNlbGVjdG9yKHQpOmRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IodCl9LHk9ZnVuY3Rpb24odCl7dmFyIGU9e307cmV0dXJuIGZ1bmN0aW9uKHQsbyl7aWYoImZ1bmN0aW9uIj09dHlwZW9mIHQpcmV0dXJuIHQoKTtpZih2b2lkIDA9PT1lW3RdKXt2YXIgcj1iLmNhbGwodGhpcyx0LG8pO2lmKHdpbmRvdy5IVE1MSUZyYW1lRWxlbWVudCYmciBpbnN0YW5jZW9mIHdpbmRvdy5IVE1MSUZyYW1lRWxlbWVudCl0cnl7cj1yLmNvbnRlbnREb2N1bWVudC5oZWFkfWNhdGNoKHQpe3I9bnVsbH1lW3RdPXJ9cmV0dXJuIGVbdF19fSgpLGc9bnVsbCxrPTAseD1bXSx3PW8oNyk7dC5leHBvcnRzPWZ1bmN0aW9uKHQsZSl7aWYoInVuZGVmaW5lZCIhPXR5cGVvZiBERUJVRyYmREVCVUcmJiJvYmplY3QiIT10eXBlb2YgZG9jdW1lbnQpdGhyb3cgbmV3IEVycm9yKCJUaGUgc3R5bGUtbG9hZGVyIGNhbm5vdCBiZSB1c2VkIGluIGEgbm9uLWJyb3dzZXIgZW52aXJvbm1lbnQiKTtlPWV8fHt9LGUuYXR0cnM9Im9iamVjdCI9PXR5cGVvZiBlLmF0dHJzP2UuYXR0cnM6e30sZS5zaW5nbGV0b258fCJib29sZWFuIj09dHlwZW9mIGUuc2luZ2xldG9ufHwoZS5zaW5nbGV0b249bSgpKSxlLmluc2VydEludG98fChlLmluc2VydEludG89ImhlYWQiKSxlLmluc2VydEF0fHwoZS5pbnNlcnRBdD0iYm90dG9tIik7dmFyIG89bih0LGUpO3JldHVybiByKG8sZSksZnVuY3Rpb24odCl7Zm9yKHZhciBhPVtdLGk9MDtpPG8ubGVuZ3RoO2krKyl7dmFyIHM9b1tpXSxjPXZbcy5pZF07Yy5yZWZzLS0sYS5wdXNoKGMpfWlmKHQpe3Iobih0LGUpLGUpfWZvcih2YXIgaT0wO2k8YS5sZW5ndGg7aSsrKXt2YXIgYz1hW2ldO2lmKDA9PT1jLnJlZnMpe2Zvcih2YXIgZj0wO2Y8Yy5wYXJ0cy5sZW5ndGg7ZisrKWMucGFydHNbZl0oKTtkZWxldGUgdltjLmlkXX19fX07dmFyIHo9ZnVuY3Rpb24oKXt2YXIgdD1bXTtyZXR1cm4gZnVuY3Rpb24oZSxvKXtyZXR1cm4gdFtlXT1vLHQuZmlsdGVyKEJvb2xlYW4pLmpvaW4oIlxuIil9fSgpfSxmdW5jdGlvbih0LGUpe3QuZXhwb3J0cz1mdW5jdGlvbih0KXt2YXIgZT0idW5kZWZpbmVkIiE9dHlwZW9mIHdpbmRvdyYmd2luZG93LmxvY2F0aW9uO2lmKCFlKXRocm93IG5ldyBFcnJvcigiZml4VXJscyByZXF1aXJlcyB3aW5kb3cubG9jYXRpb24iKTtpZighdHx8InN0cmluZyIhPXR5cGVvZiB0KXJldHVybiB0O3ZhciBvPWUucHJvdG9jb2wrIi8vIitlLmhvc3Qscj1vK2UucGF0aG5hbWUucmVwbGFjZSgvXC9bXlwvXSokLywiLyIpO3JldHVybiB0LnJlcGxhY2UoL3VybFxzKlwoKCg/OlteKShdfFwoKD86W14pKF0rfFwoW14pKF0qXCkpKlwpKSopXCkvZ2ksZnVuY3Rpb24odCxlKXt2YXIgbj1lLnRyaW0oKS5yZXBsYWNlKC9eIiguKikiJC8sZnVuY3Rpb24odCxlKXtyZXR1cm4gZX0pLnJlcGxhY2UoL14nKC4qKSckLyxmdW5jdGlvbih0LGUpe3JldHVybiBlfSk7aWYoL14oI3xkYXRhOnxodHRwOlwvXC98aHR0cHM6XC9cL3xmaWxlOlwvXC9cL3xccyokKS9pLnRlc3QobikpcmV0dXJuIHQ7dmFyIGE7cmV0dXJuIGE9MD09PW4uaW5kZXhPZigiLy8iKT9uOjA9PT1uLmluZGV4T2YoIi8iKT9vK246cituLnJlcGxhY2UoL15cLlwvLywiIiksInVybCgiK0pTT04uc3RyaW5naWZ5KGEpKyIpIn0pfX0sZnVuY3Rpb24odCxlKXt0LmV4cG9ydHM9ZnVuY3Rpb24odCxlLG8scil7dmFyIG4sYT10PXR8fHt9LGk9dHlwZW9mIHQuZGVmYXVsdDsib2JqZWN0IiE9PWkmJiJmdW5jdGlvbiIhPT1pfHwobj10LGE9dC5kZWZhdWx0KTt2YXIgcz0iZnVuY3Rpb24iPT10eXBlb2YgYT9hLm9wdGlvbnM6YTtpZihlJiYocy5yZW5kZXI9ZS5yZW5kZXIscy5zdGF0aWNSZW5kZXJGbnM9ZS5zdGF0aWNSZW5kZXJGbnMpLG8mJihzLl9zY29wZUlkPW8pLHIpe3ZhciBjPU9iamVjdC5jcmVhdGUocy5jb21wdXRlZHx8bnVsbCk7T2JqZWN0LmtleXMocikuZm9yRWFjaChmdW5jdGlvbih0KXt2YXIgZT1yW3RdO2NbdF09ZnVuY3Rpb24oKXtyZXR1cm4gZX19KSxzLmNvbXB1dGVkPWN9cmV0dXJue2VzTW9kdWxlOm4sZXhwb3J0czphLG9wdGlvbnM6c319fSxmdW5jdGlvbih0LGUpe3QuZXhwb3J0cz17cmVuZGVyOmZ1bmN0aW9uKCl7dmFyIHQ9dGhpcyxlPXQuJGNyZWF0ZUVsZW1lbnQsbz10Ll9zZWxmLl9jfHxlO3JldHVybiBvKCJkaXYiLHtzdGF0aWNDbGFzczoiY2xvY2siLHN0eWxlOnQuY2xvY2tTdHlsZX0sW28oImRpdiIse3N0YXRpY0NsYXNzOiJjbG9jay1jaXJjbGUifSksdC5fdigiICIpLG8oImRpdiIse3N0YXRpY0NsYXNzOiJjbG9jay1ob3VyIixzdHlsZTp7dHJhbnNmb3JtOnQuaG91clJvdGF0ZX19KSx0Ll92KCIgIiksbygiZGl2Iix7c3RhdGljQ2xhc3M6ImNsb2NrLW1pbnV0ZSIsc3R5bGU6e3RyYW5zZm9ybTp0Lm1pbnV0ZVJvdGF0ZX19KSx0Ll92KCIgIiksbygiZGl2Iix7c3RhdGljQ2xhc3M6ImNsb2NrLXNlY29uZCIsc3R5bGU6e3RyYW5zZm9ybTp0LnNlY29uZFJvdGF0ZX19KSx0Ll92KCIgIiksdC5fbCh0LnRpbWVMaXN0LGZ1bmN0aW9uKGUpe3JldHVybiBvKCJiIix7a2V5OmUsc3RhdGljQ2xhc3M6ImhvdXIifSxbbygic3BhbiIsW28oImkiLHtzdHlsZTp7dHJhbnNmb3JtOnQudHJhbnNmb3JtfX0sW3QuX3YodC5fcyhlKSldKV0pXSl9KV0sMil9LHN0YXRpY1JlbmRlckZuczpbXX19XSl9KTsKLy8jIHNvdXJjZU1hcHBpbmdVUkw9dnVlLWNsb2NrLm1pbi5qcy5tYXA=');

// Injecting template but normally you would create a template file.
$clock_template = new HtmlTemplate(<<<'EOF'
    <div id="{$_id}" class="ui center aligned segment">
    <my-clock inline-template v-bind="initData">
        <div>
            <clock :color="color" :border="border" :bg="bg"></clock>
            <div class="ui basic segment inline"><div class="ui button primary" @click="onChangeStyle">Change Style</div></div>
        </div>
    </my-clock>
    </div>{$script}
    EOF);

// Injecting script but normally you would create a separate js file and include it in your page.
// This is the vue component definition. It is also using another external vue component 'vue-clock2'
$clock_script = <<<'EOF'
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
            atk.eventBus.on(this.$root.$el.id + '-clock-change-style', (payload) => {
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
    </script>
    EOF;

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
