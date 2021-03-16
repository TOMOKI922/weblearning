<?php
//ログの表示
ini_set('log_errors', 'on');

ini_set('error_log', 'php.log');

//デバッグ設定
$debug_flg = true;
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//セッションの設定
session_save_path('\xampp\tmp');
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);

session_start();
session_regenerate_id();

function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>> 画面処理開始');
  debug('セッションＩＤ:'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

//メッセージ設定
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'パスワード（再入力）があっていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('MSG18', 'その名前は既に登録されています');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');

//エラーメッセージ設定
$err_msg = array();

//入力チェック
function validRequired($str, $key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//メールの形式チェック
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

//メールの重複チェック
function validEmailDup($email){
  global $err_msg;

  try{

    $dbh = dbConnect();

    $sql = 'SELECT count(*) FROM users WHERE email = :email  AND delete_flg = 0';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

//名前の重複チェック
function validNameDup($name){
  global $err_msg;

  try{

    $dbh = dbConnect();

    $sql = 'SELECT count(*) FROM users WHERE username = :name  AND delete_flg = 0';
    $data = array(':name' => $name);
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty(array_shift($result))){
      $err_msg['username'] = MSG18;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

//メールの確認
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

//文字数チェック
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}

function validMaxLen($str, $key, $max = 256){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//半角チェック
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}

//半角数字チェック
function validZip($str, $key){
  if(!preg_match("/^\d{7}$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}

//電話番号チェック
function validTel($str, $key){
  if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}
//年齢チェック
function validAge($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG17;
  }
}

//文字数チェック
function validLength($str, $key, $len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len.MSG14;
  }
}

//パスワードチェック
function validPass($str, $key){

  validHalf($str, $key);

  validMaxLen($str, $key);

  validMinLen($str, $key);
}

//カテゴリーチェック
function validSelect($str, $key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG15;
  }
}

//エラーメッセージ取得
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

//ログイン期限チェック
function isLogin(){
  if(!empty($_SESSION['login_date'])){
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン期限オーバーです。');

      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限内です。');
      return true;
    }
  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}

//DB接続を関数化
function dbConnect(){

  $dsn = 'mysql:dbname=message;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

//クエリ送信を関数化
function queryPost($dbh, $sql, $data){
  $stmt = $dbh->prepare($sql);
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('失敗したSQL:'.print_r($stmt, true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリに成功しました。');
  return $stmt;
}

function getUser($user_id){
  debug('ユーザー情報を取得します。');

  try{
    $dbh = dbConnect();

    $sql = 'SELECT * FROM users WHERE id = :user_id AND delete_flg = 0';

    $data = array(':user_id' => $user_id);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

  function sendMail($from, $to, $subject, $comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
      mb_language("Japanese");
      mb_internal_encoding("UTF-8");

      $result = mb_send_mail($to, $subject, $comment, "From:".$from);

      if($result) {
        debug('メールを送信しました。');
      } else {
        debug('【エラー発生】メールの送信にしました。');
      }
    }
  }

//Sessionを取得
  function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
      return $_SESSION[$key];
    }
  }

//ランダムキー生成
  function makeRandKey($length = 8){
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i = 0; $i < $length; ++$i){
      $str .= $chars[mt_rand(0,61)];
    }
    return $str;
  }

  //画像の表示
  function showImg($path){
    if(empty($path)){
      return 'img/sample-img.png';
    }else{
      return $path;
    }
  }
  //GETパラメータ付与
  // $del_key : 付与から取り除きたいGETパラメータのキー
  function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
      $str = '?';
      foreach($_GET as $key => $val){
        if(!in_array($key, $arr_del_key, true)){//取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
          $str .= $key.'='.$val.'&';
        }
      }
      $str = mb_substr($str, 0, -1, "UTF-8");
      return $str;
    }
  }

  //サニタイズ
  function sanitize($str){
    return htmlspecialchars($str, ENT_QUOTES);
  }
  // フォーム入力保持
  function getFormData($str, $flg = false){
    if($flg){
      $method = $_GET;
    }else{
      $method = $_POST;
    }
    global $dbFormData;
    if(!empty($dbFormData)){

      if(!empty($err_msg[$str])){

        if(isset($method[$str])){
          return sanitize($method[$str]);
        }else{
          return sanitize($dbFormData[$str]);
        }
      }else{
        if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
          return sanitize($method[$str]);
        }else{
          return sanitize($dbFormData[$str]);
        }
      }
    }else{
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }
    }
  }

  function uploadImg($file, $key){
    debug('画像アップロード処理開始');
    debug('FILE情報:'.print_r($file, true));

    if(isset($file['error']) && is_int($file['error'])) {
      try{
        switch($file['error']){
          case UPLOAD_ERR_OK:
            break;
          case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('ファイルが選択されていません');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('ファイルサイズが大きすぎます');
          default:
            throw new RuntimeException('その他のエラーが発生しました');
        }

        $type = @exif_imagetype($file['tmp_name']);
        if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
          throw new RuntimeException('画像形式が未対応です');
        }

        $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
        if(!move_uploaded_file($file['tmp_name'], $path)){
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }

        chmod($path, 0644);
        debug('ファイルは正常にアップロードされました');
        debug('ファイルパス：'.$path);
        return $path;
      } catch (RuntimeException $e) {
        debug($e->getMessage());
        global $err_msg;
        $err_msg[$key] = $e->getMessage();
      }
    }
  }
