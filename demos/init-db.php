<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Form;
use Mvorisek\Atk4\Hintable\Data\HintablePropertyDef;

try {
    require_once file_exists(__DIR__ . '/db.php')
        ? __DIR__ . '/db.php'
        : __DIR__ . '/db.default.php';
} catch (\PDOException $e) {
    // do not show $e unless you can secure DSN!
    throw (new \Atk4\Ui\Exception('This demo requires access to the database. See "demos/init-db.php"'))
        ->addMoreInfo('PDO error', $e->getMessage());
}

// a very basic file that sets up Agile Data to be used in some demonstrations

class ModelWithPrefixedFields extends Model
{
    /** @var array<string, string> */
    private static $prefixedFieldNames = [];

    private function prefixFieldName(string $fieldName, bool $forActualName = false): string
    {
        $tableShort = $this->table;
        if (strlen($tableShort) > 8) {
            $tableShort = substr(md5($tableShort), 0, 8);
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
        if (strlen($fieldShort) > 8) {
            $fieldShort = substr(md5($fieldShort), 0, 8);
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
        if ($this->id_field === 'id') {
            $this->id_field = $this->prefixFieldName($this->id_field);
        }

        if ($this->title_field === 'name') {
            $this->title_field = $this->prefixFieldName($this->title_field);
        }

        parent::init();
    }

    public function addField($name, $seed = []): \Atk4\Data\Field
    {
        $seed = \Atk4\Core\Factory::mergeSeeds($seed, [
            'actual' => $this->prefixFieldName($name, true),
            'caption' => $this->readableCaption($this->unprefixFieldName($name)),
        ]);

        return parent::addField($name, $seed);
    }
}

trait ModelLockTrait
{
    public function lock(): void
    {
        $this->getUserAction('add')->callback = function (Model $model) {
            return 'Form Submit! Data are not save in demo mode.';
        };
        $this->getUserAction('edit')->callback = function (Model $model) {
            return 'Form Submit! Data are not save in demo mode.';
        };

        $delete = $this->getUserAction('delete');
        $delete->confirmation = 'Please go ahead. Demo mode does not really delete data.';

        $delete->callback = function (Model $model) {
            return 'Only simulating delete when in demo mode.';
        };
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

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name, ['actual' => 'atk_afp_country__nicename', 'required' => true, 'type' => 'string']);
        $this->addField($this->fieldName()->sys_name, ['actual' => 'atk_afp_country__name', 'system' => true]);

        $this->addField($this->fieldName()->iso, ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui' => ['table' => ['sortable' => false]]]);
        $this->addField($this->fieldName()->iso3, ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField($this->fieldName()->numcode, ['caption' => 'ISO Numeric Code', 'type' => 'integer', 'required' => true]);
        $this->addField($this->fieldName()->phonecode, ['caption' => 'Phone Prefix', 'type' => 'integer', 'required' => true]);

        $this->onHook(Model::HOOK_BEFORE_SAVE, function (self $model) {
            if (!$model->sys_name) {
                $model->sys_name = mb_strtoupper($model->name);
            }
        });
    }

    public function validate($intent = null): array
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
        if ($c->isLoaded() && $c->getId() !== $this->getId()) {
            $errors[$this->fieldName()->name] = 'Country name must be unique';
        }

        return $errors;
    }
}

class CountryLock extends Country
{
    use ModelLockTrait;
    public $caption = 'Country';

    protected function init(): void
    {
        parent::init();
        $this->lock();
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
    public $title = 'Project Stat';

    protected function init(): void
    {
        parent::init();

        $this->addField($this->fieldName()->project_name, ['type' => 'string']);
        $this->addField($this->fieldName()->project_code, ['type' => 'string']);
        $this->title_field = $this->fieldName()->project_name;
        $this->addField($this->fieldName()->description, ['type' => 'text']);
        $this->addField($this->fieldName()->client_name, ['type' => 'string']);
        $this->addField($this->fieldName()->client_address, ['type' => 'text', 'ui' => ['form' => [Form\Control\Textarea::class, 'rows' => 4]]]);

        $this->hasOne($this->fieldName()->client_country_iso, [
            'model' => [Country::class],
            'their_field' => Country::hinting()->fieldName()->iso,
            'type' => 'string',
            'ui' => [
                'form' => [Form\Control\Line::class],
            ],
        ])
            ->addField($this->fieldName()->client_country, Country::hinting()->fieldName()->name);

        $this->addField($this->fieldName()->is_commercial, ['type' => 'boolean']);
        $this->addField($this->fieldName()->currency, ['values' => ['EUR' => 'Euro', 'USD' => 'US Dollar', 'GBP' => 'Pound Sterling']]);
        $this->addField($this->fieldName()->currency_symbol, ['never_persist' => true]);
        $this->onHook(Model::HOOK_AFTER_LOAD, function (self $model) {
            /* implementation for "intl"
            $locale = 'en-UK';
            $fmt = new \NumberFormatter($locale . '@currency=' . $model->currency, NumberFormatter::CURRENCY);
            $model->currency_symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
             */

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

class Percent extends \Atk4\Data\Field
{
    public $type = 'float'; // will need to be able to affect rendering and storage
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

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name);

        $this->addField($this->fieldName()->type, ['caption' => 'MIME Type']);
        $this->addField($this->fieldName()->is_folder, ['type' => 'boolean']);

        $this->hasMany($this->fieldName()->SubFolder, [
            'model' => [self::class],
            'their_field' => self::hinting()->fieldName()->parent_folder_id,
        ])
            ->addField($this->fieldName()->count, ['aggregate' => 'count', 'field' => $this->persistence->expr($this, '*')]);

        $this->hasOne($this->fieldName()->parent_folder_id, [
            'model' => [Folder::class],
        ])
            ->addTitle();
    }

    /**
     * Perform import from filesystem.
     */
    public function importFromFilesystem(string $path, bool $isSub = null): void
    {
        if ($isSub === null) {
            if ($this->isEntity()) {
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
            if ($fileinfo->isDot() || in_array($fileinfo->getFilename(), ['.git', 'vendor', 'js'], true)) {
                continue;
            }

            if (in_array($fileinfo->getFilename(), ['demos', 'src', 'tests'], true) || $isSub) {
                $entity = $this->createEntity();

                /*
                // Disabling saving file in db
                $entity->save([
                    $this->fieldName()->name => $fileinfo->getFilename(),
                    $this->fieldName()->is_folder => $fileinfo->isDir(),
                    $this->fieldName()->type => pathinfo($fileinfo->getFilename(), \PATHINFO_EXTENSION),
                ]);
                */

                if ($fileinfo->isDir()) {
                    $entity->SubFolder->importFromFilesystem($fileinfo->getPath() . '/' . $fileinfo->getFilename(), true);
                }
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

class FileLock extends File
{
    use ModelLockTrait;
    public $caption = 'File';

    protected function init(): void
    {
        parent::init();
        $this->lock();
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
            'their_field' => SubCategory::hinting()->fieldName()->product_category_id,
        ]);
        $this->hasMany($this->fieldName()->Products, [
            'model' => [Product::class],
            'their_field' => Product::hinting()->fieldName()->product_category_id,
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
            'their_field' => Product::hinting()->fieldName()->product_sub_category_id,
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

class ProductLock extends Product
{
    use ModelLockTrait;
    public $caption = 'Product';

    protected function init(): void
    {
        parent::init();
        $this->lock();
    }
}
