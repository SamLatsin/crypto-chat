
<?php
 
//  // Создать объект асинхронного сервера WebSocket
// $host = "0.0.0.0";
// $port = 9502;
// $server = new swoole_websocket_server($host, $port);
// echo "[server] ".json_encode($server).PHP_EOL;
 
//  // Слушаем рукопожатие клиента
// $server->on("open", function(swoole_websocket_server $server, $request){
//     echo PHP_EOL;
//          $fd = $request->fd; // Получить дескриптор файла, запрошенный клиентом
//     echo "[open] client {$fd} handshake success".PHP_EOL;
//     echo "[server] ".json_encode($server).PHP_EOL;
//     echo "[request] ".json_encode($request).PHP_EOL;
// });
 
//  // Прослушивание сообщений, отправленных клиентом
// $server->on("message", function(swoole_websocket_server $server, $frame){
//     echo PHP_EOL;
//          $fd = $frame->fd; // Получить дескриптор файла, запрошенный клиентом
//          $data = $frame->data; // Получить сообщение, отправленное клиентом
//     echo "[message] client {$fd} : {$data}".PHP_EOL;
//     echo "[server] ".json_encode($server).PHP_EOL;
//     echo "[frame] ".json_encode($frame).PHP_EOL;
 
//     $message = "success";
//     $server->push($fd, $message);
// });

// // $server->on('message', function ($server, $request) {
 
// //     // $request->fd The ID assigned by the server when the client connects successfully
// //     // $server->push($request->fd, $request->data);
// //     // $request->data receives the message sent by the client
// //     $fd_list = $server->connections; // all connected clients
// //     if(!empty($fd_list)){
// //         foreach ($fd_list as $fd){
// //             $server->push($fd, $request->data);
// //         }
// //     }
 
// // });
 
//  // Прослушивающий клиент отключается
// $server->on("close", function(swoole_websocket_server $server, $fd){
//     echo PHP_EOL;
//     echo "[close] client {$fd}".PHP_EOL;
//     echo "[server] ".json_encode($server).PHP_EOL;
//     echo "[fd] ".json_encode($fd).PHP_EOL;
// });
 
//  // Запустить сервер
// $server->start();

// use Swoole\Http\Server;
// use Swoole\Http\Request;
// use Swoole\Http\Response;

// $http = new Server("0.0.0.0", 9501);

// $http->set([
//     'worker_num' => 4,
// ]);

// $http->on('request', function ($request, swoole_http_response $response)   {
    // $db = new Swoole\Coroutine\PostgreSQL();
    // $db->connect(
    //     "host=postgres 
    //     port=5432 
    //     dbname="  .getenv("PG_DBNAME")." 
    //     user="    .getenv("PG_USER")." 
    //     password=".getenv("PG_PASSWORD"));
//     $db->prepare('test', 'SELECT test FROM test.test');
//     $res = $db->execute('test', []);
//     $arr = $db->fetchAll($res);
    // $data = [
    //   'code' => 'lolo1',
    //   'error' => false,
    //   'result'=>$arr,
    // ];
    // $response->header('Content-Type', 'application/json');   
    // $response->end(json_encode($data));
// });

// $http->start();

// $server = new swoole_websocket_server("0.0.0.0", 9502);

// $server->on('open', function($server, $req) {
//     echo "connection open: {$req->fd}\n";
// });

// $server->on('message', function($server, $frame) {
//     echo "received message: {$frame->data}\n";
//     $server->push($frame->fd, json_encode(["hello", "world"]));
// });

// $server->on('close', function($server, $fd) {
//     echo "connection close: {$fd}\n";
// });

// $server->start();

// $http = new swoole_http_server('0.0.0.0', 9501);

// $http->on('request', function ($request, $response) {
//    $db = new Swoole\Coroutine\PostgreSQL();
//     $db->connect(
//         "host=postgres 
//         port=5432 
//         dbname="  .getenv("PG_DBNAME")." 
//         user="    .getenv("PG_USER")." 
//         password=".getenv("PG_PASSWORD"));
//     $db->prepare('test', 'SELECT test FROM test.test');
//     $res = $db->execute('test', []);
//     $arr = $db->fetchAll($res);
//     $data = [
//       'code' => 'lolo1',
//       'error' => false,
//       'result'=>$arr,
//     ];
//     $response->header('Content-Type', 'application/json');   
//     $response->end(json_encode($data));
// });

// $http->start();



// $server = new swoole_websocket_server("0.0.0.0", 9501);

// $server->on('start', function ($server) {
//     // http 'start'
//     echo "Swoole http server is started at http://127.0.0.1:80\n";
// });

// $server->on('request', function ($request, $response) {
//     // http 'request'
//     $response->header("Content-Type", "text/plain");
//     $response->end("Hello World\n");
// });

// $server->on('open', function($server, $req) {
//     // ws 'open'
//     echo "connection open: {$req->fd}\n";
// });

// $server->on('message', function($server, $frame) {
//     // ws 'message'
//     echo "received message: {$frame->data}\n";
//     $server->push($frame->fd, json_encode($frame->data));
// });

// $server->on('close', function($server, $fd) {
//     // ws 'close'
//     echo "connection close: {$fd}\n";
// });

// $server->start();



