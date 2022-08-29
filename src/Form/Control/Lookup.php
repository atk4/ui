<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model;
use Atk4\Data\Persistence\Sql\Query;
use Atk4\Ui\Button;
use Atk4\Ui\Callback;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsModal;
use Atk4\Ui\VirtualPage;

class Lookup extends Input
{
    public $defaultTemplate = 'formfield/autocomplete.html';

    /**
     * Object used to capture requests from the browser.
     *
     * @var Callback
     */
    public $callback;

    /**
     * Set this to true, to permit "empty" selection. If you set it to string, it will be used as a placeholder for empty value.
     *
     * @var string
     */
    public $empty = '...';

    /**
     * Either set this to array of fields which must be searched (e.g. "name", "surname"), or define this
     * as a callback to be executed callback($model, $query);.
     *
     * If left null, then search will be performed on a model's title field
     *
     * @var array|\Closure|null
     */
    public $search;

    /**
     * Set this to create right-aligned button for adding a new a new record.
     *
     * true = will use "Add new" label
     * string = will use your string
     *
     * @var bool|string|null
     */
    public $plus = false;

    /**
     * Sets the max. amount of records that are loaded. The default 10
     * displays nicely in UI.
     *
     * @var int
     */
    public $limit = 10;

    /**
     * Set custom model field here to use it's value as ID in dropdown instead of default model ID field.
     *
     * @var string|null
     */
    public $idField;

    /**
     * Set custom model field here to display it's value in dropdown instead of default model title field.
     *
     * @var string|null
     */
    public $titleField;

    /**
     * Fomantic-UI uses cache to remember choices. For dynamic sites this may be dangerous, so
     * it's disabled by default. To switch cache on, set 'cache' => 'local'.
     *
     * Use this apiConfig variable to pass API settings to Fomantic-UI in .dropdown()
     *
     * @var array<string, mixed>
     */
    public array $apiConfig = [];

    /**
     * Fomantic-UI dropdown module settings.
     * Use this setting to configure various dropdown module settings
     * to use with Autocomplete.
     *
     * For example, using this setting will automatically submit
     * form when field value is changes.
     * $form->addField('field', [
     *     'AutoComplete',
     *     'settings' => [
     *         'allowReselection' => true,
     *         'selectOnKeydown' => false,
     *         'onChange' => new JsExpression('function(value, t, c) {
     *             if ($(this).data("value") !== value) {
     *                 $(this).parents(".form").form("submit");
     *                 $(this).data("value", value);
     *             }
     *         }'),
     *     ]
     * ]);
     *
     * @var array<string, mixed>
     */
    public array $settings = [];

