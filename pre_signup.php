<?php
require('function.php');

debug('「「「「「「「「「「「「「「');
debug('「「　ユーザー登録ページ　「「');
debug('「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST)){
  $email = $_POST['email'];

  validRequired($email, 'email');


  if(empty($err_msg)){
    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validEmailDup($email);


      if(empty($err_msg)){
        $urltoken = hash('sha256', uniqid(rand(),1));
        $url = "http://localhost:8080/signup.php?urltoken=".$urltoken;
        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO pre_user (urltoken, email, date) VALUES(:urltoken, :email, :created_at)';
          $data = array(':urltoken' => $urltoken,':email' => $email,
                        ':created_at' => date('Y-m-d H:i:s')
        );
        $stmt = queryPost($dbh, $sql, $data);
        header("Location: signup.php?urltoken=".$urltoken);
      /*  $from = 'info@learn.com';
        $to = $email;
        $subject = '【仮登録の受付】学習情報交換';
        $comment = <<<EOT
 この度はご登録いただきありがとうございます。
24時間以内に下記のURLからご登録下さい。
本登録ページ: {$url}
※認証キーの有効期限は30分となります
EOT;
          sendMail($from, $to, $subject, $comment);
*/
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key_limit'] = time()+(60*30);
          debug('セッション変数の中身：'.print_r($_SESSION, true));



      } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
      }
    }
  }



$siteTitle = 'ユーザー仮登録';
require('head.php');
 ?>

 <body class="page-signup page-1colum">

   <?php
    require('header.php');
    ?>

    <div id="contents" class="site-width signup-contents">

      <section id="main" class="signup">

        <div class="form-container">

          <form action="" method="post" class="form">
            <h2 class="title">ユーザー仮登録</h2>
            <div class="area-msg">
              <?php
                if(!empty($err_msg['common'])) echo $err_msg['common'];
               ?>
             </div>
             <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
               Email
               <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
             </label>
             <div class="area-msg">
               <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
             </div>

             <div class="btn-container">
               <input type="submit" class="btn btn-mid" value="仮登録する">
             </div>
           </form>
         </div>

       </section>
     </div>

     <?php require('footer.php'); ?>
