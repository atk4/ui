<?php

namespace atk4\ui;

/**
 * Various helper functions for Agile UI.
 *
 * @method jQuery_Chain ajaxec()
 * @method jQuery_Chain successMessage()
 * @method jQuery_Chain errorMessage()
 * @method jQuery_Chain consoleError()
 * @method jQuery_Chain dialog()
 * @method jQuery_Chain dialogError()
 * @method jQuery_Chain closeDialog()
 * @method jQuery_Chain frameURL()
 * @method jQuery_Chain atk4_checkboxes()
 * @method jQuery_Chain atk4_expander()
 * @method jQuery_Chain atk4_uploader()
 * @method jQuery_Chain atk4_loader()
 * @method jQuery_Chain atk4_load()
 * @method jQuery_Chain selectmenu()
 * @method jQuery_Chain datepicker()
 * @method jQuery_Chain button()
 * @method jQuery_Chain slider()
 * @method jQuery_Chain spinner()
 * @method jQuery_Chain tabs()
 */
class jUniv extends jsChain
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
