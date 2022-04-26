<?
namespace APP;

class MessageStat{
  private $db;
  private $model;

  function __construct($db) {
      $this->db = $db;
      $this->model = "public.messages_stats";
  }

  function markAsRead($fields){
    $data = [];
    array_push($data, $fields['chat_id']);
    array_push($data, $fields['sender_user_id']);
    $phql =  'UPDATE '.$this->model.' as ms1
              SET is_read = true
              FROM public.messages as msg
              INNER JOIN public.messages_stats as ms
              ON msg.id = ms.message_id
              WHERE ms1.user_id = ms.user_id and ms1.message_id = ms.message_id and 
                   msg.chat_id = $1 and ms1.is_read = false and
                  ((msg.sender_user_id != $2 and ms1.user_id != $2) OR
                  (msg.sender_user_id != $2 and ms1.user_id = $2))';
    $this->db->prepare('mark_as_read', $phql);
    $res = $this->db->execute('mark_as_read', $data);
    return $res;
  }

  function deleteLocalChat($fields){
    $data = [];
    array_push($data, $fields['user_id']);
    array_push($data, $fields['chat_id']);
    $phql = 'UPDATE '.$this->model.' AS ms 
             SET is_deleted = true
             FROM public.messages AS msg
             WHERE ms.user_id = $1 and msg.chat_id = $2';
    $this->db->prepare('delete_local_chat', $phql);
    $res = $this->db->execute('delete_local_chat', $data);
    return $res;
  }

  function deleteLocalMessage($fields){
    $data = [];
    array_push($data, $fields['message_id']);
    array_push($data, $fields['user_id']);
    $phql = 'UPDATE '.$this->model.'
             SET is_deleted=true
             WHERE message_id=$1 and user_id = $2';
    $this->db->prepare('delete_local_message', $phql);
    $res = $this->db->execute('delete_local_message', $data);
    return $res;
  }

  // function deleteMessageStat($id){
  //   $phql  = "DELETE FROM ".$this->model." WHERE id = $1";
  //   $this->db->prepare('delete', $phql);
  //   $res = $this->db->execute('delete', [$id]);
  //   return $res;
  // }

  function insertMessageStatForAllRecievers($fields) {
    $data = [];
    array_push($data, $fields['chat_id']);
    array_push($data, $fields['message_id']);
    // array_push($data, $fields['sender_user_id']);
    $phql = 'INSERT INTO '.$this->model.'
             SELECT msg.id AS message_id, cu.user_id FROM public.messages AS msg
             INNER JOIN public.chats_users AS cu
             ON cu.chat_id = msg.chat_id
             WHERE cu.chat_id = $1 AND msg.id = $2';
    $this->db->prepare('insert_message_stats', $phql);
    $res = $this->db->execute('insert_message_stats', $data);
    return $res;
  }

  // function insertMessageStat($fields){
  //   $phql = 'INSERT INTO '.$this->model;
  //   $data = [];
  //   $i = 1;
  //   foreach ($fields as $key => $field) {
  //     $keys[] = $key;
  //     $values[] = '$'.$i;
  //     array_push($data, $field);
  //     $i += 1;
  //   }
  //   $keyRes = implode(',',$keys);
  //   $valRes =  implode(',',$values);
  //   $phql = $phql.' ('.$keyRes.') VALUES ('.$valRes.')';
  //   $this->db->prepare('insert_message_stat', $phql);
  //   $res = $this->db->execute('insert_message_stat', $data);
  //   return $res;
  // }

  // function updateMessageStat($fields,$id,$upd=false){
  //   $phql = 'UPDATE '.$this->model.' SET ';
  //   $data = [];
  //   $i = 1;
  //   foreach ($fields as $key => $field) {
  //     if($upd!=$key){
  //       $values[] = $key.'=$'.$i;
  //       array_push($data, $field);
  //       $i += 1;
  //     }
  //   }
  //   $valRes =  implode(', ',$values);
  //   if(!$upd){
  //     $phql = $phql.$valRes.' WHERE id='.$id;
  //   }else{
  //     $phql = $phql.$valRes.' WHERE '.$upd.'=$'.$upd;
  //   }
  //   $this->db->prepare('update', $phql);
  //   $res = $this->db->execute('update', $data);
  //   return $res;
  // }
}