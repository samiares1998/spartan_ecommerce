@extends('admin.layout')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/select-live/dselect.scss') }}">
<style>
  .form-select{
    text-align:left !important;
  }
  .dropdown-menu{
    border: 1px solid #dce7f1;
  }
  .variant-option {
    margin-bottom: 15px;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 5px;
  }
</style>
@endsection

@section('content')
<div class="card">
  <div class="card-body row">
    <div class="col-md-8 col-12">
      <form action="{{ route('producSave') }}" method="post" enctype="multipart/form-data" id="productForm">
        @csrf
        
        <!-- Sección básica del producto -->
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                 placeholder="Chicken nugget spicy" value="{{ old('title') }}" required>
          @error('title')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
          <label for="category">Category</label>
          <select name="category_id" id="category" class="form-select @error('category_id') is-invalid @enderror" required>
            <option selected disabled>Select Category</option>
            @foreach ($categories as $item)
              <option value="{{ $item->id }}" {{ old('category_id') == $item->id ? 'selected' : '' }}>
                {{ $item->name }}
              </option>
            @endforeach
          </select>
          @error('category_id')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- Selector de tipo de producto -->
        <div class="form-group">
          <label>Product Type</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="product_type" id="simpleProduct" 
                   value="simple" checked onclick="toggleVariants(false)">
            <label class="form-check-label" for="simpleProduct">
              Simple Product
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="product_type" id="variantProduct" 
                   value="variant" onclick="toggleVariants(true)">
            <label class="form-check-label" for="variantProduct">
              Product with Variants
            </label>
          </div>
        </div>

        <!-- Sección para producto simple -->
        <div id="simpleProductFields">
          <div class="form-group">
            <label for="base_price">Price</label>
            <input type="number" name="base_price" id="base_price" class="form-control" 
                   placeholder="1000" value="{{ old('base_price') }}" required>
          </div>
          <div class="form-group">
            <label for="base_stock">Stock</label>
            <input type="number" name="base_stock" id="base_stock" class="form-control" 
                   placeholder="10" value="{{ old('base_stock') }}" required>
          </div>
        </div>

        <!-- Sección para variantes (inicialmente oculta) -->
        <div id="variantFields" style="display: none;">
          <h5>Variants</h5>
          
          <!-- Contenedor dinámico para variantes -->
          <div id="variantsContainer">
            <!-- Las variantes se agregarán aquí dinámicamente -->
          </div>
          
          <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addVariant()">
            <i class="bi bi-plus"></i> Add Variant
          </button>
        </div>

        <div class="form-group">
          <label for="desc">Description</label>
          <textarea name="desc" id="desc" class="form-control" 
                    placeholder="Homemade spicy chicken nuggets with healthy chicken..." required>{{ old('desc') }}</textarea>
          @error('desc')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
        </div>

        <button type="submit" class="btn btn-primary float-end">Save</button>
      </form>
    </div>
  </div>
</div>

<!-- Template para variantes (hidden) -->
<div id="variantTemplate" style="display: none;">
  <div class="variant-option mb-3">
    <div class="row">
      <div class="col-md-4">
        <label>Variant Type</label>
        <select class="form-select variant-type" name="variants[INDEX][type]">
          <option value="color">Color</option>
          <option value="size">Talla</option>
          <option value="weight">Peso</option>
          <option value="flavor">Sabor</option>
        </select>
      </div>
      <div class="col-md-6">
        <label>Value</label>
        <input type="text" class="form-control" name="variants[INDEX][value]" placeholder="Red, XL, 500g, etc.">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeVariant(this)">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-md-4">
        <label>Price</label>
        <input type="number" class="form-control" name="variants[INDEX][price]" placeholder="Price">
      </div>
      <div class="col-md-4">
        <label>Stock</label>
        <input type="number" class="form-control" name="variants[INDEX][stock]" placeholder="Stock">
      </div>
      <div class="col-md-4">
        <label>SKU</label>
        <input type="text" class="form-control" name="variants[INDEX][sku]" placeholder="SKU">
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/vendors/select-live/dselect.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
<script>
  // Toggle entre producto simple y con variantes
  function toggleVariants(show) {
    if(show) {
      $('#simpleProductFields').hide().find('input').prop('required', false);
      $('#variantFields').show();
    } else {
      $('#simpleProductFields').show().find('input').prop('required', true);
      $('#variantFields').hide();
    }
  }

  // Contador para variantes
  let variantIndex = 0;

  // Agregar nueva variante
  function addVariant() {
    const template = $('#variantTemplate').html();
    const newVariant = template.replace(/INDEX/g, variantIndex);
    $('#variantsContainer').append(newVariant);
    variantIndex++;
    
    // Inicializar selects
    const select_box_elements = document.querySelectorAll('.variant-type');
    select_box_elements.forEach(element => {
      dselect(element, { search: true });
    });
  }

  // Eliminar variante
  function removeVariant(button) {
    $(button).closest('.variant-option').remove();
  }

  // Auto-slug desde el título
  document.getElementById('title').addEventListener('keyup', function(e) {
    let result = this.value.replace(/\s+/g, "-").toLowerCase();
    // Si tienes un campo slug, puedes asignarlo:
    // document.getElementById('slug').value = result;
  });

  // Auto-ajustar textarea
  document.getElementById('desc').addEventListener('keyup', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  }, false);

  // Inicializar select de categoría
  var select_box_element = document.querySelector('#category');
  dselect(select_box_element, { search: true });

  // Verificación de título único (opcional)
  $('#title').keyup(function() {
    let title = this.value;
    $.ajax({
      headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
      type: 'POST',
      dataType: 'json',
      data: {"_token": "{{ csrf_token() }}", title: title},
      url: '{{ route("productCheck") }}',
      success: function(data) {
        if(data.exists) {
          $('#title').addClass('is-invalid');
          $('#title').removeClass('is-valid');
        } else {
          $('#title').removeClass('is-invalid');
          $('#title').addClass('is-valid');
        }
      }
    });
  });
</script>
@endsection