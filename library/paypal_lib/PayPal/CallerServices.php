<?php
/**
 * File containing the API calling code. Partially generated from the
 * WSDL - CallerServices.php.in contains the base file with a
 * placeholder for generated functions and the WSDL version.
 *
 * @package PayPal
 */

/**
 * Load files we depend on.
 */
require_once 'PayPal.php';
require_once 'PayPal/SOAP/Client.php';
require_once 'PayPal/Type/XSDSimpleType.php';
require_once 'Log.php';

/**
 * The WSDL version the SDK is built against.
 */
define('PAYPAL_WSDL_VERSION', 60);

/**
 * The WSDL version the SDK is built against.
 */
define('PAYPAL_WSDL_METHODS', 'a:48:{s:17:"RefundTransaction";a:2:{s:5:"param";s:28:"RefundTransactionRequestType";s:6:"result";s:29:"RefundTransactionResponseType";}s:21:"GetTransactionDetails";a:2:{s:5:"param";s:32:"GetTransactionDetailsRequestType";s:6:"result";s:33:"GetTransactionDetailsResponseType";}s:14:"BMCreateButton";a:2:{s:5:"param";s:25:"BMCreateButtonRequestType";s:6:"result";s:26:"BMCreateButtonResponseType";}s:14:"BMUpdateButton";a:2:{s:5:"param";s:25:"BMUpdateButtonRequestType";s:6:"result";s:26:"BMUpdateButtonResponseType";}s:20:"BMManageButtonStatus";a:2:{s:5:"param";s:31:"BMManageButtonStatusRequestType";s:6:"result";s:32:"BMManageButtonStatusResponseType";}s:18:"BMGetButtonDetails";a:2:{s:5:"param";s:29:"BMGetButtonDetailsRequestType";s:6:"result";s:30:"BMGetButtonDetailsResponseType";}s:14:"BMSetInventory";a:2:{s:5:"param";s:25:"BMSetInventoryRequestType";s:6:"result";s:26:"BMSetInventoryResponseType";}s:14:"BMGetInventory";a:2:{s:5:"param";s:25:"BMGetInventoryRequestType";s:6:"result";s:26:"BMGetInventoryResponseType";}s:14:"BMButtonSearch";a:2:{s:5:"param";s:25:"BMButtonSearchRequestType";s:6:"result";s:26:"BMButtonSearchResponseType";}s:8:"BillUser";a:2:{s:5:"param";s:19:"BillUserRequestType";s:6:"result";s:20:"BillUserResponseType";}s:17:"TransactionSearch";a:2:{s:5:"param";s:28:"TransactionSearchRequestType";s:6:"result";s:29:"TransactionSearchResponseType";}s:7:"MassPay";a:2:{s:5:"param";s:18:"MassPayRequestType";s:6:"result";s:19:"MassPayResponseType";}s:19:"BillAgreementUpdate";a:2:{s:5:"param";s:19:"BAUpdateRequestType";s:6:"result";s:20:"BAUpdateResponseType";}s:13:"AddressVerify";a:2:{s:5:"param";s:24:"AddressVerifyRequestType";s:6:"result";s:25:"AddressVerifyResponseType";}s:13:"EnterBoarding";a:2:{s:5:"param";s:24:"EnterBoardingRequestType";s:6:"result";s:25:"EnterBoardingResponseType";}s:18:"GetBoardingDetails";a:2:{s:5:"param";s:29:"GetBoardingDetailsRequestType";s:6:"result";s:30:"GetBoardingDetailsResponseType";}s:19:"CreateMobilePayment";a:2:{s:5:"param";s:30:"CreateMobilePaymentRequestType";s:6:"result";s:31:"CreateMobilePaymentResponseType";}s:15:"GetMobileStatus";a:2:{s:5:"param";s:26:"GetMobileStatusRequestType";s:6:"result";s:27:"GetMobileStatusResponseType";}s:17:"SetMobileCheckout";a:2:{s:5:"param";s:28:"SetMobileCheckoutRequestType";s:6:"result";s:29:"SetMobileCheckoutResponseType";}s:23:"DoMobileCheckoutPayment";a:2:{s:5:"param";s:34:"DoMobileCheckoutPaymentRequestType";s:6:"result";s:35:"DoMobileCheckoutPaymentResponseType";}s:10:"GetBalance";a:2:{s:5:"param";s:21:"GetBalanceRequestType";s:6:"result";s:22:"GetBalanceResponseType";}s:13:"GetPalDetails";a:2:{s:5:"param";s:24:"GetPalDetailsRequestType";s:6:"result";s:25:"GetPalDetailsResponseType";}s:24:"DoExpressCheckoutPayment";a:2:{s:5:"param";s:35:"DoExpressCheckoutPaymentRequestType";s:6:"result";s:36:"DoExpressCheckoutPaymentResponseType";}s:28:"DoUATPExpressCheckoutPayment";a:2:{s:5:"param";s:39:"DoUATPExpressCheckoutPaymentRequestType";s:6:"result";s:40:"DoUATPExpressCheckoutPaymentResponseType";}s:16:"SetAuthFlowParam";a:2:{s:5:"param";s:27:"SetAuthFlowParamRequestType";s:6:"result";s:28:"SetAuthFlowParamResponseType";}s:14:"GetAuthDetails";a:2:{s:5:"param";s:25:"GetAuthDetailsRequestType";s:6:"result";s:26:"GetAuthDetailsResponseType";}s:20:"SetAccessPermissions";a:2:{s:5:"param";s:31:"SetAccessPermissionsRequestType";s:6:"result";s:32:"SetAccessPermissionsResponseType";}s:23:"UpdateAccessPermissions";a:2:{s:5:"param";s:34:"UpdateAccessPermissionsRequestType";s:6:"result";s:35:"UpdateAccessPermissionsResponseType";}s:26:"GetAccessPermissionDetails";a:2:{s:5:"param";s:37:"GetAccessPermissionDetailsRequestType";s:6:"result";s:38:"GetAccessPermissionDetailsResponseType";}s:18:"SetExpressCheckout";a:2:{s:5:"param";s:29:"SetExpressCheckoutRequestType";s:6:"result";s:30:"SetExpressCheckoutResponseType";}s:25:"GetExpressCheckoutDetails";a:2:{s:5:"param";s:36:"GetExpressCheckoutDetailsRequestType";s:6:"result";s:37:"GetExpressCheckoutDetailsResponseType";}s:15:"DoDirectPayment";a:2:{s:5:"param";s:26:"DoDirectPaymentRequestType";s:6:"result";s:27:"DoDirectPaymentResponseType";}s:30:"ManagePendingTransactionStatus";a:2:{s:5:"param";s:41:"ManagePendingTransactionStatusRequestType";s:6:"result";s:42:"ManagePendingTransactionStatusResponseType";}s:9:"DoCapture";a:2:{s:5:"param";s:20:"DoCaptureRequestType";s:6:"result";s:21:"DoCaptureResponseType";}s:17:"DoReauthorization";a:2:{s:5:"param";s:28:"DoReauthorizationRequestType";s:6:"result";s:29:"DoReauthorizationResponseType";}s:6:"DoVoid";a:2:{s:5:"param";s:17:"DoVoidRequestType";s:6:"result";s:18:"DoVoidResponseType";}s:15:"DoAuthorization";a:2:{s:5:"param";s:26:"DoAuthorizationRequestType";s:6:"result";s:27:"DoAuthorizationResponseType";}s:27:"SetCustomerBillingAgreement";a:2:{s:5:"param";s:38:"SetCustomerBillingAgreementRequestType";s:6:"result";s:39:"SetCustomerBillingAgreementResponseType";}s:34:"GetBillingAgreementCustomerDetails";a:2:{s:5:"param";s:45:"GetBillingAgreementCustomerDetailsRequestType";s:6:"result";s:46:"GetBillingAgreementCustomerDetailsResponseType";}s:22:"CreateBillingAgreement";a:2:{s:5:"param";s:33:"CreateBillingAgreementRequestType";s:6:"result";s:34:"CreateBillingAgreementResponseType";}s:22:"DoReferenceTransaction";a:2:{s:5:"param";s:33:"DoReferenceTransactionRequestType";s:6:"result";s:34:"DoReferenceTransactionResponseType";}s:21:"DoNonReferencedCredit";a:2:{s:5:"param";s:32:"DoNonReferencedCreditRequestType";s:6:"result";s:33:"DoNonReferencedCreditResponseType";}s:19:"DoUATPAuthorization";a:2:{s:5:"param";s:30:"DoUATPAuthorizationRequestType";s:6:"result";s:31:"DoUATPAuthorizationResponseType";}s:30:"CreateRecurringPaymentsProfile";a:2:{s:5:"param";s:41:"CreateRecurringPaymentsProfileRequestType";s:6:"result";s:42:"CreateRecurringPaymentsProfileResponseType";}s:34:"GetRecurringPaymentsProfileDetails";a:2:{s:5:"param";s:45:"GetRecurringPaymentsProfileDetailsRequestType";s:6:"result";s:46:"GetRecurringPaymentsProfileDetailsResponseType";}s:36:"ManageRecurringPaymentsProfileStatus";a:2:{s:5:"param";s:47:"ManageRecurringPaymentsProfileStatusRequestType";s:6:"result";s:48:"ManageRecurringPaymentsProfileStatusResponseType";}s:21:"BillOutstandingAmount";a:2:{s:5:"param";s:32:"BillOutstandingAmountRequestType";s:6:"result";s:33:"BillOutstandingAmountResponseType";}s:30:"UpdateRecurringPaymentsProfile";a:2:{s:5:"param";s:41:"UpdateRecurringPaymentsProfileRequestType";s:6:"result";s:42:"UpdateRecurringPaymentsProfileResponseType";}}');

