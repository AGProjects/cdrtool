#!/bin/sh

db=cdrtool

if test $# != 2; then
	echo "setup_mysql.sh DB_ROOT_PASSWORD DB_HOST"
	exit;
fi

grep \'PASSWORD\' create_users.mysql | grep -i grant > /dev/null

if test $? = 0  ; then
    echo "Please edit create_users.mysql and replace the PASSWORD with a new password and try again"
    exit 1
fi

grep \'PRIVATE_IP_NETWORK\' create_users.mysql | grep -i grant > /dev/null

if test $? = 0  ; then
    echo "Please edit create_users.mysql and replace the PRIVATE_IP_NETWORK with real addresses and try again"
    exit 1
fi

mysqladmin 	-u root -p$1 -h $2 create $db

if test $? = 0  ; then
	mysql 		-u root -p$1 -h $2 mysql < ./create_users.mysql
	mysql 		-u root -p$1 -h $2 $db   < ./create_tables.mysql
	mysql 		-u root -p$1 -h $2 $db   < ./create_data.mysql
else 
	echo "Failed to create $db database on host $2"
	exit 1
fi

exit 0
