<?php

namespace App\Shop\Products\Repositories;

use App\Shop\AttributeValues\AttributeValue;
use App\Shop\Products\Exceptions\ProductCreateErrorException;
use App\Shop\Products\Exceptions\ProductUpdateErrorException;
use App\Shop\Tools\UploadableTrait;
use Jsdecena\Baserepo\BaseRepository;
use App\Shop\Brands\Brand;
use App\Shop\ProductAttributes\ProductAttribute;
use App\Shop\ProductImages\ProductImage;
use App\Shop\Products\Exceptions\ProductNotFoundException;
use App\Shop\Products\Product;
use App\Shop\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Shop\Products\Transformations\ProductTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\ReviewProduct;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    use ProductTransformable, UploadableTrait;

    /**
     * ProductRepository constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        parent::__construct($product);
        $this->model = $product;
    }

    /**
     * List all the products
     *
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @return Collection
     */
    public function listProducts(string $order = 'id', string $sort = 'desc', array $columns = ['*']) : Collection
    {
        return $this->all($columns, $order, $sort);
    }

    /**
     * Create the product
     *
     * @param array $data
     *
     * @return Product
     * @throws ProductCreateErrorException
     */
    public function createProduct(array $data) : Product
    {
        try {
            return $this->create($data);
        } catch (QueryException $e) {
            throw new ProductCreateErrorException($e);
        }
    }

    /**
     * Update the product
     *
     * @param array $data
     *
     * @return bool
     * @throws ProductUpdateErrorException
     */
    public function updateProduct(array $data) : bool
    {
        $filtered = collect($data)->except('image')->all();

        try {
            return $this->model->where('id', $this->model->id)->update($filtered);
        } catch (QueryException $e) {
            throw new ProductUpdateErrorException($e);
        }
    }

    /**
     * Find the product by ID
     *
     * @param int $id
     *
     * @return Product
     * @throws ProductNotFoundException
     */
    public function findProductById(int $id) : Product
    {
        try {
            return $this->transformProduct($this->findOneOrFail($id));
        } catch (ModelNotFoundException $e) {
            throw new ProductNotFoundException($e);
        }
    }

    /**
     * Delete the product
     *
     * @param Product $product
     *
     * @return bool
     * @throws \Exception
     * @deprecated
     * @use removeProduct
     */
    public function deleteProduct(Product $product) : bool
    {
        $product->images()->delete();
        return $product->delete();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function removeProduct() : bool
    {
        return $this->model->where('id', $this->model->id)->delete();
    }

    /**
     * Detach the categories
     */
    public function detachCategories()
    {
        $this->model->categories()->detach();
    }

    /**
     * Return the categories which the product is associated with
     *
     * @return Collection
     */
    public function getCategories() : Collection
    {
        return $this->model->categories()->get();
    }

    /**
     * Sync the categories
     *
     * @param array $params
     */
    public function syncCategories(array $params)
    {
        $this->model->categories()->sync($params);
    }

    /**
     * @param array $file
     * @param null $disk
     * @return bool
     */
    public function deleteFile(array $file, $disk = null) : bool
    {
        return $this->update(['cover' => null], $file['product']);
    }

    /**
     * @return bool
     */
    public function deleteCover(): bool
    {
        return $this->model->update(['cover' => null]);
    }

    /**
     * @param string $src
     * @return bool
     */
    public function deleteThumb(string $src) : bool
    {
        return DB::table('product_images')->where('src', $src)->delete();
    }

    /**
     * Get the product via slug
     *
     * @param array $slug
     *
     * @return Product
     * @throws ProductNotFoundException
     */
    public function findProductBySlug(array $slug) : Product
    {
        try {
            return $this->findOneByOrFail($slug);
        } catch (ModelNotFoundException $e) {
            throw new ProductNotFoundException($e);
        }
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchProduct(string $text) : Collection
    {
        if (!empty($text)) {
            return $this->model->searchProduct($text);
        } else {
            return $this->listProducts();
        }
    }

    /**
     * @return mixed
     */
    public function findProductImages() : Collection
    {
        return $this->model->images()->get();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file) : string
    {
        return $file->store('products', ['disk' => 'public']);
    }

    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function saveProductImages(Collection $collection)
    {
        $collection->each(function (UploadedFile $file) {
            $filename = $this->storeFile($file);
            $productImage = new ProductImage([
                'product_id' => $this->model->id,
                'src' => $filename
            ]);
            $this->model->images()->save($productImage);
        });
    }

    /**
     * Associate the product attribute to the product
     *
     * @param ProductAttribute $productAttribute
     * @return ProductAttribute
     */
    public function saveProductAttributes(ProductAttribute $productAttribute) : ProductAttribute
    {
        $this->model->attributes()->save($productAttribute);
        return $productAttribute;
    }

    /**
     * List all the product attributes associated with the product
     *
     * @return Collection
     */
    public function listProductAttributes() : Collection
    {
        return $this->model->attributes()->get();
    }

    /**
     * Delete the attribute from the product
     *
     * @param ProductAttribute $productAttribute
     *
     * @return bool|null
     * @throws \Exception
     */
    public function removeProductAttribute(ProductAttribute $productAttribute) : ?bool
    {
        return $productAttribute->delete();
    }

    /**
     * @param ProductAttribute $productAttribute
     * @param AttributeValue ...$attributeValues
     *
     * @return Collection
     */
    public function saveCombination(ProductAttribute $productAttribute, AttributeValue ...$attributeValues) : Collection
    {
        return collect($attributeValues)->each(function (AttributeValue $value) use ($productAttribute) {
            return $productAttribute->attributesValues()->save($value);
        });
    }

    /**
     * @return Collection
     */
    public function listCombinations() : Collection
    {
        return $this->model->attributes()->map(function (ProductAttribute $productAttribute) {
            return $productAttribute->attributesValues;
        });
    }

    /**
     * @param ProductAttribute $productAttribute
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findProductCombination(ProductAttribute $productAttribute)
    {
        $values = $productAttribute->attributesValues()->get();

        return $values->map(function (AttributeValue $attributeValue) {
            return $attributeValue;
        })->keyBy(function (AttributeValue $item) {
            return strtolower($item->attribute->name);
        })->transform(function (AttributeValue $value) {
            return $value->value;
        });
    }

    /**
     * @param Brand $brand
     */
    public function saveBrand(Brand $brand)
    {
        $this->model->brand()->associate($brand);
    }

    /**
     * @return Brand
     */
    public function findBrand()
    {
        return $this->model->brand;
    }

    /**
     * @return 
     */
    public function recommendProducts($product)
    {

         // 指定された商品の特徴ベクトルを取得
         $productVector = collect([$product->category_id, $product->material, $product->brand_id, $product->weight]);
 
         // コサイン類似度を計算して商品を推薦
         return Product::where('id', '!=', $product->id)
         ->get()
         ->map(function ($otherProduct) use ($productVector) {
             $otherVector = collect([$otherProduct->category_id, $otherProduct->material, $otherProduct->brand_id, $otherProduct->weight]);
             // ベクトルの内積を計算
             $dotProduct = $productVector->zip($otherVector)->map(function ($item) {
                 return $item[0] * $item[1];
             })->sum();
             // ベクトルの大きさを計算
             $magnitude1 = sqrt($productVector->map(function ($item) {
                 return $item ** 2;
             })->sum());
             $magnitude2 = sqrt($otherVector->map(function ($item) {
                 return $item ** 2;
             })->sum());
             // 類似度を計算
             $similarity = ($magnitude1 == 0 || $magnitude2 == 0) ? 0 : $dotProduct / ($magnitude1 * $magnitude2);
             return (object)[
                 'product' => $otherProduct,
                 'similarity' => $similarity,
             ];
         })
         ->sortByDesc('similarity')
         ->take(5); // 上位5つの類似商品を取得

    }

        /**
     * @return 
     */
    public function recommendProductReviews($recommendProducts){

        // 平均評価を格納するコレクションを初期化
        $recommendProductReviews = collect();

        // おすすめ商品のIDごとにループしてレビューデータを取得し、平均評価を計算
        foreach ($recommendProducts as $recommendProduct) {
            // 商品に関連するレビューデータの平均評価を取得
            $averageRating = ReviewProduct::where('product_id', $recommendProduct->product->id)->avg('review_star');

            // 平均評価をコレクションに追加
            $recommendProductReviews->push([
                'product_id' => $recommendProduct->product->id,
                'average_rating' => $averageRating ?: 0, // 平均評価がnullの場合は0を設定
            ]);
        }

        // 平均評価のコレクションを返す
        return $recommendProductReviews;
    }
}
