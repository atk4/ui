<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// This demo require spefic Database setup.
// MODEL

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

$form = Form::addTo($app);

$form->addControl('category_id', [Form\Control\Dropdown::class, 'model' => new Category($app->db)]);
$form->addControl('sub_category_id', [Form\Control\DropdownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => 'SubCategories']);
$form->addControl('product_id', [Form\Control\DropdownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => 'Products']);

$form->onSubmit(function (Form $form) {
    echo print_r($form->model->get(), true);
});
