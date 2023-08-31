<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Table;
use Mvorisek\Atk4\Hintable\Data\HintablePropertyDef;

try {
    require_once file_exists(__DIR__ . '/db.php')
        ? __DIR__ . '/db.php'
        : __DIR__ . '/db.default.php';
} catch (\PDOException $e) {
    // do not show $e unless you can secure DSN!
    throw (new Exception('This demo requires access to the database. See "demos/init-db.php"'))
        ->addMoreInfo('PDO error', $e->getMessage());
}

trait ModelPreventModificationTrait
{
    protected function isAllowDbModifications(): bool
    {
        static $rw = null;
        if ($rw === null) {
            $rw = file_exists(__DIR__ . '/db-behat-rw.txt');
        }

        return $rw;
    }

    public function atomic(\Closure $fx)
    {
        $eRollback = new \Exception('Prevent modification');
        $res = null;
        try {
            parent::atomic(function () use ($fx, $eRollback, &$res) {
                $res = $fx();

                if (!$this->isAllowDbModifications()) {
                    throw $eRollback;
                }
            });
        } catch (\Exception $e) {
            if ($e !== $eRollback) {
                throw $e;
            }
        }

        return $res;
    }

    /**
     * @param \Closure(Model): string $outputCallback
     */
    protected function wrapUserActionCallbackPreventModification(Model\UserAction $action, \Closure $outputCallback): void
    {
        $originalCallback = $action->callback;
        $action->callback = function (Model $model, ...$args) use ($action, $originalCallback, $outputCallback) {
            if ($model->isEntity()) {
                $action = $action->getActionForEntity($model);
                $loadedEntity = clone $model;
            }

            $callbackBackup = $action->callback;
            try {
                $action->callback = $originalCallback;
                $res = $action->execute(...$args);

                if ($this->isAllowDbModifications()) {
                    return $res;
                }
            } finally {
                $action->callback = $callbackBackup;
            }

            return $outputCallback($model->isEntity() && !$model->isLoaded() ? $loadedEntity : $model, ...$args);
        };
    }

    protected function initPreventModification(): void
    {
        $makeMessageFx = static function (string $actionName, Model $model) {
            return $model->getModelCaption() . ' action "' . $actionName . '" with "' . $model->getTitle() . '" entity '
                . ' was executed. In demo mode all changes are reversed.';
        };

        $this->wrapUserActionCallbackPreventModification($this->getUserAction('add'), static function (Model $model) use ($makeMessageFx) {
            return $makeMessageFx('add', $model);
        });

        $this->wrapUserActionCallbackPreventModification($this->getUserAction('edit'), static function (Model $model) use ($makeMessageFx) {
            return $makeMessageFx('edit', $model);
        });

        $this->getUserAction('delete')->confirmation = 'Please go ahead. Demo mode does not really delete data.';
        $this->wrapUserActionCallbackPreventModification($this->getUserAction('delete'), static function (Model $model) use ($makeMessageFx) {
            return $makeMessageFx('delete', $model);
        });
    }
}

class ModelWithPrefixedFields extends Model
{
    use ModelPreventModificationTrait;

    /** @var array<string, string> */
    private static $prefixedFieldNames = [];

    private function prefixFieldName(string $fieldName, bool $forActualName = false): string
    {
        $tableShort = $this->table;
        if (strlen($tableShort) > 16) {
            $tableShort = substr(md5($tableShort), 0, 16);
        }

        if ($forActualName) {
            $fieldName = $this->unprefixFieldName($fieldName);
        }

        $fieldShort = $fieldName;
        $fieldWithIdSuffix = false;
        if (str_ends_with($fieldShort, '_id')) {
            $fieldShort = substr($fieldShort, 0, -strlen('_id'));
            $fieldWithIdSuffix = true;
        }
        if (strlen($fieldShort) > 16) {
            $fieldShort = substr(md5($fieldShort), 0, 16);
        }
        if ($fieldWithIdSuffix) {
            $fieldShort .= '_id';
        }

        $res = 'atk_' . ($forActualName ? 'a' : '') . 'fp_' . $tableShort . '__' . $fieldShort;

        self::$prefixedFieldNames[$res] = $fieldName;

        return $res;
    }

    private function unprefixFieldName(string $name): string
    {
        if (!str_starts_with($name, 'atk_fp_')) {
            return $name;
        }

        if (isset(self::$prefixedFieldNames[$name . '_id'])) {
            return self::$prefixedFieldNames[$name . '_id'];
        }

        return self::$prefixedFieldNames[$name];
    }

