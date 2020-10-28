<?php

declare(strict_types=1);

namespace atk4\ui\HtmlTemplate;

use atk4\ui\Exception;
use atk4\ui\HtmlTemplateNew;

class TagTree
{
    /** @var HtmlTemplateNew */
    private $parentTemplate;

    /** @var string */
    private $tag;

    /** @var Value[]|string[]|HtmlTemplateNew[] */
    private $children = [];

    public function __construct(HtmlTemplateNew $parentTemplate, string $tag)
    {
        $this->parentTemplate = $parentTemplate;
        $this->tag = $tag;
    }

    private function __clone()
    {
    }

    /**
     * @return static
     */
    public function clone(HtmlTemplateNew $newParentTemplate): self
    {
        $res = new static($newParentTemplate, $this->tag);
        $res->children = [];
        foreach ($this->children as $k => $v) {
            $res->children[$k] = is_string($v) ? $v : clone $v;
        }

        return $res;
    }

    public function getParentTemplate(): HtmlTemplateNew
    {
        return $this->parentTemplate;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return Value[]|self[]|HtmlTemplateNew[]
     */
    public function getChildren(): array
    {
        $res = [];
        $parentTemplate = $this->getParentTemplate();
        foreach ($this->children as $k => $v) {
            $res[$k] = is_string($v) ? $parentTemplate->getTagTree($v) : $v;
        }

        return $res;
    }

    /**
     * @param Value|HtmlTemplateNew $value
     *
     * @return $this
     */
    public function add(object $value): self
    {
        // very important check
        if ($value instanceof self) {
            throw new Exception('Tag tree can not be added directly');
        }

        // not strictly needed, but catch issues sooner
        if (!$value instanceof Value && !$value instanceof HtmlTemplateNew) {
            throw new Exception('Value must be of type HtmlTemplate\Value or HtmlTemplate');
        }

        $this->children[] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function addTag(string $tag): self
    {
        $this->getParentTemplate()->getTagTree($tag); // check if exists

        $this->children[] = $tag;

        return $this;
    }
}
