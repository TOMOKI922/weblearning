<?php
require('function.php');
debug('「「「「「「「「「「「「「「');
debug('「「　ユーザー登録ページ　「「');
debug('「「「「「「「「「「「「「「「「「');
debugLogStart();
if(empty($_GET)){
  debug('仮登録ページに戻ります');
  header("Location: pre_signup.php");
}else{
  $urltoken = isset($_GET['urltoken']) ? $_GET['urltoken'] : NULL;
  if($urltoken == ''){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['urltoken'] = 'トークンがありません。';
  }else{
    debug('トークン確認');
    try{
      $dbh = dbConnect();
      $sql = "SELECT email FROM pre_user WHERE urltoken = :urltoken AND flg = 0";
      $data = array(':urltoken' => $urltoken);
      $stmt = queryPost($dbh, $sql, $data);
      $rowCount = $stmt->rowCount();
      if($rowCount == 1){
        $mail_array = $stmt->fetch();
        $mail = $mail_array['email'];
        $_SESSION['email'] = $mail;
      }else{
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['urltoken_timeover'] = 'このURLは利用できません';
      }
    } catch(Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
if(!empty($_POST)){

  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
  $name = $_POST['username'];


  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');
  validRequired($name, 'name');

  if(empty($err_msg)){


    validNameDup($name);

    validHalf($pass, 'pass');

    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    validMaxLen($pass_re, 'pass_re');
    validMinLen($pass_re, 'pass_re');

    validMaxLen($name, 'name');
    validMinLen($name, 'name');

    if(empty($err_msg)){
      validMatch($pass, $pass_re, 'pass_re');

      if(empty($err_msg)){

        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO users (username, password, email, pic, created_at, updated_at) VALUES(:name, :pass, :email, :pic, :created_at, :updated_at)';
          $data = array(':name' => $name ,':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['email'],
                        ':pic' => 'uploads/715b9ec26ff3fef6ed0b4bc927250ade399a1c69.jpeg',
                        ':created_at' => date('Y-m-d H:i:s'),
                        ':updated_at' => date('Y-m-d H:i:s')
        );
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
          $sesLimit = 60 * 60;
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = $sesLimit;
          $_SESSION['user_id'] = $dbh->lastInsertId();
          debug('セッション変数の中身：'.print_r($_SESSION, true));
          header("Location:mypage.php");
        }
        $sql = "UPDATE pre_user SET flg=1 WHERE email = :email";
        $data = array(':email' => $_SESSION['email']);
        $stmt = queryPost($dbh, $sql, $data);





      } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
      }
    }
  }

}

$siteTitle = 'ユーザー登録';
require('head.php');
 ?>

 <body class="page-signup page-1colum">

   <?php
    require('header.php');
    ?>

    <div id="contents" class="site-width">

      <section id="main">

        <div class="form-container">

          <form action="" method="post" class="form">
            <h2 class="title">ユーザー登録</h2>
            <div class="area-msg">
              <?php
                if(!empty($err_msg['common'])) echo $err_msg['common'];
               ?>
             </div>
             <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
               Email
              <span class="email"><?php echo $_SESSION['email']; ?></span>
             </label>
             <div class="area-msg">
               <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
             </div>
             <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
               パスワード<span style="font-size:12px">※英数字6文字以上</span>
               <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
             </label>
             <div class="area-msg">
               <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
             </div>
             <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
               パスワード（再入力）
               <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
             </label>
             <div class="area-msg">
               <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
             </div>
             <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
               ユーザーネーム
               <input type="text" name="username" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>">
             </label>
             <div class="area-msg">
               <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
             </div>
             <div class="btn-container">
               <input type="submit" class="btn btn-mid" value="登録する">
             </div>
           </form>
         </div>

       </section>
     </div>

     <?php require('footer.php'); ?>
