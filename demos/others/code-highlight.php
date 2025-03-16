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
        public $v;
    }
    EOD, 'language' => 'php']);

Header::addTo($app, ['Empty code highlight']);
Code::addTo($app, ['', 'language' => 'php']);

Header::addTo($app, ['Diff highlight']);
Code::addTo($app, [<<<'EOD'
    -removed line
    +added line
     unchanged line
    EOD, 'language' => 'diff']);

Header::addTo($app, ['Diff PHP code highlight']);
Code::addTo($app, [<<<'EOD'
     <?php

     namespace Atk4\Ns;

     class Foo
     {
    -    public $v;
    +    public $v2;
     }
    EOD, 'language' => 'diff']);
