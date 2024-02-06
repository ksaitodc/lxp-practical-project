<?php

namespace App\Http\Controllers\Front;

use App\Shop\Products\Product;
use App\Shop\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Shop\Products\Transformations\ProductTransformable;

// model
use App\ReviewProduct; 


class ProductController extends Controller
{
    use ProductTransformable;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    /**
     * ProductController constructor.
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepo = $productRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $list = $this->productRepo->searchProduct(request()->input('q'));

        $products = $list->where('status', 1)->map(function (Product $item) {
            return $this->transformProduct($item);
        });

        return view('front.products.product-search', [
            'products' => $this->productRepo->paginateArrayResults($products->all(), 10)
        ]);
    }

    /**
     * Get the product
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(string $slug)
    {
        $product = $this->productRepo->findProductBySlug(['slug' => $slug]);
        $product = $this->transformProduct($product);
        $images = $product->images()->get();
        $category = $product->categories()->first();
        $productAttributes = $product->attributes;

        // 新しいモデル ReviewProduct を使ってデータを取得
        $reviews = ReviewProduct::where('product_id', $product->id)->orderBy('created_at', 'desc')->limit(10)->get();

        if ($reviews->isEmpty()) {
            //レビューが0件の場合を切り分けるため、0を入れる
            $reviews = 0;
        }

        return view('front.products.product', compact(
            'product',
            'images',
            'productAttributes',
            'category',
            'reviews'
        ));
    }
}
