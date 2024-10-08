<?php
/* OOHForms: text
 *
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_text.inc,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 */

class of_text extends of_element {

  var $maxlength;
  var $minlength;
  var $length_e;
  var $valid_regex;
  var $valid_icase;
  var $valid_e;
  var $valid_minlength_error;
  var $valid_e2;
  var $valid_e2_error;
  var $pass;
  var $size;
  var $class;

  // Constructor
  function __construct($a) {
    $this->setup_element($a);
    if ($a["type"]=="password")
      $this->pass=1;
  }

  function self_get($val,$which, &$count) {
    $str = "";
    
    if (is_array($this->value))
      $v = htmlspecialchars($this->value[$which]);
    else 
      $v = htmlspecialchars($this->value);
    $n = $this->name . ($this->multiple ? "[]" : "");
    $str .= "<input name='$n' value=\"$v\"";
    $str .= ($this->pass)? " type='password'" : " type='text'";
    //$str .= " class=$class";
    if ($this->maxlength)
      $str .= " maxlength='$this->maxlength'";
    if ($this->size) 
      $str .= " size='$this->size'";
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
    $str .= "autocomplete='off'>";
    
    $count = 1;
    return $str;
  }

  function self_get_frozen($val,$which, &$count) {
    $str = "";
    
    if (is_array($this->value))
      $v = $this->value[$which];
    else 
      $v = $this->value;
    $n = $this->name . ($this->multiple ? "[]" : "");
    $str .= "<input type='hidden' name='$n' value=\"$v\">\n";
    $str .= "$v\n";
    
    $count = 1;
    return $str;
  }

  function self_get_js($ndx_array) {
    $str = "";
   if (is_array($ndx_array)) { //Added by DaveB
    foreach($ndx_array as $k=>$n) {
      if (!empty($this->length_e)) {
        $str .= "if (f.elements[\"$n\"].value.length < $this->minlength) {\n";
        $str .= "  alert(\"$this->length_e\");\n";
        $str .= "  f.elements[$n].focus();\n";
        $str .= "  return(false);\n}\n";
      }
      if (!empty($this->valid_e)) {
        $flags = (!empty($this->icase) ? "gi" : "g");
        $str .= "if (window.RegExp) {\n";
        $str .= "  var reg = new RegExp(\"$this->valid_regex\",\"$flags\");\n";
        $str .= "  if (!reg.test(f.elements[$n].value)) {\n";
        $str .= "    alert(\"$this->valid_e\");\n";
        $str .= "    f.elements[$n].focus();\n";
        $str .= "    return(false);\n";
        $str .= "  }\n}\n";
      }
    }
  }    
    return $str;
  }

  function self_validate($val) {
    if (!is_array($val)) $val = array($val);
    foreach($val as $k=>$v) {
      if ($this->length_e && (strlen($v) < $this->minlength)) {
        return $this->valid_e;
	// new added by adigeo 2001-05-28
      if ($this->valid_e2_error && (($this->icase &&
            !preg_match("/$this->valid_e2/i",$v)) ||
           (!$this->icase &&
            !preg_match("/$this->valid_e2/",$v))))
        return $this->valid_e2_error;

    }
    return false;
  } 

} // end TEXT

?>
