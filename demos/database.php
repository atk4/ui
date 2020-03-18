<?php

// A very basic file that sets up Agile Data to be used in some demonstrations
try {
    if (file_exists('db.php')) {
        include_once __DIR__ . '/db.php';
    } else {
        $db = new \atk4\data\Persistence\SQL('mysql:dbname=atk4;host=localhost', 'root', 'root');
    }
} catch (PDOException $e) {
    throw new \atk4\ui\Exception([
        'This demo requires access to the database. See "demos/database.php"',
    ], null, $e);
}

$app->db = $db;

if (!class_exists('Country')) {
    class Country extends \atk4\data\Model
    {
        public $table = 'country';

        public function init()
        {
            parent::init();
            $this->addField('name', ['actual' => 'nicename', 'required' => true, 'type' => 'string']);
            $this->addField('sys_name', ['actual' => 'name', 'system' => true]);

            $this->addField('iso', ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui'=>['table'=>['sortable'=>false]]]);
            $this->addField('iso3', ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
            $this->addField('numcode', ['caption' => 'ISO Numeric Code', 'type' => 'number', 'required' => true]);
            $this->addField('phonecode', ['caption' => 'Phone Prefix', 'type' => 'number', 'required' => true]);

            $this->onHook('beforeSave', function ($m) {
                if (!$m['sys_name']) {
                    $m['sys_name'] = strtoupper($m['name']);
                }
            });
        }

        public function validate($intent = null)
        {
            $errors = parent::validate($intent);

            if (strlen($this['iso']) !== 2) {
                $errors['iso'] = 'Must be exactly 2 characters';
            }

            if (strlen($this['iso3']) !== 3) {
                $errors['iso3'] = 'Must be exactly 3 characters';
            }

            // look if name is unique
            $c = clone $this;
            $c->unload();
            $c->tryLoadBy('name', $this['name']);
            if ($c->loaded() && $c->id != $this->id) {
                $errors['name'] = 'Country name must be unique';
            }

            return $errors;
        }
    }

    class Stat extends \atk4\data\Model
    {
        public $table = 'stats';
        public $title = 'Project Stat';

        public function init()
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
                    'ui'          => [
                        'display' => [
                            'form' => 'Line',
                        ],
                    ],
                ])
                ->addField('client_country', 'name');

            $this->addField('is_commercial', ['type' => 'boolean']);
            $this->addField('currency', ['enum' => ['EUR', 'USD', 'GBP']]);
            $this->addField('currency_symbol', ['never_persist' => true]);
            $this->onHook('afterLoad', function ($m) {
                /* implementation for "intl"
                $locale='en-UK';
                $fmt = new \NumberFormatter( $locale."@currency=".$m['currency'], NumberFormatter::CURRENCY );
                $m['currency_symbol'] = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
                 */

                $map = ['EUR' => '€', 'USD' => '$', 'GBP' => '£'];
                $m['currency_symbol'] = isset($map[$m['currency']]) ? $map[$m['currency']] : '?';
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

        public function init()
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
        public function importFromFilesystem($path)
        {
            $dir = new DirectoryIterator($path);
            foreach ($dir as $fileinfo) {
                if ($fileinfo->getFilename()[0] === '.') {
                    continue;
                }
                if ($fileinfo->getFilename() === 'vendor') {
                    continue;
                }

                $this->unload();

                $this->save([
                    'name'      => $fileinfo->getFilename(),
                    'is_folder' => $fileinfo->isDir(),
                    'type'      => pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION),
                ]);

                if ($fileinfo->isDir()) {
                    $this->ref('SubFolder')->importFromFilesystem($path . '/' . $fileinfo->getFilename());
                }
            }
        }
    }
}
