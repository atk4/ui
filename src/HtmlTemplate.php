<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Data\Model;
use Atk4\Ui\HtmlTemplate\TagTree;
use Atk4\Ui\HtmlTemplate\Value as HtmlValue;

/**
 * @phpstan-consistent-constructor
 */
class HtmlTemplate
{
    use AppScopeTrait;
    use WarnDynamicPropertyTrait;

    public const TOP_TAG = '_top';

    /** @var array<string, string|false> */
    private static array $_realpathCache = [];
    /** @var array<string, string|false> */
    private static array $_filesCache = [];

    private static ?self $_parseCacheParentTemplate = null;
    /** @var array<string, array<string, TagTree>> */
    private static array $_parseCache = [];

    /** @var array<string, TagTree> */
    private array $tagTrees;

    public function __construct(string $template = '')
    {
        $this->loadFromString($template);
    }

    public function _hasTag(string $tag): bool
    {
        return isset($this->tagTrees[$tag]);
    }

    /**
     * @param string|list<string> $tag
     */
    public function hasTag($tag): bool
    {
        // check if all tags exist
        if (is_array($tag)) {
            foreach ($tag as $t) {
                if (!$this->_hasTag($t)) {
                    return false;
                }
            }

            return true;
        }

        return $this->_hasTag($tag);
    }

