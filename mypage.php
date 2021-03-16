<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


require('auth.php');

$u_id = $_SESSION['user_id'];
$drillData = getMyDrills($u_id);
$favoriteData = getMyFavorite($u_id);
$archiveData = getMyArchive($u_id);

debug('投稿したドリルデータ:'.print_r($drillData, true));
debug('取得したドリルデータ:'.print_r($favoriteData, true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<,');

$siteTitle = 'マイページ';
require('head.php');
?>

  <body class="page-mypage page-2colum page-logined">

    <style>
      #main{
        border: none !important;
      }
    </style>

    <?php require('header.php'); ?>

    <p id="js-show-msg" style="display: none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
      <?php $_SESSION['msg_success'] = ''; ?>
    </p>

    <div id="contents" class="site-width">
      <h1 class="page-title">マイページ</h1>
      <section id="main">
        <section class="list panel-list">
          <h2 class="title mypage-title">
            投稿ドリル
          </h2>
          <?php
            if(!empty($drillData)):
              foreach($drillData as $key => $val):
          ?>
            <a href="registDrill.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&d_id='.$val['id'] : '?d_id='.$val['id']; ?>" class="panel">
              <div class="panel-title">
                <?php echo sanitize($val['name']); ?>
              </div>
              <div class="panel-body">
                <?php echo sanitize(mb_substr($val['comment'], 0 , 30)); ?>
              </div>
            </a>
          <?php
            endforeach;
          endif;
           ?>
        </section>

        <style>
          .list{
            margin-bottom: 30px;
          }
        </style>
        <section class="list panel-list">
          <h2 class="title mypage-title">
            お気に入りドリル一覧
          </h2>
          <?php
            if(!empty($favoriteData)):
              foreach($favoriteData as $key => $val):
           ?>
            <a href="drillDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&d_id='.$val['id'] : '?d_id='.$val['id']; ?>" class="panel">
              <div class="panel-title">
                <?php echo sanitize($val['name']); ?>
              </div>
            </a>
           <?php
                  endforeach;
                endif;
            ?>
        </section>
        <section class="list panel-list">
          <h2 class="title mypage-title">達成ドリル一覧</h2>
          <?php
            if(!empty($archiveData)):
              foreach($archiveData as $key => $val):
           ?>
           <a href="drillDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&d_id='.$val['id'] : '?d_id='.$val['id']; ?>" class="panel">
             <div class="panel-title">
               <?php echo sanitize($val['name']); ?>
             </div>
           </a>
          <?php
              endforeach;
            endif;
          ?>
        </section>
      </section>

      <?php require('sidebar_mypage.php'); ?>
    </div>

    <?php require('footer.php'); ?>
