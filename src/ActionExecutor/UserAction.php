<?php
/**
 * Created by abelair.
 * Date: 2019-10-15
 * Time: 2:18 p.m.
 */

namespace atk4\ui\ActionExecutor;

use atk4\core\HookTrait;
use atk4\data\UserAction\Generic;
use atk4\ui\Button;
use atk4\ui\Form;
use atk4\ui\Exception;
use atk4\ui\jsExpression;
use atk4\ui\jsExpressionable;
use atk4\ui\jsFunction;
use atk4\ui\jsToast;
use atk4\ui\Loader;
use atk4\ui\Modal;
use atk4\ui\View;

class UserAction extends Modal implements Interface_
{
    use HookTrait;

    /**
     * @var jsExpressionable array|callable jsExpression to return if action was successful, e.g "new jsToast('Thank you')"
     */
    public $jsSuccess = null;

    protected $actionData = [];
    protected $actionInitialized = false;

    public $action = null;
    public $currentAction = null;
    public $steps = null;
    public $step = null;

    public $preview = null;

    public $prevStepBtn = null;
    public $nextStepBtn = null;
    public $execActionBtn = null;
    public $btns = null;

    /**
     * @var string can be "console", "text", or "html"
     */
    public $previewType = 'html';

    public $argsTitle = 'Fill in require arguments:';

    public $loader = null;
    public $loaderUi = 'ui basic segment';
    public $loaderShim = [];

    public function init()
    {
        parent::init();
        $this->observeChanges();

        //Add buttons to modal for next and previous.
        $this->btns        = new \atk4\ui\View(['ui' => 'buttons']);
        $this->prevStepBtn = $this->btns->add(new Button(['Prev']));
        $this->nextStepBtn = $this->btns->add(new Button(['Next']));
        $this->addButtonAction($this->btns);

        $this->loader  = $this->add(['Loader', 'ui'   => $this->loaderUi, 'shim' => $this->loaderShim]);
        $this->loader->loadEvent = false;
        $this->actionData = $this->loader->jsGetStoreData()['session'];
    }

