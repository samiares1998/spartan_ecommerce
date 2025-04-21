<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['shop_id', 'name', 'path','slug'];

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    // Relación con productos (DEBE SER EN PLURAL)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    use HasFactory;
}
