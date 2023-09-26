<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Data\Model\EntityFieldPair;
use Atk4\Data\Reference\ContainsMany;
use Atk4\Data\ValidationException;
use Atk4\Ui\Form\Control;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsConditionalForm;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;

class Form extends View
{
    use \Atk4\Core\HookTrait;

    /** Executed when form is submitted */
    public const HOOK_SUBMIT = self::class . '@submit';
    /** Executed when form is submitted */
    public const HOOK_DISPLAY_ERROR = self::class . '@displayError';
    /** Executed when form is submitted */
    public const HOOK_DISPLAY_SUCCESS = self::class . '@displaySuccess';
    /** Executed when self::loadPost() method is called. */
    public const HOOK_LOAD_POST = self::class . '@loadPost';

    public $ui = 'form';
    public $defaultTemplate = 'form.html';

    /** @var JsCallback Callback handling form submission. */
    public $cb;

    /** @var bool Set this to false in order to prevent from leaving page if form is not submit. */
    public $canLeave = true;

    /**
     * HTML <form> element, all inner form controls are linked to it on render
     * with HTML form="form_id" attribute.
     *
     * @var View
     */
    public $formElement;

    /** @var Form\Layout A current layout of a form, needed if you call Form->addControl(). */
    public $layout;

    /** @var array<string, Control> List of form controls currently registered with this form. */
    public $controls = [];

    /**
     * Will point to the Save button. If you don't want to have save button, then set this to false
     * or destroy it. Initialized by initLayout().
     *
     * @var Button|array|false Button object, seed or false to not show button at all
     */
    public $buttonSave = [Button::class, 'Save', 'class.primary' => true];

    /**
     * When form is submitted successfully, this template is used by method
     * jsSuccess() to replace form contents.
     *
     * WARNING: may be removed in the future as we refactor into using Message class
     *          and remove the form-success.html template then.
     *
     * @var string
     */
    public $successTemplate = 'form-success.html';

    /**
     * Collection of field's conditions for displaying a target field on the form.
     *
     * Specifying a condition for showing a target field required the name of the target field
     * and the rules to show that target field. Each rule contains a source field's name and a condition for the
     * source field. When each rule is true, then the target field is show on the form.
     *
     *  Combine multiple rules for showing a field.
     *   ex: ['target' => ['source1' => 'notEmpty', 'source2' => 'notEmpty']]
     *   Show 'target' if 'source1' is not empty AND 'source2' is notEmpty.
     *
     *  Combine multiple condition to the same source field.
     *   ex: ['target' => ['source1' => ['notEmpty', 'number']]
     *   Show 'target' if 'source1 is notEmpty AND is a number.
     *
     *  Combine multiple arrays of rules will OR the rules for the target field.
     *  ex: ['target' => [['source1' => ['notEmpty', 'number']], ['source1' => 'isExactly[5]']
     *  Show "target' if 'source1' is not empty AND is a number
     *      OR
     *  Show 'target' if 'source1' is exactly 5.
     */
    public array $controlDisplayRules = [];

    /**
     * Default CSS selector for JsConditionalForm.
     * Should match the CSS class name of the control.
     * Fomantic-UI use the class name "field".
     *
     * @var string
     */
    public $controlDisplaySelector = '.field';

    /** @var array Use this apiConfig variable to pass API settings to Fomantic-UI in .api(). */
    public $apiConfig = [];

    /** @var array Use this formConfig variable to pass settings to Fomantic-UI in .from(). */
    public $formConfig = [];

    // {{{ Base Methods

    protected function init(): void
    {
        parent::init();

        $this->formElement = View::addTo($this, ['element' => 'form', 'shortName' => 'form'], ['FormElementOnly']);

        // redirect submit event to native form element
        $this->on(
            'submit',
            new JsExpression('if (event.target === this) { event.stopImmediatePropagation(); [] }', [new JsBlock([$this->formElement->js()->trigger('submit')])]),
            ['stopPropagation' => false]
        );

        $this->initLayout();

        // set CSS loader for this form
        $this->setApiConfig(['stateContext' => $this]);

        $this->cb = JsCallback::addTo($this, [], [['desired_name' => 'submit']]);
    }

