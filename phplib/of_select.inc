<?php
/* OOHForms: select
 *
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_select.inc,v 1.3 2004-07-10 09:30:56 adigeo Exp $
 */

class of_select extends of_element {

  var $options;
  var $size;
  var $valid_e;

  // Constructor
    function __construct($a) {
    $this->setup_element($a);
    if ($a["type"]=="select multiple") $this->multiple=1;
  }
  
  function self_get($val,$which, &$count) {
    $str = "";
    
    if ($this->multiple) {
      $n = $this->name . "[]";
      $t = "select multiple";
    } else {
      $n = $this->name;
      $t = "select";
    }
    $str .= "<$t name='$n'";
    if ($this->size) 
      $str .= " size='$this->size'";
    if ($this->multiple)
      $str .= "multiple='multiple'";
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
    $str .= ">";

    foreach ($this->options as $k=>$o) {
      $str .= "<option";
      if (is_array($o)) 
        $str .= " value='" .  $o["value"] . "'";
      if (!$this->multiple && ($this->value==$o["value"] || $this->value==$o))
        $str .= " selected";
      elseif ($this->multiple && is_array($this->value)) {
        foreach ($this->value as $tk=>$v) {
          if ($v==$o["value"] || $v==$o) { 
            $str .= " selected"; break; 
          }
        }
      }
      $str .= ">" . (is_array($o) ? $o["label"] : $o) . "\n";
    }
    $str .= "</select>";
    
    $count = 1;
    return $str;
  }

  function self_get_frozen($val,$which, &$count) {
    $str = "";
    
    $x = 0;
    $n = $this->name . ($this->multiple ? "[]" : "");
    $v_array = (is_array($this->value) ? $this->value : array($this->value));
    $str .= "\n";
    foreach($v_array as $tk=>$tv) {
      foreach ($this->options as $k=>$v) {
        if ((is_array($v) && 
           (($tmp=$v["value"])==$tv || $v["label"]==$tv))
         || ($tmp=$v)==$tv) {
          $x++;
          $str .= "<input type='hidden' name='$n' value='$tmp'>\n";
          $str .= "" . (is_array($v) ? $v["label"] : $v) . "\n";
        }
      }
    }
    #$str .= "<br>\n";
    
    $count = $x;
    return $str;
  }

  function self_get_js($ndx_array) {
    $str = "";
    
    if (!$this->multiple && $this->valid_e) {
      $str .= "if (f.$this->name.selectedIndex == 0) {\n";
      $str .= "  alert(\"$this->valid_e\");\n";
      $str .= "  f.$this->name.focus();\n";
      $str .= "  return(false);\n";
      $str .= "}\n";
    }
    
    return $str;
  }

  function self_validate($val) {
    if (!$this->multiple && $this->valid_e) {
      reset($this->options);
      $o = current($this->options);
      if ($val==$o["value"] || $val==$o) return $this->valid_e;
    }
    return false;
  }

} // end SELECT

?>
