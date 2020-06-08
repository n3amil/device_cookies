#!/bin/bash

echo 'Installing dependencies...'

composer install --no-progress

echo 'running psalm'

php ./vendor/bin/psalm
