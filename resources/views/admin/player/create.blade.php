@extends('admin_layouts.app')
@section('content')
    <div class="container text-center mt-4">
        <div class="row">
            <div class="col-12 col-md-8 mx-auto">
                <div class="card">
                    <!-- Card header -->

                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">Create New Player
                                </h5>

                            </div>

                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto">
                                    <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.player.index') }}">
                                        <span class="btn-inner--icon mt-1"><i
                                                class="material-icons">arrow_back</i>Back</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form role="form" method="POST" class="text-start" action="{{ route('admin.player.store') }}">
                            @csrf
                            @if(auth()->user()->hasRole('Master'))
                            <div class="custom-form-group">
                                <label for="title">Agent ReferralCode <span class="text-danger">*</span></label>
                                <input type="text" name="referral_code" class="form-control" value="{{old('referral_code')}}">
                                @error('referral_code')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            @endif
                            <div class="custom-form-group">
                                <label for="title">Player ID <span class="text-danger">*</span></label>
                                <input type="text" name="user_name" class="form-control" value="{{ $player_name }}"
                                    readonly>
                                @error('user_name')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="custom-form-group">
                                <label for="title">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Password <span class="text-danger">*</span></label>
                                <input type="text" name="password" class="form-control" value="{{ old('password') }}">
                                @error('password')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Phone No <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="custom-form-group">
                                <p>Max Balance : </p>
                                <span class="badge badge-sm bg-gradient-success">{{ auth()->user()->balanceFloat }}</span>
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Amount</label>
                                <input type="text" name="amount" class="form-control" value="{{ old('amount') }}"
                                    placeholder="0.00">
                                @error('amount')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <button class="btn btn-info" type="button" id="resetFormButton">Reset</button>

                                <button type="submit" class="btn btn-primary" type="button">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">

        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>

    <script>
        var errorMessage = @json(session('error'));
        var successMessage = @json(session('success'));
        var url = 'https://moneyking7.com/login';
        var user_name = @json(session('user_name'));
        var pw = @json(session('password'));

        console.log(user_name);

        @if (session()->has('success'))
            Swal.fire({
                title: successMessage,
                icon: "success",
                showConfirmButton: false,
                showCloseButton: true,
                html: `
  <table class="table table-bordered" style="background:#eee;">
  <tbody>
  <tr>
    <td>user_name</td>
    <td id="tuser_name"> ${user_name}</td>
  </tr>
  <tr>
    <td>pw</td>
    <td id="tpassword"> ${pw}</td>
  </tr>
  <tr>
    <td>url</td>
    <td id=""> ${url}</td>
  </tr>
  <tr>
    <td></td>
    <td><a href="#" onclick="copy()" class="btn btn-sm btn-primary">copy</a></td>
  </tr>
 </tbody>
  </table>
  `
            });
        @elseif (session()->has('error'))
            Swal.fire({
                icon: 'error',
                title: errorMessage,
                showConfirmButton: false,
                timer: 1500
            })
        @endif
        function copy() {
            var user_name = $('#tuser_name').text();
            var password = $('#tpassword').text();
            var copy = "url : " + url + "\nuser_name : " + user_name + "\npw : " + password;
            copyToClipboard(copy)
        }

        function copyToClipboard(v) {
            var $temp = $("<textarea>");
            $("body").append($temp);
            var html = v;
            $temp.val(html).select();
            document.execCommand("copy");
            $temp.remove();
        }
    </script>
@endsection
