<?php
/**
 * @package PayPal
 */

/**
 * Include SDK base class.
 */
require_once 'PayPal.php';

/**
 * The base class for all Profile handlers
 *
 * @package PayPal
 * @abstract
 */
class ProfileHandler
{
    /**
     * @access private
     */
    var $_params;

    function ProfileHandler($parameters)
    {
        $this->_params = $parameters;
    }

    function listProfiles()
    {
        return PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function loadProfile($id)
    {
        return PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function saveProfile($data)
    {
        return PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function getParamInfo()
    {
        return null;
    }

    function deleteProfile($id)
    {
        return PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function generateID()
    {
        return md5(uniqid(mt_rand(), true));
    }

    function validateParams()
    {
        return true;
    }

    function &getInstance($params)
    {
        return PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function initialize()
    {
        return true;
    }

}
