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
        'ui'=> 'ignored warning message',
    ]));

    $layout->add(new \atk4\ui\H2('DefaultForm'));

    $a = [];
    $m_register = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
    $m_register->addField('name');
    $m_register->addField('email');
    $m_register->addField('is_accept_terms', ['type'=>'boolean']);

    $f = $layout->add(new \atk4\ui\Form(['segment'=>true]));
    $f->setModel($m_register);

    $f->onSubmit(function ($f) {
        if($f->model['name'] != 'John') {
            return $f->error('name', 'Your name is not John! It is "'.$f->model['name'].'". It should be John. Pleeease!');
        } else {
            return [
                $f->jsInput('email')->val('john@gmail.com'), 
                $f->jsField('is_accept_terms')->checkbox('set checked')
            ];
        }
    });

    $layout->add(new \atk4\ui\H2('Another Form'));

    $f = $layout->add(new \atk4\ui\Form(['segment']));
    $f->setModel(new \atk4\data\Model());

    $f->addHeader('Example fields added one-by-one');
    $f->addField('field1');
    $f->addField('field2');

    $f->addHeader('Example of field grouping');
    $gr = $f->addGroup('Address with label');
    $gr->addField('ad-dress', ['width'=>'twelve']);
    $gr->addField('code', ['Post Code', 'width'=>'four']);

    $gr = $f->addGroup(['n'=>'two']);
    $gr->addField('c-ity');
    $gr->addField('c-ountry');

    $gr = $f->addGroup(['Name', 'inline'=>true]);
    $gr->addField('first_name', ['width'=>'eight']);
    $gr->addField('middle_name', ['width'=>'three', 'disabled'=>true]);
    $gr->addField('last_name', ['width'=>'five']);

    $f->onSubmit(function ($f) {
        $errors = [];

        foreach($f->model->elements as $name=>$ff){
            if($name=='id') continue;


            if($f->model[$name] != 'a') {
                $errors[] = $f->error($name, 'Field '.$name.' should contain exactly "a", but contains '.$f->model[$name]);
            }

        }
        return $f->success('No more errors', 'so we have saved everything into the database');
        //return $f->error('name', 'what that?');
    });

    //$field = $f->add(new \atk4\ui\FormField\Line(['placeholder'=>'Enter your name', 'form'=>$f]), null, ['name'=>'test']);

    $layout->add(new \atk4\ui\H2('Receipt Form with Nice dropdowns'));

    $f = $layout->add(new \atk4\ui\Form(['segment']));
    $f->setModel($m_register, false);

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    var_dump($e->getTrace());
    throw $e;
}
