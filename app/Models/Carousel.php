<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'title',
        'image',
        'video',
        'description',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}