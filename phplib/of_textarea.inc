<?php
/* OOHForms: textarea
 *
 * Copyright (c) 1998 by Jay Bloodworth
 *
 * $Id: of_textarea.inc,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 */

class of_textarea extends of_element {

  var $rows;
  var $cols;
  var $wrap;

  // Constructor
  function __construct($a) {
    $this->setup_element($a);
  }

  function self_get($val,$which, &$count) {
    $str  = "";
    $str .= "<textarea name='$this->name'";
    $str .= " rows='$this->rows' cols='$this->cols'";
    if ($this->wrap) 
      $str .= " wrap='$this->wrap'";
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
     $str .= ">" . htmlspecialchars($this->value, ENT_COMPAT,'ISO-8859-1', true) ."</textarea>";
    
    $count = 1;
    return $str;
  }

  function self_get_frozen($val,$which, &$count) {
    $str  = "";
    $str .= "<input type='hidden' name='$this->name'";
    $str .= " value='$this->value'>\n";
    $str .= "<table border=0><tr><td>\n";
    $str .=  nl2br($this->value);
    $str .= "\n</td></tr></table>\n";
    
    $count = 1;
    return $str;
  }

} // end TEXTAREA

?>
