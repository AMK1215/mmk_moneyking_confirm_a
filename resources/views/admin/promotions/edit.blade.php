@extends('admin_layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.promotions.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Edit Promotion</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.promotions.update', $promotion->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label for="inputEmail3" class="form-label text-dark">Promotion Image</label>
                  <input type="file" class="form-control border border-1 border-secondary ps-2" id="inputEmail3" name="image">
                  <img src="{{$promotion->img_url}}" alt="" width="100px">
                  @error('image')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="mb-3">
                  <label for="inputEmail3" class="form-label text-dark">Description</label>
                  <textarea name="description" id="" style="border: 1px solid gray;" class="form-control summernote">{{$promotion->description}}</textarea>
                  @error('description')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                  
                  @if(Auth::user()->hasRole('Master'))
                  <div class="mb-3">
                    <div class="d-flex">
                      <div class="me-2 single" id="single">
                        <label for="single" class="form-label">
                          <input type="radio"
                            name="type"
                            value="single"
                            class=" me-2"
                            id="single" {{$promotion->promotionAgents->count() == 1 ? 'checked' : '' }}>
                          Single
                        </label>
                      </div>
                      <div class="me-2">
                        <label for="all" class="form-label">
                          <input type="radio"
                            name="type"
                            value="all"
                            class=" me-2"
                            id="all" {{$promotion->promotionAgents->count() > 1 ? 'checked' : '' }}>
                          All
                        </label>
                      </div>
                    </div>
                    @error('type')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="custom-form-group {{$promotion->promotionAgents->count() > 1 ? 'is-hide' : '' }} " id="singleAgent">
                    <label for="title">Select Agent</label>
                    <select name="agent_id" class="form-control form-select" id="">
                      @foreach (Auth::user()->agents as $agent)
                      <option
                        value="{{ $agent->id }}"
                        {{ $promotion->promotionAgents->contains('agent_id', $agent->id) ? 'selected' : '' }}>
                        {{ $agent->name }}
                      </option> @endforeach
                    </select>
                    @error('agent_id')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  @endif
                  <div class="custom-form-group">
                    <button class="btn btn-primary" type="submit">Edit</button>
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
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>

<script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
  $(document).ready(function() {
    $('.summernote').summernote();
    $(".is-hide").hide();
    $("#single").on("change", function() {
      console.log('here');
      $("#singleAgent").show();
    });
    $("#all").on("change", function() {
      $("#singleAgent").hide();
    });
  });
</script>
@endsection