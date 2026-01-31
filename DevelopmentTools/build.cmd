@ECHO OFF

CD %~dp0
CD ..

ECHO Checking Composer...
CALL composer install --prefer-dist
CALL composer validate --strict
ECHO Outdated:
CALL composer outdated --direct

ECHO .
ECHO Checking code syntax...
CALL vendor/bin/parallel-lint --exclude .git --exclude Support --exclude vendor .

ECHO .
ECHO Code Analysis...
CALL vendor\bin\phpstan.phar.bat analyse

ECHO .
ECHO Checking Code Styles...
CALL php vendor\bin\phpcs -sp --standard=ruleset.xml SourceCode
CALL vendor\bin\phpcs.bat -sp --standard=ruleset.tests.xml Tests

ECHO Running Automated Tests
CALL vendor\bin\phpunit.bat --config Tests\phpunit.xml

IF "%1"=="release" GOTO deploy
GOTO finish

:deploy
ECHO Deploying...
if "%~2"=="" GOTO error

IF EXIST digitalzenworks-apitest.zip DEL /Q digitalzenworks-apitest.zip
zip -r digitalzenworks-apitest.zip . -x .git\* -x .vscode\* -x vendor\* -x ApiTest.code-workspace

gh release create v%2 --notes %2 digitalzenworks-apitest.zip
DEL /Q digitalzenworks-apitest.zip

:error
ECHO No Version Tag Supplied for Release

:finish
