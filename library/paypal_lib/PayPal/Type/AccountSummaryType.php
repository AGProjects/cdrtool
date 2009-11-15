<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AccountSummaryType
 * 
 * Includes account summary for the user.
 *
 * @package PayPal
 */
class AccountSummaryType extends XSDSimpleType
{
    var $AccountState;

    var $AdditionalAccount;

    /**
     * Number of additional accounts.
     */
    var $AdditionalAccountsCount;

    /**
     * Amount past due, 0.00 if not past due.
     */
    var $AmountPastDue;

    /**
     * First four digits (with remainder Xed-out). This may be an empty string
     * depending upon the value of the payment type for the user account (e.g, if no
     * debit-card specified).
     */
    var $BankAccountInfo;

    /**
     * Last time/day BankAccountInfo and/or BankRoutingInfo was modified, in GMT. This
     * may be an empty string depending upon the value of the payment type for the user
     * account (e.g, if no debit-card specified).
     */
    var $BankModifyDate;

    /**
     * Indicates the billing cycle in which eBay sends a billing invoice to the
     * specified user. Possible values: 0 = On the last day of the month. 15 = On the
     * 15th day of the month.
     */
    var $BillingCycleDate;

    /**
     * Expiration date for the credit card selected as payment method, in GMT. Empty
     * string if no credit card is on file or if account is inactive -- even if there
     * is a credit card on file.
     */
    var $CCExp;

    /**
     * Last four digits of user's credit card selected as payment type. Empty string if
     * no credit is on file. This may be an empty string depending upon the value of
     * the payment type for the user account (e.g, if no debit-card specified).
     */
    var $CCInfo;

    /**
     * Last date credit card or credit card expiration date was modified, in GMT. This
     * may be an empty string depending upon the value of the payment type for the user
     * account (e.g, Empty string if no credit card is on file.
     */
    var $CCModifyDate;

    /**
     * User's current balance. Can be 0.00, positive, or negative.
     */
    var $CurrentBalance;

    /**
     * Amount of last payment posted, 0.00 if no payments posted.
     */
    var $LastAmountPaid;

    /**
     * Amount of last invoice. 0.00 if account not yet invoiced.
     */
    var $LastInvoiceAmount;

    /**
     * Date of last invoice sent by eBay to the user, in GMT. Empty string if this
     * account has not been invoiced yet.
     */
    var $LastInvoiceDate;

    /**
     * Date of last payment by specified user to eBay, in GMT. Empty string if no
     * payments posted.
     */
    var $LastPaymentDate;

    /**
     * Indicates whether the account has past due amounts outstanding. Possible values:
     * true = Account is past due. false = Account is current.
     */
    var $PastDue;

    /**
     * Indicates the method the specified user selected for paying eBay. The values for
     * PaymentType vary for each SiteID.
     */
    var $PaymentMethod;

