<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ProductSku;
class CartController extends Controller
{
    public function carts(){
        $data = [
            'shop' => Shop::first(),
            'title' => 'Carrito de compras'
        ];

        return view('client.carts', $data);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'sku_id' => 'nullable|exists:product_skus,id'
        ]);
    
        // Obtener el SKU (para productos con variantes) o el producto base
        if ($request->sku_id) {
            $item = ProductSku::with('product')->find($request->sku_id);
            $itemType = 'sku';
            $stock = $item->stock;
            $price = $item->price;
            $title = $item->product->title . ' - ' . 
                     $item->variantOptions->map(function($option) {
                         return $option->value;
                     })->implode(', ');
        } else {
            $item = Product::find($request->product_id);
            $itemType = 'product';
            $stock = $item->base_stock;
            $price = $item->base_price;
            $title = $item->title;
        }
    
        // Verificar stock
        if ($request->quantity > $stock) {
            return response()->json([
                'status' => 'failed',
                'message' => 'No hay suficiente stock disponible',
                'cartCount' => count(session('cart', [])),
                'code' => 202
            ], 202);
        }
    
        $cart = session()->get('cart', []);
        $key = $itemType . '_' . ($request->sku_id ?? $request->product_id);
    
        // Actualizar o agregar al carrito
        if (isset($cart[$key])) {
            $newQuantity = $cart[$key]['quantity'] + $request->quantity;
            
            if ($newQuantity > $stock) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No puedes agregar más de lo disponible en stock',
                    'cartCount' => count($cart),
                    'code' => 202
                ], 202);
            }
            
            $cart[$key]['quantity'] = $newQuantity;
            $statusCode = 201;
            $message = 'Cantidad actualizada en el carrito';
        } else {
            $cart[$key] = [
                'item_type' => $itemType,
                'item_id' => $request->sku_id,
                'product_id' => $request->product_id,
                'title' => $title,
                'quantity' => $request->quantity,
                'price' => $price,
                'stock' => $stock,
               // 'image' => $item->productImage->first()?->path ?? null
            ];
            $statusCode = 200;
            $message = 'Producto añadido al carrito';
        }
    
        session()->put('cart', $cart);
    
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'cartCount' => count($cart),
            'code' => $statusCode
        ], $statusCode);
    }

  
    
    public function updateCart(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);
    
        $cart = session()->get('cart', []);
        
        if (!isset($cart[$request->item_key])) {
            return response()->json([
                'message' => 'Item no encontrado en el carrito',
                'success' => false
            ], 404);
        }
    
        // Obtener el stock disponible según el tipo de item
        if ($cart[$request->item_key]['item_type'] === 'sku') {
            $stock = ProductSku::find($cart[$request->item_key]['item_id'])->stock;
        } else {
            $stock = Product::find($cart[$request->item_key]['item_id'])->base_stock;
        }
    
        // Validar stock
        if ($request->quantity > $stock) {
            return response()->json([
                'message' => 'No hay suficiente stock disponible',
                'success' => false,
                'available_stock' => $stock
            ], 422);
        }
    
        // Actualizar cantidad
        $cart[$request->item_key]['quantity'] = $request->quantity;
        session()->put('cart', $cart);
    
        // Calcular totales
        $subtotal = $cart[$request->item_key]['price'] * $request->quantity;
        $total = $this->calculateCartTotal($cart);
    
        return response()->json([
            'message' => 'Cantidad actualizada',
            'success' => true,
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($total, 2),
            'cartCount' => count($cart)
        ]);
    }

    public function deleteCart(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string'
        ]);
    
        $cart = session()->get('cart', []);
    
        if (isset($cart[$request->item_key])) {
            unset($cart[$request->item_key]);
            session()->put('cart', $cart);
        } else {
            return response()->json([
                'message' => 'Item no encontrado en el carrito',
                'success' => false
            ], 404);
        }
    
        $total = $this->calculateCartTotal($cart);
    
        return response()->json([
            'message' => 'Producto eliminado del carrito',
            'success' => true,
            'total' => number_format($total, 2),
            'cartCount' => count($cart)
        ]);
    }

    protected function calculateCartTotal($cart)
    {
        return array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }
}
