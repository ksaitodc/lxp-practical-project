<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
//review用に新規作成したvalidator
use App\Shop\Carts\Requests\AddToReview;
// 各インターフェース、必要に応じてレビュー用のインターフェースを作成する
use App\Shop\Carts\Repositories\Interfaces\CartRepositoryInterface;
use App\Shop\Couriers\Repositories\Interfaces\CourierRepositoryInterface;
use App\Shop\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Shop\ProductAttributes\Repositories\ProductAttributeRepositoryInterface;

use App\Shop\Products\Transformations\ProductTransformable;
use App\Http\Controllers\Controller;
// model
use App\ReviewProduct; 

use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{   
    use ProductTransformable;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepo;

    /**
     * @var CourierRepositoryInterface
     */
    private $courierRepo;
    
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepo;
    
    /**
     * CartController constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param CourierRepositoryInterface $courierRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CourierRepositoryInterface $courierRepository,
        ProductRepositoryInterface $productRepository,
        ProductAttributeRepositoryInterface $productAttributeRepository
    ) {
        $this->cartRepo = $cartRepository;
        $this->courierRepo = $courierRepository;
        $this->productRepo = $productRepository;
        $this->productAttributeRepo = $productAttributeRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd($customer);
        
        $reviews = ReviewProduct::with('customer')->orderBy('created_at', 'desc')->paginate(10);


        return view('admin.reviews.list', ['reviews' => $reviews]);

    }

    public function store(AddToReview $request)
    {
        $requestCollection = collect($request);
        \Log::debug($requestCollection);
        $customer = Auth::user();
        $productId = intVal($requestCollection->get('productId'));
        $starRating = intVal($requestCollection->get('starRating'));
        $textRating = $requestCollection->get('textRating');


        $slug = $requestCollection['product']['slug'];
        
    
         // 新しいレビューを作成
        $review = new ReviewProduct([
            'product_id' => $productId,
            'customer_id' => $customer->id,
            'review_star' => $starRating,
            'review_comment' => $textRating,
        ]);

        // レビューを保存
        $review->save();

        return redirect()->route('front.get.product', ['product' => $slug])->with('message', '評価とコメントを登録しました');

    }
}

