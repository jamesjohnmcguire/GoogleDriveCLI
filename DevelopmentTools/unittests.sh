#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")"
cd ..

vendor/bin/phpunit -c Tests/phpunit.xml "$@"
