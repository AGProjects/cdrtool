#!/usr/bin/php
<?
# sample SOAP client that connects to remote CDRTool 
# and queries the software version number

$path=dirname(realpath($_SERVER['PHP_SELF']));
include("../global.inc");
include("SOAP/Client.php");

/*
$wsdl       = new SOAP_WSDL($SOAPServer['wsdl']);
$soapclient = $wsdl->getProxy();
$soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
$soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
*/

include("client_lib.php");
$soapclient = new WebService_NGNProCDRTool_NGNProCDRTool();

$options    = array('namespace' => 'http://ag-projects.com/ngnpro', 'trace' => 0);
$result     = $soapclient->call('Version',$params,$options);

if (PEAR::isError($result)) {
    $error_msg=$result->getMessage();
    print "<font color=red>$error_msg</font>";
} else {
    print "Succesfully connected to $SOAPServer[location]\n";
    print "CDRTool SOAP server version: $result\n";
}
?>
