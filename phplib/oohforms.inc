<?php
/* OOH! Forms!
 * 
 * Object Oriented HTML Forms
 *
 * Copyright (c) 1998 by Jay Bloodworth
 * 
 * $Id: oohforms.inc,v 1.2 2005-01-01 16:42:34 adigeo Exp $
 */

class of_element {

  var $name;
  var $value;
  var $multiple;
  var $extrahtml;
  
  function marshal_dispatch($m,$func) {   
    $vname = $this->name;
    global $$vname;
    return $this->$func($$vname);
  }
  
  function self_get($val, $which, &$count) {
  }
  
  function self_show($val, $which) {
    $count = 0;
    print $this->self_get($val, $which, $count);
    return $count;
  }

  function self_get_frozen($val, $which, &$count) {
    return $this->self_get($val, $which, $count);
  }

  function self_show_frozen($val, $which) {
    $count = 0;
    print $this->self_get_frozen($val, $which, $count);
    return $count;
  }

  function self_validate($val) {
    return false;
  }

  function self_get_js($ndx_array) {
  }
  
  function self_print_js($ndx_array) {
    print $this->self_get_js($ndx_array);
  }

  // Note that this function is generally quite simple since
  // most of the work of dealing with different types of values
  // is now done in show_self.  It still needs to be overidable,
  // however, for elements like checkbox that deal with state
  // differently
  function self_load_defaults($val) {
    $this->value = $val;
  }

  // Helper function for compatibility
  function setup_element($a) {
    $cv_tab = array("type"=>"ignore",
        "min_l"=>"minlength",
        "max_l"=>"maxlength",
        "extra_html"=>"extrahtml");
    reset($a);
    while (list($k,$v) = each($a)) {
      if ($cv_tab[$k]=="ignore") continue;
      else $k = ($cv_tab[$k] ? $cv_tab[$k] : $k);
      $this->$k = $v;
    }
  }

} // end ELEMENT

class of_hidden extends of_element {

  var $hidden=1;

  function of_hidden($a) {
    $this->setup_element($a);
  }

  function self_get($val,$which, &$count) {
    $str = "";

    $v = (is_array($this->value) ? $this->value : array($this->value));
    $n = $this->name . ($this->multiple ? "[]" : "");
    reset($v);
    while (list($k,$tv) = each($v)) {
      $str .= "<input type='hidden' name='$n' value='$tv'";
      if ($this->extrahtml)
        $str .=" $this->extrahtml";
      $str .= ">";
    }
    
    return $str;
  }
} // end HIDDEN

class of_reset extends of_element {

  var $src;

  function of_reset($a) {
    $this->setup_element($a);
  }

  function self_get($val, $which, &$count) {
    $str = "<input name='$this->name' type=reset value='$val'";
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
    $str .= ">";
    
    return $str;
  }
} // end RESET

class of_submit extends of_element {

  var $src;

  function of_submit($a) {    
    $this->setup_element($a);
  }

  function self_get($val, $which, &$count) {
    $str = "";
    
    $sv = empty($val) ? $this->value : $val;
    $str .= "<input name='$this->name' value='$sv'";
    if ($this->src) 
      $str .= " type='image' src='$this->src'";
    else 
      $str .= " type='submit'";
    if ($this->extrahtml) 
      $str .= " $this->extrahtml";
    $str .= ">";
    
    return $str;
  }
  
  function self_load_defaults($val) {
    // SUBMIT will not change its value
  }
} // end SUBMIT

class form {
  var $elements;
  var $hidden;
  var $jvs_name;
  var $isfile;
  var $n;

  function get_start($jvs_name="",$method="",$action="",$target="",$form_name="") {
    $PHP_SELF = $_SERVER["PHP_SELF"];
    
    $str = "";
    
    $this->jvs_name = "";
    $this->n = 0;
    if (!$method) $method = "POST";
    if (!$action) $action = $PHP_SELF;
    if (!$target) $target = "_self";

    $str .= "<form name='$form_name' ";
    if ($this->isfile) {
      $str .= " enctype='multipart/form-data'";
      $method = "POST";
    }
    $str .= " method='$method'";
    $str .= " action='$action'";
    $str .= " target='$target'";
    if ($jvs_name) {
      $this->jvs_name = $jvs_name;
      $str .= " onsubmit=\"return ${jvs_name}_Validator(this)\"";
    }
    
    $str .= ">";
    
    return $str;
  }

  function start($jvs_name="",$method="",$action="",$target="",$form_name="") {
    print $this->get_start($jvs_name,$method,$action,$target,$form_name);
  }

  function get_finish($after="",$before="") {
    global $sess;
    $str = "";
    
    if ($this->hidden) {
      reset($this->hidden);
      while (list($k,$elname) = each($this->hidden)) 
        $str .= $this->get_element($elname);
    }
    if (is_object($sess) && ($sess->mode == "get")) {
      $str .= sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\">\n", $sess->name, $sess->id);
    }
    $str .= "</form>";

