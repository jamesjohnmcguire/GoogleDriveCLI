#!/bin/bash

cd "$(dirname "${BASH_SOURCE[0]}")"
cd ..

echo Checking Composer...
composer install --prefer-dist
composer validate --strict
echo Outdated:
composer outdated --direct

echo
echo Checking Code Syntax...
SourceCode/vendor/bin/parallel-lint --exclude .git --exclude SourceCode/vendor .

echo
echo Code Analysis...
SourceCode/vendor/bin/phpstan.phar analyse

echo
echo Checking Code Styles...
SourceCode/vendor/bin/phpcs -sp --standard=ruleset.xml SourceCode
SourceCode/vendor/bin/phpcs -sp --standard=ruleset.tests.xml Tests

echo
echo Running Automated Tests
SourceCode/vendor/bin/phpunit --config Tests/phpunit.xml

if [[ $1 == "release" ]] ; then
	echo
	echo "Release is set..."

	if [ -z "$2" ] ;then
		echo "No Version Tag Supplied for Release"
		exit 1
	fi

	# rm -rf Documentation
	# phpDocumentor.phar --setting="graphs.enabled=true" -d SourceCode -t Documentation

	file="digitalzenworks-apitest.zip"

	if [ -f "$file" ] ; then
		rm "$file"
	fi

	zip -r "$file" . -x ".git/*" -x ".vscode/*" -x "vendor/*" -x "ApiTest.code-workspace"

	gh release create v$2 --notes $2 "$file"
	rm "$file"
fi