    /**
     * Default options for Autocomplete Fomantic-UI component.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultAutocompleteSettings(): array
    {
        $options = array_merge(Dropdown::getDefaultDropdownSettings(true), [
            'fields' => [
                'name' => 'title',
                'value' => 'value',
                'text' => 'title',
                'disabled' => 'disabled',
            ],
            'minCharacters' => 0,
            'filterRemoteData' => false,
            'saveRemoteData' => false,

            // it seems this does not work for JSON, so escaped on server side 'preserveHTML' => false, // default = true
            // sortSelect => true, // default = false
        ]);

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultAutocompleteApiConfig(): array
    {
        $apiConfig = [
            'method' => 'POST',
            // 'beforeXHR' => new JsFunction(['xhr'], [new JsExpression('xhr.setRequestHeader(\'Content-Type\', \'application/json; charset=UTF-8\');')]),
            'beforeSend' => new JsFunction(['settings'], [new JsExpression('var refCondValues = { }; var formFields = $(this).closest(\'.form\').serializeArray(); $.each(formFields, function(i, formField) { if ($.inArray(formField.name, [ref_condition_field_names]) !== -1) { refCondValues[formField.name] = formField.value;} }); settings.data = {\'autocomplete_query\': settings.urlData.query, \'autocomplete_ref_condition_values\': JSON.stringify(refCondValues)}; return settings;')]),
            'successTest' => new JsFunction(['response'], [new JsExpression('return response.success || false;')]),
            'onFailure' => new JsFunction(['response', 'elem'], [new JsExpression('elem.dropdown(\'set error\', \'API error\'); elem.dropdown(\'setup menu\', { \'values\': { } }); elem.dropdown(\'add message\', \'API error: Invalid response\'); elem.dropdown(\'show\');')]),
            'onError' => new JsFunction(['errorMessage', 'elem'], [new JsExpression('elem.dropdown(\'set error\', \'API error\'); elem.dropdown(\'setup menu\', { \'values\': { } }); elem.dropdown(\'add message\', \'API error: \' + errorMessage); elem.dropdown(\'show\');')]),
            'cache' => false,
        ];

        return $apiConfig;
    }

    protected function init(): void
    {
        parent::init();

        $this->template->set('input_id', $this->name . '-ac');
        $this->template->set('place_holder', $this->placeholder);

        if ($this->plus) {
            $this->action = Button::addTo($this, [is_string($this->plus) ? $this->plus : 'Add new', 'disabled' => $this->disabled || $this->readOnly]);
        }
        // var_dump($this->model->get());
        $vp = VirtualPage::addTo($this->form ?? $this->getOwner());

        $vp->set(function ($p) {
            $f = Form::addTo($p);
            $f->setModel($this->model);

            $f->onSubmit(function ($f) {
                $id = $f->model->save()->getId();

                $modalChain = new Jquery('.atk-modal');
                $modalChain->modal('hide');
                $acChain = new Jquery('#' . $this->name . '-ac');
                $acChain->dropdown('set value', $id)->dropdown('set text', $f->model->getTitle());

                return [
                    $modalChain,
                    $acChain,
                ];
            });
        });
        if ($this->action) {
            $this->action->js('click', new JsModal('Adding New Record', $vp));
        }

        $this->apiConfig = static::getDefaultAutocompleteApiConfig();
        $this->settings = static::getDefaultAutocompleteSettings();
    }

    /**
     * Returns URL which would respond with first 50 matching records.
     */
    public function getCallbackUrl(): string
    {
        return $this->callback->getJsUrl();
    }

    /**
     * @return never
     */
    public function processAutocompleteRequest(): void
    {
        header('Cache-Control: no-cache'); // make sure the response is not cached

        $postQuery = $_POST['autocomplete_query'] ?? null;
        if ($postQuery === null) {
            throw new Exception('No autocomplete query');
        }

        $postLimit = $_POST['autocomplete_limit'] ?? null;
        if ($postLimit !== null) {
            if ((string) (int) $postLimit !== (string) $postLimit || $postLimit < 0 || $postLimit > 1000) {
                throw new Exception('Invalid autocomplete limit');
            }
            $limit = (int) $postLimit;
        } else {
            $limit = $this->limit;
        }
        unset($postLimit);

        $postRefCondValues = $_POST['autocomplete_ref_condition_values'] ?? null;
        if ($postRefCondValues !== null) {
            $refCondValues = json_decode($postRefCondValues, true, 512, \JSON_BIGINT_AS_STRING);
        } else {
            $refCondValues = [];
        }
        unset($postRefCondValues);

        $autocompleteItems = $this->getAutocompleteItems($postQuery, $refCondValues, $limit + 1);
        $isLimited = false;
        if (count($autocompleteItems) > $limit) {
            $isLimited = true;
            $autocompleteItems = array_slice($autocompleteItems, 0, $limit);
        }

        if ((!$this->entityField || !$this->entityField->getField()->required) && $this->empty) {
            $autocompleteItems[] = $this->getAutocompleteEmptyItem();
        }

        if ($isLimited) {
            $autocompleteItems[] = ['value' => '', 'title' => 'Only first ' . $limit . ' results are shown. Please type longer query.', 'disabled' => true];
        }

        $response = [
            'success' => true,
            'results' => array_map(static function ($item) {
                $item['title'] = /* \Mvorisek\Utils\Text::escapeHtml */ $item['title'];

                return $item;
            }, $autocompleteItems),
            'resultsLimited' => $isLimited,
        ];

        header('Content-Type: application/json; charset=UTF-8');
        $this->getApp()->terminate(json_encode($response));
    }

