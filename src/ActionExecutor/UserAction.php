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
use atk4\ui\Modal;

class UserAction extends Modal implements Interface_
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
        $btns = (new \atk4\ui\View(['ui' => 'buttons']))->addStyle(['min-height' => '24px']);
        $this->prevStepBtn = $btns->add(new Button(['Prev']));
        $this->nextStepBtn = $btns->add(new Button(['Next']));
        $this->addButtonAction($btns);

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
    public function setAction(Generic $action)
    {
        $this->action = $action;
        // get necessary step need prior to execute action.
        if ($this->steps = $this->getSteps($action)) {
            $this->title = $this->action->owner->getModelCaption();

            $this->addButtonAction($this->execActionBtn = (new Button([$this->action->caption, 'blue']))->addStyle(['float' => 'left !important']));

            // get current step.
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
            // set initial button state
            $this->jsSetBtnState($this, $this->step);

            $id = $this->stickyGet($this->name);
            if ($id && $this->action->scope === 'single') {
                $this->action->owner->tryLoad($id);
            }

            $this->loader->set(function ($modal) {
                $this->jsSetBtnState($modal, $this->step);

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
            });
        }

        $this->actionInitialized = true;

        return $this;
    }

    /**
     * Assign a Button that will fire action execution.
     * If action require steps, it will automatically initialize
     * proper step to execute first.
     *
     * If action does not require any step, then it will assign
     * a jsEvent executor to button.
     *
     * @param Button $btn
     * @param array  $urlArgs
     * @param string $when
     *
     * @throws \atk4\core\Exception
     *
     * @return UserAction
     */
    public function assignTrigger(Button $btn, array $urlArgs = [], string $when = 'click')
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger.');
        }

        if ($this->steps) {
            // use modal for stepping action.
            $urlArgs['step'] = $this->step;
            $urlArgs['action'] = $this->action->short_name;
            if ($this->action->enabled) {
                $btn->on($when, [$this->show(), $this->loader->jsLoad($urlArgs)]);
            } else {
                $btn->addClass('disabled');
            }
        } else {
            $executor = new \atk4\ui\ActionExecutor\jsEvent($btn, $this->action, $urlArgs, $this->action->args);
            $btn->on('click', $executor, ['confirm'=> $this->action->ui['confirm'] ?? 'Are you sure?']);
        }

        return $this;
    }

    /**
     * Do action args step.
     *
     * Will ask user to fill in arguments.
     *
     * @param $modal
     *
     * @throws Exception
     */
    protected function doArgs($modal)
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
        $this->setFormField($f, $this->actionData['args'] ?? null, $this->step);

        // setup exec, next and prev button handler for this step.
        if ($this->isLastStep($this->step)) {
            $modal->js(true, $this->execActionBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
        } else {
            // submit on next
            $modal->js(true, $this->nextStepBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
        }
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
     * @param $modal
     *
     * @throws Exception
     */
    protected function doFields($modal)
    {
        $this->_addStepTitle($modal, $this->step);
        $f = $this->addFormTo($modal);

        if (is_bool($this->action->fields)) {
            $this->action->fields = array_keys($this->action->owner->getFields('editable'));
        }

        $f->setModel($this->action->owner, $this->action->fields);
        // set Fields value if set from another step.
        $this->setFormField($f, $this->actionData['fields'] ?? null, $this->step);

        // setup next and prev handler for this step.
        if ($this->isLastStep($this->step)) {
            $modal->js(true, $this->execActionBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
        } else {
            // submit on next
            $modal->js(true, $this->nextStepBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
        }
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
     * @param $modal
     *
     * @throws Exception
     */
    protected function doPreview($modal)
    {
        $this->_addStepTitle($modal, $this->step);

        if ($prev = $this->getPreviousStep($this->step)) {
            $chain = $this->loader->jsload([
                                               'action'    => $this->action->short_name,
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
                                'action'    => $this->action->short_name,
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
     * @param $modal
     *
     * @throws \atk4\core\Exception
     */
    protected function doFinal($modal)
    {
        foreach ($this->actionData['fields'] ?? [] as $field => $value) {
            $this->action->owner[$field] = $value;
        }

        $return = $this->action->execute(...$this->_getActionArgs($this->actionData['args'] ?? null));

        $success = is_callable($this->jsSuccess) ? call_user_func_array($this->jsSuccess, [$this, $this->action->owner]) : $this->jsSuccess;

        $js = $this->hook('afterExecute', [$return]) ?: $success ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : ''));

        $this->_jsSequencer($modal, $js);
        $modal->js(true, $this->hide());
        $modal->js(true, $this->loader->jsClearStoreData(true));
    }

    /**
     * Get how many steps is required for this action.
     *
     * @param $action
     *
     * @return array|null
     */
    protected function getSteps($action)
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
     * @param $step
     *
     * @return mixed|null
     */
    protected function getNextStep($step)
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
     * @param $step
     *
     * @return mixed|null
     */
    protected function getPreviousStep($step)
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
     * @param $step
     *
     * @return bool
     */
    protected function isLastStep($step)
    {
        $isLast = false;
        $step_count = count($this->steps);
        foreach ($this->steps as $k => $s) {
            if ($s === $step) {
                $isLast = $k === $step_count - 1 ? true : false;
                break;
            }
        }

        return $isLast;
    }

    /**
     * Check if step is first one.
     *
     * @param $step
     *
     * @return bool
     */
    protected function isFirstStep($step)
    {
        return $step === $this->steps[0];
    }

    /**
     * Will add field into form based on $fields array.
     *
     * @param $form
     * @param $fields
     * @param $step
     *
     * @return mixed
     */
    protected function setFormField($form, $fields, $step)
    {
        if ($fields) {
            foreach ($fields as $k => $val) {
                $form->getField($k)->set($val);
            }
        }

        $form->hook('onStep', [$step]);

        return $form;
    }

    /**
     * Get proper js after submitting a form in step.
     *
     * @param $step
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    protected function jsStepSubmit($step)
    {
        if ($this->isLastStep($step)) {
            // collect argument and execute action.
            $return = $this->action->execute(...$this->_getActionArgs($this->actionData['args'] ?? []));

            $js = [
                $this->hide(),
                $this->hook('afterExecute', [$return]) ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : '')),
            ];
        } else {
            // store data.
            $js[] = $this->loader->jsAddStoreData($this->actionData, true);
            $js[] = $this->loader->jsload([
                                              'action'    => $this->action->short_name,
                                              'step'      => $this->getNextStep($step),
                                              $this->name => $this->action->owner->get('id'),
                                          ], ['method' => 'post'], $this->loader->name);
        }

        return $js;
    }

    /**
     * Generate js for setting Buttons state based on current step.
     *
     * @param $view
     * @param $step
     */
    protected function jsSetBtnState($view, $step)
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
    }

    /**
     * Generate js for Next btn state.
     *
     * @param $step
     *
     * @return |null
     */
    protected function jsSetNextState($step)
    {
        $chain = null;

        if ($this->isLastStep($step)) {
            $chain = $this->nextStepBtn->js(true)->addClass('disabled');
        } else {
            $chain = $this->nextStepBtn->js(true)->removeClass('disabled');
        }

        return $chain;
    }

    /**
     * Generated js for Prev btn state.
     *
     * @param $step
     *
     * @return |null
     */
    protected function jsSetPrevState($step)
    {
        $chain = null;

        if ($this->isFirstStep($step)) {
            $chain = $this->prevStepBtn->js(true)->addClass('disabled');
        } else {
            $chain = $this->prevStepBtn->js(true)->removeClass('disabled');
        }

        return $chain;
    }

    /**
     * Generate js for Exec button state.
     *
     * @param $step
     *
     * @return mixed
     */
    protected function jsSetExecState($step)
    {
        if ($this->isLastStep($step)) {
            return $this->execActionBtn->js(true)->removeClass('disabled');
        } else {
            return $this->execActionBtn->js(true)->addClass('disabled');
        }
    }

    /**
     * Generate js function for Previous button.
     *
     * @param $view
     * @param $step
     *
     * @throws Exception
     */
    protected function jsSetPrevHandler($view, $step)
    {
        if ($prev = $this->getPreviousStep($step)) {
            $chain = $this->loader->jsload([
                                               'action'    => $this->action->short_name,
                                               'step'      => $prev,
                                               $this->name => $this->action->owner->get('id'),
                                           ], ['method' => 'post'], $this->loader->name);

            $view->js(true, $this->prevStepBtn->js()->on('click', new jsFunction([$chain])));
        }
    }

    /**
     * Utility for setting form in each step.
     *
     * @param $view
     *
     * @return |null
     */
    protected function addFormTo($view)
    {
        $f = null;
        if ($this->form) {
            $f = $view->add($this->form);
        } else {
            $f = $view->add('Form');
        }
        $f->buttonSave->destroy();

        return $f;
    }

    /**
     * Utility for setting Title for each step.
     *
     * @param $view
     * @param $step
     */
    private function _addStepTitle($view, $step)
    {
        if ($title = $this->stepTitle[$step] ?? null) {
            $view->add($title);
        }
    }

    /**
     * Utility for retrieving Argument.
     *
     * @param $data
     *
     * @return array
     */
    private function _getActionArgs($data)
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
     * @param $view
     * @param $js
     */
    private function _jsSequencer($view, $js)
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
}
