
<div class="col-md-3 col-6">
<a href="{{ route('clientProductDetail', $title) }}">
        <div class="card mb-0">
            <div class="card-content">
                @if($image)
                    <img src="{{ asset('shop/products/'.$image) }}" alt="{{ $title }}" class=" img-fluid" style="    width: 80%;">
                @else
                    <img src="{{ asset('assets/images/no-image.jpg') }}" alt="No image" class=" img-fluid" style="    width: 80%;">
                @endif
                <div class="card-body p-md-3 p-2">
                    
                    <p class="fw-bolder product-title mb-1">{{ $title }}</p>
                    <p>{{ $price }}</p>
           
                </div>
            </div>
        </div>
    </a>
</div>

