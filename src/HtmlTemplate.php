<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

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
     * This is a helper method which populates an array pointing
     * to the place in the template referenced by a said tag.
     *
     * Because there might be multiple tags and getTagRef is
     * returning only one template, it will return the first
     * occurrence:
     *
     * {greeting}hello{/},  {greeting}world{/}
     *
     * calling getTagRef('greeting',$template) will point
     * second argument towards &array('hello');
     *
     * @param string $tag
     * @param array  $template
     *
     * @return $this
     */
    public function getTagRef($tag, &$template)
    {
        if ($this->isTopTag($tag)) {
            $template = &$this->template;

            return $this;
        }

        $a = explode('#', $tag);
        $tag = array_shift($a);
        //$ref = array_shift($a); // unused
        if (!isset($this->tags[$tag])) {
            throw $this->exception('Tag not found in Template')
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('tags', implode(', ', array_keys($this->tags)));
        }
        $template = reset($this->tags[$tag]);

        return $this;
    }

    /**
     * For methods which execute action on several tags, this method
     * will return array of templates. You can then iterate
     * through the array and update all the template values.
     *
     * {greeting}hello{/},  {greeting}world{/}
     *
     * calling getTagRefList('greeting',$template) will point
     * second argument towards array(&array('hello'),&array('world'));
     *
     * If $tag is specified as array, then $templates will
     * contain all occurrences of all tags from the array.
     *
     * @param string|array $tag
     * @param array        &$template
     *
     * @return bool
     */
    public function getTagRefList($tag, &$template)
    {
        if (is_array($tag)) {
            // TODO: test
            $res = [];
            foreach ($tag as $t) {
                $template = [];
                $this->getTagRefList($t, $te);

                foreach ($template as &$tpl) {
                    $res[] = &$tpl;
                }

                return true;
            }
        }

        if ($this->isTopTag($tag)) {
            $template = &$this->template;

            return false;
        }

        $a = explode('#', $tag);
        $tag = array_shift($a);
        $ref = array_shift($a);
        if (!$ref) {
            if (!isset($this->tags[$tag])) {
                throw $this->exception('Tag not found in Template')
                    ->addMoreInfo('tag', $tag);
            }
            $template = $this->tags[$tag];

            return true;
        }
        if (!isset($this->tags[$tag][$ref - 1])) {
            throw $this->exception('Tag not found in Template')
                ->addMoreInfo('tag', $tag);
        }
        $template = [&$this->tags[$tag][$ref - 1]];

        return true;
    }

    /**
     * Checks if template has defined a specified tag.
     *
     * @param string|array $tag
     *
     * @return bool
     */
    public function hasTag($tag)
    {
        if (is_array($tag)) {
            return true;
        }

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
     * @param mixed        $tag
     * @param string|array $value
     * @param bool         $encode
     *
     * @return $this
     */
    public function set($tag, $value = null, $encode = true)
    {
        if (!$tag) {
            return $this;
        }

        if (is_object($tag)) {
            $tag = $this->app->ui_persistence->typecastSaveRow($tag, $tag->get());
        }

        if (is_array($tag) && $value === null) {
            foreach ($tag as $s => $v) {
                $this->trySet($s, $v, $encode);
            }

            return $this;
        }

        if (is_array($value)) {
            return $this;
        }

        if (is_object($value)) {
            throw new Exception(['Value should not be an object', 'value'=>$value]);
        }

        if ($encode) {
            $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        }

        $this->getTagRefList($tag, $template);
        foreach ($template as &$ref) {
            $ref = [$value];
        }

        return $this;
    }

    /**
     * Set value of a tag to a HTML content. The value is set without
     * encoding, so you must be sure to sanitize.
     *
     * @param mixed        $tag
     * @param string|array $value
     * @param $this
     */
    public function setHTML($tag, $value = null)
    {
        return $this->set($tag, $value, false);
    }

    /**
     * See setHTML() but won't generate exception for non-existing
     * $tag.
     *
     * @param mixed        $tag
     * @param string|array $value
     * @param $this
     */
    public function trySetHTML($tag, $value = null)
    {
        return $this->trySet($tag, $value, false);
    }

    /**
     * Same as set(), but won't generate exception for non-existing
     * $tag.
     *
     * @param mixed        $tag
     * @param string|array $value
     * @param bool         $encode
     * @param $this
     */
    public function trySet($tag, $value = null, $encode = true)
    {
        if (is_array($tag)) {
            return $this->set($tag, $value, $encode);
        }

        return $this->hasTag($tag) ? $this->set($tag, $value, $encode) : $this;
    }

    /**
     * Add more content inside a tag.
     *
     * @param mixed        $tag
     * @param string|array $value
     * @param bool         $encode
     * @param $this
     */
    public function append($tag, $value, $encode = true)
    {
        if ($encode) {
            $value = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');
        }

        $this->getTagRefList($tag, $template);
        foreach ($template as &$ref) {
            $ref[] = $value;
        }

        return $this;
    }

    /**
     * Add more content inside a tag. The content is appended without
     * encoding, so you must be sure to sanitize.
     *
     * @param mixed        $tag
     * @param string|array $value
     *
     * @return $this
     */
    public function appendHTML($tag, $value)
    {
        return $this->append($tag, $value, false);
    }

    /**
     * Get value of the tag. Note that this may contain an array
     * if tag contains a structure.
     *
     * @param string $tag
     *
     * @return $this
     */
    public function get($tag)
    {
        $template = [];
        $this->getTagRef($tag, $template);

        return $template;
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

        $this->getTagRefList($tag, $template);
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

        if ($this->getTagRefList($tag, $template)) {
            foreach ($template as $key => $templ) {
                $ref = $tag.'#'.($key + 1);
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
        $n->source = 'clone ('.$tag.') of template '.$this->source;

        return $n;
    }

    // }}}

    // {{{ Template Loading

    /**
     * Loads template from a specified file.
     *
     * @param string $filename Template file name
     *
     * @return $this
     */
    public function load($filename)
    {
        if (!is_readable($filename)) {
            throw new Exception([
                'Unable to read template from file',
                'file' => $filename,
            ]);
        }
        $this->loadTemplateFromString(file_get_contents($filename));
        $this->source = 'loaded from file: '.$filename;

        return $this;
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
        $this->source = 'string: '.$str;
        $this->template = $this->tags = [];
        if (!$str) {
            return;
        }
        $this->tag_cnt = [];

        /* First expand self-closing tags {$tag} -> {tag}{/tag} */
        $str = preg_replace('/{\$([\w]+)}/', '{\1}{/\1}', $str);

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

        return $tag.'#'.(++$this->tag_cnt[$tag]);
    }

    /**
     * Recursively find nested tags inside a string, converting them to array.
     *
     * @param array $input
     * @param array $template
     *
     * @return string|null
     */
    protected function parseTemplateRecursive(&$input, &$template)
    {
        while (list(, $tag) = @each($input)) {

            // Closing tag
            if ($tag[0] == '/') {
                return substr($tag, 1);
            }

            if ($tag[0] == '$') {
                $tag = substr($tag, 1);
                $full_tag = $this->regTag($tag);
                $template[$full_tag] = '';  // empty value
                $this->tags[$tag][] = &$template[$full_tag];

                // eat next chunk
                $chunk = @each($input);
                if ($chunk[1]) {
                    $template[] = $chunk[1];
                }

                continue;
            }

            $full_tag = $this->regTag($tag);

            // Next would be prefix
            list(, $prefix) = @each($input);
            $template[$full_tag] = $prefix ? [$prefix] : [];

            $this->tags[$tag][] = &$template[$full_tag];

            $this->parseTemplateRecursive($input, $template[$full_tag]);

            $chunk = @each($input);
            if ($chunk[1]) {
                $template[] = $chunk[1];
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
        $tag = '/{([\/$]?[-_\w]*)}/';

        $input = preg_split($tag, $str, -1, PREG_SPLIT_DELIM_CAPTURE);

        list(, $prefix) = @each($input);
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
    protected function recursiveRender(&$template)
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
    public function _getDumpTags(&$template)
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
