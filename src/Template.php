<?php

declare(strict_types=1);

namespace atk4\ui;

use atk4\data\Model;

/**
 * This class is a lightweight template engine. It's based around operating with
 * chunks of HTML code and the main aims are:
 *  - completely remove any logic from templates
 *  - speed up template parsing and manipulation speed.
 */
class Template implements \ArrayAccess
{
    use \atk4\core\AppScopeTrait;

    /** @const string */
    public const TOP_TAG = '_top';

    /** @var array */
    private static $_filesCache = [];
    /** @var array */
    private static $_parseTemplateCache = [];

    // {{{ Properties of a template

    /**
     * This is a parsed contents of the template organized inside an array. This
     * structure makes it very simple to modify any part of the array.
     *
     * @var array
     */
    private $template;

    /**
     * List of all tags found inside template implementing faster access when manipulating the template.
     *
     * @var array
     */
    private $tagsIndex;

    /**
     * Contains information about where the template was loaded from.
     *
     * @var string
     */
    protected $source;

    // }}}

    // {{{ Core methods - initialization

    // Template creation, interface functions

    public function __construct(string $template = '')
    {
        $this->loadTemplateFromString($template);
    }

    private function exceptionAddMoreInfo(Exception $e): Exception
    {
        $e->addMoreInfo('tags', implode(', ', array_keys($this->tagsIndex)));
        $e->addMoreInfo('template', $this->template);
        $e->addMoreInfo('source', $this->source);

        return $e;
    }

    // }}}

    // {{{ Tag manipulation

    /**
     * This is a helper method which returns reference to element of template
     * array referenced by a tag.
     *
     * Because there might be multiple tags and getTagRef is
     * returning only one template, it will return the first
     * occurrence:
     *
     * {greeting}hello{/},  {greeting}world{/}
     *
     * calling &getTagRef('greeting') will return reference to &'hello';
     *
     * @param int|string|null $ref Null to return the first tag
     */
    protected function &getTagRef(string $tag, $ref = null): array
    {
        if ($ref !== null) {
            if (!isset($this->tagsIndex[$tag][$ref])) {
                throw $this->exceptionAddMoreInfo(
                    (new Exception('Tag not found in template'))
                        ->addMoreInfo('tag', $tag . '#' . $ref)
                );
            }

            $path = $this->tagsIndex[$tag][$ref];
        } else {
            if ($tag === self::TOP_TAG) {
                return $this->template;
            }

            $tag = explode('#', $tag, 2)[0];
            if (!isset($this->tagsIndex[$tag])) {
                throw $this->exceptionAddMoreInfo(
                    (new Exception('Tag not found in template'))
                        ->addMoreInfo('tag', $tag)
                );
            }

            $path = reset($this->tagsIndex[$tag]);
        }

        $vRef = &$this->template;
        foreach ($path as $k) {
            $vRef = &$vRef[$k];
        }

        return $vRef;
    }

    protected function getTagRefs(string $tag): array
    {
        if ($tag === self::TOP_TAG) {
            return [&$this->template];
        }

        $tag = explode('#', $tag, 2)[0];
        if (!isset($this->tagsIndex[$tag])) {
            throw $this->exceptionAddMoreInfo(
                (new Exception('Tag not found in template'))
                    ->addMoreInfo('tag', $tag)
            );
        }

        $vsRef = [];
        foreach ($this->tagsIndex[$tag] as $ref => $ignore) {
            $vsRef[$ref] = &$this->getTagRef($tag, $ref);
        }

        return $vsRef;
    }

    /**
     * Checks if template has defined a specified tag.
     * If multiple tags are passed in as array, then return true if all of them exist.
     *
     * @param string|array $tag
     */
    public function hasTag($tag): bool
    {
        // check if all tags exist
        if (is_array($tag)) {
            foreach ($tag as $t) {
                if (!$this->hasTag($t)) {
                    return false;
                }
            }

            return true;
        }

        $tag = explode('#', $tag, 2)[0];

        return isset($this->tagsIndex[$tag]) || $tag === self::TOP_TAG;
    }

    /**
     * Re-create tags index from scratch for the whole template.
     */
    protected function rebuildTagsIndex(): void
    {
        $this->tagsIndex = [];
        $this->rebuildTagsIndexRegion([], $this->template);
    }

    private function rebuildTagsIndexRegion(array $path, array $template): void
    {
        $path[] = null;

        foreach ($template as $tag => $val) {
            if (is_numeric($tag)) {
                continue;
            }

            $path[array_key_last($path)] = $tag;

            [$tag, $ref] = explode('#', $tag, 2);

            $this->tagsIndex[$tag][$ref] = $path;
            if (is_array($val)) {
                $this->rebuildTagsIndexRegion($path, $val);
            }
        }
    }

    // }}}

    // {{{ Manipulating contents of tags

