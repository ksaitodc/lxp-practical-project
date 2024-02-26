<div class="row">
    <div class="col-md-6">
        @if (!empty($product->cover))
            <ul id="thumbnails" class="col-md-4 list-unstyled">
                <li>
                    <a href="javascript: void(0)">
                        <img class="img-responsive img-thumbnail" src="{{ $product->cover }}" alt="{{ $product->name }}" />
                    </a>
                </li>
                @if (isset($images) && !$images->isEmpty())
                    @foreach ($images as $image)
                        <li>
                            <a href="javascript: void(0)">
                                <img class="img-responsive img-thumbnail" src="{{ asset("storage/$image->src") }}"
                                    alt="{{ $product->name }}" />
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
            <figure class="text-center product-cover-wrap col-md-8">
                <img id="main-image" class="product-cover img-responsive" src="{{ $product->cover }}?w=400"
                    data-zoom="{{ $product->cover }}?w=1200">
            </figure>
        @else
            <figure>
                <img src="{{ asset('images/NoData.png') }}" alt="{{ $product->name }}"
                    class="img-bordered img-responsive">
            </figure>
        @endif
    </div>
    <div class="col-md-6">
        <div class="product-description">
            <h1>
                <span style="font-weight: bold;">{{ $product->name }}
                <br>
                <br>
                <b>
                    <span style="color: red;">{{ $product->price * 140 }}<small>{{ config('cart.currency_symbol') }}</span>
                    <span style="color: black;">+ 送料980円</span>
                </b>
                <small>SKU:{{ $product->sku }}</small>
            </h1>
            <div class="description">{!! $product->description !!}</div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    @include('layouts.errors-and-messages')
                    <form action="{{ route('cart.store') }}" class="form-inline" method="post">
                        {{ csrf_field() }}
                        @if (isset($productAttributes) && !$productAttributes->isEmpty())
                            <div class="form-group">
                                <label for="productAttribute">Choose Combination</label> <br />
                                <select name="productAttribute" id="productAttribute" class="form-control select2">
                                    @foreach ($productAttributes as $productAttribute)
                                        <option value="{{ $productAttribute->id }}">
                                            @foreach ($productAttribute->attributesValues as $value)
                                                {{ $value->attribute->name }} : {{ ucwords($value->value) }}
                                            @endforeach
                                            @if (!is_null($productAttribute->sale_price))
                                                ({{ config('cart.currency_symbol') }}
                                                {{ $productAttribute->sale_price }})
                                            @elseif(!is_null($productAttribute->price))
                                                ( {{ config('cart.currency_symbol') }} {{ $productAttribute->price }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <hr>
                        @endif
                        <div class="form-group">
                            <input type="text" class="form-control" name="quantity" id="quantity"
                                placeholder="Quantity" value="{{ old('quantity') }}" />
                            <input type="hidden" name="product" value="{{ $product->id }}" />
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="fa fa-cart-plus"></i> かごに追加
                        </button>
                    </form>
                    
                    <h2>
                        @if(!isset($reviews))
                            <small style="font-size: 18px;">商品画面からレビューを確認できます</small>
                        @elseif($reviews === 0)
                            <div class="reviews">
                                <small>まだレビューが投稿されていません</small>
                            </div>
                            <div class="reviews-form">
                                @if(Auth::check())
                                    <form action="{{ route('review.index') }}" class="form-inline" method="post">
                                        {{ csrf_field() }}
                                        <input type="number" id="star-rating" name="star-rating" min="1" max="5">
                                        <input type="text" id="text-rating" name="text-rating">
                                        <input type="hidden" name="product" value="{{ $product->id }}" />
                                        <button type="submit" class="btn btn-warning"><i class="fa fa-regist-review"></i> 登録 </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <div class="reviews">
                                <table>
                                    @foreach ($reviews as $review)
                                        <tr>
                                            <td>
                                                <div class="star-rating">
                                                    @foreach (range(1, 5) as $_)
                                                        @if ($_ <= $review->review_star)
                                                            <span class="star_on">&#9733;</span>
                                                        @else
                                                            <span class="star_off">&#9733;</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="reviwe-comment">
                                                    <textarea class="review-comment" readonly>{{$review->review_comment}}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div class="reviews-form">
                                @if(Auth::check())
                                    <form action="{{ route('review.index') }}" class="form-inline" method="post">
                                        {{ csrf_field() }}
                                        <input type="number" id="star-rating" name="star-rating" class="review-input" min="1" max="5">
                                        <input type="text" id="text-rating" name="text-rating" class="review-input-text">
                                        <input type="hidden" name="product" value="{{ $product->id }}" />
                                        <button type="submit" class="btn btn-warning" id='reviewInput'><i class="fa fa-regist-review"></i>登録</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($recommendProducts))
        <div class="col-md-24 recommends">
            <h3>
            <span>おすすめ商品</span>
                <div class="recommendation-container">
                    <ul class="col-md-24 list-unstyled recommendUl">
                        @foreach ($recommendProducts as $recommendProduct)
                            <li class="reccomendList">
                                <a href="{{ route( 'front.get.product', $recommendProduct->product->slug ) }}">
                                    @if (!isset($recommendProduct->product->cover) )
                                        <figure class="recommendFigure">
                                            <img class="img-bordered img-responsive recommendImg" src="{{ $recommendProduct->product->cover }}">
                                            <figcaption class="recommendfigCaption">{{ $recommendProduct->product->name }}</figcaption>
                                        </figure>
                                    @else
                                        <figure class="recommendFigure">
                                            <img class="img-responsive img-thumbnail recommendImg" src="{{ asset('images/NoData.png') }}" >
                                            <figcaption class="recommendfigCaption">{{ $recommendProduct->product->name }}</figcaption>
                                        </figure>
                                    @endif
                                    <div class="star-rating">
                                        @foreach (range(1, 5) as $_)
                                            @if ($_ <= $recommendProductReviews->where('product_id', $recommendProduct->product->id)->first()['average_rating'])
                                                <span class="star_on">&#9733;</span>
                                            @else
                                                <span class="star_off">&#9733;</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </h3>
        </div>
    @endif
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var productPane = document.querySelector('.product-cover');
            var paneContainer = document.querySelector('.product-cover-wrap');

            new Drift(productPane, {
                paneContainer: paneContainer,
                inlinePane: false
            });
        });
    </script>
@endsection
