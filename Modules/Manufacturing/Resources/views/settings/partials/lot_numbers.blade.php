@section('css')
<style>
    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .inline {
        display: inline;
    }
    .dataTable{
        width:100% !important;
    }
</style>
@endsection
<div class="pos-tab-content">
    <div class="container-xl">
        <!-- Page title -->
        <div class="mb-3">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="page-title">
                        {{ __('Lot Numbers') }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-lg-6  mt-10">
                    <button type="button" onclick="addByLotNumbers()" class="btn btn-primary pull-right mt-10" aria-label="Left Align">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ __('Add By Lot Numbers') }}
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="table-responsive px-2 py-2">
                            <table class="table table-bordered table-striped" id="lot_numbers_table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Lot Prefix') }}</th>
                                        <th>{{ __('Lot No') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th class="w-1">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade " id="add-bylotnumbers-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content main-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">By Lot Numbers</h4>
                </div>
                <form id="mfg_by_lot_numbers_form" action="javascript:saveByLotNumbers();" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12 mb-3">
                                <label>{{__('Product')}}</label>
                                <select class="form-control" id="inp-ID_PRODUCT">
                                </select>
                            </div>
                            
                            <div class="form-group col-sm-12 mb-3">
                                <label>{{__('Lot Prefix')}}</label>
                                <input type="text" id="inp-LOT_PREFIX" class="form-control">
                            </div>
                            
                            <div class="form-group col-sm-12 mb-3">
                                <label>{{__('Lot No')}}</label>
                                <input type="text" id="inp-LOT_NO" class="form-control">
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" onclick="submitByLotNumbers()" class="btn btn-primary">@lang( 'messages.save' )</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script type="text/javascript">
        var lot_numbers_table;
        $(document).ready(function() {

            buildByLotNumbersTable();

        });

        function submitByLotNumbers() {
            if ($('#by_products_name').valid()) {
                saveByLotNumbers();
            }
        }

        function addByLotNumbers(parameter) {
            "use strict";
            $("#add-bylotnumbers-modal").modal("show");
            
            getProductsActive();
            
            return false;
        }

        function buildByLotNumbersTable() {
            lot_numbers_table = $('#lot_numbers_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            ajax: {
                url: "{{action('\Modules\Manufacturing\Http\Controllers\SettingsController@getByLotNumbers')}}",
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'product', name: 'product' },
                { data: 'lot_prefix', name: 'lot_prefix' },
                { data: 'lot_no', name: 'lot_no' },
                { data: 'user', name: 'user' },
                { data: 'action', searchable: false, orderable: false },
                
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        }

        function saveByLotNumbers() {
            $.ajax({
                type: 'POST',
                url: '{{ route("saveByLotNumbers") }}',
                data: {
                    id_product: $('#inp-ID_PRODUCT').val(),
                    lot_prefix: $('#inp-LOT_PREFIX').val(),
                    lot_no: $('#inp-LOT_NO').val(),
                },

                success: function(response) {
                    console.log(response);
                    if (response.success == 1) {
                        swal({
                            text: response.msg,
                            icon: "success",
                        });

                        $("#add-bylotnumbers-modal").modal("hide");
                        $('#by_products_name').val('');
                        lot_numbers_table.ajax.reload();
                    } else {
                        swal({
                            text: response.msg,
                            icon: "error",
                        });
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

            return false;
        }

        function disableByLotNumbersItem(id){
            $.ajax({
                type: 'POST',
                url: '{{ route("disableItem") }}',
                data: {
                    id: id
                },

                success: function(response) {
                    console.log(response);
                    if (response.success == 1) {
                        swal({
                            text: response.msg,
                            icon: "success",
                        });
                        lot_numbers_table.ajax.reload();
                    } else {
                        swal({
                            text: response.msg,
                            icon: "error",
                        });
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

            return false;
        }


        function enableByLotNumbersItem(id){
            $.ajax({
                type: 'POST',
                url: '{{ route("enableItem") }}',
                data: {
                    id: id
                },

                success: function(response) {
                    console.log(response);
                    if (response.success == 1) {
                        swal({
                            text: response.msg,
                            icon: "success",
                        });
                        lot_numbers_table.ajax.reload();
                    } else {
                        swal({
                            text: response.msg,
                            icon: "error",
                        });
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

            return false;
        }
        
        function getProductsActive() {
            $.ajax({
                type: 'GET',
                url: '{{ route("getByProductsActive") }}',

                success: function(response) {
                    
                    var data = response.data;
                    
                    $.each(data, function (index, value) {
                      $('#inp-ID_PRODUCT').append($('<option/>', { 
                          value: value.id,
                          text : value.name + " ("+ value.sku +")", 
                      }));
                    });
                   

                },
                error: function(error) {
                    console.log(error);
                }
            });
            
            return false;
        }
    </script>

</div>