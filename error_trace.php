<?php
namespace PMVC\PlugIn\error_trace;

use PMVC as p;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\error_trace';

class error_trace extends p\PlugIn
{
    private $log_file='/tmp/error_log'; 

    public function init()
    {
        register_shutdown_function([$this,'parseRunEndErrors']);
    }

    public static function parseRunEndErrors()
    {
        $Errors=&p\getOption(p\ERRORS);
        if (!empty($Errors)) {
            p\log($Errors);
        }
    }

    public function getLogFile()
    {
       if(!empty($this['log_file'])){
           $this->log_file = $this['log_file'];
       }
       return $this->log_file;
    }

    public function log()
    {
        $time = date('Y/m/d-H:i:s');
        $log='<-- PMVC Error --'.$time."\n";
        $log.=print_r(func_get_args(), true)."\n";
        if (count($_REQUEST)) {
            $log .='---REQUEST---'."\n";
            $log .=var_export($_REQUEST, true)."\n";
        }
        $log.='-->'."\n";
        error_log($log, 3, $this->getLogFile());
    }
} //end class
