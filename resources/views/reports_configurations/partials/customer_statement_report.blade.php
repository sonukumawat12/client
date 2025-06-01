<div class="pos-tab-content active">
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[date]', 1, !empty($customer_statement_report['date']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.date' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[location]', 1, !empty($customer_statement_report['location']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.location' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[invoice_no]', 1, !empty($customer_statement_report['invoice_no']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.invoice_no' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[route]', 1, !empty($customer_statement_report['route']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.route' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[vehicle]', 1, !empty($customer_statement_report['vehicle']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.vehicle' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[customer_reference]', 1, !empty($customer_statement_report['customer_reference']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.customer_reference' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[customer_po]', 1, !empty($customer_statement_report['customer_po']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.customer_po' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[voucher_date]', 1, !empty($customer_statement_report['voucher_date']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.voucher_date' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[product]', 1, !empty($customer_statement_report['product']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.product' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[qty]', 1, !empty($customer_statement_report['qty']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.qty' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[unit_price]', 1, !empty($customer_statement_report['unit_price']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.unit_price' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[invoice_amount]', 1, !empty($customer_statement_report['invoice_amount']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.invoice_amount' ) }}
                  </label>
                </div>
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                <div class="checkbox">
                <br>
                  <label>
                    {!! Form::checkbox('customer_statement_report[due_amount]', 1, !empty($customer_statement_report['due_amount']) , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'reports_configurations.due_amount' ) }}
                  </label>
                </div>
            </div>
        </div>
        
    </div>
</div>