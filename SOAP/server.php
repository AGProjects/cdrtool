#!/usr/bin/php
<?
include("../global.inc");

require_once 'SOAP/Server.php';
require_once 'server_lib.php';

require_once '../cdrlib.phtml';

define_syslog_variables();
openlog("CDRTool-SOAP", LOG_PID, LOG_LOCAL0);

syslog(LOG_NOTICE,"--");
syslog(LOG_NOTICE,"SOAP client connection from $_SERVER[REMOTE_ADDR]");

$soapServer = new SOAP_Server;
$webservice = new NGNProCDRToolServer();

$soapServer->addObjectMap($webservice,'http://ag-projects.com/ngnpro');
$soapServer->service($HTTP_RAW_POST_DATA);

?>
