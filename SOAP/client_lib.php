<?
class WebService_NGNProCDRTool_NGNProCDRTool extends SOAP_Client
{
    function WebService_NGNProCDRTool_NGNProCDRTool()
{
        $this->SOAP_Client("https://secure.dns-hosting.info/CDRTool/SOAP/server.php", 0);
    }
    function &Version() {
        return $this->call("Version", 
                        $v = null, 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &AddBalance($auth, $account, $balance) {
        return $this->call("AddBalance", 
                        $v = array("auth"=>$auth, "account"=>$account, "balance"=>$balance), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &AddPrepaidAccount($auth, $account) {
        return $this->call("AddPrepaidAccount", 
                        $v = array("auth"=>$auth, "account"=>$account), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &DeletePrepaidAccount($auth, $account) {
        return $this->call("DeletePrepaidAccount", 
                        $v = array("auth"=>$auth, "account"=>$account), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &GetPrepaidAccountInfo($auth, $account) {
        return $this->call("GetPrepaidAccountInfo", 
                        $v = array("auth"=>$auth, "account"=>$account), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &GetCallStatistics($auth, $account, $afterDate) {
        return $this->call("GetCallStatistics", 
                        $v = array("auth"=>$auth, "account"=>$account, "afterDate"=>$afterDate), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &GetCallHistory($auth, $account, $filters) {
        // filters is a ComplexType CallHistoryFilters,
        //refer to wsdl for more info
        $filters =& new SOAP_Value('filters','{http://ag-projects.com/ngnpro}CallHistoryFilters',$filters);
        return $this->call("GetCallHistory", 
                        $v = array("auth"=>$auth, "account"=>$account, "filters"=>$filters), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &GetLastReceivedCalls($auth, $account, $filter) {
        // filter is a ComplexType CDRFilter,
        //refer to wsdl for more info
        $filter =& new SOAP_Value('filter','{http://ag-projects.com/ngnpro}CDRFilter',$filter);
        return $this->call("GetLastReceivedCalls", 
                        $v = array("auth"=>$auth, "account"=>$account, "filter"=>$filter), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &GetLastPlacedCalls($auth, $account, $filter) {
        // filter is a ComplexType CDRFilter,
        //refer to wsdl for more info
        $filter =& new SOAP_Value('filter','{http://ag-projects.com/ngnpro}CDRFilter',$filter);
        return $this->call("GetLastPlacedCalls", 
                        $v = array("auth"=>$auth, "account"=>$account, "filter"=>$filter), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &GetSERLocations($auth, $account) {
        return $this->call("GetSERLocations", 
                        $v = array("auth"=>$auth, "account"=>$account), 
                        array('namespace'=>'http://ag-projects.com/ngnpro',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
?>
