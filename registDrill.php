<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ドリル登録ページ　「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

$d_id = (!empty($_GET['d_id'])) ? $_GET['d_id'] : '';

$dbFormData = (!empty($d_id)) ?  getDrill($_SESSION['user_id'], $d_id) : '';

$edit_flg = (empty($dbFormData)) ? false : true;

$dbCategoryData = getCategory();
debug('ドリルID：'.$d_id);
debug('フォーム用DBデータ：'.print_r($dbFormData, true));
debug('カテゴリデータ：'.print_r($dbCategoryData, true));

if(!empty($d_id) && empty($dbFormData)){
  debug('GETパラメータのドリルIDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
}

if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));

  $name = $_POST['name'];
  $category = $_POST['category_id'];
  $comment = $_POST['comment'];

  if(empty($dbFormData)){
    validRequired($name, 'name');

    validMaxLen($name, 'name');

    validSelect($category, 'category_id');

    validMaxLen($comment, 'comment', 500);

  }else{
    if($dbFormData['name'] !== $name){
      validRequired($name, 'name');
      validMaxLen($name, 'name');
    }
    if($dbFormData['category_id'] !== $category){
      validSelect($category, 'category_id');
    }
    if($dbFormData['comment'] !== $comment){
      validMaxLen($comment, 'comment', 500);
    }
  }
  if(empty($err_msg)){
    debug('バリデーションOKです。');

    try{

      $dbh = dbConnect();
      debug('DB更新です。');
      if($edit_flg){
      $sql = 'UPDATE drill SET name = :name, category_id = :category, comment = :comment WHERE user_id = :u_id AND id = :d_id';
      $data = array(':name' => $name, ':category' => $category, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':d_id' => $d_id);
    }else{
      debug('DB新規登録です。');
      $sql = 'INSERT INTO drill (name, category_id, comment, user_id, created_at) VALUES (:name, :category, :comment, :u_id, :date)';
      $data = array(':name' => $name, ':category' => $category, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL:'.$sql);
      debug('流し込みデータ:'.print_r($data, true));

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      }
    } catch(Exception $e){
      error_log('エラー発生:'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
$siteTitle = (!$edit_flg) ? 'ドリル登録' : 'ドリル編集';
require('head.php');
 ?>

    <body class="page-profEdit page-2colum page-logined">

      <?php
        require('header.php');
       ?>

       <div id="contents" class="site-width">
         <h1 class="page-title"><?php echo (!$edit_flg) ? 'ドリルを投稿する' : 'ドリルを編集する'; ?></h1>
         <section id="main">
            <div class="form-container">
              <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
                <div class="area-msg">
                  <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                </div>
                <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                  タイトル<span class="label-require">必須</span>
                  <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
                </label>
                <div class="area-msg">
                  <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
                </div>
                <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
                  カテゴリ<span class="label-require">必須</span>
                    <select name="category_id" id="">
                      <option value="0" <?php if(getFormData('category_id') == 0){echo 'selected';}?> >選択してください</option>
                      <?php foreach($dbCategoryData as $key => $val){ ?>
                        <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id']){echo 'selected'; } ?> >
                          <?php echo $val['name']; ?>
                        </option>
                      <?php } ?>
                    </select>
                </label>
                <div class="area-msg">
                  <?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id']; ?>
                </div>
                <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>" >
                  詳細
                  <textarea name="comment" id="js-count" cols="30" rows="10" style="height: 150px;"><?php echo getFormData('comment'); ?></textarea>
                </label>
                <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
                <div class="area-msg">
                  <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
                </div>
                <div class="btn-container">
                  <input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '投稿する' : '更新する'; ?>" >
                </div>
              </form>
            </div>
         </section>
         <?php require('sidebar_mypage.php'); ?>
       </div>
       <?php require('footer.php'); ?>
