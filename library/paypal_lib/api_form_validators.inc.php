<?php
/**
 * Form element validation callbacks for the API Profile manager
 *
 * $Id: api_form_validators.inc.php,v 1.2 2006/02/28 02:01:17 dennis Exp $
 *
 * @package PayPal
 */

/**
 * Validates the API username
 *
 * @param string The API username to validate
 * @return mixed True if valid, a string indicating the problem if invalid
 */
function form_validate_apiUsername($value)
{
    if(empty($value))
    {
        return "You must provide an API username";
    }

    return true;
}

function form_validate_apiPassword($value)
{
    if(empty($value))
    {
        return "You must provide an API password";
    }

    return true;
}

/**
 * Validates the API subject
 *
 * @param string The API subject to validate
 * @return mixed True if valid, a string indicating the problem if invalid
 */
function form_validate_apiSubject($value)
{
    return true;
}

/**
 * Validate the API environment
 *
 * @param string the API environment to validate
 * @return mixed True if valid, a string indicating the problem if invalid
 */
function form_validate_environment($value)
{
    $profile = @new APIProfile();
    $valid = $profile->getValidEnvironments();

    if(!in_array($value, $valid, true))
    {
        return "You must specify a valid environment for the profile";
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