    protected function createHintablePropsFromClassDoc(string $className): array
    {
        return array_map(function (HintablePropertyDef $hintableProp) {
            $hintableProp->fieldName = $this->prefixFieldName($hintableProp->name);

            return $hintableProp;
        }, parent::createHintablePropsFromClassDoc($className));
    }

    protected function init(): void
    {
        if ($this->idField === 'id') {
            $this->idField = $this->prefixFieldName($this->idField);
        }

        if ($this->titleField === 'name') {
            $this->titleField = $this->prefixFieldName($this->titleField);
        }

        parent::init();

        $this->initPreventModification();
    }

    public function addField(string $name, $seed = []): Field
    {
        $seed = Factory::mergeSeeds($seed, [
            'actual' => $this->prefixFieldName($name, true),
            'caption' => $this->readableCaption($this->unprefixFieldName($name)),
        ]);

        return parent::addField($name, $seed);
    }
}

/**
 * @property string $name      @Atk4\Field()
 * @property string $sys_name  @Atk4\Field()
 * @property string $iso       @Atk4\Field()
 * @property string $iso3      @Atk4\Field()
 * @property string $numcode   @Atk4\Field()
 * @property string $phonecode @Atk4\Field()
 */
class Country extends ModelWithPrefixedFields
{
    public $table = 'country';
    public $caption = 'Country';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->name, ['actual' => 'atk_afp_country__nicename', 'required' => true, 'type' => 'string']);
        $this->addField($this->fieldName()->sys_name, ['actual' => 'atk_afp_country__name', 'system' => true]);

        $this->addField($this->fieldName()->iso, ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui' => ['table' => ['sortable' => false]]]);
        $this->addField($this->fieldName()->iso3, ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField($this->fieldName()->numcode, ['caption' => 'ISO Numeric Code', 'type' => 'integer', 'required' => true]);
        $this->addField($this->fieldName()->phonecode, ['caption' => 'Phone Prefix', 'type' => 'integer', 'required' => true]);

        $this->onHook(Model::HOOK_BEFORE_SAVE, static function (self $model) {
            if (!$model->sys_name) {
                $model->sys_name = mb_strtoupper($model->name);
            }
        });
    }

    public function validate(string $intent = null): array
    {
        $errors = parent::validate($intent);

        if (mb_strlen($this->iso ?? '') !== 2) {
            $errors[$this->fieldName()->iso] = 'Must be exactly 2 characters';
        }

        if (mb_strlen($this->iso3 ?? '') !== 3) {
            $errors[$this->fieldName()->iso3] = 'Must be exactly 3 characters';
        }

        // look if name is unique
        $c = $this->getModel()->tryLoadBy($this->fieldName()->name, $this->name);
        if ($c !== null && !$this->compare($this->idField, $c->getId())) {
            $errors[$this->fieldName()->name] = 'Country name must be unique';
        }

        return $errors;
    }
}

/**
 * @property string    $project_name           @Atk4\Field()
 * @property string    $project_code           @Atk4\Field()
 * @property string    $description            @Atk4\Field()
 * @property string    $client_name            @Atk4\Field()
 * @property string    $client_address         @Atk4\Field()
 * @property Country   $client_country_iso     @Atk4\RefOne()
 * @property string    $client_country         @Atk4\Field()
 * @property bool      $is_commercial          @Atk4\Field()
 * @property string    $currency               @Atk4\Field()
 * @property string    $currency_symbol        @Atk4\Field()
 * @property float     $project_budget         @Atk4\Field()
 * @property float     $project_invoiced       @Atk4\Field()
 * @property float     $project_paid           @Atk4\Field()
 * @property float     $project_hour_cost      @Atk4\Field()
 * @property int       $project_hours_est      @Atk4\Field()
 * @property int       $project_hours_reported @Atk4\Field()
 * @property float     $project_expenses_est   @Atk4\Field()
 * @property float     $project_expenses       @Atk4\Field()
 * @property float     $project_mgmt_cost_pct  @Atk4\Field()
 * @property float     $project_qa_cost_pct    @Atk4\Field()
 * @property \DateTime $start_date             @Atk4\Field()
 * @property \DateTime $finish_date            @Atk4\Field()
 * @property \DateTime $finish_time            @Atk4\Field()
 * @property \DateTime $created                @Atk4\Field()
 * @property \DateTime $updated                @Atk4\Field()
 */
