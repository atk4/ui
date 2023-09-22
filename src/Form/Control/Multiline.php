<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Core\Exception as CoreException;
use Atk4\Data\Field;
use Atk4\Data\Field\CallbackField;
use Atk4\Data\Field\SqlExpressionField;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\ValidationException;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;

/**
 * Creates a Multiline field within a table, which allows adding/editing multiple
 * data rows.
 *
 * Using hasMany reference will required to save reference data using Multiline::saveRows() method.
 *
 * $form = Form::addTo($app);
 * $form->setModel($invoice, []);
 *
 * // add Multiline form control and set model for Invoice items
 * $ml = $form->addControl('ml', [Multiline::class]);
 * $ml->setReferenceModel('Items', null, ['item', 'cat', 'qty', 'price', 'total']);
 *
 * $form->onSubmit(function (Form $form) use ($ml) {
 *     // save Form model and then Multiline model
 *     $form->model->save(); // saving invoice record
 *     $ml->saveRows(); // saving invoice items record related to invoice
 *     return new JsToast('Saved!');
 * });
 *
 * If Multiline's model contains expressions, these will be evaluated on the fly
 * whenever data gets entered.
 *
 * Multiline input also has an onChange callback that will return all data rows
 * in an array. It is also possible to fire onChange handler only for certain
 * fields by passing them as an array to the method.
 *
 * Note that deleting a row will always fire the onChange callback.
 *
 * You can use the returned data to update other related areas of the form.
 * For example, ypdating Grand Total field of all invoice items.
 *
 * $ml->onChange(function (array $rows) use ($form) {
 *     $grandTotal = 0;
 *     foreach ($rows as $row => $cols) {
 *         foreach ($cols as $col) {
 *             $fieldName = array_key_first($col);
 *             if ($fieldName === 'total') {
 *                 $grandTotal += $col[$fieldName];
 *             }
 *         }
 *     }
 *
 *   return $form->js(false, null, 'input[name="grand_total"]')->val($app->uiPersistence->typecastSaveField(new Field(['type' => 'atk4_money']), $grandTotal));
 * }, ['qty', 'price']);
 *
 * Finally, it's also possible to use Multiline for quickly adding records to a
 * model. Be aware that in the example below all User records will be displayed.
 * If your model contains a lot of records, you should handle their limit somehow.
 *
 * $form = Form::addTo($app);
 * $ml = $form->addControl('ml', [Form\Control\Multiline::class]);
 * $ml->setModel($user, ['name', 'is_vip']);
 *
 * $form->onSubmit(function (Form $form) use ($ml) {
 *     $ml->saveRows();
 *     return new JsToast('Saved!');
 * });
 */
class Multiline extends Form\Control
{
    /** @var HtmlTemplate|null The template needed for the multiline view. */
    public $multiLineTemplate;

    /** @var View The multiline View. Assigned in init(). */
    private $multiLine;

    // component names
    public const INPUT = 'SuiInput';
    public const READ_ONLY = 'AtkMultilineReadonly';
    public const TEXT_AREA = 'AtkMultilineTextarea';
    public const SELECT = 'SuiDropdown';
    public const DATE = 'AtkDatePicker';
    public const LOOKUP = 'AtkLookup';

    public const TABLE_CELL = 'SuiTableCell';

    /**
     * Props to be applied globally for each component supported by field type.
     * For example setting 'SuiDropdown' property globally.
     *  $componentProps = [Multiline::SELECT => ['floating' => true]].
     *
     * @var array
     */
    public $componentProps = [];

    /** @var array SuiTable component props */
    public $tableProps = [];

