<x-template.layout title="{{ $title }}">
  <x-organisms.navbar :path="$shop->path" />
    <div class="container py-y d-flex flex-column align-items-center gap-3">
      <img src="{{ asset('client/img/success-order.png') }}" class="border rounded rounded-3" style="width:40%;height:auto;">
      <div class="text-center">
        <h4>¡Muchas gracias por tu pedido!</h4>
        <p>Código de Orden: <u><b class="text-danger">{{ $order_code }}</b></u></p>
        <p>Puedes hacer seguimiento de tu pedido en <a href="{{ route('clientCheckOrder') }}"><u>Consultar Orden</u></a>. Por favor guarda este código y no lo olvides para verificar el estado de tu pedido.</p>
      </div>
      <a href="{{ route('clientCheckOrder') }}" class="btn btn-primary">Consultar Orden</a>
    </div>
  <x-organisms.footer :shop="$shop"/>
</x-template.layout>
