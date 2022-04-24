#!/usr/bin/env php
<?php

declare(strict_types=1);

include_once __DIR__ . "/vendor/autoload.php";

require_once "src/functions.php";
require_once "src/classes/Chat.php";
require_once "src/classes/ChatUser.php";
require_once "src/classes/Message.php";
require_once "src/classes/MessageStat.php";

$webSocket = new \APP\WebSocket();
$webSocket->run();