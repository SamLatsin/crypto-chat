#!/usr/bin/env php
<?php

declare(strict_types=1);

include_once __DIR__ . "/vendor/autoload.php";
$webSocket = new \APP\WebSocket();
$webSocket->run();






