@push('css')
<style>
    .order-info > tbody > tr {
        height: 35px !important;
    }
    .variant-badge {
        font-size: 0.8rem;
        background: #f3f4f6;
        color: #4b5563;
        padding: 2px 6px;
        border-radius: 4px;
        margin-right: 4px;
        margin-bottom: 4px;
        display: inline-block;
    }
</style>
@endpush

<div class="container py-3">
    @if(isset($order))
    <div class="card bg-transparent border">
        <div class="card-body">
            <div class="row g-4">
                <!-- Información de la orden -->
                <div class="col-md-3 col-12">
                    <table class="order-info">
                        <tr>
                            <td><b>Status</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>
                                @if($order->status == 0)
                                    <span class="badge bg-warning">Unprocessed</span>
                                @elseif($order->status == 1)
                                    <span class="badge bg-info">Confirmed</span>
                                @elseif($order->status == 2)
                                    <span class="badge bg-primary">Processed</span>
                                @elseif($order->status == 3)
                                    <span class="badge bg-danger">Pending</span>
                                @elseif($order->status == 4)
                                    <span class="badge bg-secondary">Shipping</span>
                                @elseif($order->status == 5)
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><b>Order Code</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><b><u>{{ $order->order_code }}</u></b></td>
                        </tr>
                        <tr>
                            <td><b>Total</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><b><u>${{ number_format($orderTotal, 2) }}</u></b></td>
                        </tr>
                        <tr>
                            <td><b>Name</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ $order->name }}</td>
                        </tr>
                        <tr>
                            <td><b>Phone</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ $order->phone }}</td>
                        </tr>
                        <tr>
                            <td><b>Address</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ $order->address }}</td>
                        </tr>
                        <tr>
                            <td><b>Note</b></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ $order->note ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Detalles de la orden -->
                <div class="col-md-9 col-12">
                    <h4>Order Details</h4>
                    
                    <!-- Versión para desktop -->
                    <div class="table-responsive d-md-block d-sm-block d-none">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Variants</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderDetail as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail['product_name'] }}</td>
                                    <td>
                                        @if(isset($detail['variants']))
                                            @foreach(explode(', ', $detail['variants']) as $variant)
                                                <span class="variant-badge">{{ $variant }}</span>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>${{ number_format($detail['price'], 2) }}</td>
                                    <td>{{ $detail['quantity'] }}</td>
                                    <td>${{ number_format($detail['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>${{ number_format($orderTotal, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Versión para móvil -->
                    <div class="d-lg-none d-sm-none d-block">
                        @foreach($orderDetail as $detail)
                            <div class="card mt-2 bg-transparent" style="width: 100%;box-shadow: rgb(0 0 0 / 10%) 0px 10px 15px -3px, rgb(0 0 0 / 5%) 0px 4px 6px -2px;">
                                <div class="card-body" style="padding: .8rem;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="font-bold font-primary">{{ $detail['product_name'] }}</h6>
                                            @if(isset($detail['variants']))
                                                <div class="mt-1">
                                                    @foreach(explode(', ', $detail['variants']) as $variant)
                                                        <span class="variant-badge">{{ $variant }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label>Price</label>
                                            <p class="font-bold">${{ number_format($detail['price'], 2) }}</p>
                                        </div>
                                        <div class="col-6">
                                            <label>Sub Total</label>
                                            <p class="font-bold">${{ number_format($detail['total'], 2) }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label>Quantity</label>
                                            <p class="font-bold">X {{ $detail['quantity'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-end mt-3">
                            <h5>Total: ${{ number_format($orderTotal, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-transparent mt-3 border">
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary font-secondary">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left"></i> Home
                        </span>
                    </a>
                </div>
                <div class="col-8">
                    <a href="/" class="btn btn-sm float-end btn-primary font-secondary">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone"></i> &nbsp;Confirm Order
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @elseif(isset($error))
    <div class="alert alert-danger text-center">
        {{ $error }}
        <div class="mt-3">
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </div>
    @endif
</div>

