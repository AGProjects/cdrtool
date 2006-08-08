<?php
/* OOHForms: radio
 * 
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_radio.inc,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 */

class of_radio extends of_element {

  var $valid_e;

  // Constructor
  function of_radio($a) {
    $this->setup_element($a);
  }

  function self_get($val, $which, &$count) {
    $str = "";
    
    $str .= "<input type='radio' name='$this->name' value='$val'";
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
    if ($this->value==$val) 
      $str .= " checked";
    $str .= ">";

    $count = 1;
    return $str;
  }

  function self_get_frozen($val,$which, &$count) {
    $str = "";
    
    $x = 0;
    if ($this->value==$val) {
      $x = 1;
      $str .= "<input type='hidden' name='$this->name' value='$val'>\n";
      $str .= "<table border=1 bgcolor=#333333>";
    } else {
      $str .= "<table border=1>";
    }
    $str .= "<tr><td>&nbsp</tr></td></table>\n";
    
    $count = $x;
    return $str;
  }

  function self_get_js($ndx_array) {
    $str = "";
    
    if ($this->valid_e) {
      $n = $this->name;
      $str .= "var l = f.${n}.length;\n";
      $str .= "var radioOK = false;\n";
      $str .= "for (i=0; i<l; i++)\n";
      $str .= "  if (f.${n}[i].checked) {\n";
      $str .= "    radioOK = true;\n";
      $str .= "    break;\n";
      $str .= "  }\n";
      $str .= "if (!radioOK) {\n";
      $str .= "  alert(\"$this->valid_e\");\n";
      $str .= "  return(false);\n";
      $str .= "}\n";
    }
  }

  function self_validate($val) {
    if ($this->valid_e && !isset($val)) return $this->valid_e;
    return false;
  }

} // end RADIO

?>
