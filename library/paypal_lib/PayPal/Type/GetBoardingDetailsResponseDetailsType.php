<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * GetBoardingDetailsResponseDetailsType
 *
 * @package PayPal
 */
class GetBoardingDetailsResponseDetailsType extends XSDSimpleType
{
    /**
     * Status of merchant's onboarding process:
     */
    var $Status;

    /**
     * Date the boarding process started
     */
    var $StartDate;

    /**
     * Date the merchant ’s status or progress was last updated
     */
    var $LastUpdated;

    /**
     * Reason for merchant ’s cancellation of sign-up.
     */
    var $Reason;

    var $ProgramName;

    var $ProgramCode;

    var $CampaignID;

    /**
     * Indicates if there is a limitation on the amount of money the business can
     * withdraw from PayPal
     */
    var $UserWithdrawalLimit;

    /**
     * Custom information you set on the EnterBoarding API call
     */
    var $PartnerCustom;

    /**
     * Details about the owner of the account
     */
    var $AccountOwner;

    /**
     * Merchant ’s PayPal API credentials
     */
    var $Credentials;

    /**
     * The APIs that this merchant has granted the business partner permission to call
     * on his behalf.
     */
    var $ConfigureAPIs;

    /**
     * Primary email verification status. Confirmed, Unconfirmed
     */
    var $EmailVerificationStatus;

    /**
     * Gives VettingStatus - Pending, Cancelled, Approved, UnderReview
     */
    var $VettingStatus;

    /**
     * Gives BankAccountVerificationStatus - Added, Confirmed
     */
    var $BankAccountVerificationStatus;

    function GetBoardingDetailsResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Status' => 
              array (
                'required' => true,
                'type' => 'BoardingStatusType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StartDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastUpdated' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Reason' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProgramName' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProgramCode' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CampaignID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'UserWithdrawalLimit' => 
              array (
                'required' => false,
                'type' => 'UserWithdrawalLimitTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PartnerCustom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AccountOwner' => 
              array (
                'required' => false,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Credentials' => 
              array (
                'required' => false,
                'type' => 'APICredentialsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ConfigureAPIs' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EmailVerificationStatus' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'VettingStatus' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BankAccountVerificationStatus' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getStatus()
    {
        return $this->Status;
    }
    function setStatus($Status, $charset = 'iso-8859-1')
    {
        $this->Status = $Status;
        $this->_elements['Status']['charset'] = $charset;
    }
    function getStartDate()
    {
        return $this->StartDate;
    }
    function setStartDate($StartDate, $charset = 'iso-8859-1')
    {
        $this->StartDate = $StartDate;
        $this->_elements['StartDate']['charset'] = $charset;
    }
    function getLastUpdated()
    {
        return $this->LastUpdated;
    }
    function setLastUpdated($LastUpdated, $charset = 'iso-8859-1')
    {
        $this->LastUpdated = $LastUpdated;
        $this->_elements['LastUpdated']['charset'] = $charset;
    }
    function getReason()
    {
        return $this->Reason;
    }
    function setReason($Reason, $charset = 'iso-8859-1')
    {
        $this->Reason = $Reason;
        $this->_elements['Reason']['charset'] = $charset;
    }
    function getProgramName()
    {
        return $this->ProgramName;
    }
    function setProgramName($ProgramName, $charset = 'iso-8859-1')
    {
        $this->ProgramName = $ProgramName;
        $this->_elements['ProgramName']['charset'] = $charset;
    }
    function getProgramCode()
    {
        return $this->ProgramCode;
    }
    function setProgramCode($ProgramCode, $charset = 'iso-8859-1')
    {
        $this->ProgramCode = $ProgramCode;
        $this->_elements['ProgramCode']['charset'] = $charset;
    }
    function getCampaignID()
    {
        return $this->CampaignID;
    }
    function setCampaignID($CampaignID, $charset = 'iso-8859-1')
    {
        $this->CampaignID = $CampaignID;
        $this->_elements['CampaignID']['charset'] = $charset;
    }
    function getUserWithdrawalLimit()
    {
        return $this->UserWithdrawalLimit;
    }
    function setUserWithdrawalLimit($UserWithdrawalLimit, $charset = 'iso-8859-1')
    {
        $this->UserWithdrawalLimit = $UserWithdrawalLimit;
        $this->_elements['UserWithdrawalLimit']['charset'] = $charset;
    }
    function getPartnerCustom()
    {
        return $this->PartnerCustom;
    }
    function setPartnerCustom($PartnerCustom, $charset = 'iso-8859-1')
    {
        $this->PartnerCustom = $PartnerCustom;
        $this->_elements['PartnerCustom']['charset'] = $charset;
    }
    function getAccountOwner()
    {
        return $this->AccountOwner;
    }
    function setAccountOwner($AccountOwner, $charset = 'iso-8859-1')
    {
        $this->AccountOwner = $AccountOwner;
        $this->_elements['AccountOwner']['charset'] = $charset;
    }
    function getCredentials()
    {
        return $this->Credentials;
    }
    function setCredentials($Credentials, $charset = 'iso-8859-1')
    {
        $this->Credentials = $Credentials;
        $this->_elements['Credentials']['charset'] = $charset;
    }
    function getConfigureAPIs()
    {
        return $this->ConfigureAPIs;
    }
    function setConfigureAPIs($ConfigureAPIs, $charset = 'iso-8859-1')
    {
        $this->ConfigureAPIs = $ConfigureAPIs;
        $this->_elements['ConfigureAPIs']['charset'] = $charset;
    }
    function getEmailVerificationStatus()
    {
        return $this->EmailVerificationStatus;
    }
    function setEmailVerificationStatus($EmailVerificationStatus, $charset = 'iso-8859-1')
    {
        $this->EmailVerificationStatus = $EmailVerificationStatus;
        $this->_elements['EmailVerificationStatus']['charset'] = $charset;
    }
    function getVettingStatus()
    {
        return $this->VettingStatus;
    }
    function setVettingStatus($VettingStatus, $charset = 'iso-8859-1')
    {
        $this->VettingStatus = $VettingStatus;
        $this->_elements['VettingStatus']['charset'] = $charset;
    }
    function getBankAccountVerificationStatus()
    {
        return $this->BankAccountVerificationStatus;
    }
    function setBankAccountVerificationStatus($BankAccountVerificationStatus, $charset = 'iso-8859-1')
    {
        $this->BankAccountVerificationStatus = $BankAccountVerificationStatus;
        $this->_elements['BankAccountVerificationStatus']['charset'] = $charset;
    }
}
