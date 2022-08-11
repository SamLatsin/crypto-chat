# crypto-chat
Instant messaging websocket server with unique features. Backend in [PHP](https://www.php.net) with [Swoole](https://openswoole.com) framework. Wire transport is JSON over websocket. Persistant storage is [PostgreSQL](https://www.postgresql.org). 
## Getting Started
### Requirements
* [Docker compose](https://docs.docker.com/compose/compose-file/)
* Linux server
* Domain
### Installation
From command line run:
```
cd /var/www/
git clone https://github.com/SamLatsin/crypto-chat.git
```
After that go project folder and create `.env` file:
```
cd crypto-chat/
touch .env
```
Put this lines in your `.env` file and provide your fields data:
```
PG_DBNAME=chat
PG_USER={PostgreSQL username}
PG_PASSWORD={PostgreSQL password}
FTP_USER={FTP username}
FTP_PASSWORD={FTP password}
API_KEY={API key for connecting to external server with users database}
REST_AUTH_URL={URL to authentificate user in websocket}
ENCRYPT_KEY={Key to encrypt messages on server}
```
The next step is to configure SSL Certificate. Open `init-letsencrypt.sh` in project folder and change {DOMAIN} to your domain name. Repeat the same for file `nginx-default.conf`.
Run application in project folder:
```
docker compose up
```