    /** @var array<string, array<string, mixed>> Set Vue component to use per field type. */
    protected $fieldMapToComponent = [
        'default' => [
            'component' => self::INPUT,
            'componentProps' => [__CLASS__, 'getSuiInputProps'],
        ],
        'readonly' => [
            'component' => self::READ_ONLY,
            'componentProps' => [],
        ],
        'textarea' => [
            'component' => self::TEXT_AREA,
            'componentProps' => [],
        ],
        'select' => [
            'component' => self::SELECT,
            'componentProps' => [__CLASS__, 'getDropdownProps'],
        ],
        'date' => [
            'component' => self::DATE,
            'componentProps' => [__CLASS__, 'getDatePickerProps'],
        ],
        'lookup' => [
            'component' => self::LOOKUP,
            'componentProps' => [__CLASS__, 'getLookupProps'],
        ],
    ];

    /** @var bool Add row when tabbing out of last column in last row. */
    public $addOnTab = false;

    /** @var array The definition of each field used in every multiline row. */
    private $fieldDefs;

    /** @var JsCallback */
    private $renderCallback;

    /** @var \Closure(mixed, Form): (JsExpressionable|View|string|void)|null Function to execute when field change or row is delete. */
    protected $onChangeFunction;

    /** @var array Set fields that will trigger onChange function. */
    protected $eventFields;

    /** @var array Collection of field errors. */
    private $rowErrors;

    /** @var array The fields names used in each row. */
    public $rowFields;

    /** @var array The data sent for each row. */
    public $rowData;

    /** @var int The max number of records (rows) that can be added to Multiline. 0 means no limit. */
    public $rowLimit = 0;

    /** @var int The maximum number of items for select type field. */
    public $itemLimit = 25;

    /**
     * Container for component that need Props set based on their field value as Lookup component.
     * Set during fieldDefinition and apply during renderView() after getValue().
     * Must contains callable function and function will receive $model field and value as parameter.
     *
     * @var array<string, \Closure(Field, string): void>
     */
    private $valuePropsBinding = [];

    /**
     * A JsFunction to execute when Multiline add(+) button is clicked.
     * The function is execute after multiline component finish adding a row of fields.
     * The function also receive the row value as an array.
     * ex: $jsAfterAdd = new JsFunction(['value'], [new JsExpression('console.log(value)')]);.
     *
     * @var JsFunction
     */
    public $jsAfterAdd;

    /**
     * A JsFunction to execute when Multiline delete button is clicked.
     * The function is execute after multiline component finish deleting rows.
     * The function also receive the row value as an array.
     * ex: $jsAfterDelete = new JsFunction(['value'], [new JsExpression('console.log(value)')]);.
     *
     * @var JsFunction
     */
    public $jsAfterDelete;

    protected function init(): void
    {
        parent::init();

        if (!$this->multiLineTemplate) {
            $this->multiLineTemplate = new HtmlTemplate('<div {$attributes}><atk-multiline v-bind="initData"></atk-multiline></div>');
        }

        $this->multiLine = View::addTo($this, ['template' => $this->multiLineTemplate]);

        $this->renderCallback = JsCallback::addTo($this);

        // load the data associated with this input and validate it
        $this->form->onHook(Form::HOOK_LOAD_POST, function (Form $form, array &$postRawData) {
            $this->rowData = $this->typeCastLoadValues($this->getApp()->decodeJson($this->getApp()->getRequestPostParam($this->shortName)));
            if ($this->rowData) {
                $this->rowErrors = $this->validate($this->rowData);
                if ($this->rowErrors) {
                    throw new ValidationException([$this->shortName => 'multiline error']);
                }
            }

            // remove __atml ID from array field
            if ($this->form->model->getField($this->shortName)->type === 'json') {
                $rows = [];
                foreach ($this->rowData as $key => $cols) {
                    unset($cols['__atkml']);
                    $rows[] = $cols;
                }
                $postRawData[$this->shortName] = $this->getApp()->encodeJson($rows);
            }
        });

        // change form error handling
        $this->form->onHook(Form::HOOK_DISPLAY_ERROR, function (Form $form, $fieldName, $str) {
            // when errors are coming from this Multiline field, then notify Multiline component about them
            // otherwise use normal field error
            if ($fieldName === $this->shortName) {
                // multiline.js component listen to 'multiline-rows-error' event
                $jsError = $this->jsEmitEvent($this->multiLine->name . '-multiline-rows-error', ['errors' => $this->rowErrors]);
            } else {
                $jsError = $form->js()->form('add prompt', $fieldName, $str);
            }

            return $jsError;
        });
    }

