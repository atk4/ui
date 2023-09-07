<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\App;

trait ReplaceAppRequestTrait
{
    public function replaceAppRequestGet(App $app, array $newQueryData): void
    {
        $requestProperty = new \ReflectionProperty(App::class, 'request');
        $requestProperty->setAccessible(true);

        $request = $app->getRequest()->withQueryParams($newQueryData);

        $requestProperty->setValue($app, $request);
    }

    public function replaceAppRequestPost(App $app, array $newPostData): void
    {
        $requestProperty = new \ReflectionProperty(App::class, 'request');
        $requestProperty->setAccessible(true);

        $request = $app->getRequest()->withParsedBody($newPostData);

        $requestProperty->setValue($app, $request);
    }
}
