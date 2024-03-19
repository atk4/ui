<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Table;

/**
 * Swaps out column decorators based on logic.
 */
class Multiformat extends Table\Column
{
    /** @var \Closure<TModel of Model, TField of Field>(TModel, TField|null): list<array<0|string, mixed>|Table\Column> Method to execute which will return array of seeds for decorators */
    protected \Closure $decoratorsFx;

    /**
     * @param \Closure<TModel of Model, TField of Field>(TModel, TField|null): list<array<0|string, mixed>|Table\Column> $decoratorsFx
     */
    public function __construct(\Closure $decoratorsFx)
    {
        parent::__construct();

        $this->decoratorsFx = $decoratorsFx;
    }

    #[\Override]
    public function getDataCellHtml(?Field $field = null, array $attr = []): string
    {
        return '{$c_' . $this->shortName . '}';
    }

    #[\Override]
    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $decorators = ($this->decoratorsFx)($row, $field);

        $name = $field->shortName;

        // we need to smartly wrap things up
        $cellHtml = null;
        $tdAttr = [];
        $htmlTags = [];
        foreach ($decorators as $cKey => $c) {
            if (!is_object($c)) {
                $c = $this->getOwner()->decoratorFactory($field, $c);
            }
            $c = Table\Column::assertInstanceOf($c);

            if ($cKey !== array_key_last($decorators)) {
                $html = $c->getDataCellTemplate($field);
                $tdAttr = $c->getTagAttributes('body', $tdAttr);
            } else {
                // last formatter, ask it to give us whole rendering
                $html = $c->getDataCellHtml($field, $tdAttr);
            }

            $cellHtml = $cellHtml === null
                ? $html
                : str_replace('{$' . $name . '}', $cellHtml, $html);

            $htmlTags = array_merge($c->getHtmlTags($row, $field), $htmlTags);
        }

        $template = new HtmlTemplate($cellHtml);
        $template->trySet($this->getApp()->uiPersistence->typecastSaveRow($row, $row->get()));
        $template->dangerouslySetHtml($htmlTags);

        $val = $template->renderToHtml();

        return ['c_' . $this->shortName => $val];
    }
}
