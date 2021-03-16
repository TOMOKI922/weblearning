<?php

require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
$search = (!empty($_GET['search'])) ? $_GET['search'] : '';
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
if(!is_numeric($currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}
  $listSpan = 20;
  $currentMinNum = (($currentPageNum-1)*$listSpan);
  $dbDrillData = getDrillList($currentMinNum,$search, $category, $sort);
  $dbCategoryData = getCategory();


  debug('画面表示処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<');
  $siteTitle = 'HOME';
  require('head.php');
  ?>

  <body class="page-home page-2colum">

    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

      <section id="sidebar" class="index-sidebar">
        <form name = "" method="get">
          <input type="text" name="search" value="<?php getFormData('search'); ?>">
          <input type="submit" value="検索">
          <h1 class="title">カテゴリー</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="c_id" id="">
              <option value="0" <?php if(getFormData('c_id', true) == 0){echo 'selected';} ?> >選択してください</option>
              <?php foreach($dbCategoryData as $key => $val){ ?>
                <option value="<?php echo $val['id']; ?>" <?php if(getFormData('c_id',true) == $val['id']){echo 'selected';}?> >
                  <?php echo $val['name']; ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <h1 class="title">表示順</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="sort">
              <option value="0" <?php if(getFormData('sort', true) == 0){echo 'selected';} ?> >選択してください</option>
              <option value="1" <?php if(getFormData('sort', true) == 1){echo 'selected';} ?> >日付が古い順</option>
              <option value="2" <?php if(getFormData('sort', true) == 2){echo 'selected';} ?> >日付が新しい順</option>
            </select>
          </div>
          <input type="submit" value="検索">
        </form>
      </section>

      <section id="main" class="index">
        <div class="search-title">
          <div class="search-left">
            <span class="total-num"><?php echo sanitize($dbDrillData['total']); ?></span>件の商品が見つかりました。
          </div>
          <div class="search-right">
            <span class="num"><?php echo (!empty($dbDrillData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo (is_countable($dbDrillData['data'])) ? $currentMinNum+count($dbDrillData['data']) :  0; ?></span>件 / <span class="num"><?php echo sanitize($dbDrillData['total']); ?></span>件中
          </div>
        </div>
        <div class="panel-list">
          <?php
          if(!empty($dbDrillData['data'])):
          foreach($dbDrillData['data'] as $key => $val):
            ?>
            <a href="drillDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&d_id='.$val['id'] : '?d_id='.$val['id']; ?>" class="panel">
              <div class="panel-title">
                <?php echo sanitize($val['name']); ?>
              </div>
              <div class="panel-body">
                <p><?php echo sanitize(mb_substr($val['comment'], 0, 50)); ?></p>
              </div>
            </a>
          <?php
                endforeach;
              endif;
           ?>


        </div>

        <?php pagination($currentPageNum, $dbDrillData['total_page']); ?>
      </section>
    </div>

    <?php require('footer.php'); ?>
