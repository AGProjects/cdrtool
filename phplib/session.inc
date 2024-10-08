<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998,1999 SH Online Dienst GmbH
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: session.inc,v 1.4 2005-01-14 10:42:48 adigeo Exp $
 *
 */

class Session {
  var $classname = "Session";         ## Needed for object serialization.

  ## Define the parameters of your session by either overwriting
  ## these values or by subclassing session (recommended).

  var $magic = "";                    ## Some string you should change.
  var $mode = "cookie";               ## We propagate session IDs with cookies
  var $fallback_mode;                 ## If this doesn't work, fall back...
  var $lifetime = 0;                  ## 0 = do session cookies, else minutes

  var $cookie_domain = "";            ## If set, the domain for which the
                                      ## session cookie is set.

  var $gc_time  = 1440;               ## Purge all session data older than 1440 minutes.
  var $gc_probability = 1;            ## Garbage collect probability in percent

  var $auto_init = "";                ## Name of the autoinit-File, if any.
  var $secure_auto_init = 1;          ## Set to 0 only, if all pages call
                                      ## page_close() guaranteed.

  var $allowcache = "private";        ## Set this to 'private' to allow private
                                      ## caching; set this to 'public' to allow
                                      ## public caching; set this to 'no' to
                                      ## never cache the page.
  var $allowcache_expire = 0;      ## If you allowcache, data expires in this
                                      ## many minutes.
  var $that_class = "";               ## Name of data storage container

  ##
  ## End of parameters.
  ##

  var $name;                          ## Session name
  var $id;                            ## Unique Session ID
  var $that;

  var $pt = array();                  ## This Array contains the registered things
  var $in = false;                    ## Marker: Did we already include the autoinit file?

  ## register($things):
  ##
  ## call this function to register the things that should become persistent

  function register($things) {
    foreach (explode(",",$things) as $thing) {
      $thing=trim($thing);
      if ( $thing ) {
        $this->pt[$thing] = true;
      }
    }
  }

  function is_registered($name) {
    if (isset($this->pt[$name]) && $this->pt[$name] == true)
      return true;
    return false;
  }

  function unregister($things) {
    foreach (explode(",",$things) as $thing) {
      $thing = trim($thing);
      if ($thing) {
        unset($this->pt[$thing]);
      }
    }
  }

  function erase($things) {
    foreach (explode(",",$things) as $thing) {
      $thing = trim($thing);
      if ($thing) {
        $GLOBALS[$thing]=false;
      }
    }
  }


  ## get_id():
  ##
  ## Propagate the session id according to mode and lifetime.
  ## Will create a new id if necessary. To take over abandoned sessions,
  ## one may provide the new session id as a parameter (not recommended).

  function get_id($id = "") {
    $newid = true;
    //print "$id";
    $this->name = $this->cookiename==""?$this->classname:$this->cookiename;

    if ( "" == $id ) {
      $newid=false;
      switch ($this->mode) {
        case "get":
          if ("" == ($id = isset($_GET[$this->name]) ? $_GET[$this->name] : ""))
            $id = isset($_POST[$this->name]) ? $_POST[$this->name] : "";
        break;
        case "cookie":
          $id = isset($_COOKIE[$this->name]) ? $_COOKIE[$this->name] : "";
        break;
        default:
          die("Your browser has cookies disabled");
        break;
      }
    }

    if ( "" == $id ) {
      $newid=true;
      $id = $this->that->ac_newid(md5(uniqid($this->magic)), $this->name);
    }

    switch ($this->mode) {
      case "cookie":
        if ( $newid && ( 0 == $this->lifetime ) ) {
          SetCookie($this->name, $id, 0, "/", $this->cookie_domain);
        }
        if ( 0 < $this->lifetime ) {
          SetCookie($this->name, $id, time()+$this->lifetime*60, "/", $this->cookie_domain);
        }
      break;
      case "get":
        if ( isset($_SERVER['QUERY_STRING']) ) {
          $_SERVER['QUERY_STRING'] = preg_replace(
            "/(^|&)".quotemeta(urlencode($this->name))."=".$id."(&|$)/",
            "\\1", $_SERVER['QUERY_STRING']);
        }
      break;
      default:
        ;
      break;
    }

    $this->id = $id;
  }

