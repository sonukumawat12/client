<!-- Main content -->
<section class="content">
    

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'leads::lang.all_labels')])
            @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-primary btn-modal pull-right" id="add_label_btn"
                    data-href="{{action('\Modules\Leads\Http\Controllers\LabelController@create')}}"
                    data-container=".category_model">
                    <i class="fa fa-plus"></i> @lang( 'leads::lang.add_category' )</button>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="leads_labels_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'leads::lang.label_1' )</th>
                                <th>@lang( 'leads::lang.label_2' )</th>
                                <th>@lang( 'leads::lang.label_3' )</th>
                                <th>@lang( 'leads::lang.user' )</th>
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