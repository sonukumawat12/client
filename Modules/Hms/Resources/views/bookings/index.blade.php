@extends('layouts.app')
@section('title', __('hms::lang.bookings'))
<style>
    .dt-nowrap {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .btn-purple {
        background-color: #6f42c1;
        color: #fff;
        border: 1px solid #6f42c1;
    }
    
    .btn-purple:hover {
        background-color: #5936a3;
        border-color: #5936a3;
    }
</style>
@section('content')
   
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('hms::lang.bookings')
        </h1>
        <p><i class="fa fa-info-circle"></i> @lang('hms::lang.bookings_help_text') </p>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                    {!! Form::select('customer_id', $customers, null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('status', __('hms::lang.status') . ':') !!}
                    {!! Form::select('status', $status, null, [
                        'class' => 'form-control',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('filter_payment_status', __('hms::lang.payment_status') . ':') !!}
                    {!! Form::select(
                        'filter_payment_status',
                        [
                            'paid' => __('lang_v1.paid'),
                            'due' => __('lang_v1.due'),
                            'partial' => __('lang_v1.partial'),
                            'overdue' => __('lang_v1.overdue'),
                        ],
                        null,
                        ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')],
                    ) !!}
                </div>
            </div>
        @endcomponent
        <!--Style: btn btn-primary pull-right all-p-bt //tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right-->
        @component('components.widget')
            <div class="box-tools tw-flex tw-justify-end tw-gap-2.5 tw-mb-4">
                @can('hms.add_booking')
                        <a class="btn btn-primary pull-right all-p-bt"
                            href="{{ action([\Modules\Hms\Http\Controllers\HmsBookingController::class, 'create']) }}">
                            <!--<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"-->
                            <!--    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"-->
                            <!--    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">-->
                            <!--    <path stroke="none" d="M0 0h24v24H0z" fill="none" />-->
                            <!--    <path d="M12 5l0 14" />-->
                            <!--    <path d="M5 12l14 0" />-->
                            <!--</svg> -->
                            @lang('messages.add')
                        </a>
                @endcan
            </div>
            <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <form id="updateStatusForm">
                      @csrf
                      <input type="hidden" id="booking_id" name="booking_id">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="updateStatusLabel">Update Status</h5>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          <select class="form-control" id="new_status" name="status" required>
                            @foreach($status as $key => $label)
                              <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
            <table class="table table-bordered table-striped" id="bookings_table">
                <thead>
                    <tr>
                        <th>
                            @lang('hms::lang.booking_Id')
                        </th>

                        <th>
                            @lang('hms::lang.stay')
                        </th>
                        <th>
                            @lang('hms::lang.customer')
                        </th>
                        <th>
                            @lang('hms::lang.status')
                        </th>
                        <th>
                            @lang('hms::lang.payment_status')
                        </th>
                        <th>
                            @lang('lang_v1.payment_method')
                        </th>
                        <th>
                            @lang('hms::lang.total_amount')
                        </th>
                        <th>
                            @lang('hms::lang.total_paid')
                        </th>
                        <th>
                            @lang('hms::lang.due')
                        </th>
                        
                        <th>
                            @lang('messages.action')
                        </th>
                    </tr>
                </thead>
            </table>
        @endcomponent
        <!-- Add HMS Extra Modal -->
        <div class="modal fade check_in_out" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        </div>
    </section>
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        <!-- /.content -->
    @endsection

    @section('javascript')
    
    <script>
        // Open modal when badge is clicked
$(document).on('click', '.editable-status', function () {
    let bookingId = $(this).data('id');
    let currentStatus = $(this).data('status');

    $('#booking_id').val(bookingId);
    $('#new_status').val(currentStatus);
    $('#updateStatusModal').modal('show');
});

// Submit new status via AJAX
$('#updateStatusForm').on('submit', function (e) {
    e.preventDefault();

    let bookingId = $('#booking_id').val();
    let newStatus = $('#new_status').val();

    $.ajax({
        url: '{{ route("hms.bookings.updateStatus") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: bookingId,
            status: newStatus
        },
        success: function () {
            $('#updateStatusModal').modal('hide');
            $('#bookings_table').DataTable().ajax.reload(null, false); // Keep current page
        },
        error: function () {
            alert('Something went wrong.');
        }
    });
});

    </script>
        <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                bookings_table = $('#bookings_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    autoWidth: false, 
                    responsive: false, 
                    scrollX: true,
                    ajax: {
                        url: "{{ action([\Modules\Hms\Http\Controllers\HmsBookingController::class, 'index']) }}",
                        "data": function(d) {
                            d.customer_id = $('#customer_id').val();
                            d.status = $('#status').val();
                            d.payment_status = $('#filter_payment_status').val();
                        },
                    },
                    aaSorting: [
                        [9, 'desc']
                    ],
                    columnDefs: [
                        {
                            targets: 3, // 0-based index of "Status" column
                            width: '102px',
                            className: 'dt-nowrap'
                        }
                    ],
                    columns: [{
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'stay',
                            name: 'stay',
                            orderable: false,
                            "searchable": false
                        },
                        {
                            data: 'c_name',
                            name: 'c.name',
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status',
                        },
                        {
                            data: 'payment_methods',
                            orderable: false,
                            "searchable": false
                        },
                        {
                            data: 'final_total',
                            name: 'final_total'
                        },
                        {
                            data: 'total_paid',
                            name: 'total_paid',
                            "searchable": false
                        },
                        {
                            data: 'total_remaining',
                            name: 'total_remaining'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            sorting: false,
                            
                        }
                    ],
                });

                $(document).on('change', '#customer_id, #status, #filter_payment_status', function() {
                    bookings_table.ajax.reload();
                });

                $(document).on('click', '.btn-modal-checkIn', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: $(this).attr('href'),
                        dataType: 'html',
                        success: function(result) {
                            $('.check_in_out')
                                .html(result)
                                .modal('show');
                        },
                    });
                });
                $(".check_in_out").on("show.bs.modal", function() {
                    var currentDate = new Date();
                    var currentDateTime = moment(currentDate);

                    $('.date_picker').datetimepicker({
                        format: moment_date_format + ' ' + moment_time_format,
                        ignoreReadonly: true,
                        defaultDate: currentDateTime
                    });
                });

            });
        </script>
    @endsection
