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
class TemplateOld implements \ArrayAccess
{
    use \atk4\core\AppScopeTrait;
    use \atk4\core\DiContainerTrait; // needed for StaticAddToTrait, removed once php7.2 support is dropped
    use \atk4\core\StaticAddToTrait;

    /** @const string */
    public const TOP_TAG = '_top';

    /** @var array */
    private static $_filesCache = [];

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
    private $tags;

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

    public function __clone()
    {
        $this->template = unserialize(serialize($this->template));

        $this->tags = null;
        $this->rebuildTagsIndex();
    }

    private function exceptionAddMoreInfo(Exception $ex): Exception
    {
        $ex->addMoreInfo('tags', implode(', ', array_keys($this->tags)));
        $ex->addMoreInfo('template', $this->template);
        $ex->addMoreInfo('source', $this->source);

        return $ex;
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
     * calling &getTagRef('greeting') will return reference to &array('hello');
     */
    public function &getTagRef(string $tag): array
    {
        if ($tag === self::TOP_TAG) {
            return $this->template;
        }

        $tag = explode('#', $tag, 2)[0];
        if (!isset($this->tags[$tag])) {
            throw $this->exceptionAddMoreInfo(
                (new Exception('Tag not found in template'))
                    ->addMoreInfo('tag', $tag)
            );
        }

        // return first array element only
        reset($this->tags[$tag]);
        $key = key($this->tags[$tag]) !== null ? key($this->tags[$tag]) : null;

        return $this->tags[$tag][$key];
    }

    protected function getTagRefs(string $tag): array
    {
        if (is_array($tag)) {
            $res = [];
            foreach ($tag as $t) {
                $list = $this->getTagRefs($t);
                foreach ($list as &$tpl) {
                    $res[] = &$tpl;
                }
            }

            return $res;
        }

        if ($tag === self::TOP_TAG) {
            return [&$this->template];
        }

        if (strpos($tag, '#') === false) {
            if (!isset($this->tags[$tag])) {
                throw $this->exceptionAddMoreInfo(
                    (new Exception('Tag not found in template'))
                        ->addMoreInfo('tag', $tag)
                );
            }

            return $this->tags[$tag];
        }

        [$tag, $ref] = explode('#', $tag, 2);

        if (!isset($this->tags[$tag][$ref - 1])) {
            throw $this->exceptionAddMoreInfo(
                (new Exception('Tag not found in template'))
                    ->addMoreInfo('tag', $tag)
            );
        }

        //return [&$this->tags[$tag][$ref - 1]];
        return $this->tags[$tag][$ref - 1];
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

        // check if tag exist
        $tag = explode('#', $tag, 2)[0];

        return isset($this->tags[$tag]) || $tag === self::TOP_TAG;
    }

    /**
     * Re-create tags index from scratch for the whole template.
     */
    public function rebuildTagsIndex()
    {
        $this->tags = [];

        $this->rebuildTagsIndexRegion($this->template);
    }

    protected function rebuildTagsIndexRegion(&$template)
    {
        foreach ($template as $tag => &$val) {
            if (is_numeric($tag)) {
                continue;
            }

            [$tag, $ref] = explode('#', $tag, 2);

            $this->tags[$tag][$ref] = &$val;
            if (is_array($val)) {
                $this->rebuildTagsIndexRegion($val);
            }
        }
    }

    // }}}

    // {{{ Manipulating contents of tags

    /**
     * Internal method for setting or appending content in $tag.
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
            throw (new Exception('Tag is not set'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        // check value
        if (!is_scalar($value) && $value !== null) {
            throw (new Exception('Value should be scalar'))
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('value', $value);
        }

        // encode value
        $value = (string) $value; // TODO, better to remove later in favor of strong string type

        if ($encode) {
            $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        }

        // if no value, then set respective conditional regions to empty string
        if (substr($tag, -1) !== '?' && ($value === false || !strlen((string) $value))) {
            $this->trySet($tag . '?', '');
        }

        // ignore not existent tags
        if (!$throwIfNotFound && !$this->hasTag($tag)) {
            return;
        }

        // set or append value
        $template = $this->getTagRefs($tag);
        foreach ($template as &$ref) {
            if ($append) {
                $ref[] = $value;
            } else {
                $ref = [$value];
            }
        }

        return;
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
                $this->tryDel($t);
            }

            return $this;
        }

        if ($tag === self::TOP_TAG) {
            $this->loadTemplateFromString('');

            return $this;
        }

        $template = $this->getTagRefs($tag);
        foreach ($template as &$ref) {
            $ref = [];
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
            return $this->del($tag);
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
    public function eachTag($tag, \Closure $callable)
    {
        // array support
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->eachTag($t, $callable);
            }

            return $this;
        }

        $template = $this->getTagRefs($tag);
        foreach ($template as $key => $templ) {
            $ref = $tag . '#' . ($key + 1);
            $this->tags[$tag][$key] = [call_user_func($callable, $this->renderRegion($templ), $ref)];
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
        if ($tag === self::TOP_TAG) {
            return clone $this;
        }

        $cl = static::class;
        $n = new $cl();
        $n->app = $this->app;
        $n->template = unserialize(serialize(['_top#1' => $this->get($tag)]));
        $n->rebuildTagsIndex();
        $n->source = 'clone (' . $tag . ') of template ' . $this->source;

        return $n;
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
        $this->tags = [];
        $this->tagCnt = [];

        if ($str !== '') {
            $this->parseTemplate($str);
        }

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

    protected function parseTemplateTree(array &$inputReversed, &$template)
    {
        if (!is_array($inputReversed) || empty($inputReversed)) {
            return;
        }

        while (true) {
            $tag = array_pop($inputReversed);

            if ($tag === null) {
                break;
            }

            if (substr($tag, 0, 1) === '/') {// is closing TAG
                return substr($tag, 1);
            }

            $full_tag = $this->regTag($tag);

            // next would be prefix
            $prefix = array_pop($inputReversed);
            $template[$full_tag] = ($prefix === false || $prefix === null || $prefix === '') ? [] : [$prefix];

            $this->tags[$tag][] = &$template[$full_tag];

            $this->parseTemplateTree($inputReversed, $template[$full_tag]);

            $chunk = array_pop($inputReversed);
            if ($chunk !== false && !empty($chunk)) {
                $template[] = $chunk;
            }
        }
    }

    /**
     * Deploys parse recursion.
     */
    protected function parseTemplate(string $str): void
    {


        // expand self-closing tags {$tag} -> {tag}{/tag}
        $str = preg_replace('~\{\$([-_:\w]+)\}~', '{\1}{/\1}', $str);

        $input = preg_split('~\{(/?[-_:\w]*)\}~', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        $inputReversed = array_reverse($input); // reverse to allow to use fast array_pop()

        $prefix = array_pop($inputReversed);
        if ($prefix !== '') {
            $this->template = [$prefix];
        }

        $this->parseTemplateTree($inputReversed, $this->template);
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
