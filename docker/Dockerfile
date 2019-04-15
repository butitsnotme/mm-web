FROM alpine:3.9

RUN apk --no-cache update && apk --no-cache add php7 php7-apache2 tini php7-json && rm -rf /var/www/localhost/*

COPY src/ /var/www/localhost/htdocs/
COPY docker/run.sh /sbin/run.sh

ENTRYPOINT [ "/sbin/tini", "--" ]

CMD [ "/sbin/run.sh" ]

VOLUME [ "/var/www/localhost/data" ]
