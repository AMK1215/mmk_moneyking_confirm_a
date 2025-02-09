@extends('admin_layouts.app')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.bannerAds.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Ads Banner Update</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.bannerAds.update', $bannerAds->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="custom-form-group">
                  <label for="title">Mobile Image</label>
                  <input type="file" class="form-control" id="inputEmail3" name="mobile_image">
                  <img src="{{ $bannerAds->mobile_image_url }}" width="150px" class="img-thumbnail" alt="">
                </div>
                <div class="custom-form-group">
                  <label for="title">Desktop Image</label>
                  <input type="file" class="form-control" id="inputEmail3" name="desktop_image">
                  <img src="{{ $bannerAds->desktop_image_url }}" width="150px" class="img-thumbnail" alt="">
                </div>
                <div class="custom-form-group">
                  <label for="title">Description</label>
                  <textarea type="file" class="form-control" id="" name="description" style="border: 1px solid gray;"> {{$bannerAds->description}}</textarea>
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
                            id="single" {{$bannerAds->bannerAdsAgents->count() == 1 ? 'checked' : '' }}>
                          Single
                        </label>
                      </div>
                      <div class="me-2">
                        <label for="all" class="form-label">
                          <input type="radio"
                            name="type"
                            value="all"
                            class=" me-2"
                            id="all" {{$bannerAds->bannerAdsAgents->count() > 1 ? 'checked' : '' }}>
                          All
                        </label>
                      </div>
                    </div>
                    @error('type')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="custom-form-group {{$bannerAds->bannerAdsAgents->count() > 1 ? 'is-hide' : '' }} " id="singleAgent">
                    <label for="title">Select Agent</label>
                    <select name="agent_id" class="form-control form-select" id="">
                      @foreach (Auth::user()->agents as $agent)
                      <option
                        value="{{ $agent->id }}"
                        {{ $bannerAds->bannerAdsAgents->contains('agent_id', $agent->id) ? 'selected' : '' }}>
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
