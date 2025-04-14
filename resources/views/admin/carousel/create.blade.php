@extends('admin.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/upload.css')}}" />
@endsection

@section('content')
  <div class="card">
    <div class="card-body row">
      <div class="col-md-6 col-12">
        <form action="{{ route('carouselSave') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div id="file-upload-form" class="uploader @error('image') is-invalid @enderror">
            <input id="file-upload" type="file" name="image" accept="image/*" />
            <label for="file-upload" id="file-drag">
              <img id="file-image" src="#" alt="Preview" class="hidden">
              <div id="start">
                <i class="fa fa-download" aria-hidden="true"></i>
                <div>Upload carousel image</div>
                @error('image')
                  <span class="text-danger">{{ $message }}</span><br>
                @enderror
                <div id="notimage" class="hidden">Please select an image</div>
                <span id="file-upload-btn" class="btn btn-primary">Select a file</span>
              </div><br>
              @if(session('errorUpload'))
                <span class="text-danger">You must use the button</span><br>
              @endif
              <div id="response" class="hidden">
                <span class="text-danger" id="max-file"></span>
                <div id="messages"></div>
                <progress class="progress" id="file-progress" value="0"><span>0</span>%</progress>
              </div>
            </label>
          </div>

          <div class="form-group mt-3">
            <label for="title">Carousel title</label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Example carousel" value="{{ old('title') }}" required autofocus>
            @error('title') 
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-group mt-3">
            <label for="video">Video URL (optional)</label>
            <input type="text" name="video" id="video" class="form-control @error('video') is-invalid @enderror" placeholder="Enter video URL" value="{{ old('video') }}">
            @error('video') 
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-group mt-3">
            <label for="description">Description (optional)</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter carousel description">{{ old('description') }}</textarea>
            @error('description') 
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <button type="submit" class="btn btn-primary float-end mt-3">Create</button>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('js')
<script src="{{ asset('assets/js/upload.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
<script>
  const title = document.getElementById("title");
  title.addEventListener('keyup', function(e){
      let result = title.value.replace(/\s+/g, "-");
      title.value = result.toLowerCase()
  });

  $('#title').keyup(function(){
    let title = this.value;

    setTimeout(() => {
      $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
        type: 'POST',
        dataType: 'json',
        data: {"_token" : "{{ csrf_token() }}", title: title},
        url: '{{ route("carouselCheck") }}',
        success: function(data){ },
        statusCode: {
          200: () => {
            $('#title').addClass('is-invalid');
            $('#title').removeClass('is-valid');
          },
          201: () => {
            $('#title').removeClass('is-invalid');
            $('#title').addClass('is-valid');
          }
        }
      })
    }, 100);
  });
</script>
@endsection