    protected function typeCastLoadValues(array $values): array
    {
        $dataRows = [];
        foreach ($values as $k => $row) {
            foreach ($row as $fieldName => $value) {
                if ($fieldName === '__atkml') {
                    $dataRows[$k][$fieldName] = $value;
                } else {
                    $dataRows[$k][$fieldName] = $this->getApp()->uiPersistence->typecastLoadField($this->model->getField($fieldName), $value);
                }
            }
        }

        return $dataRows;
    }

    /**
     * Add a callback when fields are changed. You must supply array of fields
     * that will trigger the callback when changed.
     *
     * @param \Closure(mixed, Form): (JsExpressionable|View|string|void) $fx
     */
    public function onLineChange(\Closure $fx, array $fields): void
    {
        $this->eventFields = $fields;

        $this->onChangeFunction = $fx;
    }

    /**
     * Get Multiline initial field value. Value is based on model set and will
     * output data rows as JSON string value.
     */
    public function getValue(): string
    {
        if ($this->entityField->getField()->type === 'json') {
            $jsonValues = $this->getApp()->uiPersistence->typecastSaveField($this->entityField->getField(), $this->entityField->get() ?? []);
        } else {
            // set data according to HasMany relation or using model
            $rows = [];
            foreach ($this->model as $row) {
                $cols = [];
                foreach ($this->rowFields as $fieldName) {
                    $field = $this->model->getField($fieldName);
                    $value = $this->getApp()->uiPersistence->typecastSaveField($field, $row->get($field->shortName));
                    $cols[$fieldName] = $value;
                }
                $rows[] = $cols;
            }
            $jsonValues = $this->getApp()->encodeJson($rows);
        }

        return $jsonValues;
    }

    /**
     * Validate each row and return errors if found.
     */
    public function validate(array $rows): array
    {
        $rowErrors = [];
        $entity = $this->model->createEntity();

        foreach ($rows as $cols) {
            $rowId = $this->getMlRowId($cols);
            foreach ($cols as $fieldName => $value) {
                if ($fieldName === '__atkml' || $fieldName === $entity->idField) {
                    continue;
                }

                try {
                    $field = $entity->getField($fieldName);
                    // save field value only if the field was editable
                    if (!$field->readOnly) {
                        $entity->set($fieldName, $value);
                    }
                } catch (CoreException $e) {
                    $rowErrors[$rowId][] = ['name' => $fieldName, 'msg' => $e->getMessage()];
                }
            }
            $rowErrors = $this->addModelValidateErrors($rowErrors, $rowId, $entity);
        }

        return $rowErrors;
    }

    /**
     * @return $this
     */
    public function saveRows(): self
    {
        $model = $this->model;

        // collects existing IDs
        $currentIds = array_column($model->export(), $model->idField);

        foreach ($this->rowData as $row) {
            $entity = $row[$model->idField] !== null ? $model->load($row[$model->idField]) : $model->createEntity();
            foreach ($row as $fieldName => $value) {
                if ($fieldName === '__atkml') {
                    continue;
                }

                if ($model->getField($fieldName)->isEditable()) {
                    $entity->set($fieldName, $value);
                }
            }
            $id = $entity->save()->getId();

            $k = array_search($id, $currentIds, true);
            if ($k !== false) {
                unset($currentIds[$k]);
            }
        }

        // delete removed IDs
        foreach ($currentIds as $id) {
            $model->delete($id);
        }

        return $this;
    }

    /**
     * Check for model validate error.
     */
    protected function addModelValidateErrors(array $errors, string $rowId, Model $entity): array
    {
        $entityErrors = $entity->validate();
        if ($entityErrors) {
            foreach ($entityErrors as $fieldName => $msg) {
                $errors[$rowId][] = ['name' => $fieldName, 'msg' => $msg];
            }
        }

        return $errors;
    }

