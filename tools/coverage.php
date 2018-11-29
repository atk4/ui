<?php

// log coverage for test-suite

use SebastianBergmann\CodeCoverage\CodeCoverage;

$coverage = new CodeCoverage();

$coverage->filter()->addDirectoryToWhitelist('../src');
//$coverage->filter()->addDirectoryToWhitelist(dirname(dirname(realpath(__FILE__)).'/src'));

register_shutdown_function(function () use ($coverage) {
    $coverage->stop();

    $writer = new \SebastianBergmann\CodeCoverage\Report\PHP();

    $output = $writer->process($coverage, dirname(realpath(__FILE__)).'/../coverage/'.basename($_SERVER['SCRIPT_NAME'], '.php').'-'.uniqid().'.cov');
});

$coverage->start($_SERVER['SCRIPT_NAME']);
