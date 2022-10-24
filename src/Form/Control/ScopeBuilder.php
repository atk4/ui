<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Data\Model\Scope;
use Atk4\Data\Model\Scope\Condition;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\View;

class ScopeBuilder extends Form\Control
{
    use VueLookupTrait;

    /** @var bool Do not render label for this input. */
    public $renderLabel = false;

    public array $options = [
        'enum' => [
            'limit' => 250,
        ],
        'debug' => false, // displays query output live on the page if set to true
    ];
    /**
     * Max depth of nested conditions allowed.
     * Corresponds to VueQueryBulder maxDepth.
     * Maximum support by js component is 10.
     */
    public int $maxDepth = 5;

    /** Fields to use for creating the rules. */
    public array $fields = [];

    /** @var HtmlTemplate|null The template needed for the ScopeBuilder view. */
    public $scopeBuilderTemplate;

    /** List of delimiters for auto-detection in order of priority. */
    public static array $listDelimiters = [';', ','];

    /**
     * The date, time or datetime options:
     * 'flatpickr' - any of flatpickr options
     * 'useDefault' - when true, will init date, time or datetime to current.
     */
    public array $atkdDateOptions = [
        'useDefault' => false,
        'flatpickr' => [],
    ];

    /** Atk-lookup and Fomantic-UI dropdown options. */
    public array $atkLookupOptions = [
        'ui' => 'small basic button',
    ];

    /** @var View The scopebuilder View. Assigned in init(). */
    protected $scopeBuilderView;

    /** Definition of VueQueryBuilder rules. */
    protected array $rules = [];

    /**
     * Set Labels for Vue-Query-Builder
     * see https://dabernathy89.github.io/vue-query-builder/configuration.html#labels.
     */
    public array $labels = [];

    /** Default VueQueryBuilder query. */
    protected array $query = [];

    protected const OPERATOR_TEXT_EQUALS = 'equals';
    protected const OPERATOR_TEXT_DOESNOT_EQUAL = 'does not equal';
    protected const OPERATOR_TEXT_GREATER = 'is alphabetically after';
    protected const OPERATOR_TEXT_GREATER_EQUAL = 'is alphabetically equal or after';
    protected const OPERATOR_TEXT_LESS = 'is alphabetically before';
    protected const OPERATOR_TEXT_LESS_EQUAL = 'is alphabetically equal or before';
    protected const OPERATOR_TEXT_CONTAINS = 'contains';
    protected const OPERATOR_TEXT_DOESNOT_CONTAIN = 'does not contain';
    protected const OPERATOR_TEXT_BEGINS_WITH = 'begins with';
    protected const OPERATOR_TEXT_DOESNOT_BEGIN_WITH = 'does not begin with';
    protected const OPERATOR_TEXT_ENDS_WITH = 'ends with';
    protected const OPERATOR_TEXT_DOESNOT_END_WITH = 'does not end with';
    protected const OPERATOR_TEXT_MATCHES_REGEX = 'matches regular expression';
    protected const OPERATOR_TEXT_DOESNOT_MATCH_REGEX = 'does not match regular expression';
    protected const OPERATOR_SIGN_EQUALS = '=';
    protected const OPERATOR_SIGN_DOESNOT_EQUAL = '<>';
    protected const OPERATOR_SIGN_GREATER = '>';
    protected const OPERATOR_SIGN_GREATER_EQUAL = '>=';
    protected const OPERATOR_SIGN_LESS = '<';
    protected const OPERATOR_SIGN_LESS_EQUAL = '<=';
    protected const OPERATOR_TIME_EQUALS = 'is on';
    protected const OPERATOR_TIME_DOESNOT_EQUAL = 'is not on';
    protected const OPERATOR_TIME_GREATER = 'is after';
    protected const OPERATOR_TIME_GREATER_EQUAL = 'is on or after';
    protected const OPERATOR_TIME_LESS = 'is before';
    protected const OPERATOR_TIME_LESS_EQUAL = 'is on or before';
    protected const OPERATOR_EQUALS = 'equals';
    protected const OPERATOR_DOESNOT_EQUAL = 'does not equal';
    protected const OPERATOR_IN = 'is in';
    protected const OPERATOR_NOT_IN = 'is not in';
    protected const OPERATOR_EMPTY = 'is empty';
    protected const OPERATOR_NOT_EMPTY = 'is not empty';