    protected function initLayout(): void
    {
        if (!is_object($this->layout)) { // @phpstan-ignore-line
            $this->layout = Factory::factory($this->layout ?? [Form\Layout::class]); // @phpstan-ignore-line
        }
        $this->layout->form = $this;
        $this->add($this->layout);

        // add save button in layout
        if ($this->buttonSave) {
            $this->buttonSave = $this->layout->addButton($this->buttonSave);
            $this->buttonSave->setAttr('tabindex', 0);
            $jsSubmit = $this->js()->form('submit');
            $this->buttonSave->on('click', $jsSubmit);
            $this->buttonSave->on('keypress', new JsExpression('if (event.keyCode === 13) { [] }', [new JsBlock([$jsSubmit])]));
        }
    }

    /**
     * Setter for control display rules.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setControlsDisplayRules($rules = [])
    {
        $this->controlDisplayRules = $rules;

        return $this;
    }

    /**
     * Set display rule for a group collection.
     *
     * @param array       $rules
     * @param string|View $selector
     *
     * @return $this
     */
    public function setGroupDisplayRules($rules = [], $selector = '.atk-form-group')
    {
        if (is_object($selector)) {
            $selector = '#' . $selector->getHtmlId();
        }

        $this->controlDisplayRules = $rules;
        $this->controlDisplaySelector = $selector;

        return $this;
    }

    /**
     * Associates form with the model but also specifies which of Model
     * fields should be added automatically.
     *
     * If $actualFields are not specified, then all "editable" fields
     * will be added.
     *
     * @param array<int, string>|null $fields
     */
    public function setModel(Model $entity, array $fields = null): void
    {
        $entity->assertIsEntity();

        // set model for the form and also for the current layout
        try {
            parent::setModel($entity);

            $this->layout->setModel($entity, $fields);
        } catch (Exception $e) {
            throw $e->addMoreInfo('model', $entity);
        }
    }

    /**
     * Adds callback in submit hook.
     *
     * @param \Closure($this): (JsExpressionable|View|string|void) $fx
     *
     * @return $this
     */
    public function onSubmit(\Closure $fx)
    {
        $this->onHook(self::HOOK_SUBMIT, $fx);

        $this->cb->set(function () {
            try {
                $this->loadPost();

                $response = $this->hook(self::HOOK_SUBMIT);
                // TODO JsBlock::fromHookResult() cannot be used here as long as the result can contain View
                if (is_array($response) && count($response) === 1) {
                    $response = reset($response);
                }

                return $response;
            } catch (ValidationException $e) {
                $response = new JsBlock();
                foreach ($e->errors as $field => $error) {
                    if (!isset($this->controls[$field])) {
                        throw $e;
                    }

                    $response->addStatement($this->jsError($field, $error));
                }

                return $response;
            }
        });

        return $this;
    }

    /**
     * Return form control associated with the field.
     *
     * @param string $name Name of the control
     */
    public function getControl(string $name): Control
    {
        return $this->controls[$name];
    }

    /**
     * Causes form to generate error.
     *
     * @param string $errorMessage
     */
    public function jsError(string $fieldName, $errorMessage): JsExpressionable
    {
        // by using this hook you can overwrite default behavior of this method
        if ($this->hookHasCallbacks(self::HOOK_DISPLAY_ERROR)) {
            return JsBlock::fromHookResult($this->hook(self::HOOK_DISPLAY_ERROR, [$fieldName, $errorMessage]));
        }

        return new JsBlock([$this->js()->form('add prompt', $fieldName, $errorMessage)]);
    }

