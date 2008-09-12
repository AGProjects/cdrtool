<?
function cdrtoolErrorHandler($errno, $errstr, $errfile, $errline) {
    $errType=array(
                   1	 => 'E_ERROR',
                   2	 => 'E_WARNING',
                   4	 => 'E_PARSE',
                   8	 => 'E_NOTICE',
                   16	 => 'E_CORE_ERROR',
                   32	 => 'E_CORE_WARNING',
                   64	 => 'E_COMPILE_ERROR',
                   128	 => 'E_COMPILE_WARNING',
                   256	 => 'E_USER_ERROR',
                   512	 => 'E_USER_WARNING',
                   1024	 => 'E_USER_NOTICE',
                   6143	 => 'E_ALL',
                   2048	 => 'E_STRICT',
                   4096	 => 'E_RECOVERABLE_ERROR',
                   8192	 => 'E_DEPRECATED',
                   16384 => 'E_USER_DEPRECATED'
                   );

    switch ($errno) {
    case 1:
        $log=sprintf("Error: %s: '%s' on line %s in file %s<br>\n",$errType[$errno],$errstr,$errline,$errfile);
        print $log;
        error_log($log, 3, "/var/log/cdrtool-errors.log");
        exit(1);
        break;

    case 8:
        break;

    case 2048:
        break;

    default:
    	if ($errstr != "mysql_free_result(): supplied argument is not a valid MySQL result resource") {
        	$log=sprintf("%s: '%s' on line %s in file %s<br>\n",$errType[$errno],$errstr,$errline,$errfile);
        	error_log($log, 3, "/var/log/cdrtool-errors.log");
        }
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

set_error_handler("cdrtoolErrorHandler");

?>
