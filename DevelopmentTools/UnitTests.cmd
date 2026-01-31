CD %~dp0
CD ..

CALL vendor\bin\phpunit --config Tests\phpunit.xml %1 %2 %3 %4 %5
