#!/usr/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."

composerStatus.sh

echo "Checking PHP syntax..."
SourceCode/vendor/bin/parallel-lint --exclude SourceCode/vendor SourceCode Tests

if [[ "${1:-}" == "--quality" ]]; then
	echo "Running static analysis..."
	SourceCode/vendor/bin/phpstan.phar analyse

	echo "Checking code style..."
	SourceCode/vendor/bin/phpcs --standard=ruleset.xml SourceCode
	SourceCode/vendor/bin/phpcs --standard=ruleset.tests.xml Tests
fi

echo "Running automated tests..."
bash DevelopmentTools/unitTests.sh