class Stat extends ModelWithPrefixedFields
{
    public $table = 'stat';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->project_name, ['type' => 'string']);
        $this->addField($this->fieldName()->project_code, ['type' => 'string']);
        $this->titleField = $this->fieldName()->project_name;
        $this->addField($this->fieldName()->description, ['type' => 'text']);
        $this->addField($this->fieldName()->client_name, ['type' => 'string']);
        $this->addField($this->fieldName()->client_address, ['type' => 'text', 'ui' => ['form' => [Form\Control\Textarea::class, 'rows' => 4]]]);

        $this->hasOne($this->fieldName()->client_country_iso, [
            'model' => [Country::class],
            'theirField' => Country::hinting()->fieldName()->iso,
            'type' => 'string',
            'ui' => [
                'form' => [Form\Control\Line::class],
                'table' => [Table\Column\CountryFlag::class],
            ],
        ])
            ->addField($this->fieldName()->client_country, Country::hinting()->fieldName()->name);

        $this->addField($this->fieldName()->is_commercial, ['type' => 'boolean']);
        $this->addField($this->fieldName()->currency, ['values' => ['EUR' => 'Euro', 'USD' => 'US Dollar', 'GBP' => 'Pound Sterling']]);
        $this->addField($this->fieldName()->currency_symbol, ['neverPersist' => true]);
        $this->onHook(Model::HOOK_AFTER_LOAD, static function (self $model) {
            $map = ['EUR' => '€', 'USD' => '$', 'GBP' => '£'];
            $model->currency_symbol = $map[$model->currency] ?? '?';
        });

        $this->addField($this->fieldName()->project_budget, ['type' => 'atk4_money']);
        $this->addField($this->fieldName()->project_invoiced, ['type' => 'atk4_money']);
        $this->addField($this->fieldName()->project_paid, ['type' => 'atk4_money']);
        $this->addField($this->fieldName()->project_hour_cost, ['type' => 'atk4_money']);

        $this->addField($this->fieldName()->project_hours_est, ['type' => 'integer']);
        $this->addField($this->fieldName()->project_hours_reported, ['type' => 'integer']);

        $this->addField($this->fieldName()->project_expenses_est, ['type' => 'atk4_money']);
        $this->addField($this->fieldName()->project_expenses, ['type' => 'atk4_money']);
        $this->addField($this->fieldName()->project_mgmt_cost_pct, new Percent());
        $this->addField($this->fieldName()->project_qa_cost_pct, new Percent());

        $this->addField($this->fieldName()->start_date, ['type' => 'date']);
        $this->addField($this->fieldName()->finish_date, ['type' => 'date']);
        $this->addField($this->fieldName()->finish_time, ['type' => 'time']);

        $this->addField($this->fieldName()->created, ['type' => 'datetime', 'ui' => ['form' => ['disabled' => true]]]);
        $this->addField($this->fieldName()->updated, ['type' => 'datetime', 'ui' => ['form' => ['disabled' => true]]]);
    }
}

class Percent extends Field
{
    public string $type = 'float';
}

/**
 * @property string $name             @Atk4\Field()
 * @property string $type             @Atk4\Field()
 * @property bool   $is_folder        @Atk4\Field()
 * @property File   $SubFolder        @Atk4\RefMany()
 * @property int    $count            @Atk4\Field()
 * @property Folder $parent_folder_id @Atk4\RefOne()
 */
class File extends ModelWithPrefixedFields
{
    public $table = 'file';
    public $caption = 'File';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->name);

        $this->addField($this->fieldName()->type, ['caption' => 'MIME Type']);
        $this->addField($this->fieldName()->is_folder, ['type' => 'boolean']);

        $this->hasMany($this->fieldName()->SubFolder, [
            'model' => [self::class],
            'theirField' => self::hinting()->fieldName()->parent_folder_id,
        ])
            ->addField($this->fieldName()->count, ['aggregate' => 'count', 'field' => $this->getPersistence()->expr($this, '*')]);

        $this->hasOne($this->fieldName()->parent_folder_id, [
            'model' => [Folder::class],
        ])
            ->addTitle();
    }

    public function importFromFilesystem(string $path, bool $isSub = null): void
    {
        if ($isSub === null) {
            if ($this->isEntity()) { // TODO should be not needed once UserAction is for non-entity only
                $this->getModel()->importFromFilesystem($path);

                return;
            }

            $this->atomic(function () use ($path) {
                foreach ($this as $entity) {
                    $entity->delete();
                }

                $path = __DIR__ . '/../' . $path;

                $this->importFromFilesystem($path, false);
            });

            return;
        }

        foreach (new \DirectoryIterator($path) as $fileinfo) {
            if ($fileinfo->isDot() || in_array($fileinfo->getFilename(), ['.git', 'vendor', 'node_modules', 'external'], true)) {
                continue;
            }

            $entity = $this->createEntity();

            $entity->save([
                $this->fieldName()->name => $fileinfo->getFilename(),
                $this->fieldName()->is_folder => $fileinfo->isDir(),
                $this->fieldName()->type => pathinfo($fileinfo->getFilename(), \PATHINFO_EXTENSION),
            ]);

            if ($fileinfo->isDir()) {
                $entity->SubFolder->importFromFilesystem($fileinfo->getPath() . '/' . $fileinfo->getFilename(), true);
            }

            // skip full/slow import for Behat CI testing
            if ($_ENV['CI'] ?? null) {
                break;
            }
        }
    }
}