    /**
     * Causes form to generate success message.
     *
     * @param View|string $success     Success message or a View to display in modal
     * @param string      $subHeader   Sub-header
     * @param bool        $useTemplate Backward compatibility
     */
    public function jsSuccess($success = 'Success', $subHeader = null, bool $useTemplate = true): JsExpressionable
    {
        $response = null;
        // by using this hook you can overwrite default behavior of this method
        if ($this->hookHasCallbacks(self::HOOK_DISPLAY_SUCCESS)) {
            return JsBlock::fromHookResult($this->hook(self::HOOK_DISPLAY_SUCCESS, [$success, $subHeader]));
        }

        if ($success instanceof View) {
            $response = $success;
        } elseif ($useTemplate) {
            $responseTemplate = $this->getApp()->loadTemplate($this->successTemplate);
            $responseTemplate->set('header', $success);

            if ($subHeader) {
                $responseTemplate->set('message', $subHeader);
            } else {
                $responseTemplate->del('p');
            }

            $response = $this->js()->html($responseTemplate->renderToHtml());
        } else {
            $response = new Message([$success, 'type' => 'success', 'icon' => 'check']);
            $response->setApp($this->getApp());
            $response->invokeInit();
            $response->text->addParagraph($subHeader);
        }

        return $response;
    }

    // }}}

    // {{{ Layout Manipulation

    /**
     * Add form control into current layout. If no layout, create one. If no model, create blank one.
     *
     * @param array<mixed>|Control $control
     * @param array<mixed>         $fieldSeed
     */
    public function addControl(string $name, $control = [], array $fieldSeed = []): Control
    {
        return $this->layout->addControl($name, $control, $fieldSeed);
    }

    /**
     * Add header into the form, which appears as a separator.
     *
     * @param string|array $title
     */
    public function addHeader($title = null): void
    {
        $this->layout->addHeader($title);
    }

    /**
     * Creates a group of fields and returns layout.
     *
     * @param string|array $title
     *
     * @return Form\Layout
     */
    public function addGroup($title = null)
    {
        return $this->layout->addGroup($title);
    }

    /**
     * Returns JS Chain that targets INPUT element of a specified field. This method is handy
     * if you wish to set a value to a certain field.
     *
     * @return Jquery
     */
    public function jsInput(string $name): JsExpressionable
    {
        return $this->layout->getControl($name)->jsInput();
    }

    // }}}

    // {{{ Internals

    /**
     * Provided with a Agile Data Model Field, this method have to decide
     * and create instance of a View that will act as a form-control. It takes
     * various input and looks for hints as to which class to use:.
     *
     * 1. The $seed argument is evaluated
     * 2. $f->ui['form'] is evaluated if present
     * 3. $f->type is converted into seed and evaluated
     * 4. lastly, falling back to Line, Dropdown (based on $reference and $enum)
     *
     * @param array<string, mixed> $controlSeed
     */
    public function controlFactory(Field $field, $controlSeed = []): Control
    {
        $this->model->assertIsEntity($field->getOwner());

        $fallbackSeed = [Control\Line::class];

        if ($field->type === 'json' && $field->hasReference()) {
            $limit = ($field->getReference() instanceof ContainsMany) ? 0 : 1;
            $model = $field->getReference()->refModel($this->model);
            $fallbackSeed = [Control\Multiline::class, 'model' => $model, 'rowLimit' => $limit, 'caption' => $model->getModelCaption()];
        } elseif ($field->type !== 'boolean') {
            if ($field->enum) {
                $fallbackSeed = [Control\Dropdown::class, 'values' => array_combine($field->enum, $field->enum)];
            } elseif ($field->values) {
                $fallbackSeed = [Control\Dropdown::class, 'values' => $field->values];
            } elseif ($field->hasReference()) {
                $fallbackSeed = [Control\Lookup::class, 'model' => $field->getReference()->refModel($this->model)];
            }
        }

        if (isset($field->ui['hint'])) {
            $fallbackSeed['hint'] = $field->ui['hint'];
        }

        if (isset($field->ui['placeholder'])) {
            $fallbackSeed['placeholder'] = $field->ui['placeholder'];
        }

        $controlSeed = Factory::mergeSeeds(
            $controlSeed,
            $field->ui['form'] ?? null,
            $this->typeToControl[$field->type] ?? null,
            $fallbackSeed
        );

        $defaults = [
            'form' => $this,
            'entityField' => new EntityFieldPair($this->model, $field->shortName),
            'shortName' => $field->shortName,
        ];

        return Factory::factory($controlSeed, $defaults);
    }

