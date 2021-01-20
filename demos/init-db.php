<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Form;

try {
    if (file_exists(__DIR__ . '/db.php')) {
        require_once __DIR__ . '/db.php';
    } else {
        require_once __DIR__ . '/db.default.php';
    }
} catch (\PDOException $e) {
    // do not pass $e unless you can secure DSN!
    throw (new \Atk4\Ui\Exception('This demo requires access to the database. See "demos/init-db.php"'))
        ->addMoreInfo('PDO error', $e->getMessage());
}

// a very basic file that sets up Agile Data to be used in some demonstrations

trait ModelLockTrait
{
    public function lock(): void
    {
        $this->getUserAction('add')->callback = function ($model) {
            return 'Form Submit! Data are not save in demo mode.';
        };
        $this->getUserAction('edit')->callback = function ($model) {
            return 'Form Submit! Data are not save in demo mode.';
        };

        $delete = $this->getUserAction('delete');
        $delete->confirmation = 'Please go ahead. Demo mode does not really delete data.';

        $delete->callback = function ($model) {
            return 'Only simulating delete when in demo mode.';
        };
    }
}

/**
 * @property string $name      @Atk\Field(field_name="xxx_name")
 * @property string $sys_name  @Atk\Field(field_name="xxx_sys_name")
 * @property string $iso       @Atk\Field(field_name="xxx_iso")
 * @property string $iso3      @Atk\Field(field_name="xxx_iso3")
 * @property string $numcode   @Atk\Field(field_name="xxx_numcode")
 * @property string $phonecode @Atk\Field(field_name="xxx_phonecode")
 */
