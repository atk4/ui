<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;
use Atk4\Ui\HtmlTemplate\TagTree;
use Atk4\Ui\HtmlTemplate\Value as HtmlValue;

class HtmlTemplate implements \ArrayAccess
{
    use \Atk4\Core\AppScopeTrait;

    /** @const string */
    public const TOP_TAG = '_top';

    /** @var array */
    private static $_filesCache = [];
    /** @var TagTree[tag][] */
    private static $_parseCache = [];

    /** @var TagTree[tag] */
    private $tagTrees;

    public function __construct(string $template = '')
    {
        $this->loadFromString($template);
    }

    public function _hasTag(string $tag): bool
    {
        return isset($this->tagTrees[$tag]);
    }

    /**
     * @param string|array $tag
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
            throw (new Exception('Tag not found in template'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('template_tags', array_keys($this->tagTrees));
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

    public function cloneRegion(string $tag): self
    {
        $template = new static();
        $template->tagTrees = $template->cloneTagTrees($this->tagTrees);

        // rename top tag tree
        $topTagTree = $template->tagTrees[$tag];
        unset($template->tagTrees[$tag]);
        $template->tagTrees[self::TOP_TAG] = $topTagTree;
        $topTag = self::TOP_TAG;
        \Closure::bind(function () use ($topTagTree, $topTag) {
            $topTagTree->tag = $topTag;
        }, null, TagTree::class)();

        // TODO prune unreachable nodes
        // $template->rebuildTagsIndex();

        if ($this->issetApp()) {
            $template->setApp($this->getApp());
        }

        return $template;
    }

    protected function _unsetFromTagTree(TagTree $tagTree, $k): void
    {
        \Closure::bind(function () use ($tagTree, $k) {
            unset($tagTree->children[$k]);
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
     * @param string|array|Model $tag
     * @param string             $value
     */
    protected function _setOrAppend($tag, $value = null, bool $encodeHtml = true, bool $append = false, $throwIfNotFound = true): void
    {
        if ($tag instanceof Model) {
            if (!$encodeHtml) {
                throw new Exception('HTML is not allowed to be dangerously set from Model');
            }

            $tag = $this->getApp()->ui_persistence->typecastSaveRow($tag, $tag->get());
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
            throw (new Exception('Tag must be not empty string'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        if (!is_scalar($value) && $value !== null) {
            throw (new Exception('Value must be scalar'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        // TODO remove later in favor of strong string type
        $value = (string) $value;

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
     * ALTERNATIVE USE(2) of this function is to pass associative array as
     * a single argument. This will assign multiple tags with one call.
     * Sample use is:
     *
     *  set($_GET);
     *
     * would read and set multiple region values from $_GET array.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function set($tag, $value = null): self
    {
        if (func_num_args() > 2) { // remove in v2.5
            throw new \Error('3rd param $encode is no longer supported, use dangerouslySetHtml method instead');
        }

        $this->_setOrAppend($tag, $value, true, false);

        return $this;
    }

    /**
     * Same as set(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function trySet($tag, $value = null): self
    {
        if (func_num_args() > 2) { // remove in v2.5
            throw new \Error('3rd param $encode is no longer supported, use tryDangerouslySetHtml method instead');
        }

        $this->_setOrAppend($tag, $value, true, false, false);

        return $this;
    }

    /**
     * Set value of a tag to a HTML content. The value is set without
     * encoding, so you must be sure to sanitize.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function dangerouslySetHtml($tag, $value = null): self
    {
        $this->_setOrAppend($tag, $value, false, false);

        return $this;
    }

    /**
     * See dangerouslySetHtml() but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function tryDangerouslySetHtml($tag, $value = null): self
    {
        $this->_setOrAppend($tag, $value, false, false, false);

        return $this;
    }

    /**
     * Add more content inside a tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function append($tag, $value): self
    {
        if (func_num_args() > 2) { // remove in v2.5
            throw new \Error('3rd param $encode is no longer supported, use dangerouslyAppendHtml method instead');
        }

        $this->_setOrAppend($tag, $value, true, true);

        return $this;
    }

    /**
     * Same as append(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function tryAppend($tag, $value): self
    {
        if (func_num_args() > 2) { // remove in v2.5
            throw new \Error('3rd param $encode is no longer supported, use tryDangerouslyAppendHtml method instead');
        }

        $this->_setOrAppend($tag, $value, true, true, false);

        return $this;
    }

    /**
     * Add more content inside a tag. The content is appended without
     * encoding, so you must be sure to sanitize.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function dangerouslyAppendHtml($tag, $value): self
    {
        $this->_setOrAppend($tag, $value, false, true);

        return $this;
    }

    /**
     * Same as dangerouslyAppendHtml(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     */
    public function tryDangerouslyAppendHtml($tag, $value): self
    {
        $this->_setOrAppend($tag, $value, false, true, false);

        return $this;
    }

    /**
     * @deprecated use "dangerouslySetHtml" method instead - will be removed in v2.5
     */
    public function setHtml($tag, $value = null)
    {
        'trigger_error'('Method is deprecated. Use dangerouslySetHtml instead', E_USER_DEPRECATED);

        return $this->dangerouslySetHtml($tag, $value);
    }

    /**
     * @deprecated use "tryDangerouslySetHtml" method instead - will be removed in v2.5
     */
    public function trySetHtml($tag, $value = null)
    {
        'trigger_error'('Method is deprecated. Use tryDangerouslySetHtml instead', E_USER_DEPRECATED);

        return $this->tryDangerouslySetHtml($tag, $value);
    }

    /**
     * @deprecated use "dangerouslyAppendHtml" method instead - will be removed in v2.5
     */
    public function appendHtml($tag, $value)
    {
        'trigger_error'('Method is deprecated. Use dangerouslyAppendHtml instead', E_USER_DEPRECATED);

        return $this->dangerouslyAppendHtml($tag, $value);
    }

    /**
     * @deprecated use "tryDangerouslyAppendHtml" method instead - will be removed in v2.5
     */
    public function tryAppendHtml($tag, $value)
    {
        'trigger_error'('Method is deprecated. Use tryDangerouslyAppendHtml instead', E_USER_DEPRECATED);

        return $this->tryDangerouslyAppendHtml($tag, $value);
    }

    /**
     * @deprecated use "loadFromFile" method instead - will be removed in v2.5
     */
    public function load(string $filename)
    {
        'trigger_error'('Method is deprecated. Use loadFromFile instead', E_USER_DEPRECATED);

        return $this->loadFromFile($filename);
    }

    /**
     * @deprecated use "tryLoadFromFile" method instead - will be removed in v2.5
     */
    public function tryLoad(string $filename)
    {
        'trigger_error'('Method is deprecated. Use tryLoadFromFile instead', E_USER_DEPRECATED);

        return $this->tryLoadFromFile($filename);
    }

    /**
     * @deprecated use "loadFromString" method instead - will be removed in v2.5
     */
    public function loadTemplateFromString(string $template = '')
    {
        'trigger_error'('Method is deprecated. Use loadFromString instead', E_USER_DEPRECATED);

        return $this->loadFromString($template);
    }

    /**
     * @deprecated use "renderToHtml" method instead - will be removed in v2.5
     */
    public function render(string $region = null)
    {
        'trigger_error'('Method is deprecated. Use renderToHtml instead', E_USER_DEPRECATED);

        return $this->renderToHtml($region);
    }

    /**
     * Empty contents of specified region. If region contains sub-hierarchy,
     * it will be also removed.
     *
     * @param string|array $tag
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
        \Closure::bind(function () use ($tagTree) {
            $tagTree->children = [];
        }, null, TagTree::class)();

        // TODO prune unreachable nodes
        // $template->rebuildTagsIndex();

        return $this;
    }

    /**
     * Similar to del() but won't throw exception if tag is not present.
     *
     * @param string|array $tag
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

    // {{{ ArrayAccess support - will be removed in v2.5
    public function offsetExists($name)
    {
        'trigger_error'('Array access is deprecated. Use hasTag method instead', E_USER_DEPRECATED);

        return $this->hasTag($name);
    }

    public function offsetGet($name)
    {
        'trigger_error'('Array access is deprecated. Use get method instead', E_USER_DEPRECATED);

        return $this->getTagTree($name);
    }

    public function offsetSet($name, $val)
    {
        'trigger_error'('Array access is deprecated. Use set method instead', E_USER_DEPRECATED);

        $this->set($name, $val);
    }

    public function offsetUnset($name)
    {
        'trigger_error'('Array access is deprecated. Use del method instead', E_USER_DEPRECATED);

        $this->del($name);
    }

    // }}}

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
        $filename = realpath($filename);
        if (!isset(self::$_filesCache[$filename])) {
            $data = $filename !== false ? file_get_contents($filename) : false;
            if ($data !== false) {
                $data = preg_replace('~(?:\r\n?|\n)$~s', '', $data); // always trim end NL
            }
            self::$_filesCache[$filename] = $data;
        }

        if (self::$_filesCache[$filename] === false) {
            return false;
        }

        $this->loadFromString(self::$_filesCache[$filename]);

        return $this;
    }

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
        $cKey = $str;
        if (!isset(self::$_parseCache[$cKey])) {
            // expand self-closing tags {$tag} -> {tag}{/tag}
            $str = preg_replace('~\{\$([\w\-:]+)\}~', '{\1}{/\1}', $str);

            $input = preg_split('~\{(/?[\w\-:]*)\}~', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
            $inputReversed = array_reverse($input); // reverse to allow to use fast array_pop()

            $this->tagTrees = [];
            $this->tagTrees[self::TOP_TAG] = $this->parseTemplateTree($inputReversed);

            self::$_parseCache[$cKey] = $this->tagTrees;
            $this->tagTrees = null;
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
