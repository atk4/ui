<?php

// A very basic file that sets up Agile Data to be used in some demonstrations
try {
    if (file_exists(__DIR__ . '/db.php')) {
        require_once __DIR__ . '/db.php';
    } else {
        require_once __DIR__ . '/db.example.php';
    }
} catch (PDOException $e) {
    throw (new \atk4\ui\Exception([
        'This demo requires access to the database. See "demos/database.php"',
        // do not pass $e here unless you can secure DSN!
    ]))->addMoreInfo('PDO error', $e->getMessage());
}

trait ModelLockTrait
{
    public function lock(): void
    {
        $this->getAction('add')->callback = function ($m) {
            return new \atk4\ui\jsToast('Form Submit! Data are not save in demo mode.');
        };
        $this->getAction('edit')->callback = function ($m) {
            return new \atk4\ui\jsToast('Form Submit! Data are not save in demo mode.');
        };

        $delete = $this->getAction('delete');

        $delete->confirmation = 'Please go ahead. Demo mode does not really delete data.';

        $delete->callback = function ($m) {
            return [
                (new \atk4\ui\jQuery())->closest('tr')->transition('fade left'),
                new \atk4\ui\jsToast('Simulating delete in demo mode.'),
            ];
        };
    }
}

class Country extends \atk4\data\Model
{
    public $table = 'country';

    public function init(): void
    {
        parent::init();
        $this->addField('name', ['actual' => 'nicename', 'required' => true, 'type' => 'string']);
        $this->addField('sys_name', ['actual' => 'name', 'system' => true]);

        $this->addField('iso', ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui' => ['table' => ['sortable' => false]]]);
        $this->addField('iso3', ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField('numcode', ['caption' => 'ISO Numeric Code', 'type' => 'number', 'required' => true]);
        $this->addField('phonecode', ['caption' => 'Phone Prefix', 'type' => 'number', 'required' => true]);

        $this->onHook('beforeSave', function (atk4\data\Model $m) {
            if (!$m->get('sys_name')) {
                $m->set('sys_name', mb_strtoupper($m->get('name')));
            }
        });
    }

    public function validate($intent = null)
    {
        $errors = parent::validate($intent);

        if (mb_strlen($this['iso']) !== 2) {
            $errors['iso'] = 'Must be exactly 2 characters';
        }

        if (mb_strlen($this['iso3']) !== 3) {
            $errors['iso3'] = 'Must be exactly 3 characters';
        }

        // look if name is unique
        $c = clone $this;
        $c->unload();
        $c->tryLoadBy('name', $this['name']);
        if ($c->loaded() && $c->id !== $this->id) {
            $errors['name'] = 'Country name must be unique';
        }

        return $errors;
    }
}

class CountryLock extends Country
{
    use ModelLockTrait;
    public $caption = 'Country';

    public function init(): void
    {
        parent::init();
        $this->lock();
    }
}

class Stat extends \atk4\data\Model
{
    public $table = 'stats';
    public $title = 'Project Stat';

    public function init(): void
    {
        parent::init();

        $this->addFields(['project_name', 'project_code'], ['type' => 'string']);
        $this->title_field = 'project_name';
        //$this->addField('description', ['ui'=>['form'=>['FormField/TextArea', 'rows'=>5]]]);
        $this->addField('description', ['type' => 'text']);
        $this->addField('client_name', ['type' => 'string']);
        $this->addField('client_address', ['type' => 'string', 'ui' => ['form' => [new \atk4\ui\FormField\TextArea(), 'rows' => 4]]]);

        $this->hasOne('client_country_iso', [
            new Country(),
            'their_field' => 'iso',
            'ui' => [
                'display' => [
                    'form' => 'Line',
                ],
            ],
        ])
            ->addField('client_country', 'name');

        $this->addField('is_commercial', ['type' => 'boolean']);
        $this->addField('currency', ['enum' => ['EUR', 'USD', 'GBP']]);
        $this->addField('currency_symbol', ['never_persist' => true]);
        $this->onHook(atk4\data\Model::HOOK_AFTER_LOAD, function (atk4\data\Model $m) {
            /* implementation for "intl"
            $locale='en-UK';
            $fmt = new \NumberFormatter( $locale."@currency=".$m->get('currency'), NumberFormatter::CURRENCY );
            $m->set('currency_symbol', $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL));
             */

            $map = ['EUR' => '€', 'USD' => '$', 'GBP' => '£'];
            $m->set('currency_symbol', $map[$m->get('currency')] ?? '?');
        });

        $this->addFields(['project_budget', 'project_invoiced', 'project_paid', 'project_hour_cost'], ['type' => 'money']);

        $this->addFields(['project_hours_est', 'project_hours_reported'], ['type' => 'integer']);

        $this->addFields(['project_expenses_est', 'project_expenses'], ['type' => 'money']);
        $this->addField('project_mgmt_cost_pct', new Percent());
        $this->addField('project_qa_cost_pct', new Percent());

        $this->addFields(['start_date', 'finish_date'], ['type' => 'date']);
        $this->addField('finish_time', ['type' => 'time']);

        $this->addFields(['created', 'updated'], ['type' => 'datetime', 'ui' => ['form' => ['Line', 'disabled' => true]]]);
    }
}

class Percent extends \atk4\data\Field
{
    public $type = 'float'; // will need to be able to affect rendering and storage
}

class File extends \atk4\data\Model
{
    public $table = 'file';

    public function init(): void
    {
        parent::init();
        $this->addField('name');

        $this->addField('type', ['caption' => 'MIME Type']);
        $this->addField('is_folder', ['type' => 'boolean']);

        $this->hasMany('SubFolder', [new self(), 'their_field' => 'parent_folder_id'])
            ->addField('count', ['aggregate' => 'count', 'field' => $this->expr('*')]);

        $this->hasOne('parent_folder_id', new self())
            ->addTitle();
    }

    /**
     * Perform import from filesystem.
     */
    public function importFromFilesystem($path, $isSub = false)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            $name = $fileinfo->getFilename();

            if ($fileinfo->getFilename() === '.') {
                continue;
            }
            if ($fileinfo->getFilename()[0] === '.') {
                continue;
            }

            if ($fileinfo->getFilename() === 'src' || $fileinfo->getFilename() === 'demos' || $isSub) {
                $this->unload();
                $this->save([
                    'name' => $fileinfo->getFilename(),
                    'is_folder' => $fileinfo->isDir(),
                    'type' => pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION),
                ]);

                if ($fileinfo->isDir()) {
                    $this->ref('SubFolder')->importFromFilesystem($path . '/' . $fileinfo->getFilename(), true);
                }
            }
        }
    }
}

class FileLock extends File
{
    use ModelLockTrait;
    public $caption = 'File';

    public function init(): void
    {
        parent::init();
        $this->lock();
    }
}