  ## put_id():
  ##
  ## Stop using the current session id (unset cookie, ...) and
  ## abandon a session.
  function put_id() {

    $this->name = $this->cookiename==""?$this->classname:$this->cookiename;

    switch ($this->mode) {
      case "inline":
        die("Your browser has cookies disabled");
      break;

      case "get":
        die("Your browser has cookies disabled");
      break;

      default:
            SetCookie($this->name, "", 0, "/", $this->cookie_domain);
            $_COOKIE[$this->name] = "";
      break;
    }
  }

  ## delete():
  ##
  ## Delete the current session record and put the session id.

  function delete() {
      $this->that->ac_delete($this->id, $this->name);
      $this->put_id();
  }

  ## url($url):
  ##
  ## Helper function: returns $url concatenated with the current
  ## session $id.

  function url($url){
    //$url=preg_replace("/[&?]+$/", "", $url);

    switch ($this->mode) {
      case "get":

        //$url .= ( strpos($url, "?") != false ?  "&" : "?" ).
          //      urlencode($this->name)."=".$this->id;

      break;
      default:
        ;
      break;
    }
    return $url;
  }

  function purl($url) {
    print $this->url($url);
  }

  function self_url() {
    return $this->url($_SERVER['PHP_SELF'].
           ((isset($_SERVER['QUERY_STRING']) && ("" != $_SERVER['QUERY_STRING'])) ? "?".$_SERVER['QUERY_STRING'] : ""));
  }

  function pself_url() {
    print $this->self_url();
  }

  function hidden_session()
  {
    printf("<input type=\"hidden\" name=\"%s\" value=\"%s\">\n", $this->name, $this->id);
  }

  function add_query($qarray) {

    if ((isset($_SERVER['QUERY_STRING']) && ("" != $_SERVER['QUERY_STRING']))
     || ($this->mode == "get")) {
      $sep_char = "&";
    } else {
      $sep_char = "?";
    }

    $qstring = "";
    while (list($k, $v) = each($qarray)) {
      $qstring .= $sep_char . urlencode($k) . "=" . urlencode($v);
      $sep_char = "&";
    }

    return $qstring;
  }

  function padd_query($qarray) {
    print $this->add_query($qarray);
  }

  ## serialize($var,&$str):
  ##
  ## appends a serialized representation of $$var
  ## at the end of $str.
  ##
  ## To be able to serialize an object, the object must implement
  ## a variable $classname (containing the name of the class as string)
  ## and a variable $persistent_slots (containing the names of the slots
  ## to be saved as an array of strings).
  ##
  ## You don't need to know...
  function serialize($var, &$str) {
    static $t,$l,$k;

    #print "$str <br/>";
    ## Determine the type of $$var
    eval("\$t = gettype(\$$var);");
    switch ( $t ) {

      case "array":
        ## $$var is an array. Enumerate the elements and serialize them.
        eval("reset(\$$var); \$l = gettype(list(\$k)=each(\$$var));");
        $str .= "\$$var = array(); ";
        while ( "array" == $l ) {
          ## Structural recursion
          $this->serialize($var."['".preg_replace("/([\\\'])/", "\\\\1", $k)."']", $str);
          eval("\$l = gettype(list(\$k)=each(\$$var));");
        }

      break;
      case "object":
        ## $$var is an object. Enumerate the slots and serialize them.
        eval("\$k = \$$var->classname; \$l = reset(\$$var->persistent_slots);");
        if ($k) {
          $str.="\$$var = new $k; ";
          while ( $l ) {
            ## Structural recursion.
            $this->serialize($var."->".$l,$str);
            eval("\$l = next(\$$var->persistent_slots);");
          }
        }
      break;
      default:
        ## $$var is an atom. Extract it to $l, then generate code.
        eval("\$l = \$$var;");
        $str.="\$$var = '".preg_replace("/([\\\\'])/", "\\\\1", $l)."'; ";
      break;
    }
  }

  function get_lock() {
      $this->that->ac_get_lock();
  }

  function release_lock() {
      $this->that->ac_release_lock();
  }

  ## freeze():
  ##
  ## freezes all registered things ( scalar variables, arrays, objects ) into
  ## a database table

