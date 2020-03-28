<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\data\Model;

/**
 * This class is a lightweight template engine. It's based around operating with
 * chunks of HTML code and the main aims are:.
 *
 *  - completely remove any logic from templates
 *  - speed up template parsing and manipulation speed
 *
 * @author      Romans <romans@agiletoolkit.org>
 * @copyright   MIT
 *
 * @version     3.0
 *
 * ==[ Version History ]=======================================================
 * 1.0          First public version (released with AModules3 alpha)
 * 1.1          Added support for "_top" tag
 *              Removed support for permanent tags
 *              Much more comments and other fixes
 * 2.0          Reimplemented template parsing, now doing it with regexps
 * 3.0          Re-integrated as part of Agile UI under MIT license
 */
class Template implements \ArrayAccess
{
    use \atk4\core\AppScopeTrait;
    use \atk4\core\StaticAddToTrait;

    // {{{ Properties of a template

    /**
     * This array contains list of all tags found inside template implementing
     * faster access when manipulating the template.
     *
     * @var array
     */
    public $tags = [];

    /**
     * This is a parsed contents of the template organized inside an array. This
     * structure makes it very simple to modify any part of the array.
     *
     * @var array
     */
    public $template = [];

    /**
     * Contains information about where the template was loaded from.
     *
     * @var string
     */
    public $source = null;

    /** @var string */
    public $default_exception = 'Exception_Template';

    // }}}

    // {{{ Core methods - initialization

    // Template creation, interface functions

    /**
     * Construct template.
     *
     * @param string $template
     */
    public function __construct($template = null)
    {
        if ($template !== null) {
            $this->loadTemplateFromString($template);
        }
    }

    /**
     * Clone template.
     */
    public function __clone()
    {
        $this->template = unserialize(serialize($this->template));

        unset($this->tags);
        $this->rebuildTags();
    }

    /**
     * Returns relevant exception class. Use this method with "throw".
     *
     * @param string $message Static text of exception
     * @param int    $code    Optional error code
     *
     * @return Exception
     */
    public function exception($message = 'Undefined Exception', $code = null)
    {
        $arg = [
            $message,
            'tags'     => implode(', ', array_keys($this->tags)),
            'template' => $this->template,
        ];

        if ($this->source) {
            $arg['source'] = $this->source;
        }

        return new Exception($arg, $code);
    }

    // }}}

    // {{{ Tag manipulation

    /**
     * Returns true if specified tag is a top-tag of the template.
     *
     * Since Agile Toolkit 4.3 this tag is always called _top.
     *
     * @param string $tag
     *
     * @return bool
     */
    public function isTopTag($tag)
    {
        return $tag == '_top';
    }

    /**
     * This is a helper method which returns reference to element of template
     * array referenced by a said tag.
     *
     * Because there might be multiple tags and getTagRef is
     * returning only one template, it will return the first
     * occurrence:
     *
     * {greeting}hello{/},  {greeting}world{/}
     *
     * calling &getTagRef('greeting') will return reference to &array('hello');
     *
     * @param string $tag
     *
     * @return &array
     */
    public function &getTagRef($tag)
    {
        if ($this->isTopTag($tag)) {
            return $this->template;
        }

        $a = explode('#', $tag);
        $tag = array_shift($a);
        //$ref = array_shift($a); // unused
        if (!isset($this->tags[$tag])) {
            throw $this->exception('Tag not found in Template')
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('tags', implode(', ', array_keys($this->tags)));
        }

        // return first array element only
        reset($this->tags[$tag]);
        $key = key($this->tags[$tag]) !== null ? key($this->tags[$tag]) : null;

        return $this->tags[$tag][$key];
    }

