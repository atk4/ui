<?php

include_once __DIR__ . '/vendor/autoload.php';
class GetAssets extends \atk4\ui\App
{
    public $always_run = false;
    public $catch_exceptions = false;

    public function requireJS($path)
    {
        $file = 'public/' . basename($path);
        echo "Downloading $path into $file..\n";
        if (@copy($path, $file)) {
            echo "  ok\n";
        } else {
            echo "  failed\n";
        }
    }

    public function requireCSS($path)
    {
        return $this->requireJS($path);
    }
}

mkdir('public');
$app = new GetAssets();
$app->initIncludes();
