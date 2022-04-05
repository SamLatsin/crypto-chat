FROM phpswoole/swoole:4.8.8-php8.0

RUN \
  apt-get update && \
  apt-get install -y libpq-dev && \
  install-swoole-ext.sh postgresql 4.8.0 && \
  docker-php-ext-enable swoole_postgresql && \
  apt-get install git -y

EXPOSE 9501