<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ItemType
 *
 * @package PayPal
 */
class ItemType extends XSDSimpleType
{
    /**
     * Returns custom, application-specific data associated with the item. The data in
     * this field is stored with the item in the items table at eBay, but is not used
     * in any way by eBay. Use ApplicationData to store such special information as a
     * part or SKU number. Maximum 32 characters in length.
     */
    var $ApplicationData;

    /**
     * Carries one or more instances of the AttributeSet in a list.
     */
    var $ListOfAttributeSets;

    /**
     * If true (1), indicates that the seller requested immediate payment for the item.
     * False (0) if immediate payment was not requested. (Does not indicate whether the
     * item is still a candidate for puchase via immediate payment.) Only applicable
     * for items listed on US and UK sites in categories that support immediate
     * payment, when seller has a Premier or Business PayPal account.
     */
    var $AutoPay;

    /**
     * Indicates the status of the item's eligibility for the Buyer Protection Program.
     * Possible values: ItemIneligible - Item is ineligible (e.g., category not
     * applicable) ItemEligible - Item is eligible per standard criteria
     * ItemMarkedIneligible - Item marked ineligible per special criteria (e.g.,
     * seller's account closed) ItemMarkedIneligible - Item marked elegible per other
     * criteria Applicable for items listed to the US site and for the Parts and
     * Accessories category (6028) or Everything Else category (10368) (or their
     * subcategories) on the eBay Motors site.
     */
    var $BuyerProtection;

    /**
     * Amount a Buyer would need to bid to take advantage of the Buy It Now feature.
     * Not applicable to Fixed-Price items (Type = 7 or 9) or AdFormat-type listings.
     * For Fixed-Price items, see StartPrice instead.
     */
    var $BuyItNowPrice;

    /**
     * Charity listing container.
     */
    var $Charity;

    /**
     * 2-letter ISO 3166 Country Code.
     */
    var $Country;

    /**
     * CrossPromotions container, if applicable shows promoted items
     */
    var $CrossPromotion;

    /**
     * 3-letter ISO Currency Code.
     */
    var $Currency;

    /**
     * Item Description.
     */
    var $Description;

    /**
     * Online Escrow paid for by buyer or seller. Cannot use with real estate auctions.
     * Escrow is recommended for for transactions over $500. Escrow service, available
     * via Escrow.com, protects both buyer and seller by acting as a trusted
     * third-party during the transaction and managing the payment process from start
     * to finish. Also, if escrow by seller option used, then for Motors, this means
     * that Escrow will be negotiated at the end of the auction.
     */
    var $Escrow;

    /**
     * If set, a generic gift icon displays in the listing's Title. GiftIcon must be
     * set to to be able to use GiftServices options (e.g., GiftExpressShipping,
     * GiftShipToRecipient, or GiftWrap).
     */
    var $GiftIcon;

    /**
     * Gift service options offered by the seller of the listed item.
     */
    var $GiftServices;

    /**
     * Optional hit counter for the item's listing page. Possible values are:
     * "NoHitCounter" "HonestyStyle" "GreenLED" "Hidden"
     */
    var $HitCounter;

    /**
     * The ID that uniquely identifies the item listing.
     */
    var $ItemID;

    /**
     * Includes listing details in terms of start and end time of listing (in GMT) as
     * well as other details (e.g., orginal item for second chance, converted start
     * price, etc.).
     */
    var $ListingDetails;

    /**
     * When an item is first listed (using AddItem), a Layout template or a Theme
     * template (or both) can be assigned to the item. A Layout template is assigned to
     * a new item by specifying the Layout template ID (in the AddItem input argument
     * LayoutID). Similarly, a Theme template is assigned to the item using the ThemeID
     * argument.
     */
    var $ListingDesigner;

    /**
     * Describes the number of days the auction will be active.
     */
    var $ListingDuration;

    /**
     * Describes the types of enhancment supported for the item's listing.
     */
    var $ListingEnhancement;

    /**
     * Describes the type of listing for the item a seller has chosen (e.g., Chinese,
     * Dutch, FixedPrice, etc.).
     */
    var $ListingType;

