<?php



namespace atk4\ui\demo;

class Demo extends \atk4\ui\Columns
{
    public $left;
    public $right;
    public static $isInitialized = false;
    public $highlightDefaultStyle = 'dark';
    public $left_width = 8;
    public $right_width = 8;

    public function init(): void
    {
        parent::init();
        $this->addClass('celled');

        $this->left = $this->addColumn($this->left_width);
        $this->right = $this->addColumn($this->right_width);
    }

    public function setCode($code, $lang = 'php')
    {
        $this->highLightCode();
        \atk4\ui\View::addTo(\atk4\ui\View::addTo($this->left, ['element' => 'pre']), ['element' => 'code'])->addClass($lang)->set($code);
        $app = $this->right;
        $app->db = $this->app->db;
        eval($code);
    }

    public function highLightCode()
    {
        if (!self::$isInitialized) {
            $this->app->requireCSS('//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.16.2/build/styles/' . $this->highlightDefaultStyle . '.min.css');
            $this->app->requireJS('//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.16.2/build/highlight.min.js');
            $this->js(true, (new \atk4\ui\jsChain('hljs'))->initHighlighting());
            self::$isInitialized = true;
        }
    }
}
