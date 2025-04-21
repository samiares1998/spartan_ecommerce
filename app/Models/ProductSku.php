<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    
    protected $fillable = ['product_id', 'sku', 'price','stock'];

    use HasFactory;

     // Relación con el producto
     public function product()
     {
         return $this->belongsTo(Product::class);
     }
 
     // Relación con las opciones de variante (N:M)
     public function variantOptions()
     {
         return $this->belongsToMany(VariantOption::class, 'sku_variant_options')
             ->withTimestamps();
     }
 
     // Método para obtener las características formateadas (opcional)
     public function getFeaturesAttribute()
     {
         return $this->variantOptions->map(function($option) {
             return $option->variant->name . ': ' . $option->value;
         })->implode(', ');
     }
}
