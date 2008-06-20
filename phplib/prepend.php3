<?php

ini_set('register_globals','on');
ini_set('max_execution_time','120');

$lib_dirs=ini_get('include_path').":".
          $_PHPLIB['libdir'].":".
          $CDRTool['Path']."/library:".
          "/etc/cdrtool/local";

ini_set('include_path', $lib_dirs);

if (!is_array($_PHPLIB)) {
# Aren't we nice? We are prepending this everywhere 
# we require or include something so you can fake
# include_path  when hosted at provider that sucks.
  $_PHPLIB["libdir"] = ""; 
}

require("db_mysql.inc");  /* Change this to match your database. */
require("ct_sql.inc");    /* Change this to match your data storage container */
require("session.inc");   /* Required for everything below.      */
require("auth.inc");      /* Disable this, if you are not using authentication. */
require("perm.inc");      /* Disable this, if you are not using permission checks. */
require("user.inc");      /* Disable this, if you are not using per-user variables. */
require("oohforms.inc");
require("local.inc");     /* Required, contains your local configuration. */
require("page.inc");      /* Required, contains the page management functions. */
?>
