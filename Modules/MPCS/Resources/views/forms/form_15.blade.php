@extends('layouts.app')
@section('title', __('mpcs::lang.form_15'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f15_form'))
                    <li class="active">
                        <a href="#f15_form_tab" class="f15_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_15')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can(abilities: 'f15_settings_form'))
                    <li class="">
                        <a href="#f15_form_settings_tab" class="f15_form_settings_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.form_15_settings')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if(auth()->user()->can('f15_form'))
                    <div class="tab-pane active" id="f15_form_tab">
                        @include('mpcs::forms.partials.f15_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('f15_settings_form'))
                    <div class="tab-pane" id="f15_form_settings_tab">
                        @include('mpcs::forms.partials.15_settings_form')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade form_15_settings_modal" id="form_15_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade update_form_15_settings_modal" id="update_form_15_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        
        get15Form();

        function get15Form(){
            var selected_date = $('#form_15_date').val();

            $.ajax({
                method: 'get',
                url: '/mpcs/get-15-form',
                data: {
                    selected_date
                },
                contentType: 'html',
                success: function(result) {
                    if (result) {
                        $('#cash_sales').text(Number(result.cash_sale));
                        $('#card_sales').text(Number(result.card_sale));
                        $('#total_cash_sale').text(Number(result.card_sale) + Number(result.cash_sale));
                        $('#total_credit_sale').text(Number(result.credit_sale));
                        $('#total_sale').text(Number(result.card_sale) + Number(result.cash_sale) + Number(result.credit_sale));
                        $('#total_sale_pre_day').text(Number(result.total_sale_pre_day));
                        $('#total_sale_today').text(Number(result.card_sale) + Number(result.cash_sale) + Number(result.credit_sale) + Number(result.total_sale_pre_day));
                    } else {
                        $('#cash_sales').text("");
                        $('#card_sales').text("");
                        $('#total_cash_sale').text("");
                        $('#total_credit_sale').text("");
                        $('#total_sale').text("");
                        $('#total_sale_pre_day').text("");
                        $('#total_sale_today').text("");
                    }
                },
            });
        }

        $('#form_15_date').change(function(){
            get15Form();
        });

        //form 15 list
        form_15_settings_table = $('#form_15_settings_table').DataTable({
            processing: true,
            serverSide: true,
            paging: false, 
            ajax: {
                "type": "get",
                "url": "/mpcs/get-form-15-settings",
            },
            columns: [
                { data: 'action', name: 'action', searchable: false, orderable: false },
                { data: 'starting_number', name: 'starting_number' },
                { data: 'total_sale_to_pre', name: 'total_sale_to_pre'},
                { data: 'pre_day_cash_sale', name: 'pre_day_cash_sale' },
                { data: 'pre_day_card_sale', name: 'pre_day_card_sale' },
                { data: 'pre_day_credit_sale', name: 'pre_day_credit_sale' },
                { data: 'pre_day_cash', name: 'pre_day_cash' },
                { data: 'pre_day_cheques', name: 'pre_day_cheques'},
                { data: 'pre_day_total', name: 'pre_day_total'},
                { data: 'pre_day_balance', name: 'pre_day_balance'},
                { data: 'pre_day_grand_total', name: 'pre_day_grand_total'},
            ]
        });

        //form 15 section
        $(document).on('submit', 'form#add_f15_form_settings', function(e) {
        
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        
                        toastr.success(result.msg);
                        form_9a_settings_table.ajax.reload();
                        $('div#form_15_settings_modal').modal('hide');
                        
                        if ($('#form_15_settings_table').length > 0) {
                            $(this).find('button[type="submit"]').attr('disabled', false);
                        }
                        
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });

        //update form 9a section
        $(document).on('submit', 'form#update_f15_form_settings', function(e) {

            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        
                        toastr.success(result.msg);
                        form_9a_settings_table.ajax.reload();
                        $('div#update_form_15_settings_modal').modal('hide');
                        
                        if ($('#form_15_settings_table').length > 0) {
                            $(this).find('button[type="submit"]').attr('disabled', false);
                        }
                        
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
        
        $("#print_div").click(function(){
            printDiv();
        });

        function printDiv() {
            var w = window.open('', '_self');
            var html = `
                <html>
                    <head>
                        <style>
                            @page {
                                size: landscape;
                            }
                            body {
                                width: 100%;
                                margin: 0;
                                padding: 0;
                            }
                            @media print {
                                html, body {
                                    width: 100%;
                                    overflow: visible !important;
                                }
                                * {
                                    font-size: 8pt;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${document.getElementById("print_content").innerHTML}
                    </body>
                </html>
            `;
            $(w.document.body).html(html);
            w.print();
            w.close();
            window.location.href = "{{URL::to('/')}}/mpcs/form-15";
        }

    });
    
</script>


@endsection