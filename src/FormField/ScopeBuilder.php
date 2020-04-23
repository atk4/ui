<?php

namespace atk4\ui\FormField;

use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\Template;
use atk4\data\Field;
use atk4\data\Model\Scope\Condition;
use atk4\data\Model\Scope\Scope;
use atk4\data\Model\Scope\AbstractScope;

class ScopeBuilder extends Generic
{
    /**
     * Field type specific options.
     *
     * @var array
     */
    public $options = [
        'enum' => [
            'limit' => 250
        ]
    ];
    /**
     * Max depth of nested conditions allowed.
     * Corresponds to VueQueryBulder maxDepth
     *
     * @var integer
     */
    public $maxDepth = 10;

    /**
     * Fields to use for creating the rules
     *
     * @var array
     */
    public $fields = [];

    /**
     * The template needed for the scopebuilder view.
     *
     * @var Template
     */
    public $scopeBuilderTemplate;

    /**
     * The scopebuilder View. Assigned in init().
     *
     * @var \atk4\ui\View
     */
    protected $scopeBuilderView;

    /**
     * Definition of VueQueryBuilder rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Default VueQueryBuilder query.
     *
     * @var array
     */
    protected $query = [];

    /**
     * VueQueryBulder => Condition map of operators
     *
     * @var array
     */
    protected static $operators = [
        'equals' => '=',
        'does not equal' => '!=',
        'is greater than' => '>',
        'is greater or equal to' => '>=',
        'is less than' => '<',
        'is less or equal to' => '<=',
        'contains' => 'LIKE',
        'does not contain' => 'NOT LIKE',
        'begins with' => 'LIKE',
        'does not begin with' => 'NOT LIKE',
        'ends with' => 'LIKE',
        'does not end with' => 'NOT LIKE',
        'is in' => 'IN',
        'is not in' => 'NOT IN',
        'matches regular expression' => 'REGEXP',
        'does not match regular expression' => 'NOT REGEXP',
        'is empty' => '=',
        'is not empty' => '!=',
    ];

    /**
     * Definition of rule types.
     *
     * @var array
     */
    protected static $ruleTypes = [
        'default' => 'text',
        'text' => [
            'type' => 'text',
            'operators' => [
                'equals',
                'does not equal',
                'is greater than',
                'is greater or equal to',
                'is less than',
                'is less or equal to',
                'contains',
                'does not contain',
                'begins with',
                'does not begin with',
                'ends with',
                'does not end with',
                'is in',
                'is not in',
                'matches regular expression',
                'does not match regular expression',
            ]
        ],
        'enum' => [
            'type' => 'select',
            'operators' => [
                'equals',
                'does not equal',
            ],
            'choices' => [__CLASS__, 'getChoices']
        ],
        'numeric' => [
            'type' => 'text',
            'operators' => [
                'equals',
                'does not equal',
                'is greater than',
                'is greater or equal to',
                'is less than',
                'is less or equal to',
            ]
        ],
        'boolean' => [
            'type' => 'radio',
            'operators' => [],
            'choices' => [
                ['label' => 'Yes', 'value' => 1],
                ['label' => 'No', 'value' => 0],
            ]
        ],
        'date' => [
            'type' => 'text',
            'inputType' => 'date',
            'operators' => [
                'equals',
                'does not equal',
                'is greater than',
                'is greater or equal to',
                'is less than',
                'is less or equal to',
                'is empty' ,
                'is not empty',
            ]
        ],
        'datetime' => 'date',
        'integer' => 'numeric',
        'float' => 'numeric',
        'checkbox' => 'boolean',
    ];

    public function init(): void
    {
        parent::init();

        if (!$this->scopeBuilderTemplate) {
            $this->scopeBuilderTemplate = new Template('<div id="{$_id}" class="ui"><vue-query-builder v-bind="initData" v-model="initData.query"></vue-query-builder><div class="ui hidden divider"></div>{$Input}</div>');
        }

        $this->scopeBuilderView = \atk4\ui\View::addTo($this, ['template' => $this->scopeBuilderTemplate]);

        if ($this->form) {
            $this->form->onHook('loadPOST', function ($form, &$post) {
                $key = $this->field->short_name;

                $post[$key] = $this->queryToScope(json_decode($post[$key], true));
            });
        }
    }

