<?php
/**
 * Creates a Multiline field within a table, which allows adding/editing multiple
 * data rows.
 *
 * To save the data, use the Multiline::saveRows() method. If the Multiline's
 * model is a reference to your form's model, the form model should be saved prior
 * to calling saveRows().
 *
 * $f = \atk4\ui\Form::addTo($app);
 * $f->setModel($invoice, false);
 * // Add Form fields
 *
 * // Add Multiline field and set model for it.
 * $ml = $f->addField('ml', ['Multiline']);
 *
 * // Set model using hasMany reference of Invoice.
 * $ml->setModel($invoice, ['item','cat','qty','price', 'total'], 'Items', 'invoice_id');
 *
 * $f->onSubmit(function($f) use ($ml) {
 *     // Save Form model and then Multiline model
 *     $f->model->save();
 *     $ml->saveRows();
 *     return new \atk4\ui\jsToast('Saved!');
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
 * $ml->onChange(function($rows) use ($f) {
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
 *   return $f->js(true, null, 'input[name="grand_total"]')->val(number_format($grand_total, 2));
 * }, ['qty', 'price']);
 *
 * Finally, it's also possible to use Multiline for quickly adding records to a
 * model. Be aware that in the example below all User records will be displayed.
 * If your model contains a lot of records, you should handle their limit somehow.
 *
 * $f = \atk4\ui\Form::addTo($app);
 * $ml = $f->addField('ml', ['MultiLine']);
 * $ml->setModel($user, ['name','is_vip']);
 *
 * $f->onSubmit(function($f) use ($ml) {
 *     $ml->saveRows();
 *     return new \atk4\ui\jsToast('Saved!');
 * });
 */

namespace atk4\ui\FormField;

use atk4\data\Field\Callback;
use atk4\data\Field_SQL_Expression;
use atk4\data\Model;
use atk4\data\Reference\HasOne;
use atk4\data\ValidationException;
use atk4\ui\Exception;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;
use atk4\ui\jsVueService;
use atk4\ui\Template;

class MultiLine extends Generic
{
    /**
     * Layout view as is within form layout.
     *
     * @var bool
     */
    public $layoutWrap = false;

    /**
     * The template needed for the multiline view.
     *
     * @var Template
     */
    public $multiLineTemplate = null;

    /**
     * The multiline View. Assigned in init().
     *
     * @var View
     */
    private $multiLine = null;

    /**
     * An array of options for sui-table property.
     * Example: ['celled' => true] will render column lines in table.
     *
     * @var array
     */
    public $options = [];

    /**
     * When true, tabbing out of the last column in last row of data
     * will automatically add a new row of record.
     *
     * @var bool
     */
    public $addOnTab = false;

    /**
     * The definition of each field used in every multiline row.
     *
     * @var array
     */
    private $fieldDefs = null;

    /**
     * The JS callback.
     *
     * @var jsCallback
     */
    private $cb = null;

    /**
     * The callback function which gets triggered when fields are changed or
     * rows get deleted.
     *
     * @var callable
     */
    public $changeCb = null;

    /**
     * Array of field names that will trigger the change callback when those
     * fields are changed.
     *
     * @var array
     */
    public $eventFields = null;

    /**
     * Collection of field errors.
     *
     * @var array
     */
    private $rowErrors = null;

    /**
     * The model reference name used for Multiline input.
     *
     * @var string
     */
    public $modelRef = null;

    /**
     * The link field used for reference.
     *
     * @var string
     */
    public $linkField = null;

    /**
     * The fields names used in each row.
     *
     * @var array
     */
    public $rowFields = null;

    /**
     * The data sent for each row.
     *
     * @var array
     */
    public $rowData = null;

    /**
     * The max number of records that can be added to Multiline. 0 means no limit.
     *
     * @var int
     */
    public $rowLimit = 0;

    /**
     * Model's max rows limit to be used in enum fields.
     * Enum fields are display as Dropdown inputs.
     * This limit is set on model reference used by a field.
     *
     * @var int
     */
    public $enumLimit = 100;

    /**
     * Multiline's caption.
     *
     * @var string
     */
    public $caption = null;

