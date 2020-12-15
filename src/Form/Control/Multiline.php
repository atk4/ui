<?php

declare(strict_types=1);
/**
 * Creates a Multiline field within a table, which allows adding/editing multiple
 * data rows.
 *
 * To save the data, use the Multiline::saveRows() method. If the Multiline's
 * model is a reference to your form's model, the form model should be saved prior
 * to calling saveRows().
 *
 * $form = Form::addTo($app);
 * $form->setModel($invoice, false);
 * // Add form controls
 *
 * // Add Multiline form control and set model for it.
 * $ml = $form->addControl('ml', ['Multiline']);
 *
 * // Set model using hasMany reference of Invoice.
 * $ml->setModel($invoice, ['item','cat','qty','price', 'total'], 'Items', 'invoice_id');
 *
 * $form->onSubmit(function($form) use ($ml) {
 *     // Save Form model and then Multiline model
 *     $form->model->save();
 *     $ml->saveRows();
 *     return new \Atk4\Ui\JsToast('Saved!');
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
 * $ml->onChange(function($rows) use ($form) {
 *     $grand_total = 0;
 *     foreach ($rows as $row => $cols) {
 *         foreach ($cols as $col) {
 *             $fieldName = key($col);
 *                 if ($fieldName === 'total') {
 *                     $grand_total = $grand_total + $col[$fieldName];
 *                 }
 *          }
 *     }
 *
 *   return $form->js(true, null, 'input[name="grand_total"]')->val(number_format($grand_total, 2));
 * }, ['qty', 'price']);
 *
 * Finally, it's also possible to use Multiline for quickly adding records to a
 * model. Be aware that in the example below all User records will be displayed.
 * If your model contains a lot of records, you should handle their limit somehow.
 *
 * $form = Form::addTo($app);
 * $ml = $form->addControl('ml', [Form\Control\Multiline::class]);
 * $ml->setModel($user, ['name','is_vip']);
 *
 * $form->onSubmit(function($form) use ($ml) {
 *     $ml->saveRows();
 *     return new JsToast('Saved!');
 * });
 */

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Field;
use Atk4\Data\Field\Callback;
use Atk4\Data\FieldSqlExpression;
use Atk4\Data\Model;
use Atk4\Data\Reference\HasOne;
use Atk4\Data\ValidationException;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;

class Multiline extends Form\Control
{
    /** @var HtmlTemplate The template needed for the multiline view.*/
    public $multiLineTemplate;

    /** @var View The multiline View. Assigned in init().*/
    private $multiLine;

    /* Components name */
    public const INPUT = 'sui-input';
    public const READ_ONLY = 'atk-multiline-readonly';
    public const TEXT_AREA = 'atk-multiline-textarea';
    public const SELECT = 'sui-dropdown';
    public const DATE = 'atk-date-picker';
    public const LOOKUP = 'atk-lookup';
    public const TABLE_CELL = 'sui-table-cell';

    /**
     * Props to be apply globally for each component supported by field type.
     * For example setting 'sui-dropdown' property globally.
     *  $componentProps = [Multiline::SELECT => ['floating' => true]]
     *
     * @var array
     */
    public $componentProps = [];

    /** @var array sui-table component props */
    public $tableProps = [];