    /**
     * @var array<string, array>
     */
    protected array $typeToControl = [
        'boolean' => [Control\Checkbox::class],
        'text' => [Control\Textarea::class],
        'datetime' => [Control\Calendar::class, 'type' => 'datetime'],
        'date' => [Control\Calendar::class, 'type' => 'date'],
        'time' => [Control\Calendar::class, 'type' => 'time'],
        'atk4_money' => [Control\Money::class],
    ];

    /**
     * Looks inside the POST of the request and loads it into a current model.
     */
    protected function loadPost(): void
    {
        $postRawData = $this->getApp()->getRequest()->getParsedBody();
        $this->hook(self::HOOK_LOAD_POST, [&$postRawData]);

        $errors = [];
        foreach ($this->controls as $k => $control) {
            // save field value only if field was editable in form at all
            if (!$control->readOnly && !$control->disabled) {
                $postRawValue = $postRawData[$k];
                try {
                    $control->set($this->getApp()->uiPersistence->typecastLoadField($control->entityField->getField(), $postRawValue));
                } catch (\Exception $e) {
                    $messages = [];
                    do {
                        $messages[] = $e->getMessage();
                    } while (($e = $e->getPrevious()) !== null);

                    $errors[$k] = implode(': ', $messages);
                }
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }

    protected function renderView(): void
    {
        $this->setupAjaxSubmit();
        if ($this->controlDisplayRules !== []) {
            $this->js(true, new JsConditionalForm($this, $this->controlDisplayRules, $this->controlDisplaySelector));
        }

        parent::renderView();
    }

    protected function renderTemplateToHtml(): string
    {
        $output = parent::renderTemplateToHtml();

        return $this->fixOwningFormAttrInRenderedHtml($output);
    }

    public function fixOwningFormAttrInRenderedHtml(string $html): string
    {
        return preg_replace_callback('~<(?:button|fieldset|input|output|select|textarea)(?!\w| form=")~i', function ($matches) {
            return $matches[0] . ' form="' . $this->getApp()->encodeHtml($this->formElement->name) . '"';
        }, $html);
    }

    /**
     * Set Fomantic-UI Api settings to use with form. A complete list is here:
     * https://fomantic-ui.com/behaviors/api.html#/settings .
     *
     * @param array $config
     *
     * @return $this
     */
    public function setApiConfig($config)
    {
        $this->apiConfig = array_merge($this->apiConfig, $config);

        return $this;
    }

    /**
     * Set Fomantic-UI Form settings to use with form. A complete list is here:
     * https://fomantic-ui.com/behaviors/form.html#/settings .
     *
     * @param array $config
     *
     * @return $this
     */
    public function setFormConfig($config)
    {
        $this->formConfig = array_merge($this->formConfig, $config);

        return $this;
    }

    public function setupAjaxSubmit(): void
    {
        $this->js(true)->form(array_merge([
            'on' => 'blur',
            'inline' => true,
        ], $this->formConfig));

        $this->formElement->js(true)->api(array_merge([
            'on' => 'submit',
            'url' => $this->cb->getJsUrl(),
            'method' => 'POST',
            'serializeForm' => true,
        ], $this->apiConfig));

        // fix remove prompt for dropdown
        // https://github.com/fomantic/Fomantic-UI/issues/2797
        // [name] in selector is to suppress https://github.com/fomantic/Fomantic-UI/commit/facbca003cf0da465af7d44af41462e736d3eb8b console errors from Multiline/vue fields
        $this->on('change', '.field input[name], .field textarea[name], .field select[name]', $this->js()->form('remove prompt', new JsExpression('$(this).attr(\'name\')')));

        if (!$this->canLeave) {
            $this->js(true, (new JsChain('atk.formService'))->preventFormLeave($this->name));
        }
    }

    // }}}
}
