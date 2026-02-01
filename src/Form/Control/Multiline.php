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
use Atk4\Ui\Form;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;
use Atk4\Ui\View\ModelTrait;

/**
 * Creates a Multiline field within a table, which allows adding/editing multiple
 * data rows.
 *
 * Using hasMany reference will required to save reference data using Multiline::saveRows() method.
 *
 * $form = Form::addTo($app);
 * $form->setEntity($invoice, []);
 *
 * // add Multiline form control and set model for Invoice items
 * $ml = $form->addControl('ml', [Multiline::class]);
 * $ml->setReferenceModel('Items', null, ['item', 'cat', 'qty', 'price', 'total']);
 *
 * $form->onSubmit(function (Form $form) use ($ml) {
 *     // save Form model and then Multiline model
 *     $form->entity->save(); // saving invoice record
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
 * For example, updating Grand Total field of all invoice items.
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
    use ModelTrait {
        setModel as private _setModel;
    }

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
     * Props to be applied for each component supported by field type.
     * For example setting 'SuiDropdown' property.
     *  $componentProps = [Multiline::SELECT => ['floating' => true]].
     *
     * @var array<string, array<string, mixed>>
     */
    public $componentProps = [];

    /** @var array<string, mixed> SuiTable component props */
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

    /** @var list<array<string, mixed>> The definition of each field used in every multiline row. */
    private $fieldDefs;

    /** @var JsCallback */
    private $renderCallback;

    /** @var \Closure(list<array<string, mixed>>, list<string>, Form): (JsExpressionable|View|string|void)|null Function to execute when field change or row is delete. */
    protected $onChangeFunction;

    /** @var list<string> Set fields that will trigger onChange function. */
    protected $eventFields;

    /** @var array<string, list<array{name: string, msg: string}>> Collection of field errors. */
    private $rowErrors;

    /** @var list<string> The fields names used in each row. */
    public $rowFields;

    /** The changes set by self::setInputValue(). */
    public TheirChanges $changes;

    /** The max number of records (rows) that can be added to Multiline. 0 means no limit. */
    public int $rowLimit = 0;

    /** The maximum number of items for select type field. */
    public ?int $itemLimit = 25;

    /**
     * Container for component that need Props set based on their field value as Lookup component.
     * Set during fieldDefinition and apply during renderView() after getInputValue().
     * Must contains callable function and function will receive $model field and value as parameter.
     *
     * @var array<string, \Closure<T of Field>(T, string): void>
     */
    private array $valuePropsBinding = [];

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

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if (!$this->multiLineTemplate) {
            $this->multiLineTemplate = new HtmlTemplate('<div {$attributes}><atk-multiline v-bind="initData"></atk-multiline></div>');
        }

        $this->multiLine = View::addTo($this, ['template' => $this->multiLineTemplate]);

        $this->renderCallback = JsCallback::addTo($this);

        // change form error handling
        $this->form->onHook(Form::HOOK_DISPLAY_ERROR, function (Form $form, $fieldName, $str) {
            // when errors are coming from this Multiline field, then notify Multiline component about them
            // otherwise use normal field error
            if ($fieldName === $this->shortName && $this->rowErrors) {
                // multiline js component listen to 'multiline-rows-error' event
                $jsError = $this->jsEmitEvent($this->multiLine->name . '-multiline-rows-error', ['errors' => $this->rowErrors]);
            } else {
                $jsError = $form->js()->form('add prompt', $fieldName, $str);
            }

            return $jsError;
        });

        if ($this->isOneToOne()) {
            $this->rowLimit = 1;
        }
    }

    /**
     * @param array<mixed, array<string, mixed>> $values
     *
     * @return array<mixed, array<string, string|null>>
     */
    private function typecastUiSaveValues(array $values): array
    {
        $res = [];
        foreach ($values as $k => $row) {
            foreach ($row as $fieldName => $value) {
                $res[$k][$fieldName] = $fieldName === $this->model->idField
                    ? $this->getApp()->uiPersistence->typecastAttributeSaveField($this->model->getField($fieldName), $value)
                    : $this->getApp()->uiPersistence->typecastSaveField($this->model->getField($fieldName), $value);
            }
        }

        return $res;
    }

    /**
     * @param array<string, string|null> $row
     *
     * @return array<string, mixed>
     */
    private function typecastUiLoadRow(array $row): array
    {
        $res = [];
        foreach ($row as $fieldName => $value) {
            $res[$fieldName] = $fieldName === $this->model->idField
                ? $this->getApp()->uiPersistence->typecastAttributeLoadField($this->model->getField($fieldName), $value)
                : $this->getApp()->uiPersistence->typecastLoadField($this->model->getField($fieldName), $value);
        }

        return $res;
    }

    private function isOneToOne(): bool
    {
        return $this->entityField->getField()->hasReference()
            && $this->entityField->getField()->getReference()->isOneToOne();
    }

    #[\Override]
    public function getInputValue(): string
    {
        $theirModelOrEntity = $this->entityField->getField()->hasReference()
            ? $this->entityField->getField()->getReference()->ref($this->entityField->getEntity())
            : $this->model;
        if ($theirModelOrEntity->isEntity()) {
            $theirModelOrEntity = $theirModelOrEntity->isLoaded()
                ? [$theirModelOrEntity]
                : [];
        }

        $rows = [];
        foreach ($theirModelOrEntity as $row) {
            $rows[] = $row->get();
        }

        $rowsUi = $this->typecastUiSaveValues($rows);

        return $this->getApp()->encodeJson($rowsUi);
    }

    /**
     * @return array{list<array<string, mixed>>, list<string>}
     */
    private function decodeInput(string $json): array
    {
        $rowDataWithMlid = $this->getApp()->decodeJson($json);
        $rowData = [];
        $mlids = [];
        foreach ($rowDataWithMlid as $row) {
            $mlids[] = $row['__atkml'];
            unset($row['__atkml']);

            foreach ($row as $k => $v) {
                if ($this->model->getField($k)->readOnly) {
                    unset($row[$k]);
                }
            }

            $rowData[] = $this->typecastUiLoadRow($row);
        }

        return [$rowData, $mlids];
    }

    #[\Override]
    public function setInputValue(string $value): void
    {
        [$rowData, $mlids] = $this->decodeInput($value);

        if ($rowData !== []) {
            $this->rowErrors = $this->validate($rowData, $mlids);
            if ($this->rowErrors !== []) {
                throw new ValidationException([$this->shortName => 'Multiline error']);
            }
        }

        $theirModelOrEntity = $this->entityField->getField()->hasReference()
            ? $this->entityField->getField()->getReference()->ref($this->entityField->getEntity())
            : $this->model;

        $changes = new TheirChanges();

        // TODO this is dangerous, deleted row IDs should be passed from UI
        $idsToDelete = array_filter(array_column($rowData, $theirModelOrEntity->idField), static fn ($v) => $v !== null);
        foreach ($theirModelOrEntity->getModel(true)->createIteratorBy($theirModelOrEntity->idField, 'not in', $idsToDelete) as $entity) {
            $changes->deletes[] = [$theirModelOrEntity->idField => $entity->getId()];
        }

        foreach ($rowData as $row) {
            if ($row[$theirModelOrEntity->idField] === null) {
                $changes->inserts[] = $row;
            } else {
                $changes->updates[] = [[$theirModelOrEntity->idField => $row[$theirModelOrEntity->idField]], $row];
            }
        }

        $this->changes = $changes;

        if ($this->entityField->getField()->hasReference()) {
            $changes->saveOnSave(
                $this->entityField->getEntity(),
                $this->entityField->getField()->getReference()
            );
        }
    }

    /**
     * Add a callback when fields are changed. You must supply array of fields
     * that will trigger the callback when changed.
     *
     * @param \Closure(list<array<string, mixed>>, list<string>, Form): (JsExpressionable|View|string|void) $fx
     * @param list<string>                                                                                  $fields
     */
    public function onLineChange(\Closure $fx, array $fields): void
    {
        $this->eventFields = $fields;

        $this->onChangeFunction = $fx;
    }

    /**
     * Validate each row and return errors if found.
     *
     * @param list<array<string, mixed>> $rows
     * @param list<string>               $mlids
     *
     * @return array<string, list<array{name: string, msg: string}>>
     */
    public function validate(array $rows, array $mlids): array
    {
        $rowErrors = [];
        $entity = $this->model->createEntity();

        foreach ($rows as $i => $cols) {
            $rowId = $mlids[$i];
            foreach ($cols as $fieldName => $value) {
                if ($fieldName === $entity->idField) {
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

    public function saveRows(): void
    {
        assert(!$this->entityField->getField()->hasReference());

        $this->changes->saveTo($this->model);
    }

    /**
     * Check for model validate error.
     *
     * @param array<string, list<array{name: string, msg: string}>> $errors
     *
     * @return array<string, list<array{name: string, msg: string}>>
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
     * @param list<string>|null $fields
     */
    public function setModel(Model $model, ?array $fields = null): void
    {
        $this->_setModel($model);

        if ($fields === null) {
            $fields = array_keys($model->getFields('not system'));
        }
        $this->rowFields = array_merge([$model->idField], $fields);

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
     *
     * @param list<string> $fieldNames
     */
    public function setReferenceModel(string $refModelName, ?Model $entity = null, array $fieldNames = []): void
    {
        if ($entity === null) {
            $entity = $this->form->entity;
        }

        $this->setModel($entity->ref($refModelName), $fieldNames);
    }

    /**
     * Return field definition in order to properly render them in Multiline.
     *
     * Multiline uses Vue components in order to manage input type based on field type.
     * Component name and props are determine via the getComponentDefinition function.
     *
     * @return array<string, mixed>
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
            'isExpr' => @isset($field->expr), // @phpstan-ignore property.notFound
            'isEditable' => $field->isEditable(),
            'isHidden' => $field->isHidden(),
            'isVisible' => $field->isVisible(),
        ];
    }

    /**
     * Each field input, represent by a Vue component, is place within a table cell.
     * Cell properties can be customized via $field->ui['multiline'][Form\Control\Multiline::TABLE_CELL].
     *
     * @return array<string, mixed>
     */
    protected function getSuiTableCellProps(Field $field): array
    {
        $props = [];

        if (in_array($field->type, ['smallint', 'integer', 'bigint', 'float', 'atk4_money'], true)) {
            $props['text-align'] = 'right';
        }

        return array_merge($props, $this->componentProps[self::TABLE_CELL] ?? [], $field->ui['multiline'][self::TABLE_CELL] ?? []);
    }

    /**
     * Return props for input component.
     *
     * @return array<string, mixed>
     */
    protected function getSuiInputProps(Field $field): array
    {
        $props = $this->componentProps[self::INPUT] ?? [];

        return array_merge($props, $field->ui['multiline'][self::INPUT] ?? []);
    }

    /**
     * Return props for AtkDatePicker component.
     *
     * @return array<string, mixed>
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
     *
     * @return array<string, mixed>
     */
    protected function getDropdownProps(Field $field): array
    {
        $props = array_merge(
            ['floating' => false, 'closeOnBlur' => true, 'selection' => true, 'clearable' => true],
            $this->componentProps[self::SELECT] ?? []
        );

        $props['options'] = [];
        $items = $this->getFieldItems($field, $this->itemLimit);
        foreach ($items as $value => $text) {
            if (is_int($value)) {
                $value = (string) $value;
            }

            $props['options'][] = ['key' => $value, 'text' => $text, 'value' => $value];
        }

        return $props;
    }

    /**
     * Set property for AtkLookup component.
     *
     * @return array<string, mixed>
     */
    protected function getLookupProps(Field $field): array
    {
        $props = [];
        $props['config'] = array_merge(
            ['clearable' => true],
            $this->componentProps[self::LOOKUP] ?? []
        );

        $props['config']['options'] = [];
        $items = $this->getFieldItems($field, $this->itemLimit);
        foreach ($items as $value => $text) {
            if (is_int($value)) {
                $value = (string) $value;
            }

            $props['config']['options'][] = ['key' => $value, 'text' => $text, 'value' => $value];
        }

        if ($field->hasReference()) {
            $props['config']['reference'] = $field->shortName;
            // $props['config']['search'] = true; // breaks "clearable" config
        }

        $props['config']['placeholder'] ??= 'Select ' . $field->getCaption();

        $this->valuePropsBinding[$field->shortName] = fn ($field, $value) => $this->setLookupOptionValue($field, $value);

        return $props;
    }

    public function setLookupOptionValue(Field $field, string $value): void
    {
        $model = $field->getReference()->createTheirModel();
        $entity = $model->tryLoadBy($field->getReference()->getTheirFieldName($model), $this->getApp()->uiPersistence->typecastLoadField($field, $value));
        if ($entity !== null) {
            $option = ['key' => $value, 'text' => $entity->get($model->titleField), 'value' => $value];
            foreach ($this->fieldDefs as $key => $component) {
                if ($component['name'] === $field->shortName) {
                    $this->fieldDefs[$key]['definition']['componentProps']['optionalValues'] = array_merge($this->fieldDefs[$key]['definition']['componentProps']['optionalValues'] ?? [], [$option]);
                }
            }
        }
    }

    /**
     * Component definition require at least a name and a props array.
     *
     * @return array<string, mixed>
     */
    protected function getComponentDefinition(Field $field): array
    {
        $name = $field->ui['multiline']['component'] ?? null;
        if ($name) {
            $component = $this->fieldMapToComponent[$name];
        } elseif (!$field->isEditable()) {
            $component = $this->fieldMapToComponent['readonly'];
        } elseif ($field->enum !== null || $field->values !== null) {
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

    /**
     * @return array<string|int, string>
     */
    protected function getFieldItems(Field $field, ?int $limit): array
    {
        $items = [];
        if ($field->enum !== null) {
            $items = array_slice($field->enum, 0, $limit);
            $items = array_map(fn ($v) => $this->getApp()->uiPersistence->typecastSaveField($field, $v), $items);
            $items = array_combine($items, $items);
        }
        if ($field->values !== null) {
            $items = array_slice($field->values, 0, $limit, true);
            $items = array_combine(
                array_map(fn ($v) => $this->getApp()->uiPersistence->typecastSaveField($field, $v), array_keys($items)),
                $items
            );
        } elseif ($field->hasReference()) {
            $model = $field->getReference()->createTheirModel();
            $model->setLimit($limit);

            $theirFieldName = $field->getReference()->getTheirFieldName($model);
            foreach ($model as $item) {
                $theirValue = $this->getApp()->uiPersistence->typecastSaveField($model->getField($theirFieldName), $item->get($theirFieldName));
                $items[$theirValue] = $item->get($model->titleField);
            }
        }

        return $items;
    }

    /**
     * Apply Props to component that require props based on field value.
     */
    protected function valuePropsBinding(string $valueJson): void
    {
        $fieldValues = $this->getApp()->decodeJson($valueJson);

        foreach ($fieldValues as $rows) {
            foreach ($rows as $fieldName => $value) {
                if (isset($this->valuePropsBinding[$fieldName])) {
                    if ($value !== null) {
                        ($this->valuePropsBinding[$fieldName])($this->model->getField($fieldName), $value);
                    }
                }
            }
        }
    }

    #[\Override]
    protected function renderView(): void
    {
        $this->renderCallback->set(function () {
            $this->outputJson();
        });

        parent::renderView();

        $inputValueJson = $this->getInputValue();
        $this->valuePropsBinding($inputValueJson);

        $this->multiLine->vue('atk-multiline', [
            'data' => [
                'formName' => $this->form->formElement->name,
                'inputValue' => $inputValueJson,
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
                [$rows, $mlids] = $this->decodeInput($this->getApp()->getRequestPostParam('rows'));
                $this->renderCallback->set(fn () => ($this->onChangeFunction)($rows, $mlids, $this->form));
        }
    }

    /**
     * Return values associated with callback field.
     *
     * @return array<string, string|null>
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
     *
     * @return array<string, string|null>
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

        $dummyModel = new Model($entity->getModel()->getPersistence(), [
            'table' => new class($entity->getModel()->getPersistence()) extends Model {
                public $table = '';

                #[\Override]
                public function action(string $mode, array $args = [])
                {
                    assert($mode === 'select');
                    assert($args === []);

                    $query = Persistence\Sql::assertInstanceOf($this->getPersistence())->dsql();
                    $query->field($query->expr('[]', [1]), '_c');

                    return $query;
                }
            },
            'idField' => false,
        ]);

        $createExprFromValueFx = static function ($v) use ($dummyModel): Persistence\Sql\Expression {
            if (is_int($v)) {
                // TODO hack for multiline.php test for PostgreSQL
                // related with https://github.com/atk4/data/pull/989
                return $dummyModel->expr((string) $v);
            }

            return $dummyModel->expr('[]', [$v]);
        };

        foreach ($entity->getFields() as $field) { // @phpstan-ignore foreach.valueOverwrite (https://github.com/phpstan/phpstan/issues/11012)
            $dummyModel->addExpression($field->shortName, [
                'expr' => isset($dummyFields[$field->shortName])
                    ? $dummyFields[$field->shortName]->expr
                    : ($field->shortName === $entity->idField
                        ? '99000'
                        : $createExprFromValueFx($entity->getModel()->getPersistence()->typecastSaveField($field, $field->get($entity)))),
                'type' => $field->type,
                'actual' => $field->actual,
            ]);
        }
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
     */
    private function getValueForExpression(Field $exprField, string $fieldName, Model $entity): string
    {
        switch ($exprField->type) {
            case 'smallint':
            case 'integer':
            case 'bigint':
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
