
@push('css')
  <style>
    .autosize:focus{
      box-shadow:none !important;
    }

    @media screen and (max-width: 767px){
      #input_div{
        display:flex;
        justify-content:space-between;
        gap:10px;
      }

      .add-to-cart, #count{
        width:100% !important;  
      }
    }

    #count{
      width:100px;
    }
  </style>
@endpush

<div>
    <h1 class="mt-md-0 mt-4">{{ $dataProductContent->title }}</h1>
    <hr/>
    
  
    @if($dataProductContent->has_variants)
        <h5>${{ number_format($dataProductContent->skus->min('price'), 2) }} - ${{ number_format($dataProductContent->skus->max('price'), 2) }}</h5>
    @else
        <h5>${{ number_format($dataProductContent->base_price, 2) }}</h5>
    @endif
    
    <p>Categoría: 
    
    </p>
    
    <!-- Selectores de variantes (si aplica) -->
    @if($dataProductContent->has_variants)
        <div class="variants mb-4">
            @foreach($dataProductContent->variants as $variant)
                <div class="mb-3">
                    <label class="form-label">{{ $variant->name }}</label>
                    <select class="form-select variant-select" data-variant="{{ $variant->slug }}">
                        @foreach($variant->options as $option)
                            <option value="{{ $option->id }}" 
                                data-price="{{ $option->sku->price ?? 0 }}"
                                data-stock="{{ $option->sku->stock ?? 0 }}">
                                {{ $option->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
            
            <!-- Mostrar stock dinámico -->
            <p class="stock-display">
                <b>Existencias: <span id="variant-stock">{{ $dataProductContent->skus->sum('stock') }}</span></b>
            </p>
        </div>
    @else
        <p><b>Existencias: {{ $dataProductContent->base_stock }}</b></p>
    @endif
    
    <!-- Descripción -->
    <p><b>Descripción</b></p>
    <div class="product-description">
        {!! nl2br(e($dataProductContent->desc)) !!}
    </div>
    
    <!-- Selector de cantidad y botón -->
    @if(($dataProductContent->has_variants && $dataProductContent->skus->sum('stock') > 0) || (!$dataProductContent->has_variants && $dataProductContent->base_stock > 0))
        <div id="input_div" class="mt-4">
            <input type="button" value="-" id="moins" onclick="minus()" class="btn btn-outline-primary">
            <input type="text" value="1" id="count" class="btn btn-outline-primary font-secondary" disabled>
            <input type="button" value="+" id="plus" 
                   data-stock="{{ $dataProductContent->has_variants ? $dataProductContent->skus->sum('stock') : $dataProductContent->base_stock }}" 
                   onclick="plus()" class="btn btn-outline-primary">
        </div>
        <button class="btn btn-primary btn-lg mt-3 add-to-cart" 
        data-id-product="{{ $dataProductContent->id }}"
        data-quantity="1"
        @if($dataProductContent->has_variants) 
            data-is-variant="true" 
            data-sku-id=""
        @endif>
    Añadir al Carrito
</button>
    @else
        <div class="alert alert-warning mt-4">Producto agotado</div>
    @endif
</div>
@push('js')
<script>
    // Datos del producto disponibles en JS
    const productData = @json($dataProductContent);
    const skusData = @json($dataProductContent->skus->keyBy('id'));
    let selectedSkuId = null;

    // Manejo de selección de variantes
    document.addEventListener('DOMContentLoaded', function() {
        const variantSelects = document.querySelectorAll('.variant-select');
        
        if(variantSelects.length > 0) {
            // Inicializar selección de variantes
            updateVariantSelection();
            
            // Escuchar cambios en los selectores
            variantSelects.forEach(select => {
                select.addEventListener('change', updateVariantSelection);
            });
        }
    });

    // Función para actualizar la selección de variantes
    function updateVariantSelection() {
        const selectedOptions = Array.from(document.querySelectorAll('.variant-select'))
            .map(select => select.value);
        
        // Buscar el SKU que coincida con todas las opciones seleccionadas
        const matchingSku = Object.values(skusData).find(sku => {
            return sku.variant_options.every(option => 
                selectedOptions.includes(option.id.toString())
            );
        });

        if (matchingSku) {
            selectedSkuId = matchingSku.id;
            // Actualizar UI con los datos del SKU seleccionado
            document.getElementById('variant-stock').textContent = matchingSku.stock;
            document.querySelector('.add-to-cart').dataset.skuId = matchingSku.id;
            document.querySelector('#plus').dataset.stock = matchingSku.stock;
            
            // Actualizar el precio mostrado
            const priceDisplay = document.querySelector('h5');
            priceDisplay.textContent = `$${matchingSku.price.toFixed(2)}`;
        }
    }

    // Contador de cantidad
    let count = 1;
    const countEl = document.getElementById("count");

    function plus() {
        const maxStock = parseInt(document.querySelector('#plus').dataset.stock);
        if(count < maxStock) {
            count++;
            countEl.value = count;
            document.querySelector('.add-to-cart').dataset.quantity = count;
        }
    }

    function minus() {
        if(count > 1) {
            count--;
            countEl.value = count;
            document.querySelector('.add-to-cart').dataset.quantity = count;
        }
    }

    // Manejo del carrito
    document.querySelector('.add-to-cart')?.addEventListener('click', function() {
        const productId = this.dataset.idProduct;
        const quantity = this.dataset.quantity;
        const skuId = productData.has_variants ? this.dataset.skuId : null;

        if(productData.has_variants && !skuId) {
            showToast('error', 'Por favor selecciona todas las opciones');
            return;
        }

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
            type: "POST",
            dataType: "json",
            data: {
                "_token": "{{ csrf_token() }}",
                product_id: productId,
                quantity: quantity,
                sku_id: skuId
            },
            url: '{{ route("clientAddToCart") }}',
            success: function(data) {
                $('#cartCount').text(data.cartCount);
                countEl.value = 1;
                count = 1;
                showToast('success', data.message || 'Producto añadido al carrito');
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Error al añadir al carrito');
            }
        });
    });

    function showToast(type, message) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: type === 'success' ? "#4fbe87" : "#f3616d",
        }).showToast();
    }

    // Función para autoajustar textarea
    autosize();
    function autosize(){
        var text = $('.autosize');
        text.each(function(){
            $(this).attr('rows',1);
            resize($(this));
            this.style.overflow = 'hidden';
            this.style.backgroundColor = 'transparent';
            this.style.padding = '0';
            this.style.border = 'none';
            this.style.resize = 'none';
        });
        text.on('input', resize);
        
        function resize($text) {
            $text.css('height', 'auto').css('height', $text[0].scrollHeight+'px');
        }
    }
</script>
@endpush