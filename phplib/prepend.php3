<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998,1999 SH Online Dienst GmbH
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: prepend.php3,v 1.1.1.1 2004-04-29 10:18:10 adigeo Exp $
 *
 */ 

if (!is_array($_PHPLIB)) {
# Aren't we nice? We are prepending this everywhere 
# we require or include something so you can fake
# include_path  when hosted at provider that sucks.
  $_PHPLIB["libdir"] = ""; 
}

require($_PHPLIB["libdir"] . "db_mysql.inc");  /* Change this to match your database. */
require($_PHPLIB["libdir"] . "db_sybase.inc");  /* Change this to match your database. */
require($_PHPLIB["libdir"] . "ct_sql.inc");    /* Change this to match your data storage container */
require($_PHPLIB["libdir"] . "session.inc");   /* Required for everything below.      */
require($_PHPLIB["libdir"] . "auth.inc");      /* Disable this, if you are not using authentication. */
require($_PHPLIB["libdir"] . "perm.inc");      /* Disable this, if you are not using permission checks. */
require($_PHPLIB["libdir"] . "user.inc");      /* Disable this, if you are not using per-user variables. */
require($_PHPLIB["libdir"] . "oohforms.inc");

/* Additional require statements go below this line */

/* Additional require statements go before this line */

require($_PHPLIB["libdir"] . "local.inc");     /* Required, contains your local configuration. */

require($_PHPLIB["libdir"] . "page.inc");      /* Required, contains the page management functions. */
?>
