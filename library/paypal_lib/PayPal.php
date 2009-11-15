<?php
/**
 * Base PayPal SDK file.
 *
 * @package PayPal
 */

/**
 * Include our error class and the PEAR base class.
 */
require_once 'PEAR.php';
require_once 'PayPal/Error.php';

/**
 * End point for users to access the PayPal API, provides utility
 * methods used internally as well as factory methods.
 *
 * $Id: PayPal.php,v 1.1.1.1 2006/02/19 08:15:19 dennis Exp $
 *
 * @static
 * @package PayPal
 */
class PayPal extends PEAR
{
    /**
     * Raise an error when one occurs
     *
     * @static
     */
    function raiseError($message, $code = null)
    {
        return parent::raiseError($message, $code, null, null, null, 'PayPal_Error');
    }

    /**
     * Try to instantiate the class for $type. Looks inside the Type/
     * directory containing all generated types. Allows for run-time
     * loading of needed types.
     *
     * @param string $type  The name of the type (eg. AbstractRequestType).
     *
     * @return mixed XSDType | PayPal_Error  Either an instance of $type or an error.
     *
     * @static
     */
    function &getType($type)
    {
        $type = basename($type);
        @include_once 'PayPal/Type/' . $type . '.php';
        if (!class_exists($type)) {
            $t = PayPal::raiseError("Type $type is not defined");
        } else {
            $t = new $type();
        }
        return $t;
    }

    /**
     * Load a CallerServices object for making API calls.
     *
     * @param APIProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             CallerServices.
     *
     * @return CallerServices  A PayPal API caller object.
     *
     * @static
     */
    function &getCallerServices($profile)
    {
        if (!defined('CURLOPT_SSLCERT')) {
            $e = PayPal::raiseError("The PayPal SDK requires curl with SSL support");
            return $e;
        }

        if (!is_a($profile, 'APIProfile')) {
            $e = PayPal::raiseError("You must provide a valid APIProfile");
            return $e;
        }

        $result = $profile->validate();
        if (PayPal::isError($result)) {
            return $result;
        }

        include_once 'PayPal/CallerServices.php';
        $c =& new CallerServices($profile);
        return $c;
    }

    /**
     * Load an EWPServices object for performing encryption
     * operations.
     *
     * @param EWPProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             EWPServices.
     *
     * @return EWPServices  A PayPal EWP services object.
     *
     * @static
     */
    function &getEWPServices($profile)
    {
        if (!is_a($profile, 'EWPProfile')) {
            return parent::raiseError("You must provide a valid EWPProfile");
        }

        $result = $profile->validate();
        if (PayPal::isError($result)) {
            return $result;
        }

        include_once 'PayPal/EWPServices.php';
        return $c =& new EWPServices($profile);
    }

    /**
     * Returns the package root directory.
     *
     * @return string  The path where the package is installed.
     *
     * @static
     */
    function getPackageRoot()
    {
        return dirname(__FILE__) . '/PayPal';
    }

    /**
     * Returns the version of the WSDL that this SDK is built against.
     *
     * @return float  The WSDL version.
     *
     * @static
     */
    function getWSDLVersion()
    {
        include_once 'PayPal/CallerServices.php';
        return PAYPAL_WSDL_VERSION;
    }

    /**
     * Returns the endpoint map.
     *
     * @return mixed The Paypal endpoint map or a Paypal error object on failure
     * @static
     */
    function getEndpoints()
    {
        $package_root = PayPal::getPackageRoot();
        $file = "$package_root/wsdl/paypal-endpoints.php";
        if (@include $file) {
            if (!isset($PayPalEndpoints)) {
                return PayPal::raiseError("Endpoint map file found, but no data was found.");
            }

            return $PayPalEndpoints;
        }

        return PayPal::raiseError("Could not load endpoint mapping from '$file', please rebuild SDK.");
    }

    /**
     * Get information describing all types provided by the SDK.
     * @static
     */
    function getTypeList()
    {
        $root_dir = PayPal::getPackageRoot();
        $types = "$root_dir/Type/*.php";

        $files = glob($types);

        if (count($files) < 2) {
            return PayPal::raiseError("Types not found in package! (Looked for '$types')");
        }

        $retval = array();

        foreach ($files as $type_files) {
            $retval[] = basename(substr($type_files, 0, strpos($type_files, '.')));
        }

        return $retval;
    }

    /**
     * Get information describing which methods are available.
     * @static
     */
    function getCallerServicesIntrospection()
    {
        include_once 'PayPal/CallerServices.php';
        return unserialize(PAYPAL_WSDL_METHODS);
    }

}