    /** @var array[]  Set Vue component to use per field type. */
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
        ]
    ];

    /** @var bool Add row when tabbing out of last column in last row. */
    public $addOnTab = false;

    /** @var array The definition of each field used in every multiline row. */
    private $fieldDefs;

    /** @var JsCallback */
    private $renderCallback;

    /** @var \Closure Function to execute when field change or row is delete. */
    protected $onChangeFunction;

    /** @var array Set fields that will trigger onChange function. */
    protected $eventFields;

    /** @var array Collection of field errors. */
    private $rowErrors;

    /** @var string The model reference name used for Multiline input. */
    public $modelRef;

    /** @var string The link field used for reference.*/
    public $linkField;

    /** @var array The fields names used in each row. */
    public $rowFields;

    /** @var array The data sent for each row. */
    public $rowData;

    /** @var int The max number of records (rows) that can be added to Multiline. 0 means no limit. */
    public $rowLimit = 0;

    /** @var int The maximum number of items for select type field. */
    public $itemLimit = 25;

    /** @var string Multiline's caption. */
    public $caption;

    /**
     * A JsFunction to execute when Multiline add(+) button is clicked.
     * The function is execute after multiline component finish adding a row of fields.
     * The function also receive the row value as an array.
     * ex: $jsAfterAdd = new JsFunction(['value'],[new JsExpression('console.log(value)')]);.
     *
     * @var JsFunction|null
     */
    public $jsAfterAdd;

    /**
     * A JsFunction to execute when Multiline delete button is clicked.
     * The function is execute after multiline component finish deleting rows.
     * The function also receive the row value as an array.
     * ex: $jsAfterDelete = new JsFunction(['value'],[new JsExpression('console.log(value)')]);.
     *
     * @var JsFunction|null
     */
    public $jsAfterDelete;

    protected function init(): void
    {
        parent::init();

        if (!$this->multiLineTemplate) {
            $this->multiLineTemplate = new HtmlTemplate('<div id="{$_id}" class="ui"><atk-multiline v-bind="initData"></atk-multiline><div class="ui hidden divider"></div>{$Input}</div>');
        }

        $this->multiLine = View::addTo($this, ['template' => $this->multiLineTemplate]);

        $this->renderCallback = JsCallback::addTo($this);

        // load the data associated with this input and validate it.
        $this->form->onHook(Form::HOOK_LOAD_POST, function ($form) {
            $this->rowData = $this->getApp()->decodeJson($_POST[$this->short_name]);
            if ($this->rowData) {
                $this->rowErrors = $this->validate($this->rowData);
                if ($this->rowErrors) {
                    throw new ValidationException([$this->short_name => 'multiline error']);
                }
            }
        });

        // Change form error handling.
        $this->form->onHook(Form::HOOK_DISPLAY_ERROR, function ($form, $fieldName, $str) {
            // When errors are coming from this Multiline field, then notify Multiline component about them.
            // Otherwise use normal field error.
            if ($fieldName === $this->short_name) {
                // multiline.js component listen to 'multiline-rows-error' event.
                $jsError = [$this->jsEmitEvent($this->multiLine->name . '-multiline-rows-error', ['errors' => $this->rowErrors])];
            } else {
                $jsError = [$form->js()->form('add prompt', $fieldName, $str)];
            }

            return $jsError;
        });
    }

    /**
     * Add a callback when fields are changed. You must supply array of fields
     * that will trigger the callback when changed.
     *
     * @param array $fields
     */
    public function onLineChange(\Closure $fx, $fields)
    {
        $this->eventFields = $fields;

        $this->onChangeFunction = $fx;
    }

    /**
     * Input field collecting multiple rows of data.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->getApp()->getTag('input', [
            'name' => $this->short_name,
            'type' => 'hidden',
            'value' => $this->getValue(),
            'readonly' => true,
        ]);
    }

    /**
     * Get Multiline initial field value. Value is based on model set and will
     * output data rows as json string value.
     *
     * @return false|string
     */
    public function getValue()
    {
        $model = null;

        // Will load data when using containsMany.
        $data = $this->getApp()->ui_persistence->_typecastSaveField($this->field, $this->field->get());

        // If data is empty try to load model data directly. - For hasMany model
        // or array model already populated with data.
        if (empty($data)) {
            $rows = [];
            // Set model according to model reference if set, or simply the model passed to it.
            if ($this->model->loaded() && $this->modelRef) {
                $model = $this->model->ref($this->modelRef);
            } elseif (!$this->modelRef) {
                $model = $this->model;
            }
            if ($model) {
                foreach ($model as $row) {
                    $cols = [];
                    foreach ($this->rowFields as $fieldName) {
                        $field = $model->getField($fieldName);
                        $value = $this->getApp()->ui_persistence->_typecastSaveField($field, $row->get($field->short_name));
                        $cols[$fieldName] = $value;
                    }
                    $rows[] = $cols;
                }
            }
            $data = $this->getApp()->encodeJson($rows);
        }

        return $data;
    }

    /**
     * Validate each row and return errors if found.
     */
    public function validate(array $rows): array
    {
        $rowErrors = [];
        $model = $this->getModel();

        foreach ($rows as $cols) {
            $rowId = $this->getMlRowId($cols);
            foreach ($cols as $fieldName => $value) {
                if ($fieldName === '__atkml' || $fieldName === $model->id_field) {
                    continue;
                }

                try {
                    $field = $model->getField($fieldName);
                    // Save field value only if the field was editable
                    if (!$field->read_only) {
                        $model->set($fieldName, $this->getApp()->ui_persistence->typecastLoadField($field, $value));
                    }
                } catch (\Atk4\Core\Exception $e) {
                    $rowErrors[$rowId][] = ['field' => $fieldName, 'msg' => $e->getMessage()];
                }
            }
            $rowErrors = $this->addModelValidateErrors($rowErrors, $rowId, $model);
        }

        return $rowErrors;
    }

    /**
     * Save rows.
     */
    public function saveRows()
    {
        // If we are using a reference, make sure main model is loaded.
        if ($this->modelRef && !$this->model->loaded()) {
            throw new Exception('Parent model need to be loaded');
        }

        $model = $this->getModel();

        // collects existing ids.
        $currentIds = array_column($model->export(), $model->id_field);

        foreach ($this->rowData as $row) {
            // should clone model to be able to save it multiple times
            $row_model = clone $model;

            if ($this->modelRef && $this->linkField) {
                $row_model->set($this->linkField, $this->model->getId());
            }

            foreach ($row as $fieldName => $value) {
                if ($fieldName === '__atkml') {
                    continue;
                }

                if ($fieldName === $row_model->id_field && $value) {
                    $row_model->load($value);
                }

                $field = $row_model->getField($fieldName);
                if ($field->isEditable()) {
                    $field->set($value);
                }
            }
            $id = $row_model->save()->getId();

            $k = array_search($id, $currentIds, true);
            if ($k !== false) {
                unset($currentIds[$k]);
            }
        }

        // Delete remaining currentIds
        foreach ($currentIds as $id) {
            $model->delete($id);
        }

        return $this;
    }

    /**
     * Check for model validate error.
     *
     * @return mixed
     */
    protected function addModelValidateErrors($errors, $rowId, $model)
    {
        $e = $model->validate();
        if ($e) {
            foreach ($e as $field => $msg) {
                $errors[$rowId][] = ['field' => $field, 'msg' => $msg];
            }
        }

        return $errors;
    }

    /**
     * for javascript use - changing this method may brake JS functionality.
     *
     * Finds and returns Multiline row id.
     *
     * @return |null
     */
    private function getMlRowId(array $row)
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
     * Will return a model reference if reference was set in setModel.
     * Otherwise, will return main model.
     *
     * @return Model
     */
    public function getModel()
    {
        $model = $this->model;
        if ($this->modelRef) {
            $model = $model->ref($this->modelRef);
        }

        return $model;
    }

    /**
     * Set view model.
     * If modelRef is used then getModel will return proper model.
     *
     * @param array $fields
     *
     * @return Model
     */
    public function setModel(Model $model, $fields = [], $modelRef = null, $linkField = null)
    {
        // Remove Multiline field name from model
        if ($model->hasField($this->short_name)) {
            $model->getField($this->short_name)->never_persist = true;
        }
        $model = parent::setModel($model);

        if ($modelRef) {
            if (!$linkField) {
                throw new Exception('Using model ref required to set $linkField');
            }
            $this->linkField = $linkField;
            $this->modelRef = $modelRef;
            $model = $model->ref($modelRef);
        }

        if (!$fields) {
            $fields = array_keys($model->getFields('not system'));
        }
        $this->rowFields = array_merge([$model->id_field], $fields);

        foreach ($this->rowFields as $fieldName) {
            $this->fieldDefs[] = $this->getFieldDef($model->getField($fieldName));
        }

        return $model;
    }

    /**
     * Return field definition in order to properly render them in Multiline.
     *
     * Multiline uses Vue components in order to manage input type based on field type.
     * Component name and props are determine via the getComponentDefinition function.
     *
     */
    public function getFieldDef(Field $field): array
    {

        return [
            'field' => $field->short_name,
            'definition' => $this->getComponentDefinition($field),
            'cellProps' => $this->getSuiTableCellProps($field),
            'caption' => $field->getCaption(),
            'default' => $field->default,
            'isExpr' => isset($field->expr),
            'isHidden' => $field->isHidden(),
            'isVisible' => $field->isVisible(),
        ];
    }

    /**
     * Each field input, represent by a Vue component, is place within a table cell.
     * This table cell is also a Vue component that can use Props: sui-table-cell.
     *
     * Cell properties can be applied globally via $options['sui-table-cell'] or per field
     * via  $field->ui['multiline']['sui-table-cell']
     */
    protected function getSuiTableCellProps(Field $field): array
    {
        $props = [];

        if ($field->type === 'money' || $field->type === 'number' || $field->type === 'integer') {
            $props['text-align'] = 'right';
        }

        return array_merge($props, $this->componentProps[self::TABLE_CELL] ?? [], $field->ui['multiline'][self::TABLE_CELL] ?? []);
    }

    /**
     * Return props for input component.
     */
    protected function getSuiInputProps(Field $field)
    {
        $props = $this->componentProps[self::INPUT] ?? [];

        $props['type'] = ($field->type === 'integer' || $field->type === 'float' || $field->type === 'money') ? 'number' : 'text';

        return array_merge($props, $field->ui['multiline'][self::INPUT] ?? []);
    }

    /**
     * Return props for atk-date-picker component.
     */
    protected function getDatePickerProps(Field $field): array
    {
        $calendar = new Calendar();
        $props = $this->componentProps[self::DATE]['flatpickr'] ?? [];
        $format = $calendar->translateFormat($this->getApp()->ui_persistence->{$field->type . '_format'});
        $props['dateFormat'] = $format;

        if ($field->type === 'datetime' || $field->type === 'time') {
            $props['enableTime'] = true;
            $props['time_24hr'] = $calendar->use24hrTimeFormat($format);
            $props['noCalendar'] = ($field->type === 'time');
            $props['enableSeconds'] = $calendar->useSeconds($format);
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
     * Return a component definition.
     * Component definition require at least a name and a props array.
     */
    protected function getComponentDefinition(Field $field)
    {
        if (!$field->isEditable()) {
            $component = $this->fieldMapToComponent['readonly'];
        } elseif ($field->enum || $field->values) {
            $component = $this->fieldMapToComponent['select'];
        } elseif ($field->type === 'date' || $field->type === 'time' || $field->type === 'datetime') {
            $component = $this->fieldMapToComponent['date'];
        } elseif ($field->type === 'text') {
            $component = $this->fieldMapToComponent['textarea'];
        } elseif ($field->reference) {
            $component = $this->fieldMapToComponent['lookup'];
        } else {
            $component = $this->fieldMapToComponent['default'];
        }

        $definition =  array_map(function ($value) use ($field) {
            return is_array($value) && is_callable($value) ? call_user_func($value, $field) : $value;
        }, $component);

        return $definition;
    }

    /**
     * Return array of possible items set for a field.
     */
    protected function getFieldItems(Field $field, $limit = 10): array
    {
        $items = [];
        if ($field->enum) {
            $items = array_chunk(array_combine($field->enum, $field->enum), $limit, true)[0];
        }
        if ($field->values && is_array($field->values)) {
            $items = array_chunk($field->values, $limit, true)[0];
        } elseif ($field->reference) {
            $model = $field->reference->refModel();
            $model->setLimit($limit);

            foreach ($model as $item) {
                $items[$item->get($field->reference->getTheirFieldName())] = $item->get($model->title_field);
            }
        }

        return $items;
    }

    protected function renderView(): void
    {
        if (!$this->getModel()) {
            throw new Exception('Multiline field needs to have it\'s model setup.');
        }

        $this->renderCallback->set(function () {
            try {
                return $this->outputJson();
            } catch (\Atk4\Core\Exception | \Error $e) {
                $this->getApp()->terminateJson(['success' => false, 'error' => $e->getMessage()]);
            }
        });

        $this->multiLine->template->tryDangerouslySetHtml('Input', $this->getInput());
        parent::renderView();

        $this->multiLine->vue(
            'atk-multiline',
            [
                'data' => [
                    'linesField' => $this->short_name,
                    'fields' => $this->fieldDefs,
                    'idField' => $this->getModel()->id_field,
                    'url' => $this->renderCallback->getJsUrl(),
                    'eventFields' => $this->eventFields,
                    'hasChangeCb' => $this->onChangeFunction ? true : false,
                    'tableProps' => $this->tableProps,
                    'rowLimit' => $this->rowLimit,
                    'caption' => $this->caption,
                    'afterAdd' => $this->jsAfterAdd,
                    'afterDelete' => $this->jsAfterDelete,
                    'addOnTab' => $this->addOnTab,
                ],
            ]
        );
    }

    /**
     * For javascript use - changing these methods may brake JS functionality.
     *
     * Render callback.
     */
    private function outputJson()
    {
        $action = $_POST['__atkml_action'] ?? null;
        $response = [
            'success' => true,
            'message' => 'Success',
        ];

        switch ($action) {
            case 'update-row':
                $model = $this->setDummyModelValue(clone $this->getModel());
                $expressionValues = array_merge($this->getExpressionValues($model), $this->getCallbackValues($model));
                $this->getApp()->terminateJson(array_merge($response, ['expressions' => $expressionValues]));

                break;
            case 'on-change':
                // Let regular callback render output.
                return ($this->onChangeFunction)($this->getApp()->decodeJson($_POST['rows']), $this->form);

                break;
        }
    }

    /**
     * For javascript use - changing this method may brake JS functionality.
     *
     * Return values associated with callback field.
     *
     * @return array
     */
    private function getCallbackValues($model)
    {
        $values = [];
        foreach ($this->fieldDefs as $def) {
            $fieldName = $def['field'];
            if ($fieldName === $model->id_field) {
                continue;
            }
            $field = $model->getField($fieldName);
            if ($field instanceof Callback) {
                $value = ($field->expr)($model);
                $values[$fieldName] = $this->getApp()->ui_persistence->_typecastSaveField($field, $value);
            }
        }

        return $values;
    }

    /**
     * For javascript use - changing this method may brake JS functionality.
     *
     * Looks inside the POST of the request and loads data into model.
     * Allow to Run expression base on rowData value.
     */
    private function setDummyModelValue($model)
    {
        $post = $_POST;

        foreach ($this->fieldDefs as $def) {
            $fieldName = $def['field'];
            if ($fieldName === $model->id_field) {
                continue;
            }

            $value = $post[$fieldName] ?? null;
            if ($model->getField($fieldName)->isEditable()) {
                try {
                    $model->set($fieldName, $value);
                } catch (ValidationException $e) {
                    // Bypass validation at this point.
                }
            }
        }

        return $model;
    }

    /**
     * For javascript use - changing this method may brake JS functionality.
     *
     * Return values associated to field expression.
     *
     * @return array
     */
    private function getExpressionValues($model)
    {
        $dummyFields = [];
        $formatValues = [];

        foreach ($this->getExpressionFields($model) as $k => $field) {
            $dummyFields[$k]['name'] = $field->short_name;
            $dummyFields[$k]['expr'] = $this->getDummyExpression($field, $model);
        }

        if (!empty($dummyFields)) {
            $dummyModel = new Model($model->persistence, ['table' => $model->table]);
            foreach ($dummyFields as $field) {
                $dummyModel->addExpression($field['name'], ['expr' => $field['expr'], 'type' => $model->getField($field['name'])->type]);
            }
            $values = $dummyModel->loadAny()->get();
            unset($values[$model->id_field]);

            foreach ($values as $f => $value) {
                $field = $model->getField($f);
                $formatValues[$f] = $this->getApp()->ui_persistence->_typecastSaveField($field, $value);
            }
        }

        return $formatValues;
    }

    /**
     * For javascript use - changing this method may brake js functionality.
     *
     * Get all field expression in model, but only evaluate expression used in
     * rowFields.
     *
     * @return array
     */
    private function getExpressionFields($model)
    {
        $fields = [];
        foreach ($model->getFields() as $field) {
            if (!$field instanceof FieldSqlExpression || !in_array($field->short_name, $this->rowFields, true)) {
                continue;
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * For javascript use - changing this method may brake JS functionality.
     *
     * Return expression where fields are replace with their current or default value.
     * Ex: total field expression = [qty] * [price] will return 4 * 100
     * where qty and price current value are 4 and 100 respectively.
     *
     * @return mixed
     */
    private function getDummyExpression($exprField, $model)
    {
        $expr = $exprField->expr;
        $matches = [];

        preg_match_all('/\[[a-z0-9_]*\]|{[a-z0-9_]*}/i', $expr, $matches);

        foreach ($matches[0] as $match) {
            $fieldName = substr($match, 1, -1);
            $field = $model->getField($fieldName);
            if ($field instanceof FieldSqlExpression) {
                $expr = str_replace($match, $this->getDummyExpression($field, $model), $expr);
            } else {
                $expr = str_replace($match, $this->getValueForExpression($exprField, $fieldName, $model), $expr);
            }
        }

        return $expr;
    }

    /**
     * For javascript use - changing this method may brake JS functionality.
     *
     * Return a value according to field used in expression and the expression type.
     * If field used in expression is null, the default value is returned.
     *
     * @return int|mixed|string
     */
    private function getValueForExpression($exprField, $fieldName, Model $model)
    {
        switch ($exprField->type) {
            case 'money':
            case 'integer':
            case 'float':
                // Value is 0 or the field value.
                $value = $model->get($fieldName) ?: 0;

                break;
            default:
                $value = '"' . $model->get($fieldName) . '"';
        }

        return $value;
    }
}
