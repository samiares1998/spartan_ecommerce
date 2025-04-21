<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantOption extends Model
{
    
    protected $fillable = ['variant_id', 'value'];

 
    use HasFactory;
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
        // Si usas el nombre estándar, no necesitas el segundo parámetro
    }
    
    public function skus()
    {
        return $this->belongsToMany(ProductSku::class, 'sku_variant_options', 'variant_option_id', 'product_sku_id');
    }
}