class Folder extends File
{
    protected function init(): void
    {
        parent::init();

        $this->addCondition($this->fieldName()->is_folder, true);
    }
}

/**
 * @property string      $name          @Atk4\Field()
 * @property SubCategory $SubCategories @Atk4\RefMany()
 * @property Product     $Products      @Atk4\RefMany()
 */
class Category extends ModelWithPrefixedFields
{
    public $table = 'product_category';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->name);

        $this->hasMany($this->fieldName()->SubCategories, [
            'model' => [SubCategory::class],
            'theirField' => SubCategory::hinting()->fieldName()->product_category_id,
        ]);
        $this->hasMany($this->fieldName()->Products, [
            'model' => [Product::class],
            'theirField' => Product::hinting()->fieldName()->product_category_id,
        ]);
    }
}

/**
 * @property string   $name                @Atk4\Field()
 * @property Category $product_category_id @Atk4\RefOne()
 * @property Product  $Products            @Atk4\RefMany()
 */
class SubCategory extends ModelWithPrefixedFields
{
    public $table = 'product_sub_category';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->name);

        $this->hasOne($this->fieldName()->product_category_id, [
            'model' => [Category::class],
        ]);
        $this->hasMany($this->fieldName()->Products, [
            'model' => [Product::class],
            'theirField' => Product::hinting()->fieldName()->product_sub_category_id,
        ]);
    }
}

/**
 * @property string      $name                    @Atk4\Field()
 * @property string      $brand                   @Atk4\Field()
 * @property Category    $product_category_id     @Atk4\RefOne()
 * @property SubCategory $product_sub_category_id @Atk4\RefOne()
 */
class Product extends ModelWithPrefixedFields
{
    public $table = 'product';
    public $caption = 'Product';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->name);
        $this->addField($this->fieldName()->brand);
        $this->hasOne($this->fieldName()->product_category_id, [
            'model' => [Category::class],
        ])->addTitle();
        $this->hasOne($this->fieldName()->product_sub_category_id, [
            'model' => [SubCategory::class],
        ])->addTitle();
    }
}

/**
 * @property string    $item       @Atk4\Field()
 * @property \DateTime $inv_date   @Atk4\Field()
 * @property \DateTime $inv_time   @Atk4\Field()
 * @property Country   $country_id @Atk4\RefOne()
 * @property int       $qty        @Atk4\Field()
 * @property int       $box        @Atk4\Field()
 * @property int       $total_sql  @Atk4\Field()
 * @property int       $total_php  @Atk4\Field()
 */
class MultilineItem extends ModelWithPrefixedFields
{
    public $table = 'multiline_item';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->item, ['required' => true]);
        $this->addField($this->fieldName()->inv_date, ['type' => 'date']);
        $this->addField($this->fieldName()->inv_time, ['type' => 'time']);
        $this->hasOne($this->fieldName()->country_id, [
            'model' => [Country::class],
        ]);
        $this->addField($this->fieldName()->qty, ['type' => 'integer', 'required' => true]);
        $this->addField($this->fieldName()->box, ['type' => 'integer', 'required' => true]);
        $this->addExpression($this->fieldName()->total_sql, [
            'expr' => function (Model /* TODO self is not working because of clone in Multiline */ $row) {
                return $row->expr('{' . $this->fieldName()->qty . '} * {' . $this->fieldName()->box . '}'); // @phpstan-ignore-line
            },
            'type' => 'integer',
        ]);
        $this->addCalculatedField($this->fieldName()->total_php, [
            'expr' => static function (self $row) {
                return $row->qty * $row->box;
            },
            'type' => 'integer',
        ]);
    }
}

/**
 * @property string        $name    @Atk4\Field()
 * @property Country       $country @Atk4\RefOne()
 * @property MultilineItem $items   @Atk4\RefMany()
 */
class MultilineDelivery extends ModelWithPrefixedFields
{
    public $table = 'multiline_delivery';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->name, ['required' => true]);
        $this->containsOne($this->fieldName()->country, ['model' => [Country::class]]);
        $this->containsMany($this->fieldName()->items, ['model' => [MultilineItem::class]]);
    }
}
