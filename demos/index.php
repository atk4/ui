<?php

require'../vendor/autoload.php';


$data = [
    ['name'=>'Hello'], 
    ['name'=>'World']
];

$db = new \atk4\data\Persistence_Array($data);
$examples = new \atk4\ui\Lister('templates/index1.html');

$m = new \atk4\data\Model($db);
$m->addField('name');

$examples->setModel($m);
$examples->render();

$html = $examples->getHTML();
$js = $examples->getJS();

?>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.6/semantic.min.css">
    <script src="../public/jquery/jquery-3.1.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.6/semantic.min.js"></script>
    <script>
       $(function(){
           <?php echo $js;?>
       });
    </script>
  </head>
  <body>
    <?php echo $html;?>
  </body>
</html>
