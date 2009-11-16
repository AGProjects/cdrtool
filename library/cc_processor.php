<?php

/*
 * Application developed to support credit card processing
 * using the PayPal Payflow Pro API
 * Author: Andrew Madison <andrew@ag-projects.com>
 * Date: 2009-11-09
 * Version: 1.0
 */

session_start();

// currency format
setlocale(LC_MONETARY, 'en_US');

class CreditCardProcessor {
    // default cart items, must be set by external application after instantiating this class
    var $cart_items      = array('service1'=>array('cost'=>10,'description'=>'First Item'),
                                 'service2'=>array('cost'=>20,'description'=>'Second Item')
                                 );

    // html hidden elements that need to be preserved between submits by the application that uses this form
    // must be set by external application after instantiating this class
    var $hidden_elements = '';

    // information about the buyer obtained during login in the application using this class
    // must be set by external application after instantiating this class 
    var $user_account    = '';

    // set this to a css style for displaying the titles
    var $chapter_class   = '';

    // fancy odd rows
    var $odd_row_class   = '';

    // fancy even rows
    var $even_row_class  = '';

    var $notify_merchant = true; // set to send notification about the transaction to the merchant
    var $notify_buyer    = true; // set to send notification about the transaction to the buyer

    var $transaction_data = false; // set after transaction has been sucesfull

    var $billing_name     = ''; // saved after transaction is sucessfull
    var $billing_address  = ''; // saved after transaction is sucessfull

    // nothing should be needed to be changed below this line by the application using this class

