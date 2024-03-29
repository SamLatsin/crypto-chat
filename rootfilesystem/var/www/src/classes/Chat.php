<?
namespace APP;

class Chat{
  private $db;
  private $model;

  function __construct($db) {
      $this->db = $db;
      $this->model = "public.chats";
  }

  public function getChatsByUserId($fields, $admin = false){
    $data = [];
    array_push($data, $fields['user_id']);
    array_push($data, $fields['offset']);
    array_push($data, $fields['limit']);
    $phql =  'SELECT cht.chat_id, cht.unread_count, cht.is_blocked, msg.*, ms.is_read, chts.is_secret , cu.user_id as second_user_id
              FROM public.chats_users cht
              INNER JOIN
              (
              SELECT MAX(a.id) id, chat_id
              FROM public.messages a
              INNER JOIN public.messages_stats b
                     ON a.id = b.message_id
                     WHERE 
                     b.user_id = $1';
    if (!$admin) {
      $phql .=     ' and 
                     b.is_deleted is false and
                     a.is_deleted is false';
    }           
    $phql .=' GROUP BY chat_id
              ) m2
              ON cht.chat_id = m2.chat_id
              INNER JOIN public.messages msg ON msg.id = m2.id
              INNER JOIN public.chats chts ON chts.id = cht.chat_id
              INNER JOIN public.chats_users cu ON cu.chat_id = cht.chat_id and cu.role != 3
              INNER JOIN public.messages_stats ms ON ms.user_id = cht.user_id and msg.id = ms.message_id
              WHERE  cht.user_id = $1 and 
                  cu.role != 3 and 
                  cu.user_id != $1 
              ORDER BY msg.id DESC 
              OFFSET $2 
              LIMIT $3';
    $this->db->prepare('get_chats', $phql);
    $res = $this->db->execute('get_chats', $data);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function getChatInfoById($id) {
    $phql  = "SELECT * FROM ".$this->model." WHERE id = $1";
    $this->db->prepare('get_chat_info', $phql);
    $res = $this->db->execute('get_chat_info', [$id]);
    $arr = $this->db->fetchAll($res);
    if ($arr) {
      return $arr[0];
    }
    return $arr;
  }

  function isChatSecret($chat_id){
    $this->db->prepare('is_chat_is_secret', 'SELECT id FROM '.$this->model.' WHERE id = $1 and is_secret = true');
    $res = $this->db->execute('is_chat_is_secret', [$chat_id]);
    $arr = $this->db->fetchAll($res);
    return $arr;
  }

  function deleteChat($id){
    $phql  = "DELETE FROM ".$this->model." WHERE id = $1";
    $this->db->prepare('delete', $phql);
    $res = $this->db->execute('delete', [$id]);
    return $res;
  }

  function insertChat($fields){
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
    $this->db->prepare('insert_chat', $phql);
    $res = $this->db->execute('insert_chat', $data);
    $arr = $this->db->fetchAll($res);
    return $arr[0]['id'];
  }
}