/**
 * Interface class that wraps all WSDL ports into a unified API for
 * the user. Also handles PayPal-specific details like type handling,
 * error handling, etc.
 *
 * @package PayPal
 */
class CallerServices extends SOAP_Client
{
    /**
     * The profile to use in API calls.
     *
     * @access protected
     *
     * @var APIProfile $_profile
     */
    var $_profile;

    /**
     * The portType/environment -> endpoint map.
     *
     * @access protected
     *
     * @var array $_endpointMap
     */
    var $_endpointMap;

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
    var $_logLevel = PEAR_LOG_INFO;

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
    var $_logDir = '/tmp';

    /**
     * The PEAR Log object we use for logging.
     *
     * @access protected
     *
     * @var Log $_logger
     */
    var $_logger;

    /**
     * Construct a new CallerServices object.
     *
     * @param APIProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             the SDK.
     */
    function CallerServices($profile)
    {
        // Initialize the SOAP Client.
        parent::SOAP_Client(null);

        // Store the API profile.
        $this->setAPIProfile($profile);
	
	  // below code will switch off the server certificate validation. this is only required in staging		
	  $this->setOpt('curl', CURLOPT_SSL_VERIFYPEER, false);
	  $this->setOpt('curl', CURLOPT_SSL_VERIFYHOST, false);
      $this->_options['timeout'] = 15;

        if(!empty($profile->_subject)) {
        	$this->setOpt('curl', CURLOPT_SSL_VERIFYPEER, false);
        	$this->setOpt('curl', CURLOPT_SSL_VERIFYHOST, false);
        }
        else {
        
	        // SSL CA certificate.
	        $this->setOpt('curl', CURLOPT_CAINFO, dirname(__FILE__) . '/cert/api_cert_chain.crt');
	
	        // SSL Client certificate.
	        if (isset($profile->_certificateFile)) {
	            // Set options from the profile.
	            $this->setOpt('curl', CURLOPT_SSLCERT, $profile->getCertificateFile());
	            if ($profile->getCertificatePassword()) {
	                $this->setOpt('curl', CURLOPT_SSLCERTPASSWD, $profile->getCertificatePassword());
	            }
	        }
        }

        // Tracing.
        $this->setOpt('trace', 1);

        // Load the endpoint map.
        include 'PayPal/wsdl/paypal-endpoints.php';
        $this->_endpointMap = $PayPalEndpoints;

        // Load SDK settings.
        if (@include 'PayPal/conf/paypal-sdk.php') {
            if (isset($__PP_CONFIG['log_level'])) {
                $this->_logLevel = $__PP_CONFIG['log_level'];
            }
            if (isset($__PP_CONFIG['log_dir'])) {
                $this->_logDir = $__PP_CONFIG['log_dir'];
            }
        }
    }

    /**
     * Sets the WSDL endpoint based on $portType and on the environment
     * set in the user's profile.
     *
     * @param string $portType  The portType the current operation is part of.
     * @param string $version   The WSDL version being used.
     *
     * @return boolean | PayPal_Error  An error if mapping can't be done, else true.
     */
    function setEndpoint($portType, $version)
    {
        $version = (float)$version;

        foreach ($this->_endpointMap as $range) {
            if ($version >= $range['min'] &&
                $version <= $range['max'] &&
                isset($range['environments'][$this->_profile->getEnvironment()][$portType])) {
                  // Check 3-token auth
                  $signature = $this->_profile->getSignature();
                  if(isset($signature)) {
                     // TBD:  Is this legit?
                     $three_token_port = $portType.'-threetoken';
                     $this->_endpoint = $range['environments'][$this->_profile->getEnvironment()][$three_token_port];
                  } else {
                     $this->_endpoint = $range['environments'][$this->_profile->getEnvironment()][$portType];
                  }
                  $this->getLogger();
                  $this->_logger->log('DEBUG setEndpoint: '.$this->_endpoint, PEAR_LOG_DEBUG);
                return true;
            }
        }

        return PayPal::raiseError("Invalid version/environment/portType combination.");
    }

    /**
     * Take the decoded array from SOAP_Client::__call() and turn it
     * into an object of the appropriate AbstractResponseType
     * subclass.
     *
     * @param array $values  The decoded SOAP response.
     * @param string $type   The type of the response object.
     *
     * @return AbstractResponseType  The response object.
     */
    function &getResponseObject($values, $type)
    {
        // Check for SOAP Faults.
        if (PayPal::isError($values)) {
            return $values;
        }

        // Check for already translated objects.
        if (is_object($values) && strtolower(get_class($values)) != 'xsdsimpletype') {
            return $values;
        }

        $object =& PayPal::getType($type);
        if (PayPal::isError($object)) {
            return $values;
        }

        foreach ($values as $name => $value) {
            if (method_exists($object, 'set' . $name)) {
                if (is_object($value)) {
                    if (strtolower(get_class($value)) == 'xsdsimpletype') {
                        $value =& $this->getResponseObject((array)$value, $object->_elements[$name]['type']);
                    }
                } elseif (is_array($value)) {
                    $values = $value;
                    $value = array();
                    foreach ($values as $v) {
                        $value[] =& $this->getResponseObject($v, $object->_elements[$name]['type']);
                    }
                }
                call_user_func(array(&$object, 'set' . $name), $value);
            }
        }

        return $object;
    }

    /**
     * Use a given profile.
     *
     * @param APIProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             the SDK.
     */
    function setAPIProfile(&$profile)
    {
        $this->_profile = &$profile;
    }