    // countries that are in sync with other AG Projects backends
    var $countries=array(
    array("label"=>"Ascension Island",    "value"=>"AC"),
    array("label"=>"Afghanistan",        "value"=>"AF"),
    array("label"=>"Albania",        "value"=>"AL"),
    array("label"=>"Algeria",        "value"=>"DZ"),
    array("label"=>"American Samoa",    "value"=>"AS"),
    array("label"=>"Andorra",        "value"=>"AD"),
    array("label"=>"Angola",        "value"=>"AO"),
    array("label"=>"Anguilla",        "value"=>"AI"),
    array("label"=>"Antarctica",        "value"=>"AQ"),
    array("label"=>"Antigua And Barbuda",    "value"=>"AG"),
    array("label"=>"Argentina",        "value"=>"AR"),
    array("label"=>"Armenia",        "value"=>"AM"),
    array("label"=>"Aruba",            "value"=>"AW"),
    array("label"=>"Australia",        "value"=>"AU"),
    array("label"=>"Austria",        "value"=>"AT"),
    array("label"=>"Azerbaijan",        "value"=>"AZ"),
    array("label"=>"Bahamas",        "value"=>"BS"),
    array("label"=>"Bahrain",        "value"=>"BH"),
    array("label"=>"Bangladesh",            "value"=>"BD"),
    array("label"=>"Barbados",            "value"=>"BB"),
    array("label"=>"Belarus",            "value"=>"BY"),
    array("label"=>"Belgium",            "value"=>"BE"),
    array("label"=>"Belize",            "value"=>"BZ"),
    array("label"=>"Benin",                "value"=>"BJ"),
    array("label"=>"Bermuda",            "value"=>"BM"),
    array("label"=>"Bhutan",            "value"=>"BT"),
    array("label"=>"Bolivia",            "value"=>"BO"),
    array("label"=>"Bosnia And Herzegowina","value"=>"BA"),
    array("label"=>"Botswana",        "value"=>"BW"),
    array("label"=>"Bouvet Island",        "value"=>"BV"),
    array("label"=>"Brazil",        "value"=>"BR"),
    array("label"=>"British Indian Ocean Territory",    "value"=>"IO"),
    array("label"=>"Brunei Darussalam",    "value"=>"BN"),
    array("label"=>"Bulgaria",            "value"=>"BG"),
    array("label"=>"Burkina Faso",            "value"=>"BF"),
    array("label"=>"Burundi",            "value"=>"BI"),
    array("label"=>"Cambodia",            "value"=>"KH"),
    array("label"=>"Cameroon",            "value"=>"CM"),
    array("label"=>"Canada",            "value"=>"CA"),
    array("label"=>"Cape Verde",            "value"=>"CV"),
    array("label"=>"Cayman Islands",        "value"=>"KY"),
    array("label"=>"Central African Republic",    "value"=>"CF"),
    array("label"=>"Chad",            "value"=>"TD"),
    array("label"=>"Chile",            "value"=>"CL"),
    array("label"=>"China",            "value"=>"CN"),
    array("label"=>"Christmas Island",    "value"=>"CX"),
    array("label"=>"Cocos (Keeling) Islands",    "value"=>"CC"),
    array("label"=>"Colombia",        "value"=>"CO"),
    array("label"=>"Comoros",        "value"=>"KM"),
    array("label"=>"Congo",            "value"=>"CG"),
    array("label"=>"Congo, Democratic People's Republic",    "value"=>"CD"),
    array("label"=>"Cook Islands",         "value"=>"CK"),
    array("label"=>"Costa Rica",        "value"=>"CR"),
    array("label"=>"Cote d'Ivoire",        "value"=>"CI"),
    array("label"=>"Croatia (local name: Hrvatska)",    "value"=>"HR"),
    array("label"=>"Cuba",        "value"=>"CU"),
    array("label"=>"Cyprus",    "value"=>"CY"),
    array("label"=>"Czech Republic","value"=>"CZ"),
    array("label"=>"Denmark",    "value"=>"DK"),
    array("label"=>"Djibouti",    "value"=>"DJ"),
    array("label"=>"Dominica",    "value"=>"DM"),
    array("label"=>"Dominican Republic",    "value"=>"DO"),
    array("label"=>"East Timor",    "value"=>"TP"),
    array("label"=>"Ecuador",    "value"=>"EC"),
    array("label"=>"Egypt",        "value"=>"EG"),
    array("label"=>"El Salvador",    "value"=>"SV"),
    array("label"=>"Equatorial Guinea",    "value"=>"GQ"),
    array("label"=>"Eritrea",    "value"=>"ER"),
    array("label"=>"Estonia",    "value"=>"EE"),
    array("label"=>"Ethiopia",    "value"=>"ET"),
    array("label"=>"Falkland Islands (Malvinas)",    "value"=>"FK"),
    array("label"=>"Faroe Islands",    "value"=>"FO"),
    array("label"=>"Fiji",        "value"=>"FJ"),
    array("label"=>"Finland",    "value"=>"FI"),
    array("label"=>"France",    "value"=>"FR"),
    array("label"=>"French Guiana",    "value"=>"GF"),
    array("label"=>"French Polynesia",    "value"=>"PF"),
    array("label"=>"French Southern Territories",    "value"=>"TF"),
    array("label"=>"Gabon",        "value"=>"GA"),
    array("label"=>"Gambia",    "value"=>"GM"),
    array("label"=>"Georgia",    "value"=>"GE"),
    array("label"=>"Germany",    "value"=>"DE"),
    array("label"=>"Ghana",    "value"=>"GH"),
    array("label"=>"Gibraltar",    "value"=>"GI"),
    array("label"=>"Greece",    "value"=>"GR"),
    array("label"=>"Greenland",    "value"=>"GL"),
    array("label"=>"Grenada",    "value"=>"GD"),
    array("label"=>"Guadeloupe",    "value"=>"GP"),
    array("label"=>"Guam",    "value"=>"GU"),
    array("label"=>"Guatemala",    "value"=>"GT"),
    array("label"=>"Guernsey",    "value"=>"GG"),
    array("label"=>"Guinea",    "value"=>"GN"),
    array("label"=>"Guinea-Bissau",    "value"=>"GW"),
    array("label"=>"Guyana",    "value"=>"GY"),
    array("label"=>"Haiti",    "value"=>"HT"),
    array("label"=>"Heard And Mc Donald Islands",    "value"=>"HM"),
    array("label"=>"Honduras",    "value"=>"HN"),
    array("label"=>"Hong Kong",    "value"=>"HK"),
    array("label"=>"Hungary",    "value"=>"HU"),
    array("label"=>"Iceland",    "value"=>"IS"),
    array("label"=>"India",    "value"=>"IN"),
    array("label"=>"Indonesia",    "value"=>"ID"),
    array("label"=>"Iran (Islamic Republic Of)",    "value"=>"IR"),
    array("label"=>"Iraq",    "value"=>"IQ"),
    array("label"=>"Ireland",    "value"=>"IE"),
    array("label"=>"Isle of Man",    "value"=>"IM"),
    array("label"=>"Israel",    "value"=>"IL"),
    array("label"=>"Italy",    "value"=>"IT"),
    array("label"=>"Jamaica",    "value"=>"JM"),
    array("label"=>"Japan",    "value"=>"JP"),
    array("label"=>"Jersey",    "value"=>"JE"),
    array("label"=>"Jordan",    "value"=>"JO"),
    array("label"=>"Kazakhstan",    "value"=>"KZ"),
    array("label"=>"Kenya",    "value"=>"KE"),
    array("label"=>"Kiribati",    "value"=>"KI"),
    array("label"=>"Korea, Democratic People's Republic Of",    "value"=>"KP"),
    array("label"=>"Korea, Republic Of",    "value"=>"KR"),
    array("label"=>"Kuwait",    "value"=>"KW"),
    array("label"=>"Kyrgyzstan",    "value"=>"KG"),
    array("label"=>"Lao People's Democratic Republic",    "value"=>"LA"),
    array("label"=>"Latvia",    "value"=>"LV"),
    array("label"=>"Lebanon",    "value"=>"LB"),
    array("label"=>"Lesotho",    "value"=>"LS"),
    array("label"=>"Liberia",    "value"=>"LR"),
    array("label"=>"Libyan Arab Jamahiriya",    "value"=>"LY"),
    array("label"=>"Liechtenstein",    "value"=>"LI"),
    array("label"=>"Lithuania",    "value"=>"LT"),
    array("label"=>"Luxembourg",    "value"=>"LU"),
    array("label"=>"Macau",    "value"=>"MO"),
    array("label"=>"Macedonia, The Former Yugoslav",    "value"=>"MK"),
    array("label"=>"Of",    "value"=>"Republic"),
    array("label"=>"Madagascar",    "value"=>"MG"),
    array("label"=>"Malawi",    "value"=>"MW"),
    array("label"=>"Malaysia",    "value"=>"MY"),
    array("label"=>"Maldives",    "value"=>"MV"),
    array("label"=>"Mali",    "value"=>"ML"),
    array("label"=>"Malta",    "value"=>"MT"),
    array("label"=>"Marshall Islands",    "value"=>"MH"),
    array("label"=>"Martinique",    "value"=>"MQ"),
    array("label"=>"Mauritania",    "value"=>"MR"),
    array("label"=>"Mauritius",    "value"=>"MU"),
    array("label"=>"Mayotte",    "value"=>"YT"),
    array("label"=>"Mexico",    "value"=>"MX"),
    array("label"=>"Micronesia, Federated States Of",    "value"=>"FM"),
    array("label"=>"Moldova, Republic Of",    "value"=>"MD"),
    array("label"=>"Monaco",    "value"=>"MC"),
    array("label"=>"Mongolia",    "value"=>"MN"),
    array("label"=>"Montserrat",    "value"=>"MS"),
    array("label"=>"Morocco",    "value"=>"MA"),
    array("label"=>"Mozambique",    "value"=>"MZ"),
    array("label"=>"Myanmar",    "value"=>"MM"),
    array("label"=>"Namibia",    "value"=>"NA"),
    array("label"=>"Nauru",    "value"=>"NR"),
    array("label"=>"Nepal",    "value"=>"NP"),
    array("label"=>"Netherlands",    "value"=>"NL"),
    array("label"=>"Netherlands Antilles",    "value"=>"AN"),
    array("label"=>"New Caledonia",    "value"=>"NC"),
    array("label"=>"New Zealand",    "value"=>"NZ"),
    array("label"=>"Nicaragua",    "value"=>"NI"),
    array("label"=>"Niger",    "value"=>"NE"),
    array("label"=>"Nigeria",    "value"=>"NG"),
    array("label"=>"Niue",    "value"=>"NU"),
    array("label"=>"Norfolk Island",    "value"=>"NF"),
    array("label"=>"Northern Mariana Islands",    "value"=>"MP"),
    array("label"=>"Norway",    "value"=>"NO"),
    array("label"=>"Oman",    "value"=>"OM"),
    array("label"=>"Pakistan",    "value"=>"PK"),
    array("label"=>"Palau",    "value"=>"PW"),
    array("label"=>"Palestinian Territories",    "value"=>"PS"),
    array("label"=>"Panama",    "value"=>"PA"),
    array("label"=>"Papua New Guinea",    "value"=>"PG"),
    array("label"=>"Paraguay",    "value"=>"PY"),
    array("label"=>"Peru",    "value"=>"PE"),
    array("label"=>"Philippines",    "value"=>"PH"),
    array("label"=>"Pitcairn",    "value"=>"PN"),
    array("label"=>"Poland",    "value"=>"PL"),
    array("label"=>"Portugal",    "value"=>"PT"),
    array("label"=>"Puerto Rico",    "value"=>"PR"),
    array("label"=>"Qatar",    "value"=>"QA"),
    array("label"=>"Reunion",    "value"=>"RE"),
    array("label"=>"Romania",    "value"=>"RO"),
    array("label"=>"Russian Federation",    "value"=>"RU"),
    array("label"=>"Rwanda",    "value"=>"RW"),
    array("label"=>"Saint Kitts And Nevis",    "value"=>"KN"),
    array("label"=>"Saint Lucia",    "value"=>"LC"),
    array("label"=>"Saint Vincent And The Grenadines",    "value"=>"VC"),
    array("label"=>"Samoa",    "value"=>"WS"),
    array("label"=>"San Marino",    "value"=>"SM"),
    array("label"=>"Sao Tome And Principe",    "value"=>"ST"),
    array("label"=>"Saudi Arabia",    "value"=>"SA"),
    array("label"=>"Senegal",    "value"=>"SN"),
    array("label"=>"Seychelles",    "value"=>"SC"),
    array("label"=>"Sierra Leone",    "value"=>"SL"),
    array("label"=>"Singapore",    "value"=>"SG"),
    array("label"=>"Slovakia (Slovak Republic)",    "value"=>"SK"),
    array("label"=>"Slovenia",    "value"=>"SI"),
    array("label"=>"Solomon Islands",    "value"=>"SB"),
    array("label"=>"Somalia",    "value"=>"SO"),
    array("label"=>"South Africa",    "value"=>"ZA"),
    array("label"=>"South Georgia And South Sandwich",    "value"=>"GS"),
    array("label"=>"Spain",    "value"=>"ES"),
    array("label"=>"Sri Lanka",    "value"=>"LK"),
    array("label"=>"St. Helena",    "value"=>"SH"),
    array("label"=>"St. Pierre And Miquelon",    "value"=>"PM"),
    array("label"=>"Sudan",    "value"=>"SD"),
    array("label"=>"Suriname",    "value"=>"SR"),
    array("label"=>"Svalbard And Jan Mayen Islands",    "value"=>"SJ"),
    array("label"=>"Swaziland",    "value"=>"SZ"),
    array("label"=>"Sweden",    "value"=>"SE"),
    array("label"=>"Switzerland",    "value"=>"CH"),
    array("label"=>"Syrian Arab Republic",    "value"=>"SY"),
    array("label"=>"Taiwan, Province Of China",    "value"=>"TW"),
    array("label"=>"Tajikistan",    "value"=>"TJ"),
    array("label"=>"Tanzania, United Republic Of",    "value"=>"TZ"),
    array("label"=>"Thailand",    "value"=>"TH"),
    array("label"=>"Togo",    "value"=>"TG"),
    array("label"=>"Tokelau",    "value"=>"TK"),
    array("label"=>"Tonga",    "value"=>"TO"),
    array("label"=>"Trinidad And Tobago",    "value"=>"TT"),
    array("label"=>"Tunisia",    "value"=>"TN"),
    array("label"=>"Turkey",    "value"=>"TR"),
    array("label"=>"Turkmenistan",    "value"=>"TM"),
    array("label"=>"Turks And Caicos Islands",    "value"=>"TC"),
    array("label"=>"Tuvalu",    "value"=>"TV"),
    array("label"=>"Uganda",    "value"=>"UG"),
    array("label"=>"Ukraine",    "value"=>"UA"),
    array("label"=>"United Arab Emirates",    "value"=>"AE"),
    array("label"=>"United Kingdom",    "value"=>"UK"),
    array("label"=>"United States",    "value"=>"US"),
    array("label"=>"United States Minor Outlying Islands",    "value"=>"UM"),
    array("label"=>"Uruguay",    "value"=>"UY"),
    array("label"=>"Uzbekistan",    "value"=>"UZ"),
    array("label"=>"Vanuatu",    "value"=>"VU"),
    array("label"=>"Vatican City State (Holy See)",    "value"=>"VA"),
    array("label"=>"Venezuela",    "value"=>"VE"),
    array("label"=>"Viet Nam",    "value"=>"VN"),
    array("label"=>"Virgin Islands (British)",    "value"=>"VG"),
    array("label"=>"Virgin Islands (U.S.)",    "value"=>"VI"),
    array("label"=>"Wallis And Futuna Islands",    "value"=>"WF"),
    array("label"=>"Western Sahara",    "value"=>"EH"),
    array("label"=>"Yemen",    "value"=>"YE"),
    array("label"=>"Yugoslavia",    "value"=>"YU"),
    array("label"=>"Zaire",    "value"=>"ZR"),
    array("label"=>"Zambia",    "value"=>"ZM"),
    array("label"=>"Zimbabwe",    "value"=>"ZW"),
    array("label"=>"Undefined",    "value"=>"N/A")
    );

