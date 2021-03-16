<header>
  <div class="site-width">
    <h1><a href="index.php">学習情報交換</a></h1>
    <div class="nav-menu js-toggle-sp-menu">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <nav id="top-nav" class="js-toggle-sp-menu-target">
      <ul>
        <?php
          if(empty($_SESSION['user_id'])){
         ?>
         <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
         <li><a href="login.php">ログイン</a></li>
        <?php
      }else{
         ?>
         <li><a href="logout.php">ログアウト</a></li>
         <li><a href="mypage.php">マイページ</a></li>
         <li><a href="contact.php">お問い合わせ</a></li>
         <?php
          }
          ?>
        </ul>
      </nav>
    </div>
</header>
