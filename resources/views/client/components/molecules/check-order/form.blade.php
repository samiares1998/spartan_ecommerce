@push('css')
<style>
  .form-container {
    background: #ffffff; /* Fondo blanco */
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    color: #000000; /* Letra negra */
  }

  .form-container h2 {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #000000;
  }

  .form-container label {
    font-weight: 500;
    color: #000000;
  }

  .form-control,
  .form-select {
    border-radius: 0.5rem;
    border: 1px solid #ddd;
    color: #000;
    background-color: #fff;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #d4af37; /* Borde dorado al enfocar */
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
  }

  .btn-primary {
    background-color: #d4af37; /* Dorado */
    border: none;
    border-radius: 0.5rem;
    padding: 0.6rem 2rem;
    font-weight: 600;
    color: #000;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  .btn-primary:hover {
    background-color: #b8962f; /* Dorado más oscuro */
    color: #fff;
  }
</style>

@endpush
<div class="container py-3">
    <h3 class="mb-4 font-primary"><b><u>Consultar Orden</u></b></h3>
    <form action="{{ route('clientCheckOrderStatus') }}" method="post">
      @csrf
      <div class="input-group mb-3">
        <input type="text" name="order_code" class="form-control bg-transparent" placeholder="Escribe el numero de tu orden" aria-label="Recipient's username" aria-describedby="button-addon2" required>
        <button class="btn btn-outline-primary btn-custom" type="submit" id="button-addon2">Consultar</button>
      </div>
    </form>
    <hr/>
</div>