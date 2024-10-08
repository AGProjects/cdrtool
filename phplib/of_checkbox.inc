<?php
/* OOHForms: checkbox
 *
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_checkbox.inc,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 */

class of_checkbox extends of_element {

  var $checked;

  // Constructor
  function __construct($a) {
    $this->setup_element($a);
  }

  function self_get($val, $which, &$count) {
    $str = "";
    
    if ($this->multiple) {
      $n = $this->name . "[]";
      $str .= "<input type='checkbox' name='$n' value='$val'";
      if (is_array($this->value)) {
        reset($this->value);
        while (list($k,$v) = each($this->value)) {
          if ($v==$val) {
            $str .= " checked"; 
            break; 
          }
        }
      }
    } else {
      $str .= "<input type='checkbox' name='$this->name'";
      $str .= " value='$this->value'";
      if ($this->checked) 
        $str .= " checked";
    }
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
    $str .= ">\n";
    
    $count = 1;
    return $str;
  }

  function self_get_frozen($val, $which, &$count) {
    $str = "";
    
    $x = 0;
    $t="";
    if ($this->multiple) {
      $n = $this->name . "[]";
      if (is_array($this->value)) {
        reset($this->value);
        while (list($k,$v) = each($this->value)) {
          if ($v==$val) {
	          $x = 1;
            $str .= "<input type='hidden' name='$this->name' value='$v'>\n";
            $t =" bgcolor=#333333";
            break;
          }
        }
      }
    } else {
      if ($this->checked) {
        $x = 1;
        $t = " bgcolor=#333333";
        $str .= "<input type='hidden' name='$this->name'";
        $str .= " value='$this->value'>";
      }
    }
    $str .= "<table$t border=1><tr><td>&nbsp</td></tr></table>\n";

    $count = $x;
    return $str;
  }
  
  function self_load_defaults($val) {
    if ($this->multiple)
      $this->value = $val;
    elseif (isset($val) && (!$this->value || $val==$this->value)) 
      $this->checked=1;
    else 
      $this->checked=0;
  }

} // end CHECKBOX

?>