    public $pp_username;
    public $pricepp_pass;
    public $pp_signature;
    public $transaction_type;
    public $crt_path;
    public $sender_email;
    public $us_states_arr;
    public $can_states_arr;
    public $sql_host;
    public $sql_user;
    public $sql_pw;
    public $sql_db;
    public $aes_enc_pwd;
    
    public function __construct() {
        dprint("Init CreditCardProcessor()");

        // process the ini configuration file
        $app_settings_array = parse_ini_file("/etc/cdrtool/paypal/cc_processor.ini");

        // set the includes directory parameter
        set_include_path(get_include_path() . PATH_SEPARATOR . $app_settings_array['library_path']);

        // include all Paypal library files
        require_once 'PayPal.php';
        require_once 'PayPal/Profile/Handler/Array.php';
        require_once 'PayPal/Profile/API.php';
        require_once 'PayPal/Profile/Handler.php';
        require_once 'PayPal/Type/DoDirectPaymentRequestType.php';
        require_once 'PayPal/Type/DoDirectPaymentRequestDetailsType.php';
        require_once 'PayPal/Type/DoDirectPaymentResponseType.php';
        require_once 'PayPal/Type/GetTransactionDetailsRequestType.php';

        // Add all Paypal data types
        require_once 'PayPal/Type/BasicAmountType.php';
        require_once 'PayPal/Type/PaymentDetailsType.php';
        require_once 'PayPal/Type/AddressType.php';
        require_once 'PayPal/Type/CreditCardDetailsType.php';
        require_once 'PayPal/Type/PayerInfoType.php';
        require_once 'PayPal/Type/PersonNameType.php';
        require_once 'api_form_validators.inc.php';
        require_once 'functions.inc.php';
        require_once 'constants.inc.php';

        // Add logger process file
        //require_once 'PayLogger.php';

        foreach ($this->countries as $_country) {
            $countries_array[$_country['value']]=$_country['label'];
        }

        $us_states_arr = array('AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District Of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois', 'IN'=>'Indiana', 'IA'=>'Iowa',  'KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland', 'MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma', 'OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming');
        $can_states_arr = array('AB'=>'Alberta','BC'=>'British Columbia','MB'=>'Manitoba','NB'=>'New Brunswick','NL'=>'Newfoundland/Labrador','NS'=>'Nova Scotia','NT'=>'Northwest Territories','NU'=>'Nunavut','ON'=>'Ontario','PE'=>'Prince Edward Island','QC'=>'Quebec','SK'=>'Saskatchewan','YT'=>'Yukon');

        // set class variables 
        $this->pp_username = $app_settings_array['pp_username'];
        $this->pricepp_pass = $app_settings_array['pp_pass'];
        $this->pp_signature = $app_settings_array['pp_signature'];
        $this->transaction_type = $app_settings_array['transaction_type'];
        $this->sender_email = $app_settings_array['sender_email'];
        $this->countries_array = $countries_array;
        $this->us_states_arr = $us_states_arr;
        $this->can_states_arr = $can_states_arr;
        $this->user_account = null;
        $this->sql_host = $app_settings_array['sql_host'];
        $this->sql_user = $app_settings_array['sql_user'];
        $this->sql_pw = $app_settings_array['sql_pw'];
        $this->sql_db = $app_settings_array['sql_db'];
        $this->aes_enc_pwd = $app_settings_array['aes_enc_pwd'];
    }
    
