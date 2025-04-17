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
use Validator;
use Str;


class ClientController extends Controller
{
    public function index(){

        if(!Shop::exists()){
            return redirect()->route('register');
        }

        $data = [
            'shop' => Shop::first(),
            'dataCarousel' => Carousel::all(),
            'product' => Product::all()->sortByDesc('id')->take(8),
            'category' => Category::all()->sortByDesc('id')->take(4),
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
            'category' => Category::where('name', $category)->first(),
            'title' => 'Category - '.str_replace('-', ' ', ucwords($category))
        ];

        return view('client.categoryProducts', $data);
    }

    public function productDetail($product){

        $product = Product::where('title', $product)->first();

        if($product->category->product->count() > 1){
            $recomendationProducts = $product->category->product->take(8);
        }else{
            $recomendationProducts = Product::all()->sortByDesc('id')->take(8);
        }

        $data = [
            'shop' => Shop::first(),
            'product' => $product,
            'recomendationProducts' => $recomendationProducts,
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
            $product = Product::find($id);
            
            if(!$product) {
                return redirect()->route('clientCheckout')
                       ->with('error', 'El producto '.$details['title'].' ya no está disponible');
            }
    
            if($product->stock < $details['quantity']) {
                return redirect()->route('clientCheckout')
                       ->with('error', 'No hay suficiente stock de '.$details['title'].' (Disponibles: '.$product->stock.')');
            }
        }
    
        // Procesar compra con transacción
        DB::beginTransaction();
        try {
            $order_code = Str::random(3).'-'.Date('Ymd');
            $total = 0;
            $data = [];
    
            foreach((array) session('cart') as $id => $details) {
                $product = Product::find($id);
                $total += $details['price'] * $details['quantity'];
    
                // Actualizar stock
                $product->decrement('stock', $details['quantity']);
    
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

    public function checkOrderStatus(Request $request){



        $order = Order::where('order_code', $request->order_code)->first();


        if($order){
            $data = [
                'shop' => Shop::first(),
                'order' => $order,
                'orderDetail' => OrderDetail::where('order_code', $request->order_code)->get(),
                'title' => 'Consultar Orden'
            ];
            return view('client.check-order', $data);

        }

        $data = [
            'shop' => Shop::first(),
            'title' => 'Consultar Orden'
        ];

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
