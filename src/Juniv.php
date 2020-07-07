<?php

declare(strict_types=1);

namespace atk4\ui;

/**
 * Various helper functions for Agile UI.
 *
 * @method Jquery atkAjaxec()
 * @method Jquery successMessage()
 * @method Jquery errorMessage()
 * @method Jquery consoleError()
 * @method Jquery dialog()
 * @method Jquery dialogError()
 * @method Jquery closeDialog()
 * @method Jquery frameURL()
 * @method Jquery atk4_checkboxes()
 * @method Jquery atk4_expander()
 * @method Jquery atk4_uploader()
 * @method Jquery atk4_loader()
 * @method Jquery atk4_load()
 * @method Jquery selectmenu()
 * @method Jquery datepicker()
 * @method Jquery button()
 * @method Jquery slider()
 * @method Jquery atkSpinner()
 * @method Jquery tabs()
 */
class Juniv extends jsChain
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
