<?php

namespace atk4\ui\FormField;

class AutoComplete extends Input
{
    public $defaultTemplate = 'formfield/autocomplete.html';
    public $ui = 'search selection dropdown';
    public $searchClassName = 'search';

    public function init()
    {
        parent::init();

        $this->js(true)->dropdown([
            'fields'      => ['name' => 'name', 'value'=>'id'],
            'apiSettings' => [
                'mockResponse' => [
                    'success' => true,
                    'results' => [
                        ['name' => 'd1', 'id' => 'd1v'],
                        ['name' => 'd2', 'id' => 'd2v'],
                        ['name' => 'd3', 'id' => 'd3v'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'       => $this->short_name,
            'type'       => 'hidden',
            'placeholder'=> $this->placeholder,
            'id'         => $this->id.'_input',
            'value'      => $this->getValue(),
        ]);
        //return '<input name="'.$this->short_name.'" type="'.$this->inputType.'" placeholder="'.$this->placeholder.'" id="'.$this->id.'_input"/>';
    }
}
