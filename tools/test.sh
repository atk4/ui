#!/bin/bash

echo "Setting up coverage logging";
mkdir coverage
chmod 777 coverage
cp tools/coverage.php demos/coverage.php

echo "Running UI tests"
./vendor/bin/behat

ls -l coverage/
./vendor/bin/phpcov  merge coverage/ --clover clover.xml

wc -l clover.xml