function getDrillOne($d_id){
  debug('商品情報を取得します。');
  debug('商品ID：'.$d_id);

  try{
    $dbh = dbConnect();

    $sql = 'SELECT d.id, d.name, d.comment, d.user_id, d.created_at, d.update_time, c.name AS category
            FROM drill AS d LEFT JOIN category AS c ON d.category_id = c.id WHERE d.id = :d_id AND d.delete_flg = 0 AND c.delete_flg = 0';
    $data = array(':d_id' => $d_id);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

  function getCategory(){
    debug('カテゴリー情報を取得します。');

    try{
      $dbh = dbConnect();

      $sql = 'SELECT * FROM category';
      $data = array();
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        return $stmt->fetchAll();
      }else{
        return false;
      }
    } catch(Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
    }
}
  function isLike($u_id, $d_id){
    debug('お気に入り情報があるか確認します。');
    debug('ユーザーID：'.$u_id);
    debug('ドリルID：'.$d_id);
    try{
      $dbh = dbConnect();

      $sql = 'SELECT * FROM favorite WHERE drill_id = :d_id AND user_id = :u_id';
      $data = array(':u_id' => $u_id, ':d_id' => $d_id);

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt->rowCount()){
        debug('お気に入りです');
        return true;
      }else{
        debug('特に気に入ってません');
        return false;
      }
    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
    }
  }

  function isArchive($u_id, $d_id){
    debug('達成ドリル情報があるか確認します。');
    debug('ユーザーID：'.$u_id);
    debug('ドリルID：'.$d_id);
    try{
      $dbh = dbConnect();

      $sql = 'SELECT * FROM archive WHERE drill_id = :d_id AND user_id = :u_id';
      $data = array(':u_id' => $u_id, ':d_id' => $d_id);

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt->rowCount()){
        debug('達成しています');
        return true;
      }else{
        debug('達成していません');
        return false;
      }
    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
    }
  }



  function getDrill($u_id, $d_id){
    debug('ドリル情報を取得します。');
    debug('ユーザーID：'.$u_id);
    debug('ドリルID：'.$d_id);

    try{
      $dbh = dbConnect();

      $sql = 'SELECT * FROM Drill WHERE user_id = :u_id AND id = :d_id AND delete_flg = 0';
      $data = array(':u_id' => $u_id, ':d_id' => $d_id);

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
        return false;
      }
    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
    }
  }
  function getMsgList($d_id){
    debug('コメント情報を取得します。');
    try{
      $dbh = dbConnect();
      $sql = 'SELECT id FROM msg WHERE drill_id = :d_id AND delete_flg = 0';
      $data = array(':d_id' => $d_id);

      $stmt = queryPost($dbh, $sql, $data);
      $rst['total'] = $stmt->rowCount();
      if(!$stmt){
        return false;
      }
      $sql = 'SELECT m.id, m.msg, u.username, u.pic FROM msg AS m JOIN users AS u ON m.user_id = u.id WHERE drill_id = :d_id AND m.delete_flg = 0';
      $data = array(':d_id' => $d_id);
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $rst['data'] = $stmt->fetchAll();
        return $rst;
      }else{
        return false;
      }
    } catch(Exception $e) {
      error_log('エラー発生:'.$e->getMessage());
    }
  }
  function getDrillList($currentMinNum = 1, $search, $category, $sort, $span = 20){
    debug('商品情報を取得します。');

    try{
      $dbh = dbConnect();
      $sql = 'SELECT id FROM drill';
      if(!empty($search)){

        $suspect = str_replace("　"," ", $search);
        $array = explode(" ", $suspect);
        foreach($array as $val){
          $sendCondition[] = "name LIKE '%{$val}%'";
        }
        var_dump($sendCondition);
          $sendCondition = implode(' AND ',$sendCondition);
        var_dump($sendCondition);
        $sql .= ' WHERE '.$sendCondition;
      }
      if(!empty($category)){$sql .= ' WHERE category_id = '.$category;}
      if(!empty($sort)){
        switch($sort){
         case 1:
         $sql .= ' ORDER BY created_at ASC';
          break;
          case 2:
          $sql .= ' ORDER BY created_at DESC';
          break;
        }
      }
      $data = array();
      $stmt = queryPost($dbh, $sql, $data);
      if(!empty($stmt)){
      $rst['total'] = $stmt->rowCount();
      $rst['total_page'] = ceil($rst['total']/$span);
      }

      if(!$stmt){
        return false;
      }

      $sql = 'SELECT * FROM drill';
      if(!empty($search)){
        $sendCondition = NULL;
        $suspect = str_replace("　"," ", $search);
        $array = explode(" ", $suspect);
        foreach($array as $val){
          $sendCondition[] = "name LIKE '%{$val}%'";
        }
        $sendCondition = implode(' AND ', $sendCondition);
        $sql .= ' WHERE '.$sendCondition;
      }
      if(!empty($category)){$sql .= ' WHERE category_id='.$category;}
      if(!empty($sort)){
        switch($sort){
        case 1:
        $sql .= ' ORDER BY created_at ASC';
          break;
        case 2:
        $sql .= ' ORDER BY created_at DESC';
          break;
      }
    }else{
      $sql .= ' ORDER BY created_at DESC';
    }
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();
    debug('SQL:'.$sql);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

  function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
    if($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum - 4;
      $maxPageNum = $currentPageNum;
    }elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum - 3;
      $maxPageNum = $currentPageNum + 1;
    }elseif($currentPageNum == 2 && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum - 1;
      $maxPageNum = $currentPageNum + 3;
    }elseif($currentPageNum == 1 && $totalPageNum > $pageColNum){
      $minPageNum = $currentPageNum;
      $maxPageNum = 5;
    }elseif($totalPageNum < $pageColNum ){
      $minPageNum = 1;
      $maxPageNum = $totalPageNum;
    }else{
      $minPageNum = $currentPageNum - 2;
      $maxPageNum = $currentPageNum + 2;
    }

    echo '<div class="pagination">';
      echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){echo 'active';}
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
      echo '</ul>';
      echo '</div>';
  }

  function getMyDrills($u_id){
    debug('自分のドリル情報を取得します');
    debug('ユーザーID：'.$u_id);

    try {
      $dbh = dbConnect();
      $sql = 'SELECT * FROM drill WHERE user_id = :u_id AND delete_flg = 0';
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        return $stmt->fetchAll();
      }else{
        return false;
      }
    } catch (Exception $e) {
      error_log('エラー発生:'.$e->getMessage());
    }
  }
  function getMyFavorite($u_id){
    debug('自分の登録ドリル情報を取得します。');
    debug('ユーザーID:'.$u_id);

    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM favorite AS f LEFT JOIN drill AS d ON f.drill_id = d.id WHERE f.user_id = :u_id';
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        return $stmt->fetchAll();
      }else{
        return false;
      }
    } catch(Exception $e) {
      error_log('エラー発生:'.$e->getMessage());
    }
  }
  function getMyArchive($u_id) {
    debug('自分の達成ドリル情報を取得します。');
    debug('ユーザーID:'.$u_id);

    try{
      $dbh = dbConnect();
      $sql = 'SELECT * FROM archive AS a LEFT JOIN drill AS d ON a.drill_id = d.id WHERE a.user_id = :u_id';
      $data = array(':u_id' => $u_id);
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        return $stmt->fetchAll();
      }else{
        return false;
      }
    } catch(Exception $e) {
      error_log('エラー発生:'.$e->getMessage());
    }
  }
