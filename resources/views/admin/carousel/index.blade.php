@extends('admin.layout')

@section('button')
  <a href="{{ route('carouselCreate') }}" class="btn btn-outline-primary">Create</a>
@endsection

@section('content')
  <div class="card">
    <div class="card-body">
      <div class="row">
        @foreach ($carousels as $item)
          <div class="col-md-4 col-6">
            <div class="card shadow">
              <a href="javascript:void(0)" onclick="alertconfirm('{{route('carouselDelete', ['id' => $item->id, 'path' => $item->image] )}}')"  class="btn btn-sm btn-danger" style="position:absolute;z-index:9;right:0;"><i class="bi bi-trash"></i></a>
              <div class="card-content">
                <img src='{{ asset("carousel/slides/$item->image") }}' alt="" class="card-img-top img-fluid" style="height:200px;">
                <div class="card-body">
                  <h5 class="card-title">{!! str_replace('-', ' ', ucwords($item->name)) !!}</h5>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endsection

@section('js')
<script>
const alertconfirm = (url) => {
    Swal.fire({
        title: 'Sure to delete this file?',
        text: "This file will be deleted permanently",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
        if (result.isConfirmed) {
            window.location.replace(url);
        }
    })
  }
</script>
@endsection
