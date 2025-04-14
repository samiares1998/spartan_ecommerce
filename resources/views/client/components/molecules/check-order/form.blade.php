<div class="container py-3">
    <h3 class="mb-4 font-primary"><b><u>Consultar Orden</u></b></h3>
    <form action="{{ route('clientCheckOrderStatus') }}" method="post">
      @csrf
      <div class="input-group mb-3">
        <input type="text" name="order_code" class="form-control bg-transparent" placeholder="Escribe el numero de tu orden" aria-label="Recipient's username" aria-describedby="button-addon2" required>
        <button class="btn btn-outline-primary" type="submit" id="button-addon2">Consultar</button>
      </div>
    </form>
    <hr/>
</div>