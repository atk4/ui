<?php

// log coverage for test-suite

use SebastianBergmann\CodeCoverage\CodeCoverage;

$coverage = new CodeCoverage();

$coverage->filter()->addDirectoryToWhitelist('../src');

function coverage()
{
    global $coverage;
    $coverage->stop();

    $writer = new \SebastianBergmann\CodeCoverage\Report\PHP();

    $writer->process($coverage, dirname(realpath(__FILE__)) . '/../coverage/' . basename($_SERVER['SCRIPT_NAME'], '.php') . '-' . uniqid() . '.cov');
}

$coverage->start($_SERVER['SCRIPT_NAME']);
