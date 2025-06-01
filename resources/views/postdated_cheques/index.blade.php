@extends('layouts.app')
@section('title', __('lang_v1.post_dated_cheques'))
@section('content')


@php
                    
    $business_id = request()
        ->session()
        ->get('user.business_id');
    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp
   
    
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">@lang('lang_v1.post_dated_cheques')</h4>
                    <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                        <li><a href="{{ action('PostdatedChequeController@index') }}">@lang('lang_v1.post_dated_cheques')</a></li>
                        <li><span></span>@lang( 'contact.manage_your_contact', ['contacts' => __('lang_v1.post_dated_cheques') ])</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main content -->
    <section class="content main-content-inner">
        <div class="settlement_tabs">
            <ul class="nav nav-tabs">
                <li class="@if(empty(session('status.tab'))) active @endif">
                    <a href="#post_dated_cheques" data-toggle="tab">
                        <i class="fa fa-list"></i> <strong>
                            @lang('lang_v1.post_dated_cheques') </strong>
                    </a>
                </li>
                <li class=" @if(session('status.tab') == 'old_post_dated_cheques') active @endif">
                    <a href="#old_post_dated_cheques" data-toggle="tab">
                        <i class="fa fa-list"></i> <strong>
                            @lang('lang_v1.old_post_dated_cheques') </strong>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane @if(empty(session('status.tab'))) active @endif"
                id="post_dated_cheques">
                    @include('postdated_cheques.postdated_cheques')
                </div>
                <div class="tab-pane @if(session('status.tab') == 'old_post_dated_cheques') active @endif"
                id="old_post_dated_cheques">
                        @include('postdated_cheques.old_postdated_cheques')
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
<style>
  .nav-tabs-custom>.nav-tabs>li.active a{
    color:#3c8dbc;
  }
  .nav-tabs-custom>.nav-tabs>li.active a:hover{
    color:#3c8dbc;
  }
</style>

<div class="modal fade" id="noteModal" role="dialog" 
aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang( 'lang_v1.note' )</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <p id="noteContent" class="text-center text-bold"></p>
        </div>

      </div>
    </div>
  </div>
  <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
