@extends('layouts.app')
@section('title', __('lang_v1.contact_ledger_summary'))
@section('content')


<section class="content no-print">
  
    <div class="row">
        <div class="col-md-12">
            @include('contact_summary.partials.ledger_tab')
        </div>
    </div>
</section>

@stop
@section('javascript')
<script type="text/javascript">

$(document).ready(function(){
    
    if ($('#ledger_date_range_new').length == 1) {
        $('#ledger_date_range_new').daterangepicker(dateRangeSettings, function(start, end) {
            $('#ledger_date_range_new').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
            
            get_contact_ledger();
            
        });
        $('#custom_date_apply_button').on('click', function() {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#ledger_date_range_new').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#ledger_date_range_new').data('daterangepicker').setStartDate(moment(startDate));
                $('#ledger_date_range_new').data('daterangepicker').setEndDate(moment(endDate));

                $('.custom_date_typing_modal').modal('hide');
                get_contact_ledger();
            } else {
                alert("Please select both start and end dates.");
            }
        });
        $('#ledger_date_range_new').on('apply.daterangepicker', function(ev, picker) {
            if (picker.chosenLabel === 'Custom Date Range') {
                $('.custom_date_typing_modal').modal('show');
            }
        });
        $('#ledger_date_range_new').on('cancel.daterangepicker', function(ev, picker) {
            
        });
        
    }
    
    get_contact_ledger();
})

function get_contact_ledger() {
    $('#contact_ledger_div').html("<h5 class='text dange text-center'><b>{{__('lang_v1.please_wait')}}</b></h5>");
    var start_date = '';
    var end_date = '';
    var contact_type = $('select#contact_type').val();
    var contact_id = $('select#contact_id').val();
    
    if($('#ledger_date_range_new').val()) {
        start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
    
    $.ajax({
        url: '/contacts-summary/ledger?contact_id=' + contact_id + '&start_date=' + start_date + '&contact_type=' + contact_type + '&end_date=' + end_date,
        dataType: 'html',
        success: function(result) {
            $('#contact_ledger_div')
                .html(result);
            $('#ledger_table').DataTable({
                searching: true,
                ordering:false,
                paging:true,
                // dom: 't'
            });
        },
    });
}

$(document).on('change','#contact_type',function () {
    var contact_type = $(this).val() ?? 'all';
    
    $.ajax({
        method: 'get',
        url: '/contacts-summary/get-contact/'+contact_type,
        data: {},
        success: function (result) {
            var customer_id = $('#contact_id');
            customer_id.empty();
            var contacts = result;
            
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
            
            customer_id.trigger('change');
            
        },
    });
    
    $(document).on('change','#contact_id',function () {
        get_contact_ledger();
    })
    
});
</script>
@endsection