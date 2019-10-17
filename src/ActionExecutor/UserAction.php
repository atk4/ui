<?php
/**
 * Created by abelair.
 * Date: 2019-10-15
 * Time: 2:18 p.m.
 */

namespace atk4\ui\ActionExecutor;

use atk4\core\HookTrait;
use atk4\ui\Button;
use atk4\ui\Form;
use atk4\ui\Exception;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;
use atk4\ui\jsToast;
use atk4\ui\Modal;

class UserAction extends Modal implements Interface_
{
    use HookTrait;

    public $action = null;
    public $currentAction = null;
    public $steps = null;
    public $step = null;

    public $preview = null;

    public $prevAction = null;
    public $nextAction = null;

    /**
     * @var string can be "console", "text", or "html"
     */
    public $previewType = 'html';
    public $form = null;

    public $argsTitle = 'Fill in require arguments:';

    public function init()
    {
        parent::init();
        $this->observeChanges();
        //Add buttons to modal for next and previous.
        $btns = new \atk4\ui\View(['ui' => 'buttons']);
        $this->prevAction = $btns->add(new Button(['Prev']));
        $this->nextAction = $btns->add(new Button(['Next']));

        $this->addButtonAction($btns);

        $this->jsStoreData(['allo' => 'bonjour', 'blbl' => 'youljjj']);

    }

    /**
     * Will associate executor with the action.
     *
     * @param \atk4\data\UserAction\Action $action
     */
    public function setAction(\atk4\data\UserAction\Generic $action)
    {
        $this->action = $action;
        // get current step.
        $this->step = $this->stickyGet('step');

        $id = $this->stickyGet($this->name);
        if ($id && $this->action->scope === 'single') {
            $this->action->owner->tryLoad($id);
        }
        $this->currentAction = $this->stickyGet(('action'));

        $this->setSteps();

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
        }

        return $this;
    }

    public function setSteps()
    {
        if ($this->action->args) {
            $this->steps[] = 'args';
        }
        if ($this->action->preview) {
            $this->steps[] ='preview';
        }
        if ($this->action->fields) {
            $this->steps[] = 'fields';
        }

        if (!$this->step) {
            $this->step = $this->steps[0];
        } else if ($this->currentAction === $this->action->name && !$this->isLastStep($this->step)) {
            foreach ($this->steps as $k => $s) {
                if ($this->step === $s) {
                    $this->step = $this->steps[$k + 1];
                    break;
                }
            }
        }
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

    public function doFields()
    {
        $this->set(function($modal) {
            $f = $modal->add('Form');

            if (is_bool($this->action->fields)) {
                $this->action->fields = array_keys($this->action->owner->getFields('editable'));
            }
            $f->setModel($this->action->owner, $this->action->fields);

            $f->onSubmit(function ($f) {
                //return $this->jsExecute();
            });
        });
    }

    public function doArgs()
    {
        $this->set(function($modal) {
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

            if ($this->isFirstStep($this->step)) {
                $modal->js(true, $this->prevAction->js(true)->hide());
            }

            if (!$this->getNextStep($this->step)) {
                $modal->js(true, $this->nextAction->js(true)->text($this->action->caption));
            }

            $modal->js(true, $this->nextAction->js()->on('click', new jsFunction([$f->js()->form('submit')])));

            $f->onSubmit(function ($f) {

                if ($this->isLastStep($this->step)) {
                    // collect argument
                    $args = [];
                    $form_args = $f->model->get();

                    foreach ($this->action->args as $key => $val) {
                        $args[] = $form_args[$key];
                    }

                    $return = $this->action->execute(...$args);

                    $js = [
                        $this->hide(),
                        $this->hook('afterExecute', [$return]) ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : '')),
                    ];

                    return $js;
                }

                $js[] = $this->jsReload(['action' => $this->action->short_name, 'step' => $this->getNextStep($this->step), $this->name => $this->action->owner->get('id')]);
                return $js;
            });

            $this->js(true)->modal('refresh');
        });
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

    public function doPreview()
    {

        $this->set(function($modal) {

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
        $this->js(true)->modal('show');
    }

    public function getActionPreview()
    {
        $args = [];

        foreach ($this->action->args as $key => $val) {
//            $args[] = $this->arguments[$key];
        }

        return $this->action->preview(...$args);
    }

    public function assignTrigger($btn, $arg = [], $when = 'click')
    {
        if ($this->action->enabled) {
            $btn->on($when, $this->jsTrigger($arg));
        } else {
            $btn->addClass('disabled');
        }
    }

    public function jsTrigger($arg = [])
    {
        $arg['step'] = $this->step;
        $arg['action'] = $this->action->short_name;
        return $this->show($arg);
    }
}