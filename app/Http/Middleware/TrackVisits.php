<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        \Log::info('TrackVisits middleware ejecutándose');
        \Log::info('IP: '.$request->ip());       
        try {
            if (app()->environment('production') || app()->environment('local')) {
                $ip = $request->ip();
                
                // Obtener ubicación
                $location = Location::get($ip);
                
                // Registrar la visita
                Visit::create([
                    'ip_address' => $ip,
                    'country' => $location->countryName ?? 'Unknown',
                    'city' => $location->cityName ?? 'Unknown',
                    'browser' => $request->header('User-Agent'),
                    'device' => $this->parseDevice($request->header('User-Agent')),
                    'visited_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error tracking visit: '.$e->getMessage());
        }

        return $response;
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
