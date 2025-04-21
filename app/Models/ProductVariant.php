<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    
    protected $fillable = ['product_id', 'name', 'slug'];

    use HasFactory;

     // Relación con el producto
     public function product()
     {
         return $this->belongsTo(Product::class);
     }
 
     // Relación con las opciones de variante
     public function options()
     {
         return $this->hasMany(VariantOption::class, 'variant_id');
     }

     public function variantOptions()
    {
        return $this->hasMany(VariantOption::class, 'variant_id');
    }

     // Para generar slugs automáticamente (opcional)
     protected static function booted()
     {
         static::creating(function ($variant) {
             $variant->slug = \Str::slug($variant->name);
         });
     }
}
