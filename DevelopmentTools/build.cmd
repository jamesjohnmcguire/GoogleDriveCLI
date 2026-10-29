@ECHO OFF
SETLOCAL

CD /D "%~dp0.." || EXIT /B 1

CALL composerStatus.cmd || EXIT /B 1

ECHO Checking PHP syntax...
CALL SourceCode\vendor\bin\parallel-lint.bat --exclude SourceCode\vendor SourceCode Tests || EXIT /B 1

IF NOT "%~1"=="--quality" GOTO tests

ECHO Running static analysis...
CALL SourceCode\vendor\bin\phpstan.phar.bat analyse
IF ERRORLEVEL 1 EXIT /B %ERRORLEVEL%

ECHO Checking code style...
CALL SourceCode\vendor\bin\phpcs.bat --standard=ruleset.xml SourceCode
IF ERRORLEVEL 1 EXIT /B %ERRORLEVEL%
CALL SourceCode\vendor\bin\phpcs.bat --standard=ruleset.tests.xml Tests
IF ERRORLEVEL 1 EXIT /B %ERRORLEVEL%

:tests
ECHO Running automated tests...
CALL DevelopmentTools\unitTests.cmd
EXIT /B %ERRORLEVEL%
