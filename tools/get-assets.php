<?php

include'../vendor/autoload.php';
class GetAssets extends \atk4\ui\App 
{
    public $always_run = false;
    public $catch_exceptions = false;

    function requireJS($path)
    {
        $file = '../public/'.basename($path);
        echo "Downloading $path into $file..\n";
        if(@copy($path, $file)) {
            echo "  ok\n";
        } else {
            echo "  failed\n";
        }
    }
    function requireCSS($path)
    {
        return $this->requireJS($path);
    }
}

$app = new GetAssets();
$app->initIncludes();
