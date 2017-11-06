#!/bin/bash

echo "Setting up coverage logging";
mkdir coverage
chmod 777 coverage
cp behat-travis.yml behat.yml
cp tools/coverage.php demos/coverage.php
sed -i "s|node_modules_path.*|node_modules_path: `npm get prefix`/lib/node_modules|g" behat.yml
(cd js; npm install; npm run build)

echo "Running UI tests"
./vendor/bin/behat || exit -1


sleep 3

mv phpunit.cov coverage/
ls -l coverage/
#mv coverage build/
./vendor/bin/phpcov  merge coverage/ --clover clover.xml

#wc -l clover.xml
#rm demos/coverage.php
