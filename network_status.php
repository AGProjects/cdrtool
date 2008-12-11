<?
require("/etc/cdrtool/global.inc");
page_open(
    array("sess" => "CDRTool_Session",
          "auth" => "CDRTool_Auth",
          "perm" => "CDRTool_Perm"
          ));

require("sip_statistics.php");
require("media_sessions.php");

$perm->check("statistics");

$title="SIP registrar statistics";
include("header.phtml");

global $CDRTool;
if (strlen($CDRTool['filter']['domain'])) $allowedDomains=explode(' ',$CDRTool['filter']['domain']);

$layout = new pageLayoutLocal();
$layout->showTopMenu($title);

if ($_REQUEST['datasource']) {
	$datasources=array($_REQUEST['datasource']);
} else {
    $datasources=array_keys($DATASOURCES);
}

$filters=array('domain'=>$_REQUEST['domain']);

foreach ($datasources as $datasource) {

    if (in_array($datasource,$CDRTool['dataSourcesAllowed'])) {
        print "<table border=0>";
        print "<tr>";
        print "<td valign=top>";

        if ($DATASOURCES[$datasource]['networkStatus']) {
            printf ("<img src=images/SipThorOverview.php?engine=%s&role=%s align=left>",
            $DATASOURCES[$datasource]['networkStatus'],$_REQUEST['role']);

        } else if ($DATASOURCES[$datasource]['db_registrar']){

			require_once("cdr_generic.php");
			$online = new SIPonline($datasource,$DATASOURCES[$datasource]['db_registrar']);

	    	printf ("<h3>%s</h3>",$DATASOURCES[$datasource]['name']);
            $online->showAll();
        }

        print "</td>";
        print "<td valign=top>";

        if ($DATASOURCES[$datasource]['mediaSessions']) {
            $MediaSessions = new MediaSessionsNGNPro($DATASOURCES[$datasource]['mediaSessions'],$allowedDomains);
            $MediaSessions->getSessions();
            $MediaSessions->getSummary();
            print "<h2>Media relays</h2>";
            $MediaSessions->showSummary();
        }

        if ($DATASOURCES[$datasource]['networkStatus']) {

        	$NetworkStatistics = new NetworkStatistics($DATASOURCES[$datasource]['networkStatus'],$allowedDomains);

            print "<h2>SIP accounts</h2>";
            $NetworkStatistics->showStatistics();

            if (!$allowedDomains) {
                print "<p>";
                print "<h2>Network topology</h2>";
                $NetworkStatistics->showStatus();
            }
        }

        print "</td>";
        print "</tr>";
        print "</table>";
    }
}

$layout->showFooter();

print "
</body>
</html>
";
page_close();

?>