    /**
     * For methods which execute action on several tags, this method
     * will return array of templates. You can then iterate
     * through the array and update all the template values.
     *
     * {greeting}hello{/},  {greeting}world{/}
     *
     * calling $template =& getTagRefList('greeting') will point
     * $template towards array(&array('hello'),&array('world'));
     *
     * If $tag is specified as an array, then $template will
     * contain all occurrences of all tags from the array.
     *
     * @param string|array $tag
     *
     * @return array of references to template tags
     */
    public function getTagRefList($tag)
    {
        if (is_array($tag)) {
            $res = [];
            foreach ($tag as $t) {
                $list = $this->getTagRefList($t);
                foreach ($list as &$tpl) {
                    $res[] = &$tpl;
                }
            }

            return $res;
        }

        if ($this->isTopTag($tag)) {
            return [&$this->template];
        }

        $a = explode('#', $tag);
        $tag = array_shift($a);
        $ref = array_shift($a);
        if (!$ref) {
            if (!isset($this->tags[$tag])) {
                throw $this->exception('Tag not found in Template')
                    ->addMoreInfo('tag', $tag)
                    ->addMoreInfo('tags', implode(', ', array_keys($this->tags)));
            }

            return $this->tags[$tag];
        }

        if (!isset($this->tags[$tag][$ref - 1])) {
            throw $this->exception('Tag not found in Template')
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('tags', implode(', ', array_keys($this->tags)));
        }

        //return [&$this->tags[$tag][$ref - 1]];
        return $this->tags[$tag][$ref - 1];
    }

    /**
     * Checks if template has defined a specified tag.
     * If multiple tags are passed in as array, then return true if all of them exist.
     *
     * @param string|array $tag
     *
     * @return bool
     */
    public function hasTag($tag)
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
        $a = explode('#', $tag);
        $tag = array_shift($a);
        //$ref = array_shift($a); // unused

