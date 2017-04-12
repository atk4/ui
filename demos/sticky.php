<?php

require 'init.php';

use \atk4\ui\Button;

$layout->add(new \atk4\ui\View([
    'Sticky GET allows us to preserve some GET arguments',
    'ui'=> 'ignored warning message',
]));

class MyButton extends \atk4\ui\Button
{
    public function renderView()
    {
        $this->link($this->content);

        return parent::renderView();
    }
}

$layout->add(new MyButton($app->url()))->addClass('green');
$layout->add(new MyButton($app->url(['xx'=>'YEY'])))->addClass('green');
$layout->add(new MyButton($app->url(['c'=>'OHO'])))->addClass('green');
$layout->add(new MyButton($app->url(['xx'=>'YEY', 'c'=>'OHO'])))->addClass('green');

$layout->add(new \atk4\ui\Header('URLs presented by a blank app'));

$layout->add(new Button($app->url()));
$layout->add(new Button($app->url(['b'=>2])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>false])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>null])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>'abc'])));

$layout->add(new \atk4\ui\Header('Now add sticky for xx='.$app->stickyGET('xx')));

$layout->add(new Button($app->url()));
$layout->add(new Button($app->url(['b'=>2])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>false])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>null])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>'abc'])));

$layout->add(new \atk4\ui\Header('Now also add sticky for c='.$app->stickyGET('c')));

$layout->add(new Button($app->url()));
$layout->add(new Button($app->url(['b'=>2])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>false])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>null])));
$layout->add(new Button($app->url(['b'=>2, 'c'=>'abc'])));

$layout->add(new \atk4\ui\Header('Various ways to build links'));

$layout->add(new Button($app->url()));
$layout->add(new Button($app->url('other.php')));
$layout->add(new Button($app->url('other')));
$layout->add(new Button($app->url(['other', 'b'=>2])));
$layout->add(new Button($app->url('http://yahoo.com/')));
$layout->add(new Button($app->url('http://yahoo.com/?q=abc')));
