<?php

require __DIR__ . '/init.php';

use atk4\ui\Button;

$app->add(new \atk4\ui\View([
    'Sticky GET allows us to preserve some GET arguments',
    'ui' => 'ignored info message',
]));

class MyButton extends \atk4\ui\Button
{
    public function renderView()
    {
        $this->link($this->content);
        $this->addClass('green');

        return parent::renderView();
    }
}

// Buttons
$app->add(new MyButton($app->url()));
$app->add(new MyButton($app->url(['xx' => 'YEY'])));
$app->add(new MyButton($app->url(['c' => 'OHO'])));
$app->add(new MyButton($app->url(['xx' => 'YEY', 'c' => 'OHO'])));

// URLs presented by a blank app
$app->add(new \atk4\ui\Header('URLs presented by a blank app'));
$app->add(new Button($app->url()));
$app->add(new Button($app->url(['b' => 2])));
$app->add(new Button($app->url(['b' => 2, 'c' => false])));
$app->add(new Button($app->url(['b' => 2, 'c' => null])));
$app->add(new Button($app->url(['b' => 2, 'c' => 'abc'])));

// Sticky for xx=
$app->add(new \atk4\ui\Header('Now add sticky for xx='.$app->stickyGET('xx')));
$app->add(new Button($app->url()));
$app->add(new Button($app->url(['b' => 2])));
$app->add(new Button($app->url(['b' => 2, 'c' => false])));
$app->add(new Button($app->url(['b' => 2, 'c' => null])));
$app->add(new Button($app->url(['b' => 2, 'c' => 'abc'])));

// Sticky for c=
$app->add(new \atk4\ui\Header('Now also add sticky for c='.$app->stickyGET('c')));
$app->add(new Button($app->url()));
$app->add(new Button($app->url(['b' => 2])));
$app->add(new Button($app->url(['b' => 2, 'c' => false])));
$app->add(new Button($app->url(['b' => 2, 'c' => null])));
$app->add(new Button($app->url(['b' => 2, 'c' => 'abc'])));

// Various ways to build links
$app->add(new \atk4\ui\Header('Various ways to build links'));
$app->add(new Button($app->url()));
$app->add(new Button($app->url('other.php')));
$app->add(new Button($app->url('other')));
$app->add(new Button($app->url(['other', 'b' => 2])));
$app->add(new Button($app->url('http://yahoo.com/')));
$app->add(new Button($app->url('http://yahoo.com/?q=abc')));
