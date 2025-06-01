@push('javascript')
    <script type="text/javascript" defer>
         fuel_provider_table = $('#fuel_provider_table').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "{{ action('\Modules\Dsr\Http\Controllers\FuelProvidersController@index') }}",
                    columnDefs: [{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                    columns: [
                        { data: 'date', name: 'date' },
                        { data: 'name', name: 'name' },
                        { data: 'email', name: 'email' },
                        { data: 'phone', name: 'phone' },
                        { data: 'address', name: 'address' },
                        { data: 'action', name: 'action' }
                    ],
                    "fnDrawCallback": function(oSettings) {
                    }
                });
            
        $(document).on('click', 'button.provider_delete', function(){
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
                            fuel_provider_table.ajax.reload();
                        },
                    });
                }
            });
        })

    </script>
@endpush
<!-- Main content -->
<section class="content" id="app">
    <div class="row">
        <div class="col-md-12">

              @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'dsr::lang.all_fuel_providers')])
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-primary btn-modal pull-right" id="add_dsr_btn"
                                data-href="{{
    action('\Modules\Dsr\Http\Controllers\FuelProvidersController@create')}}"
                                data-container=".dsr_fuel_provider_model">
                            <i class="fa fa-plus"></i> @lang( 'dsr::lang.add_fuel_providers' )</button>
                    </div>
                @endslot
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered" id="fuel_provider_table" style="width: 100%;">
                            <thead>
                            <tr>
                                <th>@lang( 'dsr::lang.date' )</th>
                                <th>@lang( 'dsr::lang.name' )</th>
                                <th>@lang( 'dsr::lang.email' )</th>
                                <th>@lang( 'dsr::lang.phone' )</th>
                                <th>@lang( 'dsr::lang.address' )</th>
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
    <div class="modal fade dsr_fuel_provider_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>


