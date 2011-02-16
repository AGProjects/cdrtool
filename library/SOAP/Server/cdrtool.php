<?php

require_once 'SOAP/Client.php';


Notice: Undefined index:  DebitBalanceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 493

Notice: Undefined index:  DebitBalanceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 499

Notice: Undefined index:  AddBalanceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 493

Notice: Undefined index:  AddBalanceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 499

Notice: Undefined index:  MaxSessionTimeRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 493

Notice: Undefined index:  MaxSessionTimeRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 499

Notice: Undefined index:  ShowPriceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 493

Notice: Undefined index:  ShowPriceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 499

Notice: Undefined index:  GetBalanceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 493

Notice: Undefined index:  GetBalanceRequest in /Users/adigeo/Sites/library/php/SOAP/WSDL.php on line 499
class WebService_NGNProCDRTool_NGNProCDRTool extends SOAP_Client
{
    function WebService_NGNProCDRTool_NGNProCDRTool()
{
        $this->SOAP_Client("https://secure.dns-hosting.info/ngnpro/", 0);
    }
    function &DebitBalanceRequest($auth, $from, $to, $duration) {
        return $this->call("DebitBalanceRequest", 
                        $v = array("auth"=>$auth, "from"=>$from, "to"=>$to, "duration"=>$duration), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'' ));
    }
    function &AddBalanceRequest($auth, $account, $balance) {
        return $this->call("AddBalanceRequest", 
                        $v = array("auth"=>$auth, "account"=>$account, "balance"=>$balance), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'' ));
    }
    function &MaxSessionTimeRequest($auth, $from, $to, $duration) {
        return $this->call("MaxSessionTimeRequest", 
                        $v = array("auth"=>$auth, "from"=>$from, "to"=>$to, "duration"=>$duration), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'' ));
    }
    function &ShowPriceRequest($auth, $from, $to, $duration) {
        return $this->call("ShowPriceRequest", 
                        $v = array("auth"=>$auth, "from"=>$from, "to"=>$to, "duration"=>$duration), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'' ));
    }
    function &GetBalanceRequest($auth, $account) {
        return $this->call("GetBalanceRequest", 
                        $v = array("auth"=>$auth, "account"=>$account), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'' ));
    }
}

?>