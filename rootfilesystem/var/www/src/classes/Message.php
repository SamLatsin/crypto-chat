<?
namespace APP;

class Message{
  private $db;
  private $model;

  function __construct($db) {
      $this->db = $db;
      $this->model = "public.messages";
  }

  // public function getMessages(){
  //   $this->db->prepare('select', 'SELECT * FROM '.$this->model);
  //   $res = $this->db->execute('select', []);
  //   $arr = $this->db->fetchAll($res);
  //   return $arr;
  // }

  // public function getMessagesByChatId($fields){
  //   $data = [];
  //   array_push($data, $fields['chat_id']);
  //   array_push($data, $fields['offset']);
  //   array_push($data, $fields['limit']);
  //   $this->db->prepare('get_messages', 'SELECT * FROM '.$this->model." WHERE chat_id = $1 ORDER BY id ASC OFFSET $2 LIMIT $3");
  //   $res = $this->db->execute('get_messages', $data);
  //   $arr = $this->db->fetchAll($res);
  //   return $arr;
  // }

  public function getUserMessagesByChatId($fields){
    $data = [];
    array_push($data, $fields['user_id']);
    array_push($data, $fields['chat_id']);
    array_push($data, $fields['offset']);
    array_push($data, $fields['limit']);
    $phql = 'SELECT a.*, b.is_read
             FROM public.messages a
             INNER JOIN public.messages_stats b
             ON a.id = b.message_id
             WHERE 
               a.chat_id = $2 and 
               b.user_id = $1 and 
               b.is_deleted is false and
               a.is_deleted is false
             ORDER BY a.id ASC
             OFFSET $3
             LIMIT $4';
    // $phql = 'SELECT  a.*
    //          FROM public.messages a
    //           LEFT JOIN public.messages_stats b
    //           on a.id = b.message_id and b.user_id = $1
    //          WHERE a.chat_id = $2 and b.user_id is null and b.is_deleted is false ORDER BY a.id ASC
    //          OFFSET $3
    //          LIMIT $4';
    $this->db->prepare('get_messages', $phql);
    $res = $this->db->execute('get_messages', $data);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function isMessageRead($id) {
    $this->db->prepare('is_message_read', 'SELECT id FROM '.$this->model.' WHERE id = $1 and (is_read = true or is_deleted = true)');
    $res = $this->db->execute('is_message_read', [$id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function deleteMessage($id){
    $phql  = "DELETE FROM ".$this->model." WHERE id = $1";
    $this->db->prepare('delete', $phql);
    $res = $this->db->execute('delete', [$id]);
    return $res;
  }

  function insertMessage($fields){
    $phql = 'INSERT INTO '.$this->model;
    $data = [];
    $i = 1;
    foreach ($fields as $key => $field) {
      $keys[] = $key;
      $values[] = '$'.$i;
      array_push($data, $field);
      $i += 1;
    }
    $keyRes = implode(',',$keys);
    $valRes =  implode(',',$values);
    $phql = $phql.' ('.$keyRes.') VALUES ('.$valRes.') RETURNING id';
    $this->db->prepare('insert', $phql);
    $res = $this->db->execute('insert', $data);
    $arr = $this->db->fetchAll($res);
    return $arr[0]['id'];
  }

  function updateMessage($fields,$id, $for_user=false,$upd=false){
    $phql = 'UPDATE '.$this->model.' SET ';
    $data = [];
    $i = 1;
    foreach ($fields as $key => $field) {
      if($upd!=$key){
        $values[] = $key.'=$'.$i;
        array_push($data, $field);
        $i += 1;
      }
    }
    $valRes =  implode(', ',$values);
    if(!$upd){
      $phql = $phql.$valRes.' WHERE id='.$id;
    }else{
      $phql = $phql.$valRes.' WHERE '.$upd.'=$'.$upd;
    }
    if ($for_user) {
      $phql = $phql.' and sender_user_id='.$fields['sender_user_id'];
    }
    $this->db->prepare('update', $phql);
    $res = $this->db->execute('update', $data);
    return $res;
  }
}