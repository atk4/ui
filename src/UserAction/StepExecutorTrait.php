<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\Factory;
use Atk4\Data\Exception;
use Atk4\Data\Model;
use Atk4\Data\Model\UserAction;
use Atk4\Data\ValidationException;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\JsExpressionable;
use Atk4\Ui\JsFunction;
use Atk4\Ui\Loader;
use Atk4\Ui\Message;
use Atk4\Ui\View;

trait StepExecutorTrait
{
    /** @var array<int, string> The steps need to complete the action. */
    protected $steps;

    /** @var string current step. */
    protected $step;

    /** @var Loader The Loader that will execute all action step. */
    protected $loader;

    /** @var string */
    public $loaderUi = 'ui basic segment';

    /** @var array */
    public $loaderShim = [];

    /** @var Button The action step prev button. */
    protected $prevStepBtn;

    /** @var Button The action next step button. */
    protected $nextStepBtn;

    /** @var Button The execute action button. */
    protected $execActionBtn;

    /** @var View View holding buttons. */
    protected $btns;

    /** @var UserAction The action to execute. */
    public $action;

    /** @var array will collect data while doing action step. */
    private $actionData = [];

    /** @var bool */
    protected $actionInitialized = false;

    /** @var JsExpressionable|\Closure JsExpression to return if action was successful, e.g "new JsToast('Thank you')" */
    public $jsSuccess;

    /** @var array A seed for creating form in order to edit arguments/fields user entry. */
    public $formSeed = [Form::class];

    /** @var string can be "console", "text", or "html". Determine how preview step will display information. */
    public $previewType = 'html';

    /** @var array<string, array<mixed>> View seed for displaying title for each step. */
    protected $stepTitle = ['args' => [], 'fields' => [], 'preview' => []];

    /** @var string */
    public $finalMsg = 'Complete!';

    /**
     * Utility for setting Title for each step.
     */
    protected function addStepTitle(View $view, string $step): void
    {
        if ($seed = $this->stepTitle[$step] ?? null) {
            $view->add(Factory::factory($seed));
        }
    }

    /**
     * Utility for setting form in each step.
     */
    protected function addFormTo(View $view): Form
    {
        /** @var Form $f */
        $f = $view->add(Factory::factory($this->formSeed));
        $f->buttonSave->destroy();

        return $f;
    }

    /**
     * Will add field into form based on $fields array.
     */
    protected function setFormField(Form $form, array $fields, string $step): Form
    {
        foreach ($fields as $k => $val) {
            $form->getControl($k)->set($val);
        }

        $this->hook(self::HOOK_STEP, [$step, $form]);

        return $form;
    }

    protected function runSteps(): void
    {
        $this->loader->set(function (Loader $p) {
            try {
                switch ($this->step) {
                    case 'args':
                        $this->doArgs($p);

                        break;
                    case 'fields':
                        $this->doFields($p);

                        break;
                    case 'preview':
                        $this->doPreview($p);

                        break;
                    case 'final':
                        $this->doFinal($p);

                        break;
                }
            } catch (\Exception $e) {
                $this->handleException($e, $p, $this->step);
            }
        });
    }

    protected function doArgs(View $page): void
    {
        $this->addStepTitle($page, $this->step);

        $form = $this->addFormTo($page);
        foreach ($this->action->args as $key => $val) {
            if (is_numeric($key)) {
                throw (new Exception('Action arguments must be named'))
                    ->addMoreInfo('args', $this->action->args);
            }

            if ($val instanceof Model) {
                $val = ['model' => $val];
            }

            if (isset($val['model'])) {
                $val['model'] = Factory::factory($val['model']);
                $form->addControl($key, [Form\Control\Lookup::class])->setModel($val['model']);
            } else {
                $form->addControl($key, [], $val);
            }
        }

        // set args value if available
        $this->setFormField($form, $this->getActionData('args'), $this->step);

        // setup exec, next and prev button handler for this step
        $this->jsSetSubmitBtn($page, $form, $this->step);
        $this->jsSetPrevHandler($page, $this->step);

        $form->onSubmit(function (Form $form) {
            // collect arguments
            $this->setActionDataFromModel('args', $form->model, array_keys($form->model->getFields()));

            return $this->jsStepSubmit($this->step);
        });
    }

    protected function doFields(View $page): void
    {
        $this->addStepTitle($page, $this->step);
        $form = $this->addFormTo($page);

        $form->setModel($this->action->getEntity(), $this->action->fields);
        // set Fields value if set from another step
        $this->setFormField($form, $this->getActionData('fields'), $this->step);

        // setup exec, next and prev button handler for this step
        $this->jsSetSubmitBtn($page, $form, $this->step);
        $this->jsSetPrevHandler($page, $this->step);

        if (!$form->hookHasCallbacks(Form::HOOK_SUBMIT)) {
            $form->onSubmit(function (Form $form) {
                // collect fields defined in Model\UserAction
                $this->setActionDataFromModel('fields', $form->model, $this->action->fields);

                return $this->jsStepSubmit($this->step);
            });
        }
    }

