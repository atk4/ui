<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\HtmlTemplate\Value as HtmlValue;

/**
 * Simple text block view.
 */
class Text extends ViewWithContent
{
    public $defaultTemplate;

    /** @var list<HtmlValue> */
    public $content = []; // @phpstan-ignore property.phpDocType

    public function __construct($label = [])
    {
        $defaults = is_array($label) ? $label : [$label];

        if (array_key_exists(0, $defaults)) {
            $defaults[0] = [(new HtmlValue())->set($defaults[0])];
        }

        parent::__construct($defaults);
    }

    #[\Override]
    public function renderToHtml(): string
    {
        return $this->getHtml();
    }

    #[\Override]
    public function getHtml(): string
    {
        return implode('', array_map(static fn ($v) => $v->getHtml(), $this->content));
    }

    #[\Override]
    public function set($text)
    {
        $this->content = [(new HtmlValue())->set($text)];

        return $this;
    }

    /**
     * Adds HTML paragraph.
     *
     * @param string $text
     *
     * @return $this
     */
    public function addParagraph($text)
    {
        $this->dangerouslyAddHtml('<p>');
        $this->content[] = (new HtmlValue())->set($text);
        $this->dangerouslyAddHtml('</p>');

        return $this;
    }

    /**
     * Adds some HTML code.
     *
     * @return $this
     */
    public function dangerouslyAddHtml(string $html)
    {
        $this->content[] = (new HtmlValue())->dangerouslySetHtml($html);

        return $this;
    }
}
