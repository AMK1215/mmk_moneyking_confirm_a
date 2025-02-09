@extends('admin_layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.banners.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Promotion Create</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.promotions.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label for="inputEmail3" class="form-label text-dark">Promotion Image</label>
                  <input type="file" class="form-control border border-1 border-secondary ps-2" id="inputEmail3" name="image">
                  @error('image')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="mb-3">
                  <label for="inputEmail3" class="form-label text-dark">Description</label>
                  <textarea name="description" id="" style="border: 1px solid gray;" class="form-control"></textarea>
                  @error('description')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                @if(Auth::user()->hasRole('Master'))
                <div class="mb-3">
                  <div class="d-flex">
                    <div class="me-2">
                      <label for="single" class="form-label">
                        <input type="radio"
                        name="type"
                        value="single"
                        class=" me-2"
                        id="single"
                        >
                        Single
                      </label>
                    </div>
                    <div class="me-2">
                      <label for="all" class="form-label">
                        <input type="radio"
                        name="type"
                        value="all"
                        class=" me-2"
                        id="all"
                        >
                        All
                      </label>
                    </div>
                  </div>
                  @error('type')
                    <span class="text-danger">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="custom-form-group" id="singleAgent">
                  <label for="title">Select Agent</label>
                  <select name="agent_id" class="form-control form-select" id="">
                    <option value="">Select Agent</option>
                    @foreach (Auth::user()->agents as $agent)
                      <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                  </select>
                  @error('agent_id')
                    <span class="text-danger">*{{ $message }}</span>
                  @enderror
                  {{-- <input type="file" class="form-control" id="inputEmail3" name="image"> --}}
                </div>
                @endif
                <div class="mb-3">
                  <button class="btn btn-primary" type="submit">Create</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
    $('#desc').summernote({
      placeholder: 'Write Down Full Text',
      tabsize: 2,
      height: 120,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        // ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ]
    });

</script>
@endsection
