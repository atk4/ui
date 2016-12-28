<?php
/**
 * Created by PhpStorm.
 * User: rw
 * Date: 28/12/2016
 * Time: 11:31
 */

namespace atk4\ui;


class jsAPI extends jsExpression
{
    public $prefix = '';

    public function __construct($template = '', array $args = [])
    {
        parent::__construct($template, $args);
    }

}