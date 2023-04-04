<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\HtmlTemplate;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;

class AppTest extends TestCase
{
    use CreateAppTrait;

    public function testTemplateClassDefault(): void
    {
        $app = $this->createApp();

        static::assertInstanceOf(
            HtmlTemplate::class,
            $app->loadTemplate('html.html')
        );
    }

    public function testTemplateClassCustom(): void
    {
        $anotherTemplateClass = new class() extends HtmlTemplate {
        };

        $app = $this->createApp();
        $app->templateClass = get_class($anotherTemplateClass);

        static::assertInstanceOf(
            get_class($anotherTemplateClass),
            $app->loadTemplate('html.html')
        );
    }

    public function testHeaderNormalize(): void
    {
        $app = $this->createApp();
        $app->setResponseHeader('cache-control', '');

        $app->setResponseHeader('content-type', 'Xy');
        static::assertSame(['Content-Type' => ['Xy']], $app->getResponse()->getHeaders());

        $app->setResponseHeader('CONTENT-type', 'xY');
        static::assertSame(['Content-Type' => ['xY']], $app->getResponse()->getHeaders());

        $app->setResponseHeader('content-TYPE', '');
        static::assertSame([], $app->getResponse()->getHeaders());
    }

    public function testUnexpectedOutputLateError(): void
    {
        $app = $this->createApp();

        ob_start();
        $testStr = 'direct output test';
        try {
            echo $testStr;

            $this->expectException(LateOutputError::class);
            $this->expectExceptionMessage('Unexpected output detected');
            $app->terminateHtml('');
        } finally {
            static::assertSame($testStr, ob_get_contents());
            ob_end_clean();
        }
    }

    public function testTextResponse(): void
    {
        ob_start();
        $app = $this->createApp();
        $content = 'Hello, world!';
        $app->setResponseHeader('Content-Type', 'text/plain');
        $app->terminate($content); // @todo throws headers already sent
        static::assertSame($content, ob_get_contents());
        ob_end_clean();
    }

    // throws headers already sent exception so not sure how to write this test for stream output
    public function testStreamResponse(): void
    {
        ob_start();
        $app = $this->createApp();

        $pattern = str_repeat('0123456789ABCDEF', 65536); // 1Mb
        $chunks = 1024 * 1; // 1024 chunks = 1Gb
        $tempFile = tempnam(sys_get_temp_dir(), 'test');

        // Generate the data and write it to a temporary file
        $fh = fopen($tempFile, 'wb');
        for ($i = 0; $i < $chunks; $i++) {
            fwrite($fh, $pattern);
        }
        fclose($fh);

        // Send the data using a file stream response
        $factory = new Psr17Factory();
        $stream = $factory->createStreamFromFile($tempFile);
        $app->setResponseHeader('Content-Type', 'text/plain');
        $app->terminate($stream); // @todo headers already sent


        // Check that the response was not materialized somewhere
        static::assertEmpty(ob_get_contents());
    }
}
