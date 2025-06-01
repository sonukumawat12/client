<!-- Main content -->
<section class="content">
   
<!-- <div class="row">
        <div class="col-md-3 text-red">
            <b>@lang('mpcs::lang.date_and_time'): <span class="9c_from_date">{{$date}}</span> </b>
        </div>
        <div class="col-md-3 text-red">
            <b>@lang('mpcs::lang.ref_previous_form_number'): <span class="9c_from_date">{{$form_number}}</span> </b>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <h5 style="font-weight: bold;">@lang('mpcs::lang.user_added'): {{$userAdded}} <br>
            </div>
        </div>
    </div> -->


<div class="row">
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-modal" data-href="{{action('\Modules\MPCS\Http\Controllers\F20FormController@get20FormSettings')}}" data-container=".form_16_a_settings_modal">
                <i class="fa fa-plus"></i> Add 20 Form Settings</button>
        </div>
    </div>


<div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="box-body" style="margin-top: 20px;">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div id="msg"></div>

                            <table id="form_21c_settings_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('mpcs::lang.action')</th>
                                        <th>@lang('mpcs::lang.date_and_time')</th>
                                        <!--<th>@lang('mpcs::lang.form_starting_number')</th>-->
                                        <th>@lang('mpcs::lang.form_starting_number') - Total Sale</th>
                                        <th>@lang('mpcs::lang.form_starting_number') - Cash Sale</th>
                                        <th>@lang('mpcs::lang.form_starting_number') - Credit Sale</th>
                                        <th>Category</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            @endcomponent
        </div>
    </div>

  
    <div class="modal fade form_16_a_settings_modal" id="form_16_a_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade update_form_16_a_settings_modal" id="update_form_16_a_settings_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>


</section>
<!-- /.content -->

<script type="text/javascript">

 $(document).ready(function(){

 form_21c_settings_table = $('#form_21c_settings_table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/mpcs/20formsettings',
        type: 'GET',
        dataSrc: function(json) {
            var newData = [];

            json.data.forEach(function(item) {
                // First row (General details, empty Pump columns)
                newData.push({
                    action: item.action,
                    opening_date: item.opening_date,
                    // starting_number: item.starting_number,
                    total_sale: item.total_sale,
                    cash_sale: item.cash_sale,
                    credit_sale: item.credit_sale,
                    category: item.category
                });
            });

            return newData;
        }
    },
    columns: [
        { data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '' },
        { data: 'opening_date', name: 'opening_date' },
        // { data: 'starting_number', name: 'starting_number' },
        { data: 'total_sale', name: 'total_sale' },
        { data: 'cash_sale', name: 'cash_sale' },
        { data: 'credit_sale', name: 'credit_sale' },
        { data: 'category', name: 'category' }
    ]
    });

    
    });

</script>
