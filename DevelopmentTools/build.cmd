@ECHO OFF
SETLOCAL

CD /D "%~dp0.." || EXIT /B 1

CALL composerStatus.cmd || EXIT /B 1

ECHO Checking PHP syntax...
CALL SourceCode\vendor\bin\parallel-lint.bat --exclude SourceCode\vendor SourceCode Tests || EXIT /B 1

ECHO Running static analysis...
CALL SourceCode\vendor\bin\phpstan.phar.bat analyse || EXIT /B 1

ECHO Checking code style...
CALL SourceCode\vendor\bin\phpcs.bat --standard=ruleset.xml SourceCode || EXIT /B 1
CALL SourceCode\vendor\bin\phpcs.bat --standard=ruleset.tests.xml Tests || EXIT /B 1

ECHO Running automated tests...
CALL SourceCode\vendor\bin\phpunit.bat --configuration Tests\phpunit.xml || EXIT /B 1
