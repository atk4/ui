<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use WebDriver\Element;

/**
 * Selenium2Driver driver with the following fixes:
 * - https://github.com/minkphp/MinkSelenium2Driver/pull/327
 * - https://github.com/minkphp/MinkSelenium2Driver/pull/328
 * - https://github.com/minkphp/MinkSelenium2Driver/pull/352
 * - https://github.com/minkphp/MinkSelenium2Driver/pull/359
 * .
 */
class MinkSeleniumDriver extends \Behat\Mink\Driver\Selenium2Driver
{
    use WarnDynamicPropertyTrait;

    public function __construct(\Behat\Mink\Driver\Selenium2Driver $driver) // @phpstan-ignore-line
    {
        $class = self::class;
        while (($class = get_parent_class($class)) !== false) {
            \Closure::bind(function () use ($driver) {
                foreach (get_object_vars($driver) as $k => $v) {
                    $this->{$k} = $v;
                }
            }, $this, $class)();
        }
    }

    public function getText($xpath): string
    {
        $text = $this->executeJsOnXpath($xpath, 'return {{ELEMENT}}.innerText;');
        $text = trim(preg_replace('~\s+~s', ' ', $text));

        return $text;
    }

    protected function createMinkElementFromWebDriverElement(Element $element): NodeElement
    {
        // WebDriver element contains only a temporary ID assigned by Selenium,
        // to create a Mink element we must build a xpath for it first
        $script = <<<'EOF'
            var buildXpathFromElement;
            buildXpathFromElement = function (element) {
                var tagNameLc = element.tagName.toLowerCase();
                if (element.parentElement === null) {
                    return '/' + tagNameLc;
                }

                if (element.id && document.querySelectorAll(tagNameLc + '#' + element.id).length === 1) {
                    return '//' + tagNameLc + '[@id=\'' + element.id + '\']';
                }

                var children = element.parentElement.children;
                var pos = 0;
                for (var i = 0; i < children.length; i++) {
                    if (children[i].tagName.toLowerCase() === tagNameLc) {
                        pos++;
                        if (children[i] === element) {
                            break;
                        }
                    }
                }

                var xpath = buildXpathFromElement(element.parentElement) + '/' + tagNameLc + '[' + pos + ']';

                return xpath;
            };

            return buildXpathFromElement(arguments[0]);
            EOF;
        $xpath = $this->getWebDriverSession()->execute([
            'script' => $script,
            'args' => [$element],
        ]);

        $minkElements = $this->find($xpath);
        if (count($minkElements) === 0) {
            throw new DriverException(sprintf('XPath "%s" built from WebDriver element did not find any element', $xpath));
        }
        if (count($minkElements) > 1) {
            throw new DriverException(sprintf('XPath "%s" built from WebDriver element find more than one element', $xpath));
        }

        return reset($minkElements);
    }

    private function findElement(string $xpath): Element
    {
        return $this->getWebDriverSession()->element('xpath', $xpath);
    }

    private function serializeExecuteArguments(array $args): array
    {
        foreach ($args as $k => $v) {
            if ($v instanceof NodeElement) {
                $args[$k] = $this->findElement($v->getXpath());
            } elseif (is_array($v)) {
                $args[$k] = $this->serializeExecuteArguments($v);
            }
        }

        return $args;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    private function unserializeExecuteResult($data)
    {
        if ($data instanceof Element) {
            return $this->createMinkElementFromWebDriverElement($data);
        } elseif (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->unserializeExecuteResult($v);
            }
        }

        return $data;
    }

    protected function executeJsOnXpath($xpath, $script, $sync = true)
    {
        $options = [
            'script' => str_replace('{{ELEMENT}}', 'arguments[0]', $script),
            'args' => [$this->findElement($xpath)],
        ];

        $result = $sync
            ? $this->getWebDriverSession()->execute($options)
            : $this->getWebDriverSession()->execute_async($options);

        return $this->unserializeExecuteResult($result);
    }

    public function executeScript($script, array $args = []): void
    {
        if (preg_match('~^function[\s\(]~', $script)) {
            $script = preg_replace('~;$~', '', $script);
            $script = '(' . $script . ')';
        }

        $this->getWebDriverSession()->execute([
            'script' => $script,
            'args' => $this->serializeExecuteArguments($args),
        ]);
    }

    public function evaluateScript($script, array $args = [])
    {
        if (strpos(trim($script), 'return ') !== 0) {
            $script = 'return ' . $script;
        }

        $result = $this->getWebDriverSession()->execute([
            'script' => $script,
            'args' => $this->serializeExecuteArguments($args),
        ]);

        return $this->unserializeExecuteResult($result);
    }

    public function wait($timeout, $condition, array $args = []): bool
    {
        $script = 'return (' . rtrim($condition, " \t\n\r;") . ');';
        $start = microtime(true);
        $end = $start + $timeout / 1000.0;

        do {
            $result = $this->getWebDriverSession()->execute([
                'script' => $script,
                'args' => $this->serializeExecuteArguments($args),
            ]);
            if ($result) {
                break;
            }
            usleep(10_000);
        } while (microtime(true) < $end);

        return (bool) $result;
    }

    public function dragTo($sourceXpath, $destinationXpath): void
    {
        $source = $this->findElement($sourceXpath);
        $destination = $this->findElement($destinationXpath);

        $this->getWebDriverSession()->moveto(['element' => $source->getID()]);

        $this->executeScript(<<<'EOF'
            var event = document.createEvent("HTMLEvents");

            event.initEvent("dragstart", true, true);
            event.dataTransfer = {};

            arguments[0].dispatchEvent(event);
            EOF, [$source]);

        $this->getWebDriverSession()->buttondown();
        if ($destination->getID() !== $source->getID()) {
            $this->getWebDriverSession()->moveto(['element' => $destination->getID()]);
        }
        $this->getWebDriverSession()->buttonup();

        $this->executeScript(<<<'EOF'
            var event = document.createEvent("HTMLEvents");

            event.initEvent("drop", true, true);
            event.dataTransfer = {};

            arguments[0].dispatchEvent(event);
            EOF, [$destination]);
    }
}