    /**
     * Finds and returns Multiline row ID.
     */
    private function getMlRowId(array $row): ?string
    {
        $rowId = null;
        foreach ($row as $col => $value) {
            if ($col === '__atkml') {
                $rowId = $value;

                break;
            }
        }

        return $rowId;
    }

    /**
     * @param array<int, string>|null $fieldNames
     */
    public function setModel(Model $model, array $fieldNames = null): void
    {
        parent::setModel($model);

        if ($fieldNames === null) {
            $fieldNames = array_keys($model->getFields('not system'));
        }
        $this->rowFields = array_merge([$model->idField], $fieldNames);

        foreach ($this->rowFields as $fieldName) {
            $this->fieldDefs[] = $this->getFieldDef($model->getField($fieldName));
        }
    }

    /**
     * Set hasMany reference model to use with multiline.
     *
     * Note: When using setReferenceModel you might need to set this corresponding field to neverPersist to true.
     * Otherwise, form will try to save 'multiline' field value as an array when form is save.
     * $multiline = $form->addControl('multiline', [Multiline::class], ['neverPersist' => true])
     */
    public function setReferenceModel(string $refModelName, Model $entity = null, array $fieldNames = []): void
    {
        if ($entity === null) {
            if (!$this->form->model->isEntity()) {
                throw new Exception('Model entity is not set');
            }

            $entity = $this->form->model;
        }

        $this->setModel($entity->ref($refModelName), $fieldNames);
    }

    /**
     * Return field definition in order to properly render them in Multiline.
     *
     * Multiline uses Vue components in order to manage input type based on field type.
     * Component name and props are determine via the getComponentDefinition function.
     */
    public function getFieldDef(Field $field): array
    {
        return [
            'name' => $field->shortName,
            'type' => $field->type,
            'definition' => $this->getComponentDefinition($field),
            'cellProps' => $this->getSuiTableCellProps($field),
            'caption' => $field->getCaption(),
            'default' => $this->getApp()->uiPersistence->typecastSaveField($field, $field->default),
            'isExpr' => @isset($field->expr), // @phpstan-ignore-line
            'isEditable' => $field->isEditable(),
            'isHidden' => $field->isHidden(),
            'isVisible' => $field->isVisible(),
        ];
    }

    /**
     * Each field input, represent by a Vue component, is place within a table cell.
     * Cell properties can be customized via $field->ui['multiline'][Form\Control\Multiline::TABLE_CELL].
     */
    protected function getSuiTableCellProps(Field $field): array
    {
        $props = [];

        if ($field->type === 'integer' || $field->type === 'atk4_money') {
            $props['text-align'] = 'right';
        }

        return array_merge($props, $this->componentProps[self::TABLE_CELL] ?? [], $field->ui['multiline'][self::TABLE_CELL] ?? []);
    }

    /**
     * Return props for input component.
     */
    protected function getSuiInputProps(Field $field): array
    {
        $props = $this->componentProps[self::INPUT] ?? [];

        $props['type'] = ($field->type === 'integer' || $field->type === 'float' || $field->type === 'atk4_money') ? 'number' : 'text';

        return array_merge($props, $field->ui['multiline'][self::INPUT] ?? []);
    }

    /**
     * Return props for AtkDatePicker component.
     */
    protected function getDatePickerProps(Field $field): array
    {
        $props = [];
        $props['config'] = $this->componentProps[self::DATE] ?? [];
        $props['config']['allowInput'] ??= true;

        $calendar = new Calendar();
        $phpFormat = $this->getApp()->uiPersistence->{$field->type . 'Format'};
        $props['config']['dateFormat'] = $calendar->convertPhpDtFormatToFlatpickr($phpFormat, true);
        if ($field->type === 'datetime' || $field->type === 'time') {
            $props['config']['noCalendar'] = $field->type === 'time';
            $props['config']['enableTime'] = true;
            $props['config']['time_24hr'] = $calendar->isDtFormatWith24hrTime($phpFormat);
            $props['config']['enableSeconds'] ??= $calendar->isDtFormatWithSeconds($phpFormat);
            $props['config']['formatSecondsPrecision'] ??= $calendar->isDtFormatWithMicroseconds($phpFormat) ? 6 : -1;
            $props['config']['disableMobile'] = true;
        }

        return $props;
    }

