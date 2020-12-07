<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

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

class Country extends \Atk4\Data\Model
{
    public $table = 'country';

    protected function init(): void
    {
        parent::init();
        $this->addField('name', ['actual' => 'nicename', 'required' => true, 'type' => 'string']);
        $this->addField('sys_name', ['actual' => 'name', 'system' => true]);

        $this->addField('iso', ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui' => ['table' => ['sortable' => false]]]);
        $this->addField('iso3', ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField('numcode', ['caption' => 'ISO Numeric Code', 'type' => 'number', 'required' => true]);
        $this->addField('phonecode', ['caption' => 'Phone Prefix', 'type' => 'number', 'required' => true]);

        $this->onHook(\Atk4\Data\Model::HOOK_BEFORE_SAVE, function (\Atk4\Data\Model $model) {
            if (!$model->get('sys_name')) {
                $model->set('sys_name', mb_strtoupper($model->get('name')));
            }
        });
    }

    public function validate($intent = null): array
    {
        $errors = parent::validate($intent);

        if (mb_strlen($this->get('iso')) !== 2) {
            $errors['iso'] = 'Must be exactly 2 characters';
        }

        if (mb_strlen($this->get('iso3')) !== 3) {
            $errors['iso3'] = 'Must be exactly 3 characters';
        }

        // look if name is unique
        $c = clone $this;
        $c->unload();
        $c->tryLoadBy('name', $this->get('name'));
        if ($c->loaded() && $c->getId() !== $this->getId()) {
            $errors['name'] = 'Country name must be unique';
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

class Stat extends \Atk4\Data\Model
{
    public $table = 'stats';
    public $title = 'Project Stat';

    protected function init(): void
    {
        parent::init();

        $this->addFields(['project_name', 'project_code'], ['type' => 'string']);
        $this->title_field = 'project_name';
        $this->addField('description', ['type' => 'text']);
        $this->addField('client_name', ['type' => 'string']);
        $this->addField('client_address', ['type' => 'text', 'ui' => ['form' => [Form\Control\Textarea::class, 'rows' => 4]]]);

        $this->hasOne('client_country_iso', [
            new Country(),
            'their_field' => 'iso',
            'ui' => [
                'form' => [Form\Control\Line::class],
            ],
        ])
            ->addField('client_country', 'name');

        $this->addField('is_commercial', ['type' => 'boolean']);
        $this->addField('currency', ['values' => ['EUR' => 'Euro', 'USD' => 'US Dollar', 'GBP' => 'Pound Sterling']]);
        $this->addField('currency_symbol', ['never_persist' => true]);
        $this->onHook(\Atk4\Data\Model::HOOK_AFTER_LOAD, function (\Atk4\Data\Model $model) {
            /* implementation for "intl"
            $locale='en-UK';
            $fmt = new \NumberFormatter( $locale."@currency=".$model->get('currency'), NumberFormatter::CURRENCY );
            $model->set('currency_symbol', $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL));
             */

            $map = ['EUR' => '€', 'USD' => '$', 'GBP' => '£'];
            $model->set('currency_symbol', $map[$model->get('currency')] ?? '?');
        });

        $this->addFields(['project_budget', 'project_invoiced', 'project_paid', 'project_hour_cost'], ['type' => 'money']);

        $this->addFields(['project_hours_est', 'project_hours_reported'], ['type' => 'integer']);

        $this->addFields(['project_expenses_est', 'project_expenses'], ['type' => 'money']);
        $this->addField('project_mgmt_cost_pct', new Percent());
        $this->addField('project_qa_cost_pct', new Percent());

        $this->addFields(['start_date', 'finish_date'], ['type' => 'date']);
        $this->addField('finish_time', ['type' => 'time']);

        $this->addFields(['created', 'updated'], ['type' => 'datetime', 'ui' => ['form' => ['disabled' => true]]]);
    }
}

class Percent extends \Atk4\Data\Field
{
    public $type = 'float'; // will need to be able to affect rendering and storage
}

class File extends \Atk4\Data\Model
{
    public $table = 'file';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');

        $this->addField('type', ['caption' => 'MIME Type']);
        $this->addField('is_folder', ['type' => 'boolean']);

        $this->hasMany('SubFolder', [new self(), 'their_field' => 'parent_folder_id'])
            ->addField('count', ['aggregate' => 'count', 'field' => $this->persistence->expr($this, '*')]);

        $this->hasOne('parent_folder_id', Folder::class)
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
                    'name' => $fileinfo->getFilename(),
                    'is_folder' => $fileinfo->isDir(),
                    'type' => pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION),
                ]);
                */

                if ($fileinfo->isDir()) {
                    $m->ref('SubFolder')->importFromFilesystem($dir->getPath() . '/' . $name, true);
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

        $this->addCondition('is_folder', true);
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

class Category extends \Atk4\Data\Model
{
    public $table = 'product_category';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');

        $this->hasMany('SubCategories', new SubCategory());
        $this->hasMany('Products', new Product());
    }
}

class SubCategory extends \Atk4\Data\Model
{
    public $table = 'product_sub_category';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');

        $this->hasOne('product_category_id', new Category());
        $this->hasMany('Products', new Product());
    }
}

class Product extends \Atk4\Data\Model
{
    public $table = 'product';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
        $this->addField('brand');
        $this->hasOne('product_category_id', [new Category()])->addTitle();
        $this->hasOne('product_sub_category_id', [new SubCategory()])->addTitle();
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