    /**
     * @return array<int, array{value: string, title: string}>
     */
    public function getAutocompleteItems(string $searchQuery, array $refCondValues, int $limit): array
    {
        if (!$this->model) {
            throw new Exception('Form field model is not configured');
        }

        /** @var Query $q */
        $q = $this->model->getPersistence()->dsql(); // @phpstan-ignore-line
        $topAndExpr = $q->andExpr();
        $isWhereSetFunc = static function (Query $query) {
            return isset($query->args['where']) && count($query->args['where']) > 0;
        };
        $addWhereCondFunc = function (Query $query, string $fieldName, $cond, $value = null, bool $allowTypecast = true) {
            if (func_num_args() === 3) {
                $value = $cond;
                $cond = '<=>';
            }

            // based on Persistence\Sql::initQueryConditions() method
            $field = $this->model->getElement($fieldName);
            if ($allowTypecast) {
                $value = $this->model->getPersistence()->typecastSaveField($field, $value);
            } else {
                if ($value !== null) {
                    $value = (string) $value;
                }
            }
            $query->where($field, $cond, $value);
        };

        // set ref conditions
        if ($this->entityField && isset($this->entityField->getField()->refConditions)) { // @phpstan-ignore-line TODO "refConditions"
            $orExpr = $q->orExpr();
            foreach ($this->entityField->getField()->refConditions as $refCond) {
                $refCondFieldNames = [];
                foreach ($refCond['fields'] as $fName) {
                    $refCondFieldNames[$fName] = (string) $fName;
                }

                $andExpr = $q->andExpr();
                foreach ($refCondFieldNames as $refCondFieldName) {
                    $refCondValue = $refCondValues[$refCondFieldName];
                    $addWhereCondFunc($andExpr, $refCondFieldName, $refCondValue);
                }
                if ($isWhereSetFunc($andExpr)) {
                    $orExpr->where($andExpr);
                }
            }
            if ($isWhereSetFunc($orExpr)) {
                $topAndExpr->where($orExpr);
            }
        }

        $titleField = $this->titleField ?? $this->model->titleField;

        // add query conditions
        $searchQuery = trim(/* \Mvorisek\Utils\Text::cleanString */ $searchQuery/* , false */);
        if ($searchQuery !== '') {
            if ($this->search instanceof \Closure) {
                ($this->search)($topAndExpr, $searchQuery, $titleField);
            } elseif ($this->search && is_array($this->search)) {
                $orExpr = $q->orExpr();
                foreach ($this->search as $field) {
                    $addWhereCondFunc($orExpr, $field, 'like', '%' . $searchQuery . '%', false);
                }
                if ($isWhereSetFunc($orExpr)) {
                    $topAndExpr->where($orExpr);
                }
            } else {
                $addWhereCondFunc($topAndExpr, $titleField, 'like', '%' . $searchQuery . '%', false);
            }
        }

        // clone model and set the builded where condition
        $model = clone $this->model;
        if ($isWhereSetFunc($topAndExpr)) {
            $model->addCondition($topAndExpr);
        }

        // set limit
        $model->setLimit($limit);
        $model->setOrder($titleField);

        // get items
        $items = [];
        foreach ($model as $row) {
            $items[] = $this->getAutocompleteItemFromModel($row);
        }

        return $items;
    }

    public function getInput()
    {
        return $this->getApp()->getTag('input', [
            'name' => $this->shortName,
            'type' => 'hidden',
            'id' => $this->name . '_input',
            'value' => $this->getValue(),
            'readonly' => $this->readOnly ? 'readonly' : false,
            'disabled' => $this->disabled ? 'disabled' : false,
        ]);
    }