    /**
     * Return props for Dropdown components.
     */
    protected function getDropdownProps(Field $field): array
    {
        $props = array_merge(
            ['floating' => false, 'closeOnBlur' => true, 'selection' => true],
            $this->componentProps[self::SELECT] ?? []
        );

        $items = $this->getFieldItems($field, $this->itemLimit);
        foreach ($items as $value => $text) {
            $props['options'][] = ['key' => $value, 'text' => $text, 'value' => $value];
        }

        return $props;
    }

    /**
     * Set property for AtkLookup component.
     */
    protected function getLookupProps(Field $field): array
    {
        // set any of SuiDropdown props via this property
        // will be applied globally
        $props = [];
        $props['config'] = $this->componentProps[self::LOOKUP] ?? [];
        $items = $this->getFieldItems($field, 10);
        foreach ($items as $value => $text) {
            $props['config']['options'][] = ['key' => $value, 'text' => $text, 'value' => $value];
        }

        if ($field->hasReference()) {
            $props['config']['reference'] = $field->shortName;
            $props['config']['search'] = true;
        }

        $props['config']['placeholder'] ??= 'Select ' . $field->getCaption();

        $this->valuePropsBinding[$field->shortName] = fn ($field, $value) => $this->setLookupOptionValue($field, $value);

        return $props;
    }

    public function setLookupOptionValue(Field $field, string $value): void
    {
        $model = $field->getReference()->refModel($this->model);
        $entity = $model->tryLoadBy($field->getReference()->getTheirFieldName($model), $value);
        if ($entity !== null) {
            $option = ['key' => $value, 'text' => $entity->get($model->titleField), 'value' => $value];
            foreach ($this->fieldDefs as $key => $component) {
                if ($component['name'] === $field->shortName) {
                    $this->fieldDefs[$key]['definition']['componentProps']['optionalValue'] =
                        isset($this->fieldDefs[$key]['definition']['componentProps']['optionalValue'])
                        ? array_merge($this->fieldDefs[$key]['definition']['componentProps']['optionalValue'], [$option])
                        : [$option];
                }
            }
        }
    }

    /**
     * Component definition require at least a name and a props array.
     */
    protected function getComponentDefinition(Field $field): array
    {
        $name = $field->ui['multiline']['component'] ?? null;
        if ($name) {
            $component = $this->fieldMapToComponent[$name];
        } elseif (!$field->isEditable()) {
            $component = $this->fieldMapToComponent['readonly'];
        } elseif ($field->enum || $field->values) {
            $component = $this->fieldMapToComponent['select'];
        } elseif ($field->type === 'date' || $field->type === 'time' || $field->type === 'datetime') {
            $component = $this->fieldMapToComponent['date'];
        } elseif ($field->type === 'text') {
            $component = $this->fieldMapToComponent['textarea'];
        } elseif ($field->hasReference()) {
            $component = $this->fieldMapToComponent['lookup'];
        } else {
            $component = $this->fieldMapToComponent['default'];
        }

        // map all callables defaults
        foreach ($component as $k => $v) {
            if (is_array($v) && is_callable($v)) {
                $component[$k] = call_user_func($v, $field);
            }
        }

        return $component;
    }

    protected function getFieldItems(Field $field, ?int $limit = 10): array
    {
        $items = [];
        if ($field->enum !== null) {
            $items = array_slice($field->enum, 0, $limit);
            $items = array_combine($items, $items);
        }
        if ($field->values !== null) {
            $items = array_slice($field->values, 0, $limit, true);
        } elseif ($field->hasReference()) {
            $model = $field->getReference()->refModel($this->model);
            $model->setLimit($limit);

            foreach ($model as $item) {
                $items[$item->get($field->getReference()->getTheirFieldName($model))] = $item->get($model->titleField);
            }
        }

        return $items;
    }

