<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * UserType
 *
 * @package PayPal
 */
class UserType extends XSDSimpleType
{
    var $AboutMePage;

    var $EAISToken;

    var $Email;

    /**
     * Feedback scores are a quantitative expression of the desirability of dealing
     * with that person as a Buyer or a Seller in auction transactions. Each auction
     * transaction can result in one feedback entry for a given user (the Buyer can
     * leave one feedback about the Seller and the Seller can leave one feedback about
     * the Buyer). That one feedback can be positive, negative, or neutral. The
     * aggregated feedback counts for a particular user represent that user's overall
     * feedback score (referred to as a "feedback rating" on the eBay site). This
     * rating is commonly expressed as the eBay Feedback score for the user.
     */
    var $FeedbackScore;

    var $FeedbackPrivate;

    var $FeedbackRatingStar;

    var $IDVerified;

    var $NewUser;

    var $RegistrationAddress;

    var $RegistrationDate;

    var $Site;

    var $Status;

    var $UserID;

    var $UserIDChanged;

    var $UserIDLastChanged;

    /**
     * If present, indicates whether or not the user is subject to VAT. Users who have
     * registered with eBay as VAT-exempt are not subject to VAT. See Value-Added Tax
     * (VAT). Not returned for users whose country of residence is outside the EU.
     * Possible values for the user's status: 2 = Residence in an EU country but user
     * registered as VAT-exempt 3 = Residence in an EU country and user not registered
     * as VAT-exempt
     */
    var $VATStatus;

    var $BuyerInfo;

    var $SellerInfo;

    function UserType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AboutMePage' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EAISToken' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Email' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FeedbackScore' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FeedbackPrivate' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FeedbackRatingStar' => 
              array (
                'required' => false,
                'type' => 'FeedbackRatingStarCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IDVerified' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'NewUser' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RegistrationAddress' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RegistrationDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Site' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Status' => 
              array (
                'required' => false,
                'type' => 'UserStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'UserID' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'UserIDChanged' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'UserIDLastChanged' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'VATStatus' => 
              array (
                'required' => false,
                'type' => 'VATStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerInfo' => 
              array (
                'required' => false,
                'type' => 'BuyerType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerInfo' => 
              array (
                'required' => false,
                'type' => 'SellerType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAboutMePage()
    {
        return $this->AboutMePage;
    }
    function setAboutMePage($AboutMePage, $charset = 'iso-8859-1')
    {
        $this->AboutMePage = $AboutMePage;
        $this->_elements['AboutMePage']['charset'] = $charset;
    }
    function getEAISToken()
    {
        return $this->EAISToken;
    }
    function setEAISToken($EAISToken, $charset = 'iso-8859-1')
    {
        $this->EAISToken = $EAISToken;
        $this->_elements['EAISToken']['charset'] = $charset;
    }
    function getEmail()
    {
        return $this->Email;
    }
    function setEmail($Email, $charset = 'iso-8859-1')
    {
        $this->Email = $Email;
        $this->_elements['Email']['charset'] = $charset;
    }
    function getFeedbackScore()
    {
        return $this->FeedbackScore;
    }
    function setFeedbackScore($FeedbackScore, $charset = 'iso-8859-1')
    {
        $this->FeedbackScore = $FeedbackScore;
        $this->_elements['FeedbackScore']['charset'] = $charset;
    }
    function getFeedbackPrivate()
    {
        return $this->FeedbackPrivate;
    }
    function setFeedbackPrivate($FeedbackPrivate, $charset = 'iso-8859-1')
    {
        $this->FeedbackPrivate = $FeedbackPrivate;
        $this->_elements['FeedbackPrivate']['charset'] = $charset;
    }
    function getFeedbackRatingStar()
    {
        return $this->FeedbackRatingStar;
    }
    function setFeedbackRatingStar($FeedbackRatingStar, $charset = 'iso-8859-1')
    {
        $this->FeedbackRatingStar = $FeedbackRatingStar;
        $this->_elements['FeedbackRatingStar']['charset'] = $charset;
    }
    function getIDVerified()
    {
        return $this->IDVerified;
    }
    function setIDVerified($IDVerified, $charset = 'iso-8859-1')
    {
        $this->IDVerified = $IDVerified;
        $this->_elements['IDVerified']['charset'] = $charset;
    }
    function getNewUser()
    {
        return $this->NewUser;
    }
    function setNewUser($NewUser, $charset = 'iso-8859-1')
    {
        $this->NewUser = $NewUser;
        $this->_elements['NewUser']['charset'] = $charset;
    }
    function getRegistrationAddress()
    {
        return $this->RegistrationAddress;
    }
    function setRegistrationAddress($RegistrationAddress, $charset = 'iso-8859-1')
    {
        $this->RegistrationAddress = $RegistrationAddress;
        $this->_elements['RegistrationAddress']['charset'] = $charset;
    }
    function getRegistrationDate()
    {
        return $this->RegistrationDate;
    }
    function setRegistrationDate($RegistrationDate, $charset = 'iso-8859-1')
    {
        $this->RegistrationDate = $RegistrationDate;
        $this->_elements['RegistrationDate']['charset'] = $charset;
    }
    function getSite()
    {
        return $this->Site;
    }
    function setSite($Site, $charset = 'iso-8859-1')
    {
        $this->Site = $Site;
        $this->_elements['Site']['charset'] = $charset;
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
    function getUserID()
    {
        return $this->UserID;
    }
    function setUserID($UserID, $charset = 'iso-8859-1')
    {
        $this->UserID = $UserID;
        $this->_elements['UserID']['charset'] = $charset;
    }
    function getUserIDChanged()
    {
        return $this->UserIDChanged;
    }
    function setUserIDChanged($UserIDChanged, $charset = 'iso-8859-1')
    {
        $this->UserIDChanged = $UserIDChanged;
        $this->_elements['UserIDChanged']['charset'] = $charset;
    }
    function getUserIDLastChanged()
    {
        return $this->UserIDLastChanged;
    }
    function setUserIDLastChanged($UserIDLastChanged, $charset = 'iso-8859-1')
    {
        $this->UserIDLastChanged = $UserIDLastChanged;
        $this->_elements['UserIDLastChanged']['charset'] = $charset;
    }
    function getVATStatus()
    {
        return $this->VATStatus;
    }
    function setVATStatus($VATStatus, $charset = 'iso-8859-1')
    {
        $this->VATStatus = $VATStatus;
        $this->_elements['VATStatus']['charset'] = $charset;
    }
    function getBuyerInfo()
    {
        return $this->BuyerInfo;
    }
    function setBuyerInfo($BuyerInfo, $charset = 'iso-8859-1')
    {
        $this->BuyerInfo = $BuyerInfo;
        $this->_elements['BuyerInfo']['charset'] = $charset;
    }
    function getSellerInfo()
    {
        return $this->SellerInfo;
    }
    function setSellerInfo($SellerInfo, $charset = 'iso-8859-1')
    {
        $this->SellerInfo = $SellerInfo;
        $this->_elements['SellerInfo']['charset'] = $charset;
    }
}
