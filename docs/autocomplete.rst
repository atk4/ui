
.. _autocomplete:

=================
AutoComplete Field
=================

.. php:namespace:: atk4\\ui\\FormField
.. php:class:: AutoComplete

Agile UI uses "FormField\Dropdown" by default on the form, but there is also implementation
for AutoComplete field. Although they look similar, there are som differences:

 - AutoComplete will perform callback to fetch values.
 - AutoComplete can search in multiple fields.
 - AutoComplete has additional feature called "Plus"
 - AutoComplete only works with models. Won't work for pre-defined value lists.

AutoComplete can be a drop-in replacement for DropDown. 

Using Plus mode
---------------

In your application, it is handy if you can automatically add a missing "client" from the form
where you add an invoice. AutoComplete implements "Plus" mode which will automatically open a modal
form where you can enter new record details.

The form save will re-use the model of your auto-complete, so be sure to set() defaults and
addCondition()s::

    $form->addField('test', ['AutoComplete', 'plus'=>true])->setModel(new Country());

Specifying in Model
-------------------

You can also specify that you prefer to use AutoComplete inside your model definition::

    $model->hasOne('country_id', [new Country(), 'ui'=>['form'=>['AutoComplete']]]);

Advanced Usage
--------------

You can do much more with AutoComplete field by passing dropdown settings::

    $form->addField('test', [
        'AutoComplete', 
        'settings'=>[
            'allowReselection' => true,
            'selectOnKeydown' => false,
            'onChange'        => new atk4\ui\jsExpression('function(value,t,c){
                                    if ($(this).data("value") !== value) {
                                    $(this).parents(".form").form("submit");
                                    $(this).data("value", value);
                                   }}'),
        ]
    ])->setModel(new Country());

