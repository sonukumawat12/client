<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('gramaseva_province', __( 'member::lang.province' )) !!}
                    {!! Form::select('gramaseva_province',$gramaseva_province, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'shipping::lang.please_select' ), 'id' => 'gramaseva_province']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('gramaseva_district', __( 'member::lang.district' )) !!}
                    {!! Form::select('gramaseva_district',$gramaseva_district, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'shipping::lang.please_select' ), 'id' => 'gramaseva_district']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('gramaseva_electrorate', __( 'member::lang.electrorate' )) !!}
                    {!! Form::select('gramaseva_electrorate',$gramaseva_electrorate, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'shipping::lang.please_select' ), 'id' => 'gramaseva_electrorate']);
                    !!}
                </div>
            </div>
        </div>
            @endcomponent
          </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'member::lang.all_gramaseva_vasama')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right mb-12" id="add_gramaseva_vasama_btn"
                    data-href="{{action('\Modules\Member\Http\Controllers\GramasevaVasamaController@create')}}"
                    data-container=".gramaseva_vasama_model">
                    <i class="fa fa-plus"></i> @lang( 'member::lang.add' )</button>
            </div>
            @endslot
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-striped table-bordered" id="gramaseva_vasama_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'messages.action' )</th>
                                <th>@lang( 'member::lang.date' )</th>
                                <th>@lang( 'member::lang.province' )</th>
                                <th>@lang( 'member::lang.district' )</th>
                                <th>@lang( 'member::lang.electrorate' )</th>
                                <th>@lang( 'member::lang.gramaseva_vasama' )</th>
                                <th>@lang( 'member::lang.add_by' )</th>
                                
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

</section>
<!-- /.content -->