<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class materialsMst extends Model
{
    use HasFactory;

    protected $table = 'materials_mst';
    protected $primaryKey = 'id';
    protected $fillable = ['material_name'];
}