@endsection
@section('javascript')
    @if(session('status'))
        @if(!session('status')['success'])
            <script>
                toastr.error('{{ session("status")["msg"] }}');
            </script>
        @endif
    @endif
    <script>
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
    </script>

    <script>
        $(document).on('click', 'button.note_btn', function(e){
          let note = $(this).data('string');
          $("#noteContent").html(note);
          $("#noteModal").modal('show');
           
        });
            
        $(document).ready(function () {

            var postdatedChequeDateRangeSettings = Object.assign({}, dateRangeSettings, {
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
            })
            var loaded = false;

            $('#post_dated_cheque_type').change(function () {
                var cheque_type = $(this).val();
                
                if(cheque_type == 'customer_cheques'){
                    $("#post_party_type").val('customer').trigger('change');
                }else{
                    $("#post_party_type").val('').trigger('change');
                }
            });
            
            $('#post_party_type').change(function () {
                
                var cheque_type = $(this).val();
                
                $.ajax({
                    method: 'get',
                    url: '/accounting-module/dated-cheques-party-type',
                    data: {cheque_type},
                    success: function (result) {
                        var customer_id = $('#post_dated_cheque_customer_id');
                        customer_id.empty();
                        var contacts = result.data;
                        
                        customer_id.append($('<option>', {
                                value: "",
                                text: "{{ __('lang_v1.all')}}"
                            }));
        
                        $.each(contacts, function(key, value) {
                            // Create option element
                            var option = $('<option>', {
                                value: key,
                                text: value
                            });
                    
                            // Append option to select element
                            customer_id.append(option);
                        });
                    },
                });
                
            });
            
            $('#old_post_party_type').change(function () {
                var cheque_type = $(this).val();
                $.ajax({
                    method: 'get',
                    url: '/accounting-module/dated-cheques-party-type',
                    data: {cheque_type},
                    success: function (result) {
                        var customer_id = $('#old_post_dated_cheque_customer_id');
                        customer_id.empty();
                        var contacts = result.data;
                        
                        customer_id.append($('<option>', {
                                value: "",
                                text: "{{ __('lang_v1.all')}}"
                            }));
        
                        $.each(contacts, function(key, value) {
                            // Create option element
                            var option = $('<option>', {
                                value: key,
                                text: value
                            });
                    
                            // Append option to select element
                            customer_id.append(option);
                        });
                    },
                });
                
            });

            $('#old_post_dated_cheque_type').change(function () {
                var cheque_type = $(this).val();
                
                if(cheque_type == 'customer_cheques'){
                    $("#old_post_party_type").val('customer').trigger('change');
                }else{
                    $("#old_post_party_type").val('').trigger('change');
                }
            });
            
            // Date range as a button
                $('#post_dated_cheque_date_range').daterangepicker(
                    postdatedChequeDateRangeSettings,
                    function (start, end) {
                        $('#post_dated_cheque_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                            post_dated_cheque_table.ajax.reload();
                            populatePostDatedFilters();
                    }
                );

                $('#post_dated_cheque_date_range').on('cancel.daterangepicker', function (ev, picker) {
                    $('#post_dated_cheque_date_range').val('');
                    console.log('post_dated_cheque_table', post_dated_cheque_table);
                        post_dated_cheque_table.ajax.reload();
                        populatePostDatedFilters();
                });
                
                
                if(!$.fn.dataTable.isDataTable("#post_dated_cheque_table")) {
                    post_dated_cheque_table = $('#post_dated_cheque_table').DataTable({
                        processing: true,
                        serverSide: true,
                        aaSorting: [[1, 'desc']],
                        "ajax": {
                            "url": "/accounting-module/post-dated-cheques",
                            "data": function (d) {
                                if ($('#post_dated_cheque_date_range').val()) {
                                    var start = $('#post_dated_cheque_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    var end = $('#post_dated_cheque_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                    d.start_date = start;
                                    d.end_date = end;
                                }
                                d.customer_id = $('#post_dated_cheque_customer_id').val();
                                d.bank = $('#post_dated_cheque_bank').val();
                                d.payment_amount = $('#post_dated_cheque_amount').val();
                                d.cheque_number = $('#post_dated_cheque_no').val();
                                d.post_party_type = $('#post_party_type').val();
                            }
                        },
                        columns: [
                            {data: 'action', name: 'action', searchable: false},
                            {data: 'cheque_date', name: 'tp.cheque_date'},
                            {data: 'payment_ref_no', name: 'tp.payment_ref_no'},
                            {data: 'contact_type', name: 'contacts.type'},
                            {data: 'name', name: 'contacts.name'},
                            {data: 'payment_amount', name: 'payment_amount'},
                            {data: 'bank_name', name: 'bank_name'},
                            {data: 'cheque_number', name: 'cheque_number'},
                            {data: 'note', name: 'note'},
                            {data: 'username', name: 'users.username'},
                        ],
                        "fnDrawCallback": function (oSettings) {
                            __currency_convert_recursively($('#post_dated_cheque_table'));
                        },
                    });
                } else {
                    post_dated_cheque_table.ajax.reload();
                }

                $('#post_party_type,#post_dated_cheque_date_range, #post_dated_cheque_amount, #post_dated_cheque_no ,#post_dated_cheque_type, #post_dated_cheque_bank,#post_dated_cheque_customer_id').change(function () {
                    post_dated_cheque_table.ajax.reload();
                    populatePostDatedFilters();
            
                });


            // initialize data table for first time when customer or supplier id changed
            
            var oldPostDatedChequeDateRangeSettings = Object.assign({}, dateRangeSettings, {
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
            })

            $('#old_post_dated_cheque_type').change(function () {
                $('.old_p_customer_id').removeClass('d-none')                
            });
            
            
            // Date range as a button
                $('#old_post_dated_cheque_date_range').daterangepicker(
                    oldPostDatedChequeDateRangeSettings,
                    function (start, end) {
                        $('#old_post_dated_cheque_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                            old_post_dated_cheque_table.ajax.reload();
                            populateOldPostDatedFilters();
                    }
                );

                $('#old_post_dated_cheque_date_range').on('cancel.daterangepicker', function (ev, picker) {
                    $('#old_post_dated_cheque_date_range').val('');
                    console.log('old_post_dated_cheque_table', old_post_dated_cheque_table);
                        old_post_dated_cheque_table.ajax.reload();
                        populateOldPostDatedFilters();
                });
                
                if(!$.fn.dataTable.isDataTable("#old_post_dated_cheque_table")) {

                    old_post_dated_cheque_table = $('#old_post_dated_cheque_table').DataTable({
                            processing: true,
                            serverSide: true,
                            aaSorting: [[1, 'desc']],
                            "ajax": {
                                "url": "/accounting-module/old-post-dated-cheques",
                                "data": function (d) {
                                    if ($('#old_post_dated_cheque_date_range').val()) {
                                        var start = $('#old_post_dated_cheque_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                        var end = $('#old_post_dated_cheque_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                        d.start_date = start;
                                        d.end_date = end;
                                    }
                                    d.customer_id = $('#old_post_dated_cheque_customer_id').val();
                                    d.bank = $('#old_post_dated_cheque_bank').val();
                                    d.payment_amount = $('#old_post_dated_cheque_amount').val();
                                    d.cheque_number = $('#old_post_dated_cheque_no').val();
                                    d.post_party_type = $('#old_post_party_type').val();
                                }
                            },
                            columns: [
                                {data: 'action', name: 'action', searchable: false},
                                {data: 'cheque_date', name: 'tp.cheque_date'},
                                {data: 'payment_ref_no', name: 'tp.payment_ref_no'},
                                {data: 'contact_type', name: 'contacts.type'},
                                {data: 'name', name: 'contacts.name'},
                                {data: 'payment_amount', name: 'payment_amount'},
                                {data: 'bank_name', name: 'bank_name'},
                                {data: 'cheque_number', name: 'cheque_number'},
                                {data: 'note', name: 'note'},
                                {data: 'username', name: 'users.username'},
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#old_post_dated_cheque_table'));
                            },
                        });
                } else {
                    old_post_dated_cheque_table.ajax.reload();
                    populateOldPostDatedFilters();
                }

                $('#old_post_party_type,#old_post_dated_cheque_date_range, #old_post_dated_cheque_amount, #old_post_dated_cheque_no ,#old_post_dated_cheque_type, #old_post_dated_cheque_bank,#post_dated_cheque_customer_id').change(function () {
                    old_post_dated_cheque_table.ajax.reload();
                    populateOldPostDatedFilters();
            
                });


            populatePostDatedFilters();
            populateOldPostDatedFilters();

        });

        // select box
        $.fn.populate = function(data, callable = null) {
            $(this).empty()
            $(this).append(`<option value="">All</option>`)
            data.forEach(item=>{
                $(this).append(`<option value="${item}">${callable?callable(item):item}</option>`)
            })
        }

        $(document).on('click', '.view_payment_modal', function(e) {
            e.preventDefault();
            var container = $('.payment_modal');

            $.ajax({
                url: $(this).attr('href'),
                dataType: 'html',
                success: function(result) {
                    $(container)
                        .html(result)
                        .modal('show');
                    __currency_convert_recursively(container);
                },
            });
        });
        
        function populatePostDatedFilters(){
            var start_date = null;
            var end_date = null;
            var customer_id = $('#post_dated_cheque_customer_id').val();
            
            
            if ($('#post_dated_cheque_date_range').val()) {
                start_date = $('#post_dated_cheque_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end_date = $('#post_dated_cheque_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
            
            
            var bank = $('#post_dated_cheque_bank').val();
            var payment_amount = $('#post_dated_cheque_amount').val();
            var cheque_number = $('#post_dated_cheque_no').val();
            var post_party_type = $('#post_party_type').val();
                                    
            var type = 'amount';
            
            $.ajax({
                method: 'get',
                url: '/accounting-module/post-dated-cheques-filters',
                data: {start_date, end_date,customer_id, bank, post_party_type,cheque_number,type},
                success: function (result) {
                    console.log(result);
                    
                    $('#post_dated_cheque_amount').populate(result.data, (item) => {
                        // Convert the string to a number and round it to 2 decimal places
                        const num = parseFloat(item).toFixed(2);
                        // Add a thousand separator to the number
                        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    });
                },
            });
            
            var type = 'cheque_number';

            $.ajax({
                method: 'get',
                url: '/accounting-module/post-dated-cheques-filters',
                data: {start_date, end_date,customer_id, bank, post_party_type,payment_amount,type},
                success: function (result) {
                    $('#post_dated_cheque_no').populate(result.data);
                },
            });
        }
        
        
        function populateOldPostDatedFilters(){
            var start_date = null;
            var end_date = null;
            var customer_id = $('#old_post_dated_cheque_customer_id').val();
            
            
            if ($('#old_post_dated_cheque_date_range').val()) {
                start_date = $('#old_post_dated_cheque_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end_date = $('#old_post_dated_cheque_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
            
            
            var bank = $('#old_post_dated_cheque_bank').val();
            var payment_amount = $('#old_post_dated_cheque_amount').val();
            var cheque_number = $('#old_post_dated_cheque_no').val();
            var post_party_type = $('#post_party_type').val();
                                    
            var type = 'amount';
            
            $.ajax({
                method: 'get',
                url: '/accounting-module/old-post-dated-cheques-filters',
                data: {start_date, end_date,customer_id, bank, post_party_type,cheque_number,type},
                success: function (result) {
                    console.log(result);
                    
                    $('#old_post_dated_cheque_amount').populate(result.data, (item) => {
                        // Convert the string to a number and round it to 2 decimal places
                        const num = parseFloat(item).toFixed(2);
                        // Add a thousand separator to the number
                        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    });
                },
            });
            
            var type = 'cheque_number';

            $.ajax({
                method: 'get',
                url: '/accounting-module/old-post-dated-cheques-filters',
                data: {start_date, end_date,customer_id, bank, post_party_type,payment_amount,type},
                success: function (result) {
                    $('#old_post_dated_cheque_no').populate(result.data);
                },
            });
        }

    </script>
@endsection
