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
  <script>
    const order = @json($order);
    const orderDetails = @json($order_details); // Usamos los detalles manuales
    const emojiSpartan = String.fromCodePoint(0x1F3CB, 0xFE0F, 0x200D, 0x2642); 
    let message = `¡Hola *${order.name}*! *SPARTAN* ${emojiSpartan}\n\n` +
              `Gracias por tu compra. Detalles de tu pedido #${order.order_code}:\n\n` +
              `Productos:\n${orderDetails.map(item => 
                `- ${item.title} (x${item.quantity}): $${item.price * item.quantity}`
              ).join('\n')}\n\n` +
              `Total: $${order.total}\n\n` +
              `¿Qué sigue?:\n` +
              `1. Contacto para pago.\n` +
              `2. Preparación de tu pedido.\n` +
              `3. ¡Entrega lista!\n\n` +
              `¿Dudas? ¡Respóndeme aquí!`;

window.open(`https://wa.me/573213333915?text=${encodeURIComponent(message)}`, '_blank');
</script>
</x-template.layout>
