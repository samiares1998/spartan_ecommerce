<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    protected $fillable = ['id','category_id', 'title', 'desc', 'base_price', 'base_stock', 'priority'];

    public function scopeFeatured($query, $limit = null)
    {
        // Primero obtener IDs de productos destacados
        $featuredIds = Product::where('priority', '>', 0)
                             ->orderBy('priority')
                             ->pluck('id');
        
        // Si no se especifica límite, devolver todos ordenados
        if (!$limit) {
            return $query->orderByRaw(
                "FIELD(id, ".$featuredIds->implode(',').") DESC"
            )->orderByDesc('created_at');
        }
        
        // Calcular cuántos productos normales necesitamos
        $needed = $limit - $featuredIds->count();
        
        if ($needed > 0) {
            // Combinar productos destacados con otros productos
            return $query->whereIn('id', $featuredIds)
                       ->orWhere(function($q) use ($needed) {
                           $q->where('priority', 0)
                             ->orderByDesc('created_at')
                             ->limit($needed);
                       })
                       ->orderByRaw(
                           "CASE WHEN priority > 0 THEN 0 ELSE 1 END, priority"
                       )
                       ->limit($limit);
        }
        
        return $query->whereIn('id', $featuredIds)
                   ->orderBy('priority')
                   ->limit($limit);
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
