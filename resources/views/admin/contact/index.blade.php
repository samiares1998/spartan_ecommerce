@extends('admin.layout')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/simple-datatables/style.css') }}">
@endsection

@section('content')
<div class="card">
  <div class="card-body">
        <table class="table table-striped" id="table1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>first name</th>
                    <th>last name</th>
                    <th>email</th>
                    <th>subject</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$row->firstname}}</td>
                    <td>
                        {{$row->lastname}}
                    </td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->subject }}</td>
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