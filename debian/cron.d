# Normalization
*/5 * * * * root php /var/www/CDRTool/scripts/normalize.php          >/dev/null

# Check quota
*/5  * * * * root php /var/www/CDRTool/scripts/SER/quotaCheck.php    >/dev/null
  0  2 1 * * root php /var/www/CDRTool/scripts/SER/quotaReset.php    >/dev/null 
 10  3 1 * * root php /var/www/CDRTool/scripts/SER/quotaDeblock.php  >/dev/null 
 
# Purge SIP trace table 
  20 3 * * * root php /var/www/CDRTool/scripts/SER/purgeSIPTrace.php >/dev/null 

# Statistics
*/5  * * * * root php /var/www/CDRTool/scripts/buildStatistics.php   >/dev/null

# Next two jobs are only used when using a central radacct table:
#  0  3 1 * * root php /var/www/CDRTool/scripts/SER/rotateTables.php >/dev/null 
#  0  4 * * * root php /var/www/CDRTool/scripts/purgeTables.php      >/dev/null
