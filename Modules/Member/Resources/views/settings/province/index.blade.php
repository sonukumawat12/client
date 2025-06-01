<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'member::lang.all_provinces')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right mb-12" id="add_province_btn"
                    data-href="{{action('\Modules\Member\Http\Controllers\ProvinceController@create')}}"
                    data-container=".provinces_model">
                    <i class="fa fa-plus"></i> @lang( 'member::lang.add' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="province_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'member::lang.province' )</th>
                                <th>@lang( 'member::lang.country' )</th>
                                <th>@lang( 'member::lang.add_by' )</th>
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

</section>
<!-- /.content -->