    protected function _emptyRef(array &$ref): void
    {
        foreach ($ref as $k => $v) {
            if (is_array($v)) {
                $this->_emptyRef($ref[$k]);
            } else {
                unset($ref[$k]);
            }
        }
    }

    /**
     * Internal method for setting or appending content in $tag.
     *
     * If tag contains another tags, these tags are set to empty values.
     *
     * @param string|array|Model $tag
     * @param string             $value
     * @param bool               $encode Should we HTML encode content
     */
    protected function _setOrAppend($tag, $value = null, $encode = true, $append = false, $throwIfNotFound = true): void
    {
        // check tag
        if ($tag instanceof Model) {
            $tag = $this->app->ui_persistence->typecastSaveRow($tag, $tag->get());
        }

        // $tag passed as associative array [tag=>value]
        // in this case we don't throw exception if tags don't exist
        if (is_array($tag) && $value === null) {
            foreach ($tag as $t => $v) {
                $this->_setOrAppend($t, $v, $encode, $append, false);
            }

            return;
        }

        if (!$tag) {
            throw (new Exception('Tag must not be empty'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        // check value
        if (!is_scalar($value) && $value !== null) {
            throw (new Exception('Value must be scalar'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        // encode value
        $value = (string) $value; // TODO, better to remove later in favor of strong string type

        if ($encode) {
            $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        }

        // ignore not existent tags
        if (!$throwIfNotFound && !$this->hasTag($tag)) {
            return;
        }

        // set or append value
        $template = $this->getTagRefs($tag);
        foreach ($template as &$ref) {
            if (!$append) {
                $this->_emptyRef($ref);
            }
            $ref[] = $value;
        }
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
     * @param bool               $encode Should we HTML encode content
     *
     * @return $this
     */
    public function set($tag, $value = null, $encode = true)
    {
        $this->_setOrAppend($tag, $value, $encode, false, true);

        return $this;
    }

    /**
     * Same as set(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function trySet($tag, $value = null, bool $encode = true)
    {
        $this->_setOrAppend($tag, $value, $encode, false, false);

        return $this;
    }

    /**
     * Set value of a tag to a HTML content. The value is set without
     * encoding, so you must be sure to sanitize.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function setHtml($tag, $value = null)
    {
        $this->_setOrAppend($tag, $value, false, false, true);

        return $this;
    }

    /**
     * See setHtml() but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function trySetHtml($tag, $value = null)
    {
        $this->_setOrAppend($tag, $value, false, false, false);

        return $this;
    }

    /**
     * Add more content inside a tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function append($tag, $value, bool $encode = true)
    {
        $this->_setOrAppend($tag, $value, $encode, true, true);

        return $this;
    }

    /**
     * Same as append(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function tryAppend($tag, $value, bool $encode = true)
    {
        $this->_setOrAppend($tag, $value, $encode, true, false);

        return $this;
    }

    /**
     * Add more content inside a tag. The content is appended without
     * encoding, so you must be sure to sanitize.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function appendHtml($tag, $value)
    {
        $this->_setOrAppend($tag, $value, false, true, true);

        return $this;
    }

    /**
     * Same as append(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function tryAppendHtml($tag, $value)
    {
        $this->_setOrAppend($tag, $value, false, true, false);

        return $this;
    }

    /**
     * Get value of the tag. Note that this may contain an array
     * if tag contains a structure.
     */
    public function get(string $tag): array
    {
        return $this->getTagRef($tag);
    }

    /**
     * Empty contents of specified region. If region contains sub-hierarchy,
     * it will be also removed.
     *
     * IMPORTANT: This does not dispose of the tags which were previously
     * inside the region. This causes some severe pitfalls for the users
     * and ideally must be checked and proper errors must be generated.
     *
     * @param string|array $tag
     *
     * @return $this
     */
    public function del($tag)
    {
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->del($t);
            }

            return $this;
        }

        if ($tag === self::TOP_TAG) {
            $this->loadTemplateFromString('');
        } else {
            $template = $this->getTagRefs($tag);
            foreach ($template as &$ref) {
                $ref = [];
            }
            $this->rebuildTagsIndex();
        }

        return $this;
    }

    /**
     * Similar to del() but won't throw exception if tag is not present.
     *
     * @param string|array $tag
     *
     * @return $this
     */
    public function tryDel($tag)
    {
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->tryDel($t);
            }

            return $this;
        }

        return $this->hasTag($tag) ? $this->del($tag) : $this;
    }

    // }}}

    // {{{ ArrayAccess support
    public function offsetExists($name)
    {
        return $this->hasTag($name);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }

    public function offsetSet($name, $val)
    {
        $this->set($name, $val);
    }

    public function offsetUnset($name)
    {
        $this->del($name, null);
    }

    // }}}

    // {{{ Template Manipulations

    /**
     * Executes call-back for each matching tag in the template.
     *
     * @param string|array $tag
     *
     * @return $this
     */
    public function eachTag($tag, \Closure $fx)
    {
        // array support
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->eachTag($t, $fx);
            }

            return $this;
        }

        foreach ($this->getTagRefs($tag) as $ref => &$vRef) {
            $vRef = [(string) $fx($this->renderRegion($vRef), $tag . '#' . $ref)];
        }

        return $this;
    }

    /**
     * Creates a new template using portion of existing template.
     *
     * @return static
     */
    public function cloneRegion(string $tag)
    {
        $template = new static();
        $template->app = $this->app;
        if ($tag === self::TOP_TAG) {
            $template->template = $this->template;
            $template->source = $this->source;
        } else {
            $template->template = [self::TOP_TAG . '#0' => $this->get($tag)];
            $template->source = 'clone of tag "' . $tag . '" from template "' . $this->source . '"';
        }
        $template->rebuildTagsIndex();

        return $template;
    }

    // }}}

    // {{{ Template Loading

    /**
     * Loads template from a specified file.
     *
     * @return $this
     */
    public function load(string $filename)
    {
        if ($this->tryLoad($filename) !== false) {
            return $this;
        }

        throw (new Exception('Unable to read template from file'))
            ->addMoreInfo('file', $filename);
    }

    /**
     * Same as load(), but will not throw exception.
     *
     * @return $this|false
     */
    public function tryLoad(string $filename)
    {
        $filename = realpath($filename);
        if (!isset(self::$_filesCache[$filename])) {
            self::$_filesCache[$filename] = $filename !== false ? file_get_contents($filename) : false;
        }

        if (self::$_filesCache[$filename] === false) {
            return false;
        }

        $str = preg_replace('~(?:\r\n?|\n)$~s', '', self::$_filesCache[$filename]); // always trim end NL
        $this->loadTemplateFromString($str);
        $this->source = 'loaded from file: ' . $filename;

        return $this;
    }

    /**
     * Initialize current template from the supplied string.
     *
     * @return $this
     */
    public function loadTemplateFromString(string $str)
    {
        $this->source = 'string: ' . $str;
        $this->template = [];
        $this->tagsIndex = [];
        $this->tagCnt = [];

        $this->parseTemplate($str);

        return $this;
    }

    // }}}

    // {{{ Template Parsing Engine

    /**
     * Used for adding unique tag alternatives. E.g. if your template has
     * {$name}{$name}, then first would become 'name#0' and second 'name#1', but
     * both would still respond to 'name' tag.
     *
     * @var array
     */
    private $tagCnt = [];

    /**
     * Register tag and return unique tag name.
     */
    protected function regTag(string $tag): string
    {
        if (!isset($this->tagCnt[$tag])) {
            $this->tagCnt[$tag] = -1;
        }
        $nextIndex = ++$this->tagCnt[$tag];

        return $tag . '#' . $nextIndex;
    }

    protected function parseTemplateTree(array &$inputReversed, string $openedTag = null): array
    {
        $prefix = array_pop($inputReversed);
        $template = $prefix !== '' ? [$prefix] : [];

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
            $fullTag = $this->regTag($tag);
            $template[$fullTag] = $this->parseTemplateTree($inputReversed, $tag);

            $chunk = array_pop($inputReversed);
            if ($chunk !== null && $chunk !== '') {
                $template[] = $chunk;
            }
        }

        if ($openedTag !== null) {
            throw (new Exception('Template parse error: tag is not closed'))
                ->addMoreInfo('tag', $openedTag);
        }

        return $template;
    }

