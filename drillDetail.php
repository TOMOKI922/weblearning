<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　勉強詳細ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$d_id = (!empty($_GET['d_id'])) ? $_GET['d_id'] : '';
//ドリルデータの取得
$viewData = getDrillOne($d_id);

if(empty($viewData)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}
debug('取得したDBデータ：'.print_r($viewData,true));
//お気に入りの登録
if(!empty($_POST['send'])){
  debug('archiveテーブルに接続します');
  require('auth.php');
  try{
    $dbh = dbConnect();
    $sql = 'INSERT INTO archive (drill_id, user_id, created_at) VALUES (:d_id, :u_id, :date)';
    $data = array(':d_id' => $d_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
    $stmt = queryPost($dbh, $sql, $data);
  } catch(Exception $e) {
    err_log('エラー発生:'.$e->getMessage());
  }
}
//メッセージ投稿
if(!empty($_POST['message'])){
  debug('messageテーブルへ接続します');
  require('auth.php');
  $msg = $_POST['message'];
  validMaxLen($msg, 'msg', 500);
  validRequired($msg, 'msg');
  if(empty($err_msg)){

  try{
    $dbh = dbConnect();
    $sql = 'INSERT INTO msg (drill_id, user_id, created_at, msg) VALUES (:d_id, :u_id, :date, :msg)';
    $data = array(':d_id' => $d_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'), ':msg' => $msg);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      $_POST = array();
    }
  } catch (Exception $e) {
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG07;
    }
  }
}
//メッセージデータの取得
$MsgData = getMsgList($d_id);

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
$siteTitle = 'ドリル詳細';
require('head.php');
 ?>

  <body class="page-drillDetail page-1colum">
    <?php require('header.php'); ?>

    <div id="contents" class="site-width drillDetail">
      <section id="main">
        <div class="drill-top">
          <span class="badge"><?php echo sanitize($viewData['category']); ?></span>
          <div class="item-right">
            <i class="fa fa-heart-o icn-favorite js-click-favorite fav <?php if(isLike($_SESSION['user_id'], $viewData['id'])){echo 'active';} ?>" aria-hidden="true" data-drillid="<?php echo sanitize($viewData['id']); ?>"></i>
            <i class="fa fa-heart icn-favorite js-click-favorite2 fav2 <?php if(isLike($_SESSION['user_id'], $viewData['id'])){echo 'active';} ?>" aria-hidden="true" data-drillid="<?php echo sanitize($viewData['id']); ?>"></i>
          </div>
        </div>
        <div class="drill-title">
          <?php echo sanitize($viewData['name']); ?>
        </div>
          <div class="learn-detail">
            <p><?php echo sanitize($viewData['comment']); ?></p>
          </div>
          <div class="drill-regist">
            <div class="item-left">
              <a href="index.php<?php echo appendGetParam(array('d_id')); ?>">&lt; 学習一覧に戻る</a>
            </div>
            <form action="" method="post">
               <input type="submit" name="send" value="達成" class="<?php if(isArchive($_SESSION['user_id'], $viewData['id'])){echo 'active';} ?>" <?php if(isArchive($_SESSION['user_id'], $viewData['id'])){echo "disabled";} ?>>
             <div class="msg">
               コメント(<?php echo sanitize($MsgData['total']); ?>)
               <?php
                 if(!empty($MsgData['data'])):
                   foreach($MsgData['data'] as $key => $val):
                ?>
              <div class="opinion">
                <div class="user-image">
                  <img src="<?php echo sanitize($val['pic']); ?>" alt="sample">
                </div>
                <div class="user-name">
                  <p><?php echo sanitize($val['username']); ?></p>
                </div>
                <div class="comment">
                  <?php echo sanitize($val['msg']); ?>
                </div>
              </div>
                <?php
                   endforeach;
                 endif;
                 ?>
             </div>
             <div class="item-center">
               <textarea class="drill-text" name="message" cols="30" rows="10" id="js-count"><?php getFormData('message'); ?></textarea>
               <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
               <input type="submit" value="コメントする">
             </div>
            </form>
          </div>

          

      </section>
    </div>

    <?php require('footer.php'); ?>
