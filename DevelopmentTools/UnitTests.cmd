@ECHO OFF
SETLOCAL
CD /D "%~dp0.." || EXIT /B 1
CALL SourceCode\vendor\bin\phpunit.bat --configuration Tests\phpunit.xml %*
EXIT /B %ERRORLEVEL%
