@extends('layouts.app')
@section('title', __( 'account.balance_sheet' ))

@section('content')


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang( 'account.balance_sheet')</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Account Reports</a></li>
                    <li><span>@lang( 'account.balance_sheet')</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    
    <div class="box box-solid">
        
        <div class="box-body" style="width: 100%;">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-border table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <label for="business_location">@lang('account.business_locations'):</label>
                                    {!! Form::select('business_location', $business_locations, null, ['class' => 'form-control select2',
                                    'placeholder' =>__('lang_v1.all'), 'style' => 'width: 100%', 'id' => 'business_location']) !!}
                                </th>
                                <th>
                                    <label for="end_date">@lang('messages.first_period'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="end_date" value="{{date('Y-m-d')}}" class="date_fields form-control">
                                    </div>
                                </th>
                                <th>
                                    <label for="end_date">@lang('messages.second_period'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="end_date_sec" value="{{date('Y-m-d')}}" class="date_fields form-control">
                                    </div>
                                </th>
                                <th>
                                    <label for="end_date">@lang('messages.third_period'):</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="date" id="end_date_third" value="{{date('Y-m-d')}}" class="date_fields form-control">
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="box-body body-load" style="width: 100%;">
            
        </div>
        <div class="box-footer">
            
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $(document).ready( function(){
       
        update_balance_sheet();

        $('.date_fields, #business_location').change( function() {
            update_balance_sheet();
            $('#hidden_date').text($(this).val());
        });
    });

    function update_balance_sheet(){
        var loader = '<div style="width:100%; text-align: center" class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i></div>';
        $('div.body-load').each( function() {
            $(this).html(loader);
        });
        var end_date = $('input#end_date').val();
        var end_date_2 = $('input#end_date_sec').val();
        var end_date_3 = $('input#end_date_third').val();
        
        var location_id = $('select#business_location').val();
        $.ajax({
            url: "{{action('AccountReportsController@balanceSheetComparison')}}?end_date=" + end_date + "&end_date_2="+ end_date_2 + "&end_date_3="+ end_date_3 + "&location_id=" + location_id,
            contentType: 'html',
            success: function(result){
               $('.body-load').empty().append(result);
               $('#balance_sheet_comparison_table').DataTable({
                    dom: 'Bfrtip',
                    ordering: false,
                    pageLength: -1,
                    buttons: [
                        'csv', 'excel', 'pdf', 'print'
                    ]
                });
                
            }
        });
    }
</script>

@endsection