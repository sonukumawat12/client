@extends('layouts.app')
@section('title', __('vat::lang.vat_payment'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="row">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range',  __('vat::lang.date') . ':') !!}
                    {!! Form::text('date_range', null, ['id'=>'date_range','class' => 'form-control','readonly', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('account_id',  __('vat::lang.bank_name') . ':') !!}
                    {!! Form::select('account_id', $bank_accounts, null, ['id'=>'account_id','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id',  __('vat::lang.customer') . ':') !!}
                    {!! Form::select('contact_id', $customers, null, ['id'=>'contact_id','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payable_id',  __('vat::lang.vat_payable_account') . ':') !!}
                    {!! Form::select('payable_id', $payable_accounts, null, ['id'=>'payable_id','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            

        @endcomponent
            </div>
        </div>
                            
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'vat::lang.vat_payment')])
            
            @slot('tool')
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-modal add_fuel_tank"
                    data-href="{{action('\Modules\Vat\Http\Controllers\VatPaymentController@create')}}"
                    data-container=".fuel_tank_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>
            </div>
            @endslot
            
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="vat_payments_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'vat::lang.date' )</th>
                                <th>@lang( 'vat::lang.form_no' )</th>
                                <th>@lang( 'vat::lang.amount' )</th>
                                <th>@lang( 'vat::lang.customer' )</th>
                                <th>@lang( 'vat::lang.vat_payable_account' )</th>
                                <th>@lang( 'vat::lang.payment_method' )</th>
                                <th>@lang( 'vat::lang.bank_name' )</th>
                                <th>@lang( 'vat::lang.cheque_no_card_no' )</th>
                                <th>@lang( 'vat::lang.cheque_date' )</th>
                                <th>@lang( 'vat::lang.to_account' )</th>
                                <th>@lang( 'vat::lang.recipient_name' )</th>
                                <th>@lang( 'vat::lang.note' )</th>
                                <th>@lang( 'vat::lang.user_added' )</th>
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
    <div class="modal fade fuel_tank_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>

    if($('#date_range').length) {
            $('#date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    vat_payments_table.ajax.reload();
                }
            );
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_range').val('');
                vat_payments_table.ajax.reload();
            });
        }
    
    // vat_payments_table
        vat_payments_table = $('#vat_payments_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Vat\Http\Controllers\VatPaymentController@index')}}",
                data: function(d){
                    d.account_id = $("#account_id").val();
                    d.contact_id = $("#contact_id").val();
                    d.payable_id = $("#payable_id").val();
                    
                    if($('#date_range').val()) {
                        var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    
                }
            },
            
            columns: [
                {data: 'date', name: 'date'},
                {data: 'form_no', name: 'form_no'},
                {data: 'amount', name: 'amount'},
                {data: 'contact_name', name: 'contacts.name'},
                {data: 'payable_name', name: 'pay.name'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'payment_name', name: 'pmt.name'},
                {data: 'cheque_number', name: 'cheque_number'},
                {data: 'cheque_date', name: 'cheque_date'},
                {data: 'to_account_no', name: 'to_account_no'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'note', name: 'note'},
                {data: 'user_created', name: 'users.username'},
                {data: 'action', name: 'action'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        $(document).on('click', 'a.delete_task', function(){
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
                            vat_payments_table.ajax.reload();
                        },
                    });
                }
            });
        });
    


        $(document).on('click', '#add_issue_bill_customer_btn', function(){
            $('.issue_bill_customer_model').modal({
                backdrop: 'static',
                keyboard: false
            })
        })
        
        
        $(document).on('change','#account_id,#contact_id,#payable_id',function(){
            vat_payments_table.ajax.reload();
        });
        
        
        $(document).on('change','.payment_method',function(){
            var payment_mthd = $(this).val();
            
            if(payment_mthd == 'Bank'){
                $(".bank_fields").show();
                $(".bank_transfer").hide();
                
                $(".bank_field").attr('required',true);
                $(".transfer_field").attr('required',false);
                
            }else if(payment_mthd == 'bank_transfer'){
                $(".bank_fields").hide();
                $(".bank_transfer").show();   
                
                $(".bank_field").attr('required',false);
                $(".transfer_field").attr('required',true);
                
            }else{
              $(".bank_fields").hide();
              $(".bank_transfer").hide();
              
              $(".bank_field").attr('required',false);
              $(".transfer_field").attr('required',false);
            }
            
            var accounting_module = $(".payment_account_id");
            var previous_acc_id = $(".previous_acc").val();
            
             $.ajax({

                method: 'get',

                url: '/accounting-module/get-account-group-name-dp',

                data: { group_name: payment_mthd},

                contentType: 'html',

                success: function(result) {
                    
                   accounting_module.empty().append(result);
                   accounting_module.attr('required', true);
                   accounting_module.val(accounting_module.find('option:first').val());
                   if(previous_acc_id){
                    accounting_module.val(previous_acc_id).change();
                   }
                    

                },

            });
            
        });
        
</script>
@endsection