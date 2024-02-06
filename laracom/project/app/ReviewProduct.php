<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewProduct extends Model
{
    use HasFactory;

    protected $table = 'review_product';
    protected $primaryKey = 'id';
    protected $fillable = ['product_id', 'review_star', 'review_comment'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
