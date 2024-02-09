<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Shop\Customers\Customer;

class ReviewProduct extends Model
{
    use HasFactory;

    protected $table = 'review_product';
    protected $primaryKey = 'id';
    protected $fillable = ['product_id','customer_id','review_star','review_comment'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // 顧客との関連付けを定義
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

}
