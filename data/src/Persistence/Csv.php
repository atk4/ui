<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Atk4\Data\Persistence;

/**
 * Implements persistence driver that can save data and load from CSV file.
 * This basic driver only offers the load/save. It does not offer conditions or
 * id-specific operations. You can only use a single persistence object with
 * a single file.
 *
 * $p = new Persistence\Csv('file.csv');
 * $m = new MyModel($p);
 * $data = $m->export();
 *
 * Alternatively you can write into a file. First operation you perform on
 * the persistence will determine the mode.
 *
 * $p = new Persistence\Csv('file.csv');
 * $m = new MyModel($p);
 * $m->import($data);
 */
class Csv extends Persistence
{
    /**
     * Name of the file.
     *
     * @var string
     */
    public $file;

    /**
     * Line in CSV file.
     *
     * @var int
     */
    public $line = 0;

    /**
     * File handle, when the $file is opened.
     *
     * @var resource|null
     */
    public $handle;

    /**
     * Mode of operation. 'r' for reading and 'w' for writing.
     * If you manually set this operation, it will be used
     * for file opening.
     *
     * @var string
     */
    public $mode;

    /**
     * Delimiter in CSV file.
     *
     * @var string
     */
    public $delimiter = ',';

    /**
     * Enclosure in CSV file.
     *
     * @var string
     */
    public $enclosure = '"';

    /**
     * Escape character in CSV file.
     *
     * @var string
     */
    public $escape_char = '\\';

    /**
     * Array of field names.
     *
     * @var array|null
     */
    public $header;

    public function __construct(string $file, array $defaults = [])
    {
        $this->file = $file;
        $this->setDefaults($defaults);
    }

    public function __destruct()
    {
        $this->closeFile();
    }

    /**
     * Open CSV file.
     *
     * Override this method and open handle yourself if you want to
     * reposition or load some extra columns on the top.
     *
     * @param string $mode 'r' or 'w'
     */
    public function openFile(string $mode = 'r'): void
    {
        if (!$this->handle) {
            $this->handle = fopen($this->file, $mode);
            if ($this->handle === false) {
                throw (new Exception('Cannot open CSV file.'))
                    ->addMoreInfo('file', $this->file)
                    ->addMoreInfo('mode', $mode);
            }
        }
    }

    /**
     * Close CSV file.
     */
    public function closeFile(): void
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
            $this->header = null;
        }
    }

    /**
     * Returns one line of CSV file as array.
     */
    public function getLine(): ?array
    {
        $data = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape_char);
        if ($data === false) {
            return null;
        }

        ++$this->line;

        return $data;
    }

    /**
     * Writes array as one record to CSV file.
     */
    public function putLine(array $data): void
    {
        $ok = fputcsv($this->handle, $data, $this->delimiter, $this->enclosure, $this->escape_char);
        if ($ok === false) {
            throw new Exception('Cannot write to CSV file.');
        }
    }

    /**
     * When load operation starts, this will open file and read
     * the first line. This line is then used to identify columns.
     */
    public function loadHeader(): void
    {
        $this->openFile('r');

        $header = $this->getLine();
        --$this->line; // because we don't want to count header line

        $this->initializeHeader($header);
    }

    /**
     * When load operation starts, this will open file and read
     * the first line. This line is then used to identify columns.
     */
    public function saveHeader(Model $model): void
    {
        $this->openFile('w');

        $header = [];
        foreach ($model->getFields() as $name => $field) {
            if ($model->id_field && $name === $model->id_field) {
                continue;
            }

            $header[] = $name;
        }

        $this->putLine($header);

        $this->initializeHeader($header);
    }

    /**
     * Remembers $this->header so that the data can be
     * easier mapped.
     */
    public function initializeHeader(array $header): void
    {
        // removes forbidden symbols from header (field names)
        $this->header = array_map(function ($name) {
            return preg_replace('/[^a-z0-9_-]+/i', '_', $name);
        }, $header);
    }

    /**
     * Typecasting when load data row.
     */
    public function typecastLoadRow(Model $model, array $row): array
    {
        $id = null;
        if ($model->id_field) {
            if (isset($row[$model->id_field])) {
                // temporary remove id field
                $id = $row[$model->id_field];
                unset($row[$model->id_field]);
            } else {
                $id = null;
            }
        }

        $row = array_combine($this->header, $row);
        if ($model->id_field && isset($id)) {
            $row[$model->id_field] = $id;
        }

        foreach ($row as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($model->hasField($key)) {
                $row[$key] = $this->typecastLoadField($model->getField($key), $value);
            }
        }

        return $row;
    }

    public function tryLoad(Model $model, $id): ?array
    {
        if ($id !== self::ID_LOAD_ANY) {
            throw new Exception('CSV Persistence does not support other than LOAD ANY mode'); // @TODO
        }

        if (!$this->mode) {
            $this->mode = 'r';
        } elseif ($this->mode === 'w') {
            throw new Exception('Currently writing records, so loading is not possible.');
        }

        if (!$this->handle) {
            $this->loadHeader();
        }

        $data = $this->getLine();
        if ($data === null) {
            return null;
        }

        $data = $this->typecastLoadRow($model, $data);
        if ($model->id_field) {
            $data[$model->id_field] = $this->line;
        }

        return $data;
    }

    public function prepareIterator(Model $model): \Traversable
    {
        if (!$this->mode) {
            $this->mode = 'r';
        } elseif ($this->mode === 'w') {
            throw new Exception('Currently writing records, so loading is not possible.');
        }

        if (!$this->handle) {
            $this->loadHeader();
        }

        while (true) {
            $data = $this->getLine();
            if ($data === null) {
                break;
            }
            $data = $this->typecastLoadRow($model, $data);
            if ($model->id_field) {
                $data[$model->id_field] = $this->line;
            }

            yield $data;
        }
    }

    /**
     * Inserts record in data array and returns new record ID.
     *
     * @return mixed
     */
    public function insert(Model $model, array $data)
    {
        if (!$this->mode) {
            $this->mode = 'w';
        } elseif ($this->mode === 'r') {
            throw new Exception('Currently reading records, so writing is not possible.');
        }

        if (!$this->handle) {
            $this->saveHeader($model);
        }

        $line = [];

        foreach ($this->header as $name) {
            $line[] = $data[$name];
        }

        $this->putLine($line);

        return $model->id_field ? $data[$model->id_field] : null;
    }

    /**
     * Updates record in data array and returns record ID.
     *
     * @param mixed $id
     */
    public function update(Model $model, $id, array $data): void
    {
        throw new Exception('Updating records is not supported in CSV persistence.');
    }

    /**
     * Deletes record in data array.
     *
     * @param mixed $id
     */
    public function delete(Model $model, $id): void
    {
        throw new Exception('Deleting records is not supported in CSV persistence.');
    }

    /**
     * Generates new record ID.
     *
     * @return string
     */
    public function generateNewId(Model $model)
    {
        throw new Exception('Not implemented');
    }

    /**
     * Export all DataSet.
     */
    public function export(Model $model, array $fields = null): array
    {
        $data = [];

        foreach ($model as $row) {
            $rowData = $row->get();
            $data[] = $fields !== null ? array_intersect_key($rowData, array_flip($fields)) : $rowData;
        }

        // need to close file otherwise file pointer is at the end of file
        $this->closeFile();

        return $data;
    }
}
