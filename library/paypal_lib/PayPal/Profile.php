<?php
/**
 * @package PayPal
 *
 * $Id: Profile.php,v 1.1.1.1 2006/02/19 08:15:20 dennis Exp $
 */

/**
 * Base package class.
 */
require_once 'PayPal.php';

/**
 * Base class for PayPal Profiles, managaes interaction with handlers, etc.
 *
 * @package PayPal
 * @abstract
 */
class Profile
{
    /**
     * Which environment should API calls be made against?
     *
     * @see $_validEnvironments
     *
     * @access protected
     */
    var $_environment;

    /**
     * The list of valid environments that API calls can be executed
     * against.
     *
     * @access protected
     */
    var $_validEnvironments = array();

    /**
     * The ProfileHandler instance associated with this Profile.
     *
     * @access protected
     */
    var $_handler;

    /**
     * The ProfileHandler ID.
     *
     * @access protected
     */
    var $_handler_id;

    /**
     * Base constructor which creates a default handler if none was
     * provided.
     *
     * @param string The name of the profile
     * @param object An optional handler to store the profile in
     */
    function Profile($id, &$handler)
    {
        $this->_handler =& $handler;
        $this->_handler_id = $id;
    }

    /**
     * Loads the profile data from the defined handler.
     *
     * @return mixed true on success or a PayPal_Error object on failure
     * @final
     * @access private
     */
    function _load()
    {
        $loaded_data = $this->_handler->loadProfile($this->_handler_id);
        $expected_keys = $this->_getSerializeList();

        if (!is_a($this->_handler, 'ProfileHandler_Array')) {
            $expected_keys[] = 'classname';
        }

        if (PayPal::isError($loaded_data)) {
            foreach ($expected_keys as $key) {
                $ikey = "_$key";
                $this->$ikey = null;
            }

            return PayPal::raiseError("Could not load data for non-existant profile '{$this->_handler_id}'");
        }

        $expected_keys = array_flip($expected_keys);
        foreach ($loaded_data as $key => $value) {
            $ikey = "_$key";
            $this->$ikey = $value;
            unset($expected_keys[$key]);
        }

        if (!empty($expected_keys)) {
            $key_list = implode(', ', array_flip($expected_keys));
            return PayPal::raiseError("The following values were expected but not found in the profile data: $key_list");
        }

        return $this->loadEnvironments();
    }

    function getID()
    {
        return $this->_handler_id;
    }

    function getInstance($id, &$handler)
    {
        return PayPal::raiseError("Cannot return an instance of the base Profile class");
    }

    /**
     * Loads the environment names from the endpoint map.
     *
     * @return mixed True on success or a PayPal error object on failure
     */
    function loadEnvironments()
    {
        $version = PayPal::getWSDLVersion();
        $endpoints = PayPal::getEndpoints();
        if (PayPal::isError($endpoints)) {
            return $endpoints;
        }

        foreach ($endpoints as $range) {
            if ($version >= $range['min'] &&
                $version <= $range['max']) {
                foreach (array_keys($range['environments']) as $environment) {
                    $this->_validEnvironments[] = strtolower($environment);
                }
                return true;
            }
        }

        return PayPal::raiseError("Could not find any endpoint mappings for WSDL version '$version'");
    }

    /**
     * Saves the profile data to the defined handler.
     *
     * @return mixed true on success or a PayPal_Error object on failure
     * @final
     */
    function save()
    {
        $values = $this->_getSerializeList();
        foreach ($values as $value) {
            $ivalue = "_$value";
            if (isset($this->$ivalue)) {
                $data[$value] = $this->$ivalue;
            } else {
                $data[$value] = null;
            }
        }

        $data['classname'] = get_class($this);

        return $this->_handler->saveProfile($data, $this->_handler_id);
    }

    /**
     * Returns an array of member variables names which should be
     * included when storing the profile.
     *
     * @return array An array of member variable names which should be included
     * @access protected
     */
    function _getSerializeList()
    {
        return array();
    }

    /**
     * Set the environment associated with this profile.
     *
     * @param string True on success, a Paypal error object on failure
     */
    function setEnvironment($environment)
    {
        $environment = strtolower($environment);

        $envs = $this->getValidEnvironments();
        if (PayPal::isError($envs)) {
            return $envs;
        }

        if (in_array($environment, $envs)) {
            $this->_environment = $environment;
            return true;
        }

        return PayPal::raiseError("Invalid Environment Specified");
    }

    /**
     * Get the environment associated with the profile.
     *
     * @return string The environment associated with the profile.
     */
    function getEnvironment()
    {
        return strtolower($this->_environment);
    }

    /**
     * Returns an array of valid Environments
     *
     * @return array An array of valid environment names
     */
    function getValidEnvironments()
    {
        if (empty($this->_validEnvironments)) {
            $res = $this->loadEnvironments();
            if (PayPal::isError($res)) {
                return $res;
            }
        }

        return $this->_validEnvironments;
    }

}
