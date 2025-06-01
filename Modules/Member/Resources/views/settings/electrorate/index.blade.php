<!-- Main content -->
<section class="content">
    <div class="row">
      <div class="col-md-12">
          @component('components.filters', ['title' => __('report.filters')])
          <div class="row">
          <div class="col-md-3">
              <div class="form-group">
                  {!! Form::label('district_id', __( 'member::lang.district' )) !!}
                  {!! Form::select('district_id',$districts, null, ['class' => 'form-control select2',
                  'required',
                  'placeholder' => __(
                  'shipping::lang.please_select' ), 'id' => 'district_id']);
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
            'member::lang.all_electrorates')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right mb-12" id="add_electrorate_btn"
                    data-href="{{action('\Modules\Member\Http\Controllers\ElectrorateController@create')}}"
                    data-container=".electrorate_model">
                    <i class="fa fa-plus"></i> @lang( 'member::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-striped table-bordered" id="electrorate_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'messages.action' )</th>
                                <th>@lang( 'member::lang.province' )</th>
                                <th>@lang( 'member::lang.district' )</th>
                                <th>@lang( 'member::lang.electrorate' )</th>
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