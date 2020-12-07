<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

class GetAssets extends \Atk4\Ui\App
{
    public $always_run = false;
    public $catch_exceptions = false;

    public function requireJs($path, $isAsync = false, $isDefer = false)
    {
        $file = 'public/' . basename($path);
        echo "Downloading {$path} into {$file}..\n";
        if (@copy($path, $file)) {
            echo "  ok\n";
        } else {
            echo "  failed\n";
        }
    }

    public function requireCss($path)
    {
        return $this->requireJs($path);
    }
}

mkdir('public');
$app = new GetAssets();
$app->initIncludes();
