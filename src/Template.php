<?php

declare(strict_types=1);

namespace atk4\ui;
//require_once __DIR__ . '/TemplateNew.php';class Template extends TemplateNew{}return;
class Template implements \ArrayAccess
{
    use \atk4\core\AppScopeTrait;
    use \atk4\core\DiContainerTrait; // needed for StaticAddToTrait, removed once php7.2 support is dropped
    use \atk4\core\StaticAddToTrait;

    /** @const string */
    public const TOP_TAG = '_top';

    // must be declared because of ArrayAccess interface
    public function offsetExists($name) { return $this->__call('offsetExists', func_get_args()); }
    public function offsetGet($name) { return $this->__call('offsetGet', func_get_args()); }
    public function offsetSet($name, $val) { $this->__call('offsetSet', func_get_args()); }
    public function offsetUnset($name) { $this->__call('offsetUnset', func_get_args()); }

    /** @var TemplateNew */
    private $tnew;
    /** @var TemplateOld */
    private $told;

    public function __construct(string $template = '') {
        $this->tnew = new TemplateNew();
        $this->told = new TemplateOld();

        $this->loadTemplateFromString($template);
    }

    public function __clone() {
        $this->tnew = clone $this->tnew;
        $this->told = clone $this->told;

        $this->diffAfter();
    }

    private function getImpl(): object {
        return $this->tnew;
    }

    private function getImpl2(): object {
        return $this->told;
    }

    public function __isset($name)
    {
        foreach (['res' => $this->getImpl(), 'res2' => $this->getImpl2()] as $resVarName => $impl) {
            ${$resVarName} = \Closure::bind(static function () use ($impl, $name) {
                return isset($impl->{$name});
            }, null, $impl)();
        }

        $this->diffAfter();

        return $res;
    }

    public function &__get($name)
    {
        foreach (['res' => $this->getImpl(), 'res2' => $this->getImpl2()] as $resVarName => $impl) {
            ${$resVarName} = &\Closure::bind(static function &() use ($impl, $name) {
                return $impl->{$name};
            }, null, $impl)();
        }

        $this->diffAfter();

        return $res;
    }

    public function __set($name, $value)
    {
        foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
            \Closure::bind(static function () use ($impl, $name, $value) {
                $impl->{$name} = $value;
            }, null, $impl)();
        }

        $this->diffAfter();
    }

    public function __unset($name)
    {
        foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
            \Closure::bind(static function () use ($impl, $name) {
                unset($impl->{$name});
            }, null, $impl)();
        }

        $this->diffAfter();
    }

    private static $cloningFlag = false;
    public function &__call($name, $args)
    {
        // set app
        foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
            $impl->app = $this->app;
        }

        try {
            foreach (['res' => $this->getImpl(), 'res2' => $this->getImpl2()] as $resVarName => $impl) {
                ${$resVarName} = &\Closure::bind(static function &() use ($impl, $name, $args) {
                    if ((new \ReflectionClass($impl))->getMethod($name)->returnsReference()) {
                        return $impl->{$name}(...$args);
                    }

                    $v = $impl->{$name}(...$args);

                    return $v;
                }, null, $impl)();
            }
        } /*catch (\Throwable $e) {
            throw new \Error(); // only for debug, we do not except any exception...
        } */finally {
            // unset app
//            foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
//                unset($impl->app);
//            }
        }

        $this->diffAfter();

        if (!self::$cloningFlag && $res instanceof TemplateNew || $res instanceof TemplateOld) {
            self::$cloningFlag = true;
            try {
                $origRes = $res;
                $res = new static();
                $origProps = \Closure::bind(static function () use ($origRes) {
                    return get_object_vars($origRes);
                }, null, $origRes)();
                foreach ($origProps as $k => $v) {
                    $res->{$k} = $v;
                }
                $res->rebuildTagsIndex();
            } finally {
                self::$cloningFlag = false;
            }
        }

        return $res;
    }

    private function diffAfter() {
        // compare state with new refactored template
        $tnew = $this->tnew;
        $tnewTemplate = \Closure::bind(function() use($tnew) { return $tnew->template; }, null, TemplateNew::class)();
        $tnewTagsIndex = \Closure::bind(function() use($tnew) { return $tnew->tagsIndex; }, null, TemplateNew::class)();
        $told = $this->told;
        $toldTemplate = \Closure::bind(function() use($told) { return $told->template; }, null, TemplateOld::class)();
        $toldTags = \Closure::bind(function() use($told) { return $told->tags; }, null, TemplateOld::class)();

        $tnewTags = [];
        foreach ($tnewTagsIndex as $tag => $paths) {
            foreach ($paths as $ref => $path) {
                $v = $tnewTemplate;
                foreach ($path as $p) {
                    if (!isset($v[$p])) { // up-too-date template should never reach this code block
                        continue 2;
                    }
                    $v = $v[$p];
                }
                $tnewTags[$tag][$ref] = $v;
            }
        }

        foreach (debug_backtrace() as $f) { // getTagRef/getTagRefs test can not succeed
            if (isset($f['class']) && $f['class'] === tests\TemplateTest::class) {
                return;
            }
        }

        if ($tnew->render() !== $told->render()) {
            echo trim($tnew->render()) . "\n" . trim($told->render()) ."\n\n\n"  ;

            var_dump($tnew->render());
            var_dump($told->render());

            if ($tnewTemplate !== $toldTemplate) {
                echo 'new template' . "\n";
                print_r($tnewTemplate);
                echo 'old template' . "\n";
                print_r($toldTemplate);
                throw new \Error();exit;//echo (new Exception ('Template mismatch'))->getHtml();ob_end_flush(); ob_start(function() { exit; }); echo 'x'; ob_end_flush(); // exit immediatelly
            }

            if ($tnewTags !== $toldTags) {
                print_r($tnewTags);
                print_r($toldTags);
                throw new \Error();exit;//echo (new Exception ('Template mismatch'))->getHtml();ob_end_flush(); ob_start(function() { exit; }); echo 'x'; ob_end_flush(); // exit immediatelly
            }
        }
    }
}
