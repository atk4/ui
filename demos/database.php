<?php

// A very basic file that sets up Agile Data to be used in some demonstrations
try {
    if (file_exists('db.php')) {
        include 'db.php';
    } else {
        $db = new \atk4\data\Persistence_SQL('mysql:dbname=atk4;host=localhost', 'root', 'root');
    }
} catch (PDOException $e) {
    throw new \atk4\ui\Exception([
        'This demo requires access to the database. See "demos/database.php"',
    ], null, $e);
}

class Country extends \atk4\data\Model
{
    public $table = 'country';

    public function init()
    {
        parent::init();
        $this->addField('name', ['actual'=>'nicename']);

        $this->addField('iso', ['caption'=>'ISO']);
        $this->addField('iso3', ['caption'=>'ISO3']);
        $this->addField('numcode', ['caption'=>'ISO Numeric Code']);
        $this->addField('phonecode', ['caption'=>'Phone Prefix']);
    }
}

class File extends \atk4\data\Model
{
    public $table = 'file';

    public function init()
    {
        parent::init();
        $this->addField('name');

        $this->addField('type', ['caption'=>'MIME Type']);
        $this->addField('is_folder', ['type'=>'boolean']);

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
                'name'     => $fileinfo->getFilename(),
                'is_folder'=> $fileinfo->isDir(),
                'type'     => pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION),
            ]);

            if ($fileinfo->isDir()) {
                $this->ref('SubFolder')->importFromFilesystem($path.'/'.$fileinfo->getFilename());
            }
        }
    }
}
