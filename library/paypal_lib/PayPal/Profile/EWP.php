<?php
/**
 * @package PayPal
 *
 * $Id: EWP.php,v 1.1.1.1 2006/02/19 08:15:20 dennis Exp $
 */

/**
 * Include parent and package classes.
 */
require_once 'PayPal.php';
require_once 'PayPal/Profile.php';
require_once 'PayPal/EWPServices.php';

/**
 * Stores EWP Profile information used for encrypting buttons and PayPal forms.
 *
 * @package PayPal
 */
class EWPProfile extends Profile
{
    /**
     * The private Key file
     *
     * @access private
     */
    var $_privateKeyFile;

    /**
     * The password on the private key.
     *
     * @access private
     */
    var $_privateKeyPassword;

    /**
     * The URL to the button image
     *
     * @access private
     */
    var $_buttonImageURL;

    /**
     * The URL the button posts to
     *
     * @access private
     */
    var $_buttonURL;

    /**
     * The PayPal-assigned id of the certificate.
     *
     * @access private
     */
    var $_certificateId;

    /**
     * The location of the .pem certificate file.
     *
     * @access private
     */
    var $_certificateFile;

    /**
     * Class constructor
     *
     * @param ProfileHandler &$handler  A handler where the profile should be stored.
     */
    function EWPProfile($id, &$handler)
    {
        parent::Profile($id, $handler);
    }

    /**
     * Validates the profile data currently loaded before use.
     *
     * @return mixed true if the data is valid, or a PayPal_Error object on failure.
     */
    function validate()
    {
        if (empty($this->_certificateFile)) {
            return PayPal::raiseError("Certificate File must be set!");
        }
        if (empty($this->_certificateId)) {
            return PayPal::raiseError("Certificate ID must be set.");
        }
        if (empty($this->_environment)) {
            return PayPal::raiseError("Environment must be set.");
        }
        if (!file_exists($this->_certificateFile)) {
            return PayPal::raiseError("Could not find certificate file '{$this->_certificateFile}'");
        }
        if (!in_array(strtolower($this->_environment), $this->_validEnvironments, true)) {
            return PayPal::raiseError("Environment '{$this->_environment}' is not a valid environment.");
        }

        return true;
    }

    /**
     * Get the merchant certificate id associated with the profile
     *
     * @return string The certificate id associated with the profile
     */
    function getCertificateId()
    {
        return $this->_certificateId;
    }

    /**
     * Set the merchant certificate id associated with the profile
     *
     * @param string The certificate id associated with the profile
     */
    function setCertificateId($filename)
    {
        $this->_certificateId = $filename;
    }

    /**
     * Get the merchant certificate file associated with the profile
     *
     * @return string The certificate file associated with the profile
     */
    function getCertificateFile()
    {
        return $this->_certificateFile;
    }

    /**
     * Set the merchant certificate file associated with the profile
     *
     * @param string The certificate file associated with the profile
     */
    function setCertificateFile($filename)
    {
        if (!file_exists($filename)) {
            return PayPal::raiseError("The private key '$filename' does not exist");
        }

        $this->_certificateFile = $filename;
    }

    /**
     * Returns the URL where the button image is
     *
     * @return string The URL to the button image
     */
    function getButtonImage()
    {
        return $this->_buttonImageURL;
    }

    /**
     * Set the URL where the button image is
     *
     * @param string The URL to the button image
     */
    function setButtonImage($url)
    {
        $this->_buttonImageURL = $url;
    }

    /**
     * Returns the URL where the button will post to
     *
     * @return string The URL where the button will post to
     */
    function getUrl()
    {
        return $this->_buttonURL;
    }

    /**
     * Sets the URL where the button will post to
     *
     * @param string The URL where the button should post to
     */
    function setUrl($url)
    {
        $this->_buttonURL = $url;
    }

    /**
     * Set the Merchant private key file
     *
     * @param string The Merchant Private Key File
     * @return mixed True on success, a PayPal error object on faliure
     */
    function setPrivateKeyFile($filename)
    {
        if (!file_exists($filename)) {
            return PayPal::raiseError("The private key '$filename' does not exist");
        }

        $this->_privateKeyFile = $filename;

        return true;
    }

    /**
     * Get the merchant private key file associated with the profile
     *
     * @return string The merchant private key file associated with the profile
     */
    function getPrivateKeyFile()
    {
        return $this->_privateKeyFile;
    }

    /**
     * Set the merchant private key password
     *
     * @param string The private key password
     */
    function setPrivateKeyPassword($password)
    {
        $this->_privateKeyPassword = $password;
    }

    /**
     * Get the merchant private key password
     *
     * @return string  The private key password.
     */
    function getPrivateKeyPassword()
    {
        return $this->_privateKeyPassword;
    }

    /**
     * Returns an array of member variables names which should be included
     * when storing the profile.
     *
     * @return array An array of member variable names which should be included
     * @access protected
     */
    function _getSerializeList()
    {
        return array('environment', 'certificateId', 'certificateFile', 'privateKeyFile', 'buttonImageURL', 'buttonURL');
    }

    /**
     * Factory for creating instances of the EWPProfile. Used when
     * providing an existing Profile ID to load from
     *
     * @param string The Profile ID of this instance
     * @param object A valid Profile Handler instance
     * @return object A new instance of EWPProfile for the given ID or a PayPal error object on failure
     */
    function getInstance($id, &$handler)
    {
        $classname = __CLASS__;
        $inst = new $classname($id, $handler);
        $result = $inst->_load();

        if (PayPal::isError($result)) {
            return $result;
        }

        return $inst;
    }

}
