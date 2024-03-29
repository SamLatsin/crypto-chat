# crypto-chat
Instant messaging websocket server with unique features. Backend in [PHP](https://www.php.net) with [Swoole](https://openswoole.com) framework. Wire transport is JSON over websocket. Persistant storage is [PostgreSQL](https://www.postgresql.org). 
## Overview
Fast, asynchronous and event-driven backend service that can solve the problem of embedding chat in your application. It's easy to deploy and support. 
### Use cases
* Online marketplace
* On-demand services
* Banking
* Online gaming
* Online dating
* Enterprice communication
* Social communities
* Crypto P2P exchange
### Features
* Instant messaging via websocket
* Group chats
* Secret chats
* Attachments support
* Message encryption
* User with admin role can read all non secret chats and write messages in it (it can be used to provide some kind of support to your service)
* Users blocking
* Third party authentication (you should make an http endpoint on your main service)
* Message editing
* Message deleting (for all and locally for you)
* Support for custom events via websocket (it can be used to add your new custom features to the chat)
* Emoji support 😋
* Embedded FTP server
* Auto renewing SSL ([Let's encrypt](https://letsencrypt.org))
* HTTP requests optimization
* High request proccess speed (around 110515 requests/sec on low-end server)
* Automatic database backup
* Stability and restart on fail
* Easy and fast deploy
* Works as an independent service
### What needs to be done
* Rewrite attachments module to add support of multiple attachment to one message
* Fix some group chat bugs
## Getting Started
### Requirements
* [Docker compose](https://docs.docker.com/compose/compose-file/)
* Linux server
* Domain
### Installation
Read [API documentation](https://sam-latsin.gitbook.io/crypto-chat-api-eng/)

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
API_KEY={API key for connecting to external server with users database }
REST_AUTH_URL={URL to authentificate user in websocket}
ENCRYPT_KEY={Key to encrypt messages on server}
```
For additional help with API_KEY and REST_AUTH_URL see this in [documentation](https://sam-latsin.gitbook.io/crypto-chat-api-eng/api-reference/main-server)

The next step is to configure SSL Certificate. Open `init-letsencrypt.sh` in project folder and change {DOMAIN} to your domain name. Repeat the same for file `nginx-default.conf`.

Run application in project folder:
```
docker compose up
```
## License

Crypto-chat is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
