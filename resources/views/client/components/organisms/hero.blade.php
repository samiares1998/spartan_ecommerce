@push('css')
    <link rel="stylesheet" href="{{ asset('client/components/molecules/hero/hero-product.css') }}">
@endpush

<div class="container">
    <x-molecules.hero.text-block />
     <!--  
    <div class="row g-md-4 g-3">
        @foreach ($dataProduct->take(2) as $item)
        <div class="col-md-6 d-flex justify-content-center col-12">
     <x-molecules.hero.card-product :title="$item->title" :dataImage="$item->productImage"/>
        </div>
        @endforeach
    </div>
    -->
</div>