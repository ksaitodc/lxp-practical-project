<template>
	<div>
		<div class="reviews">
			<template v-if="reviews === 0">
				<small>まだレビューが投稿されていません</small>
			</template>
			<template v-else>
				<table>
					<tbody>
						<div v-for="review in reviews" :key="review.id">
							<tr>
								<td>
									<div class="starRating">
										<span v-for="star in 5" :key="star" :class="{ 'star_on': star <= review.review_star, 'star_off': star > review.review_star }">&#9733;</span>
									</div>
								</td>
							</tr>	
							<tr>
								<td>
									<div class="review-comment">
										<textarea class="review-comment" readonly v-model="review.review_comment"></textarea>
									</div>
								</td>
							</tr>
						</div>
					</tbody>
				</table>
			</template>
		</div>
		<div class="reviews-form">
			<template v-if="authenticated">
				<form @submit.prevent="submitReview" class="form-inline">
					<input type="number" id="starRating" name="starRating" class="review-input" min="1" max="5" v-model="starRating">
					<input type="text" id="textRating" name="textRating" class="review-input-text" v-model="textRating">
					<input type="hidden" id="product.id" name="product.id" v-model="product.id">
					<button type="submit" class="btn btn-warning" id='reviewInput'><i class="fa fa-regist-review"></i>登録</button>
				</form>
			</template>
		</div>
	</div>
</template>

<script>
import axios from 'axios';
export default {
  props: ['reviews', 'authenticated', 'product'],
  data() {
    return {
      starRating: '',
      reviewText: ''
    };
  },
  methods: {
	mounted() {
    // ページが読み込まれた際に実行される処理
    document.getElementById('reviewInput').style.display = 'none'; // ボタンを非表示にする
	},
	checkInputs() {
      // 両方の入力フィールドが空でない場合にボタンを表示する
      if (this.starRating !== '' && this.textRating !== '') {
        document.getElementById('reviewInput').style.display = 'block'; // ボタンを表示する
      } else {
        document.getElementById('reviewInput').style.display = 'none'; // ボタンを非表示にする
      }
    },

    submitReview() {
		const reviewData = {
			starRating: this.starRating,
			textRating: this.textRating,
			productId: this.product.id,
			product: this.product
		};

      axios.post('/review', reviewData)
        .then(response => {
          console.log('レビューが送信されました:', response.data);
          // 送信後の処理を追加
           window.location.reload();
        })
        .catch(error => {
           // エラーが発生した場合の処理
			if (error.response) {
			// サーバーからのレスポンスがある場合
			console.log('レビューが送信されました:', response.data);
			console.error('サーバーからのエラーレスポンス:', error.response.data);
			} else if (error.request) {
			// リクエストが行われたがレスポンスがない場合
			console.log('レビューが送信されました:', response.data);
			console.error('レスポンスがありませんでした:', error.request);
			} else {
			// リクエストを送信する前にエラーが発生した場合
			console.log('レビューが送信されました:', response.data);
			console.error('リクエスト送信前にエラーが発生しました:', error.message);
			}
        });
    }
  },
};
</script>


<style scoped>
/* ここにコンポーネントのスタイルを追加 */
.star_on {
  color: gold;
}
.star_off {
  color: gray;
}
</style>
