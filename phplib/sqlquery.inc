<?php
/*
 * PHP Base Library
 *
 * Copyright (c) 1998,1999 SH Online Dienst GmbH
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: sqlquery.inc,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 *
 */ 

class Sql_Query {
  var $classname = "Sql_Query";  ## Persistence Support
  var $persistent_slots = array(
    "conditions", "input_size", "input_max", "method", "lang", "translate", "container", "variable"
  );
  
  var $conditions = 1;      ## Allow for that many Query Conditions
  var $input_size = 20;     ## Used in text input field creation
  var $input_max  = 80;

  var $method     = "post"; ## Generate get or post form...
  var $lang       = "en";   ## HTML Widget language

  var $translate = "on";    ## If set, translate column names
  var $container = "on";    ## If set, create a container table
  var $variable  = "on";    ## if set, create variable size buttons
  
  ## HTML Widget dictionary
  var $dict = array(
    "de" => array(
      "searchfor" => "Suchen nach:",
      "and"       => "und",
      "or"        => "oder",
      "like"      => "enthält",
      "reset"     => "Neu",
      "submit"    => "Ausführen",
      "less"      => "Weniger",
      "more"      => "Mehr"
    ),
    
    "en" => array(
      "searchfor" => "Search for:",
      "and"       => "and",
      "or"        => "or",
      "like"      => "contains",
      "reset"     => "Reset Query",
      "submit"    => "Submit Query",
      "less"      => "Fewer",
      "more"      => "More"
    )
  );

  ## SQL comparision dictionary
  var $compare = array(
        "like" => "like",
        ">"    => ">",
        "<"    => "<",

        ">="   => ">=",
        "<="   => "<=",
        "="    => "=",
        "<>"   => "<>"
      );


  function start($class = "") {
  }

  ## selection:
  ##
  ## Create a <select> tag of the class $class with the name $name.
  ## The tag contains the options named in array $option. If $trans
  ## is true, $option is exspected to be a hash of 
  ## "long name " => "sqlname" pairs. The option matching $old
  ## is created with the attribute "selected".
  ##
  function selection($name, $option, $old = "", $trans = "", $class = "") {
    $res  = "";
    $res .= sprintf("<select%s name=\"%s\">\n",
              ($class)?" class=$class":"",
              $name);
    reset($option);
    while(list($k, $v) = each($option)) {
      if (($trans == "" && $old == $v)
       || ($trans != "" && $old == $k)) {
        $selected = " selected";
      } else {
        $selected = "";
      }
      
      $res .= sprintf("<option value=\"%s\"%s%s>%s\n",
                ($trans)?$k:$v,
                ($class)?" class=$class":"",
                ($selected)?" selected":"",
                $v);
    }
    $res .= sprintf("      </select>");
    
    return $res;
  }

  ## fieldname:
  ##
  ## Given a basename $base, and attribute name $att and an attribute
  ## number $num, this functions returns an input field name
  ## $base[$name_$num].
  ##
  ## This construct can be imported into a function namespace with a
  ## single global instruction and the field name can be easily
  ## exploded into component names and numbers.

  function makename($base, $att, $num) {
    return sprintf("%s[%s_%d]", $base, $att, $num);
  }

