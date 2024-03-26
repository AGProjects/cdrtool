<?php
//define_syslog_variables();
openlog("cdrtool", LOG_PID, LOG_LOCAL0);

ini_set('register_globals','on');
ini_set('max_execution_time','120');
ini_set('magic_quotes_gpc','Off');

$lib_dirs=$_PHPLIB['libdir'].":".
          $CDRTool['Path']."/library:".
          "/etc/cdrtool/local:".
          ini_get('include_path');

ini_set('include_path', $lib_dirs);

if (!is_array($_PHPLIB)) $_PHPLIB["libdir"] = "";
require "logger.php";

require("db_mysqli.inc");  /* Change this to match your database. */

require("ct_sql.inc");    /* Change this to match your data storage container */
require("session.inc");   /* Required for everything below.      */
require("auth.inc");      /* Disable this, if you are not using authentication. */
require("perm.inc");      /* Disable this, if you are not using permission checks. */
require("user.inc");      /* Disable this, if you are not using per-user variables. */
require("oohforms.inc");
require("local.inc");     /* Required, contains your local configuration. */
require("page.inc");      /* Required, contains the page management functions. */
?>