    /**
     * Get the current profile.
     *
     * @return APIProfile  The current profile.
     */
    function &getAPIProfile()
    {
        return $this->_profile;
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
            $file = $this->_logDir . '/' . date('Ymd') . '.PayPal.log';
            if (is_link($file) || (file_exists($file) && !is_writable($file))) {
                // Don't overwrite symlinks.
                return PayPal::raiseError('bad logfile');
            }

            $this->_logger = &Log::singleton('file', $file, $this->_profile->getAPIUsername(), array('append' => true));
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
     * Override SOAP_Client::call() to always add our security header
     * first.
     */
    function &call($method, &$params, $namespace = false, $soapAction = false)
    {
        // Create the security header.
        if(!empty($this->_profile->_subject)) {
        	$this->addHeader($rc = new SOAP_Header('RequesterCredentials', 'struct', array(
            new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Credentials', 'struct',
                           array(new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Subject', '', $this->_profile->getSubject())),
                           array('xmlns:ebl' => 'urn:ebay:apis:eBLBaseComponents'))),
            1, array('xmlns' => 'urn:ebay:api:PayPalAPI')));
        }
        else {
	        $this->addHeader($rc = new SOAP_Header('RequesterCredentials', 'struct', array(
	            new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Credentials', 'struct',
	                           array(new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Username', '', $this->_profile->getAPIUsername()),
	                                 new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Password', '', $this->_profile->getAPIPassword()),
	                                 new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Signature', '', $this->_profile->getSignature()),
	                                 new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Subject', '', $this->_profile->getSubject())),
	                           array('xmlns:ebl' => 'urn:ebay:apis:eBLBaseComponents'))),
	            1, array('xmlns' => 'urn:ebay:api:PayPalAPI')));
        }

        return parent::call($method, $params, $namespace, $soapAction);
    }

    /**
     * Override some of the default SOAP:: package _decode behavior to
     * handle simpleTypes and complexTypes with simpleContent.
     */
    function &_decode(&$soapval)
    {
        if (count($soapval->attributes)) {
            $attributes = $soapval->attributes;
        }

        $object =& PayPal::getType($soapval->type);
        if (PayPal::isError($object)) {
            return parent::_decode($soapval);
        }

        $this->_type_translation[$soapval->type] = $soapval->type;

        $result =& parent::_decode($soapval);
        if (!is_a($result, 'XSDType') && is_a($object, 'XSDSimpleType')) {
            $object->setval($result);
            if (isset($attributes)) {
                foreach ($attributes as $aname => $attribute) {
                    $object->setattr($aname, $attribute);
                }
            }
            $result =& $object;
        }

        return $result;
    }

    /**
     * Log the current transaction depending on the current log level.
     *
     * @access protected
     *
     * @param string $operation  The operation called.
     * @param integer $elapsed   Microseconds taken.
     * @param object $response   The response object.
     */
    function _logTransaction($operation, $elapsed, $response)
    {
        $logger =& $this->getLogger();
        if (PayPal::isError($logger)) {
            return $logger;
        }

        switch ($this->_logLevel) {
        case PEAR_LOG_DEBUG:
            $logger->log('Request XML: ' . $this->_sanitizeLog($this->__last_request), PEAR_LOG_DEBUG);
            $logger->log('Response XML: ' . $this->_sanitizeLog($this->__last_response), PEAR_LOG_DEBUG);

        case PEAR_LOG_INFO:
            /*
            // Commented out by adigeo because getAck() crashed Paypal API is there is no ACK in the response
            $ack = is_object($response) && method_exists($response, 'getAck') ? ', Ack: ' . $response->getAck() : '';
            $logger->log($operation . ', Elapsed: ' . $elapsed . 'ms' . $ack, PEAR_LOG_INFO);
            */

        case PEAR_LOG_ERR:
            if (PayPal::isError($response)) {
                $logger->log($response, PEAR_LOG_ERR);
            }
        }
    }

    /**
     * Strip sensitive information (API passwords and credit card
     * numbers) from raw XML requests/responses.
     *
     * @access protected
     *
     * @param string $xml  The XML to sanitize.
     *
     * @return string  The sanitized XML.
     */
    function _sanitizeLog($xml)
    {
        return preg_replace(array('/<(.*?Password.*?)>(.*?)<\/(.*?Password)>/i',
                                  '/<(.*CreditCardNumber.*)>(.*?)<\/(.*CreditCardNumber)>/i',
                                  '/<(.*Signature.*)>(.*?)<\/(.*Signature)>/i'),
                            '<\1>***</\3>',
                            $xml);
    }

    /**
     * Return the current time including microseconds.
     *
     * @access protected
     *
     * @return integer  Current time with microseconds.
     */
    function _getMicroseconds()
    {
        list($ms, $s) = explode(' ', microtime());
        return floor($ms * 1000) + 1000 * $s;
    }

    /**
     * Return the difference between now and $start in microseconds.
     *
     * @access protected
     *
     * @param integer $start  Start time including microseconds.
     *
     * @return integer  Number of microseconds elapsed since $start
     */
    function _getElapsed($start)
    {
        return $this->_getMicroseconds() - $start;
    }

    function &RefundTransaction($RefundTransactionReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($RefundTransactionReq, 'XSDSimpleType')) {
            $RefundTransactionReq->setVersion(PAYPAL_WSDL_VERSION);
            $RefundTransactionReq = $RefundTransactionReq->getSoapValue('RefundTransactionRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('RefundTransaction', $this->_getElapsed($start), $res);
            return $res;
        }

        // RefundTransactionReq is a ComplexType, refer to the WSDL for more info.
        $RefundTransactionReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $RefundTransactionReq =new SOAP_Value('RefundTransactionReq', false, $RefundTransactionReq, $RefundTransactionReq_attr);
        $result = $this->call('RefundTransaction',
                              $v = array("RefundTransactionReq" => $RefundTransactionReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'RefundTransactionResponseType');
        $this->_logTransaction('RefundTransaction', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetTransactionDetails($GetTransactionDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetTransactionDetailsReq, 'XSDSimpleType')) {
            $GetTransactionDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetTransactionDetailsReq = $GetTransactionDetailsReq->getSoapValue('GetTransactionDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetTransactionDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetTransactionDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetTransactionDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetTransactionDetailsReq =new SOAP_Value('GetTransactionDetailsReq', false, $GetTransactionDetailsReq, $GetTransactionDetailsReq_attr);
        $result = $this->call('GetTransactionDetails',
                              $v = array("GetTransactionDetailsReq" => $GetTransactionDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetTransactionDetailsResponseType');
        $this->_logTransaction('GetTransactionDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMCreateButton($BMCreateButtonReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMCreateButtonReq, 'XSDSimpleType')) {
            $BMCreateButtonReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMCreateButtonReq = $BMCreateButtonReq->getSoapValue('BMCreateButtonRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMCreateButton', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMCreateButtonReq is a ComplexType, refer to the WSDL for more info.
        $BMCreateButtonReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMCreateButtonReq =new SOAP_Value('BMCreateButtonReq', false, $BMCreateButtonReq, $BMCreateButtonReq_attr);
        $result = $this->call('BMCreateButton',
                              $v = array("BMCreateButtonReq" => $BMCreateButtonReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMCreateButtonResponseType');
        $this->_logTransaction('BMCreateButton', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMUpdateButton($BMUpdateButtonReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMUpdateButtonReq, 'XSDSimpleType')) {
            $BMUpdateButtonReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMUpdateButtonReq = $BMUpdateButtonReq->getSoapValue('BMUpdateButtonRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMUpdateButton', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMUpdateButtonReq is a ComplexType, refer to the WSDL for more info.
        $BMUpdateButtonReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMUpdateButtonReq =new SOAP_Value('BMUpdateButtonReq', false, $BMUpdateButtonReq, $BMUpdateButtonReq_attr);
        $result = $this->call('BMUpdateButton',
                              $v = array("BMUpdateButtonReq" => $BMUpdateButtonReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMUpdateButtonResponseType');
        $this->_logTransaction('BMUpdateButton', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMManageButtonStatus($BMManageButtonStatusReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMManageButtonStatusReq, 'XSDSimpleType')) {
            $BMManageButtonStatusReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMManageButtonStatusReq = $BMManageButtonStatusReq->getSoapValue('BMManageButtonStatusRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMManageButtonStatus', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMManageButtonStatusReq is a ComplexType, refer to the WSDL for more info.
        $BMManageButtonStatusReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMManageButtonStatusReq =new SOAP_Value('BMManageButtonStatusReq', false, $BMManageButtonStatusReq, $BMManageButtonStatusReq_attr);
        $result = $this->call('BMManageButtonStatus',
                              $v = array("BMManageButtonStatusReq" => $BMManageButtonStatusReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMManageButtonStatusResponseType');
        $this->_logTransaction('BMManageButtonStatus', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMGetButtonDetails($BMGetButtonDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMGetButtonDetailsReq, 'XSDSimpleType')) {
            $BMGetButtonDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMGetButtonDetailsReq = $BMGetButtonDetailsReq->getSoapValue('BMGetButtonDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMGetButtonDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMGetButtonDetailsReq is a ComplexType, refer to the WSDL for more info.
        $BMGetButtonDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMGetButtonDetailsReq =new SOAP_Value('BMGetButtonDetailsReq', false, $BMGetButtonDetailsReq, $BMGetButtonDetailsReq_attr);
        $result = $this->call('BMGetButtonDetails',
                              $v = array("BMGetButtonDetailsReq" => $BMGetButtonDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMGetButtonDetailsResponseType');
        $this->_logTransaction('BMGetButtonDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMSetInventory($BMSetInventoryReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMSetInventoryReq, 'XSDSimpleType')) {
            $BMSetInventoryReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMSetInventoryReq = $BMSetInventoryReq->getSoapValue('BMSetInventoryRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMSetInventory', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMSetInventoryReq is a ComplexType, refer to the WSDL for more info.
        $BMSetInventoryReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMSetInventoryReq =new SOAP_Value('BMSetInventoryReq', false, $BMSetInventoryReq, $BMSetInventoryReq_attr);
        $result = $this->call('BMSetInventory',
                              $v = array("BMSetInventoryReq" => $BMSetInventoryReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMSetInventoryResponseType');
        $this->_logTransaction('BMSetInventory', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMGetInventory($BMGetInventoryReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMGetInventoryReq, 'XSDSimpleType')) {
            $BMGetInventoryReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMGetInventoryReq = $BMGetInventoryReq->getSoapValue('BMGetInventoryRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMGetInventory', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMGetInventoryReq is a ComplexType, refer to the WSDL for more info.
        $BMGetInventoryReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMGetInventoryReq =new SOAP_Value('BMGetInventoryReq', false, $BMGetInventoryReq, $BMGetInventoryReq_attr);
        $result = $this->call('BMGetInventory',
                              $v = array("BMGetInventoryReq" => $BMGetInventoryReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMGetInventoryResponseType');
        $this->_logTransaction('BMGetInventory', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BMButtonSearch($BMButtonSearchReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BMButtonSearchReq, 'XSDSimpleType')) {
            $BMButtonSearchReq->setVersion(PAYPAL_WSDL_VERSION);
            $BMButtonSearchReq = $BMButtonSearchReq->getSoapValue('BMButtonSearchRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BMButtonSearch', $this->_getElapsed($start), $res);
            return $res;
        }

        // BMButtonSearchReq is a ComplexType, refer to the WSDL for more info.
        $BMButtonSearchReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BMButtonSearchReq =new SOAP_Value('BMButtonSearchReq', false, $BMButtonSearchReq, $BMButtonSearchReq_attr);
        $result = $this->call('BMButtonSearch',
                              $v = array("BMButtonSearchReq" => $BMButtonSearchReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BMButtonSearchResponseType');
        $this->_logTransaction('BMButtonSearch', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BillUser($BillUserReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BillUserReq, 'XSDSimpleType')) {
            $BillUserReq->setVersion(PAYPAL_WSDL_VERSION);
            $BillUserReq = $BillUserReq->getSoapValue('BillUserRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BillUser', $this->_getElapsed($start), $res);
            return $res;
        }

        // BillUserReq is a ComplexType, refer to the WSDL for more info.
        $BillUserReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BillUserReq =new SOAP_Value('BillUserReq', false, $BillUserReq, $BillUserReq_attr);
        $result = $this->call('BillUser',
                              $v = array("BillUserReq" => $BillUserReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BillUserResponseType');
        $this->_logTransaction('BillUser', $this->_getElapsed($start), $response);
        return $response;
    }

    function &TransactionSearch($TransactionSearchReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($TransactionSearchReq, 'XSDSimpleType')) {
            $TransactionSearchReq->setVersion(PAYPAL_WSDL_VERSION);
            $TransactionSearchReq = $TransactionSearchReq->getSoapValue('TransactionSearchRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('TransactionSearch', $this->_getElapsed($start), $res);
            return $res;
        }

        // TransactionSearchReq is a ComplexType, refer to the WSDL for more info.
        $TransactionSearchReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $TransactionSearchReq =new SOAP_Value('TransactionSearchReq', false, $TransactionSearchReq, $TransactionSearchReq_attr);
        $result = $this->call('TransactionSearch',
                              $v = array("TransactionSearchReq" => $TransactionSearchReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'TransactionSearchResponseType');
        $this->_logTransaction('TransactionSearch', $this->_getElapsed($start), $response);
        return $response;
    }

    function &MassPay($MassPayReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($MassPayReq, 'XSDSimpleType')) {
            $MassPayReq->setVersion(PAYPAL_WSDL_VERSION);
            $MassPayReq = $MassPayReq->getSoapValue('MassPayRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('MassPay', $this->_getElapsed($start), $res);
            return $res;
        }

        // MassPayReq is a ComplexType, refer to the WSDL for more info.
        $MassPayReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $MassPayReq =new SOAP_Value('MassPayReq', false, $MassPayReq, $MassPayReq_attr);
        $result = $this->call('MassPay',
                              $v = array("MassPayReq" => $MassPayReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'MassPayResponseType');
        $this->_logTransaction('MassPay', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BillAgreementUpdate($BillAgreementUpdateReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BillAgreementUpdateReq, 'XSDSimpleType')) {
            $BillAgreementUpdateReq->setVersion(PAYPAL_WSDL_VERSION);
            $BillAgreementUpdateReq = $BillAgreementUpdateReq->getSoapValue('BAUpdateRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BillAgreementUpdate', $this->_getElapsed($start), $res);
            return $res;
        }

        // BillAgreementUpdateReq is a ComplexType, refer to the WSDL for more info.
        $BillAgreementUpdateReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BillAgreementUpdateReq =new SOAP_Value('BillAgreementUpdateReq', false, $BillAgreementUpdateReq, $BillAgreementUpdateReq_attr);
        $result = $this->call('BillAgreementUpdate',
                              $v = array("BillAgreementUpdateReq" => $BillAgreementUpdateReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BAUpdateResponseType');
        $this->_logTransaction('BillAgreementUpdate', $this->_getElapsed($start), $response);
        return $response;
    }

    function &AddressVerify($AddressVerifyReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($AddressVerifyReq, 'XSDSimpleType')) {
            $AddressVerifyReq->setVersion(PAYPAL_WSDL_VERSION);
            $AddressVerifyReq = $AddressVerifyReq->getSoapValue('AddressVerifyRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('AddressVerify', $this->_getElapsed($start), $res);
            return $res;
        }

        // AddressVerifyReq is a ComplexType, refer to the WSDL for more info.
        $AddressVerifyReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $AddressVerifyReq =new SOAP_Value('AddressVerifyReq', false, $AddressVerifyReq, $AddressVerifyReq_attr);
        $result = $this->call('AddressVerify',
                              $v = array("AddressVerifyReq" => $AddressVerifyReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'AddressVerifyResponseType');
        $this->_logTransaction('AddressVerify', $this->_getElapsed($start), $response);
        return $response;
    }

    function &EnterBoarding($EnterBoardingReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($EnterBoardingReq, 'XSDSimpleType')) {
            $EnterBoardingReq->setVersion(PAYPAL_WSDL_VERSION);
            $EnterBoardingReq = $EnterBoardingReq->getSoapValue('EnterBoardingRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('EnterBoarding', $this->_getElapsed($start), $res);
            return $res;
        }

        // EnterBoardingReq is a ComplexType, refer to the WSDL for more info.
        $EnterBoardingReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $EnterBoardingReq =new SOAP_Value('EnterBoardingReq', false, $EnterBoardingReq, $EnterBoardingReq_attr);
        $result = $this->call('EnterBoarding',
                              $v = array("EnterBoardingReq" => $EnterBoardingReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'EnterBoardingResponseType');
        $this->_logTransaction('EnterBoarding', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetBoardingDetails($GetBoardingDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetBoardingDetailsReq, 'XSDSimpleType')) {
            $GetBoardingDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetBoardingDetailsReq = $GetBoardingDetailsReq->getSoapValue('GetBoardingDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetBoardingDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetBoardingDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetBoardingDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetBoardingDetailsReq =new SOAP_Value('GetBoardingDetailsReq', false, $GetBoardingDetailsReq, $GetBoardingDetailsReq_attr);
        $result = $this->call('GetBoardingDetails',
                              $v = array("GetBoardingDetailsReq" => $GetBoardingDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetBoardingDetailsResponseType');
        $this->_logTransaction('GetBoardingDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &CreateMobilePayment($CreateMobilePaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($CreateMobilePaymentReq, 'XSDSimpleType')) {
            $CreateMobilePaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $CreateMobilePaymentReq = $CreateMobilePaymentReq->getSoapValue('CreateMobilePaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('CreateMobilePayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // CreateMobilePaymentReq is a ComplexType, refer to the WSDL for more info.
        $CreateMobilePaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $CreateMobilePaymentReq =new SOAP_Value('CreateMobilePaymentReq', false, $CreateMobilePaymentReq, $CreateMobilePaymentReq_attr);
        $result = $this->call('CreateMobilePayment',
                              $v = array("CreateMobilePaymentReq" => $CreateMobilePaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'CreateMobilePaymentResponseType');
        $this->_logTransaction('CreateMobilePayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetMobileStatus($GetMobileStatusReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetMobileStatusReq, 'XSDSimpleType')) {
            $GetMobileStatusReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetMobileStatusReq = $GetMobileStatusReq->getSoapValue('GetMobileStatusRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetMobileStatus', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetMobileStatusReq is a ComplexType, refer to the WSDL for more info.
        $GetMobileStatusReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetMobileStatusReq =new SOAP_Value('GetMobileStatusReq', false, $GetMobileStatusReq, $GetMobileStatusReq_attr);
        $result = $this->call('GetMobileStatus',
                              $v = array("GetMobileStatusReq" => $GetMobileStatusReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetMobileStatusResponseType');
        $this->_logTransaction('GetMobileStatus', $this->_getElapsed($start), $response);
        return $response;
    }

    function &SetMobileCheckout($SetMobileCheckoutReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($SetMobileCheckoutReq, 'XSDSimpleType')) {
            $SetMobileCheckoutReq->setVersion(PAYPAL_WSDL_VERSION);
            $SetMobileCheckoutReq = $SetMobileCheckoutReq->getSoapValue('SetMobileCheckoutRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('SetMobileCheckout', $this->_getElapsed($start), $res);
            return $res;
        }

        // SetMobileCheckoutReq is a ComplexType, refer to the WSDL for more info.
        $SetMobileCheckoutReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $SetMobileCheckoutReq =new SOAP_Value('SetMobileCheckoutReq', false, $SetMobileCheckoutReq, $SetMobileCheckoutReq_attr);
        $result = $this->call('SetMobileCheckout',
                              $v = array("SetMobileCheckoutReq" => $SetMobileCheckoutReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'SetMobileCheckoutResponseType');
        $this->_logTransaction('SetMobileCheckout', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoMobileCheckoutPayment($DoMobileCheckoutPaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoMobileCheckoutPaymentReq, 'XSDSimpleType')) {
            $DoMobileCheckoutPaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoMobileCheckoutPaymentReq = $DoMobileCheckoutPaymentReq->getSoapValue('DoMobileCheckoutPaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoMobileCheckoutPayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoMobileCheckoutPaymentReq is a ComplexType, refer to the WSDL for more info.
        $DoMobileCheckoutPaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoMobileCheckoutPaymentReq =new SOAP_Value('DoMobileCheckoutPaymentReq', false, $DoMobileCheckoutPaymentReq, $DoMobileCheckoutPaymentReq_attr);
        $result = $this->call('DoMobileCheckoutPayment',
                              $v = array("DoMobileCheckoutPaymentReq" => $DoMobileCheckoutPaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoMobileCheckoutPaymentResponseType');
        $this->_logTransaction('DoMobileCheckoutPayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetBalance($GetBalanceReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetBalanceReq, 'XSDSimpleType')) {
            $GetBalanceReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetBalanceReq = $GetBalanceReq->getSoapValue('GetBalanceRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetBalance', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetBalanceReq is a ComplexType, refer to the WSDL for more info.
        $GetBalanceReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetBalanceReq =new SOAP_Value('GetBalanceReq', false, $GetBalanceReq, $GetBalanceReq_attr);
        $result = $this->call('GetBalance',
                              $v = array("GetBalanceReq" => $GetBalanceReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetBalanceResponseType');
        $this->_logTransaction('GetBalance', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetPalDetails($GetPalDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetPalDetailsReq, 'XSDSimpleType')) {
            $GetPalDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetPalDetailsReq = $GetPalDetailsReq->getSoapValue('GetPalDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetPalDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetPalDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetPalDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetPalDetailsReq =new SOAP_Value('GetPalDetailsReq', false, $GetPalDetailsReq, $GetPalDetailsReq_attr);
        $result = $this->call('GetPalDetails',
                              $v = array("GetPalDetailsReq" => $GetPalDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetPalDetailsResponseType');
        $this->_logTransaction('GetPalDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoExpressCheckoutPayment($DoExpressCheckoutPaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoExpressCheckoutPaymentReq, 'XSDSimpleType')) {
            $DoExpressCheckoutPaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoExpressCheckoutPaymentReq = $DoExpressCheckoutPaymentReq->getSoapValue('DoExpressCheckoutPaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoExpressCheckoutPayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoExpressCheckoutPaymentReq is a ComplexType, refer to the WSDL for more info.
        $DoExpressCheckoutPaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoExpressCheckoutPaymentReq =new SOAP_Value('DoExpressCheckoutPaymentReq', false, $DoExpressCheckoutPaymentReq, $DoExpressCheckoutPaymentReq_attr);
        $result = $this->call('DoExpressCheckoutPayment',
                              $v = array("DoExpressCheckoutPaymentReq" => $DoExpressCheckoutPaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoExpressCheckoutPaymentResponseType');
        $this->_logTransaction('DoExpressCheckoutPayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoUATPExpressCheckoutPayment($DoUATPExpressCheckoutPaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoUATPExpressCheckoutPaymentReq, 'XSDSimpleType')) {
            $DoUATPExpressCheckoutPaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoUATPExpressCheckoutPaymentReq = $DoUATPExpressCheckoutPaymentReq->getSoapValue('DoUATPExpressCheckoutPaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoUATPExpressCheckoutPayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoUATPExpressCheckoutPaymentReq is a ComplexType, refer to the WSDL for more info.
        $DoUATPExpressCheckoutPaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoUATPExpressCheckoutPaymentReq =new SOAP_Value('DoUATPExpressCheckoutPaymentReq', false, $DoUATPExpressCheckoutPaymentReq, $DoUATPExpressCheckoutPaymentReq_attr);
        $result = $this->call('DoUATPExpressCheckoutPayment',
                              $v = array("DoUATPExpressCheckoutPaymentReq" => $DoUATPExpressCheckoutPaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoUATPExpressCheckoutPaymentResponseType');
        $this->_logTransaction('DoUATPExpressCheckoutPayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &SetAuthFlowParam($SetAuthFlowParamReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($SetAuthFlowParamReq, 'XSDSimpleType')) {
            $SetAuthFlowParamReq->setVersion(PAYPAL_WSDL_VERSION);
            $SetAuthFlowParamReq = $SetAuthFlowParamReq->getSoapValue('SetAuthFlowParamRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('SetAuthFlowParam', $this->_getElapsed($start), $res);
            return $res;
        }

        // SetAuthFlowParamReq is a ComplexType, refer to the WSDL for more info.
        $SetAuthFlowParamReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $SetAuthFlowParamReq =new SOAP_Value('SetAuthFlowParamReq', false, $SetAuthFlowParamReq, $SetAuthFlowParamReq_attr);
        $result = $this->call('SetAuthFlowParam',
                              $v = array("SetAuthFlowParamReq" => $SetAuthFlowParamReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'SetAuthFlowParamResponseType');
        $this->_logTransaction('SetAuthFlowParam', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetAuthDetails($GetAuthDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetAuthDetailsReq, 'XSDSimpleType')) {
            $GetAuthDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetAuthDetailsReq = $GetAuthDetailsReq->getSoapValue('GetAuthDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetAuthDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetAuthDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetAuthDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetAuthDetailsReq =new SOAP_Value('GetAuthDetailsReq', false, $GetAuthDetailsReq, $GetAuthDetailsReq_attr);
        $result = $this->call('GetAuthDetails',
                              $v = array("GetAuthDetailsReq" => $GetAuthDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetAuthDetailsResponseType');
        $this->_logTransaction('GetAuthDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &SetAccessPermissions($SetAccessPermissionsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($SetAccessPermissionsReq, 'XSDSimpleType')) {
            $SetAccessPermissionsReq->setVersion(PAYPAL_WSDL_VERSION);
            $SetAccessPermissionsReq = $SetAccessPermissionsReq->getSoapValue('SetAccessPermissionsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('SetAccessPermissions', $this->_getElapsed($start), $res);
            return $res;
        }

        // SetAccessPermissionsReq is a ComplexType, refer to the WSDL for more info.
        $SetAccessPermissionsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $SetAccessPermissionsReq =new SOAP_Value('SetAccessPermissionsReq', false, $SetAccessPermissionsReq, $SetAccessPermissionsReq_attr);
        $result = $this->call('SetAccessPermissions',
                              $v = array("SetAccessPermissionsReq" => $SetAccessPermissionsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'SetAccessPermissionsResponseType');
        $this->_logTransaction('SetAccessPermissions', $this->_getElapsed($start), $response);
        return $response;
    }

    function &UpdateAccessPermissions($UpdateAccessPermissionsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($UpdateAccessPermissionsReq, 'XSDSimpleType')) {
            $UpdateAccessPermissionsReq->setVersion(PAYPAL_WSDL_VERSION);
            $UpdateAccessPermissionsReq = $UpdateAccessPermissionsReq->getSoapValue('UpdateAccessPermissionsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('UpdateAccessPermissions', $this->_getElapsed($start), $res);
            return $res;
        }

        // UpdateAccessPermissionsReq is a ComplexType, refer to the WSDL for more info.
        $UpdateAccessPermissionsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $UpdateAccessPermissionsReq =new SOAP_Value('UpdateAccessPermissionsReq', false, $UpdateAccessPermissionsReq, $UpdateAccessPermissionsReq_attr);
        $result = $this->call('UpdateAccessPermissions',
                              $v = array("UpdateAccessPermissionsReq" => $UpdateAccessPermissionsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'UpdateAccessPermissionsResponseType');
        $this->_logTransaction('UpdateAccessPermissions', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetAccessPermissionDetails($GetAccessPermissionDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetAccessPermissionDetailsReq, 'XSDSimpleType')) {
            $GetAccessPermissionDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetAccessPermissionDetailsReq = $GetAccessPermissionDetailsReq->getSoapValue('GetAccessPermissionDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetAccessPermissionDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetAccessPermissionDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetAccessPermissionDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetAccessPermissionDetailsReq =new SOAP_Value('GetAccessPermissionDetailsReq', false, $GetAccessPermissionDetailsReq, $GetAccessPermissionDetailsReq_attr);
        $result = $this->call('GetAccessPermissionDetails',
                              $v = array("GetAccessPermissionDetailsReq" => $GetAccessPermissionDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetAccessPermissionDetailsResponseType');
        $this->_logTransaction('GetAccessPermissionDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &SetExpressCheckout($SetExpressCheckoutReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($SetExpressCheckoutReq, 'XSDSimpleType')) {
            $SetExpressCheckoutReq->setVersion(PAYPAL_WSDL_VERSION);
            $SetExpressCheckoutReq = $SetExpressCheckoutReq->getSoapValue('SetExpressCheckoutRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('SetExpressCheckout', $this->_getElapsed($start), $res);
            return $res;
        }

        // SetExpressCheckoutReq is a ComplexType, refer to the WSDL for more info.
        $SetExpressCheckoutReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $SetExpressCheckoutReq =new SOAP_Value('SetExpressCheckoutReq', false, $SetExpressCheckoutReq, $SetExpressCheckoutReq_attr);
        $result = $this->call('SetExpressCheckout',
                              $v = array("SetExpressCheckoutReq" => $SetExpressCheckoutReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'SetExpressCheckoutResponseType');
        $this->_logTransaction('SetExpressCheckout', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetExpressCheckoutDetails($GetExpressCheckoutDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetExpressCheckoutDetailsReq, 'XSDSimpleType')) {
            $GetExpressCheckoutDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetExpressCheckoutDetailsReq = $GetExpressCheckoutDetailsReq->getSoapValue('GetExpressCheckoutDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetExpressCheckoutDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetExpressCheckoutDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetExpressCheckoutDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetExpressCheckoutDetailsReq =new SOAP_Value('GetExpressCheckoutDetailsReq', false, $GetExpressCheckoutDetailsReq, $GetExpressCheckoutDetailsReq_attr);
        $result = $this->call('GetExpressCheckoutDetails',
                              $v = array("GetExpressCheckoutDetailsReq" => $GetExpressCheckoutDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetExpressCheckoutDetailsResponseType');
        $this->_logTransaction('GetExpressCheckoutDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoDirectPayment($DoDirectPaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoDirectPaymentReq, 'XSDSimpleType')) {
            $DoDirectPaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoDirectPaymentReq = $DoDirectPaymentReq->getSoapValue('DoDirectPaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoDirectPayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoDirectPaymentReq is a ComplexType, refer to the WSDL for more info.
        $DoDirectPaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoDirectPaymentReq =new SOAP_Value('DoDirectPaymentReq', false, $DoDirectPaymentReq, $DoDirectPaymentReq_attr);
        $result = $this->call('DoDirectPayment',
                              $v = array("DoDirectPaymentReq" => $DoDirectPaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoDirectPaymentResponseType');
        $this->_logTransaction('DoDirectPayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &ManagePendingTransactionStatus($ManagePendingTransactionStatusReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($ManagePendingTransactionStatusReq, 'XSDSimpleType')) {
            $ManagePendingTransactionStatusReq->setVersion(PAYPAL_WSDL_VERSION);
            $ManagePendingTransactionStatusReq = $ManagePendingTransactionStatusReq->getSoapValue('ManagePendingTransactionStatusRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('ManagePendingTransactionStatus', $this->_getElapsed($start), $res);
            return $res;
        }

        // ManagePendingTransactionStatusReq is a ComplexType, refer to the WSDL for more info.
        $ManagePendingTransactionStatusReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $ManagePendingTransactionStatusReq =new SOAP_Value('ManagePendingTransactionStatusReq', false, $ManagePendingTransactionStatusReq, $ManagePendingTransactionStatusReq_attr);
        $result = $this->call('ManagePendingTransactionStatus',
                              $v = array("ManagePendingTransactionStatusReq" => $ManagePendingTransactionStatusReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'ManagePendingTransactionStatusResponseType');
        $this->_logTransaction('ManagePendingTransactionStatus', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoCapture($DoCaptureReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoCaptureReq, 'XSDSimpleType')) {
            $DoCaptureReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoCaptureReq = $DoCaptureReq->getSoapValue('DoCaptureRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoCapture', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoCaptureReq is a ComplexType, refer to the WSDL for more info.
        $DoCaptureReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoCaptureReq =new SOAP_Value('DoCaptureReq', false, $DoCaptureReq, $DoCaptureReq_attr);
        $result = $this->call('DoCapture',
                              $v = array("DoCaptureReq" => $DoCaptureReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoCaptureResponseType');
        $this->_logTransaction('DoCapture', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoReauthorization($DoReauthorizationReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoReauthorizationReq, 'XSDSimpleType')) {
            $DoReauthorizationReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoReauthorizationReq = $DoReauthorizationReq->getSoapValue('DoReauthorizationRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoReauthorization', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoReauthorizationReq is a ComplexType, refer to the WSDL for more info.
        $DoReauthorizationReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoReauthorizationReq =new SOAP_Value('DoReauthorizationReq', false, $DoReauthorizationReq, $DoReauthorizationReq_attr);
        $result = $this->call('DoReauthorization',
                              $v = array("DoReauthorizationReq" => $DoReauthorizationReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoReauthorizationResponseType');
        $this->_logTransaction('DoReauthorization', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoVoid($DoVoidReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoVoidReq, 'XSDSimpleType')) {
            $DoVoidReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoVoidReq = $DoVoidReq->getSoapValue('DoVoidRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoVoid', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoVoidReq is a ComplexType, refer to the WSDL for more info.
        $DoVoidReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoVoidReq =new SOAP_Value('DoVoidReq', false, $DoVoidReq, $DoVoidReq_attr);
        $result = $this->call('DoVoid',
                              $v = array("DoVoidReq" => $DoVoidReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoVoidResponseType');
        $this->_logTransaction('DoVoid', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoAuthorization($DoAuthorizationReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoAuthorizationReq, 'XSDSimpleType')) {
            $DoAuthorizationReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoAuthorizationReq = $DoAuthorizationReq->getSoapValue('DoAuthorizationRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoAuthorization', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoAuthorizationReq is a ComplexType, refer to the WSDL for more info.
        $DoAuthorizationReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoAuthorizationReq =new SOAP_Value('DoAuthorizationReq', false, $DoAuthorizationReq, $DoAuthorizationReq_attr);
        $result = $this->call('DoAuthorization',
                              $v = array("DoAuthorizationReq" => $DoAuthorizationReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoAuthorizationResponseType');
        $this->_logTransaction('DoAuthorization', $this->_getElapsed($start), $response);
        return $response;
    }

    function &SetCustomerBillingAgreement($SetCustomerBillingAgreementReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($SetCustomerBillingAgreementReq, 'XSDSimpleType')) {
            $SetCustomerBillingAgreementReq->setVersion(PAYPAL_WSDL_VERSION);
            $SetCustomerBillingAgreementReq = $SetCustomerBillingAgreementReq->getSoapValue('SetCustomerBillingAgreementRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('SetCustomerBillingAgreement', $this->_getElapsed($start), $res);
            return $res;
        }

        // SetCustomerBillingAgreementReq is a ComplexType, refer to the WSDL for more info.
        $SetCustomerBillingAgreementReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $SetCustomerBillingAgreementReq =new SOAP_Value('SetCustomerBillingAgreementReq', false, $SetCustomerBillingAgreementReq, $SetCustomerBillingAgreementReq_attr);
        $result = $this->call('SetCustomerBillingAgreement',
                              $v = array("SetCustomerBillingAgreementReq" => $SetCustomerBillingAgreementReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'SetCustomerBillingAgreementResponseType');
        $this->_logTransaction('SetCustomerBillingAgreement', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetBillingAgreementCustomerDetails($GetBillingAgreementCustomerDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetBillingAgreementCustomerDetailsReq, 'XSDSimpleType')) {
            $GetBillingAgreementCustomerDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetBillingAgreementCustomerDetailsReq = $GetBillingAgreementCustomerDetailsReq->getSoapValue('GetBillingAgreementCustomerDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetBillingAgreementCustomerDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetBillingAgreementCustomerDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetBillingAgreementCustomerDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetBillingAgreementCustomerDetailsReq =new SOAP_Value('GetBillingAgreementCustomerDetailsReq', false, $GetBillingAgreementCustomerDetailsReq, $GetBillingAgreementCustomerDetailsReq_attr);
        $result = $this->call('GetBillingAgreementCustomerDetails',
                              $v = array("GetBillingAgreementCustomerDetailsReq" => $GetBillingAgreementCustomerDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetBillingAgreementCustomerDetailsResponseType');
        $this->_logTransaction('GetBillingAgreementCustomerDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &CreateBillingAgreement($CreateBillingAgreementReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($CreateBillingAgreementReq, 'XSDSimpleType')) {
            $CreateBillingAgreementReq->setVersion(PAYPAL_WSDL_VERSION);
            $CreateBillingAgreementReq = $CreateBillingAgreementReq->getSoapValue('CreateBillingAgreementRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('CreateBillingAgreement', $this->_getElapsed($start), $res);
            return $res;
        }

        // CreateBillingAgreementReq is a ComplexType, refer to the WSDL for more info.
        $CreateBillingAgreementReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $CreateBillingAgreementReq =new SOAP_Value('CreateBillingAgreementReq', false, $CreateBillingAgreementReq, $CreateBillingAgreementReq_attr);
        $result = $this->call('CreateBillingAgreement',
                              $v = array("CreateBillingAgreementReq" => $CreateBillingAgreementReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'CreateBillingAgreementResponseType');
        $this->_logTransaction('CreateBillingAgreement', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoReferenceTransaction($DoReferenceTransactionReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoReferenceTransactionReq, 'XSDSimpleType')) {
            $DoReferenceTransactionReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoReferenceTransactionReq = $DoReferenceTransactionReq->getSoapValue('DoReferenceTransactionRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoReferenceTransaction', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoReferenceTransactionReq is a ComplexType, refer to the WSDL for more info.
        $DoReferenceTransactionReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoReferenceTransactionReq =new SOAP_Value('DoReferenceTransactionReq', false, $DoReferenceTransactionReq, $DoReferenceTransactionReq_attr);
        $result = $this->call('DoReferenceTransaction',
                              $v = array("DoReferenceTransactionReq" => $DoReferenceTransactionReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoReferenceTransactionResponseType');
        $this->_logTransaction('DoReferenceTransaction', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoNonReferencedCredit($DoNonReferencedCreditReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoNonReferencedCreditReq, 'XSDSimpleType')) {
            $DoNonReferencedCreditReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoNonReferencedCreditReq = $DoNonReferencedCreditReq->getSoapValue('DoNonReferencedCreditRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoNonReferencedCredit', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoNonReferencedCreditReq is a ComplexType, refer to the WSDL for more info.
        $DoNonReferencedCreditReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoNonReferencedCreditReq =new SOAP_Value('DoNonReferencedCreditReq', false, $DoNonReferencedCreditReq, $DoNonReferencedCreditReq_attr);
        $result = $this->call('DoNonReferencedCredit',
                              $v = array("DoNonReferencedCreditReq" => $DoNonReferencedCreditReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoNonReferencedCreditResponseType');
        $this->_logTransaction('DoNonReferencedCredit', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoUATPAuthorization($DoUATPAuthorizationReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoUATPAuthorizationReq, 'XSDSimpleType')) {
            $DoUATPAuthorizationReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoUATPAuthorizationReq = $DoUATPAuthorizationReq->getSoapValue('DoUATPAuthorizationRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('DoUATPAuthorization', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoUATPAuthorizationReq is a ComplexType, refer to the WSDL for more info.
        $DoUATPAuthorizationReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoUATPAuthorizationReq =new SOAP_Value('DoUATPAuthorizationReq', false, $DoUATPAuthorizationReq, $DoUATPAuthorizationReq_attr);
        $result = $this->call('DoUATPAuthorization',
                              $v = array("DoUATPAuthorizationReq" => $DoUATPAuthorizationReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoUATPAuthorizationResponseType');
        $this->_logTransaction('DoUATPAuthorization', $this->_getElapsed($start), $response);
        return $response;
    }

    function &CreateRecurringPaymentsProfile($CreateRecurringPaymentsProfileReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($CreateRecurringPaymentsProfileReq, 'XSDSimpleType')) {
            $CreateRecurringPaymentsProfileReq->setVersion(PAYPAL_WSDL_VERSION);
            $CreateRecurringPaymentsProfileReq = $CreateRecurringPaymentsProfileReq->getSoapValue('CreateRecurringPaymentsProfileRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('CreateRecurringPaymentsProfile', $this->_getElapsed($start), $res);
            return $res;
        }

        // CreateRecurringPaymentsProfileReq is a ComplexType, refer to the WSDL for more info.
        $CreateRecurringPaymentsProfileReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $CreateRecurringPaymentsProfileReq =new SOAP_Value('CreateRecurringPaymentsProfileReq', false, $CreateRecurringPaymentsProfileReq, $CreateRecurringPaymentsProfileReq_attr);
        $result = $this->call('CreateRecurringPaymentsProfile',
                              $v = array("CreateRecurringPaymentsProfileReq" => $CreateRecurringPaymentsProfileReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'CreateRecurringPaymentsProfileResponseType');
        $this->_logTransaction('CreateRecurringPaymentsProfile', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetRecurringPaymentsProfileDetails($GetRecurringPaymentsProfileDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetRecurringPaymentsProfileDetailsReq, 'XSDSimpleType')) {
            $GetRecurringPaymentsProfileDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetRecurringPaymentsProfileDetailsReq = $GetRecurringPaymentsProfileDetailsReq->getSoapValue('GetRecurringPaymentsProfileDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('GetRecurringPaymentsProfileDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetRecurringPaymentsProfileDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetRecurringPaymentsProfileDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetRecurringPaymentsProfileDetailsReq =new SOAP_Value('GetRecurringPaymentsProfileDetailsReq', false, $GetRecurringPaymentsProfileDetailsReq, $GetRecurringPaymentsProfileDetailsReq_attr);
        $result = $this->call('GetRecurringPaymentsProfileDetails',
                              $v = array("GetRecurringPaymentsProfileDetailsReq" => $GetRecurringPaymentsProfileDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetRecurringPaymentsProfileDetailsResponseType');
        $this->_logTransaction('GetRecurringPaymentsProfileDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &ManageRecurringPaymentsProfileStatus($ManageRecurringPaymentsProfileStatusReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($ManageRecurringPaymentsProfileStatusReq, 'XSDSimpleType')) {
            $ManageRecurringPaymentsProfileStatusReq->setVersion(PAYPAL_WSDL_VERSION);
            $ManageRecurringPaymentsProfileStatusReq = $ManageRecurringPaymentsProfileStatusReq->getSoapValue('ManageRecurringPaymentsProfileStatusRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('ManageRecurringPaymentsProfileStatus', $this->_getElapsed($start), $res);
            return $res;
        }

        // ManageRecurringPaymentsProfileStatusReq is a ComplexType, refer to the WSDL for more info.
        $ManageRecurringPaymentsProfileStatusReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $ManageRecurringPaymentsProfileStatusReq =new SOAP_Value('ManageRecurringPaymentsProfileStatusReq', false, $ManageRecurringPaymentsProfileStatusReq, $ManageRecurringPaymentsProfileStatusReq_attr);
        $result = $this->call('ManageRecurringPaymentsProfileStatus',
                              $v = array("ManageRecurringPaymentsProfileStatusReq" => $ManageRecurringPaymentsProfileStatusReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'ManageRecurringPaymentsProfileStatusResponseType');
        $this->_logTransaction('ManageRecurringPaymentsProfileStatus', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BillOutstandingAmount($BillOutstandingAmountReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BillOutstandingAmountReq, 'XSDSimpleType')) {
            $BillOutstandingAmountReq->setVersion(PAYPAL_WSDL_VERSION);
            $BillOutstandingAmountReq = $BillOutstandingAmountReq->getSoapValue('BillOutstandingAmountRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('BillOutstandingAmount', $this->_getElapsed($start), $res);
            return $res;
        }

        // BillOutstandingAmountReq is a ComplexType, refer to the WSDL for more info.
        $BillOutstandingAmountReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BillOutstandingAmountReq =new SOAP_Value('BillOutstandingAmountReq', false, $BillOutstandingAmountReq, $BillOutstandingAmountReq_attr);
        $result = $this->call('BillOutstandingAmount',
                              $v = array("BillOutstandingAmountReq" => $BillOutstandingAmountReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BillOutstandingAmountResponseType');
        $this->_logTransaction('BillOutstandingAmount', $this->_getElapsed($start), $response);
        return $response;
    }

    function &UpdateRecurringPaymentsProfile($UpdateRecurringPaymentsProfileReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($UpdateRecurringPaymentsProfileReq, 'XSDSimpleType')) {
            $UpdateRecurringPaymentsProfileReq->setVersion(PAYPAL_WSDL_VERSION);
            $UpdateRecurringPaymentsProfileReq = $UpdateRecurringPaymentsProfileReq->getSoapValue('UpdateRecurringPaymentsProfileRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (PayPal::isError($res)) {
            $this->_logTransaction('UpdateRecurringPaymentsProfile', $this->_getElapsed($start), $res);
            return $res;
        }

        // UpdateRecurringPaymentsProfileReq is a ComplexType, refer to the WSDL for more info.
        $UpdateRecurringPaymentsProfileReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $UpdateRecurringPaymentsProfileReq =new SOAP_Value('UpdateRecurringPaymentsProfileReq', false, $UpdateRecurringPaymentsProfileReq, $UpdateRecurringPaymentsProfileReq_attr);
        $result = $this->call('UpdateRecurringPaymentsProfile',
                              $v = array("UpdateRecurringPaymentsProfileReq" => $UpdateRecurringPaymentsProfileReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'UpdateRecurringPaymentsProfileResponseType');
        $this->_logTransaction('UpdateRecurringPaymentsProfile', $this->_getElapsed($start), $response);
        return $response;
    }
}