        return isset($this->tags[$tag]) || $this->isTopTag($tag);
    }

    /**
     * Re-create tag indexes from scratch for the whole template.
     */
    public function rebuildTags()
    {
        $this->tags = [];

        $this->rebuildTagsRegion($this->template);
    }

    /**
     * Add tags from a specified region.
     *
     * @param array $template
     */
    protected function rebuildTagsRegion(&$template)
    {
        foreach ($template as $tag => &$val) {
            if (is_numeric($tag)) {
                continue;
            }

            $a = explode('#', $tag);
            $key = array_shift($a);
            $ref = array_shift($a);

            $this->tags[$key][$ref] = &$val;
            if (is_array($val)) {
                $this->rebuildTagsRegion($val);
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
     * @param bool               $append Should we append value instead of changing it?
     * @param bool               $strict Should we throw exception if tag not found?
     *
     * @return $this
     */
    protected function _setOrAppend($tag, $value = null, $encode = true, $append = false, $strict = true)
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

            return $this;
        }

        if (!$tag) {
            throw new Exception(['Tag is not set', 'tag' => $tag, 'value' => $value]);
        }

        // check value
        if (!is_scalar($value) && $value !== null) {
            throw new Exception(['Value should be scalar', 'tag' => $tag, 'value' => $value]);
        }

        // encode value
        if ($encode) {
            $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        }

        // if no value, then set respective conditional regions to empty string
        if (substr($tag, -1) != '?' && ($value === false || !strlen((string) $value))) {
            $this->trySet($tag . '?', '');
        }

        // ignore not existent tags
        if (!$strict && !$this->hasTag($tag)) {
            return $this;
        }

        // set or append value
        $template = $this->getTagRefList($tag);
        foreach ($template as &$ref) {
            if ($append) {
                $ref[] = $value;
            } else {
                $ref = [$value];
            }
        }

        return $this;
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
        return $this->_setOrAppend($tag, $value, $encode, false, true);
    }

    /**
     * Same as set(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     * @param bool               $encode
     *
     * @return $this
     */
    public function trySet($tag, $value = null, $encode = true)
    {
        return $this->_setOrAppend($tag, $value, $encode, false, false);
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
    public function setHTML($tag, $value = null)
    {
        return $this->_setOrAppend($tag, $value, false, false, true);
    }

    /**
     * See setHTML() but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     *
     * @return $this
     */
    public function trySetHTML($tag, $value = null)
    {
        return $this->_setOrAppend($tag, $value, false, false, false);
    }

    /**
     * Add more content inside a tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     * @param bool               $encode
     *
     * @return $this
     */
    public function append($tag, $value, $encode = true)
    {
        return $this->_setOrAppend($tag, $value, $encode, true, true);
    }

    /**
     * Same as append(), but won't generate exception for non-existing
     * $tag.
     *
     * @param string|array|Model $tag
     * @param string             $value
     * @param bool               $encode
     *
     * @return $this
     */
    public function tryAppend($tag, $value, $encode = true)
    {
        return $this->_setOrAppend($tag, $value, $encode, true, false);
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
    public function appendHTML($tag, $value)
    {
        return $this->_setOrAppend($tag, $value, false, true, true);
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
    public function tryAppendHTML($tag, $value)
    {
        return $this->_setOrAppend($tag, $value, false, true, false);
    }

    /**
     * Get value of the tag. Note that this may contain an array
     * if tag contains a structure.
     *
     * @param string $tag
     *
     * @return array
     */
    public function get($tag)
    {
        return /*&*/$this->getTagRef($tag); // return array not referenced to it
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

        if ($this->isTopTag($tag)) {
            $this->loadTemplateFromString('');

            return $this;
        }

        $template = $this->getTagRefList($tag);
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
     * @param callable     $callable
     *
     * @return $this
     */
    public function eachTag($tag, $callable)
    {
        if (!$this->hasTag($tag)) {
            return $this;
        }

        // array support
        if (is_array($tag)) {
            foreach ($tag as $t) {
                $this->eachTag($t, $callable);
            }

            return $this;
        }

        // $tag should be string here
        $template = $this->getTagRefList($tag);
        if ($template != $this->template) {
            foreach ($template as $key => $templ) {
                $ref = $tag . '#' . ($key + 1);
                $this->tags[$tag][$key] = [call_user_func($callable, $this->recursiveRender($templ), $ref)];
            }
        } else {
            $this->tags[$tag][0] = [call_user_func($callable, $this->recursiveRender($template), $tag)];
        }

        return $this;
    }

    /**
     * Creates a new template using portion of existing template.
     *
     * @param string $tag
     *
     * @return self
     */
    public function cloneRegion($tag)
    {
        if ($this->isTopTag($tag)) {
            return clone $this;
        }

        $cl = get_class($this);
        $n = new $cl();
        $n->app = $this->app;
        $n->template = unserialize(serialize(['_top#1' => $this->get($tag)]));
        $n->rebuildTags();
        $n->source = 'clone (' . $tag . ') of template ' . $this->source;

        return $n;
    }

    // }}}

    // {{{ Template Loading

    /**
     * Loads template from a specified file.
     *
     * @param string $filename Template file name
     *
     * @throws Exception
     *
     * @return $this
     */
    public function load($filename)
    {
        if ($t = $this->tryLoad($filename)) {
            return $t;
        }

        throw new Exception([
            'Unable to read template from file',
            'cwd'  => getcwd(),
            'file' => $filename,
        ]);
    }

    /**
     * Same as load(), but will not throw exception.
     *
     * @param string $filename Template file name
     *
     * @return $this|false
     */
    public function tryLoad($filename)
    {
        if (is_readable($filename) && is_file($filename)) {
            $str = preg_replace('~(?:\r\n?|\n)$~s', '', file_get_contents($filename)); // load file and trim end NL
            $this->loadTemplateFromString($str);
            $this->source = 'loaded from file: ' . $filename;

            return $this;
        }

        return false;
    }

    /**
     * Initialize current template from the supplied string.
     *
     * @param string $str
     *
     * @return $this
     */
    public function loadTemplateFromString($str)
    {
        $this->source = 'string: ' . $str;
        $this->template = $this->tags = [];
        if (!$str) {
            return;
        }
        $this->tag_cnt = [];

        /* First expand self-closing tags {$tag} -> {tag}{/tag} */
        $str = preg_replace('/{\$([-_:\w]+)}/', '{\1}{/\1}', $str);

        $this->parseTemplate($str);

        return $this;
    }

    // }}}

    // {{{ Template Parsing Engine

    /**
     * Used for adding unique tag alternatives. E.g. if your template has
     * {$name}{$name}, then first would become 'name#1' and second 'name#2', but
     * both would still respond to 'name' tag.
     *
     * @var array
     */
    private $tag_cnt = [];

    /**
     * Register tags and return unique tag name.
     *
     * @param string $tag tag name
     *
     * @return string unique tag name
     */
    protected function regTag($tag)
    {
        if (!isset($this->tag_cnt[$tag])) {
            $this->tag_cnt[$tag] = 0;
        }

        return $tag . '#' . (++$this->tag_cnt[$tag]);
    }

    /**
     * Recursively find nested tags inside a string, converting them to array.
     *
     * @param array &$input
     * @param array &$template
     */
    protected function parseTemplateRecursive(&$input, &$template)
    {
        if (!is_array($input) || empty($input)) {
            return;
        }

        while (true) {
            $tag = current($input);
            next($input);

            if ($tag === false) {
                break;
            }

            $firstChar = substr($tag, 0, 1);

            switch ($firstChar) {
                // is closing TAG
                case '/':
                    return substr($tag, 1);
                break;

                // is TAG
                case '$':

                    $tag = substr($tag, 1);

                    $full_tag = $this->regTag($tag);
                    $template[$full_tag] = '';  // empty value
                    $this->tags[$tag][] = &$template[$full_tag];

                    // eat next chunk
                    $chunk = current($input); next($input);
                    if ($chunk !== false && $chunk !== null) {
                        $template[] = $chunk;
                    }

                break;

                // recurse
                default:

                    $full_tag = $this->regTag($tag);

                    // next would be prefix
                    $prefix = current($input); next($input);
                    $template[$full_tag] = ($prefix === false || $prefix === null) ? [] : [$prefix];

                    $this->tags[$tag][] = &$template[$full_tag];

                    $this->parseTemplateRecursive($input, $template[$full_tag]);

                    $chunk = current($input); next($input);
                    if ($chunk !== false && !empty($chunk)) {
                        $template[] = $chunk;
                    }

                break;
            }
        }
    }

    /**
     * Deploys parse recursion.
     *
     * @param string $str
     */
    protected function parseTemplate($str)
    {
        $tag = '/{([\/$]?[-_:\w]*[\?]?)}/';

        $input = preg_split($tag, $str, -1, PREG_SPLIT_DELIM_CAPTURE);

        $prefix = current($input);
        next($input);
        $this->template = [$prefix];

        $this->parseTemplateRecursive($input, $this->template);
    }

    // }}}

    // {{{ Template Rendering

    /**
     * Render either a whole template or a specified region. Returns
     * current contents of a template.
     *
     * @param string $region
     *
     * @return string
     */
    public function render($region = null)
    {
        if ($region) {
            return $this->recursiveRender($this->get($region));
        }

        return $this->recursiveRender($this->template);
    }

    /**
     * Walk through the template array collecting the values
     * and returning them as a string.
     *
     * @param array $template
     *
     * @return string
     */
    protected function recursiveRender($template)
    {
        $output = '';
        foreach ($template as $val) {
            if (is_array($val)) {
                $output .= $this->recursiveRender($val);
            } else {
                $output .= $val;
            }
        }

        return $output;
    }

    // }}}

    // {{{ Debugging functions

    /*
     * Returns HTML-formatted code with all tags
     *
    public function _getDumpTags($template)
    {
        $s = '';
        foreach ($template as $key => $val) {
            if (is_array($val)) {
                $s .= '<font color="blue">{'.$key.'}</font>'.
                    $this->_getDumpTags($val).'<font color="blue">{/'.$key.'}</font>';
            } else {
                $s .= htmlspecialchars($val);
            }
        }

        return $s;
    }
    /*** TO BE REFACTORED ***/

    /*
     * Output all tags
     *
    public function dumpTags()
    {
        echo '"'.$this->_getDumpTags($this->template).'"';
    }
    /*** TO BE REFACTORED ***/
    // }}}
}
