<?php

declare(strict_types=1);

namespace Atk4\Ui\HtmlTemplate;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Ui\Exception;
use Atk4\Ui\HtmlTemplate;

/**
 * @phpstan-consistent-constructor
 */
class TagTree
{
    use WarnDynamicPropertyTrait;

    private HtmlTemplate $parentTemplate;

    private string $tag;

    /** @var array<int, Value|string|HtmlTemplate> */
    private array $children = [];

    public function __construct(HtmlTemplate $parentTemplate, string $tag)
    {
        $this->parentTemplate = $parentTemplate;
        $this->tag = $tag;
    }

    private function __clone()
    {
        // prevent cloning
    }

    /**
     * @return static
     */
    public function clone(HtmlTemplate $newParentTemplate): self
    {
        $res = new static($newParentTemplate, $this->tag);
        foreach ($this->children as $k => $v) {
            $res->children[$k] = is_string($v) ? $v : clone $v;
        }

        return $res;
    }

    public function getParentTemplate(): HtmlTemplate
    {
        return $this->parentTemplate;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return array<int, Value|self|HtmlTemplate>
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
     * @param Value|HtmlTemplate $value
     *
     * @return $this
     */
    public function add(object $value): self
    {
        if (!$value instanceof Value && !$value instanceof HtmlTemplate) { // @phpstan-ignore-line
            if ($value instanceof self) { // @phpstan-ignore-line
                throw new Exception('Tag tree cannot be added directly');
            }

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
