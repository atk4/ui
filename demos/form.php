<?php
/**
 * Testing form.
 */
require '../vendor/autoload.php';

try {
    $layout = new \atk4\ui\Layout\App(['defaultTemplate'=>'./templates/layout2.html']);

    $layout->js(true, new \atk4\ui\jsExpression('$.fn.api.settings.successTest = function(response) {
  if(response && response.eval) {
     var result = function(){ eval(response.eval); }.call(this.obj);
  }
  return false;
}'));

    $layout->add(new \atk4\ui\View([
        'Forms below focus on Data integration and automated layouts',
        'ui'=>'ignored warning message'
    ]));

    $layout->add(new \atk4\ui\H2('DefaultForm'));

    $a = [];
    $m_register = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
    $m_register->addField('name');
    $m_register->addField('email');
    $m_register->addField('is_accept_terms', ['type'=>'boolean']);

    $f = $layout->add(new \atk4\ui\Form(['segment'=>true]));
    $f->setModel($m_register);

    $f->onSubmit(function($f){
        return $f->error('name','what that?');
    });

    $layout->add(new \atk4\ui\H2('Another Form'));

    $f = $layout->add(new \atk4\ui\Form(['segment'=>true]));
    $f->setModel($m_register, false);

    $f->addField('email');
    $f->addField('name');
    $f->addField('surname');
    $f->addField('is_accept_terms');

    //$field = $f->add(new \atk4\ui\FormField\Line(['placeholder'=>'Enter your name', 'form'=>$f]), null, ['name'=>'test']);

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    var_dump($e->getTrace());
    throw $e;
}