    /**
     * @var null | jsFunction
     *
     * A jsFunction to execute when Multiline add(+) button is clicked.
     * The function is execute after mulitline component finish adding a row of fields.
     * The function also receive the row vaue as an array.
     * ex: $jsAfterAdd = new jsFunction(['value'],[new jsExpression('console.log(value)')]);
     */
    public $jsAfterAdd = null;

    /**
     * @var null | jsFunction
     *
     * A jsFunction to execute when Multiline delete button is clicked.
     * The function is execute after mulitline component finish deleting rows.
     * The function also receive the row vaue as an array.
     * ex: $jsAfterDelete = new jsFunction(['value'],[new jsExpression('console.log(value)')]);
     */
    public $jsAfterDelete = null;

    public function init(): void
    {
        parent::init();

        if (!$this->multiLineTemplate) {
            $this->multiLineTemplate = new Template('<div id="{$_id}" class="ui"><atk-multiline v-bind="initData"></atk-multiline><div class="ui hidden divider"></div>{$Input}</div>');
        }

        /* No need for this anymore. See: https://github.com/atk4/ui/commit/8ec4d22cf9dcbd4969d9c88d8f09b705ca8798a6
        if ($this->model) {
            $this->setModel($this->model);
        }
        */

        $this->multiLine = \atk4\ui\View::addTo($this, ['template' => $this->multiLineTemplate]);

        $this->cb = \atk4\ui\jsCallback::addTo($this);

        // load the data associated with this input and validate it.
        $this->form->onHook('loadPOST', function ($form) {
            $this->rowData = $this->app->decodeJson($_POST[$this->short_name]);
            if ($this->rowData) {
                $this->rowErrors = $this->validate($this->rowData);
                if ($this->rowErrors) {
                    throw new ValidationException([$this->short_name => 'multiline error']);
                }
            }
        });

        // Change form error handling.
        $this->form->onHook('displayError', function ($form, $fieldName, $str) {
            // When errors are coming from this Multiline field, then notify Multiline component about them.
            // Otherwise use normal field error.
            if ($fieldName === $this->short_name) {
                $jsError = [(new jsVueService())->emitEvent('atkml-row-error', ['id' => $this->multiLine->name, 'errors' => $this->rowErrors])];
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
     * @param callable $fx
     * @param array    $fields
     *
     * @throws Exception
     */
    public function onLineChange($fx, $fields)
    {
        if (!is_callable($fx)) {
            throw new Exception('Function is required for onLineChange event.');
        }
        $this->eventFields = $fields;

        $this->changeCb = $fx;
    }

    /**
     * Input field collecting multiple rows of data.
     *
     * @throws \atk4\core\Exception
     *
     * @return string
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'        => $this->short_name,
            'type'        => 'hidden',
            'value'       => $this->getValue(),
            'readonly'    => true,
        ]);
    }

    /**
     * Get Multiline initial field value. Value is based on model set and will
     * output data rows as json string value.
     *
     * @throws \atk4\core\Exception
     *
     * @return false|string
     */
    public function getValue()
    {
        $m = null;
        // Will load data when using containsMany.
        $data = $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get());

        // If data is empty try to load model data directly. - For hasMany model
        // or array model already populated with data.
        if (empty($data)) {
            // Set model according to model reference if set, or simply the model passed to it.
            if ($this->model->loaded() && $this->modelRef) {
                $m = $this->model->ref($this->modelRef);
            } elseif (!$this->modelRef) {
                $m = $this->model;
            }
            if ($m) {
                foreach ($m as $id => $row) {
                    $d_row = [];
                    foreach ($this->rowFields as $fieldName) {
                        $field = $m->getField($fieldName);
                        if ($field->isEditable()) {
                            $value = $row->get($field);
                        } else {
                            $value = $this->app->ui_persistence->_typecastSaveField($field, $row->get($field));
                        }
                        $d_row[$fieldName] = $value;
                    }
                    $data[] = $d_row;
                }
            }
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        return $data;
    }

    /**
     * Validate each row and return errors if found.
     *
     * @throws \atk4\core\Exception
     *
     * @return array|null
     */
    public function validate(array $rows)
    {
        $rowErrors = [];
        $m = $this->getModel();

        foreach ($rows as $row => $cols) {
            $rowId = $this->getMlRowId($cols);
            foreach ($cols as $fieldName => $value) {
                if ($fieldName === '__atkml' || $fieldName === $m->id_field) {
                    continue;
                }

                try {
                    $field = $m->getField($fieldName);
                    // Save field value only if the field was editable
                    if (!$field->read_only) {
                        $m[$fieldName] = $this->app->ui_persistence->typecastLoadField($field, $value);
                    }
                } catch (\atk4\core\Exception $e) {
                    $rowErrors[$rowId][] = ['field' => $fieldName, 'msg' => $e->getMessage()];
                }
            }
            $rowErrors = $this->addModelValidateErrors($rowErrors, $rowId, $m);
        }

        if ($rowErrors) {
            return $rowErrors;
        }
    }

    /**
     * Save rows.
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function saveRows()
    {
        // If we are using a reference, make sure main model is loaded.
        if ($this->modelRef && !$this->model->loaded()) {
            throw new Exception('Parent model need to be loaded');
        }

        $model = $this->model;
        if ($this->modelRef) {
            $model = $model->ref($this->modelRef);
        }

        $currentIds = [];
        foreach ($this->getModel() as $id => $data) {
            $currentIds[] = $id;
        }

        foreach ($this->rowData as $row) {
            if ($this->modelRef && $this->linkField) {
                $model[$this->linkField] = $this->model->get('id');
            }

            foreach ($row as $fieldName => $value) {
                if ($fieldName === '__atkml') {
                    continue;
                }

                if ($fieldName === $model->id_field && $value) {
                    $model->load($value);
                }

                $field = $model->getField($fieldName);
                if ($field->isEditable()) {
                    $field->set($value);
                }
            }
            $id = $model->save()->get($model->id_field);
            $k = array_search($id, $currentIds);
            if ($k > -1) {
                unset($currentIds[$k]);
            }

            $model->unload();
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
            foreach ($e as $f => $msg) {
                $errors[$rowId][] = ['field' => $f, 'msg' => $msg];
            }
        }

        return $errors;
    }

    /**
     * for javascript use - changing this method may brake JS functionality.
     *
     * Finds and returns MultiLine row id.
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
     * @throws \atk4\core\Exception
     *
     * @return Model
     */
    public function getModel()
    {
        $m = $this->model;
        if ($this->modelRef) {
            $m = $m->ref($this->modelRef);
        }

        return $m;
    }

    /**
     * Set view model.
     * If modelRef is used then getModel will return proper model.
     *
     * @param Model $m
     * @param array $fields
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return Model
     */
    public function setModel(\atk4\data\Model $model, $fields = [], $modelRef = null, $linkField = null)
    {
        // Remove Multiline field name from model
        if ($model->hasField($this->short_name)) {
            $model->getField($this->short_name)->never_persist = true;
        }
        $m = parent::setModel($model);

        if ($modelRef) {
            if (!$linkField) {
                throw new Exception('Using model ref required to set $linkField');
            }
            $this->linkField = $linkField;
            $this->modelRef = $modelRef;
            $m = $m->ref($modelRef);
        }

        if (!$fields) {
            $fields = array_keys($m->getFields('not system'));
        }
        $this->rowFields = array_merge([$m->id_field], $fields);

        foreach ($this->rowFields as $fieldName) {
            $this->fieldDefs[] = $this->getFieldDef($m->getField($fieldName));
        }

        return $m;
    }

    /**
     * Return the field definition to use in JS for rendering this field.
     * $component is one of the following html input types:
     * - input
     * - dropdown
     * - checkbox
     * - textarea.
     *
     * Depending on the component, additional data is set to fieldOptions
     * (dropdown needs values, input needs type)
     *
     * @param \atk4\data\Field $field
     *
     * @return array
     */
    public function getFieldDef(\atk4\data\Field $field): array
    {
        // Default is input
        $component = 'input';

        // First check in Field->ui['multiline'] if there are settings for Multiline display
        // $test = $field->ui['multiline'];
        if (isset($field->ui['multiline'][0])) {
            $component = $this->_mapComponent($field->ui['multiline'][0]);
        }
        // Next, check if there is a 'standard' UI seed set
        elseif (isset($field->ui['form'][0])) {
            $component = $this->_mapComponent($field->ui['form'][0]);
        }
        // If values or enum property is set, display a DropDown
        elseif ($field->enum || $field->values || $field->reference instanceof HasOne) {
            $component = 'dropdown';
        }
        // Figure UI FormField type by field type.
        // TODO: Form already does this, maybe use that somehow?
        elseif ($field->type) {
            $component = $this->_mapComponent($field->type);
        }

        return [
            'field'       => $field->short_name,
            'component'   => $component,
            'caption'     => $field->getCaption(),
            'default'     => $field->default,
            'isExpr'      => isset($field->expr),
            'isEditable'  => $field->isEditable(),
            'isHidden'    => $field->isHidden(),
            'isVisible'   => $field->isVisible(),
            'fieldOptions'=> $this->_getFieldOptions($field, $component),
        ];
    }

    /**
     * Maps into input, checkbox, dropdown or textarea, defaults into input.
     *
     * @param string|object $field_type
     *
     * @return string
     */
    protected function _mapComponent($field_type): string
    {
        if (is_string($field_type)) {
            switch (strtolower($field_type)) {
                case 'dropdown':
                case 'enum':
                    return 'dropdown';
                case 'boolean':
                case 'checkbox':
                    return 'checkbox';
                case 'text':
                case 'textarea':
                    return 'textarea';
                default: return 'input';
            }
        }

        // If an object was passed, use its classname as string
        elseif (is_object($field_type)) {
            return $this->_mapComponent((new \ReflectionClass($field_type))->getShortName());
        }

        return 'input';
    }

    protected function _getFieldOptions(\atk4\data\Field $field, string $component): array
    {
        $options = [];

        // If additional options are defined for field, add them.
        if (isset($field->ui['multiline']) && is_array($field->ui['multiline'])) {
            $add_options = $field->ui['multiline'];
            if (isset($add_options[0])) {
                if (is_array($add_options[0])) {
                    $options = array_merge($options, $add_options[0]);
                }
                if (isset($add_options[1]) && is_array($add_options[1])) {
                    $options = array_merge($options, $add_options[1]);
                }
            } else {
                $options = array_merge($options, $add_options);
            }
        } elseif (isset($field->ui['form']) && is_array($field->ui['form'])) {
            $add_options = $field->ui['form'];
            if (isset($add_options[0])) {
                unset($add_options[0]);
            }
            $options = array_merge($options, $add_options);
        }

        // Some input types need additional options set, make sure they are there
        switch ($component) {
            // Input needs to have type set (text, number, date etc)
            case 'input':
                if (!isset($options['type'])) {
                    $options['type'] = $this->_addTypeOption($field);
                }
                break;
            // DropDown needs values set
            case 'dropdown':
                if (!isset($options['values'])) {
                    $options['values'] = $this->_addValuesOption($field);
                }
                break;
        }

        return $options;
    }

    /**
     * HTML input field needs type property set. If it wasnt found in $field->ui,
     * determine from rest.
     */
    protected function _addTypeOption(\atk4\data\Field $field): string
    {
        switch ($field->type) {
            case 'integer':
                return 'number';
            //case 'date':
                //return 'date';
            default:
                return 'text';
        }
    }

    /**
     * DropDown field needs values set. If it wasnt found in $field->ui, determine
     * from rest.
     */
    protected function _addValuesOption(\atk4\data\Field $field): array
    {
        if ($field->enum) {
            return array_combine($field->enum, $field->enum);
        }
        if ($field->values && is_array($field->values)) {
            return $field->values;
        } elseif ($field->reference) {
            $m = $field->reference->refModel()->setLimit($this->enumLimit);

            $values = [];
            foreach ($m->export([$m->id_field, $m->title_field]) as $item) {
                $values[$item[$m->id_field]] = $item[$m->title_field];
            }

            return $values;
        }

        return [];
    }

    public function renderView()
    {
        if (!$this->getModel()) {
            throw new Exception('Multiline field needs to have it\'s model setup.');
        }

        if ($this->cb->triggered()) {
            $this->cb->set(function () {
                try {
                    return $this->renderCallback();
                } catch (\atk4\Core\Exception $e) {
                    $this->app->terminateJSON(['success' => false, 'error' => $e->getMessage()]);
                } catch (\Error $e) {
                    $this->app->terminateJSON(['success' => false, 'error' => $e->getMessage()]);
                }
            });
        }

        $this->multiLine->template->trySetHTML('Input', $this->getInput());
        parent::renderView();

        $this->multiLine->vue(
            'atk-multiline',
            [
                                  'data' => [
                                      'linesField'  => $this->short_name,
                                      'fields'      => $this->fieldDefs,
                                      'idField'     => $this->getModel()->id_field,
                                      'url'         => $this->cb->getJSURL(),
                                      'eventFields' => $this->eventFields,
                                      'hasChangeCb' => $this->changeCb ? true : false,
                                      'options'     => $this->options,
                                      'rowLimit'    => $this->rowLimit,
                                      'caption'     => $this->caption,
                                      'afterAdd'    => $this->jsAfterAdd,
                                      'afterDelete' => $this->jsAfterDelete,
                                      'addOnTab'    => $this->addOnTab,
                                  ],
                              ]
        );
    }

    /**
     * For javascript use - changing these methods may brake JS functionality.
     *
     * Render callback.
     *
     * @throws ValidationException
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    private function renderCallback()
    {
        $action = $_POST['__atkml_action'] ?? null;
        $response = [
            'success' => true,
            'message' => 'Success',
        ];

        switch ($action) {
            case 'update-row':
                $m = $this->setDummyModelValue(clone $this->getModel());
                $expressionValues = array_merge($this->getExpressionValues($m), $this->getCallbackValues($m));
                $this->app->terminateJSON(array_merge($response, ['expressions' => $expressionValues]));
                break;
            case 'on-change':
                // Let regular callback render output.
                return call_user_func_array($this->changeCb, [$this->app->decodeJson($_POST['rows']), $this->form]);
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
                $value = call_user_func($field->expr, $model);
                $values[$fieldName] = $this->app->ui_persistence->_typecastSaveField($field, $value);
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
                    $model[$fieldName] = $value;
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
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return array
     */
    private function getExpressionValues($m)
    {
        $dummyFields = [];
        $formatValues = [];

        foreach ($this->getExpressionFields($m) as $k => $field) {
            $dummyFields[$k]['name'] = $field->short_name;
            $dummyFields[$k]['expr'] = $this->getDummyExpression($field, $m);
        }

        if (!empty($dummyFields)) {
            $dummyModel = new Model($m->persistence, ['table' => $m->table]);
            foreach ($dummyFields as $f) {
                $dummyModel->addExpression($f['name'], ['expr'=>$f['expr'], 'type' => $m->getField($f['name'])->type]);
            }
            $values = $dummyModel->loadAny()->get();
            unset($values[$m->id_field]);

            foreach ($values as $f => $value) {
                $field = $m->getField($f);
                $formatValues[$f] = $this->app->ui_persistence->_typecastSaveField($field, $value);
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
        foreach ($model->getFields() as $f) {
            if (!$f instanceof Field_SQL_Expression || !in_array($f->short_name, $this->rowFields)) {
                continue;
            }

            $fields[] = $f;
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
     * @throws \atk4\core\Exception
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
            if ($field instanceof Field_SQL_Expression) {
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
    private function getValueForExpression($exprField, $fieldName, $model)
    {
        switch ($exprField->type) {
            case 'money':
            case 'integer':
            case 'float':
                // Value is 0 or the field value.
                $value = $model[$fieldName] ? $model[$fieldName] : 0;
                break;
            default:
                // Value is "" or field value enclosed in bracket: "value"
                $value = $model[$fieldName] ? '"' . $model[$fieldName] . '"' : '""';
        }

        return $value;
    }
}
