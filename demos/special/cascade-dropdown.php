<?php
/***
 * This demo require spefic Database setup.
 */

chdir('..');

require_once 'atk-init.php';




/*********** MODEL ***************/

class Category extends \atk4\data\Model
{
    public $table = 'category';

    public function init(): void
    {
        parent::init();
        $this->addField('name');

        $this->hasMany('SubCategories', new SubCategory());
        $this->hasMany('Products', new Product());
    }
}

class SubCategory extends \atk4\data\Model
{
    public $table = 'sub_category';

    public function init(): void
    {
        parent::init();
        $this->addField('name');

        $this->hasOne('category_id', new Category());
        $this->hasMany('Products', new Product());
    }
}

class Product extends \atk4\data\Model
{
    public $table = 'product';

    public function init(): void
    {
        parent::init();
        $this->addField('name');
        $this->addField('brand');
        $this->hasOne('category_id', [new Category()])->addTitle();
        $this->hasOne('sub_category_id', [new SubCategory()])->addTitle();
    }
}

$f = \atk4\ui\Form::addTo($app);

$f->addField('category_id', [\atk4\ui\FormField\DropDown::class, 'model' => new Category($db)]);
$f->addField('sub_category_id', [\atk4\ui\FormField\DropDownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => 'SubCategories']);
$f->addField('product_id', [\atk4\ui\FormField\DropDownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => 'Products']);

$f->onSubmit(function ($f) {
    echo print_r($f->model->get(), true);
});
