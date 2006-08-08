#!/usr/bin/php
<?
include("../global.inc");
require_once('SOAP/WSDL.php');

if ($SOAPServer['wsdl']) {
    $wsdl= new SOAP_WSDL($SOAPServer['wsdl']);
    print $wsdl->generateProxyCode();
} else {
    print "Please define \$SOAPServer['wsdl'] in global.inc\n";
}
?>
