<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkuVariantOption extends Model
{
    use HasFactory;

    protected $table = 'sku_variant_options';
    protected $fillable = ['product_sku_id', 'variant_option_id'];
}
