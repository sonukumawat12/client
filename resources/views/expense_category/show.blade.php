<div class="modal-dialog" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'expense.show_expense_category' )</h4>
    </div>

    <div class="modal-body">
      <!-- Display Category Code -->
      <div class="form-group">
        {!! Form::label('code', __( 'expense.category_code' ) . ': ') !!}
        <p>{{ $expense_category->code }}</p>
      </div>

      <!-- Display Category Name -->
      <div class="form-group">
        {!! Form::label('name', __( 'expense.category_name' ) . ': ') !!}
        <p>{{ $expense_category->name }}</p>
      </div>

      <!-- Display Expense Account -->
      <div class="form-group">
        {!! Form::label('expense_account', __('sale.expense_account') . ': ') !!}
        <p>{{ $expense_category->expense_account ? $expense_accounts[$expense_category->expense_account] : 'N/A' }}</p>
      </div>

      <!-- Display Payee -->
      <div class="form-group">
        {!! Form::label('payee', 'Payee' . ': ') !!}
        <p>{{ isset($payees[$expense_category->payee_id]) ? $payees[$expense_category->payee_id] : 'N/A' }}</p>
      </div>

      <!-- Display VAT Claimed -->
      <div class="form-group">
        {!! Form::label('vat_claimed', __('product.vat_input_claimed') . ': ') !!}
        <p>{{ $expense_category->vat_claimed ? __('messages.yes') : __('messages.no') }}</p>
      </div>

      <!-- Display Sub Category -->
      <div class="form-group">
        {!! Form::label('is_sub_category', __('expense.is_sub_category') . ': ') !!}
        <p>{{ $expense_category->is_sub_category ? __('messages.yes') : __('messages.no') }}</p>
      </div>

      <!-- Display Parent Category -->
      <div class="form-group @if($expense_category->is_sub_category == 0) hide @endif">
        {!! Form::label('parent_category', __('expense.parent_category') . ': ') !!}
        <p>{{ isset($expense_categories[$expense_category->parent_id]) ? $expense_categories[$expense_category->parent_id] : 'N/A' }}</p>
      </div>

      <!-- Display Employee Info -->
      <div class="form-group">
        {!! Form::label('is_employee', __('expense.is_employee') . ': ') !!}
        <p>{{ $expense_category->is_employee ? __('messages.yes') : __('messages.no') }}</p>
      </div>

      <div class="form-group @if($expense_category->is_employee == 0) hide @endif employee_list">
        {!! Form::label('employee', __('expense.employee_list') . ': ') !!}
        <p>{{ isset($employees[$expense_category->employee_id]) ? $employees[$expense_category->employee_id] : 'N/A' }}</p>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
