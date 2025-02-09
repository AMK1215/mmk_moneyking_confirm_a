@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
 <div class="col-8">
  <div class="container mt-2">
   <div class="d-flex justify-content-between">
    <h4>Agent Detail</h4>
    <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.agent.index') }}">
     <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
    </a>
   </div>
   <div class="card">
    <div class="table-responsive">
     <table class="table align-items-center mb-0">
      <tbody>
       <tr>
        <th>ID</th>
        <td>{!! $user_detail->id !!}</td>
       </tr>
       <tr>
        <th>User Name</th>
        <td>{!! $user_detail->name !!}</td>
       </tr>
       <tr>
        <th>Phone</th>
        <td>{!! $user_detail->phone !!}</td>
       </tr>
       <tr>
        <th>Role</th>
        <td>
         @foreach ($user_detail->roles as $role)
         <span class="badge badge-pill badge-primary">{{ $role->title }}</span>
         @endforeach
        </td>
       </tr>

       <tr>
        <th>Create Date</th>
        <td>{!! $user_detail->created_at !!}</td>
       </tr>
       <tr>
        <th>Update Date</th>
        <td>{!! $user_detail->updated_at !!}</td>
       </tr>
      </tbody>
     </table>
    </div>
   </div>
  </div>
 </div>
</div>


@endsection
@section('scripts')
<script>
if (document.getElementById('choices-tags-edit')) {
 var tags = document.getElementById('choices-tags-edit');
 const examples = new Choices(tags, {
  removeItemButton: true
 });
}
</script>
<script>
if (document.getElementById('choices-roles')) {
 var role = document.getElementById('choices-roles');
 const examples = new Choices(role, {
  removeItemButton: true
 });

 examples.setChoices(
  [{
    value: 'One',
    label: 'Expired',
    disabled: true
   },
   {
    value: 'Two',
    label: 'Out of Role',
    selected: true
   }
  ],
  'value',
  'label',
  false,
 );
}
// store role
$(document).ready(function() {
 $('#submitForm').click(function(e) {
  e.preventDefault();

  $.ajax({
   type: "POST",
   url: "{{ route('admin.roles.store') }}",
   data: $('form').serialize(),
   success: function(response) {
    Swal.fire({
     icon: 'success',
     title: 'Role created successfully',
     showConfirmButton: false,
     timer: 1500
    });
    // Reset the form after successful submission
    $('form')[0].reset();
   },
   error: function(error) {
    console.log(error);
    Swal.fire({
     icon: 'error',
     title: 'Oops...',
     text: 'Something went wrong!'
    });
   }
  });
 });
});
</script>
@endsection