    /**
     * Apply Props to component that require props based on field value.
     */
    protected function valuePropsBinding(string $values): void
    {
        $fieldValues = $this->getApp()->decodeJson($values);

        foreach ($fieldValues as $rows) {
            foreach ($rows as $fieldName => $value) {
                if (isset($this->valuePropsBinding[$fieldName])) {
                    ($this->valuePropsBinding[$fieldName])($this->model->getField($fieldName), $value);
                }
            }
        }
    }

    protected function renderView(): void
    {
        $this->model->assertIsModel();

        $this->renderCallback->set(function () {
            $this->outputJson();
        });

        parent::renderView();

        $inputValue = $this->getValue();
        $this->valuePropsBinding($inputValue);

        $this->multiLine->vue('atk-multiline', [
            'data' => [
                'formName' => $this->form->formElement->name,
                'inputValue' => $inputValue,
                'inputName' => $this->shortName,
                'fields' => $this->fieldDefs,
                'url' => $this->renderCallback->getJsUrl(),
                'eventFields' => $this->eventFields,
                'hasChangeCb' => $this->onChangeFunction !== null,
                'tableProps' => $this->tableProps,
                'rowLimit' => $this->rowLimit,
                'caption' => $this->caption,
                'afterAdd' => $this->jsAfterAdd,
                'afterDelete' => $this->jsAfterDelete,
                'addOnTab' => $this->addOnTab,
            ],
        ]);
    }

    /**
     * Render callback according to multi line action.
     * 'update-row' need special formatting.
     */
    private function outputJson(): void
    {
        switch ($this->getApp()->getRequestPostParam('__atkml_action')) {
            case 'update-row':
                $entity = $this->createDummyEntityFromPost($this->model);
                $expressionValues = array_merge($this->getExpressionValues($entity), $this->getCallbackValues($entity));
                $this->getApp()->terminateJson(['success' => true, 'expressions' => $expressionValues]);
                // no break - expression above always terminate
            case 'on-change':
                $rowsRaw = $this->getApp()->decodeJson($this->getApp()->getRequestPostParam('rows'));
                $response = ($this->onChangeFunction)($this->typeCastLoadValues($rowsRaw), $this->form);
                $this->renderCallback->terminateAjax($this->renderCallback->getAjaxec($response));
                // TODO JsCallback::terminateAjax() should return never
        }
    }

    /**
     * Return values associated with callback field.
     */
    private function getCallbackValues(Model $entity): array
    {
        $values = [];
        foreach ($this->fieldDefs as $def) {
            $fieldName = $def['name'];
            if ($fieldName === $entity->idField) {
                continue;
            }
            $field = $entity->getField($fieldName);
            if ($field instanceof CallbackField) {
                $value = ($field->expr)($entity);
                $values[$fieldName] = $this->getApp()->uiPersistence->typecastSaveField($field, $value);
            }
        }

        return $values;
    }

    /**
     * Looks inside the POST of the request and loads data into model.
     * Allow to Run expression base on post row value.
     */
    private function createDummyEntityFromPost(Model $model): Model
    {
        $entity = (clone $model)->createEntity(); // clone for clearing "required"

        foreach ($this->fieldDefs as $def) {
            $fieldName = $def['name'];
            if ($fieldName === $entity->idField) {
                continue;
            }

            $field = $entity->getField($fieldName);

            $value = $this->getApp()->uiPersistence->typecastLoadField($field, $this->getApp()->getRequestPostParam($fieldName));
            if ($field->isEditable()) {
                try {
                    $field->required = false;
                    $entity->set($fieldName, $value);
                } catch (ValidationException $e) {
                    // bypass validation at this point
                }
            }
        }

        return $entity;
    }

