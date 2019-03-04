<?php

namespace atk4\ui\FormField;

use atk4\ui\jQuery;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

class Lookup extends Input
{
    public $defaultTemplate = 'formfield/lookup.html';
    public $ui = 'input';

    /**
     * Object used to capture requests from the browser.
     *
     * @var callable
     */
    public $callback;

    /**
     * Set this to true, to permit "empty" selection. If you set it to string, it will be used as a placeholder for empty value.
     *
     * @var string
     */
    public $empty = '...';

    /**
     * Either set this to array of fields which must be searched (e.g. "name", "surname"), or define this
     * as a callback to be executed callback($model, $search_string);.
     *
     * If left null, then search will be performed on a model's title field
     *
     * @var array|Closure
     */
    public $search;

    /**
     * Set this to create right-aligned button for adding a new a new record.
     *
     * true = will use "Add new" label
     * string = will use your string
     *
     * @var null|bool|string
     */
    public $plus = false;

    /**
     * Sets the max. amount of records that are loaded. The default 10
     * displays nicely in UI.
     *
     * @var int
     */
    public $limit = 10;

    /**
     * Set custom model field here to use it's value as ID in dropdown instead of default model ID field.
     *
     * @var string
     */
    public $id_field;

    /**
     * Set custom model field here to display it's value in dropdown instead of default model title field.
     *
     * @var string
     */
    public $title_field;

    /**
     * Semantic UI uses cache to remember choices. For dynamic sites this may be dangerous, so
     * it's disabled by default. To switch cache on, set 'cache'=>'local'.
     *
     * Use this apiConfig variable to pass API settings to Semantic UI in .dropdown()
     *
     * @var array
     */
    public $apiConfig = ['cache' => false];

    /**
     * Semantic UI dropdown module settings.
     * Use this setting to configure various dropdown module settings
     * to use with Autocomplete.
     *
     * For example, using this setting will automatically submit
     * form when field value is changes.
     * $form->addField('field', ['AutoComplete', 'settings'=>['allowReselection' => true,
     *                           'selectOnKeydown' => false,
     *                           'onChange'        => new atk4\ui\jsExpression('function(value,t,c){
     *                                                          if ($(this).data("value") !== value) {
     *                                                            $(this).parents(".form").form("submit");
     *                                                            $(this).data("value", value);
     *                                                          }
     *                                                         }'),
     *                          ]]);
     *
     * @var array
     */
    public $settings = [];

    /**
     * Default string for presenting filter.
     *
     * @var string
     */
    public $filterHeaderLabel = 'Filtering options:';

    /**
     * Default label when no data is selected in filter.
     *
     * @var string
     */
    public $filterEmpty = 'All';

    public $filterChain = null;

    /**
     * Array containing filters.
     *
     * @var null|array
     */
    public $filters = null;

    public function init()
    {
        parent::init();

        $this->label = null;

        $this->template->set('input_id', $this->name.'-ac');

        $this->template->set('place_holder', $this->placeholder);

        if ($this->plus) {
            $this->action = $this->factory(['Button', is_string($this->plus) || is_array($this->plus) ? $this->plus : 'Add new', 'disabled' => ($this->disabled || $this->readonly)]);
        }
        //var_Dump($this->model->get());
        if ($this->form) {
            $vp = $this->form->add('VirtualPage');
        } else {
            $vp = $this->owner->add('VirtualPage');
        }

        $vp->set(function ($p) {
            $f = $p->add('Form');
            $f->setModel($this->model);

            $f->onSubmit(function ($f) {
                $id = $f->model->save()->id;

                $modal_chain = new jQuery('.atk-modal');
                $modal_chain->modal('hide');
                $ac_chain = new jQuery('#'.$this->name.'-ac');
                $ac_chain->dropdown('set value', $id)->dropdown('set text', $f->model->getTitle());

                return [
                    $modal_chain,
                    $ac_chain,
                ];
            });
        });
        if ($this->action) {
            $this->action->js('click', new \atk4\ui\jsModal('Adding New Record', $vp));
        }
    }

