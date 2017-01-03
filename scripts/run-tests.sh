#!/usr/bin/env bash

./vendor/bin/phpcs --standard=PSR2 src --exclude=Generic.Files.LineLength
./vendor/bin/phpcs --standard=PSR2 tests/src --exclude=Generic.Files.LineLength
./vendor/bin/phpunit