    function dbConnection(){
        $mysql = new mysqli($this->sql_host,$this->sql_user,$this->sql_pw,$this->sql_db);
        return $mysql;
    }

    function getTransactionDetails ($tran_id) {
        // retrieves information based on transaction ID
        $tran_data = array();
        $sql_conn = $this->dbConnection();
        try {
            $q_result = mysqli_query($sql_conn, "CALL sproc_cc_get_transaction_details('".$tran_id."')");
            while ($row = mysqli_fetch_array($q_result,MYSQLI_ASSOC)) {
                $tran_data = array_merge($tran_data,$row);
            }
            $q_result->close();
        } catch (Exception $ex) {
            print $ex;
        }
        $sql_conn->close();
        return $tran_data;
    }

    function getTransactionItems ($tran_id) {
        // retrieves list of items purchased based on transaction ID
        $tran_data = array();
        $sql_conn = $this->dbConnection();
        try {
            $q_result = mysqli_query($sql_conn, "CALL sproc_cc_get_transaction_items('".$tran_id."')");
            $i = 0;
            while ($row = mysqli_fetch_array($q_result,MYSQLI_ASSOC)) {
                $tran_data[$i] = array_merge($tran_data,$row);
                $i = $i + 1;
            }
            $q_result->close();
        } catch (Exception $ex) {
            print $ex;
        }
        $sql_conn->close();
        return $tran_data;
    }

    function getPageURL () {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        return $pageURL;
    }
    
    function showSubmitForm () {

        if(count($this->cart_items) > 0) {
            $amt = 0;

            foreach($this->cart_items as $item_array => $item_details){
                $amt = $amt+$item_details['cost'];
            }

            $amt_currency = money_format('%i', $amt);
            
            // javascript functions in header
            $page_head_objects = "";
    
            $page_body_content = "";
    
            $page_body_content.= "<script language=\"JavaScript\" src=\"ccp_java.js\" type=\"text/javascript\"></script>\n";
    
            $page_body_content .= "<script type = \"text/javascript\">\n";
            $page_body_content .= "function changeAmount(frm) {\n";
            $page_body_content .= "var amt_purchase = document.getElementById('amt_purchase');\n";
            //$page_body_content .= "var id = frm.options[frm.selectedIndex].value;\n";
            //$page_body_content .= "var split_vars = id.split(\"|\");\n";
            //$page_body_content .= "amt_purchase.innerHTML = '' + split_vars[1] + ' USD <input type=\"hidden\" name=\"amount\" value=\"' + split_vars[1] + '\"><input type=\"hidden\" name=\"item\" value=\"' + split_vars[0] + '\">';\n";
            $page_body_content .= "amt_purchase.innerHTML = '".$amt_currency."<input type=\"hidden\" name=\"amount\" value=\"".$amt."\">';\n";
            $page_body_content .= "}\n";
    
            $page_body_content .= "function changeStates(frm) {\n";
            $page_body_content .= "var states_list = document.getElementById('states_list');\n";
            $page_body_content .= "var id = frm.options[frm.selectedIndex].value;\n";
    
            $page_body_content .= "if(id == 'CA'){\n";
            // Canada States
            $states_arr = $this->can_states_arr;
            $str .= "<select name=\"state\">";
            foreach($states_arr as $state_abbr => $state_name){
                if($state_abbr == $this->user_account['State']){
                    $str .= "<option value=\"".$state_abbr."\" selected>".$state_name."</option>";
                }else{
                    $str .= "<option value=\"".$state_abbr."\">".$state_name."</option>";
                }     
            }
            $str .= "</select>";
            $page_body_content .= "states_list.innerHTML = '".$str."';\n";
            $page_body_content .= "}else if(id == 'US'){\n";
            // US States
            $states_arr = $this->us_states_arr;
            $str = "<select name=\"state\">";
            foreach($states_arr as $state_abbr => $state_name){
                if($state_abbr == $this->user_account['State']){
                    $str .= "<option value=\"".$state_abbr."\" selected>".$state_name."</option>";
                }else{
                    $str .= "<option value=\"".$state_abbr."\">".$state_name."</option>";
                }     
            }
            $str .= "</select>";
            $page_body_content .= "states_list.innerHTML = '".$str."';\n";
            $page_body_content .= "}else{\n";
            $page_body_content .= "states_list.innerHTML = '<select name=\"state\"><option value=\"\">N/A</option></select>';\n";
            $page_body_content .= "}\n";
            $page_body_content .= "}\n";
    
            $page_body_content .= "function resetFields() {\n";
            // set labels font color to black
            $page_body_content .= "var lbl_errors = document.getElementById('lbl_errors');\n";
            $page_body_content .= "lbl_errors.innerHTML = '';\n";
            $page_body_content .= "var lbl_fname = document.getElementById('lbl_fname');\n";
            $page_body_content .= "lbl_fname.innerHTML = '<font color=\"#000000\">First Name on Card</font>';\n";
            $page_body_content .= "var lbl_lname = document.getElementById('lbl_lname');\n";
            $page_body_content .= "lbl_lname.innerHTML = '<font color=\"#000000\">Last Name on Card</font>';\n";
            $page_body_content .= "var lbl_email = document.getElementById('lbl_email');\n";
            $page_body_content .= "lbl_email.innerHTML = '<font color=\"#000000\">Email:</font>';\n";
            $page_body_content .= "var lbl_ccnum = document.getElementById('lbl_ccnum');\n";
            $page_body_content .= "lbl_ccnum.innerHTML = '<font color=\"#000000\">Card Number</font>';\n";
            $page_body_content .= "var lbl_cvn = document.getElementById('lbl_cvn');\n";
            $page_body_content .= "lbl_cvn.innerHTML = '<font color=\"#000000\">Card Verification Number</font>';\n";
            $page_body_content .= "var lbl_addr1 = document.getElementById('lbl_addr1');\n";
            $page_body_content .= "lbl_addr1.innerHTML = '<font color=\"#000000\">Address</font>';\n";
            $page_body_content .= "var lbl_city = document.getElementById('lbl_city');\n";
            $page_body_content .= "lbl_city.innerHTML = '<font color=\"#000000\">City</font>';\n";
            $page_body_content .= "var lbl_postcode = document.getElementById('lbl_postcode');\n";
            $page_body_content .= "lbl_postcode.innerHTML = '<font color=\"#000000\">Postal Code</font>';\n";
            $page_body_content .= "var amt_purchase = document.getElementById('amt_purchase');\n";
            //$page_body_content .= "amt_purchase.innerHTML = '".$this->cart_items[0]['cost']." USD <input type=\"hidden\" name=\"amount\" value=\"".$this->cart_items[0]['cost']."\"><input type=\"hidden\" name=\"item\" value=\"".$this->cart_items[0]."\">';\n";
            $page_body_content .= "amt_purchase.innerHTML = '".$amt_currency."<input type=\"hidden\" name=\"amount\" value=\"".$amt."\">';\n";
            $page_body_content .= "var states_list = document.getElementById('states_list');\n";
            $page_body_content .= "if('".$this->user_account['Country']."' == 'CA'){\n";
            // Canada States
            $states_arr = $this->can_states_arr;
            $str1 .= "<select name=\"state\">";
            foreach($states_arr as $state_abbr => $state_name){
                if($state_abbr == $this->user_account['State']){
                    $str1 .= "<option value=\"".$state_abbr."\" selected>".$state_name."</option>";
                }else{
                    $str1 .= "<option value=\"".$state_abbr."\">".$state_name."</option>";
                }     
            }
            $str1 .= "</select>";
            $page_body_content .= "states_list.innerHTML = '".$str1."';\n";
            $page_body_content .= "}else if('".$this->user_account['Country']."' == 'US'){\n";
            // US States
            $states_arr = $this->us_states_arr;
            $str2 = "<select name=\"state\">";
            foreach($states_arr as $state_abbr => $state_name){
                if($state_abbr == $this->user_account['State']){
                    $str2 .= "<option value=\"".$state_abbr."\" selected>".$state_name."</option>";
                }else{
                    $str2 .= "<option value=\"".$state_abbr."\">".$state_name."</option>";
                }     
            }
            $str2 .= "</select>";
            $page_body_content .= "states_list.innerHTML = '".$str2."';\n";
            $page_body_content .= "}else{\n";
            $page_body_content .= "states_list.innerHTML = '<select name=\"state\"><option value=\"\">N/A</option></select>';\n";
            $page_body_content .= "}\n";
            $page_body_content .= "}\n";
            $page_body_content .= "</script>\n";

            $page_body_content .= '<body onload="javascript:resetFields();" marginwidth=15 leftmargin=15 link=#000066>';

            $page_body_content .= "<form method=\"POST\" name=\"agpay_frm\" id=\"agpay_frm\" onsubmit=\"return agpay_frm_validator(this)\" >\n";
            $page_body_content .= "<table width=100%>\n";
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td colspan=\"2\" class=%s><b>%s</b></td>\n",$this->chapter_class,_("Shopping Cart"));
            $page_body_content .= "</tr>\n";
    
            /*
            // decided to display all items in the shopping cart as a list rather than a 
            // dropdown menu
            $page_body_content .= "<select name=\"item_purchase\" onChange=\"changeAmount(this)\">\n";
            foreach($this->cart_items as $item_array => $item_details){
                $page_body_content .=  "<option value=\"".$item_array."|".$item_details['cost']."\">".$item_details['description']."</option>\n";
            }
            $page_body_content .= "</select>\n";
            */
    
            $t=0;
            foreach($this->cart_items as $item_array => $item_details) {
                $t++;
    
                $rr=floor($t/2);
                $mod=$t-$rr*2;
        
                if ($mod ==0) {
                    $_class=$this->odd_row_class;
                } else {
                    $_class=$this->even_row_class;
                }
    
                $page_body_content .= "<tr class=".$_class.">
                <input type=\"hidden\" name=\"cart_item[]\" value=\"".$item_array."\">
                    <input type=\"hidden\" name=\"cart_item_price[]\" value=\"".$item_details['cost']."\">".
                "<td>".$item_details['description']."</td>".
                "<td>".money_format('%i', $item_details['cost'])."</td></tr>\n";
            }

            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_odd_class);
            $page_body_content .= sprintf("<td><b>%s</b></td>\n",_("Total Due"));
            $page_body_content .= "<td><div id=\"amt_purchase\"></div></td>\n";
            $page_body_content .= "</tr>\n";

