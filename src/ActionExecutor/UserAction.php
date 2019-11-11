<?php
/**
 * Modal executor for action.
 */

namespace atk4\ui\ActionExecutor;

use atk4\core\HookTrait;
use atk4\data\UserAction\Generic;
use atk4\ui\Button;
use atk4\ui\Exception;
use atk4\ui\Form;
use atk4\ui\jsExpressionable;
use atk4\ui\jsFunction;
use atk4\ui\jsToast;
use atk4\ui\Message;
use atk4\ui\Modal;
use atk4\ui\View;

class UserAction extends Modal implements Interface_, jsInterface_
{
    use HookTrait;

    /**
     * @var jsExpressionable array|callable jsExpression to return if action was successful, e.g "new jsToast('Thank you')"
     */
    public $jsSuccess = null;

    /**
     * @var array Will collect action data while doing action step.
     */
    private $actionData = [];
    protected $actionInitialized = false;

    /**
     * The action to execute.
     *
     * @var null
     */
    public $action = null;

    /**
     * The action steps.
     *
     * @var null
     */
    private $steps = null;
    private $step = null;

    /**
     * The action step button.
     *
     * @var null
     */
    private $prevStepBtn = null;
    private $nextStepBtn = null;
    private $execActionBtn = null;
    private $btns = null;

    /**
     * A form for action argument and fields user entry.
     *
     * @var string
     */
    public $form = Form::class;

    /**
     * @var string can be "console", "text", or "html"
     */
    public $previewType = 'html';

    /**
     * View seed for displaying title for each step.
     *
     * @var array
     */
    public $stepTitle = ['args' => null, 'fields' => null, 'preview' => null];

    /**
     * The Loader that will execute all action step.
     *
     * @var null
     */
    public $loader = null;
    public $loaderUi = 'ui basic segment';
    public $loaderShim = [];

    public function init()
    {
        parent::init();
        $this->observeChanges();

        //Add buttons to modal for next and previous.
        $this->btns = (new View())->addStyle(['min-height' => '24px']);
        $this->prevStepBtn = $this->btns->add(new Button(['Prev']))->addStyle(['float' => 'left !important']);
        $this->nextStepBtn = $this->btns->add(new Button(['Next', 'blue']));
        $this->addButtonAction($this->btns);

        $this->loader = $this->add(['Loader', 'ui'   => $this->loaderUi, 'shim' => $this->loaderShim]);
        $this->loader->loadEvent = false;
        $this->actionData = $this->loader->jsGetStoreData()['session'];
    }

    /**
     * Will associate executor with the action.
     *
     * @param Generic $action
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return UserAction
     */
    public function setAction(Generic $action) :View
    {
        $this->action = $action;

        // get necessary step need prior to execute action.
        if ($this->steps = $this->getSteps($action)) {
            $this->title = $this->action->owner->getModelCaption();

            $this->btns->add($this->execActionBtn = (new Button([$this->action->caption, 'blue'])));

            // get current step.
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
            // set initial button state
            $this->jsSetBtnState($this, $this->step);
        }

        $this->actionInitialized = true;

        return $this;
    }

    public function renderView()
    {
        $id = $this->stickyGet($this->name);
        if ($id && $this->action->scope === 'single') {
            $this->action->owner->tryLoad($id);
        }
        $this->loader->set(function ($modal) {
            $this->jsSetBtnState($modal, $this->step);

            try {
                switch ($this->step) {
                    case 'args':
                        $this->doArgs($modal);
                        break;
                    case 'preview':
                        $this->doPreview($modal);
                        break;
                    case 'fields':
                        $this->doFields($modal);
                        break;
                    case 'final':
                        $this->doFinal($modal);
                        break;
                }
            } catch (\Exception $e) {
                $this->_handleException($e, $modal, $this->step);
            }
        });

        parent::renderView();
    }