class Country extends Model
{
    public $table = 'country';
    public $title_field = 'xxx_name';

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name, ['actual' => 'yyy_nicename', 'required' => true, 'type' => 'string']);
        $this->addField($this->fieldName()->sys_name, ['actual' => 'yyy_name', 'system' => true]);

        $this->addField($this->fieldName()->iso, ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui' => ['table' => ['sortable' => false]]]);
        $this->addField($this->fieldName()->iso3, ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField($this->fieldName()->numcode, ['caption' => 'ISO Numeric Code', 'type' => 'number', 'required' => true]);
        $this->addField($this->fieldName()->phonecode, ['caption' => 'Phone Prefix', 'type' => 'number', 'required' => true]);

        $this->onHook(Model::HOOK_BEFORE_SAVE, function (self $model) {
            if (!$model->sys_name) {
                $model->sys_name = mb_strtoupper($model->name);
            }
        });
    }

    public function validate($intent = null): array
    {
        $errors = parent::validate($intent);

        if (mb_strlen($this->iso) !== 2) {
            $errors[$this->fieldName()->iso] = 'Must be exactly 2 characters';
        }

        if (mb_strlen($this->iso3) !== 3) {
            $errors[$this->fieldName()->iso3] = 'Must be exactly 3 characters';
        }

        // look if name is unique
        $c = clone $this;
        $c->unload();
        $c->tryLoadBy($this->fieldName()->name, $this->name);
        if ($c->loaded() && $c->getId() !== $this->getId()) {
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
 * @property string    $project_name           @Atk\Field(field_name="xxx_project_name")
 * @property string    $project_code           @Atk\Field(field_name="xxx_project_code")
 * @property string    $description            @Atk\Field(field_name="xxx_description")
 * @property string    $client_name            @Atk\Field(field_name="xxx_client_name")
 * @property string    $client_address         @Atk\Field(field_name="xxx_client_address")
 * @property Country   $client_country_iso     @Atk\RefOne(field_name="xxx_client_country_iso")
 * @property string    $client_country         @Atk\Field(field_name="xxx_client_country")
 * @property bool      $is_commercial          @Atk\Field(field_name="xxx_is_commercial")
 * @property string    $currency               @Atk\Field(field_name="xxx_currency")
 * @property string    $currency_symbol        @Atk\Field(field_name="xxx_currency_symbol")
 * @property float     $project_budget         @Atk\Field(field_name="xxx_project_budget")
 * @property float     $project_invoiced       @Atk\Field(field_name="xxx_project_invoiced")
 * @property float     $project_paid           @Atk\Field(field_name="xxx_project_paid")
 * @property float     $project_hour_cost      @Atk\Field(field_name="xxx_project_hour_cost")
 * @property int       $project_hours_est      @Atk\Field(field_name="xxx_project_hours_est")
 * @property int       $project_hours_reported @Atk\Field(field_name="xxx_project_hours_reported")
 * @property float     $project_expenses_est   @Atk\Field(field_name="xxx_project_expenses_est")
 * @property float     $project_expenses       @Atk\Field(field_name="xxx_project_expenses")
 * @property float     $project_mgmt_cost_pct  @Atk\Field(field_name="xxx_project_mgmt_cost_pct")
 * @property float     $project_qa_cost_pct    @Atk\Field(field_name="xxx_project_qa_cost_pct")
 * @property \DateTime $start_date             @Atk\Field(field_name="xxx_start_date")
 * @property \DateTime $finish_date            @Atk\Field(field_name="xxx_finish_date")
 * @property \DateTime $finish_time            @Atk\Field(field_name="xxx_finish_time")
 * @property \DateTime $created                @Atk\Field(field_name="xxx_created")
 * @property \DateTime $updated                @Atk\Field(field_name="xxx_updated")
 */
class Stat extends Model
{
    public $table = 'stats';
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

        $this->addField($this->fieldName()->project_budget, ['type' => 'money']);
        $this->addField($this->fieldName()->project_invoiced, ['type' => 'money']);
        $this->addField($this->fieldName()->project_paid, ['type' => 'money']);
        $this->addField($this->fieldName()->project_hour_cost, ['type' => 'money']);

        $this->addField($this->fieldName()->project_hours_est, ['type' => 'integer']);
        $this->addField($this->fieldName()->project_hours_reported, ['type' => 'integer']);

        $this->addField($this->fieldName()->project_expenses_est, ['type' => 'money']);
        $this->addField($this->fieldName()->project_expenses, ['type' => 'money']);
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
 * @property string $name             @Atk\Field(field_name="xxx2_name")
 * @property string $type             @Atk\Field(field_name="xxx_type")
 * @property bool   $is_folder        @Atk\Field(field_name="xxx_is_folder")
 * @property File   $SubFolder        @Atk\RefOne(field_name="xxx_SubFolder")
 * @property int    $count            @Atk\Field(field_name="xxx_count")
 * @property Folder $parent_folder_id @Atk\RefOne(field_name="xxx_parent_folder_id")
 */
class File extends Model
{
    public $table = 'file';
    public $title_field = 'xxx2_name';

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name);

        $this->addField($this->fieldName()->type, ['caption' => 'MIME Type']);
        $this->addField($this->fieldName()->is_folder, ['type' => 'boolean']);

        $this->hasMany($this->fieldName()->SubFolder, ['model' => [self::class], 'their_field' => self::hinting()->fieldName()->parent_folder_id])
            ->addField($this->fieldName()->count, ['aggregate' => 'count', 'field' => $this->persistence->expr($this, '*')]);

        $this->hasOne($this->fieldName()->parent_folder_id, ['model' => [Folder::class]])
            ->addTitle();
    }

    /**
     * Perform import from filesystem.
     */
    public function importFromFilesystem($path, $isSub = false)
    {
        if (!$isSub) {
            $path = __DIR__ . '/../' . $path;
        }

        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            $name = $fileinfo->getFilename();

            if ($name === '.' || $name[0] === '.') {
                continue;
            }

            if ($name === 'src' || $name === 'demos' || $isSub) {
                $m = clone $this;

                /*
                // Disabling saving file in db
                $m->save([
                    $this->fieldName()->name => $fileinfo->getFilename(),
                    $this->fieldName()->is_folder => $fileinfo->isDir(),
                    $this->fieldName()->type => pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION),
                ]);
                */

                if ($fileinfo->isDir()) {
                    $m->SubFolder->importFromFilesystem($dir->getPath() . '/' . $name, true);
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
 * @property string      $name          @Atk\Field(field_name="xxx3_name")
 * @property SubCategory $SubCategories @Atk\RefOne(field_name="xxx_SubCategories")
 * @property Product     $Products      @Atk\RefOne(field_name="xxx_Products")
 */
class Category extends Model
{
    public $table = 'product_category';
    public $title_field = 'xxx3_name';

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name);

        $this->hasMany($this->fieldName()->SubCategories, ['model' => [SubCategory::class], 'their_field' => SubCategory::hinting()->fieldName()->product_category_id]);
        $this->hasMany($this->fieldName()->Products, ['model' => [Product::class], 'their_field' => Product::hinting()->fieldName()->product_category_id]);
    }
}

/**
 * @property string   $name                @Atk\Field(field_name="xxx4_name")
 * @property Category $product_category_id @Atk\RefOne(field_name="xxx2_product_category_id")
 * @property Product  $Products            @Atk\RefOne(field_name="xxx2_Products")
 */
class SubCategory extends Model
{
    public $table = 'product_sub_category';
    public $title_field = 'xxx4_name';

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name);

        $this->hasOne($this->fieldName()->product_category_id, ['model' => [Category::class]]);
        $this->hasMany($this->fieldName()->Products, ['model' => [Product::class], 'their_field' => Product::hinting()->fieldName()->product_category_id]);
    }
}

/**
 * @property string      $name                    @Atk\Field(field_name="xxx5_name")
 * @property string      $brand                   @Atk\Field(field_name="xxx_brand")
 * @property Category    $product_category_id     @Atk\RefOne(field_name="xxx_product_category_id")
 * @property SubCategory $product_sub_category_id @Atk\RefOne(field_name="xxx_product_sub_category_id")
 */
class Product extends Model
{
    public $table = 'product';
    public $title_field = 'xxx5_name';

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->name);
        $this->addField($this->fieldName()->brand);
        $this->hasOne($this->fieldName()->product_category_id, ['model' => [Category::class]])->addTitle();
        $this->hasOne($this->fieldName()->product_sub_category_id, ['model' => [SubCategory::class]])->addTitle();
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