    /**
     * Input field collecting output of builder.
     *
     * @throws \atk4\core\Exception
     *
     * @return string
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'         => $this->short_name,
            'type'         => 'hidden',
            ':value'       => 'JSON.stringify(this.initData.query, null, 2)',
            'readonly'     => true,
        ]);
    }

    /**
     * Set the model to build scope for.
     *
     * @param Model $model
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return Model
     */
    public function setModel(Model $model)
    {
        $model = parent::setModel($model);

        $this->fields = $this->fields ?: array_keys($model->getFields());

        foreach ($this->fields as $fieldName) {
            $field = $model->getField($fieldName);

            $this->addFieldRule($field);

            $this->addReferenceRules($field);
        }

        $this->query = $this->scopeToQuery($model->scope())['query'] ?? [];

        return $model;
    }

    /**
     * Add the field rules to use in VueQueryBuilder.
     *
     * @param Field $field
     *
     * @return self
     */
    protected function addFieldRule(Field $field): self
    {
        $type = ($field->enum || $field->values || $field->reference) ? 'enum' : $field->type;

        $this->rules[] = self::getRule($type, array_merge([
            'id'        => $field->short_name,
            'label'     => $field->getCaption(),
            'options'   => $this->options[strtolower($type)] ?? [],
        ], $field->ui['scopebuilder'] ?? []), $field);

        return $this;
    }

    /**
     * Add rules on the referenced model fields.
     *
     * @param Field $field
     *
     * @return self
     */
    protected function addReferenceRules(Field $field): self
    {
        if ($reference = $field->reference) {
            // add the number of records rule
            $this->rules[] = self::getRule('numeric', [
                'id'        => $reference->link . '/#',
                'label'     => $field->getCaption() . ' number of records ',
            ]);

            $refModel = $reference->getModel();

            // add rules on all fields of the referenced model
            foreach ($refModel->getFields() as $refField) {
                $refField->ui['scopebuilder'] = [
                    'id' => $reference->link . '/' . $refField->short_name,
                    'label' => $field->getCaption() . ' is set to record where ' . $refField->getCaption()
                ];

                $this->addFieldRule($refField);
            }
        }

        return $this;
    }