            $page_body_content .= "<tr>\n";
            $page_body_content .= "<td colspan=\"2\"><div id=\"lbl_errors\"></div></td>\n";
            $page_body_content .= "</tr>\n";
    
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td colspan=\"2\" class=%s><b>%s</b></td>\n",$this->chapter_class,_("Credit Card Details"));
            $page_body_content .= "</tr>\n";
            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td>%s</td>\n",_("Card Type"));
            $page_body_content .= "<td>\n";
            $page_body_content .= "<select name=\"creditCardType\">\n";
            $page_body_content .= "<option value=\"Visa\" selected>Visa</option>\n";
            $page_body_content .= "<option value=\"MasterCard\">MasterCard</option>\n";
            $page_body_content .= "<option value=\"Discover\">Discover</option>\n";
            $page_body_content .= "<option value=\"Amex\">American Express</option>\n";
            $page_body_content .= "</select>\n";
            $page_body_content .= "</td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td>%s</td>\n",_("<div id=\"lbl_ccnum\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"30\" maxlength=\"19\" name=\"creditCardNumber\"></td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td>%s</td>\n",_("Expiration Date"));
            $page_body_content .= "<td>";
            $page_body_content .= "<select name=\"expDateMonth\">\n";
            for ($m = 1; $m <= 12; $m++) {
                if ($m == 11) {
                    $page_body_content .= "<option value=\"".$m."\" selected>".date("F", mktime(0, 0, 0, $m+1, 0, 0, 0))."</option>\n";
                } else {
                    $page_body_content .= "<option value=\"".$m."\">".date("F", mktime(0, 0, 0, $m+1, 0, 0, 0))."</option>\n";
                }
            }
            $page_body_content .= "</select>\n";
            $page_body_content .= "<select name=\"expDateYear\">\n";
            $cur_year = date('Y');
            $years_out = 10;
            $max_year = $cur_year + $years_out;
            for ($y = $cur_year-1; $y <= $max_year; $y++){
                if($cur_year == $y){
                    $page_body_content .= "<option value=\"".$y."\" selected>".$y."</option>\n";
                }else{
                    $page_body_content .= "<option value=\"".$y."\">".$y."</option>\n";
                }    
            }
            $page_body_content .= "</select>\n";
            $page_body_content .= "</td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td>%s</td>\n",_("<div id=\"lbl_cvn\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"3\" maxlength=\"4\" name=\"cvv2Number\" value=\"\"></td>\n";
            $page_body_content .= "</tr>\n";

            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td colspan=\"2\" class=%s><b>%s</b></td>\n",$this->chapter_class,_("Card Holder Information"));
            $page_body_content .= "</tr>\n";
            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td>%s</td>\n",_("<div id=\"lbl_fname\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"30\" maxlength=\"32\" name=\"firstName\" value=\"".$this->user_account['FirstName']."\"></td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf( "<td>%s</td>\n",_("<div id=\"lbl_lname\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"30\" maxlength=\"32\" name=\"lastName\" value=\"".$this->user_account['LastName']."\"></td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td>%s</td>\n",_("<div id=\"lbl_email\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"30\" maxlength=\"50\" name=\"emailAddress\" value=\"".$this->user_account['Email']."\"></td>\n";
            $page_body_content .= "</tr>\n";


            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td colspan=\"2\" class=%s><b>%s</b></td>\n",$this->chapter_class,_("Billing Address"));
            $page_body_content .= "</tr>\n";
            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td valign=top>%s</td>\n",_("<div id=\"lbl_addr1\"></div>"));
            $page_body_content .= "<td><textarea cols=\"30\" rows=3 maxlength=\"200\" name=\"address1\">".$this->user_account['Address1']."</textarea></td>\n";
            $page_body_content .= "</tr>\n";
    
            /*
            $page_body_content .= "<tr>\n";
            $page_body_content .= "<td>Sta/Apt</td>\n";
            $page_body_content .= "<td><input type=\"text\" size=\"30\" maxlength=\"100\" name=\"address2\" value=\"".$this->user_account['Address2']."\"></td>\n";
            $page_body_content .= "</tr>\n";
            */
    
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td>%s</td>\n",_("<div id=\"lbl_city\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"30\" maxlength=\"40\" name=\"city\" value=\"".$this->user_account['City']."\"></td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td>%s</td>\n",_("Country"));
            $page_body_content .= "<td>\n";
            $page_body_content .= "<select name=\"country\" id=\"country\" onChange=\"changeStates(this)\">\n";
            foreach($this->countries_array as $country_abbr => $country_name){
                if ($this->user_account['Country'] == $country_abbr){
                    $page_body_content .= "<option value=\"".$country_abbr."\" selected>".$country_name."</option>\n";
                } else {
                    $page_body_content .= "<option value=\"".$country_abbr."\">".$country_name."</option>\n";
                }
            }
            $page_body_content .= "</select>\n";
            $page_body_content .= "</td>\n";
            $page_body_content .= "</tr>\n";

            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td>%s</td>\n",_("State"));
            $page_body_content .= "<td><div id=\"states_list\"></div>\n";
            $page_body_content .= "</td>\n";
            $page_body_content .= "</tr>\n";

            $page_body_content .= sprintf ("<tr class=%s>\n",$this->even_row_class);
            $page_body_content .= sprintf("<td>%s</td>\n",_("<div id=\"lbl_postcode\"></div>"));
            $page_body_content .= "<td><input type=\"text\" size=\"8\" maxlength=\"10\" name=\"zip\" value=\"".$this->user_account['PostCode']."\"></td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= "<tr>\n";
            $page_body_content .= sprintf("<td colspan=2><input type=\"submit\" name=\"submit\" value=\"%s\">\n",_("Purchase"));
            $page_body_content .= "<input type=\"reset\" value=\"Reset\"></td>\n";
            $page_body_content .= "</tr>\n";
            $page_body_content .= "</table>\n";
    
            $page_body_content .= $this->hidden_elements;
    
            $page_body_content .= "</form>\n";

            $page_body_close    = "</body></html>";

        } else{
            $page_body_content  = "<html><head></head>";
            $page_body_start    = "<body>";
            $page_body_content  = _("You have no items in your cart. ");
            $page_body_content  .= "<a href=\"javascript:history.go(-1);\">"._("Go Back")."</a>";
            $page_body_close    = "</body></html>";
        }

        $arr_form_page_objects = array(
            'page_head_objects' => $page_head_objects,
            'page_body_start'   => $page_body_start,
            'page_body_content' => $page_body_content,
            'page_body_close'   => $page_body_close
        );
        return $arr_form_page_objects;
    }

    function checkForm ($post_vars) {
        dprint("checkForm()");
        // check server side things related to the submitted form
        $errors = array();
        // check amount
        $amount = 0;
        if($post_vars['amount'] > 0){
            $amount = $post_vars['amount'];
        }else{
            $errors = array_merge($errors,array('amount'=>array('field'=>'Cart Amount','desc'=>_('Amount cannot be zero'))));
        }
        // check first name
        if(strlen(str_replace(" ", "", filter_var($post_vars['firstName'], FILTER_SANITIZE_STRING))) < 2 || is_string($post_vars['firstName']) == false){
            $errors = array_merge($errors,array('firstname'=>array('field'=>'First Name','desc'=>_('Invalid First Name provided'))));
        }
        // check last name
        if(strlen(str_replace(" ", "", filter_var($post_vars['lastName'], FILTER_SANITIZE_STRING))) < 2 || is_string($post_vars['lastName']) == false){
            $errors = array_merge($errors,array('lastname'=>array('field'=>'Last Name','desc'=>_('Invalid Last Name provided'))));
        }
        // check email
        if(strlen(str_replace(" ", "", filter_var($post_vars['emailAddress'], FILTER_SANITIZE_EMAIL))) < 6){
            $errors = array_merge($errors,array('email'=>array('field'=>'Email','desc'=>_('Invalid Email Address provided'))));
        }
        // check card number
        if(strlen(str_replace(" ", "", filter_var($post_vars['creditCardNumber'], FILTER_SANITIZE_NUMBER_INT))) < 16 || $post_vars['creditCardNumber'] == 0){
            $errors = array_merge($errors,array('ccnumber'=>array('field'=>'Card Number','desc'=>_('Invalid Credit Card Number'))));
        }
        // check expiration
        $time_from_exp = mktime(0, 0, 0, $post_vars['expDateMonth'], 31, $post_vars['expDateYear']);
        if(time() > $time_from_exp){
            $errors = array_merge($errors,array('ccexp'=>array('field'=>'Card Expiration','desc'=>_('Invalid Credit Card Expiration Date'))));
        }
        // check card verify code
        if(strlen(filter_var($post_vars['cvv2Number'], FILTER_SANITIZE_NUMBER_INT)) < 3 || $post_vars['cvv2Number'] < 100){
            $errors = array_merge($errors,array('ccvn'=>array('field'=>'Card Verification Number','desc'=>_('Invalid Card Verification Number'))));
        }
        // check address line 1
        if(strlen(str_replace(" ", "", filter_var($post_vars['address1'], FILTER_SANITIZE_STRING))) < 5){
            $errors = array_merge($errors,array('address1'=>array('field'=>'Address','desc'=>_('Invalid Address'))));
        }
        // check city
        if(strlen(str_replace(" ","",$post_vars['city'])) < 2 || !is_string($post_vars['city'])){
            $errors = array_merge($errors,array('city'=>array('field'=>'City','desc'=>_('Invalid City'))));
        }
        return $errors;
    }
    
    function displayProcessErrors($arr_errors){
        dprint("displayProcessErrors()");

        $page_body_content .= "<table>\n";
        $page_body_content .= "<tr><td colspan=\"2\">"._("Error").":</td></tr>\n";
        foreach($arr_errors as $arr_error => $error_desc){
            $page_body_content .= "<tr><td>".$error_desc['field'].":</td><td>".$error_desc['desc']."</td></tr>\n";
        }
        $page_body_content .= "<tr><td colspan=\"2\"><a href=\"javascript:history.go(-1);\">"._("Go Back")."</a>, "._("correct the errors and re-submit. ")."</td></tr>\n";
        $page_body_content .= "</table>\n";
        return $page_body_content;
    }

    function processPayment () {
        dprint("processPayment()");
        // return sucess and set relevant data from the transaction to variables belonging to the class
        $errors = array();
        $pid = ProfileHandler::generateID();
        $handler = & ProfileHandler_Array::getInstance(array(
            'username' => $this->pp_username,
            'certificateFile' => null,
            'subject' => null,
            'environment' => ENVIRONMENT ));

        $profile = & new APIProfile($pid, $handler);
        $profile->setAPIUsername($this->pp_username);
        $profile->setAPIPassword($this->pricepp_pass);
        $profile->setSignature($this->pp_signature); 
        $profile->setCertificateFile(null);
        $profile->setEnvironment(ENVIRONMENT); 
        $dp_request =& PayPal::getType('DoDirectPaymentRequestType');
        $paymentType = $this->transaction_type;

        $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
        $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
        $emailAddress = filter_var($_POST['emailAddress'], FILTER_SANITIZE_EMAIL);
        $creditCardType = filter_var($_POST['creditCardType'], FILTER_SANITIZE_STRING);
        $creditCardNumber = filter_var($_POST['creditCardNumber'], FILTER_SANITIZE_NUMBER_INT);
        $expDateMonth = filter_var($_POST['expDateMonth'], FILTER_SANITIZE_NUMBER_INT);
        // Month must be padded with leading zero
        $padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
        $expDateYear = filter_var($_POST['expDateYear'], FILTER_SANITIZE_NUMBER_INT);
        $cvv2Number = filter_var($_POST['cvv2Number'], FILTER_SANITIZE_NUMBER_INT);
        $address1 = filter_var($_POST['address1'], FILTER_SANITIZE_STRING);
        $address2 = filter_var($_POST['address2'], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
        $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
        $zip = filter_var($_POST['zip'], FILTER_SANITIZE_STRING);
        $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
        $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_INT);

        // Populate SOAP request information
        // Payment details
        $OrderTotal =& PayPal::getType('BasicAmountType');
        $OrderTotal->setattr('currencyID', 'USD');
        $OrderTotal->setval($amount, 'iso-8859-1');
        $PaymentDetails =& PayPal::getType('PaymentDetailsType');
        $PaymentDetails->setOrderTotal($OrderTotal);
        
        $shipTo =& PayPal::getType('AddressType');
        $shipTo->setName($firstName.' '.$lastName);
        $shipTo->setStreet1($address1);
        $shipTo->setStreet2($address2);
        $shipTo->setCityName($city);
        $shipTo->setStateOrProvince($state);
        $shipTo->setCountry($country);
        $shipTo->setPostalCode($zip);
        $PaymentDetails->setShipToAddress($shipTo);
        
        $dp_details =& PayPal::getType('DoDirectPaymentRequestDetailsType');
        $dp_details->setPaymentDetails($PaymentDetails);
        
        // Credit Card info
        $card_details =& PayPal::getType('CreditCardDetailsType');
        $card_details->setCreditCardType($creditCardType);
        $card_details->setCreditCardNumber($creditCardNumber);
        $card_details->setExpMonth($padDateMonth);
        $card_details->setExpYear($expDateYear);
        $card_details->setCVV2($cvv2Number);
        
        $payer =& PayPal::getType('PayerInfoType');
        $person_name =& PayPal::getType('PersonNameType');
        $person_name->setFirstName($firstName);
        $person_name->setLastName($lastName);
        $payer->setPayerName($person_name);
        
        $payer->setPayerCountry($country);
        $payer->setAddress($shipTo);
        
        $card_details->setCardOwner($payer);
        
        $dp_details->setCreditCard($card_details);
        $dp_details->setIPAddress($_SERVER['SERVER_ADDR']);
        $dp_details->setPaymentAction($paymentType);
        
        $dp_request->setDoDirectPaymentRequestDetails($dp_details);
        
        $caller =& PayPal::getCallerServices($profile);

        // Execute SOAP request
        $response = $caller->DoDirectPayment($dp_request);
        $ack = $response->getAck();
        $pp_return = array();
        if($ack == "Success"){
            $pp_return = array('success'=>array('field'=>'Card Processing','desc'=>$response));
        }else{
            $pp_return = array('error'=>array('field'=>'Card Processing','desc'=>$response->Errors->LongMessage));
        }
        return $pp_return;
    }

    function saveOrder ($form_data, $payment_results) {
        dprint("saveOrder()");
        // save order information in a database, etc
        $_TransactionNum = $payment_results['success']['desc']->TransactionID;
        $amt_obj = $payment_results['success']['desc']->getAmount();
        $amt = $amt_obj->_value;
        $currency_cd = $amt_obj->_attributeValues['currencyID'];
        $_TotalAmount = $amt;
        $_Currency = $currency_cd;
        $_AVSCode = $payment_results['success']['desc']->AVSCode;
        $_CVV2Code = $payment_results['success']['desc']->CVV2Code;
        $_PendingReason = $payment_results['success']['desc']->PendingReason;
        $_PaymentStatus = $payment_results['success']['desc']->PaymentStatus;
        $_FMFDetails = $payment_results['success']['desc']->FMFDetails;
        $_ThreeDSecureResponse = $payment_results['success']['desc']->ThreeDSecureResponse;
        $_APITimestamp = $payment_results['success']['desc']->Timestamp;
        $_AckResponse = $payment_results['success']['desc']->Ack;
        $_CorrelationID = $payment_results['success']['desc']->CorrelationID;
        $_Errors = $payment_results['success']['desc']->Errors;
        $_AES_ENC_PWD = $this->aes_enc_pwd;        
        $_FirstName = filter_var($form_data['firstName'], FILTER_SANITIZE_STRING);
        $_LastName = filter_var($form_data['lastName'], FILTER_SANITIZE_STRING);
        $_UserAcct = $_SESSION['login'];    // change this with actual account ientifier session
        $_Email = filter_var($form_data['emailAddress'], FILTER_SANITIZE_EMAIL);;
        $_CCType = filter_var($form_data['creditCardType'], FILTER_SANITIZE_STRING);
        $_CCNum = filter_var($form_data['creditCardNumber'], FILTER_SANITIZE_NUMBER_INT);
        $_CCLast = substr($_CCNum,-4);
        $_CCVCode = filter_var($form_data['cvv2Number'], FILTER_SANITIZE_NUMBER_INT);
        $_CCExpMonth = filter_var($form_data['expDateMonth'], FILTER_SANITIZE_NUMBER_INT);
        $_CCExpYear = filter_var($form_data['expDateYear'], FILTER_SANITIZE_NUMBER_INT);
        $_BillingAddress1 = filter_var($form_data['address1'], FILTER_SANITIZE_STRING);
        $_BillingAddress2 = filter_var($form_data['address2'], FILTER_SANITIZE_STRING);
        $_BillingCity = filter_var($form_data['city'], FILTER_SANITIZE_STRING);
        $_BillingState = filter_var($form_data['state'], FILTER_SANITIZE_STRING);
        $_BillingPostalCode = filter_var($form_data['zip'], FILTER_SANITIZE_STRING);
        $_BillingCountry = filter_var($form_data['country'], FILTER_SANITIZE_STRING);
        $_RequesterIP = $_SERVER['REMOTE_ADDR'];
        $_RequesterSID = session_id();

        // save billing name and address for later use
        $this->billing_name     = $_FirstName.' '.$_LastName;

        $this->billing_address  = $_BillingAddress1."\n";
        if ($_BillingAddress2) {
            $this->billing_address .= $_BillingAddress2."\n";
        }

        $this->billing_address .= $_BillingPostalCode.', '.$_BillingCity."\n";
        $this->billing_address .= $_BillingState.', '.$_BillingCountry;

        $sql_conn = $this->dbConnection();
        // insert transaction information
        try{
            mysqli_query($sql_conn, "CALL sproc_cc_add_transaction(
                '".$_TransactionNum."', '".$_TotalAmount."', '".$_Currency."', '".$_AVSCode."', '".$_CVV2Code."',
                '".$_PendingReason."', '".$_PaymentStatus."', '".$_FMFDetails."', '".$_ThreeDSecureResponse."',
                '".$_APITimestamp."', '".$_AckResponse."', '".$_CorrelationID."', '".$_Errors."', '".$_AES_ENC_PWD."',
                '".$_FirstName."', '".$_LastName."', '".$_UserAcct."', '".$_Email."', '".$_CCType."', '".$_CCNum."', '".$_CCLast."',
                '".$_CCVCode."', '".$_CCExpMonth."', '".$_CCExpYear."', '".$_BillingAddress1."', '".$_BillingAddress2."',
                '".$_BillingCity."', '".$_BillingState."', '".$_BillingPostalCode."', '".$_BillingCountry."',
                '".$_RequesterIP."', '".$_RequesterSID."'
            )");
        } catch (Exception $ex) {
            print $ex;
        }
        // insert item purchase information
        foreach ($form_data['cart_item'] as $cart_item_key => $service_id){
            try{
                mysqli_query($sql_conn, "CALL sproc_cc_add_purchase_items(
                    '".$_TransactionNum."', '".$service_id."', '".$form_data['cart_item_price'][$cart_item_key]."', '".$_Currency."'
                )");
            }catch (Exception $ex){
                print $ex;
            }
        }

        $sql_conn->close();

        $this->saveOrderExternal($form_data, $payment_results);

        $this->transaction_data = $this->getTransactionDetails($_TransactionNum);

        $this->notifyMerchant();
        $this->notifyMerchant();

        return $_TransactionNum;
    }

    function saveOrderExternal($form_data, $payment_results) {
        // save to additional databases ...
    }

    function deliverMerchandise ($transaction_data) {
        dprint ("deliverMerchandise()");
        //dprint_r($transaction_data);
        // use this information as needed
        // information is in the following format:
        /*
        array(14) {
          ["TRANSACTION_ID"]=>
          string(17) "6J581384EM0001112"
          ["TOTAL_AMOUNT"]=>
          string(5) "30.00"
          ["CURRENCY"]=>
          string(3) "USD"
          ["PURCHASE_TIMESTAMP"]=>
          string(19) "2009-11-11 21:50:40"
          ["FIRST_NAME"]=>
          string(6) "Andrew"
          ["LAST_NAME"]=>
          string(7) "Madison"
          ["AG_USER_ACCOUNT"]=>
          string(0) ""
          ["USER_EMAIL"]=>
          string(18) "uskratos@gmail.com"
          ["ADDRESS1"]=>
          string(21) "3619 E. Long Lake Rd."
          ["ADDRESS2"]=>
          string(0) ""
          ["CITY"]=>
          string(7) "Phoenix"
          ["STATE"]=>
          string(2) "AZ"
          ["POSTCODE"]=>
          string(5) "85048"
          ["COUNTRY"]=>
          string(2) "US"
        }
        */
    }

    function notifyBuyer () {
        dprint ("notifyBuyer()");

        if (!$this->notify_buyer) return true;
        if (!is_array($this->transaction_data)) return false;

        // send email notifications to te customer etc...
        $items_purchase_list = $this->getTransactionItems($this->transaction_data['TRANSACTION_ID']);
        $msg = "Dear ".$this->transaction_data['FIRST_NAME']." ".$this->transaction_data['LAST_NAME'].",\n\n";
        $msg .= "This message is to confirm that on ".$this->transaction_data['PURCHASE_TIMESTAMP']." ";
        $msg .= "you purchased from AG Projects services in amount of ".$this->transaction_data['TOTAL_AMOUNT']." ".$this->transaction_data['CURRENCY'].".\n\n";
        $msg .= "Services Purchased:\n\n";
        foreach($items_purchase_list as $item_purchase){
            $msg .= "Service: ".$item_purchase['ITEM_NAME']." Price: ".$item_purchase['AMOUNT']." ".$item_purchase['CURRENCY']."\n";
        }
        $msg .= "\n";
        $msg .= "Your account is credited and you can use your available credit immediately. If you ";
        $msg .= "do not recognize this charge, please contact us at ".$this->sender_email." with Transaction ID ".$this->transaction_data['TRANSACTION_ID']." as reference.\n\n";
        $msg .= "Thank you for your purchase!\n\n";
        $msg .= "AG Projects";

        return $this->sendEmail($this->sender_email,$this->transaction_data['USER_EMAIL'],'AG Projects Purchase Notice',$msg);
    }

    function notifyMerchant () {
        dprint ("notifyMerchant()");

        if (!$this->notify_merchant) return true;
        if (!is_array($this->transaction_data)) return false;

        // send email notifications to AG Projects
        $items_purchase_list = $this->getTransactionItems($this->transaction_data['TRANSACTION_ID']);
        $msg = "New Site Credit Card Transaction:\n";
        $msg .= "Transaction Number: ".$this->transaction_data['TRANSACTION_ID']."\n";
        $msg .= "Amount: ".$this->transaction_data['TOTAL_AMOUNT']." ".$this->transaction_data['CURRENCY']."\n\n";
        $msg .= "Items Purchased:\n\n";
        foreach($items_purchase_list as $item_purchase){
            $msg .= "Item: ".$item_purchase['ITEM_NAME']." Price: ".$item_purchase['AMOUNT']." ".$item_purchase['CURRENCY']."\n";
        }
        $msg .= "\n";
        $msg .= "Purchased On: ".$this->transaction_data['PURCHASE_TIMESTAMP']."\n";
        $msg .= "First Name: ".$this->transaction_data['FIRST_NAME']."\n";
        $msg .= "Last Name: ".$this->transaction_data['LAST_NAME']."\n";
        $msg .= "User Account: ".$this->transaction_data['AG_USER_ACCOUNT']."\n";
        $msg .= "Email: ".$this->transaction_data['USER_EMAIL']."\n";
        $msg .= "Address: ".$this->transaction_data['ADDRESS1']." ".$this->transaction_data['ADDRESS2']."\n";
        $msg .= "City: ".$this->transaction_data['CITY']."\n";
        $msg .= "State: ".$this->transaction_data['STATE']."\n";
        $msg .= "Postal Code: ".$this->transaction_data['POSTCODE']."\n";
        $msg .= "Country: ".$this->transaction_data['COUNTRY']."\n";
        $msg .= "User IP: ".$_SERVER['REMOTE_ADDR']."";

        return $this->sendEmail($this->sender_email,$this->sender_email,'AG Projects Purchase Notice',$msg);
    }

    function sendEmail ($from, $to, $subject, $msg) {
        dprint ("sendEmail()");
        // send email notifications to AG Projects
        $extra = "From: ".$from."" . "\r\n" .
        "Reply-To: ".$from."" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();
        $mail_subject = $subject;
        $mail_body = $msg;
        if (mail($to, $mail_subject, $mail_body, $extra)) {
            return true;
        } else {
            return false;
        }
    }
}

?>
