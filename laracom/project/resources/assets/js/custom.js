$(document).ready(function () {
    $("#brand-logo").owlCarousel({
        autoPlay: 3000, //Set AutoPlay to 3 seconds
        items: 6,
        itemsDesktop: [1199, 6],
        itemsDesktopSmall: [979, 6]
    });

    $('.select2').select2();

    if ($('#thumbnails li img').length > 0) {
        $('#thumbnails li img').on('click', function () {
            $('#main-image')
                .attr('src', $(this).attr('src') +'?w=400')
                .attr('data-zoom', $(this).attr('src') +'?w=1200');
        });
    }

    $(".img-orderDetail").mouseover(function() {
      $(this).css({ width: '150px', height: '150px' });
    }).mouseout(function() {
      $(".img-orderDetail").css({ width: '50px', height: '50px'});
    });
});

document.addEventListener("DOMContentLoaded", function() {
  // 入力フィールドの値が変更されたときに実行される関数
  function checkInputs() {
      var starRating = document.getElementById('star-rating').value.trim(); // 星評価の値を取得
      var textRating = document.getElementById('text-rating').value.trim(); // テキスト評価の値を取得

      // 両方の入力フィールドが空でない場合にボタンを表示する
      if (starRating !== '' && textRating !== '') {
          document.getElementById('reviewInput').style.display = 'block'; // ボタンを表示する
      } else {
          document.getElementById('reviewInput').style.display = 'none'; // ボタンを非表示にする
      }
  }

  // 入力が変更されるたびに実行されるイベントリスナーを追加
  document.getElementById('star-rating').addEventListener('input', checkInputs);
  document.getElementById('text-rating').addEventListener('input', checkInputs);

  // 初期状態でボタンを隠す
  checkInputs();
});
