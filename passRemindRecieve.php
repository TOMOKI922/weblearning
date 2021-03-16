<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード債発行認証キー入力ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(empty($_SESSION['auth_key'])){
  header("Location:passRemindSend.php");
}


if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報:'.print_r($_POST, true));

  $auth_key = $POST['key'];

  validRequired($auth_key,'key');

  if(empty($err_msg)){
    debug('未入力チェックOK。');
    validLength($auth_key, 'key');

    validHalf($auth_key, 'key');

    if(empty($err_msg)){
      debug('バリデーションＯＫ。');

      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG15;
      }
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['common'] = MSG16;
      }

      if(empty($err_msg)){
        debug('認証ＯＫ。');

        $pass = makeRandKey();

        try{
          $dbh = dbConnect();

          $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
          $data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
          $stmt = queryPost($dbh, $sql, $data);

          if($stmt){
            debug('クエリ成功。');

            $from = 'info@english-learn.com';
            $to = $_SESSION['auth_email'];
            $subject = '【パスワード再発行完了】| 学習情報交換';
            $comment = <<<EOT
本メールアドレス宛にパスワードの再発行をいたしました。
下記のURLにて再発行パスワードをご入力いただき、ログインください。

ログインぺージ：http://localhost/ウェブ学習アプリ/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願いいたします

学習情報交換
URL http://english-learn.com/
E-mail info@english-learn.com
EOT;
          sendMail($from, $to, $subject, $comment);
          session_unset();
          $_SESSION['msg_success'] = SUC03;
          debug('セッション変数の中身：'.print_r($_SESSION, true));

          header("Location:login.php");
        }else{
          debug('クエリに失敗しました。');
          $err_msg['common'] = MSG07;
        }
      } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
      }
    }

  }
}
$siteTitle = 'パスワード再発行認証';
require('head.php');
?>

    <body class="page-signup page-1colum">

      <?php require('header.php'); ?>

      <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg-success'); ?>
      </p>

      <div id="contents" class="site-width">
        <section id="main">
          <div class="form-container">
            <form action="" method="post" class="form">
              <p>ご指定のメールアドレスをお送りした【パスワード再発行認証】メール内にある「認証キー」をご入力ください。</p>
              <div class="area-msg">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['key'])) echo 'err'; ?>">
                認証キー
                <input type="text" name="key" value="<?php echo getFormData('key'); ?>">
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['key'])) echo $err_msg['key']; ?>
              </div>
              <div class="btn-container">
                <input type="submit" class="btn btn-mid" value="再発行する">
              </div>
            </form>
          </div>
          <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
        </section>
      </div>

      <?php require('footer.php'); ?>
