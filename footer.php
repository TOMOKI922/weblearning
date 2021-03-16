<footer id="footer">
  Copyright 学習情報交換.All right Reserved.
</footer>

<script src="js/vendor/jquery-3.5.1.min.js"></script>
<script>
  $(function(){

    // フッターを最下部に固定


    //メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s ]+|[\s ]+$/g, "").length){
      $jsShowMsg.slideToggle('slow');
      setTimeout(function(){$jsShowMsg.slideToggle('slow');}, 5000);
    }

    // 画像プレビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '3px #ccc dashed');
    });
    $dropArea.on('dragleave', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e){
      $dropArea.css('border', 'none');
      var file = this.files[0],
          $img = $(this).siblings('.prev-img'),
          fileReader = new FileReader();

          fileReader.onload = function(event){
            $img.attr('src', event.target.result).show();
          };
          // 画像読み込み
          fileReader.readAsDataURL(file);
    });

    // テキストエリアカウント
    var $countUp = $('#js-count'),
        $countView = $('#js-count-view');
    $countUp.on('keyup', function(e){
      $countView.html($(this).val().length);
    });



    //お気に入り登録・削除
    var $favorite,
        favoriteDrillId;
    $favorite = $('.js-click-favorite') || null;
    favoriteDrillId = $favorite.data('drillid') || null;

    if(favoriteDrillId !== undefined && favoriteDrillId !== null){
      $favorite.on('click', function(){
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajaxLike.php",
          data: {drillId : favoriteDrillId}
        }).done(function(data){
          console.log('Ajax Success');
          $this.toggleClass('fa-heart-o');
          $this.toggleClass('fa-heart');
          $this.toggleClass('active');
          $('.js-click-favorite2').toggleClass('active');
        }).fail(function(msg){
          console.log('Ajax Error');
        });
      });
    }
    //メディアクエリ用メニュー
    $('.js-toggle-sp-menu').on('click', function() {
      $(this).toggleClass('active');
      $('.js-toggle-sp-menu-target').toggleClass('active');
    });
    $('#top-nav a[href]').on('click', function(event) {
      $('.js-toggle-sp-menu').trigger('click');
    });
  });
</script>
</body>
</html>
