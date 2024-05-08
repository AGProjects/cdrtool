# Normalization
*/5 * * * * root [ -x /var/www/CDRTool/scripts/normalize.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/normalize.php  >/dev/null ;fi

# Check quota
*/5 * * * * root [ -x /var/www/CDRTool/scripts/OpenSIPS/quotaCheck.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/OpenSIPS/quotaCheck.php >/dev/null; fi
  0 2 1 * * root [ -x  /var/www/CDRTool/scripts/OpenSIPS/quotaReset.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/OpenSIPS/quotaReset.php    >/dev/null ; fi
 10 0 * * * root [ -x  /var/www/CDRTool/scripts/OpenSIPS/quotaDailyReset.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/OpenSIPS/quotaDailyReset.php >/dev/null ; fi
 
# Purge SIP trace table
 20 3 * * * root [ -x  /var/www/CDRTool/scripts/OpenSIPS/purgeSIPTrace.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/OpenSIPS/purgeSIPTrace.php >/dev/null ; fi

# Statistics
*/5  * * * * root [ -x  /var/www/CDRTool/scripts/buildStatistics.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/buildStatistics.php >/dev/null ;fi

# Next two jobs are only used when using a central radacct table:
#  0  3 1 * * root [ -x  /var/www/CDRTool/scripts/OpenSIPS/rotateTables.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/OpenSIPS/rotateTables.php >/dev/null ; fi
#  0  4 * * * root [ -x  /var/www/CDRTool/scripts/purgeTables.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/purgeTables.php >/dev/null; fi

# Send email with last missed calls to SIP subscribers
15 2 * * * root [ -x  /var/www/CDRTool/scripts/notifyLastSessions.php ] && if [ ! -d /run/systemd/system ]; then /var/www/CDRTool/scripts/OpenSIPS/notifyLastSessions.php >/dev/null ; fi

# Import rating tables
15 5 * * * root [ -x  /var/www/CDRTool/scripts/importRatingTables.php ] && if [ ! -d /run/systemd/system ]; then  /var/www/CDRTool/scripts/importRatingTables.php >/dev/null ;fi
