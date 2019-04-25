# mm-web
# Copyright (C) 2019  Dennis Bellinger
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published
# by the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

FROM alpine:3.9

RUN apk --no-cache update && apk --no-cache add php7 php7-apache2 tini php7-json && rm -rf /var/www/localhost/*

COPY src/ /var/www/localhost/htdocs/
COPY docker/run.sh /sbin/run.sh

ENTRYPOINT [ "/sbin/tini", "--" ]

CMD [ "/sbin/run.sh" ]

VOLUME [ "/var/www/localhost/data" ]