  function freeze() {
    global $auth;
    unset($auth->db);  // PDO objects can't be serialized and we don't need this anyway
    $str="";
        $arr=array();
        #print_r($this->in);
    //$this->serialize("this->in",$str);
    //$this->serialize("this->pt",$str);
    foreach ($this->pt as $thing=>$val) {
      $thing=trim($thing);
      if ( $thing ) {
        //$this->serialize("GLOBALS['".$thing."']", $str);  //php3 serialize was broken, use our own.
	$arr[$thing] = $GLOBALS[$thing];
      }
    }
        $str=serialize($arr);
        //print_r(unserialize($str));
    $r = $this->that->ac_store($this->id, $this->name, $str);
    $this->release_lock();

    if (!$r) $this->that->ac_halt("Session: freeze() failed.");
  }

  ## thaw:
  ##
  ## Reload frozen variables from the database and microwave them.

  function thaw() {
    $this->get_lock();

    $vals = $this->that->ac_get_value($this->id, $this->name);

	$arr = unserialize($vals);   // new serialised data req. php => 4.07
	if (is_array($arr)) {
	    foreach($arr as $k=>$v) {
		$this->pt[$k]=1;
		$GLOBALS[$k]=$v;
	    }
	}
	//print "<pre>";print_r($GLOBALS['auth']);print "</pre>";
    //eval(sprintf(";%s", $vals));
  }

  ##
  ## Garbage collection
  ##
  ## Destroy all session data older than this
  ##
	// commented out on 18/02/2004
  #function gc() {
  #    $this->that->ac_gc($this->gc_time, $this->name);
  #}

  ##
  ## Variable precedence functions
  ##
  function reimport_get_vars() {
    $this->reimport_any_vars("_GET");
  }

  function reimport_post_vars() {
    $this->reimport_any_vars("_POST");
  }

  function reimport_cookie_vars() {
    $this->reimport_any_vars("_COOKIE");
  }

  function reimport_any_vars($arrayname) {
    global $$arrayname;

    if (!is_array($$arrayname))
      return;

    foreach ($$arrayname as $key=>$val) {
      $GLOBALS[$key] = $val;
    }
  }

  ##
  ## All this is support infrastructure for the start() method
  ##

  function set_container(){
    $name = $this->that_class;
    $this->that = new $name;

    $this->that->ac_start();
  }

  function set_tokenname(){
    $this->name = $this->cookiename==""?$this->classname:$this->cookiename;
  }

  function release_token(){
    if (   isset($this->fallback_mode)
    && ( "get" == $this->fallback_mode )
    && ( "cookie" == $this->mode )
    && ( ! isset($_COOKIE[$this->name]) ) ) {
      if ( isset($_GET[$this->name]) ) {
        $this->mode = $this->fallback_mode;
      } else {
        header("Status: 302 Moved Temporarily");
        $this->get_id($sid);
	$this->mode = $this->fallback_mode;
        if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ){
        ## You will need to fix suexec as well, if you use Apache and CGI PHP
          $PROTOCOL='https';
        } else {
          $PROTOCOL='http';
        }
        header("Location: ". $PROTOCOL. "://".$_SERVER['HTTP_HOST'].$this->self_url());
        exit;
      }
    }
  }

  function put_headers() {
    # Allowing a limited amount of caching, as suggested by
    # Padraic Renaghan on phplib@shonline.de.
    # Note that in HTTP/1.1 the Cache-Control headers override the Expires
    # headers and HTTP/1.0 ignores headers it does not recognize (e.g,
    # Cache-Control). Mulitple Cache-Control directives are split into
    # mulitple headers to better support MSIE 4.x.
    switch ($this->allowcache) {

      case "public":
        $exp_gmt = gmdate("D, d M Y H:i:s", time() + $this->allowcache_expire * 60) . " GMT";
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";
        header("Expires: " . $exp_gmt);
        header("Last-Modified: " . $mod_gmt);
        header("Cache-Control: public");
        header("Cache-Control: max-age=" . $this->allowcache_expire * 60);
      break;

      case "jl";
	break;

      case "private":
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . $mod_gmt);
        header("Cache-Control: private");
        header("Cache-Control: max-age=" . $this->allowcache_expire * 60);
      break;

      default:
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
      break;
    }
  }

  ##
  ## Garbage collection
  ##
  ## Destroy all session data older than this
  ##
  function gc() {
    srand(time());
    if ((rand()%100) < $this->gc_probability) {
      $this->that->ac_gc($this->gc_time, $this->name);
    }
  }


  ##
  ## Initialization
  ##

  function start($sid = "") {
    $this->set_container();
    $this->set_tokenname();
    $this->release_token($sid);
    $this->put_headers();
    $this->get_id($sid);
    $this->thaw();
    $this->gc();
  }

}
?>
