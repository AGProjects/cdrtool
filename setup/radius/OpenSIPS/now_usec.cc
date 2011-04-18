#include <my_global.h>
#include <my_sys.h>
#include <mysql.h>

#include <stdio.h>
#include <sys/time.h>
#include <time.h>
#include <unistd.h>

extern "C" {
   my_bool now_usec_init(UDF_INIT *initid, UDF_ARGS *args, char *message);
   char *now_usec(
               UDF_INIT *initid,
               UDF_ARGS *args,
               char *result,
               unsigned long *length, char *is_null, char *error);
}

my_bool now_usec_init(UDF_INIT *initid, UDF_ARGS *args, char *message) {
   return 0;
}

char *now_usec(UDF_INIT *initid, UDF_ARGS *args, char *result,
               unsigned long *length, char *is_null, char *error) {

  struct timeval tv;
  struct tm* ptm;
  char time_string[20]; /* e.g. "2006-04-27 17:10:52" */
  char *usec_time_string = result;
  time_t t;

  /* Obtain the time of day, and convert it to a tm struct. */
  gettimeofday (&tv, NULL);
  t = (time_t)tv.tv_sec;
  ptm = localtime (&t);

  /* Format the date and time, down to a single second.  */
  strftime (time_string, sizeof (time_string), "%Y-%m-%d %H:%M:%S", ptm);

  /* Print the formatted time, in seconds, followed by a decimal point
 *      and the microseconds.  */
  sprintf(usec_time_string, "%s.%06ld\n", time_string, tv.tv_usec);

  *length = 26;

  return(usec_time_string);
}
