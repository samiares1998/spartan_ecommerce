@push('css')
    <style>
         .product-title{
            font-size:1.2rem;
            display: -webkit-box;
            overflow: hidden;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            height:3.5rem;
            transition: 0.2s cubic-bezier(0.57, 0, 0.06, 0.95);
        }

        .card:hover{
            transition: 0.2s cubic-bezier(0.57, 0, 0.06, 0.95);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        @media screen and (max-width: 767px){
            .product-title{
                font-size:1rem;
            }
        }
    </style>
@endpush

<div class="container py-5">
    {{ $slot }}
    <div class="row g-3">
        @foreach ($dataProduct as $product)
            <x-molecules.product-card 
                :product="$product"
                :image="$product->productImage->first()?->path"
                :category="$product->category->name" 
                :title="$product->title"
                :price="$product->has_variants ? 
                    'From $'.number_format($product->skus->min('price'), 2) : 
                    '$'.number_format($product->base_price, 2)"
            />
        @endforeach
    </div>
    {{ $productCTA ?? '' }}
</div>