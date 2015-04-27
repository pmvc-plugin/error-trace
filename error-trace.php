<?php
use PMVC as p;

${_INIT_CONFIG}[_CLASS] = '_PMVC_PLUG_ErrorTrace';
register_shutdown_function(array(${_INIT_CONFIG}[_CLASS],'_parseRunEndErrors'));

class _PMVC_PLUG_ErrorTrace extends p\PLUGIN
{
    var $_error = array(
        E_ERROR=>'E_ERROR',
        E_WARNING=>'E_WARNING',
        E_PARSE=>'E_PARSE',
        E_NOTICE=>'E_NOTICE',
        E_CORE_ERROR=>'E_CORE_ERROR',
        E_CORE_WARNING=>'E_CORE_WARNING',
        E_COMPILE_ERROR=>'E_COMPILE_ERROR',
        E_COMPILE_WARNING=>'E_COMPILE_WARNING',
        E_USER_ERROR=>'E_USER_ERROR',
        E_USER_WARNING=>'E_USER_WARNING',
        E_USER_NOTICE=>'E_USER_NOTICE',
        p\MY_USER_ERRORS=>array(
            E_USER_ERROR=>'E_USER_ERROR'
        ),
        p\APP_ERRORS=>array(
            E_USER_WARNING=>'E_USER_WARNING',
            E_USER_NOTICE=>'E_USER_NOTICE',
        ),
    );
    
    function init() {
        p\plug('observer')->addObserver($this,'SetConfig');
        p\plug('observer')->addObserver($this,'Finish');
        set_error_handler(array($this,'handleError'));
    }

    function onSetConfig() {
        if( p\plug('observer')->isSetOption(_ERROR_REPORTING) )
        { 
            error_reporting(p\getOption(_ERROR_REPORTING));
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        }
    }

    function onFinish(){
        restore_error_handler();
    }

    function handleError($number, $message, $file, $line, $context){
        if( !isset($this->_error[$number]) ){
            return null;
        }
        p\d($message); 
        $Errors =& p\getOption(p\ERRORS);
        if(isset($this->_error[p\MY_USER_ERRORS][$number])){
            $Errors[p\MY_USER_ERRORS][]=$message;
            $Errors[p\MY_USER_LAST_ERROR]=$message;
        }elseif(isset($this->_error[p\APP_ERRORS][$number])){
            $Errors[p\APP_ERRORS][]=$message;
            $Errors[p\APP_LAST_ERROR]=$message;
        }else{
            $Errors[p\SYSTEM_ERRORS][]=$message;
            $Errors[p\SYSTEM_LAST_ERROR]=$message;
        }
    }

    public static function _parseRunEndErrors(){
        if(p\getOption(_ERROR_ENABLE_LOG)){
            $Errors=&p\getOption(p\ERRORS);
            if(!empty($Errors)){
                p\log($Errors);
            }
        }
    }

    function log(){
        $log='<---PMVC Error---'."\n";
        $log.=var_export(func_get_args(),true)."\n";
        if(count($_REQUEST)){
            $log .='---REQUEST---'."\n";
            $log .=var_export($_REQUEST,true)."\n";
        }
        $log.='-->'."\n";
        error_log($log,3,'/tmp/error_log');
    }

} //end class