    /**
     * Indicates the geographical location of the item.
     */
    var $Location;

    /**
     * Needed for add item only for partners.
     */
    var $PartnerCode;

    /**
     * Needed for add item only for partners.
     */
    var $PartnerName;

    /**
     * List of payment methods accepted by a seller from a buyer for a (checkout)
     * transaction.
     */
    var $PaymentMethods;

    /**
     * Valid PayPal e-mail address if seller has chosen PayPal as a payment method for
     * the listed item.
     */
    var $PayPalEmailAddress;

    /**
     * Container for data on the primary category of listing.
     */
    var $PrimaryCategory;

    /**
     * Private auction. Not applicable to Fixed Price items.
     */
    var $PrivateListing;

    /**
     * Number of items being sold in the auction.
     */
    var $Quantity;

    /**
     * Region where the item is listed. See Region Table for values. If the item is
     * listed with a Region of 0 (zero), then this return field denotes no region
     * association with the item, meaning that it is not listing the item regionally.
     */
    var $RegionID;

    /**
     * If true, creates a link from the old listing for the item to the new relist
     * page, which accommodates users who might still look for the item under its old
     * item ID. Also adds the relist ID to the old listing's record in the eBay
     * database, which can be returned by calling GetItem for the old ItemId. If your
     * application creates the listing page for the user, you need to add the relist
     * link option to your application for your users.
     */
    var $RelistLink;

    /**
     * Indicates the reserve price for a reserve auction. Returned only if DetailLevel
     * = 4. ReservePrice is only returned for auctions with a reserve price where the
     * user calling GetItem is the item's seller. Returned as null for International
     * Fixed Price items. For more information on reserve price auctions, see
     * http://pages.ebay.com/help/basics/f-format.html#1.
     */
    var $ReservePrice;

    /**
     * Revise Status contains information about the item being revised.
     */
    var $ReviseStatus;

    var $ScheduleTime;

    /**
     * Container for data on the secondary category of listing. Secondary category is
     * optional.
     */
    var $SecondaryCategory;

    /**
     * Item picture information for pictures hosted at eBay site.
     */
    var $SiteHostedPicture;

    /**
     * Seller user.
     */
    var $Seller;

    /**
     * Container for for selling status information (e.g., BidCount, BidIncrement,
     * HighBidder, MinimimumToBid, etc).
     */
    var $SellingStatus;

    /**
     * Specifies where the seller is willing to ship the item. Default "SiteOnly".
     * Valid values are: SiteOnly (the default) WorldWide SitePlusRegions WillNotShip
     * If SitePlusRegions is selected, then at least one regions argument
     * (ShipToNorthAmerica, ShipToEurope, etc.) must also be set.
     */
    var $ShippingOption;

    /**
     * Contains the shipping payment related information for the listed item.
     */
    var $ShippingDetails;

    /**
     * Regions that seller will ship to.
     */
    var $ShippingRegions;

    /**
     * Describes who pays for the delivery of an item (e.g., buyer or seller).
     */
    var $ShippingTerms;

    /**
     * eBay site on which item is listed.
     */
    var $Site;

    /**
     * Starting price for the item. For Type=7 or Type=9 (Fixed Price) items, if the
     * item price (MinimumBid) is revised, this field returns the new price.
     */
    var $StartPrice;

    /**
     * Storefront is shown for any item that belongs to an eBay Store owner, regardless
     * of whether it is fixed price or auction type. Not returned for International
     * Fixed Price items.
     */
    var $Storefront;

    /**
     * Subtitle to use in addition to the title. Provides more keywords when buyers
     * search in titles and descriptions.
     */
    var $SubTitle;

    /**
     * Time until the the end of the listing (e.g., the amount of time left in an
     * active auction).
     */
    var $TimeLeft;

    /**
     * Name of the item as it appears for auctions.
     */
    var $Title;

    /**
     * Universally unique constraint tag. The UUID is unique to a category.
     */
    var $UUID;

    /**
     * VAT info container.
     */
    var $VATDetails;

