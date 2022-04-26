<?php

namespace APP;

use Swoole\WebSocket\Server;
use Swoole\Coroutine\PostgreSQL;
use APP\Chat;
use APP\ChatUser;
use APP\Message;

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

    private function init_db() {
        $db = new PostgreSQL();
        $db->connect(
            "host=postgres 
            port=5432 
            dbname="  .getenv("PG_DBNAME")." 
            user="    .getenv("PG_USER")." 
            password=".getenv("PG_PASSWORD"));
        return $db;
    }

    private function getUserByToken($token) {
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
        $user = $this->getUserByToken($token);
        if (!$user) {
            $server->close($request->fd);
        }

        $user = [
            'fd' => $request->fd,
            'id' => $user['id'],
            'name'=>$user['login'],
            'avatar'=>$user['img']
        ];
        $this->table->set($request->fd, $user);
    }

    private function getUsersFDsById($id) {
        $fds = [];
        foreach ($this->table as $row) {
            if ($row['id'] == $id) {
                $fds[] = $row['fd'];
            }
        }
        return $fds;
    }

    public function request($request, $response)
    {
        $token = $request->get['atoken'] ?? null;
        $user = $this->getUserByToken($token);
        $response->header('Content-Type', 'application/json');   
        if (!$user) {
            $response->status(401);
            $data = [
              'error'=>true,
              'status'=>'unauthorized',
            ];
            return $response->end(json_encode($data));
        }
        $id = $user['id'];
        $db = $this->init_db();
        $Message = new Message($db);
        $Chat = new Chat($db);
        $ChatUser = new ChatUser($db);
        $MessageStat = new MessageStat($db);
        $task = $request->get['task'];     
        switch($task){
            case 'get_messages':
                $chat_id = $request->get['chat_id'] ?? 0;
                $messages = false;
                if (!$ChatUser->isUserInChat($chat_id, $id)) {
                    $res = [
                      'error'=>false,
                      'result'=>$messages,
                    ];
                    return $response->end(json_encode($res));
                }
                $fields = [
                    'user_id'=>$id,
                    'chat_id'=>$chat_id,
                    'offset'=>$request->get["offset"] ?? 0,
                    'limit'=>30
                ];
                $messages = $Message->getUserMessagesByChatId($fields);
                if ($Chat->isChatSecret($chat_id)) {
                    foreach ($messages as $key => $message) {
                        $messages[$key]['content'] = decrypt($message['content'], getenv("ENCRYPT_KEY"));
                    }
                }
                $res = [
                  'error'=>false,
                  'result'=>$messages,
                ];
                return $response->end(json_encode($res));
            case 'new_chat':
                $is_secret = $request->get['is_secret'] ?? false;
                $is_secret = $is_secret ? 'true' : 'false';
                $user_id = $request->get['user_id'] ?? null;
                if ($ChatUser->isUserBlocked($ChatUser->getUsersDialog($id, $user_id), $id)) {
                    $res = [
                      'error'=>true,
                      'result'=>"blocked",
                    ];
                    return $response->end(json_encode($res));
                }
                $chat_id = $ChatUser->getUsersDialog($id, $user_id, $is_secret);
                if (!$chat_id){ // create chat
                    $fields = [
                        'is_secret'=>$is_secret,
                    ];
                    $chat_id = $Chat->insertChat($fields);
                    $ChatUser->insertChatUser(['chat_id'=>$chat_id,'user_id'=>$id]);
                    $ChatUser->insertChatUser(['chat_id'=>$chat_id,'user_id'=>$user_id]);
                }
                $res = [
                  'error'=>false,
                  'result'=>$chat_id,
                ];
               
                return $response->end(json_encode($res));
            case 'get_chats':
                $fields = [
                    'user_id'=>$id,
                    'offset'=>$request->get["offset"] ?? 0,
                    'limit'=>10
                ];
                $chats = $Chat->getChatsByUserId($fields);
                $chats_count = $ChatUser->getChatsCount($id);
                $result = [
                    'count'=>$chats_count,
                    'chats'=>$chats,
                ];
                $res = [
                  'error'=>false,
                  'result'=>$result,
                ];
                return $response->end(json_encode($res));
            case 'delete_chat':
                $chat_id = $request->get["chat_id"];
                if (!$ChatUser->isUserInChat($chat_id, $id)) {
                    $res = [
                      'error'=>false,
                      'result'=>false,
                    ];
                    return $response->end(json_encode($res));
                }
                if ($Chat->isChatSecret($chat_id)) {
                    $Chat->deleteChat($chat_id);
                    $res = [
                      'error'=>false,
                      'result'=>true,
                    ];
                    return $response->end(json_encode($res));
                }
                $fields = [
                    'chat_id'=>$chat_id,
                    'user_id'=>$id,
                ];
                $MessageStat->deleteLocalChat($fields);
                $res = [
                  'error'=>false,
                  'result'=>true,
                ];
                return $response->end(json_encode($res));
            case 'block_user':
                $user_id = $request->get['user_id'] ?? null;
                $chat_id = $ChatUser->getUsersDialog($id, $user_id);
                $ChatUser->blockUser($chat_id, $user_id);
                $chat_id = $ChatUser->getUsersDialog($id, $user_id, true);
                $ChatUser->blockUser($chat_id, $user_id);
                $res = [
                  'error'=>false,
                  'result'=>true,
                ];
                return $response->end(json_encode($res));
            case 'unblock_user':
                $user_id = $request->get['user_id'] ?? null;
                $chat_id = $ChatUser->getUsersDialog($id, $user_id);
                $ChatUser->unblockUser($chat_id, $user_id);
                $chat_id = $ChatUser->getUsersDialog($id, $user_id, true);
                $ChatUser->unblockUser($chat_id, $user_id);
                $res = [
                  'error'=>false,
                  'result'=>true,
                ];
                return $response->end(json_encode($res));
        }
        $data = [
          'error'=>true,
          'status'=>'No task',
        ];
        return $response->end(json_encode($data));
    }

    public function message(Server $server, $frame)
    {
        if ($frame->data == "ping") {
            return;
        }
        $db = $this->init_db();
        $data = json_decode($frame->data, true);
        if (!$data) {
            return;
        }
        $Message = new Message($db);
        $Chat = new Chat($db);
        $ChatUser = new ChatUser($db);
        $MessageStat = new MessageStat($db);
        $sender_id = $this->table->get($frame->fd)['id'];
        $chat_id = $data["chat_id"];
        switch($data['task']){
            case 'read':
                if (!$ChatUser->isUserInChat($chat_id, $sender_id)) {
                    break;
                }
                $fields = [
                    "chat_id"=>$chat_id,
                    "sender_user_id"=>$sender_id,
                ];
                $MessageStat->markAsRead($fields);
                $ChatUser->setZeroMessageCount($chat_id, $sender_id);

                $out_data = [
                    'type'=>'read',
                    'content'=>$chat_id,
                    'time'=>date('Y-m-d H:i:s', time()),
                ];
                $this->sendToRecievers($server, $ChatUser, $chat_id, 
                                       $frame->fd, $out_data);
                break;
            case 'send_text':
                if (!$ChatUser->isUserInChat($chat_id, $sender_id)) {
                    break;
                }
                if ($ChatUser->isUserBlocked($chat_id, $sender_id)) {
                    // you are blocked
                    break;
                }
                if ($Chat->isChatSecret($chat_id)) {
                    $data["data"] = encrypt($data["data"], getenv("ENCRYPT_KEY"));
                }
                $fields = [
                    "content"=>$data["data"],
                    "chat_id"=>$chat_id,
                    "sender_user_id"=>$sender_id,
                ];
                $message_id = $Message->insertMessage($fields);
                $fields = [
                    'message_id'=>$message_id,
                    'chat_id'=>$chat_id,
                    'sender_user_id'=>$sender_id,
                ];
                $ChatUser->incrementUnreadCount($chat_id, $sender_id);
                $MessageStat->insertMessageStatForAllRecievers($fields);

                $out_data = htmlspecialchars(json_decode($frame->data, true)["data"]);
                $out_data = [
                    'type'=>'text',
                    'content'=>$out_data,
                    'sender'=>$sender_id,
                    'time'=>date('Y-m-d H:i:s', time()),
                ];

                $this->sendToRecievers($server, $ChatUser, $chat_id, 
                                       $frame->fd, $out_data);
                break;
            case 'send_file':

                break;
            case 'update_text':
                if (!$ChatUser->isUserInChat($chat_id, $sender_id)) {
                    break;
                }
                if ($Chat->isChatSecret($chat_id)) {
                    $data["data"] = encrypt($data["data"], getenv("ENCRYPT_KEY"));
                }
                $fields = [
                    "content"=>$data["data"],
                    "sender_user_id"=>$sender_id,
                    "is_edited"=>true,
                ];
                $Message->updateMessage($fields, $data['message_id'], true);
                $out_data = [
                    'message_id'=>$data['message_id'],
                    'content'=>json_decode($frame->data, true)["data"],
                ];
                $this->sendToRecievers($server, $ChatUser, $chat_id, 
                                       $frame->fd, $out_data, 'update');
                break;
            case 'delete_messages':
                if (!$ChatUser->isUserInChat($chat_id, $sender_id)) {
                    break;
                }
                if ($Chat->isChatSecret($chat_id)) {
                    foreach ($data['ids'] as $key => $message_id) {
                        $Message->deleteMessage($message_id);
                        $ChatsUser->decrementUnreadCountForDeletedMessage($message_id, $sender_id);
                        $out_data = [
                            'type'=>'delete',
                            'content'=>$message_id,
                            'time'=>date('Y-m-d H:i:s', time()),
                        ];
                        $this->sendToRecievers($server, $ChatUser, $chat_id, 
                                               $frame->fd, $out_id);
                        break;
                    }
                }
                if ($data["for_all"]) {
                    foreach ($data['ids'] as $key => $message_id) {
                        $fields = [
                            "is_deleted"=>'true',
                            "sender_user_id"=>$sender_id,
                        ];
                        $Message->updateMessage($fields, $message_id, true);
                        $ChatsUser->decrementUnreadCountForDeletedMessage($message_id, $sender_id);

                        $out_data = [
                            'type'=>'delete',
                            'content'=>$message_id,
                            'time'=>date('Y-m-d H:i:s', time()),
                        ];
                        $this->sendToRecievers($server, $ChatUser, $chat_id, 
                                               $frame->fd, $out_data);
                        break;
                    }
                }
                foreach ($data['ids'] as $key => $message_id) {
                    $fields = [
                        'message_id'=>$message_id,
                        'user_id'=>$sender_id,
                    ];
                    $MessageStat->deleteLocalMessage($fields);
                }
                break;
            case 'custom_event':
                $out_data = [
                    'type'=>'custom_event',
                    'content'=>$data['data'],
                    'sender'=>$sender_id,
                    'time'=>date('Y-m-d H:i:s', time()),
                ];
                $this->sendToRecievers($server, $ChatUser, $chat_id, 
                                       $frame->fd, $out_data);
                break;
        }
    }

    public function close(Server $server, $fd)
    {
        if ($this->table->get($fd)) {
            $user = $this->table->get($fd);
            $this->table->del($fd);
        }
    }

    private function sendToRecievers($server, $ChatUser, $chat_id, $sender_fd, $data) {
        $sender_id = $this->table->get($sender_fd)['id'];
        $recievers = $ChatUser->getUsersInChat($chat_id, $sender_id);
        foreach ($recievers as $key => $reciever) {
            $fds = $this->getUsersFDsById($reciever['user_id']);
            foreach ($fds as $key => $fd) {
                $this->pushMessage($server, $data, $fd, $sender_fd);
            }
        }
        $data = [
            'type'=>'status',
            'result'=>true,
            'time'=>date('Y-m-d H:i:s', time()),
        ];
        $this->pushMessage($server, $data, $sender_fd);
    }

    private function pushMessage(Server $server, $data, $frameFd, $senderFd = false)
    {
        $server->push($frameFd, json_encode($data));
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