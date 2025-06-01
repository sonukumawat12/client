<!-- Main content -->
<section class="content" id="designatedOfficer">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
          'dsr::lang.all_designated_officers')])
                @slot('tool')
                    <div class="box-tools">

                        <button type="button" class="btn btn-primary btn-modal pull-right" id="add_dsr_btn"
                                data-href="{{action('\Modules\Dsr\Http\Controllers\DesignatedDsrController@create')}}"
                                data-container=".dsr_designated_officer_model">
                            <i class="fa fa-plus"></i> @lang( 'dsr::lang.add_designated_officers' )</button>

                        <button type="button" class="btn d-none btn-primary btn-modal pull-right" id="add_dsr_btn"
                                data-href="{{action('\Modules\Dsr\Http\Controllers\DesignatedDsrController@create')}}"
                                data-container=".dsr_designated_officer_model">
                            <i class="fa fa-plus"></i> @lang( 'dsr::lang.add_designated_officers' )</button>
                    </div>
                @endslot
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered" id="designated_officer_table" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>@lang('dsr::lang.officer_name')</th>
                                <th>@lang('dsr::lang.officer_mobile')</th>
                                <th>@lang('dsr::lang.country')</th>
                                <th>@lang('dsr::lang.state')</th>
                                <th>@lang('dsr::lang.business')</th>
                                <th>@lang('messages.action')</th>
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
    <div class="modal fade dsr_designated_officer_model" size="lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@push('javascript')
    <script type="text/javascript">
        $('#designated_officer_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ action('\Modules\Dsr\Http\Controllers\DesignatedDsrController@index') }}",
            columnDefs: [{
                "targets": 1,
                "orderable": false,
                "searchable": false
            }],
            columns: [
                { data: 'officer_name', name: 'officer_name' },
                { data: 'officer_mobile', name: 'officer_mobile' },
                { data: 'country', name: 'country' },
                { data: 'state', name: 'state' },
                { data: 'business', name: 'business' },
                { data: 'action', name: 'action' }
            ],
            "fnDrawCallback": function(oSettings) {
            }
        });
        $(document).on('click', 'button.dsr_officer_del', function(){
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
                            priority_table.ajax.reload();
                        },
                    });
                }
            });
        })
    </script>
@endpush
