<div class="modal-dialog" role="document">
    <form id="airport_form">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="airport_title">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('date_added', __('airline::lang.date_added') . ':*') !!}
                        {!! Form::text('date_added', @format_date(date('Y-m-d')), [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('airline::lang.date_added'),
                        ]) !!}
                    </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('country_select', __('airline::lang.country_select') . ':*') !!}
                        {!! Form::select('country_select', $countries, null, [
                            'class' => 'form-control select2',
                            'id' => 'country_select',
                            'required',
                            'placeholder' => __('messages.please_select'),
                        ]) !!}
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::label('province_select', __('airline::lang.province_select') . ':*') !!}
                        {!! Form::text('province_select', null, [
                            'class' => 'form-control',
                            'id' => 'province_select',
                            'required',
                            'placeholder' => __('airline::lang.province'),
                        ]) !!}
                    </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('airport_name', __('airline::lang.airport_name') . ':*') !!}
                        {!! Form::text('airport_name', null, [
                            'class' => 'form-control',
                            'id' => 'airport_name',
                            'required',
                            'placeholder' => __('airline::lang.airport_name'),
                        ]) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(".select2").select2();
</script>