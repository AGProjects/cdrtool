<?php
include("../global.inc");
page_open(
    array("sess" => "CDRTool_Session",
          "auth" => "CDRTool_Auth",
          "perm" => "CDRTool_Perm"
          ));

$perm->check("statistics");

class ThorNetwork {
    var $imgsize   = 630;

    function ThorNetwork($node='demo.example.com') {

        if ($fp = fsockopen ($node, "2000", $errno, $errstr, 5)) {
            fwrite($fp,"get\r\n");
            while(!feof($fp)) {
                $json.=fgets ($fp,200000);
            }
            fclose($fp);
        } else {
            print "Error: cannot connect.\n";
            exit;
        }
        
        $this->nodes=json_decode(trim($json),1);
    }

    function buildImage() {
        $img = imagecreatetruecolor($this->imgsize, $this->imgsize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        imagefill($img, 0, 0, $white);
    
        $c=count($this->nodes);
        imagestring ($img, 5, 20, 20, "SIP UA distribution", $black);
     
        $cx=$this->imgsize/2;
        $cy=$cx;
     
        $radius=0.7*$cx;
     
        // Get ThorNode image
        $node_img = @imagecreatefrompng('Node.png');
        list($nw, $nh) = getimagesize('Node.png');
    
        // Get ClassicNode image
        $cnode_img = @imagecreatefrompng('ClassicNode.png');
        list($cnw, $cnh) = getimagesize('ClassicNode.png');
    
        // Get Cloud Image
        $cloud_img = @imagecreatefrompng('InternetCloud.png');
        list($cw, $ch) = getimagesize('InternetCloud.png');
    
        // Get Thor rectangle image
        $thor_img = @imagecreatefrompng('P2PThorTitle.png');
        list($tw, $th) = getimagesize('P2PThorTitle.png');
    
        // Get Classic rectangle image
        $cthor_img = @imagecreatefrompng('ClassicRectangle.png');
        list($ctw, $cth) = getimagesize('ClassicRectangle.png');
    
        if (count($this->nodes)) {
            if (count($this->nodes) > 1) {
                imagecopy ($img,$thor_img, $this->imgsize/2-$tw/2, $this->imgsize/2-$th/2-5, 0, 0, $tw, $th);
                imagecopy ($img,$cloud_img, $this->imgsize/2-$cw/2, $this->imgsize/2-$ch/2, 0, 0, $cw, $ch);
        
                $dash=false; 
                $dashsize=2;
                
                for ($angle=0; $angle<=(180+$dashsize); $angle+=$dashsize) { 
                  $x = ($radius * cos(deg2rad($angle))); 
                  $y = ($radius * sin(deg2rad($angle))); 
             
                  if ($dash) { 
                      imageline($img, $cx+$px, $cy+$py, $cx+$x, $cy+$y, 'black');
                      imageline($img, $cx-$px, $cx-$py, $cx-$x, $cy-$y, 'black');
                  } 
                  
                  $dash=!$dash; 
                  $px=$x; 
                  $py=$y; 
                }
            } else {
                imagecopy ($img,$cthor_img, $this->imgsize/2-$ctw/2, $this->imgsize/2-$cth/2+50, 0, 0, $ctw, $cth);
            }
    
            $dashsize=360/count($this->nodes);
            $j=0;

            $node_names=array_keys($this->nodes);

            for ($angle=0; $angle<360; $angle+=$dashsize) {
         
              $x = ($radius * cos(deg2rad($angle)));
              $y = ($radius * sin(deg2rad($angle))); 
              
              $text = $node_names[$j];
              $px=$x; 
              $py=$y; 
         
              if (count($this->nodes) > 1) {
                  if (($angle >= 120 && $angle < 240)) {
                    imagestring ($img, 3, $cx+$px-70, $cy+$py-60, $text, $black);
                  } else {
                    imagestring ($img, 3, $cx+$px-110, $cy+$py-30, $text, $black);
                  }
                  imagecopy ($img,$node_img, $cx+$px-$nw/2+7, $cy+$py-$nh/2+5, 0, 0, $nw-20, $nh-20);
              } else {
                  imagecopy ($img,$cnode_img, $this->imgsize/2-$cnw/2-100, $this->imgsize/2-$cnh/2, 0, 0, $cnw, $cnh);
                  imagestring ($img, 3, $this->imgsize/2-$cnw/2-60, $this->imgsize/2-$cnh/2+220, $text, $black);
              }
              $j++;
              
            }
        }
    
        return $img;
    }
}

$ThorNetwork = new ThorNetwork($_REQUEST['node']);
$img=$ThorNetwork->buildImage();

header("Content-type: image/png");
imagepng($img);
imagedestroy($img);

page_close();
?>
