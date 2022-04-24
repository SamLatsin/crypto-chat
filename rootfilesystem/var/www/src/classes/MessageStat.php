<?
namespace APP;

class MessageStat{
  private $db;
  private $model;

  function __construct($db) {
      $this->db = $db;
      $this->model = "public.messages_stats";
  }

  function deleteLocalChat($fields){
    $data = [];
    array_push($data, $fields['user_id']);
    array_push($data, $fields['chat_id']);
    $phql = 'INSERT INTO '.$this->model.'
             SELECT id as message_id, $1 as deleted_user_id FROM public.messages as msg
             FULL OUTER JOIN '.$this->model.' as st on st.message_id = msg.id 
             WHERE msg.chat_id = $2 and st.message_id IS NULL OR msg.id  IS NULL';
    $this->db->prepare('insert_deleted_messages', $phql);
    $res = $this->db->execute('insert_deleted_messages', $data);
    return $res;
  }

  function deleteMessageStat($id){
    $phql  = "DELETE FROM ".$this->model." WHERE id = $1";
    $this->db->prepare('delete', $phql);
    $res = $this->db->execute('delete', [$id]);
    return $res;
  }

  function insertMessageStat($fields){
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
    $this->db->prepare('insert_message_stat', $phql);
    $res = $this->db->execute('insert_message_stat', $data);
    return $res;
  }

  function updateMessageStat($fields,$id,$upd=false){
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