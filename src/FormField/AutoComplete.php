<?php

namespace atk4\ui\FormField;

<<<<<<< HEAD
use atk4\ui\jQuery;

=======
>>>>>>> atk4/feature/autocomplete-field
class AutoComplete extends Input
{
    public $defaultTemplate = 'formfield/autocomplete.html';
    public $ui = 'input';
    public $searchClassName = 'search';

    public function init()
    {
        parent::init();
        $this->template->set('input_id', $this->name.'-ac');

        $this->template->set('place_holder', $this->placeholder);

        $chain = new jQuery('#'.$this->name.'-ac');

        $chain->dropdown([
            'fields'      => ['name' => 'name', 'value' => 'id', 'text' => 'description'],
            'apiSettings' => [
                'mockResponse' => [
                    'success' => true,
                    'results' => [
<<<<<<< HEAD
                        ['name' => 'd1', 'id' => 'd1v', 'description' => 'd1 apple'],
                        ['name' => 'd2', 'id' => 'd2v', 'description' => 'd2 google'],
                        ['name' => 'd3', 'id' => 'd3v', 'description' => 'd3 yahoo'],
                    ]
                ],
            ],
            'filterRemoteData'  => true,
=======
                        ['name' => 'd1', 'id' => 'd1v'],
                        ['name' => 'd2', 'id' => 'd2v'],
                        ['name' => 'd3', 'id' => 'd3v'],
                    ],
                ],
            ],
>>>>>>> atk4/feature/autocomplete-field
        ]);
        $this->js(true, $chain);
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'       => $this->short_name,
            'type'       => 'hidden',
            /*'placeholder'=> $this->placeholder,*/
            'id'         => $this->id.'_input',
            'value'      => $this->getValue(),
        ]);
    }
}
