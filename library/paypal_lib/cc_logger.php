<?php

require_once 'Log.php';

/**
 * Class to handle logging for the PHP SDK sample apps.
 *
 * @package samples_php
 */
class cc_logger
{

    /**
     * PHP Sample being logged
     *
     * @var string $_logSample
     */
    var $_logSample;

    /**
     * What level should we log at? Valid levels are:
     *   PEAR_LOG_ERR   - Log only severe errors.
     *   PEAR_LOG_INFO  - (default) Date/time of operation, operation name, elapsed time, success or failure indication.
     *   PEAR_LOG_DEBUG - Full text of SOAP requests and responses and other debugging messages.
     *
     * See the PayPal SDK User Guide for more details on these log
     * levels.
     *
     * @access protected
     *
     * @var integer $_logLevel
     */
    var $_logLevel;

    /**
     * If we're logging, what directory should we create log files in?
     * Note that a log name coincides with a symlink, logging will
     * *not* be done to avoid security problems. File names are
     * <DateStamp>.PayPal.log.
     *
     * @access protected
     *
     * @var string $_logFile
     */
    var $_logDir;

    /**
     * The PEAR Log object we use for logging.
     *
     * @access protected
     *
     * @var Log $_logger
     */
    var $_logger;

    function SampleLogger($sample, $level)
    {
        $this->_logSample = $sample;

        // Load SDK settings.
        /*
        if (@include 'PayPal/conf/paypal-sdk.php') {
            if (isset($__PP_CONFIG['log_level'])) {
                $this->_logLevel = $__PP_CONFIG['log_level'];
            }
            if (isset($__PP_CONFIG['log_dir'])) {
                $this->_logDir = $__PP_CONFIG['log_dir'];
            }
        }
        if(isset($level)) {
            $this->_logLevel = $level;
        }
        */

        $this->getLogger();
    }

    /**
     * Gets the PEAR Log object to use.
     *
     * @return Log  A Log object, either provided by the user or
     *              created by this function.
     */
    function &getLogger()
    {
        if (!$this->_logger) {
            $file = $this->_logDir . '/' . date('Ymd') . '.PayPalSamples.log';
            if (is_link($file) || (file_exists($file) && !is_writable($file))) {
                // Don't overwrite symlinks.
                // return PayPal::raiseError('bad logfile');
            }

            $this->_logger = &Log::singleton('file', $file, 'CC_transaction', array('append' => true));
        }

        return $this->_logger;
    }

    /**
     * Sets a custom PEAR Log object to use in logging.
     *
     * @param Log  A PEAR Log instance.
     */
    function setLogger(&$logger)
    {
        if (is_a($logger, 'Log')) {
            $this->_logger =& $logger;
        }
    }

    /**
     * Log a string
     *
     * @access protected
     *
     * @param string $msg.  Log this string.
     */
    function _log($msg)
    {
        $logger =& $this->getLogger();
        $clean = $this->_sanitizeLog($msg);
        switch ($this->_logLevel) {
        case PEAR_LOG_DEBUG:
        case PEAR_LOG_INFO:
        case PEAR_LOG_ERR:
            $logger->log('Log Data ' . $this->_logSample. ': ' . $clean, $this->_logLevel);
        }
    }

    /**
     * Strip sensitive information (API passwords and credit card
     * numbers) from log info.
     *
     * @access protected
     *
     * @param string $msg  The string to sanitize.
     *
          [_password] => 12345678
          [creditCardNumber] => 4489251633598686
          [apiPassword] => WZPZJEKN9QQJKVPD
          [signature] => AeM1g39auuNZ1gPUSxogAKLX6.olAtBMuUZOiANhkYgZYaaXPHeR.9tN
          [_password] => WZPZJEKN9QQJKVPD
          [_signature] => AeM1g39auuNZ1gPUSxogAKLX6.olAtBMuUZOiANhkYgZYaaXPHeR.9tN
          [CreditCardNumber] => 4036390962483116
     *
     * @return string  The sanitized string.
     */
    function _sanitizeLog($msg)
    {
        return preg_replace(array('/(\[_?password\]\s*=>) (.+)/i',
                                  '/(\[creditCardNumber\]\s*=>) (.+)/i',
                                  '/(\[apiPassword\]\s=>) (.+)/i',
                                  '/(\[CreditCardNumber\]\s*=>) (.+)/i',
                                  '/(\[_?signature\]\s*=>) (.+)/i'),
                            '\1 **MASKED** ',
                            $msg);
        $this->_log('_sanitizeLog called...');
    }

}
