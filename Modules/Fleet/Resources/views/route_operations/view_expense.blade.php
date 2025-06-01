<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content" style="padding: 10px!important;">
     <div class="modal-header">
      <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-label="Close"
      >
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang( 'fleet::lang.view_expense' )</h4>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            @component('components.widget')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="expense_table">
                    <thead>
                        <tr>
                            <th class="notexport">@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('expense.payee_name')</th>
                            <th>@lang('expense.expense_category')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('product.tax')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('purchase.payment_due')
                            <th>@lang('expense.payment_method')
                            <th>@lang('expense.expense_for')</th>
                            <th>@lang('expense.expense_note')</th>
                            <th>@lang('lang_v1.added_by')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_payment_status_count"></td>
                            <td><span class="display_currency" id="footer_expense_total"
                                    data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_total_due" data-currency_symbol="true"></span>
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>

    
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
        @lang( 'messages.close' )
      </button>
    </div>
    
 </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->


<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    
    expense_table = $('#expense_table').DataTable({

        processing: true,

        serverSide: true,

        aaSorting: [
            [1, 'desc']
        ],

        ajax: {

            url: "{{action('\App\Http\Controllers\ExpenseController@routeperationExpenses', [$id])}}",

            data: function(d) {

               

            },

        },

        columns: [

            { data: 'action', name: 'action', orderable: false, searchable: false },

            { data: 'transaction_date', name: 'transaction_date' },

            { data: 'ref_no', name: 'ref_no' },
            
            { data: 'payee_name', name: 'contacts.name' },

            { data: 'category', name: 'ec.name' },

            { data: 'location_name', name: 'bl.name' },

            { data: 'payment_status', name: 'payment_status', orderable: false },

            { data: 'tax', name: 'tr.name' },

            { data: 'final_total', name: 'final_total' },

            { data: 'payment_due', name: 'payment_due' },

            { data: 'payment_method', name: 'payment_method' },

            { data: 'expense_for', name: 'expense_for' },

            { data: 'additional_notes', name: 'additional_notes' },

            { data: 'created_by', name: 'created_by' },

        ],

        fnDrawCallback: function(oSettings) {

            var expense_total = sum_table_col($('#expense_table'), 'final-total');

            $('#footer_expense_total').text(expense_total);

            var total_due = sum_table_col($('#expense_table'), 'payment_due');

            $('#footer_total_due').text(total_due);

            $('#footer_payment_status_count').html(

                __sum_status_html($('#expense_table'), 'payment-status')

            );

            __currency_convert_recursively($('#expense_table'));

        },

        createdRow: function(row, data, dataIndex) {

            $(row).find('td:eq(4)').attr('class', 'clickable_td');

        },

    });

</script>