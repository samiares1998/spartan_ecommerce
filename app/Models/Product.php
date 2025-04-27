<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    protected $fillable = ['id','category_id', 'title', 'desc', 'base_price', 'base_stock', 'priority'];

    public function scopeFeatured($query, $limit = null)
    {
        $query = $query->where('priority', '>', 0)
                      ->orderBy('priority')
                      ->orderByDesc('created_at');
        
        if ($limit) {
            $query->take($limit);
        }
        
        return $query;
    }

    // Método para cambiar prioridad
    public function setPriority($value)
    {
        $this->update(['priority' => max(0, min(255, (int)$value))]);
    }
    
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
