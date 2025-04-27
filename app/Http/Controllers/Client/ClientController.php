<?php

namespace App\Http\Controllers\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Carousel;
use App\Models\ContactForm;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Validator;
use Str;


class ClientController extends Controller
{
    public function index()
    {
        if(!Shop::exists()){
            return redirect()->route('register');
        }
    
        $data = [
            'shop' => Shop::first(),
            'dataCarousel' => Carousel::all(),
            'product' => Product::with(['skus', 'category', 'productImage'])
                            ->orderByDesc('id')
                            ->take(8)
                            ->get(),
            'category' => Category::orderByDesc('id')->take(4)->get(),
            'title' => 'Home'
        ];

   
        return view('client.index', $data);
    }
    public function products(){
        $data = [
            'shop' => Shop::first(),
            'product' => Product::orderBy('id', 'DESC')->paginate(16),
            'category' => Category::all()->sortByDesc('id'),
            'title' => 'Products'
        ];

        return view('client.products', $data);
    }

    public function searchProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'product' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->route('clientHome')->withErrors($validator)->withInput();
        }else{
            
            $search = str_replace(' ', '-', strtolower($request->product));

            $data = [
                'title' => 'Result',
                'shop' => Shop::first(),
                'product' => Product::where('title', 'LIKE', '%'.$search.'%')->orderBy('id', 'DESC')->paginate(20),
                'search' => $request->product
            ];

            return view('client.productSearch', $data);

        }
    }

    public function category(){
        $data = [
            'shop' => Shop::first(),
            'category' => Category::orderBy('id', 'DESC')->paginate(12),
            'title' => 'Products'
        ];

        return view('client.category', $data);
    }

    public function categoryProducts($category){
        $data = [
            'shop' => Shop::first(),
            'category' => Category::with('products')->where('name', $category)->first(),
            'title' => 'Category - '.str_replace('-', ' ', ucwords($category))
        ];

        return view('client.categoryProducts', $data);
    }
    


    public function productDetail($product)
    {
        $product = Product::with([
                    'category.products', // Carga la categoría y sus productos
                    'productImage', 
                    'skus.variantOptions.variant',
                    'variants.options'
                  ])
                 // ->where('slug', $product)
                  ->where('title', $product)
                  ->firstOrFail();
    
        // Obtener productos recomendados
        $recommendationProducts = $product->category->products()
                                ->where('id', '!=', $product->id)
                                ->with(['productImage'])
                                ->take(8)
                                ->get();
    
        // Si no hay suficientes, completar con productos aleatorios
        if($recommendationProducts->count() < 4) {
            $additionalProducts = Product::where('category_id', '!=', $product->category_id)
                                   ->with(['productImage'])
                                   ->inRandomOrder()
                                   ->take(8 - $recommendationProducts->count())
                                   ->get();
            
            $recommendationProducts = $recommendationProducts->merge($additionalProducts);
        }
    
        $data = [
            'shop' => Shop::first(),
            'product' => $product,
            'recomendationProducts' => $recommendationProducts,
            'title' => str_replace('-', ' ', ucwords($product->title))
        ];

        return view('client.productDetail', $data);
    }


    public function checkout(){
        $data = [
            'shop' => Shop::first(),
            'title' => 'Checkout'
        ];

        return view('client.checkout', $data);
    }

    public function checkoutSave(Request $request) {
        $validator = Validator($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required'
        ]);
    
        if($validator->fails()) {
            return redirect()->route('clientCheckout')->withErrors($validator)->withInput();
        }
    
        // Verificar stock antes de procesar
        foreach((array) session('cart') as $id => $details) {
          
            $product = Product::with([
                'skus.variantOptions.variant',
              ])
             // ->where('slug', $product)
              ->where('id', $details['product_id'])
              ->firstOrFail();

            
           
            if(!$product) {
                return redirect()->route('clientCheckout')
                       ->with('error', 'El producto '.$details['title'].' ya no está disponible');
            }
            if($details['item_id']==0 && !$product->has_variants){
                if($product->base_stock < $details['quantity']) {
                    return redirect()->route('clientCheckout')
                           ->with('error', 'No hay suficiente stock de '.$details['title'].' (Disponibles: '.$product->base_stock.')');
                }
            }else{
                $sku = $product->skus->firstWhere('id', $details['item_id']); // si el SKU ID está en $details
                if (!$sku || $sku->stock < $details['quantity']) {
                    return redirect()->route('clientCheckout')
                        ->with('error', 'No hay suficiente stock de '.$details['title'].' (Disponibles: '.($sku ? $sku->stock : 0).')');
                }
            }
    
         
        }
        
        // Procesar compra con transacción
        DB::beginTransaction();
        try {
            $order_code = Str::random(3).'-'.Date('Ymd');
            $total = 0;
            $data = [];
    
            foreach((array) session('cart') as $id => $details) {

                $product = Product::with([
                    'skus.variantOptions.variant',
                  ])
                 // ->where('slug', $product)
                  ->where('id', $details['product_id'])
                  ->firstOrFail();
    
               
                if($details['item_id']==0 && !$product->has_variants){
                    $product->decrement('base_stock', $details['quantity']);
                }else{
                    $sku = $product->skus->firstWhere('id', $details['item_id']);
                    $sku->decrement('stock', $details['quantity']);
                }
             
                $total += $details['price'] * $details['quantity'];
           
                $data[] = [
                    'order_code' => $order_code,
                    'title' => $details['title'],
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                ];
            }
    
            Order::create([
                'shop_id' => Shop::first()->id,
                'order_code' => $order_code,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'note' => $request->note,
                'email' => $request->email,
                'total' => $total,
                'status' => 0
            ]);
    
            OrderDetail::insert($data);
            session()->forget('cart');
            DB::commit();
    
            return redirect()->route('clientOrderCode', $order_code);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en checkout: '.$e->getMessage());
            return redirect()->route('clientCheckout')
                   ->with('error', 'Ocurrió un error al procesar tu pedido. Por favor intenta nuevamente.');
        }
    }

    public function successOrder($order_code){
        // Obtener la orden principal
        $order = Order::where('order_code', $order_code)->first();
        // Obtener los detalles manualmente (sin relación Eloquent)
        $orderDetails = OrderDetail::where('order_code', $order_code)->get();
      
        $data = [
            'shop' => Shop::first(),
            'order_code' => $order_code,
            'order' => $order,
            'order_details' => $orderDetails,
            'title' => 'Checkout'
        ];

        return view('client.success-order', $data);
    }
    

    public function checkOrder(){
        $data = [
            'shop' => Shop::first(),
            'title' => 'Consultar Orden'
        ];

        return view('client.check-order', $data);
    }

    public function checkOrderStatus(Request $request)
    {
        $shop = Shop::first(); // Mejor obtener esto una sola vez
        $data = [
            'shop' => $shop,
            'title' => 'Consultar Orden'
        ];
    
        if ($request->order_code) {
            $order = Order::with([
                'details.sku.variantOptions.variant', // Cargar relación de variantes
                'details.product' // Cargar relación del producto base
            ])->where('order_code', $request->order_code)->first();

            if ($order) {
                // Transformar los detalles para mostrar mejor la información
                $orderDetails = $order->details->map(function($detail) {
                  
                    $item = [
                        'product_name' => $detail->title,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'total' => $detail->price * $detail->quantity
                    ];
    
                    // Si tiene SKU (producto con variantes)
                    if ($detail->sku) {
                        $item['variants'] = $detail->sku->variantOptions->map(function($option) {
                            return $option->variant->name . ': ' . $option->value;
                        })->implode(', ');
                    }
    
                    return $item;
                });
    
                $data['order'] = $order;
                $data['orderDetail'] = $orderDetails;
                $data['orderTotal'] = $orderDetails->sum('total');
               
            } else {
                $data['error'] = 'No se encontró una orden con ese código';
            }
        }
    
        return view('client.check-order', $data);
    }

    public function about(){
        $data = [
            'shop' => Shop::first(),
            'title' => 'About'
        ];

        return view('client.about', $data);
    }

    public function contact(){
        $data = [
            'shop' => Shop::first(),
            'title' => 'Contacto'
        ];

        return view('client.contact', $data);
    }


    public function contactForm(Request $request){

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country' => 'required|string|max:100',
            'subject' => 'nullable|string',
        ]);
    
        ContactForm::create($request->all());
    
        $data = [
            'shop' => Shop::first(),
            'title' => 'Contacto'
        ];

        return redirect()->route('clientHome')->with('success', 'Gracias por tu mensaje. Te responderemos pronto');
    }


}
