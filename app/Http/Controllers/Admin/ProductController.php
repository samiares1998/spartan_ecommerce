<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductSku;
use App\Models\SkuVariantOption;
use App\Models\VariantOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Str;
use File;

class ProductController extends Controller
{
    public function index()
    {
        $data = [
            'products' => Product::with(['skus.variantOptions.variant'])
                        ->orderByDesc('id')
                        ->get(),
            'title' => 'Products'
        ];
    
        return view('admin.product.index', $data);
    }
    public function create()
    {
        $data = [
            'title' => 'Add Product',
            'variants' => Auth::user()->shop->variants ?? [], // Si tienes variantes predefinidas
            'categories' => Auth::user()->shop->category
        ];
    
        return view('admin.product.create', $data);
    }

    public function check(Request $request){
        $name = Product::where('title', $request->title)->exists();
        if($name){
            return response()->json(['status' => 'success', 'messages' => 'not available', 'code' => 200], 200);
        }else{
            return response()->json(['status' => 'success', 'messages' => 'available', 'code' => 201], 201);
        }
    }
    public function save(Request $request)
    {

        // Validación básica común
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|unique:products',
            'desc' => 'required',
            'product_type' => 'required|in:simple,variant', // Asegurar que es uno de estos valores
        ]);
    
        // Validación condicional según el tipo de producto
        if ($request->product_type == 'simple') {
            $validator->addRules([
                'base_price' => 'required|integer|min:0',
                'base_stock' => 'required|integer|min:0'
            ]);
        } else {
            $validator->addRules([
                'variants' => 'required|array|min:1',
                'variants.*.price' => 'required|integer|min:0',
                'variants.*.stock' => 'required|integer|min:0',
                'variants.*.value' => 'required|string'
            ]);
        }
    
        if ($validator->fails()) {
            return redirect()->route('productCreate')
                            ->withErrors($validator)
                            ->withInput();
        }
    
        // Crear el producto base
        $product = Product::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'desc' => $request->desc,
            'base_price' => $request->product_type == 'simple' ? $request->base_price : 0,
            'base_stock' => $request->product_type == 'simple' ? $request->base_stock : 0,
            'has_variants' => $request->product_type == 'variant',
            'user_id' => Auth::id() // Asignar el usuario creador
        ]);
    
        // Manejar variantes si es necesario
        if ($request->product_type == 'variant') {
            foreach ($request->variants as $variantData) {
                // 1. Crear o obtener la variante (ej: "Color")
                $variant = ProductVariant::firstOrCreate([
                    'product_id' => $product->id,
                    'name' => $variantData['type'] ?? 'Generic', // Default si no se especifica
                    'slug' => Str::slug($variantData['type'] ?? 'generic')
                ]);
    
                // 2. Crear la opción específica (ej: "Rojo")
                $option = VariantOption::create([
                    'variant_id' => $variant->id,
                    'value' => $variantData['value']
                ]);
    
                // 3. Crear el SKU
                $sku = ProductSku::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'] ?? $this->generateSku($product, $variantData),
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock']
                ]);
    
                // 4. Relacionar SKU con la opción
                $sku->variantOptions()->attach($option->id);
            }
        }
    
        // Redirigir a añadir imágenes
        return redirect()->route('productAddImages', ['product' => $product->id]);
    }

    protected function generateSku($product, $variantData)
    {
        return Str::upper(substr($product->title, 0, 3)) . '-' . 
            Str::upper(substr($variantData['type'] ?? 'GEN', 0, 3)) . '-' . 
            Str::upper(substr($variantData['value'], 0, 3));
    }
   
    public function addImages($product){

       

        $productData = Product::where('id', $product)->first();

        $data = [
            'title' => 'Add Images product id - '. str_replace('-', ' ', $product),
            'product' => $productData
        ];

        return view('admin.product.addImages', $data);
    }


    public function getImages(Request $request){
        $data = ProductImage::where('product_id', $request->id_products)->orderByDesc('id')->get();
        return response()->json($data);
    }

    public function addImagesSave(Request $request){
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('shop/products'), $imageName);

        ProductImage::create([
            'product_id' => $request->id_product,
            'path' => $imageName
        ]);

        return response()->json(['status' => 'success', 'code' => 200], 200);
    }

    public function deleteImages(Request $request){
        $filename = $request->get('filename');
        ProductImage::where('path', $filename)->delete();
        $paths = public_path().'/shop/products/'. $filename;
        if(file_exists($paths)){
            unlink($paths);
        }
        return response()->json(['status' => 'success', 'code' => 200], 200);
    }


    
    public function edit($product){
        $productData = Product::with(['skus.variantOptions.variant', 'variants'])
        ->where('id', $product)
        ->firstOrFail();

        $data = [
            'product' => $productData,
            'title' => 'Edit product '. str_replace('-', ' ', $product)
        ];

        
        return view('admin.product.edit', $data);
    }

    public function editSave(Request $request, $product, $id)
    {
        // Validación básica común
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'product_type' => 'required|in:simple,variant',
        ]);
    
        // Regla condicional para el título
        if (Str::slug($product) != Str::slug($request->title)) {
            $validator->addRules(['title' => 'unique:products,title']);
        }
    
        // Validación condicional según el tipo de producto
        if ($request->product_type == 'simple') {
            $validator->addRules([
                'base_price' => 'required|numeric|min:0',
                'base_stock' => 'required|integer|min:0'
            ]);
        } else {
            $validator->addRules([
                'variants' => 'required|array|min:1',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.stock' => 'required|integer|min:0',
                'variants.*.value' => 'required|string'
            ]);
        }
    
        if ($validator->fails()) {
            return redirect()->route('productEdit', ['product' => $product, 'id' => $id])
                            ->withErrors($validator)
                            ->withInput();
        }
    
        DB::beginTransaction();
        try {
            // Actualizar el producto base
            $productData = [
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'desc' => $request->desc,
                'has_variants' => $request->product_type == 'variant',
                'base_price' => $request->product_type == 'simple' ? $request->base_price : 0,
                'base_stock' => $request->product_type == 'simple' ? $request->base_stock : 0,
            ];
 
            Product::where('id', $id)->update($productData);
    
            // Si es producto con variantes
            if ($request->product_type == 'variant') {
                $this->handleProductVariants($id, $request->variants);
            } else {
                // Eliminar variantes existentes si se cambia a producto simple
                ProductSku::where('product_id', $id)->delete();
            }
       
            DB::commit();
         
            return redirect()->route('productEdit', ['product' => Str::slug($request->id), 'id' => $id])
                        ->with('success', 'Product updated successfully');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }
    
    protected function handleProductVariants($productId, $variantsData)
    {
        $existingSkus = [];
        
        foreach ($variantsData as $variantData) {
            // Si es una variante existente (tiene ID)
            if (!empty($variantData['id'])) {
                // Actualizar SKU existente
                $sku = ProductSku::find($variantData['id']);
                if ($sku) {
                    $sku->update([
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'sku' => $variantData['sku'] ?? $sku->sku
                    ]);
                    $existingSkus[] = $sku->id;
                    continue;
                }
            }
    
            // Crear o obtener la variante (ej: "Color")
            $variant = ProductVariant::firstOrCreate([
                'product_id' => $productId,
                'name' => ucfirst($variantData['type'] ?? 'Generic'),
                'slug' => Str::slug($variantData['type'] ?? 'generic')
            ]);
    
            // Crear opción de variante (ej: "Rojo")
            $option = VariantOption::firstOrCreate([
                'variant_id' => $variant->id,
                'value' => $variantData['value']
            ]);
    
            // Crear nuevo SKU
            $sku = ProductSku::create([
                'product_id' => $productId,
                'sku' => $variantData['sku'] ?? $this->generateSku($productId, $variantData),
                'price' => $variantData['price'],
                'stock' => $variantData['stock']
            ]);
    
            // Relacionar SKU con la opción
            $sku->variantOptions()->sync([$option->id]);
            $existingSkus[] = $sku->id;
        }
    
        // Eliminar SKUs que ya no existen
        ProductSku::where('product_id', $productId)
                  ->whereNotIn('id', $existingSkus)
                  ->delete();
    }
    

    public function delete($id){
        $product = Product::where('id', $id)->first();

        $dataImage = [];

        foreach($product->productImage as $i => $item){
            array_push($dataImage, public_path().'/shop/products/'.$item->path);
        }

        File::delete($dataImage);

        Product::destroy($id);
        return redirect()->route('products')->with('success', 'Data product deleted');
    }
}