    protected const DATE_OPERATORS = [
        self::OPERATOR_TIME_EQUALS,
        self::OPERATOR_TIME_DOESNOT_EQUAL,
        self::OPERATOR_TIME_GREATER,
        self::OPERATOR_TIME_GREATER_EQUAL,
        self::OPERATOR_TIME_LESS,
        self::OPERATOR_TIME_LESS_EQUAL,
        self::OPERATOR_EMPTY,
        self::OPERATOR_NOT_EMPTY,
    ];

    protected const ENUM_OPERATORS = [
        self::OPERATOR_EQUALS,
        self::OPERATOR_DOESNOT_EQUAL,
        self::OPERATOR_EMPTY,
        self::OPERATOR_NOT_EMPTY,
    ];

    protected const DATE_OPERATORS_MAP = [
        self::OPERATOR_TIME_EQUALS => Condition::OPERATOR_EQUALS,
        self::OPERATOR_TIME_DOESNOT_EQUAL => Condition::OPERATOR_DOESNOT_EQUAL,
        self::OPERATOR_TIME_GREATER => Condition::OPERATOR_GREATER,
        self::OPERATOR_TIME_GREATER_EQUAL => Condition::OPERATOR_GREATER_EQUAL,
        self::OPERATOR_TIME_LESS => Condition::OPERATOR_LESS,
        self::OPERATOR_TIME_LESS_EQUAL => Condition::OPERATOR_LESS_EQUAL,
    ];

    /**
     * VueQueryBulder => Condition map of operators.
     *
     * Operator map supports also inputType specific operators in sub maps
     *
     * @var array<string, array<string, string>>
     */
    protected static array $operatorsMap = [
        'number' => [
            self::OPERATOR_SIGN_EQUALS => Condition::OPERATOR_EQUALS,
            self::OPERATOR_SIGN_DOESNOT_EQUAL => Condition::OPERATOR_DOESNOT_EQUAL,
            self::OPERATOR_SIGN_GREATER => Condition::OPERATOR_GREATER,
            self::OPERATOR_SIGN_GREATER_EQUAL => Condition::OPERATOR_GREATER_EQUAL,
            self::OPERATOR_SIGN_LESS => Condition::OPERATOR_LESS,
            self::OPERATOR_SIGN_LESS_EQUAL => Condition::OPERATOR_LESS_EQUAL,
        ],
        'date' => self::DATE_OPERATORS_MAP,
        'time' => self::DATE_OPERATORS_MAP,
        'datetime' => self::DATE_OPERATORS_MAP,
        'text' => [
            self::OPERATOR_TEXT_EQUALS => Condition::OPERATOR_EQUALS,
            self::OPERATOR_TEXT_DOESNOT_EQUAL => Condition::OPERATOR_DOESNOT_EQUAL,
            self::OPERATOR_TEXT_GREATER => Condition::OPERATOR_GREATER,
            self::OPERATOR_TEXT_GREATER_EQUAL => Condition::OPERATOR_GREATER_EQUAL,
            self::OPERATOR_TEXT_LESS => Condition::OPERATOR_LESS,
            self::OPERATOR_TEXT_LESS_EQUAL => Condition::OPERATOR_LESS_EQUAL,
            self::OPERATOR_TEXT_CONTAINS => Condition::OPERATOR_LIKE,
            self::OPERATOR_TEXT_DOESNOT_CONTAIN => Condition::OPERATOR_NOT_LIKE,
            self::OPERATOR_TEXT_BEGINS_WITH => Condition::OPERATOR_LIKE,
            self::OPERATOR_TEXT_DOESNOT_BEGIN_WITH => Condition::OPERATOR_NOT_LIKE,
            self::OPERATOR_TEXT_ENDS_WITH => Condition::OPERATOR_LIKE,
            self::OPERATOR_TEXT_DOESNOT_END_WITH => Condition::OPERATOR_NOT_LIKE,
            self::OPERATOR_IN => Condition::OPERATOR_IN,
            self::OPERATOR_NOT_IN => Condition::OPERATOR_NOT_IN,
            self::OPERATOR_TEXT_MATCHES_REGEX => Condition::OPERATOR_REGEXP,
            self::OPERATOR_TEXT_DOESNOT_MATCH_REGEX => Condition::OPERATOR_NOT_REGEXP,
            self::OPERATOR_EMPTY => Condition::OPERATOR_EQUALS,
            self::OPERATOR_NOT_EMPTY => Condition::OPERATOR_DOESNOT_EQUAL,
        ],
        'select' => [
            self::OPERATOR_EQUALS => Condition::OPERATOR_EQUALS,
            self::OPERATOR_DOESNOT_EQUAL => Condition::OPERATOR_DOESNOT_EQUAL,
        ],
        'lookup' => [
            self::OPERATOR_EQUALS => Condition::OPERATOR_EQUALS,
            self::OPERATOR_DOESNOT_EQUAL => Condition::OPERATOR_DOESNOT_EQUAL,
        ],
    ];

