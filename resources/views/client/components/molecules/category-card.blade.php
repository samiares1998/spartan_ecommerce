<div class="category-card2" style="width:{{$width}};background-image: url({{ asset('shop/products/'. $path ) }})">
    <div class="py-5 ">
        <h2 class="text-center text-white">{!! str_replace('-', ' ', ucwords($name)) !!}</h2>
        <x-molecules.button text="Ver Productos" type="light" align="center" size="sm" link="{{ route('clientCategoryProducts', $name) }}"/>
    </div>
</div>