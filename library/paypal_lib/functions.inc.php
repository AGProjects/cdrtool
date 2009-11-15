<?php
/**
 * Support functions for the Profile Admin tool
 *
 * $Id: functions.inc.php,v 1.1 2006/02/27 06:57:08 dennis Exp $
 *
 * @package PayPal
 */

require_once 'PayPal.php';

/**
 * Iterates over an array of input parameters, calling a callback
 * for each. This callback must return true if the parameter was
 * valid or a string error message on failure. All keys in this
 * array must have a callback function.
 *
 * @param array An array of input parameters
 * @return array An array of error messages (empty array if none)
 */
function validate_form_input($vars)
{
    $errors = false;

    foreach($_POST as $var => $value)
    {
        $function_name = "form_validate_$var";

        if(!function_exists($function_name))
        {
            $errors['unknown'][] = "Unknown submission variable $var, aborting.";
        }
        else
        {
            if(is_string(($retval = $function_name($value))))
            {
                $errors[$var] = $retval;
            }
        }

    }

    return $errors;
}

/**
 * Loads all of the Profile Handler classes that can be found
 *
 * @internal
 * @param array An array of paths to search for classes in
 * @param string An optional string representing the particular handler we're looking for
 * @return array An array of handlers loaded and their parameters
 */
function _loadProfileHandlerClasses($handler_paths, $h_name = null)
{
    $loaded_handlers = array();

    foreach($handler_paths as $path)
    {
        $files = glob("$path/*.php");

        foreach($files as $handler_file)
        {
            $handler_filename = basename($handler_file);
            $handler_name = substr($handler_filename, 0, strpos($handler_filename, '.'));
            $handler_cname = "ProfileHandler_$handler_name";

            if(is_null($h_name) || (strtolower($h_name) == strtolower($handler_name)))
            {
                if(@include $handler_file)
                {
                    if(!class_exists($handler_cname))
                    {
                        trigger_error("Warning: Trying to load profile handler '$handler_cname' (should have been declared in '$handler_file')", E_USER_WARNING);
                    }
                    else
                    {
                        $t = new $handler_cname(array());
                        $loaded_handlers[$handler_name] = $t->getParamInfo();
                        unset($t);
                    }
                } else {
                    trigger_error("Warning: Could not load file '$handler_file'", E_USER_WARNING);
                }
            }
        }
    }

    return $loaded_handlers;
}

/**
 * Returns the path where Profile Certificates should be saved taken from
 * the PayPal SDK configuration file with checks to make sure it's sane.
 *
 * @internal
 * @return mixed A string containing the path to save the certificate files at
 *               or a PayPal error object on failure.
 */
function _getProfileCertSavePath()
{
    $package_root = PayPal::getPackageRoot();

    $path = '/tmp';

    if(@include "$package_root/conf/paypal-sdk.php")
    {
        if(isset($__PP_CONFIG['profile_cert_dir']) &&
           !empty($__PP_CONFIG['profile_cert_dir']))
        {
            $path =  $__PP_CONFIG['profile_cert_dir'];
        }
    }

    if(!file_exists($path))
    {
        return PayPal::raiseError("Certificate save path '$path' does not exist.");
    }

    if(!is_dir($path))
    {
        return PayPal::raiseError("You must specify a certificate save directory, '$path' is a file.");
    }

    if(!is_writeable($path))
    {
        return PayPal::raiseError("The path '$path' must be writeable by the web server.");
    }

    return $path;
}

/**
 * Returns an array of paths where the Admin should search for Profile Handler classes
 *
 * @internal
 * @return array An array of paths to search
 */
function _getHandlerPaths()
{
    $package_root = PayPal::getPackageRoot();

    $handler_paths = array("$package_root/Profile/Handler");

    if(@include "$package_root/conf/paypal-sdk.php")
    {
        if(isset($__PP_CONFIG['custom_handler_dir']) &&
           is_array($__PP_CONFIG['custom_handler_dir']))
        {
            $handler_paths = array_merge($__PP_CONFIG['custom_handler_dir'], $handler_paths);
        }
    }

    return $handler_paths;
}

/**
 * Returns an instance of the specified handler for use
 *
 * @internal
 * @param string The name of the handler (i.e. 'File', 'Array)
 * @param array An array of parameters for the specified handler
 * @return object A new instance of the Handler or a PayPal error object on failure
 */
function &_getHandlerInstance($handler_name, $handler_params)
{
    $handler_paths = _getHandlerPaths();
    $handler_classname = "ProfileHandler_$handler_name";

    _loadProfileHandlerClasses($handler_paths, $handler_name);

    if(!class_exists($handler_classname))
    {
        return PayPal::raiseError("Could not load handler '$handler_name'");
    }

    $inst = new $handler_classname($handler_params);
    return $inst;
}

?>