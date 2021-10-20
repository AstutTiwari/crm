@extends('layouts.app')
@section('plugin-css')
<!-- Plugins css -->

@endsection
@section('custom-css')
<link href="{{ URL::asset('css/leads.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 leads-section teant-top-btn">
            <div class="top-bar-section">
                <a href="{{route('companies.create')}}"  class="btn btn-cancel mr-2" >Add Company <i class="mdi mdi-information-outline"></i></a>
            </div>
            <div class="card leads-table-card">
                <div class="card-body leads-table-body">
                    <div class="section-title-top">
                        <div class="title-left">
                           <h1 class="header-title">Company</h1>
                        </div>
                        
                    </div>
                    <table id="companyTable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Email</th> 
                                <th>Action</th>
                            </tr>
                        </thead>                
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- upload document model  -->
    
</div>
@endsection
@section('custom-script')
<script>
    
    var l = false;
    var status;
    var table_instance;  
    var initialized = false;
    table_instance = $('#companyTable').DataTable({
        "lengthChange": false,
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "processing": '<div class="table-loader d-flex align-items-center p-2"> <strong>Processing...</strong> <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div> </div>'
        },
        'drawCallback': function (oSettings) {
            if (!initialized) {
                    $('#search_application.dataTables_filter').each(function () {
                        initialized = true;
                    });
                }
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        searching: false,    
        processing: true,
        serverSide: true,
        responsive:true,
        order: [], //Initial no order.
        ajax: {
            url: "{{ route('company.list') }}",
            method: 'POST',
            data:function(d)
            {
                d.status = status;
            },
            complete: function(res) {
                if(l) {
                        l.stop();
                    }
                }
        },
        columnDefs: [
            { 
                "responsivePriority": 1, 
                "targets": -1 
            }
        ],
        columns: [
            {data: 'logo', name: 'logo'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action', "searchable": false, "orderable": false, width: '50px', className : "text-center"}
        ]
    });
    function companyDelete(data=null,id=null)
{
    $.confirm({
        title: 'Confirm!',
        content: 'Do you want to delete the selected company ?',
        buttons: {
            confirm: {
                text: 'Yes',
                btnClass: 'btn btn-success',
                keys: ['enter'],
                action: function() {
                    $.ajax({
                        type: 'DELETE',
                        url: "{{route('companies.destroy',"+id+")}}",
                        data: {
                            'id': id,
                        },
                        dataType: "json",
                        success: function(resultData) {
                            if (resultData.success) {
                                $.toast({
                                    heading: 'Success',
                                    text: resultData.message,
                                    icon: 'success',
                                    position: 'top-right',
                                    hideAfter: 5000,
                                    loader: false,
                                });
                                table_instance.ajax.reload(null,true);
                            }
                            else if (!resultData.success) {
                                $.toast({
                                    heading: 'Error',
                                    text: resultData.error,
                                    icon: 'error',
                                    position: 'top-right',
                                    hideAfter: 5000,
                                    loader: false,
                                })
                            }
                        },
                        error: function (jqXHR, exception) {
                            var msg = '';
                            if (jqXHR.status === 0) {
                                msg = 'Not connect.\n Verify Network.';
                            } else if (jqXHR.status == 404) {
                                msg = 'Requested page not found. [404]';
                            } else if (jqXHR.status == 500) {
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
                }
            },
            cancel: function() {
                return true;
            }
        }
    });
}
</script>
@endsection