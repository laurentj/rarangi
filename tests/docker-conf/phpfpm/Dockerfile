FROM 3liz/liz-php-fpm:7.4

COPY app_entrypoint.sh /bin/entrypoint.d/
COPY appctl.sh /bin/
RUN chmod 755 /bin/entrypoint.d/app_entrypoint.sh /bin/appctl.sh

