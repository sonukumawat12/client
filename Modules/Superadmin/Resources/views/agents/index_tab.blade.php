
    <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#agents_list" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-list"></i> <strong>@lang('superadmin::lang.list_agents')</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#agent_dashboard" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-dashboard"></i>
                            <strong>@lang('superadmin::lang.agent_dashboard')</strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="agents_list">
                        @include('superadmin::agents.partials.list_agents_tab')
                    </div>
                    <div class="tab-pane" id="agent_dashboard">
                        @include('superadmin::agents.partials.agent_dashboard_tab')
                    </div>
                </div>
            </div>
    
    <div class="modal fade pump_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

@section('javascript')
<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    $('#date_range_tab').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range_tab').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            agents_table.ajax.reload();
        }
    );
    $('#date_range_tab').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range_tab').val('');
        agents_table.ajax.reload();
    });

    $('#agent_dashboard_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#agent_dashboard_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            // journal_table.ajax.reload();
        }
    );
    $('#agent_dashboard_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#agent_dashboard_date_range').val('');
        // journal_table.ajax.reload();
    });


    var columns = [
            { data: 'date', name: 'date' },
            { data: 'referral_code', name: 'referral_code' },
            { data: 'name', name: 'name' },
            { data: 'mobile_number', name: 'mobile_number' },
            { data: 'email', name: 'email' },
            { data: 'referral_group', name: 'referral_group' },
            { data: 'total_orders', name: 'total_orders' },
            { data: 'active_subscription', name: 'active_subscription' },
            { data: 'income', name: 'income' },
            { data: 'paid', name: 'paid' },
            { data: 'due', name: 'due' },
            { data: 'action', name: 'action' },
        ];
  
    
    var agents_table = $('#agents_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{action("\Modules\Superadmin\Http\Controllers\AgentController@index")}}',
            data: function (d) {
                if($('#date_range_tab').val()) {
                    d.start_date= $('#date_range_tab').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date = $('#date_range_tab').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
            }
        },
        columns: columns,
        fnDrawCallback: function(oSettings) {
        
        },
    });

    $('#date_range_tab').change(function() {
        agents_table.ajax.reload();
    })

    $(document).on('click', '.delete_agent', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            text: 'This agent will be deleted',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                        agents_table.ajax.reload();
                    },
                });
            }
        });
    });

    $(document).on('click', 'a.edit_entity', function(e) {
        e.preventDefault();
        $('div.view_modal').load($(this).attr('href'), function() {
            $(this).modal('show');
        });
    });




</script>
@endsection