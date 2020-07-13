<?php

declare(strict_types=1);

namespace atk4\ui;

/**
 * Various helper functions for Agile UI.
 *
 * @method JsChain atkAjaxec()
 * @method JsChain successMessage()
 * @method JsChain errorMessage()
 * @method JsChain consoleError()
 * @method JsChain dialog()
 * @method JsChain dialogError()
 * @method JsChain closeDialog()
 * @method JsChain frameURL()
 * @method JsChain atk4_checkboxes()
 * @method JsChain atk4_expander()
 * @method JsChain atk4_uploader()
 * @method JsChain atk4_loader()
 * @method JsChain atk4_load()
 * @method JsChain selectmenu()
 * @method JsChain datepicker()
 * @method JsChain button()
 * @method JsChain slider()
 * @method JsChain atkSpinner()
 * @method JsChain tabs()
 */
class Juniv extends JsChain
{
    public $_include = 'univ.min.js';
    public $_version = '2.0.0';
    //public $_integrity = 'sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=';
    public $_library = '$.univ()';

    public function univ()
    {
        return new self($this);
    }
}
