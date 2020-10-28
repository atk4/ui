<?php

declare(strict_types=1);

namespace atk4\ui;

trait HtmlTemplateDualRenderTrait
{
    use \atk4\core\AppScopeTrait;
    use \atk4\core\DiContainerTrait; // needed for StaticAddToTrait, removed once php7.2 support is dropped
    use \atk4\core\StaticAddToTrait;

    // must be declared because of ArrayAccess interface
    public function offsetExists($name)
    {
        return $this->__call('offsetExists', func_get_args());
    }

    public function offsetGet($name)
    {
        return $this->__call('offsetGet', func_get_args());
    }

    public function offsetSet($name, $val)
    {
        $this->__call('offsetSet', func_get_args());
    }

    public function offsetUnset($name)
    {
        $this->__call('offsetUnset', func_get_args());
    }

    /** @var HtmlTemplateNew */
    private $tnew;
    /** @var HtmlTemplateOld */
    private $told;

    /** @var array */
    private $executedOperations = [];

    private function logOperation(): void
    {
//        $this->executedOperations[] = array_map(function (array $frame) {
//            if (($frame['type'] ?? null) === '->') {
//                unset($frame['type']);
//            }
//
//            if (isset($frame['args'])) {
//                if ($frame['args'] === []) {
//                    unset($frame['args']);
//                } else {
//                    $frame['args'] = array_map(function ($v) { return is_object($v) ? 'object - ' . get_class($v) : $v; }, $frame['args']);
//                }
//            }
//
//            return $frame;
//        }, array_slice(debug_backtrace(0), 1));
    }

    public function __construct(string $template = '')
    {
        $this->logOperation();

        $this->tnew = new HtmlTemplateNew();
        $this->told = new HtmlTemplateOld();

        $this->loadFromString($template);
    }

    public function __clone()
    {
        $this->logOperation();

        $this->tnew = clone $this->tnew;
        $this->told = clone $this->told;

        $this->diffAfter();
    }

    private function getImpl(): object
    {
        return $this->tnew;
    }

    private function getImpl2(): object
    {
        return $this->told;
    }

    public function __isset($name)
    {
        $this->logOperation();

        throw new \Error('Unallowed "' . $name . '" property access');
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
        $this->logOperation();

        throw new \Error('Unallowed "' . $name . '" property access');
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
        $this->logOperation();

        throw new \Error('Unallowed "' . $name . '" property access');
        foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
            \Closure::bind(static function () use ($impl, $name, $value) {
                $impl->{$name} = $value;
            }, null, $impl)();
        }

        $this->diffAfter();
    }

    public function __unset($name)
    {
        $this->logOperation();

        throw new \Error('Unallowed "' . $name . '" property access');
        foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
            \Closure::bind(static function () use ($impl, $name) {
                unset($impl->{$name});
            }, null, $impl)();
        }

        $this->diffAfter();
    }

    public function &__call($name, $args)
    {
        $this->logOperation();

        // set app
        foreach ([$this->getImpl(), $this->getImpl2()] as $impl) {
            if ($this->issetApp()) {
                $impl->setApp($this->getApp());
            }
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
        } catch (\Exception $e) {
//            $this->executedOperations[] = 'Exception: ' . (string) $e;
//            print_r(array_reverse($this->executedOperations));
            throw $e;
        }

        $this->diffAfter();

        // if return is template, keep it wrapped
        if ($res instanceof HtmlTemplateNew || $res instanceof HtmlTemplateOld) {
            $resNew = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
            $resNew->tnew = $this->getImpl() === $this->tnew ? $res : $res2;
            $resNew->told = $this->getImpl() === $this->tnew ? $res2 : $res;
            $res = $resNew;
            unset($resNew);
            $res->logOperation();

            $res->diffAfter();
        }

        return $res;
    }

    private function diffAfter()
    {
        // compare state with new refactored template
        $tnew = $this->tnew;
        $tnewTagTrees = \Closure::bind(function () use ($tnew) { return $tnew->tagTrees; }, null, HtmlTemplateNew::class)();
        $told = $this->told;
        $toldTemplate = \Closure::bind(function () use ($told) { return $told->template; }, null, HtmlTemplateOld::class)();
        $toldTagsIndex = \Closure::bind(function () use ($told) { return $told->tagsIndex; }, null, HtmlTemplateOld::class)();

//        $tnewTags = [];
//        foreach ($tnewTagsIndex as $tag => $paths) {
//            foreach ($paths as $ref => $path) {
//                $v = $tnewTemplate;
//                foreach ($path as $p) {
//                    if (!isset($v[$p])) { // up-too-date template should never reach this code block
//                        continue 2;
//                    }
//                    $v = $v[$p];
//                }
//                $tnewTags[$tag][$ref] = $v;
//            }
//        }

        foreach (debug_backtrace() as $f) { // getTagRef/getTagRefs test can not succeed
            if (isset($f['class']) && $f['class'] === tests\TemplateTest::class) {
                return;
            }
        }

        if ($tnew->toLoadableString() !== $told->toLoadableString()) {
            echo 'new-';
            var_dump($tnew->toLoadableString());
            echo 'old-';
            var_dump($told->toLoadableString());

//            print_r(array_reverse($this->executedOperations));

            throw new \Error('Template mismatch');
        }

        if ($tnew->renderToHtml() !== $told->renderToHtml()) {
            echo 'new-render-';
            var_dump($tnew->renderToHtml());
            echo 'old-render-';
            var_dump($told->renderToHtml());

//            print_r(array_reverse($this->executedOperations));

            throw new \Error('Template render mismatch');
//            if ($tnewTemplate !== $toldTemplate) {
//                echo 'new template' . "\n";
//                print_r($tnewTemplate);
//                echo 'old template' . "\n";
//                print_r($toldTemplate);
//
//                throw new \Error('Stop execution');
//                exit; //echo (new Exception ('Template mismatch'))->getHtml();ob_end_flush(); ob_start(function() { exit; }); echo 'x'; ob_end_flush(); // exit immediatelly
//            }
//
//            if ($tnewTags !== $toldTagsIndex) {
//                print_r($tnewTags);
//                print_r($toldTagsIndex);
//
//                throw new \Error('Stop execution');
//                exit; //echo (new Exception ('Template mismatch'))->getHtml();ob_end_flush(); ob_start(function() { exit; }); echo 'x'; ob_end_flush(); // exit immediatelly
//            }
        }
    }
}
