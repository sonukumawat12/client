@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'vat::lang.vat_invoice').' 127'])
            
            @slot('tool')
            <div class="box-tools">
                <a type="button" class="btn btn-primary pull-right" 
                    href="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@create127')}}">
                    <i class="fa fa-plus"></i> @lang( 'vat::lang.add' )</a>
            </div>
            @endslot
            
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="vat_invoices_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'vat::lang.date' )</th>
                                <th>@lang( 'vat::lang.bill_no' )</th>
                                <th>@lang( 'vat::lang.total' )</th>
                                <th>@lang( 'vat::lang.customer' )</th>
                                <th>@lang( 'vat::lang.sub_customer' )</th>
                                <th>@lang( 'vat::lang.credit_limit' )</th>
                                <th>@lang( 'vat::lang.outstanding' )</th>
                                <th>@lang( 'vat::lang.user' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade issue_bill_customer_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    <div class="modal fade" id="confirmPrint" role="dialog" aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
    
            <!-- Modal Body -->
            <div class="modal-body">
              <p class="text-center text-bold">@lang('vat::lang.need_to_print_acknowledge')</p>
              <input type="hidden" id="print_url">
            </div>
            
            <div class="modal-footer text-center">
                <button type="button" data-string="no" class="btn btn-secondary print_invoice">@lang('lang_v1.no')</button>
                <button type="button" data-string="yes" class="btn btn-primary print_invoice">@lang('lang_v1.yes')</button>
            </div>
    
          </div>
        </div>
      </div
    
    
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
    // vat_invoices_table
        vat_invoices_table = $('#vat_invoices_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@index127')}}",
            },
            
            columns: [
                {data: 'date', name: 'date'},
                {data: 'customer_bill_no', name: 'customer_bill_no'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'customer_name', name: 'contacts.name'},
                
                {data: 'sub_customer', name: 'subc.name'},
                
                {data: 'credit_limit', name: 'credit_limit'},
                {data: 'outstanding_amount', name: 'outstanding_amount'},
                
                {data: 'username', name: 'users.username'},
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete-issue_bill_customer', function(e){
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            vat_invoices_table.ajax.reload();
                        },
                    });
                }
            });
        });
        
        @if(!empty(session('status')['print_url']))
            let href = "{{session('status')['print_url']}}";
            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });
        @endif
        
         $(document).on('click', 'a.print_bill', function(){
              $("#confirmPrint").modal('show');
              let href = $(this).data('href');
              $("#print_url").val(href);
        });
        
        

        $(document).on('click', '.print_invoice', function(){
            var string = $(this).data('string');
            
            if(string == "yes"){
                var href = $("#print_url").val()+"?ack=1";
            }else{
                var href = $("#print_url").val();
            }
            
            $.ajax({
                method: 'get',
                url: href,
                data: {  },
                contentType: 'html',
                success: function(result) {
                    html = result;
                    console.log(html);
                    var w = window.open('', '_self');
                    $(w.document.body).html(html);
                    w.print();
                    w.close();
                    location.reload();
                },
            });


        });


        $(document).on('click', '#add_issue_bill_customer_btn', function(){
            $('.issue_bill_customer_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
</script>
@endsection