    /**
     * Will associate executor with the action.
     *
     * @param Generic $action
     *
     * @return UserAction
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function setAction(Generic $action)
    {
        $this->action = $action;
        $this->steps = $this->getSteps($action);
        $this->addButtonAction($this->execActionBtn = (new Button([$this->action->caption, 'blue']))->addStyle(['float' => 'left !important']));

        // get current step.
        $this->step = $this->stickyGet('step') ?? $this->steps[0];
        // set initial button state
        $this->jsSetBtnState($this, $this->step);

        $id = $this->stickyGet($this->name);
        if ($id && $this->action->scope === 'single') {
            $this->action->owner->tryLoad($id);
        }
        $this->currentAction = $this->stickyGet(('action'));

        switch ($this->step) {
            case 'args':
                $this->doArgs();
                break;
            case 'preview':
                $this->doPreview();
                break;
            case 'fields':
                $this->doFields();
                break;
            case 'final':
                $this->doAction();
                break;
        }

        $this->actionInitialized = true;
        return $this;
    }

    /**
     * Assign a Button that will fire action execution.
     *
     * @param Button $btn
     * @param array $args
     * @param string $when
     *
     * @throws \atk4\core\Exception
     */
    public function assignTrigger(Button $btn, array $args = [], string $when = 'click')
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger.');
        }

        $args['step'] = $this->step;
        $args['action'] = $this->action->short_name;
        if (!$this->action) {
            throw new Exception('Action need to be setup prior to assing trigger.');
        }

        if ($this->action->enabled) {
            $btn->on($when, [$this->show(), $this->loader->jsLoad($args)]);
        } else {
            $btn->addClass('disabled');
        }
    }


    public function getSteps($action)
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

    public function getNextStep($step)
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

    public function isLastStep($step)
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

    public function isFirstStep($step)
    {
        return $step === $this->steps[0];
    }

    /**
     * Generate js for setting Buttons state based on current step.
     *
     * @param $view
     * @param $step
     */
    public function jsSetBtnState($view, $step)
    {
        $view->js(true, $this->jsSetPrevNextState($step));
        $view->js(true, $this->jsSetExecState($step));
        $view->js(true, $this->execActionBtn->js(true)->off());
        $view->js(true, $this->nextStepBtn->js(true)->off());
        $view->js(true, $this->prevStepBtn->js(true)->off());
    }

    public function jsSetPrevNextState($step) {
        $chain = null;

        if ($this->isLastStep($step)) {
//            return $this->btns->js(true)->hide();
        }

        if ($this->isFirstStep($step)) {
            $chain =  $this->prevStepBtn->js(true)->addClass('disabled');
        } else {
            $chain = $this->prevStepBtn->js(true)->removeClass('disabled');
        }

        return $chain;
    }

    public function jsSetExecState($step)
    {
        if ($this->isLastStep($step)) {
            return $this->execActionBtn->js(true)->removeClass('disabled');
        } else {
            return $this->execActionBtn->js(true)->addClass('disabled');
        }
    }

    public function doArgs()
    {
        $this->loader->set(function($modal) {
            $this->jsSetBtnState($modal, $this->step);
            $modal->add(['Header', $this->argsTitle, 'size' => 4,]);
            $f = $modal->add(['form']);

            foreach ($this->action->args as $key=>$val) {
                if (is_numeric($key)) {
                    throw new Exception(['Action arguments must be named', 'args'=>$this->actions->args]);
                }

                if ($val instanceof \atk4\data\Model) {
                    $f->addField($key, ['AutoComplete'])->setModel($val);
                } else {
                    $f->addField($key, null, $val);
                }
            }

            $f->buttonSave->destroy();

            if ($this->isLastStep($this->step)) {
                $modal->js(true, $this->execActionBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
            } else {
                // submit on next
                $modal->js(true, $this->nextStepBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
            }

            $f->onSubmit(function ($f) use ($modal) {

                // collect arguments.
                $this->actionData['args'] = $f->model->get();

                if ($this->isLastStep($this->step)) {
                    // Execute action.
                    $return = $this->action->execute(...$this->actionData['args']);

                    $js = [
                        $this->hide(),
//                        $this->execActionBtn->js()->off(),
//                        $this->nextStepBtn->js()->off(),
                        $this->hook('afterExecute', [$return]) ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : '')),
                    ];

                } else {
                    // move to next step.
//                    $js[] = $this->nextStepBtn->js()->off();
                    $js[] = $this->loader->jsAddStoreData($this->actionData, true);

                    $js[] = $this->loader->jsload([
                                                      'action'    => $this->action->short_name,
                                                      'step'      => $this->getNextStep($this->step),
                                                      $this->name => $this->action->owner->get('id')
                                                  ],
                                                  ['method' => 'post'], $this->loader->name
                    );
                }

                return $js;
            });
        });
    }

    public function doFields()
    {
        $this->loader->set(function($modal) {
            $this->jsSetBtnState($modal, $this->step);

            $f = $modal->add('Form');

            if (is_bool($this->action->fields)) {
                $this->action->fields = array_keys($this->action->owner->getFields('editable'));
            }

            $f->setModel($this->action->owner, $this->action->fields);
            $f->buttonSave->destroy();

            if ($this->isLastStep($this->step)) {
                $modal->js(true, $this->execActionBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
            } else {
                // submit on next
                $modal->js(true, $this->nextStepBtn->js()->on('click', new jsFunction([$f->js()->form('submit')])));
            }


            $f->onSubmit(function ($f) {

                // collect fields.
                $form_fields = $f->model->get();

                foreach ($this->action->fields as $key => $field) {
                    $this->actionData['fields'][$field] = $form_fields[$field];
                }

                if ($this->isLastStep($this->step)) {
                    // collect argument and execute action.
                    $args = [];

                    $return = $this->action->execute(...$args);

                    $js = [
                        $this->hide(),
                        $this->execActionBtn->js()->off(),
                        $this->nextStepBtn->js()->off(),
                        $this->hook('afterExecute', [$return]) ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : '')),
                    ];

                } else {
                    $js[] = $this->nextStepBtn->js()->off();
                    $js[] = $this->loader->jsAddStoreData($this->actionData, true);
                    $js[] = $this->loader->jsload([
                                                      'action'    => $this->action->short_name,
                                                      'step'      => $this->getNextStep($this->step),
                                                      $this->name => $this->action->owner->get('id')
                                                  ], ['method' => 'post'], $this->loader->name);
                }

                return $js;
            });
        });
    }

    public function doPreview()
    {
        $this->loader->set(function($modal) {
            $this->jsSetBtnState($modal, $this->step);
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
                                $this->name => $this->action->owner->get('id')
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
                    $this->preview = $modal->add(['ui'=>'inverted black segment', 'element'=>'pre']);
                    $this->preview->set($text);
                    break;
                case 'text':
                    $this->preview = $modal->add(['ui'=>'segment']);
                    $this->preview->set($text);
                    break;
                case 'html':
                    $this->preview = $modal->add(['ui'=>'segment']);
                    $this->preview->template->setHTML('Content', $text);
                    break;
            }
        });
    }

    public function doAction()
    {
        $this->loader->set(function($modal) {
            $args = [];

            foreach ($this->action->args as $key => $val) {
                $args[] = $this->actionData['args'][$key];
            }

            foreach ($this->actionData['fields'] as $field => $value) {
                $this->action->owner[$field] = $value;
            }

            $return = $this->action->execute(...$args);

            $success = is_callable($this->jsSuccess) ? call_user_func_array($this->jsSuccess, [$this, $this->action->owner]) : $this->jsSuccess;


            $js = $this->hook('afterExecute', [$return]) ?: $success ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : ''));

            $this->jsSequencer($modal, $js);
            $modal->js(true, $this->hide());
        });
    }

    private function jsSequencer($view, $js)
    {
        if (is_array($js)) {
            foreach ($js as $jq) {
                $this->jsSequencer($view, $jq);
            }
        } else {
            $view->js(true, $js);
        }
    }


    public function getActionPreview()
    {
        $args = [];

        foreach ($this->action->args as $key => $val) {
            $args[] = $this->actionData['args'][$key];
        }

        return $this->action->preview(...$args);
    }

}