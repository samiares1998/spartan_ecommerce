@push('css')
<style>
    .product-card img {
        width: 100%;
        height: 200px; /* Altura fija */
        object-fit: contain; /* Mantiene proporciones sin recortar */
        object-position: center;
        margin: 0 auto;
        display: block;
        padding: 10px;
    }
    
    .product-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .product-title {
        font-size: 1.2rem;
        display: -webkit-box;
        overflow: hidden;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        height: 3.5rem;
        transition: 0.2s cubic-bezier(0.57, 0, 0.06, 0.95);
    }
    
    @media screen and (max-width: 767px) {
        .product-card img {
            height: 150px; /* Altura menor en móviles */
        }
        
        .product-title {
            font-size: 1rem;
        }
    }
</style>
@endpush


<div class="col-md-3 col-6 mb-4">
    <a href="{{ route('clientProductDetail', $title) }}" class="text-decoration-none">
        <div class="card product-card h-100">
            <div class="card-content">
                @if($image)
                    <img src="{{ asset('shop/products/'.$image) }}" 
                         alt="{{ $title }}" 
                         class="img-fluid product-image"
                         loading="lazy">
                @else
                    <img src="{{ asset('assets/images/no-image.jpg') }}" 
                         alt="No image" 
                         class="img-fluid product-image"
                         loading="lazy">
                @endif
                <div class="card-body">
                    <p class="fw-bolder product-title mb-1">{{ $title }}</p>
                    <p class="mt-auto">{{ $price }}</p>
                </div>
            </div>
        </div>
    </a>
</div>

