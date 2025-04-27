<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;
use App\Models\ProductVisit;
use App\Models\Product;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $visit = null;
        // Verificar si ya hemos registrado esta visita en la sesión
        if (!$request->session()->has('visit_tracked')) {
            try {
                if (app()->environment('production') || app()->environment('local')) {
                    $ip = $request->ip();
                    $location = Location::get($ip);
                    
                    $visit =   $visit = Visit::create([
                        'ip_address' => $ip,
                        'country' => $location->countryName ?? 'Unknown',
                        'city' => $location->cityName ?? 'Unknown',
                        'browser' => $request->header('User-Agent'),
                        'device' => $this->parseDevice($request->header('User-Agent')),
                        'time_spent' => 0, // Inicializamos en 0
                        'visited_at' => now(),
                    ]);
                    
                    // Guardar el ID de la visita en la sesión
                    $request->session()->put('visit_tracked', $visit->id);
                    $request->session()->put('visit_start_time', $start);
                }
            } catch (\Exception $e) {
                Log::error('Error tracking visit: '.$e->getMessage());
            }
        } else {
            // Actualizar tiempo si ya existe la visita
            $visit = Visit::find($request->session()->get('visit_tracked'));
            if ($visit) {
                $duration = round(microtime(true) - $request->session()->get('visit_start_time'), 2);
                $visit->update(['time_spent' => $duration]);
            }
        }
        
        if($visit && $request->is('product/*')){
            $this->recordProductVisit($request, $visit);
        }
        
        return $response;
    }

    protected function recordProductVisit($request, $visit)
    {
        try {
            $productTitle = $request->route('product');
            
            if (!$productTitle) {
                throw new \Exception('No product title in route');
            }
    
            $product = Product::where('title', $productTitle)->first();
            
            if (!$product) {
                throw new \Exception("Product not found with title: {$productTitle}");
            }
    
            // Ahora funcionará porque los campos son fillable
            ProductVisit::create([
                'visit_id' => $visit->id,
                'product_id' => $product->id,
                'clicked_at' => now()
            ]);
    
            \Log::info("Product visit recorded", [
                'product_id' => $product->id,
                'visit_id' => $visit->id
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Product visit error', [
                'error' => $e->getMessage(),
                'product' => $productTitle,
                'visit_id' => $visit->id ?? null
            ]);
            
            // Para desarrollo, muestra el error directamente
            if (app()->environment('local')) {
                throw $e;
            }
        }
    }
    
    protected function parseDevice($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        }
        return 'Desktop';
    }
}
