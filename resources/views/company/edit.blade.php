
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
              <h1 class="page-title">Company Update</h1>
          </div>
      </div>
  </div> 
  <div class="row">
      <div class="col-12">
          <div class="card application-card"> 
              <div class="card-body">
                  <div class="row">
                      <div class="banner-page-section col-md-12">             	
                          <form id="company-form">
                              <div class="settings-form">
                                  <div class="row no-margin">
                                      <input type="hidden" name="image" id="image">
                                      <input type="hidden" name="company_id" id="company_id" value="{{$company->id}}">
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="name">Title<span class="required">*</span></label>
                                          {!! Form::text('name',$company->name,['class'=>'form-control required ','id'=>'name']) !!}
                                          <label for="name" id="name-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="mb-3 col-md-6">
                                          <label class="col-form-label" for="email">Email<span class="required">*</span></label>
                                          {!! Form::email('email',$company->email,['class'=>'form-control required ','id'=>'email']) !!}
                                          <label for="email" id="email-error" generated="true" class="is-invalid" style="display:none"></label>
                                      </div>
                                      <div class="form-group banner-change mb-3 col-md-6">
                                          <label class="col-form-label" for="images">Upload Logo<span class="required">*</span></label>
                                          <button type="button" class="image-guide btn btn-light" data-toggle="tooltip" data-placement="top" title="" data-original-title="The Image ratio should be 2.327(W)*1(H) for better preview!"><i class="mdi mdi-information-outline"></i></button>
                                          <input id="images" class="" type="file" name="images" accept=".jpg, .png, image/jpeg, image/png, image/webp">
                                          <!-- <input id="images" class="" type="file" name="images" accept="image/*"> -->
                                          {{-- <input id="images" onchange="verifyFileUpload(event)" class="" type="file" name="images" accept=".jpg, .png, image/jpeg, image/png"/> --}}
                                          <label for="images" id="images-error" generated="true"class="is-invalid" style="display:none"></label>
                                          <input type="hidden" class="form-control required" name="image_check"  id="image_check" value="{{@$company->logo}}">
                                          <ul class="warning-listing">
                                              <p><strong>Note:</strong></p>
                                              <li><i class="fa fa-check" aria-hidden="true"></i>The Image ratio should be <strong>100(W)*100(H)</strong> for better preview!!</li>
                                          </ul>
                                      </div>
                                      <div class="form-group mt-4 col-md-6">
                                            @if(@$company->logo)         
            								    @php
            								    $image = 'storage/'.@$company->logo;
            								    @endphp        
            								@endif
                                        <ul class="single-upload" id="upload_image">
                                              <li class="image-single">
                                                  <img src="{{ asset($image)}}" id="uploaded_image" name="image_ids" alt="image" width="121px" hight="121px" class="img-fluid img-thumbnail">
                                                  <label for="image" id="image-error" generated="true"class="is-invalid" style="display:none"></label>
                                              </li>
                                          </ul>
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
<script type="text/javascript" src="{{ asset('assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/libs/jquery-fancy-file-uploader/fancy-file-uploader/jquery.ui.widget.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/libs/jquery-fancy-file-uploader/fancy-file-uploader/jquery.fileupload.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/libs/jquery-fancy-file-uploader/fancy-file-uploader/jquery.iframe-transport.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/libs/jquery-fancy-file-uploader/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script>
@endsection 
@section('custom-script')
<script>
$(document).ready(function() {
  var l;
  $('#company-form').validate({
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
      l = Ladda.create( document.querySelector('#company-form .btn-submit') );
      l.start();
      $.ajax({
          url: "{{route('companies.update',$company->id)}}",
          method: "PUT",
          dataType: 'json',
          data: $("#company-form").serialize(),
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
                  window.location.replace("{{route('companies.index')}}");	
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
  $('#images').FancyFileUpload({
      maxNumberOfFiles: 1,
      maxfilesize : 5242880,
      fileupload: {
          maxChunkSize: 10000000,
          retries : 1,
      },
      accept :['png','jpg','jpeg','webp'],
      added: function(e, data) {  
          if (typeof(data.ff_info.errors[0]) !== "undefined") {
              return false;
          }
          if($(".ff_fileupload_queued").length > 1)
          {
              $(".ff_fileupload_queued").first().find('.ff_fileupload_remove_file').trigger('click');
          }
          var file = data.files[0]; 
          if(file)
          {
              var reader = new FileReader();
              reader.onload = function(){
                  
                  $('#uploaded_image').attr('src',reader.result);
                  $("#image").val(reader.result);
                  $("#image_check").val("yes");
              }
              reader.readAsDataURL(file);
              $(".ff_fileupload_queued").first().find('.ff_fileupload_remove_file').trigger('click');
          }
      }
  });
})
</script>
@endsection 