    protected static function getRule($type, array $defaults = [], Field $field = null): array
    {
        $rule = self::$ruleTypes[strtolower($type)] ?? self::$ruleTypes['default'];

        // when $rule is an alias
        if (is_string($rule)) {
            return self::getRule($rule, $defaults, $field);
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
     * Returns the choises array for the field rule.
     *
     * @param Field $field
     *
     * @return array
     */
    protected static function getChoices(Field $field, $options = []): array
    {
        $choices= [];
        if ($field->enum) {
            $choices= array_combine($field->enum, $field->enum);
        }
        if ($field->values && is_array($field->values)) {
            $choices = $field->values;
        } elseif ($field->reference) {
            $model = $field->reference->refModel();

            if ($limit = $options['limit'] ?? false) {
                $model->setLimit($limit);
            }

            foreach ($model as $item) {
                $choices[$item[$model->id_field]] = $item[$model->title_field];
            }
        }

        $ret = [['label' => '[empty]', 'value' => null]];
        foreach ($choices as $value => $label) {
            $ret[] = compact('label', 'value');
        }

        return $ret;
    }

    public function renderView()
    {
        $this->app->addStyle('
            .vue-query-builder select,input {
                width: auto !important;
            }
        ');

        $this->scopeBuilderView->template->trySetHTML('Input', $this->getInput());

        parent::renderView();

        $this->scopeBuilderView->vue(
            'vue-query-builder',
            [
                'rules' => $this->rules,
                'maxDepth' => $this->maxDepth,
                'query' => $this->query,
            ],
            'VueQueryBuilder'
        );
    }

    /**
     * Converts an VueQueryBuilder query array to Condition or Scope
     *
     * @param array $query
     *
     * @return AbstractScope
     */
    public static function queryToScope(array $query): AbstractScope
    {
        $type = $query['type'] ?? 'query-builder-group';
        $query = $query['query'] ?? $query;

        switch ($type) {
            case 'query-builder-group':
                $components = array_map([static::class, 'queryToScope'], $query['children']);
                $junction = $query['logicalOperator'] == 'all' ? Scope::AND : Scope::OR;

                $scope = Scope::create($components, $junction);

                break;

            case 'query-builder-rule':
                $scope = self::queryToCondition($query);

                break;

            default:
                $scope = Scope::create();
            break;
        }

        return $scope;
    }

    /**
     * Converts an VueQueryBuilder rule array to Condition or Scope
     *
     * @param array $query
     *
     * @return Condition
     */
    public static function queryToCondition(array $query): Condition
    {
        $key = $query['rule'] ?? null;
        $operator = $query['operator'] ?? null;
        $value = $query['value'] ?? null;

        switch ($operator) {
            case 'is empty':
            case 'is not empty':
                $value = null;
            break;

            case 'begins with':
            case 'does not begin with':
                $value = $value . '%';
            break;

            case 'ends with':
            case 'does not end with':
                $value = '%' . $value;
            break;

            case 'contains':
            case 'does not contain':
                $value = '%' . $value . '%';
            break;

            case 'is in':
            case 'is not in':
                $value = explode(',', $value);
                break;
            default:

            break;
        }

        $operator = $operator ? (self::$operators[strtolower($operator)] ?? '=') : null;

        return Condition::create($key, $operator, $value);
    }

    /**
     * Converts Scope or Condition to VueQueryBuilder query array.
     *
     * @param AbstractScope $scope
     *
     * @return array
     */
    public static function scopeToQuery(AbstractScope $scope): array
    {
        $query = [];
        switch (get_class($scope)) {
            case Condition::class:
                $query = [
                    'type' => 'query-builder-rule',
                    'query' => self::conditionToQuery($scope)
                ];

            break;

            case Scope::class:
                $children = [];
                foreach ($scope->getActiveComponents() as $component) {
                    $children[] = self::scopeToQuery($component);
                }

                $query = $children ? [
                    'type' => 'query-builder-group',
                    'query' => [
                        'logicalOperator' => $scope->all() ? 'all' : 'any',
                        'children' => $children
                    ]
                ] : [];
            break;
        }

        return $query;
    }

    /**
     * Converts a Condition to VueQueryBuilder query array.
     *
     * @param Condition $condition
     *
     * @throws Exception
     *
     * @return array
     */
    public static function conditionToQuery(Condition $condition): array
    {
        if (is_string($condition->key)) {
            $rule = $condition->key;
        } elseif ($condition->key instanceof Field) {
            $rule = $condition->key->short_name;
        } else {
            throw new Exception('Unsupported scope key: ' . gettype($condition->key));
        }

        $operator = $condition->operator;
        $value = $condition->value;

        if (stripos($operator, 'like') !== false) {
            // no %
            $match = 0;
            // % at the beginning
            $match += substr($value, 0, 1) == '%' ? 1 : 0;
            // % at the end
            $match += substr($value, -1) == '%' ? 2 : 0;

            $map = [
                'LIKE' => [
                    'equals',
                    'begins with',
                    'ends with',
                    'contains'
                ],
                'NOT LIKE' => [
                    'does not equal',
                    'does not begin with',
                    'does not end with',
                    'does not contain'
                ]
            ];

            $operator = $map[strtoupper($operator)][$match];

            $value = trim($value, '%');
        } else {
            if (is_array($value)) {
                $map = [
                    '=' => 'IN',
                    '!=' => 'NOT IN'
                ];
                $value = implode(',', $value);
                $operator = $map[$operator] ?? 'IN';
            }
            $operator = array_search(strtoupper($operator), self::$operators) ?: 'equals';
        }

        return compact('rule', 'operator', 'value');
    }
}
