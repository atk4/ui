<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Code;
use Atk4\Ui\Header;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Markdown highlight']);
Code::addTo($app, [<<<'EOD'
    # Header

    Text...

    - a
    - b
    EOD, 'language' => 'md']);

Header::addTo($app, ['PHP code highlight']);
Code::addTo($app, [<<<'EOD'
    <?php

    namespace Atk4\Ns;

    class Foo
    {
        public $tag = '<b>';
    }
    EOD, 'language' => 'php']);

Header::addTo($app, ['Empty code highlight']);
Code::addTo($app, ['', 'language' => 'php']);

// TODO https://github.com/highlightjs/highlight.js/issues/3796
Header::addTo($app, ['Diff highlight']);
Code::addTo($app, [<<<'EOD'
    -removed line
    +added line
     unchanged line
    EOD, 'language' => 'diff']);

// TODO https://github.com/highlightjs/highlight.js/issues/480#issuecomment-57007817 and https://github.com/valeriangalliat/highlightjs-code-diff
Header::addTo($app, ['Diff PHP code highlight']);
Code::addTo($app, [<<<'EOD'
     class Foo
     {
    -    public $tag = 'foo';
    +    public $tag = 'bar';
     }
    EOD, 'language' => 'php diff']);
