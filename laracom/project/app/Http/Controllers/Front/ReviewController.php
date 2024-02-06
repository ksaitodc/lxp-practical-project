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

    }

    public function store(AddToReview $request)
    {
        $product = $this->productRepo->findProductById($request->input('product'));

         // 新しいレビューを作成
        $review = new ReviewProduct([
            'product_id' => $product->id,
            'review_star' => $request->input('star-rating'),
            'review_comment' => $request->input('text-rating'),
        ]);

        // レビューを保存
        $review->save();

        return redirect()->route('front.get.product', ['product' => $product->slug])->with('message', '評価とコメントを登録しました');

    }
}

