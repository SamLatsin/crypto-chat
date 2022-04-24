<?
namespace APP;

class ChatUser{
  private $db;
  private $model;

  function __construct($db) {
      $this->db = $db;
      $this->model = "public.chats_users";
  }

  public function getChatsUsers(){
    $this->db->prepare('select', 'SELECT * FROM '.$this->model);
    $res = $this->db->execute('select', []);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function getChatsCount($user_id) {
    $phql = 'SELECT COUNT(DISTINCT cu.chat_id) FROM public.chats_users cu 
             INNER JOIN public.messages msg 
             ON msg.chat_id = cu.chat_id
             FULL OUTER JOIN public.messages_stats st 
             ON st.message_id = msg.id 
             WHERE cu.user_id = $1 and st.message_id IS NULL OR msg.id  IS NULL';
    $this->db->prepare('get_chats_count', $phql);
    $res = $this->db->execute('get_chats_count', [$user_id]);
    $arr = $this->db->fetchAll($res);
    if ($arr) {
      return $arr[0]['count'];
    }
    return $arr;
  }

  function getUsersDialog($user1_id, $user2_id, $is_secret = 'false'){
    $data = [];
    array_push($data, $user1_id);
    array_push($data, $user2_id);
    array_push($data, $is_secret);
    $phql = 'SELECT c_u.chat_id, ch.is_secret FROM public.chats AS ch
             INNER JOIN 
             (
              SELECT chat_id FROM '.$this->model.' 
              where role != 3
              GROUP BY chat_id
              HAVING count(user_id) = 2 and bool_and(user_id in ($1,$2)) = true
             ) as c_u
             ON c_u.chat_id = ch.id
             WHERE ch.is_secret = $3';
    $this->db->prepare('get_users_dialog', $phql);
    $res = $this->db->execute('get_users_dialog', $data);
    $arr = $this->db->fetchAll($res);
    if ($arr) {
      return $arr[0]['chat_id'];
    }
    return null;
  }

  function blockUser($chat_id, $user_id) {
    $phql = 'UPDATE '.$this->model.' SET is_blocked = true WHERE chat_id = $1 and user_id = $2';
    $this->db->prepare('block_user', $phql);
    $res = $this->db->execute('block_user', [$chat_id, $user_id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function unblockUser($chat_id, $user_id) {
    $phql = 'UPDATE '.$this->model.' SET is_blocked = false WHERE chat_id = $1 and user_id = $2';
    $this->db->prepare('unblock_user', $phql);
    $res = $this->db->execute('unblock_user', [$chat_id, $user_id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function isUserBlocked($chat_id, $id){
    $this->db->prepare('is_user_blocked', 'SELECT user_id FROM '.$this->model.' WHERE chat_id = $1 and user_id = $2 and is_blocked = true');
    $res = $this->db->execute('is_user_blocked', [$chat_id, $id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function isUserInChat($chat_id, $id){
    $this->db->prepare('is_user_in_chat', 'SELECT user_id FROM '.$this->model.' WHERE chat_id = $1 and user_id = $2');
    $res = $this->db->execute('is_user_in_chat', [$chat_id, $id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function incrementUnreadCount($chat_id, $user_id){
    $phql = 'UPDATE '.$this->model.' SET unread_count = unread_count + 1 WHERE chat_id = $1 and user_id != $2';
    $this->db->prepare('increment_unread_count', $phql);
    $res = $this->db->execute('increment_unread_count', [$chat_id, $user_id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function decrementUnreadCount($chat_id, $user_id){
    $phql = 'UPDATE '.$this->model.' SET unread_count = unread_count - 1 WHERE chat_id = $1 and user_id != $2';
    $this->db->prepare('increment_unread_count', $phql);
    $res = $this->db->execute('increment_unread_count', [$chat_id, $user_id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function deleteChatUser($id){
    $phql  = "DELETE FROM ".$this->model." WHERE id = $1";
    $this->db->prepare('delete', $phql);
    $res = $this->db->execute('delete', [$id]);
    return $res;
  }

  function insertChatUser($fields){
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
    $phql = $phql.' ('.$keyRes.') VALUES ('.$valRes.')';
    $this->db->prepare('insert_chat_user', $phql);
    $res = $this->db->execute('insert_chat_user', $data);
    return $res;
  }

  function updateChatUser($fields,$id,$upd=false){
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
    $this->db->prepare('update', $phql);
    $res = $this->db->execute('update', $data);
    return $res;
  }
}