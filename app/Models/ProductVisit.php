<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVisit extends Model
{
    protected $table = 'product_visits';
    public $timestamps = false;
    protected $fillable = [
        'visit_id',
        'product_id',
        'clicked_at'
        // Agrega aquí cualquier otro campo que necesites asignar masivamente
    ];
    
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}