
@extends('layouts.app')
@section('pulgin-css')
<link href="{{ asset('assets/libs/jquery-fancy-file-uploader/fancy-file-uploader/fancy_fileupload.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('custom-css')
<link href="{{ asset('css/settings.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid banner-update settings-page mt-3">
  <div class="row">
      <div class="col-12">
          <div class="page-title-box">
              <h1 class="page-title">Company Insert</h1>
          </div>
      </div>
  </div> 
  <div class="row">
      <div class="col-12">
          <div class="card application-card"> 
              <div class="card-body">
                  <div class="row">
                      <div class="banner-page-section col-md-12">             	
                          <form id="employee-form">
                              <div class="settings-form">
                                  <div class="row no-margin">
                                      <input type="hidden" name="employe_id" id="employe_id" value="{{$employe->id}}">
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="firstname">First Name<span class="required">*</span></label>
                                            {!! Form::text('firstname',$employe->firstname,['class'=>'form-control required ','id'=>'firstname']) !!}
                                          <label for="firstname" id="firstname-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="lastname">Last Name<span class="required">*</span></label>
                                            {!! Form::text('lastname',$employe->lastname,['class'=>'form-control required ','id'=>'lastname']) !!}
                                          <label for="lastname" id="lastname-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="email">Email<span class="required">*</span></label>
                                          {!! Form::email('email',$employe->email,['class'=>'form-control required ','id'=>'email']) !!}
                                          <label for="email" id="email-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="phone">Phone<span class="required">*</span></label>
                                            {!! Form::tel('phone',$employe->phone,['class'=>'form-control required ','id'=>'phone']) !!}
                                          <label for="phone" id="phone-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="company_id">Company<span class="required">*</span></label>
                                              {!! Form::select('company_id',$company_list,$employe->company_id, ['id'=>'company_id', 'class' => 'form-control selectpicker required',  'title' => 'Select Company']); !!}
                                          <label for="company_id" id="company_id-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="form-group mb-3 mt-2 col-md-12 btn-group">
                                            <a href="{{ url()->previous() }}" class="btn btn-cancel btn-outline-primary">Cancel</a>
                                          <button type="submit"  class="btn btn-save btn-primary btn-submit" data-style="slide-down">Save</button>
                                      </div>
                                  </div>
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
@section('plugin-script') 
@endsection 
@section('custom-script')
<script>
$(document).ready(function() {
  var l;
  $('#employee-form').validate({
  errorClass: 'is-invalid',
  ignore: [],
  debug: false,
  
  submitHandler: function(form) {
     // var text  = CKEDITOR.instances.text.document.getBody().getText().trim();
      // if(text=='')
      // {
      //     $('#text-error').show().html('Please enter the valid content');
      //     $('#text').addClass('is-invalid').focus();
      //     return false;
      // }
      l = Ladda.create( document.querySelector('#employee-form .btn-submit') );
      l.start();
      $.ajax({
          url: "{{route('emploies.update',$employe->id)}}",
          method: "PUT",
          dataType: 'json',
          data: $("#employee-form").serialize(),
          success: function (resultData) {
              l.stop();
              if(!resultData.success) {
                  if(resultData.type === "validation-error")
                  {
                      $.each( resultData.error, function( key, value ) {
                          $('#'+key+'-error').show().html(value);
                          $('#'+key).addClass('is-invalid');
                      });
                  }
                  else
                  {
                      var msg = resultData.error;
                      $.toast({
                          heading: 'Error',
                          text: msg,
                          icon: 'error',
                          position: 'top-right',
                          hideAfter: 5000,
                          loader: false,
                      })
                  }
              }
              else if(resultData.success)
              {
                  var msg = resultData.message;
                  $.toast({
                      heading: 'Success',
                      text: msg,
                      icon: 'success',
                      position: 'top-right',
                      hideAfter: 5000,
                      loader: false,
                  });
                  window.location.replace("{{route('emploies.index')}}");	
              }
          },
          error: function (jqXHR, exception) {
              var msg = '';
              if (jqXHR.status === 0) {
                  msg = 'Not connect.\n Verify Network.';
              } else if (jqXHR.status == 404) {
                  msg = 'Requested page not found. [404]';
              }else if (jqXHR.status == 401) {
                  window.location.reload();
              }else if (jqXHR.status == 500) {
                  msg = 'Internal Server Error [500].';
              } else if (exception === 'parsererror') {
                  msg = 'Requested JSON parse failed.';
              } else if (exception === 'timeout') {
                  msg = 'Time out error.';
              } else if (exception === 'abort') {
                  msg = 'Ajax request aborted.';
              } else {
                  msg = 'Uncaught Error.\n' + jqXHR.responseText;
              }
              l.stop();
              $.toast({
                  heading: 'Error',
                  text: msg,
                  icon: 'error',
                  position: 'top-right',
                  hideAfter: 5000,
                  loader: false,
              })
          }
      });
      return false;
      }
  });
  
})
</script>
@endsection 