    /** @var array<string, string|array<string, mixed>> Definition of rule types. */
    protected static array $ruleTypes = [
        'default' => 'text',
        'text' => [
            'type' => 'text',
            'operators' => [
                self::OPERATOR_TEXT_EQUALS,
                self::OPERATOR_TEXT_DOESNOT_EQUAL,
                self::OPERATOR_TEXT_GREATER,
                self::OPERATOR_TEXT_GREATER_EQUAL,
                self::OPERATOR_TEXT_LESS,
                self::OPERATOR_TEXT_LESS_EQUAL,
                self::OPERATOR_TEXT_CONTAINS,
                self::OPERATOR_TEXT_DOESNOT_CONTAIN,
                self::OPERATOR_TEXT_BEGINS_WITH,
                self::OPERATOR_TEXT_DOESNOT_BEGIN_WITH,
                self::OPERATOR_TEXT_ENDS_WITH,
                self::OPERATOR_TEXT_DOESNOT_END_WITH,
                self::OPERATOR_TEXT_MATCHES_REGEX,
                self::OPERATOR_TEXT_DOESNOT_MATCH_REGEX,
                self::OPERATOR_IN,
                self::OPERATOR_NOT_IN,
                self::OPERATOR_EMPTY,
                self::OPERATOR_NOT_EMPTY,
            ],
        ],
        'lookup' => [
            'type' => 'custom-component',
            'inputType' => 'lookup',
            'component' => 'atk-lookup',
            'operators' => self::ENUM_OPERATORS,
            'componentProps' => [__CLASS__, 'getLookupProps'],
        ],
        'enum' => [
            'type' => 'select',
            'inputType' => 'select',
            'operators' => self::ENUM_OPERATORS,
            'choices' => [__CLASS__, 'getChoices'],
        ],
        'numeric' => [
            'type' => 'text',
            'inputType' => 'number',
            'operators' => [
                self::OPERATOR_SIGN_EQUALS,
                self::OPERATOR_SIGN_DOESNOT_EQUAL,
                self::OPERATOR_SIGN_GREATER,
                self::OPERATOR_SIGN_GREATER_EQUAL,
                self::OPERATOR_SIGN_LESS,
                self::OPERATOR_SIGN_LESS_EQUAL,
                self::OPERATOR_EMPTY,
                self::OPERATOR_NOT_EMPTY,
            ],
        ],
        'boolean' => [
            'type' => 'radio',
            'operators' => [],
            'choices' => [
                ['label' => 'Yes', 'value' => '1'],
                ['label' => 'No', 'value' => '0'],
            ],
        ],
        'date' => [
            'type' => 'custom-component',
            'component' => 'atk-date-picker',
            'inputType' => 'date',
            'operators' => self::DATE_OPERATORS,
            'componentProps' => [__CLASS__, 'getDatePickerProps'],
        ],
        'datetime' => [
            'type' => 'custom-component',
            'component' => 'atk-date-picker',
            'inputType' => 'datetime',
            'operators' => self::DATE_OPERATORS,
            'componentProps' => [__CLASS__, 'getDatePickerProps'],
        ],
        'time' => [
            'type' => 'custom-component',
            'component' => 'atk-date-picker',
            'inputType' => 'time',
            'operators' => self::DATE_OPERATORS,
            'componentProps' => [__CLASS__, 'getDatePickerProps'],
        ],
        'integer' => 'numeric',
        'float' => 'numeric',
        'atk4_money' => 'numeric',
        'checkbox' => 'boolean',
    ];

    protected function init(): void
    {
        parent::init();

        if (!$this->scopeBuilderTemplate) {
            $this->scopeBuilderTemplate = new HtmlTemplate('<div id="{$_id}" class="ui"><atk-query-builder v-bind="initData"></atk-query-builder></div>');
        }

        $this->scopeBuilderView = View::addTo($this, ['template' => $this->scopeBuilderTemplate]);

        if ($this->form) {
            $this->form->onHook(Form::HOOK_LOAD_POST, function (Form $form, array &$postRawData) {
                $key = $this->entityField->getFieldName();
                $postRawData[$key] = static::queryToScope($this->getApp()->decodeJson($postRawData[$key] ?? '{}'));
            });
        }
    }

