<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Column for formatting country code as flags.
 */
class Flag extends Table\Column
{
    /**
     * Name of model field which contains country ALPHA-2 (2 letter) codes.
     *
     * @var string
     */
    public $code_field;

    /**
     * Optional name of model field which contains country names.
     *
     * @var string
     */
    public $name_field;

    protected function init(): void
    {
        parent::init();

        if (!$this->code_field) {
            throw new Exception('Country code field must be defined');
        }
    }

    public function getHtmlTags(Model $row, $field)
    {
        if ($row->hasField($this->code_field)) {
            $code = $row->get($this->code_field);
            $name = $this->name_field ? $row->get($this->name_field) : null;

            return [
                $field->short_name => empty($code) ? '' : $this->getApp()->getTag('i', ['class' => strtolower($code) . ' flag', 'title' => $code . ($name ? ': ' . $name : '')]),
            ];
        }

        return [$field->short_name => $field->get($row)];
    }
}
