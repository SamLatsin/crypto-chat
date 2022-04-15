<?php

namespace APP;

use Swoole\WebSocket\Server;
use Swoole\Coroutine\PostgreSQL;

class WebSocket
{
    private $server;

    private $table;

    protected $config;

    public function __construct()
    {
        $this->createTable();
        $this->config = Config::instance();
    }

    public function run()
    {
        $this->server = new Server($this->config['webim']['ip'], $this->config['webim']['port']);

        $this->server->on('open', [$this, 'open']);
        $this->server->on('message', [$this, 'message']);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('close', [$this, 'close']);

        $this->server->start();
    }

    private function getIdByToken($token) {
        $data = [
            'key'=>getenv("API_KEY"),
            'token'=>$token
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_URL, getenv("REST_AUTH_URL"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result, True);
        if ($result['result']) {
            return $result['result'];
        }
        return False;
    }

    public function open(Server $server, $request)
    {
        $token = $request->get['atoken'] ?? null;
        $id = $this->getIdByToken($token);
        if (!$id) {
            $server->close($request->fd);
        }

        $user = [
            'fd' => $request->fd,
            'id' => $id,
        ];
        $this->table->set($request->fd, $user);
        var_dump($user);
        // $this->pushMessage($server, $user, 'open', $request->fd);
    }

    private function allUser()
    {
        $users = [];
        foreach ($this->table as $row) {
            $users[] = $row;
        }
        return $users;
    }

    public function request($request, $response)
    {
        $data = [
          'code' => 'test',
          'error' => false,
          'result'=>"hello world",
        ];
        $response->header('Content-Type', 'application/json');   
        $response->end(json_encode($data));
    }

    public function message(Server $server, $frame)
    {
        if ($frame->data == "ping") {
            return;
        }

        var_dump($frame);
        $this->pushMessage($server, $frame->data, 'message', $frame->fd);
    }

    public function close(Server $server, $fd)
    {
        $user = $this->table->get($fd);
        $this->pushMessage($server, $user['name']."Leave's the chat room", 'close', $fd);
        $this->table->del($fd);
    }

    private function pushMessage(Server $server, $message, $messageType, $frameFd)
    {
        $message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());
        $user = $this->table->get($frameFd);
        $server->push($frameFd, $message);
        foreach ($this->table as $row) {
            if ($frameFd == $row['fd']) {
                continue;
            }
            $server->push($row['fd'], json_encode([
                    'type' => $messageType,
                    'message' => $message,
                    'datetime' => $datetime,
                    'user' => $user
                ])
            );
        }
    }

    private function createTable()
    {
        $this->table = new \swoole_table(1024);
        $this->table->column('fd', \swoole_table::TYPE_INT);
        $this->table->column('id', \swoole_table::TYPE_INT);
        $this->table->column('name', \swoole_table::TYPE_STRING, 255);
        $this->table->column('avatar', \swoole_table::TYPE_STRING, 255);
        $this->table->create();
    }

}