    /**
     * Get all field expression in model, but only evaluate expression used in rowFields.
     *
     * @return array<string, SqlExpressionField>
     */
    private function getExpressionFields(Model $model): array
    {
        $fields = [];
        foreach ($model->getFields() as $field) {
            if (!in_array($field->shortName, $this->rowFields, true) || !$field instanceof SqlExpressionField) {
                continue;
            }

            $fields[$field->shortName] = $field;
        }

        return $fields;
    }

    /**
     * Return values associated to field expression.
     */
    private function getExpressionValues(Model $entity): array
    {
        $dummyFields = $this->getExpressionFields($entity);
        foreach ($dummyFields as $k => $field) {
            $dummyFields[$k] = clone $field;
            $dummyFields[$k]->expr = $this->getDummyExpression($field, $entity);
        }

        if ($dummyFields === []) {
            return [];
        }

        $dummyModel = new Model($entity->getModel()->getPersistence(), ['table' => $entity->table]);
        $dummyModel->removeField('id');
        $dummyModel->idField = $entity->idField;

        $createExprFromValueFx = static function ($v) use ($dummyModel): Persistence\Sql\Expression {
            if (is_int($v)) {
                // TODO hack for multiline.php test for PostgreSQL
                // related with https://github.com/atk4/data/pull/989
                return $dummyModel->expr((string) $v);
            }

            return $dummyModel->expr('[]', [$v]);
        };

        foreach ($entity->getFields() as $field) {
            $dummyModel->addExpression($field->shortName, [
                'expr' => isset($dummyFields[$field->shortName])
                    ? $dummyFields[$field->shortName]->expr
                    : ($field->shortName === $dummyModel->idField
                        ? '-1'
                        : $createExprFromValueFx($entity->getModel()->getPersistence()->typecastSaveField($field, $field->get($entity)))),
                'type' => $field->type,
                'actual' => $field->actual,
            ]);
        }
        $dummyModel->setLimit(1); // TODO must work with empty table, no table should be used
        $values = $dummyModel->loadOne()->get();
        unset($values[$entity->idField]);

        $formatValues = [];
        foreach ($values as $f => $value) {
            if (isset($dummyFields[$f])) {
                $field = $entity->getField($f);
                $formatValues[$f] = $this->getApp()->uiPersistence->typecastSaveField($field, $value);
            }
        }

        return $formatValues;
    }

    /**
     * Return expression where fields are replace with their current or default value.
     * Ex: total field expression = [qty] * [price] will return 4 * 100
     * where qty and price current value are 4 and 100 respectively.
     *
     * @return string
     */
    private function getDummyExpression(SqlExpressionField $exprField, Model $entity)
    {
        $expr = $exprField->expr;
        if ($expr instanceof \Closure) {
            $expr = $exprField->getDsqlExpression($entity->getModel()->expr(''));
        }
        if ($expr instanceof Persistence\Sql\Expression) {
            $expr = \Closure::bind(static fn () => $expr->template, null, Persistence\Sql\Expression::class)();
        }

        $matches = [];
        preg_match_all('~\[[a-z0-9_]*\]|{[a-z0-9_]*}~i', $expr, $matches);

        foreach ($matches[0] as $match) {
            $fieldName = substr($match, 1, -1);
            $field = $entity->getField($fieldName);
            if ($field instanceof SqlExpressionField) {
                $expr = str_replace($match, $this->getDummyExpression($field, $entity), $expr);
            } else {
                $expr = str_replace($match, $this->getValueForExpression($exprField, $fieldName, $entity), $expr);
            }
        }

        return $expr;
    }

    /**
     * Return a value according to field used in expression and the expression type.
     * If field used in expression is null, the default value is returned.
     *
     * @return string
     */
    private function getValueForExpression(Field $exprField, string $fieldName, Model $entity)
    {
        switch ($exprField->type) {
            case 'integer':
            case 'float':
            case 'atk4_money':
                $value = (string) ($entity->get($fieldName) ?? 0);

                break;
            default:
                $value = '"' . $entity->get($fieldName) . '"';
        }

        return $value;
    }
}