    function AccountSummaryType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AccountState' => 
              array (
                'required' => false,
                'type' => 'AccountStateCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AdditionalAccount' => 
              array (
                'required' => false,
                'type' => 'AdditionalAccountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AdditionalAccountsCount' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AmountPastDue' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BankAccountInfo' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BankModifyDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingCycleDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CCExp' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CCInfo' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CCModifyDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CurrentBalance' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastAmountPaid' => 
              array (
                'required' => true,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastInvoiceAmount' => 
              array (
                'required' => true,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastInvoiceDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastPaymentDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PastDue' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentMethod' => 
              array (
                'required' => true,
                'type' => 'SellerPaymentMethodCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAccountState()
    {
        return $this->AccountState;
    }
    function setAccountState($AccountState, $charset = 'iso-8859-1')
    {
        $this->AccountState = $AccountState;
        $this->_elements['AccountState']['charset'] = $charset;
    }
    function getAdditionalAccount()
    {
        return $this->AdditionalAccount;
    }
    function setAdditionalAccount($AdditionalAccount, $charset = 'iso-8859-1')
    {
        $this->AdditionalAccount = $AdditionalAccount;
        $this->_elements['AdditionalAccount']['charset'] = $charset;
    }
    function getAdditionalAccountsCount()
    {
        return $this->AdditionalAccountsCount;
    }
    function setAdditionalAccountsCount($AdditionalAccountsCount, $charset = 'iso-8859-1')
    {
        $this->AdditionalAccountsCount = $AdditionalAccountsCount;
        $this->_elements['AdditionalAccountsCount']['charset'] = $charset;
    }
    function getAmountPastDue()
    {
        return $this->AmountPastDue;
    }
    function setAmountPastDue($AmountPastDue, $charset = 'iso-8859-1')
    {
        $this->AmountPastDue = $AmountPastDue;
        $this->_elements['AmountPastDue']['charset'] = $charset;
    }
    function getBankAccountInfo()
    {
        return $this->BankAccountInfo;
    }
    function setBankAccountInfo($BankAccountInfo, $charset = 'iso-8859-1')
    {
        $this->BankAccountInfo = $BankAccountInfo;
        $this->_elements['BankAccountInfo']['charset'] = $charset;
    }
    function getBankModifyDate()
    {
        return $this->BankModifyDate;
    }
    function setBankModifyDate($BankModifyDate, $charset = 'iso-8859-1')
    {
        $this->BankModifyDate = $BankModifyDate;
        $this->_elements['BankModifyDate']['charset'] = $charset;
    }
    function getBillingCycleDate()
    {
        return $this->BillingCycleDate;
    }
    function setBillingCycleDate($BillingCycleDate, $charset = 'iso-8859-1')
    {
        $this->BillingCycleDate = $BillingCycleDate;
        $this->_elements['BillingCycleDate']['charset'] = $charset;
    }
    function getCCExp()
    {
        return $this->CCExp;
    }
    function setCCExp($CCExp, $charset = 'iso-8859-1')
    {
        $this->CCExp = $CCExp;
        $this->_elements['CCExp']['charset'] = $charset;
    }
    function getCCInfo()
    {
        return $this->CCInfo;
    }
    function setCCInfo($CCInfo, $charset = 'iso-8859-1')
    {
        $this->CCInfo = $CCInfo;
        $this->_elements['CCInfo']['charset'] = $charset;
    }
    function getCCModifyDate()
    {
        return $this->CCModifyDate;
    }
    function setCCModifyDate($CCModifyDate, $charset = 'iso-8859-1')
    {
        $this->CCModifyDate = $CCModifyDate;
        $this->_elements['CCModifyDate']['charset'] = $charset;
    }
    function getCurrentBalance()
    {
        return $this->CurrentBalance;
    }
    function setCurrentBalance($CurrentBalance, $charset = 'iso-8859-1')
    {
        $this->CurrentBalance = $CurrentBalance;
        $this->_elements['CurrentBalance']['charset'] = $charset;
    }
    function getLastAmountPaid()
    {
        return $this->LastAmountPaid;
    }
    function setLastAmountPaid($LastAmountPaid, $charset = 'iso-8859-1')
    {
        $this->LastAmountPaid = $LastAmountPaid;
        $this->_elements['LastAmountPaid']['charset'] = $charset;
    }
    function getLastInvoiceAmount()
    {
        return $this->LastInvoiceAmount;
    }
    function setLastInvoiceAmount($LastInvoiceAmount, $charset = 'iso-8859-1')
    {
        $this->LastInvoiceAmount = $LastInvoiceAmount;
        $this->_elements['LastInvoiceAmount']['charset'] = $charset;
    }
    function getLastInvoiceDate()
    {
        return $this->LastInvoiceDate;
    }
    function setLastInvoiceDate($LastInvoiceDate, $charset = 'iso-8859-1')
    {
        $this->LastInvoiceDate = $LastInvoiceDate;
        $this->_elements['LastInvoiceDate']['charset'] = $charset;
    }
    function getLastPaymentDate()
    {
        return $this->LastPaymentDate;
    }
    function setLastPaymentDate($LastPaymentDate, $charset = 'iso-8859-1')
    {
        $this->LastPaymentDate = $LastPaymentDate;
        $this->_elements['LastPaymentDate']['charset'] = $charset;
    }
    function getPastDue()
    {
        return $this->PastDue;
    }
    function setPastDue($PastDue, $charset = 'iso-8859-1')
    {
        $this->PastDue = $PastDue;
        $this->_elements['PastDue']['charset'] = $charset;
    }
    function getPaymentMethod()
    {
        return $this->PaymentMethod;
    }
    function setPaymentMethod($PaymentMethod, $charset = 'iso-8859-1')
    {
        $this->PaymentMethod = $PaymentMethod;
        $this->_elements['PaymentMethod']['charset'] = $charset;
    }
}
