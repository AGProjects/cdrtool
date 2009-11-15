<?php
/**
 * Form element validation callbacks for the EWP manager
 *
 * $Id: ewp_form_validators.inc.php,v 1.1 2006/02/27 06:57:08 dennis Exp $
 *
 * @package PayPal
 */


/**
 * Validate the EWP environment
 *
 * @param string the EWP environment to validate
 * @return mixed True if valid, a string indicating the problem if invalid
 */
function form_validate_environment($value)
{
    $profile = @new EWPProfile();
    $valid = $profile->getValidEnvironments();

    if(!in_array($value, $valid, true))
    {
        return "You must specify a valid environment for the profile";
    }

    return true;
}

/**
 * Validate the EWP certificate id.
 *
 * @param string the EWP certificate id to validate
 * @return mixed True if valid, a string indicating the problem if invalid
 */
function form_validate_cert_id($value)
{
    if(!$value)
    {
        return "You must specify a certificate id for the profile";
    }

    return true;
}

/**
 * Validates the button URL
 *
 * @param string the button URL
 * @return mixed True if valid, a stirng indicating why it is not valid if invalid
 */
function form_validate_buttonURL($value)
{
    if(!empty($value))
    {
        if(ini_get("allow_fopen_url"))
        {
            $fr = fopen($value, 'r');

            if(!$fr)
            {
                return "Could not open remote URL '$value', please confirm and try again";
            }

            fclose($fr);
        }
    }

    return true;
}

/**
 * Validates the button Image URL
 *
 * @param string The button Image URL
 * @return mixed True if successful, a string indicating the error if invalid
 */
function form_validate_buttonImageURL($value)
{
    if(!empty($value))
    {
        if(ini_get("allow_fopen_url"))
        {
            $fr = fopen($value, 'r');

            if(!$fr)
            {
                return "Could not open remote URL '$value', please confirm and try again";
            }

            fclose($fr);
        }
    }
    return true;
}

/**
 * Validates the EWP profile ID
 *
 * @param string The Profile ID
 * @return mixed True if valid, a string indicating why it is not valid if invalid
 */
function form_validate_pid($value)
{
    if(empty($value))
    {
        return "No profile ID provided, cannot locate record.";
    }

    return true;
}

/**
 * Validates the Certificate file for the EWP profile
 *
 * @param string The Certificate file for the profile
 * @return mixed True if valid, a stirng indicating why it is not valid if invalid
 */
function form_validate_certificate_file($value)
{
    if(!file_exists($value))
    {
        return "Certificate file '$value' does not exist.";
    }

    return true;
}
