#!/bin/sh
db=cdrtool
mysqladmin 	-u root -p$1 -h $2 drop $db
mysqladmin 	-u root -p$1 -h $2 create $db
mysql 		-u root -p$1 -h $2 mysql < ./create_users.mysql
mysql 		-u root -p$1 -h $2 $db   < ./create_tables.mysql
mysql 		-u root -p$1 -h $2 $db   < ./create_data.mysql
mysqladmin 	-u root -p$1 -h $2 reload
