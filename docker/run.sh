#!/bin/sh

chown -R apache:apache /var/www/localhost/data

exec /usr/sbin/httpd -DFOREGROUND
