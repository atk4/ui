<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Loader;
use Atk4\Ui\View;

trait StepExecutorTrait
{
    /** @var array<int, string> The steps need to complete the action. */
    protected array $steps;

    /** @var string current step. */
    protected $step;

    /** @var Loader The Loader that will execute all action step. */
    protected $loader;

    /** @var string */
    public $loaderUi = 'basic segment';

    /** @var array */
    public $loaderShim = [];

    /** @var Button The action step previous button. */
    protected $previousStepButton;

    /** @var Button The action next step button. */
    protected $nextStepButton;

    /** @var Button The execute action button. */
    protected $executeActionButton;

    /** @var View */
    protected $buttonsView;

    /** @var UserAction The action to execute. */
    public $action;

    /** @var array will collect data while doing action step. */
    private $actionData = [];

    /** @var bool */
    protected $actionInitialized = false;

    /** @var JsExpressionable|\Closure JS expression to return if action was successful, e.g "new JsToast('Thank you')" */
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
        $seed = $this->stepTitle[$step] ?? null;
        if ($seed) {
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
        });
    }

    protected function doArgs(View $page): void
    {
        $this->addStepTitle($page, $this->step);

        $form = $this->addFormTo($page);
        foreach ($this->action->args as $key => $val) {
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

        // setup execute, next and previous button handler for this step
        $this->jsSetSubmitButton($page, $form, $this->step);
        $this->jsSetPreviousHandler($page, $this->step);

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

        // setup execute, next and previous button handler for this step
        $this->jsSetSubmitButton($page, $form, $this->step);
        $this->jsSetPreviousHandler($page, $this->step);

        $form->onSubmit(function (Form $form) {
            // collect fields defined in Model\UserAction
            $this->setActionDataFromModel('fields', $form->model, $this->action->fields);

            return $this->jsStepSubmit($this->step);
        });
    }

    protected function doPreview(View $page): void
    {
        $this->addStepTitle($page, $this->step);

        $fields = $this->getActionData('fields');
        if ($fields) {
            $this->action->getEntity()->setMulti($fields);
        }

        if (!$this->isFirstStep($this->step)) {
            $chain = $this->loader->jsLoad(
                [
                    'step' => $this->getPreviousStep($this->step),
                    $this->name => $this->action->getEntity()->getId(),
                ],
                ['method' => 'POST'],
                $this->loader->name
            );

            $page->js(true, $this->previousStepButton->js()->on('click', new JsFunction([], [$chain])));
        }

        // setup executor button to perform action
        $page->js(
            true,
            $this->executeActionButton->js()->on('click', new JsFunction([], [
                $this->loader->jsLoad(
                    [
                        'step' => 'final',
                        $this->name => $this->action->getEntity()->getId(),
                    ],
                    ['method' => 'POST'],
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

        $page->js(true, $this->jsGetExecute($return, $this->action->getEntity()->getId()));
    }

    /**
     * Get how many steps is required for this action.
     */
    protected function getSteps(): array
    {
        $steps = [];
        if ($this->action->args) {
            $steps[] = 'args';
        }
        if ($this->action->fields) {
            $steps[] = 'fields';
        }
        if ($this->action->preview) {
            $steps[] = 'preview';
        }

        return $steps;
    }

    protected function isFirstStep(string $step): bool
    {
        return $this->steps[array_key_first($this->steps)] === $step;
    }

    protected function isLastStep(string $step): bool
    {
        return $this->steps[array_key_last($this->steps)] === $step;
    }

    protected function getPreviousStep(string $step): string
    {
        $steps = array_values($this->steps);

        return $steps[array_search($step, $steps, true) - 1];
    }

    protected function getNextStep(string $step): string
    {
        $steps = array_values($this->steps);

        return $steps[array_search($step, $steps, true) + 1];
    }

    protected function getStep(): string
    {
        return $this->step;
    }

    protected function createButtonBar(): View
    {
        $this->buttonsView = (new View())->setStyle(['min-height' => '24px']);
        $this->previousStepButton = Button::addTo($this->buttonsView, ['Previous'])->setStyle(['float' => 'left !important']);
        $this->nextStepButton = Button::addTo($this->buttonsView, ['Next', 'class.blue' => true]);
        $this->executeActionButton = $this->getExecutorFactory()->createTrigger($this->action, ExecutorFactory::MODAL_BUTTON);
        $this->buttonsView->add($this->executeActionButton);

        return $this->buttonsView;
    }

    /**
     * Generate JS for setting Buttons state based on current step.
     */
    protected function jsSetButtonsState(View $view, string $step): void
    {
        if (count($this->steps) === 1) {
            $view->js(true, $this->previousStepButton->js()->hide());
            $view->js(true, $this->nextStepButton->js()->hide());
        } else {
            $view->js(true, $this->jsSetPreviousState($step));
            $view->js(true, $this->jsSetNextState($step));
            $view->js(true, $this->jsSetExecuteState($step));
        }

        // reset button handler
        $view->js(true, $this->executeActionButton->js()->off());
        $view->js(true, $this->nextStepButton->js()->off());
        $view->js(true, $this->previousStepButton->js()->off());
        $view->js(true, $this->nextStepButton->js()->removeClass('disabled'));
        $view->js(true, $this->executeActionButton->js()->removeClass('disabled'));
    }

    /**
     * Generate JS for Next button state.
     */
    protected function jsSetNextState(string $step): JsExpressionable
    {
        if ($this->isLastStep($step)) {
            return $this->nextStepButton->js()->hide();
        }

        return $this->nextStepButton->js()->show();
    }

    /**
     * Generated JS for Previous button state.
     */
    protected function jsSetPreviousState(string $step): JsExpressionable
    {
        if ($this->isFirstStep($step)) {
            return $this->previousStepButton->js()->hide();
        }

        return $this->previousStepButton->js()->show();
    }

    /**
     * Generate JS for Execute button state.
     */
    protected function jsSetExecuteState(string $step): JsExpressionable
    {
        if ($this->isLastStep($step)) {
            return $this->executeActionButton->js()->show();
        }

        return $this->executeActionButton->js()->hide();
    }

    /**
     * Generate JS function for Previous button.
     */
    protected function jsSetPreviousHandler(View $view, string $step): void
    {
        if (!$this->isFirstStep($step)) {
            $chain = $this->loader->jsLoad(
                [
                    'step' => $this->getPreviousStep($step),
                    $this->name => $this->action->getEntity()->getId(),
                ],
                ['method' => 'POST'],
                $this->loader->name
            );

            $view->js(true, $this->previousStepButton->js()->on('click', new JsFunction([], [$chain])));
        }
    }

    /**
     * Determine which button is responsible for submitting form on a specific step.
     */
    protected function jsSetSubmitButton(View $view, Form $form, string $step): void
    {
        $button = $this->isLastStep($step)
            ? $this->executeActionButton
            : $this->nextStepButton; // submit on next

        $view->js(true, $button->js()->on('click', new JsFunction([], [$form->js()->form('submit')])));
    }

    /**
     * Get proper JS after submitting a form in a step.
     */
    protected function jsStepSubmit(string $step): JsBlock
    {
        if (count($this->steps) === 1) {
            // collect argument and execute action
            $return = $this->action->execute(...$this->getActionArgs($this->getActionData('args')));
            $js = $this->jsGetExecute($return, $this->action->getEntity()->getId());
        } else {
            // store data and setup reload
            $js = new JsBlock([
                $this->loader->jsAddStoreData($this->actionData, true),
                $this->loader->jsLoad(
                    [
                        'step' => $this->isLastStep($step) ? 'final' : $this->getNextStep($step),
                        $this->name => $this->action->getEntity()->getId(),
                    ],
                    ['method' => 'POST'],
                    $this->loader->name
                ),
            ]);
        }

        return $js;
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
}