    /**
     * Item picture information for pictures hosted at vendor (i.e., remote) site.
     */
    var $VendorHostedPicture;

    function ItemType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ApplicationData' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListOfAttributeSets' => 
              array (
                'required' => false,
                'type' => 'ListOfAttributeSetType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AutoPay' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerProtection' => 
              array (
                'required' => false,
                'type' => 'BuyerProtectionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyItNowPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Charity' => 
              array (
                'required' => false,
                'type' => 'CharityType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Country' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CrossPromotion' => 
              array (
                'required' => false,
                'type' => 'CrossPromotionsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Currency' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Description' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Escrow' => 
              array (
                'required' => false,
                'type' => 'EscrowCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'GiftIcon' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'GiftServices' => 
              array (
                'required' => false,
                'type' => 'GiftServicesCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HitCounter' => 
              array (
                'required' => false,
                'type' => 'HitCounterCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemID' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListingDetails' => 
              array (
                'required' => false,
                'type' => 'ListingDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListingDesigner' => 
              array (
                'required' => false,
                'type' => 'ListingDesignerType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListingDuration' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListingEnhancement' => 
              array (
                'required' => false,
                'type' => 'ListingEnhancementsCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ListingType' => 
              array (
                'required' => false,
                'type' => 'ListingTypeCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Location' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PartnerCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PartnerName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentMethods' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayPalEmailAddress' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PrimaryCategory' => 
              array (
                'required' => false,
                'type' => 'CategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PrivateListing' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Quantity' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RegionID' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RelistLink' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReservePrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReviseStatus' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ScheduleTime' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SecondaryCategory' => 
              array (
                'required' => false,
                'type' => 'CategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SiteHostedPicture' => 
              array (
                'required' => false,
                'type' => 'SiteHostedPictureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Seller' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellingStatus' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingOption' => 
              array (
                'required' => false,
                'type' => 'ShippingOptionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingDetails' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingRegions' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingTerms' => 
              array (
                'required' => false,
                'type' => 'ShippingTermsCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Site' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StartPrice' => 
              array (
                'required' => false,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Storefront' => 
              array (
                'required' => false,
                'type' => 'StorefrontType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SubTitle' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TimeLeft' => 
              array (
                'required' => false,
                'type' => 'duration',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Title' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'UUID' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'VATDetails' => 
              array (
                'required' => false,
                'type' => 'VATDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'VendorHostedPicture' => 
              array (
                'required' => false,
                'type' => 'VendorHostedPictureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getApplicationData()
    {
        return $this->ApplicationData;
    }
    function setApplicationData($ApplicationData, $charset = 'iso-8859-1')
    {
        $this->ApplicationData = $ApplicationData;
        $this->_elements['ApplicationData']['charset'] = $charset;
    }
    function getListOfAttributeSets()
    {
        return $this->ListOfAttributeSets;
    }
    function setListOfAttributeSets($ListOfAttributeSets, $charset = 'iso-8859-1')
    {
        $this->ListOfAttributeSets = $ListOfAttributeSets;
        $this->_elements['ListOfAttributeSets']['charset'] = $charset;
    }
    function getAutoPay()
    {
        return $this->AutoPay;
    }
    function setAutoPay($AutoPay, $charset = 'iso-8859-1')
    {
        $this->AutoPay = $AutoPay;
        $this->_elements['AutoPay']['charset'] = $charset;
    }
    function getBuyerProtection()
    {
        return $this->BuyerProtection;
    }
    function setBuyerProtection($BuyerProtection, $charset = 'iso-8859-1')
    {
        $this->BuyerProtection = $BuyerProtection;
        $this->_elements['BuyerProtection']['charset'] = $charset;
    }
    function getBuyItNowPrice()
    {
        return $this->BuyItNowPrice;
    }
    function setBuyItNowPrice($BuyItNowPrice, $charset = 'iso-8859-1')
    {
        $this->BuyItNowPrice = $BuyItNowPrice;
        $this->_elements['BuyItNowPrice']['charset'] = $charset;
    }
    function getCharity()
    {
        return $this->Charity;
    }
    function setCharity($Charity, $charset = 'iso-8859-1')
    {
        $this->Charity = $Charity;
        $this->_elements['Charity']['charset'] = $charset;
    }
    function getCountry()
    {
        return $this->Country;
    }
    function setCountry($Country, $charset = 'iso-8859-1')
    {
        $this->Country = $Country;
        $this->_elements['Country']['charset'] = $charset;
    }
    function getCrossPromotion()
    {
        return $this->CrossPromotion;
    }
    function setCrossPromotion($CrossPromotion, $charset = 'iso-8859-1')
    {
        $this->CrossPromotion = $CrossPromotion;
        $this->_elements['CrossPromotion']['charset'] = $charset;
    }
    function getCurrency()
    {
        return $this->Currency;
    }
    function setCurrency($Currency, $charset = 'iso-8859-1')
    {
        $this->Currency = $Currency;
        $this->_elements['Currency']['charset'] = $charset;
    }
    function getDescription()
    {
        return $this->Description;
    }
    function setDescription($Description, $charset = 'iso-8859-1')
    {
        $this->Description = $Description;
        $this->_elements['Description']['charset'] = $charset;
    }
    function getEscrow()
    {
        return $this->Escrow;
    }
    function setEscrow($Escrow, $charset = 'iso-8859-1')
    {
        $this->Escrow = $Escrow;
        $this->_elements['Escrow']['charset'] = $charset;
    }
    function getGiftIcon()
    {
        return $this->GiftIcon;
    }
    function setGiftIcon($GiftIcon, $charset = 'iso-8859-1')
    {
        $this->GiftIcon = $GiftIcon;
        $this->_elements['GiftIcon']['charset'] = $charset;
    }
    function getGiftServices()
    {
        return $this->GiftServices;
    }
    function setGiftServices($GiftServices, $charset = 'iso-8859-1')
    {
        $this->GiftServices = $GiftServices;
        $this->_elements['GiftServices']['charset'] = $charset;
    }
    function getHitCounter()
    {
        return $this->HitCounter;
    }
    function setHitCounter($HitCounter, $charset = 'iso-8859-1')
    {
        $this->HitCounter = $HitCounter;
        $this->_elements['HitCounter']['charset'] = $charset;
    }
    function getItemID()
    {
        return $this->ItemID;
    }
    function setItemID($ItemID, $charset = 'iso-8859-1')
    {
        $this->ItemID = $ItemID;
        $this->_elements['ItemID']['charset'] = $charset;
    }
    function getListingDetails()
    {
        return $this->ListingDetails;
    }
    function setListingDetails($ListingDetails, $charset = 'iso-8859-1')
    {
        $this->ListingDetails = $ListingDetails;
        $this->_elements['ListingDetails']['charset'] = $charset;
    }
    function getListingDesigner()
    {
        return $this->ListingDesigner;
    }
    function setListingDesigner($ListingDesigner, $charset = 'iso-8859-1')
    {
        $this->ListingDesigner = $ListingDesigner;
        $this->_elements['ListingDesigner']['charset'] = $charset;
    }
    function getListingDuration()
    {
        return $this->ListingDuration;
    }
    function setListingDuration($ListingDuration, $charset = 'iso-8859-1')
    {
        $this->ListingDuration = $ListingDuration;
        $this->_elements['ListingDuration']['charset'] = $charset;
    }
    function getListingEnhancement()
    {
        return $this->ListingEnhancement;
    }
    function setListingEnhancement($ListingEnhancement, $charset = 'iso-8859-1')
    {
        $this->ListingEnhancement = $ListingEnhancement;
        $this->_elements['ListingEnhancement']['charset'] = $charset;
    }
    function getListingType()
    {
        return $this->ListingType;
    }
    function setListingType($ListingType, $charset = 'iso-8859-1')
    {
        $this->ListingType = $ListingType;
        $this->_elements['ListingType']['charset'] = $charset;
    }
    function getLocation()
    {
        return $this->Location;
    }
    function setLocation($Location, $charset = 'iso-8859-1')
    {
        $this->Location = $Location;
        $this->_elements['Location']['charset'] = $charset;
    }
    function getPartnerCode()
    {
        return $this->PartnerCode;
    }
    function setPartnerCode($PartnerCode, $charset = 'iso-8859-1')
    {
        $this->PartnerCode = $PartnerCode;
        $this->_elements['PartnerCode']['charset'] = $charset;
    }
    function getPartnerName()
    {
        return $this->PartnerName;
    }
    function setPartnerName($PartnerName, $charset = 'iso-8859-1')
    {
        $this->PartnerName = $PartnerName;
        $this->_elements['PartnerName']['charset'] = $charset;
    }
    function getPaymentMethods()
    {
        return $this->PaymentMethods;
    }
    function setPaymentMethods($PaymentMethods, $charset = 'iso-8859-1')
    {
        $this->PaymentMethods = $PaymentMethods;
        $this->_elements['PaymentMethods']['charset'] = $charset;
    }
    function getPayPalEmailAddress()
    {
        return $this->PayPalEmailAddress;
    }
    function setPayPalEmailAddress($PayPalEmailAddress, $charset = 'iso-8859-1')
    {
        $this->PayPalEmailAddress = $PayPalEmailAddress;
        $this->_elements['PayPalEmailAddress']['charset'] = $charset;
    }
    function getPrimaryCategory()
    {
        return $this->PrimaryCategory;
    }
    function setPrimaryCategory($PrimaryCategory, $charset = 'iso-8859-1')
    {
        $this->PrimaryCategory = $PrimaryCategory;
        $this->_elements['PrimaryCategory']['charset'] = $charset;
    }
    function getPrivateListing()
    {
        return $this->PrivateListing;
    }
    function setPrivateListing($PrivateListing, $charset = 'iso-8859-1')
    {
        $this->PrivateListing = $PrivateListing;
        $this->_elements['PrivateListing']['charset'] = $charset;
    }
    function getQuantity()
    {
        return $this->Quantity;
    }
    function setQuantity($Quantity, $charset = 'iso-8859-1')
    {
        $this->Quantity = $Quantity;
        $this->_elements['Quantity']['charset'] = $charset;
    }
    function getRegionID()
    {
        return $this->RegionID;
    }
    function setRegionID($RegionID, $charset = 'iso-8859-1')
    {
        $this->RegionID = $RegionID;
        $this->_elements['RegionID']['charset'] = $charset;
    }
    function getRelistLink()
    {
        return $this->RelistLink;
    }
    function setRelistLink($RelistLink, $charset = 'iso-8859-1')
    {
        $this->RelistLink = $RelistLink;
        $this->_elements['RelistLink']['charset'] = $charset;
    }
    function getReservePrice()
    {
        return $this->ReservePrice;
    }
    function setReservePrice($ReservePrice, $charset = 'iso-8859-1')
    {
        $this->ReservePrice = $ReservePrice;
        $this->_elements['ReservePrice']['charset'] = $charset;
    }
    function getReviseStatus()
    {
        return $this->ReviseStatus;
    }
    function setReviseStatus($ReviseStatus, $charset = 'iso-8859-1')
    {
        $this->ReviseStatus = $ReviseStatus;
        $this->_elements['ReviseStatus']['charset'] = $charset;
    }
    function getScheduleTime()
    {
        return $this->ScheduleTime;
    }
    function setScheduleTime($ScheduleTime, $charset = 'iso-8859-1')
    {
        $this->ScheduleTime = $ScheduleTime;
        $this->_elements['ScheduleTime']['charset'] = $charset;
    }
    function getSecondaryCategory()
    {
        return $this->SecondaryCategory;
    }
    function setSecondaryCategory($SecondaryCategory, $charset = 'iso-8859-1')
    {
        $this->SecondaryCategory = $SecondaryCategory;
        $this->_elements['SecondaryCategory']['charset'] = $charset;
    }
    function getSiteHostedPicture()
    {
        return $this->SiteHostedPicture;
    }
    function setSiteHostedPicture($SiteHostedPicture, $charset = 'iso-8859-1')
    {
        $this->SiteHostedPicture = $SiteHostedPicture;
        $this->_elements['SiteHostedPicture']['charset'] = $charset;
    }
    function getSeller()
    {
        return $this->Seller;
    }
    function setSeller($Seller, $charset = 'iso-8859-1')
    {
        $this->Seller = $Seller;
        $this->_elements['Seller']['charset'] = $charset;
    }
    function getSellingStatus()
    {
        return $this->SellingStatus;
    }
    function setSellingStatus($SellingStatus, $charset = 'iso-8859-1')
    {
        $this->SellingStatus = $SellingStatus;
        $this->_elements['SellingStatus']['charset'] = $charset;
    }
    function getShippingOption()
    {
        return $this->ShippingOption;
    }
    function setShippingOption($ShippingOption, $charset = 'iso-8859-1')
    {
        $this->ShippingOption = $ShippingOption;
        $this->_elements['ShippingOption']['charset'] = $charset;
    }
    function getShippingDetails()
    {
        return $this->ShippingDetails;
    }
    function setShippingDetails($ShippingDetails, $charset = 'iso-8859-1')
    {
        $this->ShippingDetails = $ShippingDetails;
        $this->_elements['ShippingDetails']['charset'] = $charset;
    }
    function getShippingRegions()
    {
        return $this->ShippingRegions;
    }
    function setShippingRegions($ShippingRegions, $charset = 'iso-8859-1')
    {
        $this->ShippingRegions = $ShippingRegions;
        $this->_elements['ShippingRegions']['charset'] = $charset;
    }
    function getShippingTerms()
    {
        return $this->ShippingTerms;
    }
    function setShippingTerms($ShippingTerms, $charset = 'iso-8859-1')
    {
        $this->ShippingTerms = $ShippingTerms;
        $this->_elements['ShippingTerms']['charset'] = $charset;
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
    function getStartPrice()
    {
        return $this->StartPrice;
    }
    function setStartPrice($StartPrice, $charset = 'iso-8859-1')
    {
        $this->StartPrice = $StartPrice;
        $this->_elements['StartPrice']['charset'] = $charset;
    }
    function getStorefront()
    {
        return $this->Storefront;
    }
    function setStorefront($Storefront, $charset = 'iso-8859-1')
    {
        $this->Storefront = $Storefront;
        $this->_elements['Storefront']['charset'] = $charset;
    }
    function getSubTitle()
    {
        return $this->SubTitle;
    }
    function setSubTitle($SubTitle, $charset = 'iso-8859-1')
    {
        $this->SubTitle = $SubTitle;
        $this->_elements['SubTitle']['charset'] = $charset;
    }
    function getTimeLeft()
    {
        return $this->TimeLeft;
    }
    function setTimeLeft($TimeLeft, $charset = 'iso-8859-1')
    {
        $this->TimeLeft = $TimeLeft;
        $this->_elements['TimeLeft']['charset'] = $charset;
    }
    function getTitle()
    {
        return $this->Title;
    }
    function setTitle($Title, $charset = 'iso-8859-1')
    {
        $this->Title = $Title;
        $this->_elements['Title']['charset'] = $charset;
    }
    function getUUID()
    {
        return $this->UUID;
    }
    function setUUID($UUID, $charset = 'iso-8859-1')
    {
        $this->UUID = $UUID;
        $this->_elements['UUID']['charset'] = $charset;
    }
    function getVATDetails()
    {
        return $this->VATDetails;
    }
    function setVATDetails($VATDetails, $charset = 'iso-8859-1')
    {
        $this->VATDetails = $VATDetails;
        $this->_elements['VATDetails']['charset'] = $charset;
    }
    function getVendorHostedPicture()
    {
        return $this->VendorHostedPicture;
    }
    function setVendorHostedPicture($VendorHostedPicture, $charset = 'iso-8859-1')
    {
        $this->VendorHostedPicture = $VendorHostedPicture;
        $this->_elements['VendorHostedPicture']['charset'] = $charset;
    }
}
