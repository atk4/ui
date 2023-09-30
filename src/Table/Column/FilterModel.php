<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\NameTrait;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Type\Types as CustomTypes;
use Atk4\Ui\App;
use Atk4\Ui\SessionTrait;
use Doctrine\DBAL\Types\Types;

/**
 * Implement a generic filter model for filtering column data.
 */
abstract class FilterModel extends Model
{
    use AppScopeTrait; // needed for SessionTrait
    use NameTrait; // needed for SessionTrait
    use SessionTrait;

    /** @var Field The operator for defining a condition on a field. */
    public $op;

    /** @var Field The value for defining a condition on a field. */
    public $value;

    /** @var bool Determines if this field shouldn't have a value field, and use only op field. */
    public $noValueField = false;

    /** The field where this filter need to query data. */
    public Field $lookupField;

    public function __construct(App $app, array $defaults = [])
    {
        $this->setApp($app);

        $persistence = new Persistence\Array_();

        parent::__construct($persistence, $defaults);
    }

    /**
     * Factory method that will return a FilterModel Type class.
     */
    public static function factoryType(App $app, Field $field): self
    {
        $class = [
            Types::STRING => FilterModel\TypeString::class,
            Types::TEXT => FilterModel\TypeString::class,

            Types::BOOLEAN => FilterModel\TypeBoolean::class,
            Types::INTEGER => FilterModel\TypeNumber::class,
            Types::FLOAT => FilterModel\TypeNumber::class,
            CustomTypes::MONEY => FilterModel\TypeNumber::class,

            Types::DATE_MUTABLE => FilterModel\TypeDate::class,
            Types::DATE_IMMUTABLE => FilterModel\TypeDate::class,
            Types::TIME_MUTABLE => FilterModel\TypeTime::class,
            Types::TIME_IMMUTABLE => FilterModel\TypeTime::class,
            Types::DATETIME_MUTABLE => FilterModel\TypeDatetime::class,
            Types::DATETIME_IMMUTABLE => FilterModel\TypeDatetime::class,

            Types::JSON => FilterModel\TypeString::class,

            'TODO we do not support enum type, any type can be enum' => FilterModel\TypeEnum::class,
        ][$field->type];

        // you can set your own filter model class
        if (isset($field->ui['filterModel'])) {
            if ($field->ui['filterModel'] instanceof self) {
                return $field->ui['filterModel'];
            }
            $class = $field->ui['filterModel'];
        }

        $filterModel = new $class($app, ['lookupField' => $field]);

        return $filterModel;
    }

    protected function init(): void
    {
        parent::init();

        $this->op = $this->addField('op', ['ui' => ['caption' => '']]);

        if (!$this->noValueField) {
            $this->value = $this->addField('value', ['ui' => ['caption' => '']]);
        }

        $this->afterInit();
    }

    public function afterInit(): void
    {
        $this->addField('name', ['default' => $this->lookupField->shortName, 'system' => true]);

        // create a name for our filter model to save as session data
        $this->name = 'filter_model_' . $this->lookupField->shortName;

        if ($this->getApp()->tryGetRequestQueryParam('atk_clear_filter') ?? false) {
            $this->forget();
        }

        // add hook in order to persist data in session
        $this->onHook(self::HOOK_AFTER_SAVE, function (Model $model) {
            $this->memorize('data', $model->get());
        });
    }

    public function recallData(): ?array
    {
        return $this->recall('data');
    }

    /**
     * Method that will set conditions on a model base on $op and $value value.
     * Each FilterModel\TypeModel should override this method.
     *
     * @return Model
     */
    abstract public function setConditionForModel(Model $model);

    /**
     * Method that will set Field display condition in a form.
     * If form filter need to have a field display at certain condition, then
     * override this method in your FilterModel\TypeModel.
     */
    public function getFormDisplayRules(): array
    {
        return [];
    }

    public function clearData(): void
    {
        $this->forget();
    }
}
