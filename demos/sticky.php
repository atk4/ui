<?php

require 'init.php';

use atk4\ui\Button;

$app->add(new \atk4\ui\View([
    'Sticky GET allows us to preserve some GET arguments',
    'ui' => 'ignored warning message',
]));

class MyButton extends \atk4\ui\Button
{
    public function renderView()
    {
        $this->link($this->content);

        return parent::renderView();
    }
}

$app->add(new MyButton($app->url()))->addClass('green');
$app->add(new MyButton($app->url(['xx' => 'YEY'])))->addClass('green');
$app->add(new MyButton($app->url(['c' => 'OHO'])))->addClass('green');
$app->add(new MyButton($app->url(['xx' => 'YEY', 'c' => 'OHO'])))->addClass('green');

$app->add(new \atk4\ui\Header('URLs presented by a blank app'));

$app->add(new Button($app->url()));
$app->add(new Button($app->url(['b' => 2])));
$app->add(new Button($app->url(['b' => 2, 'c' => false])));
$app->add(new Button($app->url(['b' => 2, 'c' => null])));
$app->add(new Button($app->url(['b' => 2, 'c' => 'abc'])));

$app->add(new \atk4\ui\Header('Now add sticky for xx='.$app->stickyGET('xx')));

$app->add(new Button($app->url()));
$app->add(new Button($app->url(['b' => 2])));
$app->add(new Button($app->url(['b' => 2, 'c' => false])));
$app->add(new Button($app->url(['b' => 2, 'c' => null])));
$app->add(new Button($app->url(['b' => 2, 'c' => 'abc'])));

$app->add(new \atk4\ui\Header('Now also add sticky for c='.$app->stickyGET('c')));

$app->add(new Button($app->url()));
$app->add(new Button($app->url(['b' => 2])));
$app->add(new Button($app->url(['b' => 2, 'c' => false])));
$app->add(new Button($app->url(['b' => 2, 'c' => null])));
$app->add(new Button($app->url(['b' => 2, 'c' => 'abc'])));

$app->add(new \atk4\ui\Header('Various ways to build links'));

$app->add(new Button($app->url()));
$app->add(new Button($app->url('other.php')));
$app->add(new Button($app->url('other')));
$app->add(new Button($app->url(['other', 'b' => 2])));
$app->add(new Button($app->url('http://yahoo.com/')));
$app->add(new Button($app->url('http://yahoo.com/?q=abc')));
