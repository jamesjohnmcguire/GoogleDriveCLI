#!/usr/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."

SourceCode/vendor/bin/phpunit --configuration Tests/phpunit.xml "$@"