    /**
     * Set the model to build scope for.
     */
    public function setModel(Model $model): void
    {
        parent::setModel($model);

        $this->initVueLookupCallback();

        $this->buildQuery($model);
    }

    /**
     * Build query from model scope.
     */
    protected function buildQuery(Model $model): void
    {
        if (!$this->fields) {
            $this->fields = array_keys($model->getFields());
        }

        foreach ($this->fields as $fieldName) {
            $field = $model->getField($fieldName);

            $this->addFieldRule($field);

            $this->addReferenceRules($field);
        }

        // build a ruleId => inputType map
        // this is used when selecting proper operator for the inputType (see self::$operatorsMap)
        $inputsMap = array_column($this->rules, 'inputType', 'id');

        if ($this->entityField && $this->entityField->get() !== null) {
            $scope = $this->entityField->get();
        } else {
            $scope = $model->scope();
        }

        $this->query = static::scopeToQuery($scope, $inputsMap)['query'];
    }

    /**
     * Add the field rules to use in VueQueryBuilder.
     */
    protected function addFieldRule(Field $field): self
    {
        if ($field->enum || $field->values) {
            $type = 'enum';
        } elseif ($field->hasReference()) {
            $type = 'lookup';
        } else {
            $type = $field->type;
        }

        $rule = $this->getRule($type, array_merge([
            'id' => $field->shortName,
            'label' => $field->getCaption(),
            'options' => $this->options[$type] ?? [],
        ], $field->ui['scopebuilder'] ?? []), $field);

        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Set property for atk-lookup component.
     */
    protected function getLookupProps(Field $field): array
    {
        // set any of sui-dropdown props via this property. Will be applied globally.
        $props = $this->atkLookupOptions;
        $items = $this->getFieldItems($field, 10);
        foreach ($items as $value => $text) {
            $props['options'][] = ['key' => $value, 'text' => $text, 'value' => $value];
        }

        if ($field->hasReference()) {
            $props['url'] = $this->dataCb->getUrl();
            $props['reference'] = $field->shortName;
            $props['search'] = true;
        }

        $props['placeholder'] ??= 'Select ' . $field->getCaption();

        return $props;
    }

    /**
     * Set property for atk-date-picker component.
     */
    protected function getDatePickerProps(Field $field): array
    {
        $calendar = new Calendar();
        $props = $this->atkdDateOptions['flatpickr'] ?? [];
        $phpFormat = $this->getApp()->uiPersistence->{$field->type . 'Format'};
        $props['altFormat'] = $calendar->convertPhpDtFormatToFlatpickr($phpFormat); // why altFormat format?
        $props['dateFormat'] = 'Y-m-d';
        $props['altInput'] = true;

        if ($field->type === 'datetime' || $field->type === 'time') {
            $props['enableTime'] = true;
            $props['time_24hr'] = $calendar->isDtFormatWith24hrTime($phpFormat);
            $props['noCalendar'] = $field->type === 'time';
            $props['enableSeconds'] = $calendar->isDtFormatWithSeconds($phpFormat);
            $props['allowInput'] = $calendar->isDtFormatWithMicroseconds($phpFormat);
            $props['dateFormat'] = $field->type === 'datetime' ? 'Y-m-d H:i:S' : 'H:i:S';
        }

        $props['useDefault'] = $this->atkdDateOptions['useDefault'];

        return $props;
    }

    /**
     * Add rules on the referenced model fields.
     */
    protected function addReferenceRules(Field $field): self
    {
        if ($field->hasReference()) {
            $reference = $field->getReference();

            // add the number of records rule
            $this->rules[] = $this->getRule('numeric', [
                'id' => $reference->link . '/#',
                'label' => $field->getCaption() . ' number of records ',
            ]);

            $theirModel = $reference->createTheirModel();

            // add rules on all fields of the referenced model
            foreach ($theirModel->getFields() as $theirField) {
                $theirField->ui['scopebuilder'] = [
                    'id' => $reference->link . '/' . $theirField->shortName,
                    'label' => $field->getCaption() . ' is set to record where ' . $theirField->getCaption(),
                ];

                $this->addFieldRule($theirField);
            }
        }

        return $this;
    }

    protected function getRule(string $type, array $defaults = [], Field $field = null): array
    {
        $rule = static::$ruleTypes[$type] ?? static::$ruleTypes['default'];

        // when $rule is an alias
        if (is_string($rule)) {
            return $this->getRule($rule, $defaults, $field);
        }

        $options = $defaults['options'] ?? [];

        // 'options' is atk specific so not necessary to pass it to VueQueryBuilder
        unset($defaults['options']);

        // when $rule is callable
        if (is_callable($rule)) {
            $rule = call_user_func($rule, $field, $options);
        }

        // map all values for callables and merge with defaults
        return array_merge(array_map(function ($value) use ($field, $options) {
            return is_array($value) && is_callable($value) ? call_user_func($value, $field, $options) : $value;
        }, $rule), $defaults);
    }

    /**
     * Return an array of items id and name for a field.
     * Return field enum, values or reference values.
     */
    protected function getFieldItems(Field $field, ?int $limit = 250): array
    {
        $items = [];
        if ($field->enum) {
            $items = array_slice($field->enum, 0, $limit);
            $items = array_combine($items, $items);
        }
        if ($field->values && is_array($field->values)) {
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
     * Returns the choices array for Select field rule.
     */
    protected function getChoices(Field $field, array $options = []): array
    {
        $choices = $this->getFieldItems($field, $options['limit'] ?? 250);

        $ret = [
            ['label' => '[empty]', 'value' => null],
        ];
        foreach ($choices as $value => $label) {
            $ret[] = ['label' => $label, 'value' => $value];
        }

        return $ret;
    }

    protected function renderView(): void
    {
        parent::renderView();

        $this->scopeBuilderView->vue('atk-query-builder', [
            'data' => [
                'rules' => $this->rules,
                'maxDepth' => $this->maxDepth,
                'query' => $this->query,
                'name' => $this->shortName,
                'labels' => $this->labels !== [] ? $this->labels : null, // TODO do we need to really pass null for empty array?
                'form' => $this->form->formElement->name,
                'debug' => $this->options['debug'] ?? false,
            ],
        ]);
    }

    /**
     * Converts an VueQueryBuilder query array to Condition or Scope.
     */
    public static function queryToScope(array $query): Scope\AbstractScope
    {
        $type = $query['type'] ?? 'query-builder-group';
        $query = $query['query'] ?? $query;

        switch ($type) {
            case 'query-builder-group':
                $components = array_map(fn ($v) => static::queryToScope($v), $query['children']);
                $scope = new Scope($components, $query['logicalOperator']);

                break;
            case 'query-builder-rule':
                $scope = static::queryToCondition($query);

                break;
            default:
                $scope = Scope::createAnd();
        }

        return $scope;
    }

    /**
     * Converts an VueQueryBuilder rule array to Condition or Scope.
     */
    public static function queryToCondition(array $query): Scope\Condition
    {
        $key = $query['rule'] ?? null;
        $operator = (string) ($query['operator'] ?? null);
        $value = $query['value'] ?? null;

        switch ($operator) {
            case self::OPERATOR_EMPTY:
            case self::OPERATOR_NOT_EMPTY:
                $value = null;

                break;
            case self::OPERATOR_TEXT_BEGINS_WITH:
            case self::OPERATOR_TEXT_DOESNOT_BEGIN_WITH:
                $value .= '%';

                break;
            case self::OPERATOR_TEXT_ENDS_WITH:
            case self::OPERATOR_TEXT_DOESNOT_END_WITH:
                $value = '%' . $value;

                break;
            case self::OPERATOR_TEXT_CONTAINS:
            case self::OPERATOR_TEXT_DOESNOT_CONTAIN:
                $value = '%' . $value . '%';

                break;
            case self::OPERATOR_IN:
            case self::OPERATOR_NOT_IN:
                $value = explode(static::detectDelimiter($value), (string) $value);

                break;
        }

        $operatorsMap = array_merge(...array_values(static::$operatorsMap));

        $operator = $operator ? ($operatorsMap[strtolower($operator)] ?? '=') : null;

        return new Scope\Condition($key, $operator, $value);
    }

    /**
     * Converts Scope or Condition to VueQueryBuilder query array.
     */
    public static function scopeToQuery(Scope\AbstractScope $scope, array $inputsMap = []): array
    {
        $query = [];
        if ($scope instanceof Scope\Condition) {
            $query = [
                'type' => 'query-builder-rule',
                'query' => static::conditionToQuery($scope, $inputsMap),
            ];
        }

        if ($scope instanceof Scope) {
            $children = [];
            foreach ($scope->getNestedConditions() as $nestedCondition) {
                $children[] = static::scopeToQuery($nestedCondition, $inputsMap);
            }

            $query = [
                'type' => 'query-builder-group',
                'query' => [
                    'logicalOperator' => $scope->getJunction(),
                    'children' => $children,
                ],
            ];
        }

        return $query;
    }

    /**
     * Converts a Condition to VueQueryBuilder query array.
     */
    public static function conditionToQuery(Scope\Condition $condition, array $inputsMap = []): array
    {
        if (is_string($condition->key)) {
            $rule = $condition->key;
        } elseif ($condition->key instanceof Field) {
            $rule = $condition->key->shortName;
        } else {
            throw new Exception('Unsupported scope key: ' . gettype($condition->key));
        }

        $operator = $condition->operator;
        $value = $condition->value;

        $inputType = $inputsMap[$rule] ?? 'text';

        if (in_array($operator, [Condition::OPERATOR_LIKE, Condition::OPERATOR_NOT_LIKE], true)) {
            // no %
            $match = 0;
            // % at the beginning
            $match += substr($value, 0, 1) === '%' ? 1 : 0;
            // % at the end
            $match += substr($value, -1) === '%' ? 2 : 0;

            $map = [
                Condition::OPERATOR_LIKE => [
                    self::OPERATOR_TEXT_EQUALS,
                    self::OPERATOR_TEXT_BEGINS_WITH,
                    self::OPERATOR_TEXT_ENDS_WITH,
                    self::OPERATOR_TEXT_CONTAINS,
                ],
                Condition::OPERATOR_NOT_LIKE => [
                    self::OPERATOR_TEXT_DOESNOT_EQUAL,
                    self::OPERATOR_TEXT_DOESNOT_BEGIN_WITH,
                    self::OPERATOR_TEXT_DOESNOT_END_WITH,
                    self::OPERATOR_TEXT_DOESNOT_CONTAIN,
                ],
            ];

            $operator = $map[strtoupper($operator)][$match];

            $value = trim($value, '%');
        } else {
            if (is_array($value)) {
                $map = [
                    Condition::OPERATOR_EQUALS => Condition::OPERATOR_IN,
                    Condition::OPERATOR_DOESNOT_EQUAL => Condition::OPERATOR_NOT_IN,
                ];
                $value = implode(', ', $value);
                $operator = $map[$operator] ?? Condition::OPERATOR_NOT_IN;
            }

            $operatorsMap = array_merge(static::$operatorsMap[$inputType] ?? [], static::$operatorsMap['text']);
            $operatorKey = array_search(strtoupper($operator), $operatorsMap, true);
            $operator = $operatorKey !== false ? $operatorKey : self::OPERATOR_EQUALS;
        }

        return [
            'rule' => $rule,
            'operator' => $operator,
            'value' => $value,
            'option' => static::getOption($inputType, $value, $condition),
        ];
    }

    /**
     * Return extra value option associate with certain inputType or null otherwise.
     */
    protected static function getOption(string $type, string $value, Condition $condition): ?array
    {
        $option = null;
        switch ($type) {
            case 'lookup':
                $condField = $condition->getModel()->getField($condition->key);
                $reference = $condField->getReference();
                $model = $reference->refModel($condField->getOwner());
                $fieldName = $reference->getTheirFieldName($model);
                $entity = $model->tryLoadBy($fieldName, $value);
                if ($entity !== null) {
                    $option = [
                        'key' => $value,
                        'text' => $entity->get($model->titleField),
                        'value' => $value,
                    ];
                }

                break;
        }

        return $option;
    }

    /**
     * Auto-detects a string delimiter based on list of predefined values in ScopeBuilder::$listDelimiters in order of priority.
     *
     * @phpstan-return non-empty-string
     */
    public static function detectDelimiter(string $value): string
    {
        $matches = [];
        foreach (static::$listDelimiters as $delimiter) {
            $matches[$delimiter] = substr_count($value, $delimiter);
        }

        $max = array_keys($matches, max($matches), true);

        return $max !== [] ? reset($max) : reset(static::$listDelimiters);
    }
}
