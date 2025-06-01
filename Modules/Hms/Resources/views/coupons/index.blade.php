@extends('layouts.app')
@section('title', __('hms::lang.coupons'))
@section('content')
   
    <section class="content-header" style="padding-left: 56px;
    padding-right: 15px;
    margin-bottom: -52px;">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('hms::lang.coupons')
        </h1>
        <p><i class="fa fa-info-circle"></i> @lang('hms::lang.coupon_help_text') </p>
    </section>

    <!-- Main content  -->
    <!-- style btn btn-primary pull-right all-p-btn, //tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal-coupon-->
    <section class="content">
        @component('components.widget_hms')
            <div class="box-tools tw-flex tw-justify-end tw-gap-2.5 tw-mb-4">
                <a class="btn btn-primary pull-right all-p-btn btn-sm d-inline-flex align-items-center px-1 py-0 btn-modal-coupon"
                    href="{{ action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'create']) }}">
                    <!--<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"  fill="none" stroke="currentColor"-->
                    <!--    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"-->
                    <!--    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">-->
                    <!--    <path stroke="none" d="M0 0h24v24H0z" fill="none" />-->
                    <!--    <path d="M12 5l0 14" />-->
                    <!--    <path d="M5 12l14 0" />-->
                    <!--</svg>-->
                    @lang('messages.add')
                </a>
            </div>
            <div style="overflow-x: auto;">
                <table class="table table-bordered table-striped" id="extras_table">
                    <thead>
                        <tr>
                            <th>
                                @lang('hms::lang.type')
                            </th>
                            <th>
                                @lang('hms::lang.coupon_code')
                            </th>
                            <th>
                                @lang('hms::lang.date_from')
                            </th>
                            <th>
                                @lang('hms::lang.date_to')
                            </th>
                            <th>
                                @lang('hms::lang.discount')
                            </th>
                            <th>
                                @lang('hms::lang.discount_type')
                            </th>
                            <th>
                                @lang('lang_v1.created_at')
                            </th>
                            <th>
                                @lang('messages.action')
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent



        <!-- Add HMS Extra Modal -->
       
        

    </section>
    <!-- /.content -->
    <div class="modal fade view_modal_coupon" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="edit_modal_coupons" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')

    <script type="text/javascript">
    console.log('javascrit')
        $(document).ready(function() {

            $(document).on('click', '#edit_coupon_modal', function () {
                var url = $(this).data('href');
                $.ajax({
                    method: 'GET',
                    dataType: 'html',
                    url: url,
                    success: function (response) {
                        $("#edit_modal_coupons").html(response).modal('show');
                    }
                });
            });

            superadmin_business_table = $('#extras_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                ajax: {
                    url: "{{ action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'index']) }}",
                },
                aaSorting: [
                    [6, 'desc']
                ],
                columns: [{
                        data: 'type',
                        name: 'type.type'
                    },
                    {
                        data: 'coupon_code',
                        name: 'coupon_code'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'discount_type',
                        name: 'discount_type'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        sorting: false,
                    }
                ],
            });

            $(document).on('click', '.btn-modal-coupon', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('href'),
                    dataType: 'html',
                    success: function(result) {
                        $('.view_modal_coupon')
                            .html(result)
                            .modal('show');
                    },
                });
            });

            $(".view_modal_coupon").on("show.bs.modal", function() {
                var currentDate = new Date();
                var currentDateTime = moment(currentDate);

                $('.date_picker').datetimepicker({
                    format: moment_date_format,
                    ignoreReadonly: true,
                    defaultDate: currentDateTime
                });
            });
//edit


            $(".view_modal_coupon").on("show.bs.modal", function() {
                var currentDate = new Date();
                var currentDateTime = moment(currentDate);

                $('.date_picker').datetimepicker({
                    format: moment_date_format,
                    ignoreReadonly: true,
                    defaultDate: currentDateTime
                });
            });
            $(document).on('click', 'a.delete_coupon_confirmation', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    text: "Once deleted, you will not be able to recover this Coupon !",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed) => {
                    if (confirmed) {
                        window.location.href = $(this).attr('href');
                    }
                });
            });
        });
    </script>
@endsection
