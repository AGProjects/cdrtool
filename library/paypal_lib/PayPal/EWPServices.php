<?php
/**
 * File containing the encryption services code.
 *
 * @package PayPal
 */

/**
 * Load files we depend on.
 */
require_once 'PayPal.php';

/**
 * API for doing PayPal encryption services.
 *
 * @package PayPal
 */
class EWPServices
{
    /**
     * The profile to use for encryption.
     *
     * @access protected
     *
     * @var EWPProfile $_profile
     */
    var $_profile;

    /**
     * Construct a new EWP services object.
     *
     * @param EWPProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             the SDK.
     */
    function EWPServices($profile)
    {
        $this->setEWPProfile($profile);
    }

    /**
     * Use a given profile.
     *
     * @param EWPProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             the SDK.
     */
    function setEWPProfile($profile)
    {
        $this->_profile = $profile;
    }

    /**
     * Get the current profile.
     *
     * @return EWPProfile  The current profile.
     */
    function getEWPProfile()
    {
        return $this->_profile;
    }

    /**
     * Creates a new encrypted button HTML block
     *
     * @param array The button parameters as key/value pairs
     * @return mixed A string of HTML or a Paypal error object on failure
     */
    function encryptButton($buttonParams)
    {
        if (!is_object($this->_profile)) {
            return PayPal::raiseError("No Profile is set, cannot encrypt");
        }

        $res = $this->_profile->validate();
        if (PayPal::isError($res)) {
            return $res;
        }

        $merchant_cert = 'file://' . $this->_profile->getCertificateFile();
        $merchant_key = 'file://' . $this->_profile->getPrivateKeyFile();
        $enc_cert = 'file://' . $this->getPayPalCertificateFile($this->_profile->getEnvironment());

        $tmpin_file  = tempnam('/tmp', 'paypal_');
        $tmpout_file = tempnam('/tmp', 'paypal_');
        $tmpfinal_file = tempnam('/tmp', 'paypal_');

        $rawdata = array();
        $buttonParams['cert_id'] = $this->_profile->getCertificateId();
        foreach ($buttonParams as $name => $value) {
            $rawdata[] = "$name=$value";
        }
        $rawdata = implode("\n", $rawdata);

        $fp = fopen($tmpin_file, 'w');
        if (!$fp) {
            return PayPal::raiseError("Could not open temporary file '$tmpin_file')");
        }
        fwrite($fp, $rawdata);
        fclose($fp);

        if (!@openssl_pkcs7_sign($tmpin_file, $tmpout_file, $merchant_cert,
                                 array($merchant_key, $this->_profile->getPrivateKeyPassword()),
                                 array(), PKCS7_BINARY)) {
            return PayPal::raiseError("Could not sign encrypted data: " . openssl_error_string());
        }

        $data = file_get_contents($tmpout_file);
        $data = explode("\n\n", $data);
        $data = $data[1];
        $data = base64_decode($data);
        $fp = fopen($tmpout_file, 'w');
        if (!$fp) {
            return PayPal::raiseError("Could not open temporary file '$tmpin_file')");
        }
        fwrite($fp, $data);
        fclose($fp);

        if (!@openssl_pkcs7_encrypt($tmpout_file, $tmpfinal_file, $enc_cert, array(), PKCS7_BINARY)) {
            return PayPal::raiseError("Could not encrypt data:" . openssl_error_string());
        }

        $encdata = @file_get_contents($tmpfinal_file, false);
        if (!$encdata) {
            return PayPal::raiseError("Encryption and signature of data failed.");
        }

        $encdata = explode("\n\n", $encdata);
        $encdata = trim(str_replace("\n", '', $encdata[1]));
        $encdata = "-----BEGIN PKCS7-----$encdata-----END PKCS7-----";

        @unlink($tmpfinal_file);
        @unlink($tmpin_file);
        @unlink($tmpout_file);

        $action = $this->_profile->getUrl();
        $buttonimgurl = $this->_profile->getButtonImage();

        $retval = <<< PPHTML
<FORM ACTION="$action" METHOD="post">
<INPUT TYPE="hidden" NAME="cmd" VALUE="_s-xclick">
<INPUT TYPE="hidden" NAME="encrypted" VALUE="$encdata">
<INPUT TYPE="image" SRC="$buttonimgurl" BORDER="0" NAME="submit" ALT="Make Payments with PayPal -- it's fast, free and secure!">
</FORM>
PPHTML;
        return $retval;
    }

    /**
     * Returns the PayPal public certificate filename.
     *
     * @param string The environment to get the certificate for.
     * @return mixed The path and file of the certificate file, or a PayPal error object on failure.
     */
    function getPayPalCertificateFile($environment)
    {
        $package_root = PayPal::getPackageRoot();
        $cert = $package_root . '/cert/' . strtolower($environment) . '.paypal.com.pem';

        if (@include "$package_root/conf/paypal-sdk.php") {
            if (isset($__PP_CONFIG['paypal_cert_file']) &&
                !empty($__PP_CONFIG['paypal_cert_file'])) {
                $cert =  $__PP_CONFIG['paypal_cert_file'][$environment];
            }
        }

        if (!file_exists($cert)) {
            return PayPal::raiseError("Could not file Paypal public Certificate file '$cert'");
        }

        return $cert;
    }

}