    protected function doPreview(View $page): void
    {
        $this->addStepTitle($page, $this->step);

        if ($fields = $this->getActionData('fields')) {
            $this->action->getEntity()->setMulti($fields);
        }

        if ($prev = $this->getPreviousStep($this->step)) {
            $chain = $this->loader->jsLoad([
                'step' => $prev,
                $this->name => $this->action->getEntity()->getId(),
            ], ['method' => 'post'], $this->loader->name);

            $page->js(true, $this->prevStepBtn->js()->on('click', new JsFunction([$chain])));
        }

        // setup executor button to perform action
        $page->js(
            true,
            $this->execActionBtn->js()->on('click', new JsFunction([
                $this->loader->jsLoad(
                    [
                        'step' => 'final',
                        $this->name => $this->action->getEntity()->getId(),
                    ],
                    ['method' => 'post'],
                    $this->loader->name
                ),
            ]))
        );

        $text = $this->getActionPreview();

        switch ($this->previewType) {
            case 'console':
                $preview = View::addTo($page, ['ui' => 'inverted black segment', 'element' => 'pre']);
                $preview->set($text);

                break;
            case 'text':
                $preview = View::addTo($page, ['ui' => 'basic segment']);
                $preview->set($text);

                break;
            case 'html':
                $preview = View::addTo($page, ['ui' => 'basic segment']);
                $preview->template->dangerouslySetHtml('Content', $text);

                break;
        }
    }

    protected function doFinal(View $page): void
    {
        View::addTo($page, ['content' => $this->finalMsg]);
        foreach ($this->getActionData('fields') as $field => $value) {
            $this->action->getEntity()->set($field, $value);
        }

        $return = $this->action->execute(...$this->getActionArgs($this->getActionData('args')));

        $this->jsSequencer($page, $this->jsGetExecute($return, $this->action->getEntity()->getId()));
    }

    /**
     * Get how many steps is required for this action.
     */
    protected function getSteps(UserAction $action): array
    {
        $steps = [];
        if ($action->args) {
            $steps[] = 'args';
        }
        if ($action->fields) {
            $steps[] = 'fields';
        }
        if ($action->preview) {
            $steps[] = 'preview';
        }

        return $steps;
    }

    protected function getNextStep(string $step): ?string
    {
        $next = null;
        if (!$this->isLastStep($step)) {
            foreach ($this->steps as $k => $s) {
                if ($step === $s) {
                    $next = $this->steps[$k + 1];

                    break;
                }
            }
        }

        return $next;
    }

    protected function getPreviousStep(string $step): ?string
    {
        $prev = null;

        if (!$this->isFirstStep($step)) {
            foreach ($this->steps as $k => $s) {
                if ($s === $step) {
                    $prev = $this->steps[$k - 1];

                    break;
                }
            }
        }

        return $prev;
    }

    protected function isLastStep(string $step): bool
    {
        $isLast = false;
        $step_count = count($this->steps);
        foreach ($this->steps as $k => $s) {
            if ($s === $step) {
                $isLast = $k === $step_count - 1;

                break;
            }
        }

        return $isLast;
    }

    protected function isFirstStep(string $step): bool
    {
        return $step === $this->steps[0];
    }

    protected function getStep(): string
    {
        return $this->step;
    }

    protected function createButtonBar(Model\UserAction $action): View
    {
        $this->btns = (new View())->addStyle(['min-height' => '24px']);
        $this->prevStepBtn = Button::addTo($this->btns, ['Prev'])->addStyle(['float' => 'left !important']);
        $this->nextStepBtn = Button::addTo($this->btns, ['Next', 'class.blue' => true]);
        $this->execActionBtn = $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::MODAL_BUTTON);
        $this->btns->add($this->execActionBtn);