    /**
     * Set Fomantic-UI Api settings to use with dropdown.
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
     * Override this method if you want to add more logic to the initialization of the
     * auto-complete field.
     *
     * @param Jquery $chain
     */
    protected function initDropdown($chain): void
    {
        if (!$this->model) {
            throw new Exception('Form field model is not configured');
        }

        $defaultOptions = [];
        $value = $this->getValue();
        if ($value !== '') {
            $this->model->tryLoad($value);
            $defaultOptions[] = array_merge($this->getAutocompleteItemFromModel($this->model), ['selected' => true]);
        } elseif ($this->empty) {
            $defaultOptions[] = array_merge($this->getAutocompleteEmptyItem(), ['selected' => true]);
        }

        // set ref conditions fields names, values for this fields will be send with the autocomplete query request
        $refCondFieldNames = [];
        if ($this->entityField && isset($this->entityField->getField()->refConditions)) { // @phpstan-ignore-line TODO "refConditions"
            foreach ($this->entityField->getField()->refConditions as $refCond) {
                foreach ($refCond['fields'] as $fName) {
                    $refCondFieldNames[(string) $fName] = (string) $fName;
                }
            }
        }
        $this->apiConfig['beforeSend']->fx_statements[0]->args = [
            'ref_condition_field_names' => array_values($refCondFieldNames), ];

        $settings = array_merge([
            'apiSettings' => array_merge(['url' => $this->getCallbackUrl()], $this->apiConfig),
            'values' => $defaultOptions,
        ], $this->settings);

        $chain->dropdown($settings);
    }

    public function renderView(): void
    {
        if ($this->icon || $this->iconLeft) { // our css fixes are currently not compatible with icons on either side
            throw (new Exception('Cannot use icon or iconLeft for dropdown'))
                ->addMoreInfo('icon', $this->icon)
                ->addMoreInfo('iconLeft', $this->iconLeft);
        }

        $this->callback = Callback::addTo($this);
        $this->callback->set(fn () => $this->processAutocompleteRequest());

        if ($this->disabled) {
            $this->template->set('disabledClass', 'disabled');
            $this->template->dangerouslySetHtml('disabled', 'disabled="disabled"');
        } elseif ($this->readOnly) {
            $this->template->set('disabledClass', 'read-only');
            $this->template->dangerouslySetHtml('disabled', 'readonly="readonly"');

            $this->settings['apiSettings'] = null;
            $this->settings['onShow'] = new JsFunction([new JsExpression('return false')]);
            $this->template->set('readonly', 'readonly');
        }

        $chain = new Jquery('#' . $this->name . '-ac');

        $this->initDropdown($chain);

        // fix: remove search term on outfocus event if dropdown is closed (user changed the search term,
        //      but data was not loaded yet - menu not shown yet), needed with forceSelection = false (default since Fomantic-UI v2.9.0)
        $chain->focusout( // @phpstan-ignore-line TODO add function to Jquery
            new jsFunction([new JsExpression('if (!$(this).dropdown(\'is visible\')) { $(this).dropdown(\'remove searchTerm\'); }')])
        );

        if ($this->entityField && $this->entityField->get()) {
            $idField = $this->idField ?? $this->model->idField;
            $titleField = $this->titleField ?? $this->model->titleField;

            $this->model->tryLoadBy($idField, $this->entityField->get());

            if (!$this->model->isLoaded()) {
                $this->entityField->set(null);
            } else {
                // IMPORTANT: always convert data to string, otherwise numbers can be rounded by JS
                $chain->dropdown('set value', (string) $this->model[$idField])
                    ->dropdown('set text', (string) $this->model[$titleField]);
                $this->js(true, $chain);
            }
        }

        $this->js(true, $chain);

        parent::renderView();
    }

    /**
     * @return array{value: string, title: string}
     */
    public function getAutocompleteItemFromModel(Model $model): array
    {
        if (!$model->isLoaded()) {
            throw new Exception('Form field model is not loaded');
        }

        $idField = $this->idField ?? $this->model->idField;
        $titleField = $this->titleField ?? $this->model->titleField;

        $value = $model->get($idField);
        if ($titleField !== $idField && $model->hasElement($titleField)) {
            $title = $model->get($titleField);
        } else {
            try {
                $title = strtoupper(/* \Mvorisek\Kelly\Model::encodeID */ $value);
            } catch (\Exception $e) {
                $title = $value;
            }
        }
        $item = ['value' => (string) $value, 'title' => (string) $title];

        return $item;
    }

    /**
     * @return array{value: string, title: string}
     */
    public function getAutocompleteEmptyItem(): array
    {
        return ['value' => '', 'title' => $this->empty];
    }
}
