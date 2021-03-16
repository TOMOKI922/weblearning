<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

if(!empty($_POST)){
  debug('POST送信があります');

  try{
    $dbh = dbConnect();
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
    $sql2 = 'UPDATE drill SET delete_flg = 1 WHERE user_id = :us_id';
    $sql3 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :us_id';
    $sql4 = 'UPDATE msg SET delete_flg = 1 WHERE user_id = :us_id';

    $data = array(':us_id' => $_SESSION['user_id']);

    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    $stmt3 = queryPost($dbh, $sql3, $data);
    $stmt4 = queryPost($dbh, $sql4, $data);
    
    if($stmt1){
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します');
      header("Location:index.php");
    }else{
      debug('クエリに失敗しました。');
      $err_msg['common'] = MSG07;
    }

  } catch(Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
$siteTitle = '退会';
require('head.php');
 ?>

 <body class="page-withdraw page-1colum">

   <style>
    .form .btn{
      float: none;
    }
    .form{
      text-align: center;
    }
   </style>

   <?php require('header.php'); ?>

   <div id="contents" class="site-width">

     <section id="main">
     <div class="form-container">
       <form action="" method="post" class="form">
         <h2 class="title">退会</h2>
         <div class="area-msg">
           <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
         </div>
         <div class="btn-container">
           <input type="submit" class="btn btn-mid" value="退会する" name="submit">
         </div>
       </form>
     </div>
     <a href="mypage.php">&lt; マイページへ戻る</a>
   </section>
   </div>

   <?php require('footer.php'); ?>