    if ($this->jvs_name) {
      $jvs_name = $this->jvs_name;
      $str .= "<script language='javascript'>\n<!--\n";
      $str .= "function ${jvs_name}_Validator(f) {\n";

      if (strlen($before))
        $str .= "$before\n";
      reset($this->elements);
      while (list($k,$elrec) = each($this->elements)) {
        $el = $elrec["ob"];
        $str .= $el->self_get_js($elrec["ndx_array"]);
      }
      if (strlen($after))
        $str .= "$after\n";
      $str .= "}\n//-->\n</script>";
    }
    
    return $str;
  }
  
  function finish($after="",$before="") {
    print $this->get_finish($after, $before);
  }
  
  function add_element($el) {

    if (!is_array($el)) 
      return false;
    
    $cv_tab = array("select multiple"=>"select", "image"=>"submit");
    if ($t = $cv_tab[$el["type"]]) 
      $t = ("of_" . $t);
    else
      $t = ("of_" . $el["type"]);
    
    // translate names like $foo[int] to $foo{int} so that they can cause no
    // harm in $this->elements
    # Original match
    # if (preg_match("/(\w+)\[(d+)\]/i", $el[name], $regs)) { 
    if (ereg("([a-zA-Z_]+)\[([0-9]+)\]", $el["name"], $regs)) {
       $el["name"] = sprintf("%s{%s}", $regs[1], $regs[2]);
       $el["multiple"] = true;
    }
    $el = new $t($el);
    $el->type = $t; # as suggested by Michael Graham (magog@the-wire.com)
    if ($el->isfile) 
      $this->isfile = true;
    $this->elements[$el->name]["ob"] = $el;
    if ($el->hidden)
      $this->hidden[] = $el->name;
  }

  function get_element($name,$value=false) {
    $str = "";
    $x   = 0;
    $flag_nametranslation = false;
    
    // see add_element: translate $foo[int] to $foo{int}
#   Original pattern
#   if (preg_match("/(w+)\[(\d+)\]/i", $name, $regs) {
    if (ereg("([a-zA-Z_]+)\[([0-9]+)\]", $name, $regs)) {
       $org_name = $name;
       $name = sprintf("%s{%s}", $regs[1], $regs[2]);
       $flag_nametranslation = true;
    }
    
    if (!isset($this->elements[$name])) 
      return false; 

    if (!isset($this->elements[$name]["which"]))
      $this->elements[$name]["which"] = 0;
   
    $el = $this->elements[$name]["ob"];
    if (true == $falg_nametranslation)
      $el->name = $org_name; 

    if (false == $value) 
       $value = $el->value; 

    if ($this->elements[$name]["frozen"])
      $str .= $el->self_get_frozen($value,$this->elements[$name]["which"]++, $x);
    else
      $str .= $el->self_get($value,$this->elements[$name]["which"]++, $x);
    $this->elements[$name]["ndx_array"][] = $this->n;
    $this->n += $x;
    
    return $str;
  }

  function show_element($name, $value="") {
    print $this->get_element($name, $value);
  }

  function ge($name, $value="") {
    return $this->get_element($name, $value);
  }

  function se($name, $value="") {
    $this->show_element($name, $value);
  }

  function ae($el) {
    $this->add_element($el);
  }

  function validate($default=false,$vallist="") {
    if ($vallist) {
      reset($vallist);
      $elrec = $this->elements[current($vallist)];
    } else {
      reset($this->elements);
      $elrec = current($this->elements);
    }
    while ($elrec) {
      $el = $elrec["ob"];
      if ($res = $el->marshal_dispatch($this->method,"self_validate"))
        return $res; 
      if ($vallist) {
        next($vallist);
        $elrec = $this->elements[current($vallist)];
      } else {
        next($this->elements);
        $elrec = current($this->elements);
      }
    }
    return $default;
  }

  function load_defaults($deflist="") {
    if ($deflist) {
      reset($deflist);
      $elrec = $this->elements[current($deflist)];
    } else {
      reset($this->elements);
      $elrec = current($this->elements);
    }
    while ($elrec) {
      $el = $elrec["ob"];
      $el->marshal_dispatch($this->method,"self_load_defaults");
      $this->elements[$el->name]["ob"] = $el;  // no refs -> must copy back
      if ($deflist) {
        next($deflist);
        $elrec = $this->elements[current($deflist)];
      } else {
        next($this->elements);
        $elrec = current($this->elements);
      }
    }
  }

  function freeze($flist="") {
    if ($flist) {
      reset($flist);
      $elrec = $this->elements[current($flist)];
    } else {
      reset($this->elements);
      $elrec = current($this->elements);
    }
    while ($elrec) {
      $el = $elrec["ob"];
      $this->elements[$el->name]["frozen"]=1;
      if ($flist) {
        next($flist);
        $elrec = $this->elements[current($flist)];
      } else {
        next($this->elements);
        $elrec = current($this->elements);
      }
    }
  }

} /* end FORM */

include($_PHPLIB["libdir"] . "of_text.inc");
include($_PHPLIB["libdir"] . "of_select.inc");
include($_PHPLIB["libdir"] . "of_radio.inc");
include($_PHPLIB["libdir"] . "of_checkbox.inc");
include($_PHPLIB["libdir"] . "of_textarea.inc");
include($_PHPLIB["libdir"] . "of_file.inc");

?>