    /**
     * Deploys parse recursion.
     */
    protected function parseTemplate(string $str): void
    {
        $cKey = $str;
        if (!isset(self::$_parseTemplateCache[$cKey])) {
            // expand self-closing tags {$tag} -> {tag}{/tag}
            $str = preg_replace('~\{\$([-_:\w]+)\}~', '{\1}{/\1}', $str);

            $input = preg_split('~\{(/?[-_:\w]*)\}~', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
            $inputReversed = array_reverse($input); // reverse to allow to use fast array_pop()

            $this->template = $this->parseTemplateTree($inputReversed);
            $this->rebuildTagsIndex();

            self::$_parseTemplateCache[$cKey] = [$this->template, $this->tagsIndex];
            $this->template = null;
            $this->tagsIndex = null;
        }

        [$this->template, $this->tagsIndex] = self::$_parseTemplateCache[$cKey];
    }

    // }}}

    // {{{ Template Rendering

    /**
     * Render either a whole template or a specified region. Returns
     * current contents of a template.
     */
    public function render(string $region = null): string
    {
        return $this->renderRegion($region !== null ? $this->get($region) : $this->template);
    }

    /**
     * Walk through the template array collecting the values
     * and returning them as a string.
     */
    protected function renderRegion(array $template): string
    {
        $res = [];
        foreach ($template as $val) {
            $res[] = is_array($val) ? $this->renderRegion($val) : $val;
        }

        return implode('', $res);
    }

    // }}}
}