    /**
     * Assign a View that will fire action execution.
     * If action require steps, it will automatically initialize
     * proper step to execute first.
     *
     * @param View   $view
     * @param array  $urlArgs
     * @param string $when
     * @param null   $selector
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return View
     */
    public function assignTrigger(View $view, array $urlArgs = [], string $when = 'click', $selector = null)
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger.');
        }

        if ($this->steps) {
            // use modal for stepping action.
            $urlArgs['step'] = $this->step;
            if ($this->action->enabled) {
                if ($selector) {
                    $view->on($when, $selector, [$this->show(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])]);
                } else {
                    $view->on($when, [$this->show(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])]);
                }
            } else {
                $view->addClass('disabled');
            }
        }

        return $this;
    }

    /**
     * Generate js for triggering action.
     *
     * @param array $urlArgs
     *
     * @throws Exception
     *
     * @return array
     */
    public function jsExecute(array $urlArgs = [])
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger.');
        }

        $urlArgs['step'] = $this->step;

        return [$this->show(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])];
    }

    /**
     * Do action args step.
     *
     * Will ask user to fill in arguments.
     *
     * @param View $modal
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    protected function doArgs(View $modal)
    {
        $this->_addStepTitle($modal, $this->step);

        $f = $this->addFormTo($modal);
        foreach ($this->action->args as $key=>$val) {
            if (is_numeric($key)) {
                throw new Exception(['Action arguments must be named', 'args' => $this->actions->args]);
            }

            if ($val instanceof \atk4\data\Model) {
                $f->addField($key, ['AutoComplete'])->setModel($val);
            } else {
                $f->addField($key, null, $val);
            }
        }

        // set args value if available.
        $this->setFormField($f, $this->actionData['args'] ?? [], $this->step);

        // setup exec, next and prev button handler for this step.
        $this->jsSetSubmitBtn($modal, $f, $this->step);
        $this->jsSetPrevHandler($modal, $this->step);

        $f->onSubmit(function ($f) use ($modal) {
            // collect arguments.
            $this->actionData['args'] = $f->model->get();

            return $this->jsStepSubmit($this->step);
        });
    }

    /**
     * Do action Fields step.
     *
     * @param View $modal
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    protected function doFields(View $modal)
    {
        $this->_addStepTitle($modal, $this->step);
        $f = $this->addFormTo($modal);

        if (is_bool($this->action->fields)) {
            $this->action->fields = array_keys($this->action->owner->getFields('editable'));
        }

        $f->setModel($this->action->owner, $this->action->fields);
        // set Fields value if set from another step.
        $this->setFormField($f, $this->actionData['fields'] ?? [], $this->step);

        // setup exec, next and prev button handler for this step.
        $this->jsSetSubmitBtn($modal, $f, $this->step);
        $this->jsSetPrevHandler($modal, $this->step);

        $f->onSubmit(function ($f) {
            // collect fields.
            $form_fields = $f->model->get();
            foreach ($this->action->fields as $key => $field) {
                $this->actionData['fields'][$field] = $form_fields[$field];
            }

            return $this->jsStepSubmit($this->step);
        });
    }

    /**
     * Do action preview step.
     *
     * @param View $modal
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    protected function doPreview(View $modal)
    {
        $this->_addStepTitle($modal, $this->step);

        if ($prev = $this->getPreviousStep($this->step)) {
            $chain = $this->loader->jsload([
                                               'step'      => $prev,
                                               $this->name => $this->action->owner->get('id'),
                                           ], ['method' => 'post'], $this->loader->name);

            $modal->js(true, $this->prevStepBtn->js()->on('click', new jsFunction([$chain])));
        }

        // setup executor button to perform action.
        $modal->js(
            true,
            $this->execActionBtn->js()->on(
                'click',
                new jsFunction(
                    [
                        $this->loader->jsload(
                            [
                                'step'      => 'final',
                                $this->name => $this->action->owner->get('id'),
                            ],
                            ['method' => 'post'],
                            $this->loader->name
                        ),
                    ]
                )
            )
        );

        $text = $this->getActionPreview();

        switch ($this->previewType) {
            case 'console':
                $preview = $modal->add(['ui'=>'inverted black segment', 'element'=>'pre']);
                $preview->set($text);
                break;
            case 'text':
                $preview = $modal->add(['ui'=>'basic segment']);
                $preview->set($text);
                break;
            case 'html':
                $preview = $modal->add(['ui'=>'basic segment']);
                $preview->template->setHTML('Content', $text);
                break;
        }
    }

    /**
     * Execute action when all step are completed.
     *
     * @param View $modal
     *
     * @throws \atk4\core\Exception
     */
    protected function doFinal(View $modal)
    {
        foreach ($this->actionData['fields'] ?? [] as $field => $value) {
            $this->action->owner[$field] = $value;
        }

        $return = $this->action->execute(...$this->_getActionArgs($this->actionData['args'] ?? []));

        $this->_jsSequencer($modal, $this->jsGetExecute($return, $this->action->owner->id));
    }

    /**
     * Return proper js statement need after action execution.
     *
     * @param $obj
     * @param $id
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    protected function jsGetExecute($obj, $id)
    {
        $success = is_callable($this->jsSuccess) ? call_user_func_array($this->jsSuccess, [$this, $this->action->owner, $id]) : $this->jsSuccess;

        if (is_array($success) || $success instanceof jsExpressionable) {
            return $success;
        }

        return [
            $this->hide(),
            $this->hook('afterExecute', [$obj, $id]),
            $success ?: new jsToast('Success'.(is_string($obj) ? (': '.$obj) : '')),
            $this->loader->jsClearStoreData(true),
        ];
    }

    /**
     * Get how many steps is required for this action.
     *
     * @param Generic $action The Model action.
     *
     * @return array|null
     */
    protected function getSteps(Generic $action) :?array
    {
        $steps = null;
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

    /**
     * Get next step after $step.
     *
     * @param string $step
     *
     * @return string|null
     */
    protected function getNextStep(string $step) :?string
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

    /**
     * Get previous step before $step.
     *
     * @param string $step
     *
     * @return string|null
     */
    protected function getPreviousStep(string $step) :?string
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

    /**
     * Check if $step is last one.
     *
     * @param string $step
     *
     * @return bool
     */
    protected function isLastStep(string $step) :bool
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

    /**
     * Check if step is first one.
     *
     * @param string $step
     *
     * @return bool
     */
    protected function isFirstStep(string $step) :bool
    {
        return $step === $this->steps[0];
    }

    /**
     * Will add field into form based on $fields array.
     *
     * @param Form   $form
     * @param array  $fields
     * @param string $step
     *
     * @throws \atk4\core\Exception
     *
     * @return Form
     */
    protected function setFormField(Form $form, array $fields, string $step) :Form
    {
        foreach ($fields as $k => $val) {
            $form->getField($k)->set($val);
        }

        $form->hook('onStep', [$step]);

        return $form;
    }

    /**
     * Get proper js after submitting a form in step.
     *
     * @param string $step
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    protected function jsStepSubmit(string $step)
    {
        try {
            if ($this->isLastStep($step)) {
                // collect argument and execute action.
                $return = $this->action->execute(...$this->_getActionArgs($this->actionData['args'] ?? []));
                $js = $this->jsGetExecute($return, $this->action->owner->id);
            } else {
                // store data and setup reload.
                $js = [
                    $this->loader->jsAddStoreData($this->actionData, true),
                    $this->loader->jsload([
                                              'step'      => $this->getNextStep($step),
                                              $this->name => $this->action->owner->get('id'),
                                          ], ['method' => 'post'], $this->loader->name),
                ];
            }

            return $js;
        } catch (\Exception $e) {
            $m = new Message('Error executing '.$this->action->caption, 'red');
            $m->init();
            $m->text->content = $e->getHTML();

            return $m;
        }
    }

    /**
     * Generate js for setting Buttons state based on current step.
     *
     * @param View   $view
     * @param string $step
     *
     * @throws \atk4\core\Exception
     */
    protected function jsSetBtnState(View $view, string $step)
    {
        if (count($this->steps) === 1) {
            $view->js(true, $this->prevStepBtn->js()->hide());
            $view->js(true, $this->nextStepBtn->js()->hide());
        } else {
            $view->js(true, $this->jsSetPrevState($step));
            $view->js(true, $this->jsSetNextState($step));
            $view->js(true, $this->jsSetExecState($step));
        }

        // reset button handler.
        $view->js(true, $this->execActionBtn->js(true)->off());
        $view->js(true, $this->nextStepBtn->js(true)->off());
        $view->js(true, $this->prevStepBtn->js(true)->off());
        $view->js(true, $this->nextStepBtn->js()->removeClass('disabled'));
        $view->js(true, $this->execActionBtn->js()->removeClass('disabled'));
    }

    /**
     * Generate js for Next btn state.
     *
     * @param string $step
     *
     * @return jsExpressionable
     */
    protected function jsSetNextState(string $step) :jsExpressionable
    {
        if ($this->isLastStep($step)) {
            return $this->nextStepBtn->js(true)->hide();
        } else {
            return $this->nextStepBtn->js(true)->show();
        }
    }

    /**
     * Generated js for Prev btn state.
     *
     * @param string $step
     *
     * @return jsExpressionable
     */
    protected function jsSetPrevState(string $step) :jsExpressionable
    {
        if ($this->isFirstStep($step)) {
            return $this->prevStepBtn->js(true)->hide();
        } else {
            return $this->prevStepBtn->js(true)->show();
        }
    }

    /**
     * Generate js for Exec button state.
     *
     * @param string $step
     *
     * @return jsExpressionable
     */
    protected function jsSetExecState(string $step) :jsExpressionable
    {
        if ($this->isLastStep($step)) {
            return $this->execActionBtn->js(true)->show();
        } else {
            return $this->execActionBtn->js(true)->hide();
        }
    }

    /**
     * Determine which button is responsible for submitting form on a specific step.
     *
     * @param View   $view
     * @param Form   $form
     * @param string $step
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    protected function jsSetSubmitBtn(View $view, Form $form, string $step)
    {
        if ($this->isLastStep($step)) {
            $view->js(true, $this->execActionBtn->js()->on('click', new jsFunction([$form->js()->form('submit')])));
        } else {
            // submit on next
            $view->js(true, $this->nextStepBtn->js()->on('click', new jsFunction([$form->js()->form('submit')])));
        }
    }

    /**
     * Generate js function for Previous button.
     *
     * @param View   $view
     * @param string $step
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    protected function jsSetPrevHandler(View $view, string $step)
    {
        if ($prev = $this->getPreviousStep($step)) {
            $chain = $this->loader->jsload([
                                               'step'      => $prev,
                                               $this->name => $this->action->owner->get('id'),
                                           ], ['method' => 'post'], $this->loader->name);

            $view->js(true, $this->prevStepBtn->js()->on('click', new jsFunction([$chain])));
        }
    }

    /**
     * Utility for setting form in each step.
     *
     * @param View $view
     *
     * @throws \atk4\core\Exception
     *
     * @return Form |null
     */
    protected function addFormTo(View $view) :Form
    {
        $f = $view->add($this->form);
        $f->buttonSave->destroy();

        return $f;
    }

    /**
     * Utility for setting Title for each step.
     *
     * @param View   $view
     * @param string $step
     *
     * @throws \atk4\core\Exception
     */
    private function _addStepTitle(View $view, string $step)
    {
        if ($title = $this->stepTitle[$step] ?? null) {
            $view->add($title);
        }
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
            $args[] = $this->actionData['args'][$key];
        }

        return $this->action->preview(...$args);
    }

    /**
     * Utility for retrieving Argument.
     *
     * @param array $data
     *
     * @return array
     */
    private function _getActionArgs(array $data) :array
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
     * @param View                   $view
     * @param array|jsExpressionable $js
     *
     * @throws \atk4\core\Exception
     */
    private function _jsSequencer(View $view, $js)
    {
        if (is_array($js)) {
            foreach ($js as $jq) {
                $this->_jsSequencer($view, $jq);
            }
        } else {
            $view->js(true, $js);
        }
    }

    /**
     * Handle exception.
     *
     * @param $e
     * @param $view
     * @param $step
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    private function _handleException($e, $view, $step)
    {
        $msg = $view->add(['Message', 'Error:', 'type' => 'error']);
        $msg->text->addParagraph($e->getMessage());
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