    /**
     * Returns URL which would respond with first 50 matching records.
     */
    public function getCallbackURL()
    {
        return $this->callback->getJSURL();
    }

    public function getData()
    {
        if (!$this->model) {
            $this->app->terminate(json_encode([['id' => '-1', 'name' => 'Model must be set for AutoComplete']]));
        }

        $id_field = $this->id_field ?: $this->model->id_field;
        $title_field = $this->title_field ?: $this->model->title_field;

        $this->renderFilters();
        $this->applyFilters();

        $this->model->setLimit($this->limit);

        if (isset($_GET['q'])) {
            if ($this->search instanceof Closure) {
                $this->search($this->model, $_GET['q']);
            } elseif ($this->search && is_array($this->search)) {
                $this->model->addCondition(array_map(function ($field) {
                    return [$field, 'like', '%'.$_GET['q'].'%'];
                }, $this->search));
            } else {
                $this->model->addCondition($title_field, 'like', '%'.$_GET['q'].'%');
            }
        }

        $data = [];
        foreach ($this->model as $junk) {
            // IMPORTANT: always convert data to string, otherwise numbers can be rounded by JS
            $data[] = [
                'id'   => (string) $this->model[$id_field],
                'name' => (string) $this->model[$title_field],
            ];
        }

        if ($this->empty) {
            array_unshift($data, ['id' => '0', 'name' => (string) $this->empty]);
        }

        $this->app->terminate(json_encode([
                                              'success' => true,
                                              'results' => $data,
                                          ]));
    }

    /**
     * Add filter dropdown.
     *
     * Ex: you need to restreint value available in city dropdown base on user input Country and language
     *
     *      $l = $form->addField('city',['Lookup']);
     *      $l->addFilter('country_test', 'Country');
     *      $l->addFilter('language', 'Lang');
     *
     * This way, dropdown value will contains city corresponding to proper country and/or language.
     *
     * @param $field
     * @param null $label
     */
    public function addFilter($field, $label = null)
    {
        if (!$this->model->hasElement($field) instanceof \atk4\data\Field) {
            throw new \atk4\ui\Exception([
                'Unable to filter by non-existant field',
                'field'=> $field,
            ]);
        }
        $this->filters[] = ['field' => $field, 'label' => $label];
    }

    /**
     * Check if filtering is need.
     */
    public function applyFilters()
    {
        if ($this->filters) {
            foreach ($this->filters as $k => $filter) {
                if (
                    isset($_GET[$filter['field']]) &&
                    !empty($_GET[$filter['field']]) &&
                    $_GET[$filter['field']] != $this->filterEmpty &&
                    @$_GET['filter'] != $filter['field']
                ) {
                    $this->model->addCondition($filter['field'], $_GET[$filter['field']]);
                }
            }
        }
    }

