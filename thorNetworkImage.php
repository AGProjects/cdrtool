<?php

require("sip_statistics_lib.phtml");

$ThorNetwork = new ThorNetwork();
$img=$ThorNetwork->buildImage();

header("Content-type: image/png");
imagepng($img);
imagedestroy($img);
?>
