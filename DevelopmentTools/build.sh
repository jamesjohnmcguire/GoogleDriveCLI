#!/usr/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."

echo "Checking Composer..."
composer install --prefer-dist
composer validate --strict

echo "Checking PHP syntax..."
SourceCode/vendor/bin/parallel-lint --exclude SourceCode/vendor SourceCode Tests

echo "Running static analysis..."
SourceCode/vendor/bin/phpstan.phar analyse

echo "Checking code style..."
SourceCode/vendor/bin/phpcs --standard=ruleset.xml SourceCode
SourceCode/vendor/bin/phpcs --standard=ruleset.tests.xml Tests

echo "Running automated tests..."
SourceCode/vendor/bin/phpunit --configuration Tests/phpunit.xml
