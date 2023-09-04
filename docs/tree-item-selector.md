:::{php:namespace} Atk4\Ui
:::

:::{php:class} Form\Control\TreeItemSelector
:::

# TreeItemSelector Form Control

TreeItemSelector Form Control will display a list of items in a hierarchical (tree) structure. It allow for a user to select multiple
or single item within a tree like list structure.

## Attributes

:::{php:attr} treeItems
:::

The list of items to be render by the components as an array. End item must at least contains a name and ID value. Name will be display
within the list structure and ID will be collect when user add or remove them.
Items are grouped together by using nodes forming a category in the list. ID value is not mandatory for a group.

The TreeItemSelector will automatically create group of items based on the treeItems array. It will create a group when an item contains a nodes key within
the treeItems array and that nodes key is not empty. Below is a sample of a group name call Electronics using two children nodes.:

```
$items = [
    'Electronics' => [
        'nodes' => [
            [
                'name' => 'tv',
                'id' => '100',
            ],
            [
                'name' => 'radio',
                'id' => '100',
            ],
        ],
    ],
]
```

:::{php:attr} allowMultiple
:::

This attribute will decide into witch mode the component will run. When allowMultiple is set to true (default) then
the component will allow multiple selection to be made within the list of items. Otherwise, only one selection
will be allowed.

## Basic Usage

Adding a TreeItemSelector form control to a Form:

```
$items = [
    [
        'name' => 'Electronics',
        'nodes' => [
            [
                'name' => 'Phone',
                'nodes' => [
                    [
                        'name' => 'iPhone',
                        'id' => 502,
                    ],
                    [
                        'name' => 'Google Pixels',
                        'id' => 503,
                    ],
                ],
            ],
            ['name' => 'Tv', 'id' => 501, 'nodes' => []],
            ['name' => 'Radio', 'id' => 601, 'nodes' => []],
        ],
    ],
    ['name' => 'Cleaner', 'id' => 201, 'nodes' => []],
    ['name' => 'Appliances', 'id' => 301, 'nodes' => []],
];

$form = \Atk4\Ui\Form::addTo($app);
$control = $form->addControl('tree', [new TreeItemSelector(['treeItems' => $items]), 'caption' => 'Select items:'], ['type' => 'json']);
$control->set([201, 301, 503]);
```

Please note that when using TreeItemSelector in multiple mode, you need to specify field attribute type to 'json'
because in multiple mode, it will collect item value as an array type.

## Callback Usage

:::{php:method} onItem($fx)
:::

It is possible to run a callback function every time an item is select on the list. The callback function will receive the selected item
set by the user.:

```
$control->onItem(function (array $value) {
    return new \Atk4\Ui\Js\JsToast($this->getApp()->encodeJson($value));
});
```

## Note

This form control component is made to collect ID's of end item only, i.e. item with no children nodes, and will be working in recursive selection
mode when allowMultiple is set to true. Recursive selection mean that when user click on a group, it will automatically select or unselect children
of that group depending on the state of the group when clicked. Be aware of this when building your item tree.
