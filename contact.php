<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　お問い合わせフォーム　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST['submit'])){
  debug('POST送信があります。');
  debug('POST情報:'.print_r($_POST,true));

  $email = $_POST['email'];
  $comment = $_POST['comment'];

  validRequired($email, 'email');
  validRequired($comment, 'comment');


  if(empty($err_msg)){
    debug('未入力チェックOK。');

    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validMaxLen($comment, 'comment');

    if(empty($err_msg)){
      debug('バリデーションOK。');
      try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);

        $stmt = queryPost($dbh, $sql, $data);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt && array_shift($result)){
          debug('クエリ成功。DB登録あり。');
          $_SESSION['msg_success'] = SUC03;

          $from = 'info@learnenglish.com';
          $to = $email;
          $subject = '【お問い合わせ】| ウェブ学習アプリ';

          $comment = <<<EOT
お問い合わせありがとうございます。頂いた意見は今後の参考とさせていただきます。
これからもウェブ学習アプリをよろしくお願いいたします。

ウェブ学習アプリ
URL http://ウェブ学習アプリ.com/
EOT;
          sendMail($from, $to, $subject, $comment);

          header("Location:mypage.php");

        }else{
          debug('クエリに失敗したかDBに登録のないEmailが入力されました');
          $err_msg['common'] = MSG07;
        }
      } catch (Exception $e) {
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
  $siteTitle = "お問い合わせフォーム";
  require('head.php');
 ?>

 <body class="page-contact page-1colum page-logined">



   <?php require('header.php'); ?>

  <div id="contents" class="site-width">
    <h1 class="page-title">お問い合わせ</h1>

   <section id="main">
     <div class="form-container contact">
       <form action="" method="post" class="form">
         <div class="area-msg">
           <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
         </div>
         <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
           Email
           <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
         </label>
         <div class="area-msg">
           <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
         </div>
         <label class="<?php if(!empty($err_msg['subject'])) echo 'err'; ?>">
           件名
           <input type="text" name="subject" value="<?php echo getFormData('subject'); ?>">
         </label>
         <div class="area-msg">
           <?php if(!empty($err_msg['subject'])) echo $err_msg['subject']; ?>
         </div>
         <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
           ご意見・ご感想
           <textarea name="comment" id="js-count" cols="30" rows="10" style="height: 150px;"><?php echo getFormData('comment'); ?></textarea>
         </label>
         <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
         <div class="area-msg">
           <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
         </div>
         <div class="btn-container">
           <input type="submit" class="btn btn-mid" value="送信する">
         </div>
       </form>
     </div>
     <a href="mypage.php">&lt; マイページに戻る</a>
   </section>
 </div>

<?php require('footer.php'); ?>
