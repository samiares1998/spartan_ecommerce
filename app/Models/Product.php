<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    protected $fillable = ['category_id', 'title', 'desc', 'base_price', 'base_stock'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productImage(){
        return $this->hasMany(ProductImage::class);
    }
   // Relación con variantes
   public function variants()
   {
       return $this->hasMany(ProductVariant::class);
   }

   // Relación con SKUs
   public function skus()
   {
       return $this->hasMany(ProductSku::class,'product_id', 'id');
   }

   // Método para verificar si tiene variantes
   public function getHasVariantsAttribute()
   {
       return $this->variants()->exists();
   }

    use HasFactory;
}