        return $this->btns;
    }

    /**
     * Generate js for setting Buttons state based on current step.
     */
    protected function jsSetBtnState(View $view, string $step): void
    {
        if (count($this->steps) === 1) {
            $view->js(true, $this->prevStepBtn->js()->hide());
            $view->js(true, $this->nextStepBtn->js()->hide());
        } else {
            $view->js(true, $this->jsSetPrevState($step));
            $view->js(true, $this->jsSetNextState($step));
            $view->js(true, $this->jsSetExecState($step));
        }

        // reset button handler
        $view->js(true, $this->execActionBtn->js(true)->off());
        $view->js(true, $this->nextStepBtn->js(true)->off());
        $view->js(true, $this->prevStepBtn->js(true)->off());
        $view->js(true, $this->nextStepBtn->js()->removeClass('disabled'));
        $view->js(true, $this->execActionBtn->js()->removeClass('disabled'));
    }

    /**
     * Generate js for Next btn state.
     */
    protected function jsSetNextState(string $step): JsExpressionable
    {
        if ($this->isLastStep($step)) {
            return $this->nextStepBtn->js(true)->hide();
        }

        return $this->nextStepBtn->js(true)->show();
    }

    /**
     * Generated js for Prev btn state.
     */
    protected function jsSetPrevState(string $step): JsExpressionable
    {
        if ($this->isFirstStep($step)) {
            return $this->prevStepBtn->js(true)->hide();
        }

        return $this->prevStepBtn->js(true)->show();
    }

    /**
     * Generate js for Exec button state.
     */
    protected function jsSetExecState(string $step): JsExpressionable
    {
        if ($this->isLastStep($step)) {
            return $this->execActionBtn->js(true)->show();
        }

        return $this->execActionBtn->js(true)->hide();
    }

    /**
     * Generate js function for Previous button.
     */
    protected function jsSetPrevHandler(View $view, string $step): void
    {
        if ($prev = $this->getPreviousStep($step)) {
            $chain = $this->loader->jsLoad([
                'step' => $prev,
                $this->name => $this->action->getEntity()->getId(),
            ], ['method' => 'post'], $this->loader->name);

            $view->js(true, $this->prevStepBtn->js()->on('click', new JsFunction([$chain])));
        }
    }

    /**
     * Determine which button is responsible for submitting form on a specific step.
     */
    protected function jsSetSubmitBtn(View $view, Form $form, string $step): void
    {
        if ($this->isLastStep($step)) {
            $view->js(true, $this->execActionBtn->js()->on('click', new JsFunction([$form->js(false, null, $form->formElement)->form('submit')])));
        } else {
            // submit on next
            $view->js(true, $this->nextStepBtn->js()->on('click', new JsFunction([$form->js(false, null, $form->formElement)->form('submit')])));
        }
    }

    /**
     * Get proper js after submitting a form in a step.
     *
     * @return mixed
     */
    protected function jsStepSubmit(string $step)
    {
        try {
            if ($this->isLastStep($step)) {
                // collect argument and execute action
                $return = $this->action->execute(...$this->getActionArgs($this->getActionData('args')));
                $js = $this->jsGetExecute($return, $this->action->getEntity()->getId());
            } else {
                // store data and setup reload
                $js = [
                    $this->loader->jsAddStoreData($this->actionData, true),
                    $this->loader->jsLoad([
                        'step' => $this->getNextStep($step),
                        $this->name => $this->action->getEntity()->getId(),
                    ], ['method' => 'post'], $this->loader->name),
                ];
            }

            return $js;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $msg = new Message(['Error executing ' . $this->action->caption, 'type' => 'error', 'class.red' => true]);
            $msg->invokeInit();
            $msg->text->content = $this->getApp()->renderExceptionHtml($e);

            return $msg;
        }
    }

    protected function getActionData(string $step): array
    {
        return $this->actionData[$step] ?? [];
    }

    /**
     * @param array<string> $fields
     */
    private function setActionDataFromModel(string $step, Model $model, array $fields): void
    {
        $data = [];
        foreach ($fields as $k) {
            $data[$k] = $model->get($k);
        }
        $this->actionData[$step] = $data;
    }

    /**
     * Get action preview based on it's argument.
     *
     * @return mixed
     */
    protected function getActionPreview()
    {
        $args = [];

        foreach ($this->action->args as $key => $val) {
            $args[] = $this->getActionData('args')[$key];
        }

        return $this->action->preview(...$args);
    }

    /**
     * Utility for retrieving Argument.
     */
    protected function getActionArgs(array $data): array
    {
        $args = [];

        foreach ($this->action->args as $key => $val) {
            $args[] = $data[$key];
        }

        return $args;
    }

    /**
     * Create a sequence of js statement for a view.
     *
     * @param array|JsExpressionable $js
     */
    protected function jsSequencer(View $view, $js): void
    {
        if (is_array($js)) {
            foreach ($js as $jq) {
                $this->jsSequencer($view, $jq);
            }
        } else {
            $view->js(true, $js);
        }
    }

    protected function handleException(\Throwable $exception, View $view, string $step): void
    {
        $msg = Message::addTo($view, ['Error:', 'type' => 'error']);
        $msg->text->addHtml($this->getApp()->renderExceptionHtml($exception));
        $view->js(true, $this->nextStepBtn->js()->addClass('disabled'));
        if (!$this->isFirstStep($step)) {
            $this->jsSetPrevHandler($view, $step);
        }
        if ($this->isLastStep($step)) {
            $view->js(true, $this->execActionBtn->js()->addClass('disabled'));
        }
        if ($step === 'final') {
            $this->jsSetPrevHandler($view, $this->steps[count($this->steps) - 1]);
        }
    }
}