    public function getTagTree(string $tag): TagTree
    {
        if (!isset($this->tagTrees[$tag])) {
            throw (new Exception('Tag is not defined in template'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('template_tags', array_diff(array_keys($this->tagTrees), [self::TOP_TAG]));
        }

        return $this->tagTrees[$tag];
    }

    private function cloneTagTrees(array $tagTrees): array
    {
        $res = [];
        foreach ($tagTrees as $k => $v) {
            $res[$k] = $v->clone($this);
        }

        return $res;
    }

    public function __clone()
    {
        $this->tagTrees = $this->cloneTagTrees($this->tagTrees);
    }

    /**
     * @return static
     */
    public function cloneRegion(string $tag): self
    {
        $template = new static();
        $template->tagTrees = $template->cloneTagTrees($this->tagTrees);

        // rename top tag tree
        $topTagTree = $template->tagTrees[$tag];
        unset($template->tagTrees[$tag]);
        $template->tagTrees[self::TOP_TAG] = $topTagTree;
        $topTag = self::TOP_TAG;
        \Closure::bind(static function () use ($topTagTree, $topTag) {
            $topTagTree->tag = $topTag;
        }, null, TagTree::class)();

        // TODO prune unreachable nodes
        // $template->rebuildTagsIndex();

        if ($this->issetApp()) {
            $template->setApp($this->getApp());
        }

        return $template;
    }

    protected function _unsetFromTagTree(TagTree $tagTree, int $k): void
    {
        \Closure::bind(static function () use ($tagTree, $k) {
            if ($k === array_key_last($tagTree->children)) {
                array_pop($tagTree->children);
            } else {
                unset($tagTree->children[$k]);
            }
        }, null, TagTree::class)();
    }

    protected function emptyTagTree(TagTree $tagTree): void
    {
        foreach ($tagTree->getChildren() as $k => $v) {
            if ($v instanceof TagTree) {
                $this->emptyTagTree($v);
            } else {
                $this->_unsetFromTagTree($tagTree, $k);
            }
        }
    }

    /**
     * Internal method for setting or appending content in $tag.
     *
     * If tag contains another tag trees, these tag trees are emptied.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     */
    protected function _setOrAppend($tag, string $value = null, bool $encodeHtml = true, bool $append = false, bool $throwIfNotFound = true): void
    {
        if ($tag instanceof Model) {
            if (!$encodeHtml) {
                throw new Exception('HTML is not allowed to be dangerously set from Model');
            }

            $tag = $this->getApp()->uiPersistence->typecastSaveRow($tag, $tag->get());
        }

        // $tag passed as associative array [tag => value]
        // in this case we don't throw exception if tags don't exist
        if (is_array($tag) && $value === null) {
            foreach ($tag as $k => $v) {
                $this->_setOrAppend($k, $v, $encodeHtml, $append, false);
            }

            return;
        }

        if (!is_string($tag) || $tag === '') {
            throw (new Exception('Tag must be non-empty string'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        if ($value === null) {
            $value = '';
        }

        $htmlValue = new HtmlValue();
        if ($encodeHtml) {
            $htmlValue->set($value);
        } else {
            $htmlValue->dangerouslySetHtml($value);
        }

        // set or append value
        if (!$throwIfNotFound && !$this->hasTag($tag)) {
            return;
        }

        $tagTree = $this->getTagTree($tag);
        if (!$append) {
            $this->emptyTagTree($tagTree);
        }
        $tagTree->add($htmlValue);
    }

    /**
     * This function will replace region referred by $tag to a new content.
     *
     * If tag is found inside template several times, all occurrences are
     * replaced.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function set($tag, string $value = null): self
    {
        $this->_setOrAppend($tag, $value, true, false);

        return $this;
    }

    /**
     * Same as set(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function trySet($tag, string $value = null): self
    {
        $this->_setOrAppend($tag, $value, true, false, false);

        return $this;
    }

    /**
     * Set value of a tag to a HTML content. The value is set without
     * encoding, so you must be sure to sanitize.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function dangerouslySetHtml($tag, string $value = null): self
    {
        $this->_setOrAppend($tag, $value, false, false);

        return $this;
    }

    /**
     * See dangerouslySetHtml() but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function tryDangerouslySetHtml($tag, string $value = null): self
    {
        $this->_setOrAppend($tag, $value, false, false, false);

        return $this;
    }

    /**
     * Add more content inside a tag.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function append($tag, ?string $value): self
    {
        $this->_setOrAppend($tag, $value, true, true);

        return $this;
    }

    /**
     * Same as append(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function tryAppend($tag, ?string $value): self
    {
        $this->_setOrAppend($tag, $value, true, true, false);

        return $this;
    }

    /**
     * Add more content inside a tag. The content is appended without
     * encoding, so you must be sure to sanitize.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function dangerouslyAppendHtml($tag, ?string $value): self
    {
        $this->_setOrAppend($tag, $value, false, true);

        return $this;
    }

    /**
     * Same as dangerouslyAppendHtml(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array<string, string>|Model          $tag
     * @param ($tag is array|Model ? never : string|null) $value
     *
     * @return $this
     */
    public function tryDangerouslyAppendHtml($tag, ?string $value): self
    {
        $this->_setOrAppend($tag, $value, false, true, false);

        return $this;
    }

    /**
     * Empty contents of specified region. If region contains sub-hierarchy,
     * it will be also removed.
     *
     * @param string|list<string> $tag
     *
     * @return $this
     */
    public function del($tag): self
    {
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->del($t);
            }

            return $this;
        }

        $tagTree = $this->getTagTree($tag);
        \Closure::bind(static function () use ($tagTree) {
            $tagTree->children = [];
        }, null, TagTree::class)();

        // TODO prune unreachable nodes
        // $template->rebuildTagsIndex();

        return $this;
    }

    /**
     * Similar to del() but won't throw exception if tag is not present.
     *
     * @param string|list<string> $tag
     *
     * @return $this
     */
    public function tryDel($tag): self
    {
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->tryDel($t);
            }

            return $this;
        }

        if ($this->hasTag($tag)) {
            $this->del($tag);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function loadFromFile(string $filename): self
    {
        if ($this->tryLoadFromFile($filename) !== false) {
            return $this;
        }

        throw (new Exception('Unable to read template from file'))
            ->addMoreInfo('filename', $filename);
    }

    /**
     * Same as load(), but will not throw an exception.
     *
     * @return $this|false
     */
    public function tryLoadFromFile(string $filename)
    {
        // realpath() is slow on Windows, so cache it and dedup only directories
        $filenameBase = basename($filename);
        $filename = dirname($filename);
        if (!isset(self::$_realpathCache[$filename])) {
            self::$_realpathCache[$filename] = realpath($filename);
        }
        $filename = self::$_realpathCache[$filename];
        if ($filename === false) {
            return false;
        }
        $filename .= '/' . $filenameBase;

        if (!isset(self::$_filesCache[$filename])) {
            $data = @file_get_contents($filename);
            if ($data !== false) {
                $data = preg_replace('~(?:\r\n?|\n)$~s', '', $data); // always trim end NL
            }
            self::$_filesCache[$filename] = $data;
        }

        $str = self::$_filesCache[$filename];
        if ($str === false) {
            return false;
        }

        $this->loadFromString($str);

        return $this;
    }

    /**
     * @return $this
     */
    public function loadFromString(string $str): self
    {
        $this->parseTemplate($str);

        return $this;
    }

    protected function parseTemplateTree(array &$inputReversed, string $openedTag = null): TagTree
    {
        $tagTree = new TagTree($this, $openedTag ?? self::TOP_TAG);

        $chunk = array_pop($inputReversed);
        if ($chunk !== '') {
            $tagTree->add((new HtmlValue())->dangerouslySetHtml($chunk));
        }

        while (($tag = array_pop($inputReversed)) !== null) {
            $firstChar = substr($tag, 0, 1);
            if ($firstChar === '/') { // is closing tag
                $tag = substr($tag, 1);
                if ($openedTag === null
                    || ($tag !== '' && $tag !== $openedTag)) {
                    throw (new Exception('Template parse error: tag was not opened'))
                        ->addMoreInfo('opened_tag', $openedTag)
                        ->addMoreInfo('tag', $tag);
                }

                $openedTag = null;

                break;
            }

            // is new/opening tag
            $childTagTree = $this->parseTemplateTree($inputReversed, $tag);
            $this->tagTrees[$tag] = $childTagTree;
            $tagTree->addTag($tag);

            $chunk = array_pop($inputReversed);
            if ($chunk !== null && $chunk !== '') {
                $tagTree->add((new HtmlValue())->dangerouslySetHtml($chunk));
            }
        }

        if ($openedTag !== null) {
            throw (new Exception('Template parse error: tag is not closed'))
                ->addMoreInfo('tag', $openedTag);
        }

        return $tagTree;
    }

    protected function parseTemplate(string $str): void
    {
        $cKey = static::class . "\0" . $str;
        if (!isset(self::$_parseCache[$cKey])) {
            // expand self-closing tags {$tag} -> {tag}{/tag}
            $str = preg_replace('~\{\$([\w\-:]+)\}~', '{\1}{/\1}', $str);

            $input = preg_split('~\{(/?[\w\-:]*)\}~', $str, -1, \PREG_SPLIT_DELIM_CAPTURE);
            $inputReversed = array_reverse($input); // reverse to allow to use fast array_pop()

            $this->tagTrees = [];
            try {
                $this->tagTrees[self::TOP_TAG] = $this->parseTemplateTree($inputReversed);
                $tagTrees = $this->tagTrees;

                if (self::$_parseCacheParentTemplate === null) {
                    $cKeySelfEmpty = self::class . "\0";
                    self::$_parseCache[$cKeySelfEmpty] = [];
                    try {
                        self::$_parseCacheParentTemplate = new self();
                    } finally {
                        unset(self::$_parseCache[$cKeySelfEmpty]);
                    }
                }
                $parentTemplate = self::$_parseCacheParentTemplate;

                \Closure::bind(static function () use ($tagTrees, $parentTemplate) {
                    foreach ($tagTrees as $tagTree) {
                        $tagTree->parentTemplate = $parentTemplate;
                    }
                }, null, TagTree::class)();
                self::$_parseCache[$cKey] = $tagTrees;
            } finally {
                $this->tagTrees = [];
            }
        }

        $this->tagTrees = $this->cloneTagTrees(self::$_parseCache[$cKey]);
    }

    public function toLoadableString(string $region = self::TOP_TAG): string
    {
        $res = [];
        foreach ($this->getTagTree($region)->getChildren() as $v) {
            if ($v instanceof HtmlValue) {
                $res[] = $v->getHtml();
            } elseif ($v instanceof TagTree) {
                $tag = $v->getTag();
                $tagInnerStr = $this->toLoadableString($tag);
                $res[] = $tagInnerStr === ''
                    ? '{$' . $tag . '}'
                    : '{' . $tag . '}' . $tagInnerStr . '{/' . $tag . '}';
            } else {
                throw (new Exception('Value class has no save support'))
                    ->addMoreInfo('value_class', get_class($v));
            }
        }

        return implode('', $res);
    }

    public function renderToHtml(string $region = null): string
    {
        return $this->renderTagTreeToHtml($this->getTagTree($region ?? self::TOP_TAG));
    }

    protected function renderTagTreeToHtml(TagTree $tagTree): string
    {
        $res = [];
        foreach ($tagTree->getChildren() as $v) {
            if ($v instanceof HtmlValue) {
                $res[] = $v->getHtml();
            } elseif ($v instanceof TagTree) {
                $res[] = $this->renderTagTreeToHtml($v);
            } elseif ($v instanceof self) {
                $res[] = $v->renderToHtml();
            } else {
                throw (new Exception('Unexpected value class'))
                    ->addMoreInfo('value_class', get_class($v));
            }
        }

        return implode('', $res);
    }
}