  ## form:
  ##
  ## Draw SQL Query selection form.
  ##
  function form($base, $option, $class = "", $target = "") {
    global $sess;

  ##
	## load the HTML results of this function into $res.
	##
    $res  = "";
    
    ## A hack. We cannot do language dependent initialisation of
    ## static values.
    if (isset($this->compare["like"])) {
      $this->compare["like"] = $this->dict[$this->lang]["like"];
    }
    
    ## Prepare a self-directed container form
    if ($this->container) {
      $res .= sprintf("<table border=1%s><tr%s><td>\n",
        ($class)?" class=$class":"",
        ($class)?" class=$class":"",
        ($class)?" class=$class":"");
    }
    $res .= sprintf("<form method=\"%s\" action=\"%s\">\n",
      $this->method, 
      ($target)?$target:$sess->self_url());
    
    ## Prepare the inner table, laying out the selection elements
    $res .= sprintf("<table%s>\n", ($class)?" class=$class":"");

    ## Build $this->conditions many selection elements    
    for ($i=1; $i<= $this->conditions; $i++) {
      $res .= sprintf(" <tr%s>\n",   ($class)?" class=$class":"");

      ## Build conjunction (first row does not have a conjunction)
      if ($i == 1) {
        $res .= sprintf("  <td%s>%s</td>\n", 
          ($class)?" class=$class":"", 
          $this->dict[$this->lang]["searchfor"]);
      } else {
        $res .= sprintf("  <td%s>%s</td>\n", 
          ($class)?" class=$class":"", 
          $this->selection($this->makename($base, "conj", $i),
                           array("and" => $this->dict[$this->lang]["and"], "or" => $this->dict[$this->lang]["or"]),
                           $GLOBALS[$base]["conj_".$i], 
                           "on",
                           $class));
      }
      
      ## Build field selection
      $res .= sprintf("  <td%s>%s</td>\n",
        ($class)?" class=$class":"",
        $this->selection(
          $this->makename($base, "sel", $i), 
          $option, 
          $GLOBALS[$base]["sel_".$i], 
          $this->translate, 
          $class));
        
      ## Build comparison selection
      $res .= sprintf("  <td%s>%s</td>\n",
        ($class)?" class=$class":"",
        $this->selection(
          $this->makename($base, "comp", $i), 
          $this->compare, 
          $GLOBALS[$base]["comp_".$i], 
          "on", 
          $class));
      ## Create text input field.
      $res .= sprintf("  <td%s><input type=\"text\" name=\"%s\" value=\"%s\" size=%d maxlength=%d%s></td>\n",
        ($class)?" class=$class":"",
        $this->makename($base, "input", $i),
        $GLOBALS[$base]["input_".$i],
        $this->input_size,
        $this->input_max,
        ($class)?" class=$class":"");

      $res .= sprintf(" </tr>\n");
    }

    ## Create variable size buttons
    $res .= sprintf(" <tr%s>\n",  ($class)?" class=$class":"");
    $res .= sprintf("  <td%s>&nbsp;</td>\n", ($class)?" class=$class":"");
    
    if ($this->variable) {
      $res .= sprintf("  <td%s><input type=\"submit\" name=\"%s\" value=\"%s\">&nbsp;",
        ($class)?" class=$class":"",
        $this->makename($base, "more", 0),
        $this->dict[$this->lang]["more"]);
      $res .= sprintf("<input type=\"submit\" name=\"%s\"value=\"%s\"></td>\n",
        $this->makename($base, "less", 0),
        $this->dict[$this->lang]["less"]);
    } else {
      $res .= sprintf("  <td%s>&nbsp;</td>\n", ($class)?" class=$class":"");
    }
    
    $res .= sprintf("  <td%s>&nbsp;</td>\n", ($class)?" class=$class":"");
    $res .= sprintf("  <td%s><input type=\"reset\" value=\"%s\">&nbsp;",
      ($class)?" class=$class":"",
      $this->dict[$this->lang]["reset"]);
    $res .= sprintf("<input type=\"submit\" name=\"%s\"value=\"%s\"></td>\n",
      $this->makename($base, "submit", 0),
      $this->dict[$this->lang]["submit"]);
    
    $res .= sprintf(" </tr>\n");
    $res .= sprintf("</table>\n");
    
    $res .= sprintf("</form>\n");
    if ($this->container) {
      $res .= sprintf("</td></tr></table>\n");
    }
    $res .= sprintf("<!-- End %s generated query form -->\n", $this->classname);

    return $res;
  }
  
  ## plain_where:
  ##
  ## Given a base variable name, creates a condition suitable for
  ## the where clause of a SQL query.
  ##
  function plain_where($base) {
    for($i=1; $i<=$this->conditions; $i++) {
      ## Only create conditions for used input fields
      if ($GLOBALS[$base]["input_".$i] == "")
        continue;

      ## If necessary, add conjunction
      if ($q != "")
        $q .= sprintf(" %s ", $GLOBALS[$base]["conj_".$i]);
      
      ## Handle "like"
      if ($GLOBALS[$base]["comp_".$i] == "like")
        $v = "%".$GLOBALS[$base]["input_".$i]."%";
      else
        $v = $GLOBALS[$base]["input_".$i];

      ## Create subcondition
      $q .= sprintf("%s %s '%s'",
              $GLOBALS[$base]["sel_".$i],
              $GLOBALS[$base]["comp_".$i],
              $v);
    }
    
    if (!$q) {
      $q = "1=0";
    }
    
    return "( $q )";
  }

  ## translated_plain_where:
  ##
  ## Given a base variable name, creates a translated version of
  ## the where clause of a SQL query.
  ##
  function translated_plain_where($base, $field) {
    for($i=1; $i<=$this->conditions; $i++) {
      ## Only create conditions for used input fields
      if ($GLOBALS[$base]["input_".$i] == "")
        continue;

      ## If necessary, add conjunction
      if ($q != "")
        $q .= sprintf(" %s ", $this->dict[$this->lang][$GLOBALS[$base]["conj_".$i]]);
      
      ## Handle "like"
      if ($GLOBALS[$base]["comp_".$i] == "like")
        $c = $this->dict[$this->lang][$GLOBALS[$base]["comp_".$i]];
      else
        $c = $this->compare[$GLOBALS[$base]["comp_".$i]];

      ## Create subcondition
      $q .= sprintf("%s %s '%s'",
              $field[$GLOBALS[$base]["sel_".$i]],
              $c,
              $GLOBALS[$base]["input_".$i]);
    }
    
    if (!$q) {
      $q = "1=0";
    }
    
    return "( $q )";
  }
  
  ## where:
  ##
  ## Same as plain_where(), but also inspects the submit button
  ## used to submit the query. Changes $this->conditions appropriately.
  function where($base, $incr = 1) {
    if (isset($GLOBALS[$base]["less_0"]))
      $this->conditions -= $incr;
    
    if (isset($GLOBALS[$base]["more_0"]))
      $this->conditions += $incr;
    
    if ($this->conditions < 1)
      $this->conditions = 1;
    
    return $this->plain_where($base);
  }
}
?>
