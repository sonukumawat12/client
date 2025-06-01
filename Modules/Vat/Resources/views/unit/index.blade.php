@extends('layouts.app')
@section('title', __( 'unit.units' ))

@section('content')


  <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang( 'unit.manage_your_units' )</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang( 'unit.units' )</a></li>
                    <li><span>@lang( 'unit.manage_your_units' )</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

  <!-- Main content -->
  <section class="content main-content-inner">
   <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatProductController@index')}}" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.products')</strong>
                        </a>
                    </li>
                  
                   
                    <li class="active">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatUnitController@index')}}"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.units')</strong>
                        </a>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'unit.all_your_units' )])
    @can('unit.create')
    @slot('tool')
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-primary btn-modal" 
          data-href="{{action('\Modules\Vat\Http\Controllers\VatUnitController@create')}}" 
          data-container=".unit_modal">
          <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
        </div>
    @endslot
    @endcan
    @can('unit.view')
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="vat_unit_table">
        <thead>
          <tr>
            <th>@lang( 'unit.name' )</th>
            <th>@lang( 'unit.allow_decimal' ) </th>
            <th class="notexport">@lang( 'messages.action' )</th>
          </tr>
        </thead>
      </table>
    </div>
    @endcan
    @endcomponent

    <div class="modal fade unit_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
  </div>

</section>
@stop
@section('javascript')
<!-- /.content -->
<script type="text/javascript">
  
</script>
@endsection
