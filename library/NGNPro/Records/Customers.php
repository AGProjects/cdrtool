<?php

class Customers extends Records
{
    var $children     = array();
    var $showAddForm  = false;

    var $sortElements = array(
        'changeDate'   => 'Change date',
        'username'     => 'Username',
        'firstName'    => 'First name',
        'lastName'     => 'Last name',
        'organization' => 'Organization',
        'customer'     => 'Customer'
    );

    var $propertiesItems = array(
        'sip_credit'          => array(
            'name'      => 'Credit for SIP accounts',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'sip_alias_credit'    => array(
            'name'      => 'Credit for SIP aliases',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'enum_range_credit'   => array(
            'name'      => 'Credit for ENUM ranges',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'enum_number_credit'  => array(
            'name'      => 'Credit for ENUM numbers',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'dns_zone_credit'     => array(
            'name'      => 'Credit for DNS zones',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'email_credit'        => array(
            'name'      => 'Credit for E-mail aliases',
            'category'   => 'credit',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'pstn_access'         => array(
            'name'      => 'Access to PSTN',
            'category'   => 'sip',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'prepaid_changes'      => array(
            'name'      => 'Prepaid Changes',
            'category'   => 'sip',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'pstn_changes'      => array(
            'name'       => 'Pstn Changes',
            'category'   => 'sip',
            'permission' => 'admin',
            'resellerMayManageForChildAccounts' => true
        ),
        'payment_processor_class'      => array(
            'name'       => 'Payment Processor Class',
            'category'   => 'sip',
            'permission' => 'admin'
        ),
        'voicemail_server'      => array(
            'name'       => 'Voicemail Server Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'voicemail_access_number'    => array(
            'name'       => 'Voicemail Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FUNC_access_number'    => array(
            'name'      => 'Forwarding Unconditional Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FNOL_access_number'    => array(
            'name'      => 'Forwarding Not-Online Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FNOA_access_number'    => array(
            'name'      => 'Forwarding Not-Available Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'FBUS_access_number'    => array(
            'name'      => 'Forwarding On Busy Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'change_privacy_access_number' => array(
            'name'      => 'Change privacy Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'check_privacy_access_number' => array(
            'name'      => 'Check privacy Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'reject_anonymous_access_number' => array(
            'name'      => 'Reject anonymous Access Number',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'sip_proxy'           => array(
            'name'      => 'SIP Proxy Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'sip_outbound_proxy'   => array(
            'name'      => 'SIP Client Outbound proxy',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'store_clear_text_passwords' => array(
            'name'      => 'Store clear text passwords',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'xcap_root'           => array(
            'name'      => 'XCAP Root URL',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'absolute_voicemail_uri'=> array(
            'name'    => 'Use Absolute Voicemail Uri',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'dns_admin_email'     => array('name'     => 'DNS zones Administrator Email',
            'category' => 'dns',
            'permission'  => 'customer'),
        'support_web'         => array('name'      => 'Support Web Site',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'support_email'       => array('name'      => 'Support Email Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'billing_email'       => array('name'      => 'Billing Email Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'support_company'     => array('name'      => 'Support Organization',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'cdrtool_address'     => array('name'      => 'CDRTool Address',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'sip_settings_page'   => array('name'      => 'SIP Settings Page',
            'category'   => 'sip',
            'permission' => 'customer'
        ),
        'digest_settings_page' => array('name'     => 'Settings Page (Digest Auth)',
            'category'   => 'sip',
            'permission' => 'reseller'
        ),
        'records_per_page'    => array('name'     => 'Records per page',
            'category'  => 'web',
            'permission'  => 'customer'
        ),
        'push_notifications_server' => array('name'=>'Push server public interface',
            'category' =>'sip',
            'permission' => 'customer'
        ),
        'push_notifications_server_private' => array('name'=>'Push server private interface',
            'category' =>'sip',
            'permission' => 'customer'
        )
    );

    var $FieldsReadOnly = array(
        'id'          => array('type'=>'integer'),
        'reseller'    => array('type'=>'integer')
    );
    var $Fields = array(
        'resellerActive' => array (
            'type'      => 'boolean',
            'name'      => 'Reseller active',
            'adminonly' => true
        ),
        'impersonate'     => array(
            'type'       =>'integer',
            'name'       =>'Impersonate'),
        'companyCode' => array(
            'type'       =>'text',
            'name'       =>'Company code',
            'adminonly'  => true
        ),
        'balance'     => array(
            'type'       => 'float',
            'adminonly'  => true
        ),
        'credit'      => array(
            'type'       => 'float',
            'adminonly'  => true
        ),
        'username'    => array(
            'type'       =>'text', 'extra_html' => 'autocomplete="off"'
        ),
        'password'    => array(
            'type'=>'text',
            'name'=>'Password'),
        'firstName'   => array(
            'type'=>'text',
            'name'=>'First name'),
        'lastName'    => array(
            'type'=>'text',
            'name'=>'Last name'),
        'organization'=> array('type'=>'text'),
        'tel'         => array('type'=>'text'),
        'fax'         => array('type'=>'text'),
        'sip'         => array('type'=>'text'),
        'enum'        => array('type'=>'text'),
        'mobile'      => array('type'=>'text'),
        'email'       => array('type'=>'text'),
        'web'         => array('type'=>'text'),
        'address'     => array('type'=>'textarea'),
        'postcode'    => array('type'=>'text'),
        'city'        => array('type'=>'text'),
        'state'       => array('type'=>'text'),
        'country'     => array('type'=>'text'),
        'timezone'    => array('type'=>'text'),
        'language'    => array('type'=>'text'),
        'vatNumber'   => array(
            'type'=>'text',
            'name'=>'VAT number'
        ),
        'bankAccount' => array(
            'type'=>'text',
            'name'=>'Bank account'
        ),
        'billingEmail' => array(
            'type'=>'text',
            'name'=>'Billing email'
        ),
        'billingAddress' => array(
            'type'=>'textarea',
            'name'=>'Billing address'
        ),
    );

    var $addFields = array(
        'username'    => array(
            'type'       =>'text'
        ),
        'password'    => array(
            'type'=>'text',
            'name'=>'Password'
        ),
        'firstName'   => array(
            'type'=>'text',
            'name'=>'First name'
        ),
        'lastName'    => array(
            'type'=>'text',
            'name'=>'Last name'
        ),
        'organization'=> array('type'=>'text'),
        'tel'         => array('type'=>'text'),
        'email'       => array('type'=>'text'),
        'address'     => array('type'=>'textarea'),
        'postcode'    => array('type'=>'text'),
        'city'        => array('type'=>'text'),
        'state'       => array('type'=>'text'),
        'country'     => array('type'=>'text'),
        'timezone'    => array('type'=>'text')
    );

    var $states = array(
        array("label"=>"", "value"=>"N/A"),
        array("label"=>"-- CANADA --", "value"=>"-"),
        array("label"=>"Alberta", "value"=>"AB"),
        array("label"=>"British Columbia", "value"=>"BC"),
        array("label"=>"Manitoba", "value"=>"MB"),
        array("label"=>"New Brunswick", "value"=>"NB"),
        array("label"=>"Newfoundland/Labrador", "value"=>"NL"),
        array("label"=>"Northwest Territory", "value"=>"NT"),
        array("label"=>"Nova Scotia", "value"=>"NS"),
        array("label"=>"Nunavut", "value"=>"NU"),
        array("label"=>"Ontario", "value"=>"ON"),
        array("label"=>"Prince Edward Island", "value"=>"PE"),
        array("label"=>"Quebec", "value"=>"QC"),
        array("label"=>"Saskatchewan", "value"=>"SN"),
        array("label"=>"Yukon", "value"=>"YT"),
        array("label"=>"---- US -----", "value"=>"-"),
        array("label"=>"Alabama", "value"=>"AL"),
        array("label"=>"Alaska", "value"=>"AK"),
        array("label"=>"American Samoa", "value"=>"AS"),
        array("label"=>"Arizona", "value"=>"AZ"),
        array("label"=>"Arkansas", "value"=>"AR"),
        array("label"=>"California", "value"=>"CA"),
        array("label"=>"Canal Zone", "value"=>"CZ"),
        array("label"=>"Colorado", "value"=>"CO"),
        array("label"=>"Connecticut", "value"=>"CT"),
        array("label"=>"Delaware", "value"=>"DE"),
        array("label"=>"District of Columbia", "value"=>"DC"),
        array("label"=>"Florida", "value"=>"FL"),
        array("label"=>"Georgia", "value"=>"GA"),
        array("label"=>"Guam", "value"=>"GU"),
        array("label"=>"Hawaii", "value"=>"HI"),
        array("label"=>"Idaho", "value"=>"ID"),
        array("label"=>"Illinois", "value"=>"IL"),
        array("label"=>"Indiana", "value"=>"IN"),
        array("label"=>"Iowa", "value"=>"IA"),
        array("label"=>"Kansas", "value"=>"KS"),
        array("label"=>"Kentucky", "value"=>"KY"),
        array("label"=>"Louisiana", "value"=>"LA"),
        array("label"=>"Maine", "value"=>"ME"),
        array("label"=>"Mariana Islands", "value"=>"MP"),
        array("label"=>"Maryland", "value"=>"MD"),
        array("label"=>"Massachusetts", "value"=>"MA"),
        array("label"=>"Michigan", "value"=>"MI"),
        array("label"=>"Minnesota", "value"=>"MN"),
        array("label"=>"Mississippi", "value"=>"MS"),
        array("label"=>"Missouri", "value"=>"MO"),
        array("label"=>"Montana", "value"=>"MT"),
        array("label"=>"Nebraska", "value"=>"NE"),
        array("label"=>"Nevada", "value"=>"NV"),
        array("label"=>"New Hampshire", "value"=>"NH"),
        array("label"=>"New Jersey", "value"=>"NJ"),
        array("label"=>"New Mexico", "value"=>"NM"),
        array("label"=>"New York", "value"=>"NY"),
        array("label"=>"North Carolina", "value"=>"NC"),
        array("label"=>"North Dakota", "value"=>"ND"),
        array("label"=>"Ohio", "value"=>"OH"),
        array("label"=>"Oklahoma", "value"=>"OK"),
        array("label"=>"Oregon", "value"=>"OR"),
        array("label"=>"Pennsylvania", "value"=>"PA"),
        array("label"=>"Puerto Rico", "value"=>"PR"),
        array("label"=>"Rhode Island", "value"=>"RI"),
        array("label"=>"South Carolina", "value"=>"SC"),
        array("label"=>"South Dakota", "value"=>"SD"),
        array("label"=>"Tennessee", "value"=>"TN"),
        array("label"=>"Texas", "value"=>"TX"),
        array("label"=>"Utah", "value"=>"UT"),
        array("label"=>"Vermont", "value"=>"VT"),
        array("label"=>"Virgin Islands", "value"=>"VI"),
        array("label"=>"Virginia", "value"=>"VA"),
        array("label"=>"Washington", "value"=>"WA"),
        array("label"=>"West Virginia", "value"=>"WV"),
        array("label"=>"Wisconsin", "value"=>"WI"),
        array("label"=>"Wyoming", "value"=>"WY"),
        array("label"=>"APO", "value"=>"AP"),
        array("label"=>"AEO", "value"=>"AE"),
        array("label"=>"AAO", "value"=>"AA"),
        array("label"=>"FPO", "value"=>"FP")
    );

    var $countries = array(
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
        array("label"=>"Bosnia And Herzegowina", "value"=>"BA"),
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
        array("label"=>"Czech Republic", "value"=>"CZ"),
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

    var $hide_html = false;

    public function __construct($SoapEngine)
    {
        dprint("init Customers");

        $this->filters   = array(
            'username'       => trim($_REQUEST['username_filter']),
            'firstName'      => trim($_REQUEST['firstName_filter']),
            'lastName'       => trim($_REQUEST['lastName_filter']),
            'organization'   => trim($_REQUEST['organization_filter']),
            'tel'            => trim($_REQUEST['tel_filter']),
            'email'          => htmlspecialchars(trim($_REQUEST['email_filter'])),
            'web'            => trim($_REQUEST['web_filter']),
            'country'        => trim($_REQUEST['country_filter']),
            'city'           => trim($_REQUEST['city_filter']),
            'only_resellers' => trim($_REQUEST['only_resellers_filter'])
        );

        parent::__construct($SoapEngine);

        $this->showAddForm = $_REQUEST['showAddForm'];

        if (is_array($this->SoapEngine->customer_properties)) {
            $this->customer_properties = $this->SoapEngine->customer_properties;
        } else {
            $this->customer_properties = array();
        }

        $this->allProperties = array_merge($this->propertiesItems, $this->customer_properties);
    }

    function showSeachForm()
    {
        printf(
            "<p><b>%s</b>",
            $this->SoapEngine->ports[$this->SoapEngine->port]['description'],
            '%'
        );

        printf("<form class=form-inline method=post name=engines action=%s>", $_SERVER['PHP_SELF']);
        print "
        <div class='well well-small'>
        ";
        print "
        ";
        print "
        <button class='btn btn-primary' type=submit name=action value=Search>Search</button>";

        $this->showEngineSelection();

        print "
        <div class='pull-right'>
        ";
        $this->showSortForm();

        print "
        </div><div style='clear:both' /><br/><div class=input-prepend><span class=\"add-on\">Id</span>";


        $this->showCustomerSelection();
        $this->showResellerSelection();
        print "</div>
        ";

        $this->showSeachFormCustom();

        $this->printHiddenFormElements('skipServiceElement');
        print "</div>
            </div>
        </form>
        ";
    }

    function listRecords()
    {
        // Filter
        $filter = array(
            'username'     => $this->filters['username'],
            'firstName'    => $this->filters['firstName'],
            'lastName'     => $this->filters['lastName'],
            'organization' => $this->filters['organization'],
            'tel'          => $this->filters['tel'],
            'email'        => $this->filters['email'],
            'web'          => $this->filters['web'],
            'city'         => $this->filters['city'],
            'country'      => $this->filters['country'],
            'only_resellers' => $this->filters['only_resellers'],
            'customer'     => intval($this->filters['customer']),
            'reseller'     => intval($this->filters['reseller'])
        );

        //print_r($filter);

        // Range
        $range = array(
            'start' => intval($this->next),
            'count' => intval($this->maxrowsperpage)
        );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array(
            'attribute' => $this->sorting['sortBy'],
            'direction' => $this->sorting['sortOrder']
        );

        // Compose query
        $Query = array(
            'filter'     => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        $this->showSeachForm();

        if ($this->showAddForm) {
            $this->showAddForm();
            return true;
        }

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        if ($this->adminonly && $this->filters['only_resellers']) {
            $this->log_action('getResellers');
            $result = $this->SoapEngine->soapclient->getResellers($Query);
        } else {
            $this->log_action('getCustomers');
            $result = $this->SoapEngine->soapclient->getCustomers($Query);
        }

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            $url_data = array(
                'service' => $this->SoapEngine->service,
                'showAddForm' => 1
            );

            print "
            <div class='alert alert-success'><center>$this->rows records found. Click on the id to edit the account.</center></div>
            ";

            print "
                <div class=\"btn-group\">
            ";

            $_add_url = $this->buildUrl($url_data);
            printf("<a class='btn btn-warning' href=%s>Add new account</a> ", $_add_url);

            if ($this->adminonly) {
                if ($this->adminonly && $this->filters['reseller']) {
                    $url_data['reseller_filter'] = $this->filters['reseller'];
                    $_add_url = $this->buildUrl($url_data);
                    printf(
                        "<a class='btn btn-warning' href=%s>Add a new account for reseller %s</a>",
                        $_add_url,
                        $this->filters['reseller']
                    );
                }
            }
            print "</div>";
            if ($this->rows > 1) {
                print "
                <table class='table table-striped table-condensed' border=0 cellpadding=2 width=100%>
                <thead>
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Impersonate</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Organization</th>
                    <th>Country</th>
                    <th>E-mail</th>
                    <th>Phone number</th>
                    <th>Change date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                ";
            }

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows = $this->rows;
            }

            $i=0;

            if ($this->rows > 1) {
                while ($i < $maxrows) {
                    if (!$result->accounts[$i]) break;

                    $customer = $result->accounts[$i];

                    $index = $this->next+$i+1;

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'reseller_filter' => $customer->reseller,
                        'customer_filter' => $customer->id,
                    );

                    $show_delete = True;
                    foreach ($customer->properties as $_property) {
                        if ($_property->name == "support_order" and $_property->value) {
                            $show_delete = False;
                            break;
                        }
                    }

                    $delete_url_data = array_merge(
                        $base_url_data,
                        array(
                            'action' => 'Delete',
                        )
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['customer_filter'] == $customer->id) {
                        $delete_url_data['confirm'] = 1;
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->buildUrl($delete_url_data);
                    $_customer_url = $this->buildUrl($base_url_data);

                    printf(
                        "
                        <tr>
                        <td>%s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s %s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=mailto:%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>
                        ",
                        $index,
                        $_customer_url,
                        $customer->id,
                        $customer->reseller,
                        $customer->impersonate,
                        strip_tags($customer->username),
                        strip_tags($customer->firstName),
                        strip_tags($customer->lastName),
                        strip_tags($customer->organization),
                        strip_tags($customer->country),
                        strip_tags($customer->email),
                        strip_tags($customer->email),
                        $customer->tel,
                        $customer->changeDate
                    );
                    
                    if ($show_delete) {
                        printf(
                            "
                            <a class='btn-small btn-danger' href=%s>%s</a>
                            ",
                            $_url,
                            $actionText
                        );
                    }

                    $this->showExtraActions($customer);
                    print "</td>
                    </tr>
                    ";

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1) {
                $customer = $result->accounts[0];
                $this->showRecord($customer);
            }


            $this->showPagination($maxrows);

            return true;
        }
    }

    function showSeachFormCustom()
    {
        printf("<div class=input-prepend><span class=\"add-on\">Username</span><input class='span1' type=text name=username_filter value='%s'></div>", $this->filters['username']);
        printf("<div class=input-prepend><span class=\"add-on\">FN</span><input class='span2' type=text name=firstName_filter value='%s'></div>\n", $this->filters['firstName']);
        printf("<div class=input-prepend><span class=\"add-on\">LN</span><input class='span2' type=text name=lastName_filter value='%s'></div>\n", $this->filters['lastName']);
        printf("<div class=input-prepend><span class=\"add-on\">Organization</span><input class='span2' type=text name=organization_filter value='%s'></div>\n", $this->filters['organization']);
        printf("<div class=input-prepend><span class=\"add-on\">Email</span><input class='span2' type=text name=email_filter value='%s'></div>\n", $this->filters['email']);

        if ($this->adminonly) {
            if ($this->filters['only_resellers']) $check_only_resellers_filter='checked';
            printf(" Resellers <input class=checkbox type=checkbox name=only_resellers_filter value=1 %s>", $check_only_resellers_filter);
        }
    }

    function deleteRecord($dictionary = array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['customer']) {
            $customer = $dictionary['customer'];
        } else {
            $customer = $this->filters['customer'];
        }

        if (!strlen($customer)) {
            print "<p><font color=red>Error: missing customer id. </font>";
            return false;
        }

        $function = array('commit'   => array('name'       => 'deleteAccount',
                                            'parameters' => array(intval($customer)),
                                            'logs'       => array('success' => sprintf('Customer id %s has been deleted', $this->filters['customer'])))
                        );
        if ($this->SoapEngine->execute($function, $this->html)) {
            unset($this->filters);
            return true;
        } else {
            return false;
        }
    }

    function getRecord($id)
    {

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccount');
        $result     = $this->SoapEngine->soapclient->getAccount(intval($id));

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return $result;
        }
    }

    function showRecordHeader($customer)
    {
    }

    function showRecordFooter($customer)
    {
    }

    function showExtraActions($customer)
    {
    }

    function showRecord($customer)
    {
        //dprint_r($customer);

        $this->showRecordHeader($customer);

        print "<table border=0 cellpadding=10>";

        printf("<form method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        print "

        <tr>
        <td align=left>";
        if ($_REQUEST['action'] != 'Delete' && $_REQUEST['action'] != 'Copy') {
            print "<input class='btn' type=submit name=action value=Update>";
            printf(" E-mail <input class=checkbox type=checkbox name=notify value='1'> account information");
        }

        print "</td>
        <td align=right>";

        printf("<input type=hidden name=customer_filter value=%s>", $customer->id);

        if ($this->adminonly) {
            printf("<input type=hidden name=reseller_filter value=%s>", $customer->reseller);
        }

        if ($this->adminonly || $this->reseller == $customer->reseller) {
            if ($_REQUEST['action'] != 'Delete') {
                print "<div class='btn-group'><input class='btn' type=submit name=action value=Copy>";
            }

            $show_delete = True;
            foreach ($customer->properties as $_property) {
                if ($_property->name == "support_order" and $_property->value) {
                    $show_delete = False;
                    break;
                }
            }

            if ($show_delete) {
                print "<input class='btn btn-danger'type=submit name=action value=Delete></div>";
            }
            if ($_REQUEST['action'] == 'Delete' || $_REQUEST['action'] == 'Copy') {
                print "<input class='btn btn-warning' type=hidden name=confirm value=1>";
            }
        }

        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <td valign=top>

        <table border=0>
        ";

        printf("<tr bgcolor=lightgrey>
        <td class=border>Property</td>
        <td class=border>Value</td>
        </tr>");
        foreach (array_keys($this->FieldsReadOnly) as $item) {
            printf(
                "
                <tr>
                <td class=border valign=top>%s</td>
                <td class=border>%s</td>
                </tr>
                ",
                ucfirst($item),
                $customer->$item
            );
        }

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name = $this->Fields[$item]['name'];
            } else {
                $item_name = ucfirst($item);
            }

            if ($item=='timezone') {
                printf(
                    "<tr><td class=border valign=top>%s</td>",
                    $item_name
                );
                print "<td class=border>";

                $this->showTimezones($customer->$item);

                print "</td>
                </tr>
                ";
            } elseif ($item=='state') {
                printf(
                    "<tr><td class=border valign=top>%s</td>",
                    $item_name
                );
                print "<td class=border>
                <select name=state_form>";

                $selected_state[$customer->state]='selected';

                foreach ($this->states as $_state) {
                    printf("<option value='%s' %s>%s", $_state['value'], $selected_state[$_state['value']], $_state['label']);
                }

                print "
                </select>
                </td>
                </tr>
                ";
            } elseif ($item=='country') {
                printf(
                    "<tr><td class=border valign=top>%s</td>",
                    $item_name
                );
                print "<td class=border>
                <select name=country_form>";

                $selected_country[$customer->country]='selected';

                foreach ($this->countries as $_country) {
                    printf("<option value='%s' %s>%s", $_country['value'], $selected_country[$_country['value']], $_country['label']);
                }

                print "
                </select>
                </td>
                </tr>
                ";
            } elseif ($item=='resellerActive' && ($customer->reseller != $customer->id)) {
                printf(
                    "<input name=%s_form type=hidden value='%s'>",
                    $item,
                    $customer->$item
                );
            } elseif ($item=='impersonate') {
                if ($customer->reseller != $customer->id) {
                    if ($this->adminonly || $this->customer == $customer->reseller) {
                        printf(
                            "<tr><td class=border valign=top>%s</td>",
                            $item_name
                        );
                        print "<td class=border>      ";
                        $this->getChildren($customer->reseller);
                        if (count($this->children)> 0) {
                            print "
                            <select name=impersonate_form>
                            <option>";
                            $selected_impersonate[$customer->impersonate]='selected';
                            foreach (array_keys($this->children) as $_child) {
                                printf("<option value='%s' %s>%s. %s %s", $_child, $selected_impersonate[$_child], $_child, $this->children[$_child]['firstName'], $this->children[$_child]['lastName']);
                            }

                            print "
                            </select>
                            ";
                        } else {
                            printf(
                                "<input name=%s_form size=30 type=text value='%s'>",
                                $item,
                                $customer->$item
                            );
                        }
                        print "
                        </td>
                        </tr>
                        ";
                    } else {
                        printf(
                            "
                            <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border><input name=%s_form type=hidden value='%s'>%s</td>
                            </tr>
                            ",
                            $item_name,
                            $item,
                            $customer->$item,
                            $customer->$item
                        );
                    }
                } else {
                    printf(
                        "<input name=%s_form type=hidden value='%s'>",
                        $item,
                        $customer->$item
                    );
                }
            } else {
                if ($this->Fields[$item]['type'] == 'textarea') {
                    printf(
                        "
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $customer->$item
                    );
                } elseif ($this->Fields[$item]['type'] == 'boolean') {
                    if ($this->Fields[$item]['adminonly'] && !$this->adminonly) {
                        printf(
                            "
                            <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border><input name=%s_form type=hidden value='%s'>%s</td>
                            </tr>
                            ",
                            $item_name,
                            $item,
                            $customer->$item,
                            $customer->$item
                        );
                    } else {
                        $_var='select_'.$item;
                        ${$_var}[$customer->$item]='selected';

                        printf(
                            "
                            <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border>
                            <select name=%s_form>
                            <option value='0' %s>False
                            <option value='1' %s>True
                            </select>
                            </td>
                            </tr>
                            ",
                            $item_name,
                            $item,
                            ${$_var}[0],
                            ${$_var}[1]
                        );
                    }
                } else {
                    if ($this->Fields[$item]['adminonly'] && !$this->adminonly) {
                        printf(
                            "
                            <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border><input name=%s_form type=hidden value='%s'>%s</td>
                            </tr>
                            ",
                            $item_name,
                            $item,
                            $customer->$item,
                            $customer->$item
                        );
                    } else {
                        printf(
                            "
                            <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border><input name=%s_form size=30 type=text value='%s' %s></td>
                            </tr>
                            ",
                            $item_name,
                            $item,
                            $customer->$item,
                            $this->Fields[$item]['extra_html']
                        );
                    }
                }
            }
        }

        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        //print "</form>";
        print "
        </table>
        ";

        /*
        print "<pre>";
        print_r($customer);
        print "</pre>";
        */

        print "</td>
        <td valign=top>";
        /*
        print "<pre>";
        print_r($this->login_credentials);
        print "</pre>";
        */

        print "
        <table border=0>";

        if ($this->login_credentials['login_type'] == 'admin') {
            print <<< END
<tr bgcolor=lightgrey>
    <td class=border>Category</td>
    <td class=border>Level</td>
    <td class=border>Property</td>
    <td class=border>Value</td>
    <td class=border>Description</td>
</tr>
END;
        } elseif ($this->login_credentials['login_type'] == 'reseller') {
            print <<< END
<tr bgcolor=lightgrey>
    <td class=border>Level</td>
    <td class=border>Property</td>
    <td class=border>Value</td>
</tr>
END;
        } else {
            print <<< END
<tr bgcolor=lightgrey>
    <td class=border>Property</td>
    <td class=border>Value</td>
</tr>
END;
        }

        foreach ($customer->properties as $_property) {
            if (in_array($_property->name, array_keys($this->allProperties))) {
                $this->allProperties[$_property->name]['value'] = $_property->value;
            }
        }

        foreach (array_keys($this->allProperties) as $item) {
            $item_print = preg_replace("/_/", " ", $item);

            $_permission = $this->allProperties[$item]['permission'];

            if ($this->login_credentials['login_type'] == 'admin') {
                if ($this->allProperties[$item]['permission'] == 'admin'
                    && $customer->id != $customer->reseller
                    && $this->allProperties[$item]['resellerMayManageForChildAccounts']
                ) {
                        $_permission='reseller';
                }

                printf(
                    "
                    <tr>
                    <td class=border>%s</td>
                    <td class=border>%s</td>
                    <td class=border>%s</td>
                    <td class=border><input type=text size=45 name='%s_form' value='%s' autocomplete='no'></td>
                    <td class=border>%s</td>
                    </tr>
                    ",
                    $this->allProperties[$item]['category'],
                    ucfirst($_permission),
                    $item_print,
                    $item,
                    $this->allProperties[$item]['value'],
                    $this->allProperties[$item]['name']
                );
            } elseif ($this->login_credentials['login_type'] == 'reseller') {
                // logged in as reseller

                if ($this->allProperties[$item]['permission'] == 'admin') {
                    if ($customer->id == $customer->reseller) {
                        // reseller cannot modify himself for items with admin permission
                        if (!$this->allProperties[$item]['invisible']) {
                            printf(
                                "
                                <tr>
                                <td class=border>%s</td>
                                <td class=border>%s</td>
                                <td class=border>%s</td>
                                </tr>
                                ",
                                ucfirst($this->allProperties[$item]['permission']),
                                $this->allProperties[$item]['name'],
                                $this->allProperties[$item]['value']
                            );
                        }
                    } else {
                        if ($this->allProperties[$item]['resellerMayManageForChildAccounts']) {
                            // reseller can manage these properties for his customers
                            printf(
                                "
                                <tr>
                                <td class=border>%s</td>
                                <td class=border>%s</td>
                                <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                                </tr>
                                ",
                                'Reseller',
                                $this->allProperties[$item]['name'],
                                $item,
                                $this->allProperties[$item]['value']
                            );
                        } else {
                            if (!$this->allProperties[$item]['invisible']) {
                                // otherwise cannot modify them
                                printf(
                                    "
                                    <tr>
                                    <td class=border>%s</td>
                                    <td class=border>%s</td>
                                    <td class=border>%s </td>
                                    </tr>
                                    ",
                                    ucfirst($this->allProperties[$item]['permission']),
                                    $this->allProperties[$item]['name'],
                                    $this->allProperties[$item]['value']
                                );
                            }
                        }
                    }
                } else {
                    printf(
                        "
                        <tr>
                        <td class=border>%s</td>
                        <td class=border>%s</td>
                        <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                        </tr>
                        ",
                        ucfirst($this->allProperties[$item]['permission']),
                        $this->allProperties[$item]['name'],
                        $item,
                        $this->allProperties[$item]['value']
                    );
                }
            } else {
                // logged in as customer
                if ($this->allProperties[$item]['permission'] == 'admin' || $this->allProperties[$item]['permission'] == 'reseller' ) {
                    if (!$this->allProperties[$item]['invisible']) {
                        printf(
                            "
                            <tr>
                            <td class=border>%s</td>
                            <td class=border>%s </td>
                            </tr>
                            ",
                            $this->allProperties[$item]['name'],
                            $this->allProperties[$item]['value']
                        );
                    }
                } else {
                    printf(
                        "
                        <tr>
                        <td class=border>%s</td>
                        <td class=border><input type=text size=45 name='%s_form' value='%s'></td>
                        </tr>
                        ",
                        $this->allProperties[$item]['name'],
                        $item,
                        $this->allProperties[$item]['value']
                    );
                }
            }
        }

        print "
        </table>
        ";

        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        print "</form>";
        print "
        </td>
        </tr>
        </table>
        ";

        $this->showRecordFooter($customer);
    }

    function updateRecord()
    {
        //print "<p>Updating customer ...";

        if (!strlen($this->filters['customer'])) {
            return false;
        }

        if (!$customer = $this->getRecord($this->filters['customer'])) {
            return false;
        }

        if ($_REQUEST['notify']) {
            $customer_notify = array(
                'firstName'=> $customer->firstName,
                'lastName' => $customer->lastName,
                'email'    => $customer->email,
                'username' => $customer->username,
                'password' => $customer->password
            );

            if ($this->notify($customer_notify)) {
                print "<p>";
                printf(_("The login account details have been sent to %s"), $customer->email);
                return true;
            } else {
                print "<p>";
                printf(_("Error sending e-mail notification"));
                return false;
            }
        }

        if (!$this->updateBefore($customer)) {
            return false;
        }

        $customer->credit      = floatval($customer->credit);
        $customer->balance     = floatval($customer->balance);

        foreach ($customer->properties as $_property) {
            $properties[] = $_property;
        }

        if (is_array($properties)) {
            $customer->properties = $properties;
        } else {
            $customer->properties = array();
        }

        $customer_old = $customer;

        // update properties

        foreach (array_keys($this->allProperties) as $item) {
            $var_name   = $item.'_form';

            $updated_property = array();

            foreach (array_keys($customer->properties) as $_key) {
                $_property = $customer->properties[$_key];

                if ($_property->name == $item) {
                    // update property

                    if ($_property->permission == 'admin') {
                        if ($this->login_credentials['login_type'] == 'admin') {
                            $customer->properties[$_key]->value = trim($_REQUEST[$var_name]);
                        } elseif ($this->login_credentials['login_type'] == 'reseller'
                            && $this->allProperties[$item]['resellerMayManageForChildAccounts']
                        ) {
                            if ($customer->id != $customer->reseller) {
                                $customer->properties[$_key]->value = trim($_REQUEST[$var_name]);
                            }
                        }
                    } elseif ($_property->permission == 'reseller') {
                        if ($this->login_credentials['login_type'] == 'admin'
                            || $this->login_credentials['login_type'] == 'reseller'
                        ) {
                            $customer->properties[$_key]->value = trim($_REQUEST[$var_name]);
                        }
                    } else {
                        $customer->properties[$_key]->value = trim($_REQUEST[$var_name]);
                        if ($_key == 'yubikey' && $_REQUEST[$var_name] != '') {
                            $customer->properties[$_key]->value = substr($customer->properties[$_key]->value, 0, 12);
                        }
                    }

                    $updated_property[$item]++;

                    break;
                }
            }

            if (!$updated_property[$item] && strlen($_REQUEST[$var_name])) {
                // add new property

                unset($var_value);
                unset($_permission);

                if ($this->allProperties[$item]['permission'] == 'admin') {
                    $_permission = 'admin';

                    if ($this->login_credentials['login_type'] == 'admin') {
                        $var_value   =  trim($_REQUEST[$var_name]);
                    } elseif ($this->login_credentials['login_type'] == 'reseller' && $this->allProperties[$item]['resellerMayManageForChildAccounts']) {
                        if ($customer->id != $customer->reseller) {
                            $var_value   =  trim($_REQUEST[$var_name]);
                        }
                    }
                } elseif ($this->allProperties[$item]['permission'] == 'reseller') {
                    $_permission = 'reseller';

                    if ($this->login_credentials['login_type'] == 'admin' || $this->login_credentials['login_type'] == 'reseller') {
                        $var_value   =  trim($_REQUEST[$var_name]);
                    }
                } else {
                    $_permission = 'customer';
                    $var_value   =  trim($_REQUEST[$var_name]);
                }

                if (strlen($var_value)) {
                    if ($item == 'yubikey') {
                        $var_value = substr($var_value, 0, 12);
                    }
                    $customer->properties[] = array(
                        'name'       => $item,
                        'value'      => $var_value,
                        'category'   => $this->allProperties[$item]['category'],
                        'permission' => $this->allProperties[$item]['permission']
                    );
                }
            }
        }
        /*
        print "<pre>";
        print_r($customer->properties);
        print "</pre>";
        */

        foreach (array_keys($this->Fields) as $item) {
            $var_name = $item.'_form';
            //printf("<br>%s=%s", $var_name, $_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer' || $this->Fields[$item]['type'] == 'boolean') {
                $customer->$item = intval($_REQUEST[$var_name]);
            } elseif ($this->Fields[$item]['type'] == 'float') {
                $customer->$item = floatval($_REQUEST[$var_name]);
            } else {
                $customer->$item = strip_tags(trim($_REQUEST[$var_name]));
            }
        }

        $customer->tel  = preg_replace("/[^\+0-9]/", "", $customer->tel);
        $customer->fax  = preg_replace("/[^\+0-9]/", "", $customer->fax);
        $customer->enum = preg_replace("/[^\+0-9]/", "", $customer->enum);

        if (!strlen($_REQUEST['password_form'])) $customer->password = $this->RandomString(6);

        if (!strlen($_REQUEST['state_form']))    $customer->state    = 'N/A';
        if (!strlen($_REQUEST['country_form']))  $customer->country  = 'N/A';
        if (!strlen($_REQUEST['city_form']))     $customer->city     = 'Unknown';
        if (!strlen($_REQUEST['address_form']))  $customer->address  = 'Unknown';
        if (!strlen($_REQUEST['postcode_form'])) $customer->postcode = 'Unknown';
        if (!strlen($_REQUEST['tel_form']))      $customer->tel      = '+19999999999';

        if ($customer->reseller != $customer->id) {
            // a subaccount cannot change his own impersonate field
            if (!$this->adminonly) {
                if ($this->customer != $customer->reseller) {
                    $customer->impersonate = $customer_old->impersonate;
                }
            }
        }

        $function = array(
            'commit'   => array(
                'name'       => 'updateAccount',
                'parameters' => array($customer),
                'logs'       => array('success' => sprintf('Customer id %s has been updated', $customer->id))
            )
        );

        //dprint_r($customer);

        if ($this->SoapEngine->execute($function, $this->html, $this->adminonly)) {
            $this->updateAfter($customer, $customer_old);
            return true;
        } else {
            return false;
        }
    }

    function showTimezones($timezone)
    {
        if (!$fp = fopen("timezones", "r")) {
            print _("Failed to open timezone file.");
            return false;
        }

        print "<select name=timezone_form>";
        print "\n<option>";
        while ($buffer = fgets($fp, 1024)) {
            $buffer = trim($buffer);
            if ($timezone==$buffer) {
                $selected="selected";
            } else {
                $selected="";
            }
            print "\n<option $selected>";
            print "$buffer";
        }
        fclose($fp);
        print "</select>";
    }

    function getChildren($reseller)
    {
        return;
        // Filter

        $filter = array('reseller'     => intval($reseller));

        // Range
        $range = array('start' => 0,
                     'count' => 1000
                     );

        // Order
        $orderBy = array('attribute' => 'firstName',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query = array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getCustomers');

        // Call function
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $i=0;
            if ($result->total > 100) return;
            while ($i < $result->total) {
                $customer = $result->accounts[$i];

                $this->children[$customer->id] = array(
                    'firstName'    => $customer->firstName,
                    'lastName'     => $customer->lastName,
                    'organization' => $customer->organization
                );
                $i++;
            }
        }
    }

    function copyRecord()
    {
        //print "<p>Copy customer ...";

        if (!strlen($this->filters['customer'])) {
            return false;
        }

        if (!$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Copy again to confirm the copy</font>";
            return true;
        }

        if (!$customer = $this->getRecord($this->filters['customer'])) {
            return false;
        }

        $customer->credit      = floatval($customer->credit);
        $customer->balance     = floatval($customer->balance);

        foreach ($customer->properties as $_property) {
            $properties[] = $_property;
        }

        if (is_array($properties)) {
            $customer->properties = $properties;
        } else {
            $customer->properties = array();
        }

        // change username
        $customer_new = $customer;
        unset($customer_new->id);

        $j=1;
        while ($j < 9) {
            $customer_new->username = $customer->username.$j;

            $function = array(
                'commit'   => array('name'       => 'addAccount',
                    'parameters' => array($customer_new),
                    'logs'       => array(
                        'success' => sprintf('Customer id %s has been copied', $customer->id))
                )
            );

            if ($this->SoapEngine->execute($function, $this->html)) {
                // Reset filters to find the copy
                $this->filters = array();
                $this->filters['username'] = $customer_new->username;

                return true;
            } else {
                if ($this->SoapEngine->error_fault->detail->exception->errorcode != "5001") {
                    return false;
                }
            }

            $j++;
        }
    }

    function showAddForm($confirmPassword = false)
    {

        print "<div class='row-fluid'>
        <h1 class=page-header>Add new account</h1>";
        print "<p>";
        print _("Accounts are used for login and to assign ownership to data created in the platform. ");
        printf("<form class=form-horizontal method=post name=addform action=%s>", $_SERVER['PHP_SELF']);

        print "
        <p>
        <input type=hidden name=showAddForm value=1>
        ";

        if ($this->adminonly && $this->filters['reseller']) {
            printf("<tr><td class=border>Reseller</td>
            <td class=border>%s</td></tr>", $this->filters['reseller']);

            printf("<input type=hidden name=reseller_filter value='%s'>", $this->filters['reseller']);
        } elseif ($this->reseller) {
            printf("<tr><td class=border>Reseller</td>
            <td class=border>%s</td></tr>", $this->reseller);
        }

        foreach (array_keys($this->addFields) as $item) {
            if ($this->addFields[$item]['name']) {
                $item_name = $this->addFields[$item]['name'];
            } else {
                $item_name = ucfirst($item);
            }

            $item_form = $item.'_form';

            if ($item=='timezone') {
                $_value = $_REQUEST['timezone_form'];
                if (!$_value) {
                    if ($this->SoapEngine->default_timezone) {
                        $_value = $this->SoapEngine->default_timezone;
                    } else {
                        $_value='Europe/Amsterdam';
                    }
                }

                printf(
                    "
                    <div class=\"control-group\">
                    <label class=\"control-label\">%s</label>
                    ",
                    $item_name
                );
                print "<div class=\"controls\">";

                $this->showTimezones($_value);

                print "</div>
                </div>
                ";
            } elseif ($item=='state') {
                printf(
                    "
                    <div class=\"control-group\">
                    <label class=\"control-label\">
                    %s
                    </label>
                    <div class=\"controls\">",
                    $item_name
                );
                print "
                <select name=state_form>";

                $selected_state[$_REQUEST[$item_form]]='selected';

                foreach ($this->states as $_state) {
                    printf(
                        "<option value='%s' %s>%s",
                        $_state['value'],
                        $selected_state[$_state['value']],
                        $_state['label']
                    );
                }

                print "
                </select>
                </div>
                </div>
                ";
            } elseif ($item=='country') {
                printf(
                    "
                    <div class=\"control-group\">
                    <label class=\"control-label\">
                    %s
                    </label>
                    <div class=\"controls\">
                    ",
                    $item_name
                );
                print "
                <select name=country_form>";

                if (!$_REQUEST[$item_form]) {
                    if ($this->SoapEngine->default_country) {
                        $_value = $this->SoapEngine->default_country;
                    } else {
                        $_value='NL';
                    }
                } else {
                    $_value = $_REQUEST[$item_form];
                }

                $selected_country[$_value]='selected';

                foreach ($this->countries as $_country) {
                    printf(
                        "<option value='%s' %s>%s",
                        $_country['value'],
                        $selected_country[$_country['value']],
                        $_country['label']
                    );
                }

                print "
                </select>
                </div>
                </div>
                ";
            } else {
                if ($this->addFields[$item]['type'] == 'textarea') {
                    printf(
                        "
                        <div class=\"control-group\">
                        <label class=\"control-label\">
                        %s
                        </label>
                        <div class=\"controls\">
                        <textarea cols=30 name=%s_form rows=4>%s</textarea>
                        </div>
                        </div>
                        ",
                        $item_name,
                        $item,
                        $_REQUEST[$item_form]
                    );
                } elseif ($this->addFields[$item]['type'] == 'boolean') {
                    $_var='select_'.$item;
                    ${$_var}[$_REQUEST[$item_form]]='selected';

                    printf(
                        "
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border>
                        <select name=%s_form>
                        <option value='0' %s>False
                        <option value='1' %s>True
                        </select>
                        </td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        ${$_var}[0],
                        ${$_var}[1]
                    );
                } else {
                    $type='text';
                    if (strstr($item, 'password')) $type='password';

                    printf(
                        "
                        <div class=\"control-group\">
                        <label class=\"control-label\">
                        %s
                        </label>
                        <div class=\"controls\">
                        <input name=%s_form size=30 type=%s value='%s'>
                        </div>
                        </div>
                        ",
                        $item_name,
                        $item,
                        $type,
                        $_REQUEST[$item_form]
                    );

                    if ($item=='password' && $confirmPassword) {
                        printf(
                            "
                            <div class=\"control-group error\">
                            <label class=\"control-label\">
                            Confirm password
                            </label>
                            <div class=\"controls\">
                            <input name=confirm_password_form size=30 type=password value='%s'>
                            </div>
                            </div>
                            </tr>
                            ",
                            $_REQUEST['confirm_password_form']
                        );
                    }
                }
            }
        }
        if ($_REQUEST['notify']) $checked_notify='checked';

        printf(
            "
            <div class=\"control-group\">
            <label class=\"control-label\">Email notification</label>
            <div class=\"controls\">
            <input class=checkbox type=checkbox name=notify value='1' %s></div></div>
            ",
            $checked_notify
        );

        $this->printHiddenFormElements();
        print "<tr><td colspan=2><div class=form-actions><input class='btn' type=submit name=action value=Add></div></td></tr></form>";
        print "
        </div>
        ";
    }

    function addRecord($dictionary = array(), $confirmPassword = false)
    {
        if (!$this->checkRecord($dictionary)) {
            return false;
        }

        foreach (array_keys($this->addFields) as $item) {

            if ($dictionary[$item]) {
                $customer[$item] = strip_tags(trim($dictionary[$item]));
            } else {
                $item_form       = $item.'_form';
                $customer[$item] = strip_tags(trim($_REQUEST[$item_form]));
            }
        }

        if (!strlen($customer['username'])) $customer['username'] = trim($customer['firstName']).'.'.trim($customer['lastName'].$this->RandomNumber(5));
        if (!strlen($customer['state']))    $customer['state']    = 'N/A';
        if (!strlen($customer['country']))  $customer['country']  = 'N/A';
        if (!strlen($customer['city']))     $customer['city']     = 'Unknown';
        if (!strlen($customer['address']))  $customer['address']  = 'Unknown';
        if (!strlen($customer['postcode'])) $customer['postcode'] = 'Unknown';
        if (!strlen($customer['timezone'])) $customer['timezone'] = 'Europe/Amsterdam';

        if ($dictionary['reseller']) {
            $customer['reseller'] = intval($dictionary['reseller']);
        } elseif ($this->adminonly && $this->filters['reseller']) {
            $customer['reseller'] = intval($this->filters['reseller']);
        }

        $customer['username'] = strtolower(preg_replace("/\s+/", ".", trim($customer['username'])));
        $customer['username'] = preg_replace("/\.{2,}/", ".", $customer['username']);

        if ($customer['state'] != 'N/A') {
            $_state = $customer['state'].' ';
        } else {
            $_state='';
        }

        if (!strlen($customer['tel'])) {
            $customer['tel'] = '+19999999999';
        } else {
            $customer['tel'] = preg_replace("/[^0-9\+]/", "", $customer['tel']);
            if (preg_match("/^00(\d{1, 20})$/", $customer['tel'], $m)) {
                $customer['tel'] = "+".$m[1];
            }
        }

        $customer['billingEmail']   = $customer['email'];

        if ($customer['address'] != 'Unknown') {
            $customer['billingAddress'] = $customer['address']."\n".
                                          $customer['postcode']." ".$customer['city']."\n".
                                          $_state.$customer['country']."\n";
        }

        if ($confirmPassword) {
            if (!strlen($customer['password'])) {
                $this->errorMessage='Password cannot be empty';
                return false;
            } elseif ($customer['password'] != $_REQUEST['confirm_password_form']) {
                $this->errorMessage='Password is not confirmed';
                return false;
            }
        }

        if (!strlen($customer['password'])) $customer['password'] = $this->RandomString(6);

        if (is_array($dictionary['properties'])) {
            $customer['properties'] = $dictionary['properties'];
        } else {
            $customer['properties'] = array();
        }

        if ($this->hide_html) {
            $logs = array();
        } else {
            $logs = array(
                'success' => sprintf(
                    'Customer entry %s %s has been created',
                    $customer['firstName'],
                    $customer['lastName'])
            );
        }

        $function = array(
            'commit'   => array(
                'name'       => 'addAccount',
                'parameters' => array($customer),
                'logs'       => $logs
            )
        );

        if ($result = $this->SoapEngine->execute($function, $this->html)) {
            // We have succesfully added customer entry
            $this->showAddForm = false;

            if ($dictionary['notify'] || $_REQUEST['notify']) $this->notify($customer);

            return $result;
        } else {
            return false;
        }
    }

    function notify($customer)
    {
        /*
        must be supplied with an array:
        $customer = array('firstName' => ''
                        'lastName'  => '',
                        'email'     => '',
                        'username'  => '',
                        'password'  => ''
                        );
        */

        if ($this->support_web) {
            $url = $this->support_web;
        } else {
            if ($_SERVER['HTTPS']=="on") {
                $protocolURL="https://";
            } else {
                $protocolURL="http://";
            }

            $url = sprintf("%s%s", $protocolURL, $_SERVER['HTTP_HOST']);
        }

        $body =
        sprintf("Dear %s,\n\n", $customer['firstName']).
        sprintf("This e-mail message is for your record. You have registered a login account at %s as follows:\n\n", $url).
        sprintf("Username: %s\n", $customer['username']).
        sprintf("Password: %s\n", $customer['password']).
        "\n".

        sprintf("The registration has been performed from the IP address %s.", $_SERVER['REMOTE_ADDR']).
        "\n".
        "\n".

        sprintf("This message was sent in clear text over the Internet and it is advisable, in order to protect your account, to login and change your password displayed in this message. ").
        "\n".

        "\n".
        "This is an automatic message, do not reply.\n";

        $from    = sprintf("From: %s", $this->support_email);
        $subject = sprintf("Your account at %s", $url);

        return mail($customer['email'], $subject, $body, $from);
    }

    function getRecordKeys()
    {
        // Filter
        $filter = array(
            'username'       => $this->filters['username'],
            'firstName'      => $this->filters['firstName'],
            'lastName'       => $this->filters['lastName'],
            'organization'   => $this->filters['organization'],
            'tel'            => $this->filters['tel'],
            'email'          => $this->filters['email'],
            'web'            => $this->filters['web'],
            'city'           => $this->filters['city'],
            'country'        => $this->filters['country'],
            'only_resellers' => $this->filters['only_resellers'],
            'customer'       => intval($this->filters['customer']),
            'reseller'       => intval($this->filters['reseller'])
        );

        // Range
        $range = array(
            'start' => 0,
            'count' => 1000
        );

        // Order
        $orderBy = array(
            'attribute' => 'customer',
            'direction' => 'ASC'
        );

        // Compose query
        $Query = array(
            'filter'     => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        if ($this->adminonly && $this->filters['only_resellers']) {
            $this->log_action('getResellers');
            $result = $this->SoapEngine->soapclient->getResellers($Query);
        } else {
            $this->log_action('getCustomers');
            $result = $this->SoapEngine->soapclient->getCustomers($Query);
        }

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach ($result->accounts as $customer) {
                $this->selectionKeys[] = $customer->id;
            }
        }
    }

    function getProperty($customer, $name)
    {
        foreach ($customer->properties as $_property) {
            if ($_property->name == $name) {
                return $_property->value;
            }
        }
        return false;
    }

    function getCustomerId($username)
    {
        if (!strlen($username)) return false;
        $filter  = array('username' => $username);
        $range   = array('start' => 0,'count' => 1);
        $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');
        $Query = array('filter'     => $filter, 'orderBy' => $orderBy, 'range'   => $range);

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        // Call function
        $this->log_action('getCustomers');
        $result = $this->SoapEngine->soapclient->getCustomers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if (count($result->accounts) == 1) {
                return $result->accounts[0]->id;
            } else {
                return false;
            }
        }
    }

    function getCustomer($username)
    {
        if (!strlen($username)) {
            return false;
        }
        $filter  = array('username' => $username);
        $range   = array('start' => 0,'count' => 1);
        $orderBy = array('attribute' => 'customer', 'direction' => 'ASC');
        $Query = array('filter'     => $filter, 'orderBy' => $orderBy, 'range'   => $range);

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Call function
        $this->log_action('getCustomers');
        $result     = $this->SoapEngine->soapclient->getCustomers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if (count($result->accounts) == 1) {
                return $result->accounts[0];
            } else {
                return false;
            }
        }
    }

    function setInitialCredits($credits = array())
    {
        $properties = array();

        foreach (array_keys($credits) as $item) {
            if ($this->allProperties[$item]['category'] != 'credit') continue;
            $properties[] = array(
                'name'       => $item,
                'value'      => "$credits[$item]",
                'category'   => $this->allProperties[$item]['category'],
                'permission' => $this->allProperties[$item]['permission']
            );
        }

        return $properties;
    }

    function showVcard($vcardDictionary)
    {
        #http://www.stab.nu/vcard/
        # This file will return an vCard Version 3.0 Compliant file to the user. Observe that you should set up     #
        # your web-server with the correct MIME-type. The reason to use the \r\n as breakes is because it should be #
        # more compatible with MS Outlook. All other, better coded, clients sholdnt have any problems with this.    #
        #                                                                                                           #
        # Version 1.0 (2003-08-29)                                                                                  #
        #                                                                                                           #
        # Author: Alf Lovbo <affe@stab.nu>                                                                          #
        #                                                                                                           #
        # This document is released under the GNU General Public License.                                           #
        #                                                                                                           #
        #############################################################################################################
        #                                                                                                           #
        # USAGE                                                                                                     #
        # -----                                                                                                     #
        # The following variables can be used togheter with this document for accessing the functions supplied. All #
        # of the functions listed below takes an value described by the comment after the |-symbol.                 #
        #                                                                                                           #
        # $vcard_birtda | Birthday YYYY-MM-DD                $vcard_f_name | Family name                            #
        # $vcard_cellul | Cellular Phone Number              $vcard_compan | Company Name                           #
        # $vcard_h_addr | Street Address (home)              $vcard_h_city | City (home)                            #
        # $vcard_h_coun | Country (home)                     $vcard_h_fax  | Fax (home)                             #
        # $vcard_h_mail | E-mail (home)                      $vcard_h_phon | Phone (home)                           #
        # $vcard_h_zip  | Zip-code (home)                    $vcard_nickna | Nickname                               #
        # $vcard_note   | Note                               $vcard_s_name | Given name                             #
        # $vcard_uri    | Homepage, URL                      $vcard_w_addr | Street Address (work)                  #
        # $vcard_w_city | City (work)                        $vcard_w_coun | Country (work)                         #
        # $vcard_w_fax  | Fax (work)                         $vcard_w_mail | E-mail (work)                          #
        # $vcard_w_phon | Phone (work)                       $vcard_w_role | Function (work)                        #
        # $vcard_w_titl | Title (work)                       $vcard_w_zip  | Zip-code (work)                        #
        #                                                                                                           #
        #############################################################################################################
        # You dont need to change anything below this comment.                                                      #
        #############################################################################################################

        /*
        $vcardDictionary = array(
                               "vcard_nickna"   => $this->username,
                               "vcard_f_name"   => $this->lastname,
                               "vcard_s_name"   => $this->firstname,
                               "vcard_compan"   => $this->organization,
                               "vcard_w_addr"   => $this->address,
                               "vcard_w_zip"    => $this->postcode,
                               "vcard_w_city"   => $this->city,
                               "vcard_w_state"  => $this->county,
                               "vcard_w_coun"   => $this->country,
                               "vcard_w_mail"   => $this->email,
                               "vcard_w_phon"   => $this->tel,
                               "vcard_w_fax"    => $this->fax,
                               "vcard_enum"     => $this->enum,
                               "vcard_sip"      => $this->sip,
                               "vcard_uri"      => $this->web,
                               "vcard_cellul"   => $this->mobile
                               );
        */

        foreach (array_keys($vcardDictionary) as $field) {
            $value = $vcardDictionary[$field];
            ${$field}=$value;
        }

        if ($vcard_w_state=="N/A") $vcard_w_state=" ";
        $vcard_w_addr = preg_replace("/[\n|\r]/", " ", $vcard_w_addr);

        $vcard_sortst = $vcard_f_name;

        $vcard_tz = date("O");
        $vcard_rev = date("Y-m-d");

        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "CLASS:PUBLIC\r\n";
        $vcard .= "PRODID:-//PHP vCard Class//NONSGML Version 1//SE\r\n";
        $vcard .= "REV:" . $vcard_rev . "\r\n";
        $vcard .= "TZ:" . $vcard_tz . "\r\n";
        if ($vcard_f_name != "") {
            if ($vcard_s_name != "") {
                $vcard .= "FN:" . $vcard_s_name . " " . $vcard_f_name . "\r\n";
                $vcard .= "N:" . $vcard_f_name . ";" . $vcard_s_name . "\r\n";
            } else {
                $vcard .= "FN:" . $vcard_f_name . "\r\n";
                $vcard .= "N:" . $vcard_f_name . "\r\n";
            }
        } elseif ($vcard_s_name != "") {
            $vcard .= "FN:" . $vcard_s_name . "\r\n";
            $vcard .= "N:" . $vcard_s_name . "\r\n";
        }
        if ($vcard_nickna != "") {
            $vcard .= "NICKNAME:" . $vcard_nickna . "\r\n";
        }
        if ($vcard_compan != "") {
            $vcard .= "ORG:" . $vcard_compan . "\r\n";
            $vcard .= "SORTSTRING:" . $vcard_compan . "\r\n";
        } elseif ($vcard_f_name != "") {
            $vcard .= "SORTSTRING:" . $vcard_f_name . "\r\n";
        }
        if ($vcard_birtda != "") {
            $vcard .= "BDAY:" . $vcard_birtda . "\r\n";
        }
        if ($vcard_w_role != "") {
            $vcard .= "ROLE:" . $vcard_w_role . "\r\n";
        }
        if ($vcard_w_titl != "") {
            $vcard .= "TITLE:" . $vcard_w_titl . "\r\n";
        }
        if ($vcard_note != "") {
            $vcard .= "NOTE:" . $vcard_note . "\r\n";
        }
        if ($vcard_w_mail != "") {
            $item++;
            $vcard .= "item$item.EMAIL;TYPE=INTERNET;type=PREF:" . $vcard_w_mail . "\r\n";
            $vcard .= "item$item.X-ABLabel:email" . "\r\n";
        }
        if ($vcard_cellul != "") {
            $vcard .= "TEL;TYPE=VOICE,CELL:" . $vcard_cellul . "\r\n";
        }
        if ($vcard_enum != "") {
            $item++;
            $vcard .= "item$item.TEL:" . $vcard_enum . "\r\n";
            $vcard .= "item$item.X-ABLabel:ENUM" . "\r\n";
        }
        if ($vcard_sip != "") {
            $item++;
            $vcard .= "item$item.TEL;TYPE=INTERNET:" . $vcard_sip . "\r\n";
            $vcard .= "item$item.X-ABLabel:SIP" . "\r\n";
        }
        if ($vcard_w_fax != "") {
            $vcard .= "TEL;TYPE=FAX,WORK:" . $vcard_w_fax . "\r\n";
        }
        if ($vcard_w_phon != "") {
            $vcard .= "TEL;TYPE=VOICE,WORK:" . $vcard_w_phon . "\r\n";
        }
        if ($vcard_uri != "") {
            $vcard .= "URL:" . $vcard_uri . "\r\n";
        }
        if ($vcard_addr != "") {
            $vcard .= "ADR;TYPE=HOME,POSTAL,PARCEL:" . $vcard_addr . "\r\n";
        }
        if ($vcard_labl != "") {
            $vcard .= "LABEL;TYPE=DOM,HOME,POSTAL,PARCEL:" . $vcard_labl . "\r\n";
        }
        $vcard_addr = "";
        $vcard_labl = "";
        if ($vcard_w_addr != "") {
            $vcard_addr = ";;" . $vcard_w_addr;
            $vcard_labl = $vcard_w_addr;
        }
        if ($vcard_w_city != "") {
            if ($vcard_addr != "") {
                $vcard_addr .= ";" . $vcard_w_city;
            } else {
                $vcard_addr .= ";;;" . $vcard_w_city;
            }
            if ($vcard_labl != "") {
                $vcard_labl .= "\\r\\n" . $vcard_w_city;
            } else {
                $vcard_labl = $vcard_w_city;
            }
        }
        if ($vcard_w_state != "") {
            if ($vcard_addr != "") {
                $vcard_addr .= ";" . $vcard_w_state;
            } else {
                $vcard_addr .= ";;;" . $vcard_w_state;
            }
            if ($vcard_labl != "") {
                $vcard_labl .= "\\r\\n" . $vcard_w_state;
            } else {
                $vcard_labl = $vcard_w_state;
            }
        }
        if ($vcard_w_zip != "") {
            if ($vcard_addr != "") {
                $vcard_addr .= ";" . $vcard_w_zip;
            } else {
                $vcard_addr .= ";;;;" . $vcard_w_zip;
            }
            if ($vcard_labl != "") {
                $vcard_labl .= "\\r\\n" . $vcard_w_zip;
            } else {
                $vcard_labl = $vcard_w_zip;
            }
        }
        if ($vcard_w_coun != "") {
            if ($vcard_addr != "") {
                $vcard_addr .= ";" . $vcard_w_coun;
            } else {
                $vcard_addr .= ";;;;;" . $vcard_w_coun;
            }
            if ($vcard_labl != "") {
                $vcard_labl .= "\\r\\n" . $vcard_w_coun;
            } else {
                $vcard_labl = $vcard_w_coun;
            }
        }
        if ($vcard_addr != "") {
            $vcard .= "ADR;TYPE=WORK,POSTAL,PARCEL:" . $vcard_addr . "\r\n";
        }
        if ($vcard_labl != "") {
            $vcard .= "LABEL;TYPE=DOM,WORK,POSTAL,PARCEL:" . $vcard_labl . "\r\n";
        }
        if ($vcard_categ != "") {
            $vcard .= "CATEGORY:" . $vcard_categ . "\r\n";
        }

        $vcard .= "END:VCARD\n";
        return $vcard;
    }
}
