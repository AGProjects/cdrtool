<?
include("/etc/cdrtool/global.inc");
page_open(
    array("sess" => "CDRTool_Session",
          "auth" => "CDRTool_Auth",
          "perm" => "CDRTool_Perm"
          ));

require("sip_statistics.php");
$perm->check("statistics");

global $CDRTool;
if (strlen($CDRTool['filter']['domain'])) $allowedDomains=explode(' ',$CDRTool['filter']['domain']);

$SipThorNetworkImage = new SipThorNetworkImage($_REQUEST['engine'],$allowedDomains);
$img = $SipThorNetworkImage->buildImage();

header("Content-type: image/png");
imagepng($img);
imagedestroy($img);

page_close();
?>