<section class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('status', 'Status') !!}
        
                {!! Form::select('status', ['active' => 'active', 'expired' => 'expired', 'not set' => 'not set'], null, [
                        'id' => 'status',
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all')
                    ])
                !!}

            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('modules', 'Modules') !!}
        
                {!! Form::select('modules', $modules, null, [
                        'id' => 'modules',
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all')
                    ])
                !!}

            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('business', 'Business') !!}
        
                {!! Form::select('business', $businesses, null, [
                        'id' => 'business',
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all')
                    ])
                !!}

            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('expiry_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('expiry_date_range', null, [
                        'id' => 'expiry_date_range',
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'readonly'
                    ])
                !!}

            </div>
        </div>
        <div class="col-md-3">
            {!! Form::label('expired_on', 'Expired Before') !!}
                        {!! Form::date('expired_on', null, [
                    'id' => 'expired_on',
                    'class' => 'form-control'
                ])
            !!}

        </div>
        
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    @can('superadmin')
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="module_subscription_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Module name</th>
                                    <th>Business name</th>
                                    <th>Status</th>
                                    <th>Activated On</th>
                                    <th>Expires On</th>
                                    <th>Module Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</section>