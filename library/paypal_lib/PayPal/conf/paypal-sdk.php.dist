<?php
/**
 * Example PayPal SDK configuration file.
 */

/**
 * What level should we log at? Valid levels are:
 *   PEAR_LOG_ERR   - Log only severe errors.
 *   PEAR_LOG_INFO  - (default) Date/time of operation, operation name, elapsed time, success or failure indication.
 *   PEAR_LOG_DEBUG - Full text of SOAP requests and responses and other debugging messages.
 *
 * See the PayPal SDK User Guide for more details on these log levels.
 */

$__PP_CONFIG['log_level'] = PEAR_LOG_DEBUG; 

/**
 * If we're logging, what directory should we create log files in?
 * Note that a log name coincides with a symlink, logging will *not*
 * be done to avoid security problems. File names are
 * <DateStamp>.PayPal.log.
 */

$__PP_CONFIG['log_dir'] = '/tmp';

/**
 * The path where custom profile storage handlers are located.
 * 
 * IMPORTANT: Custom handler classes must be named ProfileHandler_<Handler Name>
 * and be stored in the file <Handler Name>.php within this directory
 */
$__PP_CONFIG['custom_handler_dir'] = array('/path/to/custom/handlers');

/**
 * Where Profile certificates will be stored. Must be writable by the 
 * web server.
 */
$__PP_CONFIG['profile_cert_dir'] = '/tmp';

/**
 * The Location of the public Paypal certificate file
 */
$__PP_CONFIG['paypal_cert_file']['Live'] = '/path/to/cert/Livefile.crt';
$__PP_CONFIG['paypal_cert_file']['Sandbox'] = '/path/to/cert/Sandboxfile.crt';