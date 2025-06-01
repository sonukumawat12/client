@extends('layouts.app')
@section('title', __('mpcs::lang.15_form'))

@section('content')
<!-- Main content -->
<section class="content">
     <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">FORM F15</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">F15</a></li>
                    <li><span>Last Record</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
               <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f15_form'))
                    <li class="active">
                        <a href="#15_form_tab" class="15_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.15_form')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f15_form'))
                    <li class="">
                        <a href="#15_form_list_tab" class="15_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('mpcs::lang.F15_form_list')</strong>
                        </a>
                    </li>
                    @endif
               </ul>        
                <div class="tab-content">
                    @if(auth()->user()->can('15_form'))
                    <div class="tab-pane active" id="15_form_tab">
                        @include('mpcs::forms.partials.15_forms')
                    </div>
                    @endif
                    @if(auth()->user()->can('15_form'))
                    <div class="tab-pane" id="15_form_list_tab">
                          @include('mpcs::forms.partials.list_f16')
                    </div>
                    @endif
                   

                </div>
               
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
     
   $(document).ready(function() {
    // Setup DataTable untuk form F15
    let f15_table = $('#form_f15_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/mpcs/get-form-f15-data', // Pastikan URL ini benar
            type: 'GET',
            data: function(d) {
                let dateRange = $('#form_15_date_range').val();
                if (dateRange) {
                    let [start, end] = dateRange.split(' - ');
                    d.start_date = start;
                    d.end_date = end;
                }
                let locationId = $('#15_location_id').val();
                if (locationId) {
                    d.location_id = locationId;
                }
                return d;
            }
        },
        columns: [
            { data: 'balance_in_hand', name: 'balance_in_hand' },
            { data: 'received', name: 'received' },
            { data: 'balance_stock_note', name: 'balance_stock_note' },
            { data: 'purchases', name: 'purchases' },
            { data: 'other_payments', name: 'other_payments' },
            { data: 'balance_in_hand_2', name: 'balance_in_hand_2' },
            { data: 'sale_price', name: 'sale_price' }
        ],
        fnDrawCallback: function(oSettings) {
            calculateTotals();
        }
    });

    // Setup date range picker
    $('#form_15_date_range').daterangepicker({
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Event handler untuk perubahan tanggal
    $('#form_15_date_range').on('change', function() {
        f15_table.ajax.reload();
        getPreviousValues();
    });

    // Fungsi untuk mendapatkan nilai sebelumnya
    function getPreviousValues() {
        let dateRange = $('#form_15_date_range').val();
        let [start, end] = dateRange.split(' - ');
        
        $.ajax({
            method: 'GET',
            url: '/mpcs/get-previous-f15-values',
            data: {
                start_date: start,
                end_date: end,
                location_id: $('#15_location_id').val()
            },
            success: function(response) {
                // Update nilai-nilai pada form
                $('#pre_balance_in_hand').text(formatNumber(response.previous.balance_in_hand));
                $('#pre_received').text(formatNumber(response.previous.received));
                $('#pre_balance_stock').text(formatNumber(response.previous.balance_stock_note));
                
                // Update total
                calculateGrandTotal(response.previous);
            },
            error: function(xhr, status, error) {
                console.error('Error getting previous values:', error);
            }
        });
    }

    // Fungsi untuk menghitung total
    function calculateTotals() {
        let balanceInHand = parseFloat($('#balance_in_hand').val()) || 0;
        let received = parseFloat($('#received').val()) || 0;
        let balanceStock = parseFloat($('#balance_stock_note').val()) || 0;
        
        let total = balanceInHand + received + balanceStock;
        $('#total_amount').val(formatNumber(total));
    }

    // Fungsi format angka
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }

    // Event handler untuk simpan data
    // $('#save_f15').click(function(e) {
    //     e.preventDefault();
        
    //     let formData = {
    //         date: $('#form_15_date_range').val(),
    //         balance_in_hand: $('#balance_in_hand').val(),
    //         received: $('#received').val(),
    //         balance_stock_note: $('#balance_stock_note').val(),
    //         purchases: $('#purchases').val(),
    //         other_payments: $('#other_payments').val(),
    //         balance_in_hand_2: $('#balance_in_hand_2').val(),
    //         sale_price: $('#sale_price').val()
    //     };

    //     $.ajax({
    //         method: 'POST',
    //         url: '/mpcs/save-form-f15',
    //         data: formData,
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function(response) {
    //             if(response.success) {
    //                 toastr.success('Form F15 saved successfully');
    //                 f15_table.ajax.reload();
    //             } else {
    //                 toastr.error(response.message);
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             toastr.error('Error saving Form F15');
    //         }
    //     });
    // });

    // Load initial data
    getPreviousValues();
});
 
</script>
@endsection