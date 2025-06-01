<div class="tab-pane" id="activity_log">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('al_date_filter', __('report.date_range') . ':') !!}
                {!! Form::text('al_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="al_users_filter">{{ trans('core.by') }}</label>
                <select id="al_users_filter" class="form-control">
                    <option value="">{{ trans_choice('core.all', 1) }}</option>
                    <option v-for="user in users" :value="user.id">@{{ user.user_full_name }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="loan_activity_log_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.subject_type')</th>
                            <th>@lang('messages.action')</th>
                            <th>@lang('lang_v1.by')</th>
                            <th>@lang('brand.note')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
