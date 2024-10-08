<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998,1999 SH Online Dienst GmbH
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: perm.inc,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 *
 */ 

class Perm {
  var $classname = "Perm";
  
  ## Hash ("Name" => Permission-Bitmask)
  var $permissions = array ();

  ##
  ## Permission code
  ##
  function check($p) {
    global $auth;

    if (! $this->have_perm($p)) {    
      if (! isset($auth->auth["perm"]) ) {
        $auth->auth["perm"] = "";
      }
      $this->perm_invalid($auth->auth["perm"], $p);
      exit();
    }
  }

  function have_perm($p) {
    global $auth;
    
    if (! isset($auth->auth["perm"]) ) {
      $auth->auth["perm"] = "";
    }
    $pageperm = preg_split("/,/", $p);
    $userperm = preg_split("/,/", $auth->auth["perm"]);
    
    list ($ok0, $pagebits) = $this->permsum($pageperm);
    list ($ok1, $userbits) = $this->permsum($userperm);

    $has_all = (($userbits & $pagebits) == $pagebits);
    if (!($has_all && $ok0 && $ok1) ) {
      return false;
    } else {
      return true;
    }
  }

  ##
  ## Permission helpers.
  ##
  function permsum($p) {
    global $auth;
    
    if (!is_array($p)) {
      return array(false, 0);
    }
    $perms = $this->permissions;
    
    $r = 0;
    foreach($p as $key=>$val) {
      if (!isset($perms[$val])) {
        return array(false, 0);
      }
      $r |= $perms[$val];
    }

    return array(true, $r);
  }
  
  ## Look for a match within an list of strints
  ## I couldn't figure out a way to do this generally using ereg().

  function perm_islisted($perms, $look_for) {
    $permlist = explode( ",", $perms );
    foreach ($permlist as $a=>$b) {
      if( $look_for == $b ) { return true; };
    };
    return false;
  }

  ## Return a complete <select> tag for permission
  ## selection.
  
  function perm_sel($name, $current = "", $class = "") {
    reset($this->permissions);
    
    $ret = sprintf("<select multiple size=10 multiple='multiple' name=\"%s[]\"%s>\n",
      $name,
      ($class!="")?" class=$class":"");
    foreach ($this->permissions as $k=>$v) {
      $ret .= sprintf(" <option%s%s>%s\n",
                $this->perm_islisted($current,$k)?" selected":"",
                ($class!="")?" class=$class":"",
                $k);
    }
    $ret .= "</select>";

    return $ret;
  }

  ##
  ## Dummy Method. Must be overridden by user.
  ##
  function perm_invalid($does_have, $must_have) { 
    printf("Access denied.\n"); 
  }
}
?>