    /**
     * Check if filtering is needed and terminate app execution.
     */
    public function renderFilters()
    {
        if (isset($_GET['filter'])) {
            if (isset($_GET['q'])) {
                $this->model->addCondition($_GET['filter'], 'like', '%'.$_GET['q'].'%');
            }
            // Apply filtering to filter.
            $this->applyFilters();
            $action = $this->model->action('field', [$_GET['filter']]);
            $action->group($_GET['filter']);
            $rows = $action->get();
            $data = [];
            foreach ($rows as $k => $v) {
                $data[] = ['id' => $k, 'name' => $v[$_GET['filter']]];
            }
            array_unshift($data, ['id' => -1, 'name' => $this->filterEmpty]);

            $this->app->terminate(json_encode([
                                                  'success' => true,
                                                  'results' => $data,
                                              ]));
        }
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'        => $this->short_name,
            'type'        => 'hidden',
            'id'          => $this->id.'_input',
            'value'       => $this->getValue(),
            'readonly'    => $this->readonly ? 'readonly' : false,
            'disabled'    => $this->disabled ? 'disabled' : false,
        ]);
    }

    public function getFilterInput($name, $id, $value = null)
    {
        return $this->app->getTag('input', [
            'name' => $name,
            'type' => 'hidden',
            /*'placeholder'=> $this->placeholder,*/
            'id'    => $id,
            'value' => $value,
        ]);
    }

    /**
     * Set Semantic-ui Api settings to use with dropdown.
     *
     * @param array $config
     *
     * @return $this
     */
    public function setApiConfig($config)
    {
        $this->apiConfig = array_merge($this->apiConfig, $config);

        return $this;
    }

    /**
     * Override this method if you want to add more logic to the initialization of the
     * auto-complete field.
     *
     * @param jQuery
     */
    protected function initDropdown($chain)
    {
        $settings = array_merge([
                                    'fields'      => ['name' => 'name', 'value' => 'id'/*, 'text' => 'description'*/],
                                    'apiSettings' => array_merge(['url' => $this->getCallbackURL().'&q={query}'], $this->apiConfig),
                                ], $this->settings);

        $chain->dropdown($settings);
    }

    /**
     * For each filters, we need to create js dropdown and setup proper api settings.
     *
     * When changing value on any of the filter we need to set main dropdown
     * apiSettings again in order for main dropdown to send proper filter value.
     * This is done via the onChange function of the dropdown filter.
     *
     * This function will generate js similar to this:
     *   $("#filterName1").dropdown({
     *      "fields":{
     *                  "name":"name",
     *                  "value":"id"
     *                },
     *      "apiSettings":{
     *                      "url":"autocomplete.php?atk_admin_form_generic_country2_callback=ajax\x26__atk_callback=1\x26q={query}",
     *                      "cache":false,
     *                      "data":{"filter":"city"}
     *                  },
     *      "onChange":function() {
     *           $("#mainDropdown").dropdown(
     *              {"fields":{"name":"name","value":"id"},
     *              "apiSettings":{"url":"autocomplete.php?atk_admin_form_generic_country2_callback=ajax\x26__atk_callback=1\x26q={query}",
     *              "cache":false,
     *              "data":{"filteName1":$("input[name=filteName1]").parent().find(".text").text(),"filteName2":$("input[name=filteName2]").parent().find(".text").text()}
     *               }
     *          });
     *    }});
     *
     * @throws \atk4\ui\Exception
     */
    public function createFilterJsDropdown()
    {
        foreach ($this->filters as $k => $filter) {
            $f_name = $this->name.'-ac_f'.$k;
            $chain = new jQuery('#'.$f_name);
            $options = [
                'fields'       => ['name' => 'name', 'value' => 'id'],
                'match'        => 'value',
                'apiSettings'  => ['url'         => $this->getCallbackURL().'&q={query}',
                                  'cache'        => false,
                                  'data'         => array_merge($this->getFilterQuery(), ['filter' => $filter['field']]),
                                  'onResponse'   => new jsFunction(['resp'], [
                                      new jsExpression('if (!resp.success){atk.apiService.atkSuccessTest(resp);}'),
                                  ]),
                ],
                'onChange'    => new jsFunction([
                                                    (new jQuery())->trigger('filterChanged'),
                                                    $this->getJsDropdown(),
                                                ]),
            ];

            $this->js(true, $chain->dropdown($options));
        }
        //set filter value using $(this) context for onChange handler instead of filter name.
        $options['apiSettings']['data']['filter'] = (new jQuery())->find('input')->attr('name');
        $this->filterChain = $options;
    }

    /**
     * Return the main dropdown js chain.
     *
     * @return jQuery
     */
    public function getJsDropdown()
    {
        $chain = new jQuery('#'.$this->name.'-ac');
        $this->initDropdown($chain);

        return $chain;
    }

    /**
     * Will create jsExpression need to add to the dropdown apiSettings data when using filters.
     *
     * ex: {"data":{"filterName1":$("input[name=filterName1]").parent().find(".text").text(),"filterName2":$("input[name=filterName2]").parent().find(".text").text()}}
     *
     * @return array
     */
    public function getFilterQuery()
    {
        $q = [];
        foreach ($this->filters as $key => $filter) {
            $q[$filter['field']] = new jsExpression('$([input]).parent().dropdown("get text")', ['input' => 'input[name='.$filter['field'].']']);
        }

        return $q;
    }

    /**
     * When filter changed value,
     * we need to regenerate each filter dropdown.
     *
     * Note: regenerating dropdown seem to have them lost their
     * text value. Need to reset text value by getting them prior to regenerating dropdown.
     *
     * @throws \atk4\ui\Exception
     *
     * @return jQuery
     */
    public function onJsFilterChanged()
    {
        return (new jQuery())
            ->on('filterChanged',
                 new jsFunction(array_merge(
                                    [new jsExpression('let dropdowns = $(".atk-filter-dropdown")')],
                                    [new jsExpression('dropdowns.each(function(){const value = $(this).dropdown("get text"); $(this).dropdown([chain]); $(this).dropdown("set text", value); $(this).dropdown("refresh");})', ['chain' => $this->filterChain])]
                                ))
            );
    }

    public function renderView()
    {
        $this->callback = $this->add('Callback');
        $this->callback->set([$this, 'getData']);

        if ($this->disabled) {
            $this->settings['showOnFocus'] = false;
            $this->settings['allowTab'] = false;

            $this->template->set('disabled', 'disabled');
        }

        if ($this->readonly) {
            $this->settings['showOnFocus'] = false;
            $this->settings['allowTab'] = false;
            $this->settings['apiSettings'] = null;
            $this->settings['onShow'] = new jsFunction([new jsExpression('return false')]);
            $this->template->set('readonly', 'readonly');
        }

        if ($this->filters) {
            $this->fieldClass = 'ui segment';

            //add filtering query to main dropdown
            $this->apiConfig = array_merge($this->apiConfig, ['data' => $this->getFilterQuery()]);

            //render filters to template
            $this->template->set('FilterHeaderLabel', $this->filterHeaderLabel);
            $ft = $this->template->cloneRegion('Dropdown');
            $html = '';

            foreach ($this->filters as $k => $filter) {
                $f_name = $this->name.'-ac_f'.$k;
                $ft->set('input_id', $f_name);
                $ft->set('FilterLabel', $filter['label']);
                $ft->set('place_holder', $this->filterEmpty);
                $ft->set('filterClass', 'atk-filter-dropdown');
                $ft->setHTML('Input', $this->getFilterInput($filter['field'], $filter['field'].'_id'));
                $html .= $ft->render();
            }
            $this->template->setHTML('Filters', $html);

            // create proper js for dropdown.
            $this->createFilterJsDropdown();

            // output changed handler for filters.
            $this->js(true, $this->onJsFilterChanged());
        } else {
            $this->template->del('FilterContainer');
        }

        $this->js(true, $this->getJsDropdown());

        if ($this->field && $this->field->get()) {
            $id_field = $this->id_field ?: $this->model->id_field;
            $title_field = $this->title_field ?: $this->model->title_field;

            $this->model->tryLoadBy($id_field, $this->field->get());

            if (!$this->model->loaded()) {
                $this->field->set(null);
            } else {
                $chain = new jQuery('#'.$this->name.'-ac');
                // IMPORTANT: always convert data to string, otherwise numbers can be rounded by JS
                $chain->dropdown('set value', (string) $this->model[$id_field])
                        ->dropdown('set text', (string) $this->model[$title_field]);
                $this->js(true, $chain);
            }
        }

        Parent::renderView();
    }
}
