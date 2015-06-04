<?php
PMVC\Load::plug();
PMVC\setPlugInFolder('../');
class ErrorTraceTest extends PHPUnit_Framework_TestCase
{
    function testHello()
    {
        $errStr='error_test';
        $err = PMVC\plug('error_trace');
        $err->setErrorReporting(E_ALL);
        trigger_error($errStr);
        $Errors =& PMVC\getOption(PMVC\ERRORS);
        $this->assertEquals($errStr,$Errors[PMVC\APP_LAST_ERROR]);
    }
}
