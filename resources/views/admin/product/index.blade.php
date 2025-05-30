@extends('admin.layout')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/simple-datatables/style.css') }}">
@endsection
@section('button')
<a href="{{ route('productCreate') }}" class="btn btn-outline-primary">Add</a>
@endsection
@section('content')
<div class="card">
  <div class="card-body">
        <table class="table table-striped" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th width="20%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                <td>{{ $product->title }}</td>
                <td>{{ $product->has_variants ? 'Con variantes' : 'Simple' }}</td>
                <td>
                    @if($product->has_variants)
                        {{ $product->skus_count }} combinaciones
                    @else
                        {{ $product->base_stock }} unidades
                    @endif
                </td>
                <td>
                    @if($product->has_variants)
                        {{ $product->skus->min('price') }} - {{ $product->skus->max('price') }}
                    @else
                        {{ $product->base_price }}
                    @endif
                </td>


                    <td>
                        <a href="{{ route('productEdit', $product->id ) }}"><span class="btn btn-sm btn-outline-primary">Detail</span></a>
                    </td>
                </tr>
                @endforeach
                <tbody>
        </table>
  </div>
</div>
@endsection
@section('js')
<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<script>
  let table1 = document.querySelector('#table1');
  let dataTable = new simpleDatatables.DataTable(table1);
</script>
@endsection