<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(isset($_POST['drillId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります');
  $d_id = $_POST['drillId'];
  debug('ドリルID：'.$d_id);

  try{
    $dbh = dbConnect();

    $sql = 'SELECT * FROM favorite WHERE drill_id = :d_id AND user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':d_id' => $d_id);

    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);

    if(!empty($resultCount)){
      $sql = 'DELETE FROM favorite WHERE drill_id = :d_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':d_id' => $d_id);

      $stmt = queryPost($dbh,$sql, $data);

    }else{
      $sql = 'INSERT INTO favorite (drill_id, user_id, created_at) VALUES (:d_id, :u_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':d_id' => $d_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
