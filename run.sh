#!/bin/bash

echo 'Installing dependencies...'

composer install --no-progress

echo 'running unit tests'

php vendor/bin/phpunit -c phpunit.xml

chmod -R 777 reports /tmp/reports
